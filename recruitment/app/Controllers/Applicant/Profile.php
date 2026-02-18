<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Applicant Profile Controller
 * Handles profile viewing and updating
 */
class Profile extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect(url('/login'));
        }
        
        if (isAdmin()) {
            redirect(url('/admin/dashboard'));
        }
    }
    
    /**
     * Display profile form
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get user with profile - use explicit u.id to avoid column collision with ap.id
        $stmt = $this->db->prepare("
            SELECT u.id as user_id, u.full_name, u.email, u.phone, u.role_id,
                   ap.profile_photo, ap.date_of_birth, ap.gender, ap.nationality, ap.place_of_birth,
                   ap.address, ap.city, ap.country, ap.postal_code,
                   ap.seaman_book_no, ap.seaman_book_expiry, ap.passport_no, ap.passport_expiry,
                   ap.height_cm, ap.weight_kg, ap.shoe_size, ap.overall_size, ap.blood_type,
                   ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                   ap.total_sea_service_months, ap.last_vessel_name, ap.last_vessel_type,
                   ap.last_rank, ap.last_sign_off, ap.profile_completion
            FROM users u
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Get document types for reference
        $docTypes = $this->db->query("SELECT * FROM document_types ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        
        // Get user's documents
        $docStmt = $this->db->prepare("
            SELECT d.*, dt.name as type_name
            FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            WHERE d.user_id = ?
            ORDER BY dt.sort_order
        ");
        $docStmt->bind_param('i', $userId);
        $docStmt->execute();
        $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('applicant/profile', [
            'user' => $user,
            'documentTypes' => $docTypes,
            'documents' => $documents,
            'pageTitle' => 'My Profile'
        ]);
    }
    
    /**
     * Update profile
     */
    public function update() {
        validate_csrf();
        
        $userId = $_SESSION['user_id'];
        
        // Check if profile exists
        $checkStmt = $this->db->prepare("SELECT id FROM applicant_profiles WHERE user_id = ?");
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $profileExists = $checkStmt->get_result()->num_rows > 0;
        
        // Prepare data
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $dateOfBirth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = $_POST['gender'] ?? null;
        $nationality = trim($_POST['nationality'] ?? '');
        $placeOfBirth = trim($_POST['place_of_birth'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        
        // Seafarer info
        $seamanBookNo = trim($_POST['seaman_book_no'] ?? '');
        $seamanBookExpiry = !empty($_POST['seaman_book_expiry']) ? $_POST['seaman_book_expiry'] : null;
        $passportNo = trim($_POST['passport_no'] ?? '');
        $passportExpiry = !empty($_POST['passport_expiry']) ? $_POST['passport_expiry'] : null;
        
        // Physical info - convert to string for bind_param compatibility with nullable values
        $heightCm = !empty($_POST['height_cm']) ? (string)(int)$_POST['height_cm'] : null;
        $weightKg = !empty($_POST['weight_kg']) ? (string)(int)$_POST['weight_kg'] : null;
        $shoeSize = trim($_POST['shoe_size'] ?? '');
        $overallSize = trim($_POST['overall_size'] ?? '');
        $bloodType = trim($_POST['blood_type'] ?? '');
        
        // Emergency contact
        $emergencyName = trim($_POST['emergency_name'] ?? '');
        $emergencyPhone = trim($_POST['emergency_phone'] ?? '');
        $emergencyRelation = trim($_POST['emergency_relation'] ?? '');
        
        // Experience
        $totalSeaServiceMonths = !empty($_POST['total_sea_service_months']) ? (string)(int)$_POST['total_sea_service_months'] : '0';
        $lastVesselName = trim($_POST['last_vessel_name'] ?? '');
        $lastVesselType = trim($_POST['last_vessel_type'] ?? '');
        $lastRank = trim($_POST['last_rank'] ?? '');
        $lastSignOff = !empty($_POST['last_sign_off']) ? $_POST['last_sign_off'] : null;
        
        // Handle profile photo upload
        $profilePhoto = null; // Will hold new filename if uploaded
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            // Validate file type
            if (!in_array($file['type'], $allowedTypes)) {
                set_flash('error', 'Invalid file type. Only JPG and PNG are allowed.');
                return $this->response->redirect('/applicant/profile');
            }
            
            // Validate file size
            if ($file['size'] > $maxSize) {
                set_flash('error', 'File size must be less than 2MB.');
                return $this->response->redirect('/applicant/profile');
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
            
            // Upload directory
            $uploadDir = dirname(__DIR__, 3) . '/public/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Move uploaded file
            $uploadPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $profilePhoto = $filename;
                
                // Delete old photo if exists
                $oldPhotoStmt = $this->db->prepare("SELECT profile_photo FROM applicant_profiles WHERE user_id = ?");
                $oldPhotoStmt->bind_param('i', $userId);
                $oldPhotoStmt->execute();
                $oldPhoto = $oldPhotoStmt->get_result()->fetch_assoc();
                if ($oldPhoto && !empty($oldPhoto['profile_photo'])) {
                    $oldPath = $uploadDir . $oldPhoto['profile_photo'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            } else {
                set_flash('error', 'Failed to upload photo.');
                return $this->response->redirect('/applicant/profile');
            }
        }
        
        // Update users table
        $userStmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
        $userStmt->bind_param('ssi', $fullName, $phone, $userId);
        $userStmt->execute();
        
        // Calculate profile completion
        $completion = $this->calculateProfileCompletion([
            'full_name' => $fullName,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
            'nationality' => $nationality,
            'address' => $address,
            'seaman_book_no' => $seamanBookNo,
            'passport_no' => $passportNo,
            'emergency_name' => $emergencyName
        ]);
        
        if ($profileExists) {
            // Update existing profile
            if ($profilePhoto !== null) {
                // Include profile_photo in update
                $sql = "UPDATE applicant_profiles SET 
                    profile_photo = ?, date_of_birth = ?, gender = ?, nationality = ?, place_of_birth = ?,
                    address = ?, city = ?, country = ?, postal_code = ?,
                    seaman_book_no = ?, seaman_book_expiry = ?, passport_no = ?, passport_expiry = ?,
                    height_cm = ?, weight_kg = ?, shoe_size = ?, overall_size = ?, blood_type = ?,
                    emergency_name = ?, emergency_phone = ?, emergency_relation = ?,
                    total_sea_service_months = ?, last_vessel_name = ?, last_vessel_type = ?, 
                    last_rank = ?, last_sign_off = ?, profile_completion = ?
                    WHERE user_id = ?";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param(
                    'ssssssssssssssssssssssssssii',
                    $profilePhoto, $dateOfBirth, $gender, $nationality, $placeOfBirth,
                    $address, $city, $country, $postalCode,
                    $seamanBookNo, $seamanBookExpiry, $passportNo, $passportExpiry,
                    $heightCm, $weightKg, $shoeSize, $overallSize, $bloodType,
                    $emergencyName, $emergencyPhone, $emergencyRelation,
                    $totalSeaServiceMonths, $lastVesselName, $lastVesselType,
                    $lastRank, $lastSignOff, $completion, $userId
                );
            } else {
                // No photo upload, skip profile_photo column
                $sql = "UPDATE applicant_profiles SET 
                    date_of_birth = ?, gender = ?, nationality = ?, place_of_birth = ?,
                    address = ?, city = ?, country = ?, postal_code = ?,
                    seaman_book_no = ?, seaman_book_expiry = ?, passport_no = ?, passport_expiry = ?,
                    height_cm = ?, weight_kg = ?, shoe_size = ?, overall_size = ?, blood_type = ?,
                    emergency_name = ?, emergency_phone = ?, emergency_relation = ?,
                    total_sea_service_months = ?, last_vessel_name = ?, last_vessel_type = ?, 
                    last_rank = ?, last_sign_off = ?, profile_completion = ?
                    WHERE user_id = ?";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param(
                    'sssssssssssssssssssssssssii',
                    $dateOfBirth, $gender, $nationality, $placeOfBirth,
                    $address, $city, $country, $postalCode,
                    $seamanBookNo, $seamanBookExpiry, $passportNo, $passportExpiry,
                    $heightCm, $weightKg, $shoeSize, $overallSize, $bloodType,
                    $emergencyName, $emergencyPhone, $emergencyRelation,
                    $totalSeaServiceMonths, $lastVesselName, $lastVesselType,
                    $lastRank, $lastSignOff, $completion, $userId
                );
            }
        } else {
            // Insert new profile
            $sql = "INSERT INTO applicant_profiles (
                user_id, profile_photo, date_of_birth, gender, nationality, place_of_birth,
                address, city, country, postal_code,
                seaman_book_no, seaman_book_expiry, passport_no, passport_expiry,
                height_cm, weight_kg, shoe_size, overall_size, blood_type,
                emergency_name, emergency_phone, emergency_relation,
                total_sea_service_months, last_vessel_name, last_vessel_type, 
                last_rank, last_sign_off, profile_completion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(
                'isssssssssssssssssssssssssssi',
                $userId, $profilePhoto, $dateOfBirth, $gender, $nationality, $placeOfBirth,
                $address, $city, $country, $postalCode,
                $seamanBookNo, $seamanBookExpiry, $passportNo, $passportExpiry,
                $heightCm, $weightKg, $shoeSize, $overallSize, $bloodType,
                $emergencyName, $emergencyPhone, $emergencyRelation,
                $totalSeaServiceMonths, $lastVesselName, $lastVesselType,
                $lastRank, $lastSignOff, $completion
            );
        }
        
        if ($stmt->execute()) {
            flash('success', 'Profile updated successfully!');
        } else {
            flash('error', 'Failed to update profile. Please try again.');
        }
        
        redirect(url('/applicant/profile'));
    }
    
    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($data) {
        $fields = [
            'full_name', 'date_of_birth', 'gender', 'nationality',
            'address', 'seaman_book_no', 'passport_no', 'emergency_name'
        ];
        
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $filled++;
            }
        }
        
        return (int)(($filled / count($fields)) * 100);
    }
}
