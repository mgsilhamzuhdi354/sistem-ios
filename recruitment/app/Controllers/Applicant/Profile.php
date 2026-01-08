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
        
        // Get user with profile
        $stmt = $this->db->prepare("
            SELECT u.*, ap.*
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
        $dateOfBirth = $_POST['date_of_birth'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $nationality = trim($_POST['nationality'] ?? '');
        $placeOfBirth = trim($_POST['place_of_birth'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        
        // Seafarer info
        $seamanBookNo = trim($_POST['seaman_book_no'] ?? '');
        $seamanBookExpiry = $_POST['seaman_book_expiry'] ?? null;
        $passportNo = trim($_POST['passport_no'] ?? '');
        $passportExpiry = $_POST['passport_expiry'] ?? null;
        
        // Physical info
        $heightCm = (int)($_POST['height_cm'] ?? 0) ?: null;
        $weightKg = (int)($_POST['weight_kg'] ?? 0) ?: null;
        $shoeSize = trim($_POST['shoe_size'] ?? '');
        $overallSize = trim($_POST['overall_size'] ?? '');
        $bloodType = trim($_POST['blood_type'] ?? '');
        
        // Emergency contact
        $emergencyName = trim($_POST['emergency_name'] ?? '');
        $emergencyPhone = trim($_POST['emergency_phone'] ?? '');
        $emergencyRelation = trim($_POST['emergency_relation'] ?? '');
        
        // Experience
        $totalSeaServiceMonths = (int)($_POST['total_sea_service_months'] ?? 0);
        $lastVesselName = trim($_POST['last_vessel_name'] ?? '');
        $lastVesselType = trim($_POST['last_vessel_type'] ?? '');
        $lastRank = trim($_POST['last_rank'] ?? '');
        $lastSignOff = $_POST['last_sign_off'] ?? null;
        
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
                'ssssssssssssiissssssississi',
                $dateOfBirth, $gender, $nationality, $placeOfBirth,
                $address, $city, $country, $postalCode,
                $seamanBookNo, $seamanBookExpiry, $passportNo, $passportExpiry,
                $heightCm, $weightKg, $shoeSize, $overallSize, $bloodType,
                $emergencyName, $emergencyPhone, $emergencyRelation,
                $totalSeaServiceMonths, $lastVesselName, $lastVesselType,
                $lastRank, $lastSignOff, $completion, $userId
            );
        } else {
            // Insert new profile
            $sql = "INSERT INTO applicant_profiles (
                user_id, date_of_birth, gender, nationality, place_of_birth,
                address, city, country, postal_code,
                seaman_book_no, seaman_book_expiry, passport_no, passport_expiry,
                height_cm, weight_kg, shoe_size, overall_size, blood_type,
                emergency_name, emergency_phone, emergency_relation,
                total_sea_service_months, last_vessel_name, last_vessel_type, 
                last_rank, last_sign_off, profile_completion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(
                'issssssssssssssssssssississi',
                $userId, $dateOfBirth, $gender, $nationality, $placeOfBirth,
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
