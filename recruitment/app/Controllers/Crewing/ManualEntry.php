<?php
/**
 * Crewing - Manual Entry Controller
 * Allows crewing staff to manually enter walk-in candidates
 * Shows all applicants managed by this crewing (manual + pipeline + assigned)
 */

require_once APPPATH . 'Controllers/BaseController.php';
require_once APPPATH . 'Libraries/ErpSync.php';

class ManualEntry extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isCrewingOrAdmin()) {
            flash('error', 'Access denied');
            redirect(url('/'));
        }
    }
    
    /**
     * Search applicant data by KTP number (NIK) - AJAX endpoint
     */
    public function searchByKtp() {
        header('Content-Type: application/json');
        
        $ktp = trim($_GET['ktp'] ?? '');
        
        if (strlen($ktp) < 10) {
            echo json_encode(['found' => false, 'message' => 'Nomor KTP terlalu pendek']);
            return;
        }
        
        // Search in applicant_profiles by ktp_number
        $stmt = $this->db->prepare("
            SELECT 
                u.full_name, u.email, u.phone,
                ap.date_of_birth, ap.gender, ap.place_of_birth, ap.nationality, ap.blood_type,
                ap.address, ap.city, ap.country, ap.postal_code,
                ap.seaman_book_no, ap.seaman_book_expiry, ap.passport_no, ap.passport_expiry,
                ap.height_cm, ap.weight_kg, ap.shoe_size, ap.overall_size,
                ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                ap.total_sea_service_months, ap.last_rank, ap.last_vessel_name, ap.last_vessel_type, ap.last_sign_off
            FROM applicant_profiles ap
            JOIN users u ON ap.user_id = u.id
            WHERE ap.ktp_number = ?
            LIMIT 1
        ");
        $stmt->bind_param('s', $ktp);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            echo json_encode([
                'found' => true,
                'message' => 'Data ditemukan!',
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'found' => false,
                'message' => 'Data dengan NIK tersebut tidak ditemukan di database'
            ]);
        }
    }
    
    /**
     * Display manual entry form
     */
    public function form($vacancyId = null) {
        $vacancy = null;
        
        if ($vacancyId) {
            $stmt = $this->db->prepare("SELECT * FROM job_vacancies WHERE id = ? AND status = 'published'");
            $stmt->bind_param('i', $vacancyId);
            $stmt->execute();
            $vacancy = $stmt->get_result()->fetch_assoc();
        }
        
        $vacancies = $this->db->query("
            SELECT v.*, d.name as department_name
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            WHERE v.status = 'published'
            ORDER BY v.created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/manual_entry/form', [
            'pageTitle' => 'Manual Candidate Entry',
            'vacancy' => $vacancy,
            'vacancies' => $vacancies
        ]);
    }
    
    /**
     * Submit manual entry with complete data
     */
    public function submit() {
        validate_csrf();
        
        // Basic required fields
        $vacancyId   = $_POST['vacancy_id'] ?? null;
        $fullName    = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $ktpNumber   = trim($_POST['ktp_number'] ?? '');
        
        // Optional personal info
        $dob          = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender       = !empty($_POST['gender']) ? $_POST['gender'] : null;
        $placeOfBirth = trim($_POST['place_of_birth'] ?? '');
        $nationality  = trim($_POST['nationality'] ?? 'Indonesia');
        $bloodType    = !empty($_POST['blood_type']) ? $_POST['blood_type'] : null;
        $address      = trim($_POST['address'] ?? '');
        $city         = trim($_POST['city'] ?? '');
        $country      = trim($_POST['country'] ?? 'Indonesia');
        $postalCode   = trim($_POST['postal_code'] ?? '');
        
        // Documents
        $seamanBookNo     = trim($_POST['seaman_book_no'] ?? '');
        $seamanBookExpiry = !empty($_POST['seaman_book_expiry']) ? $_POST['seaman_book_expiry'] : null;
        $passportNo       = trim($_POST['passport_no'] ?? '');
        $passportExpiry   = !empty($_POST['passport_expiry']) ? $_POST['passport_expiry'] : null;
        
        // Physical
        $heightCm    = !empty($_POST['height_cm']) ? (int)$_POST['height_cm'] : null;
        $weightKg    = !empty($_POST['weight_kg']) ? (int)$_POST['weight_kg'] : null;
        $shoeSize    = trim($_POST['shoe_size'] ?? '');
        $overallSize = trim($_POST['overall_size'] ?? '');
        
        // Emergency
        $emergencyName     = trim($_POST['emergency_name'] ?? '');
        $emergencyPhone    = trim($_POST['emergency_phone'] ?? '');
        $emergencyRelation = trim($_POST['emergency_relation'] ?? '');
        
        // Sea experience
        $totalSeaMonths = !empty($_POST['total_sea_service_months']) ? (int)$_POST['total_sea_service_months'] : 0;
        $lastRank       = trim($_POST['last_rank'] ?? '');
        $lastVesselName = trim($_POST['last_vessel_name'] ?? '');
        $lastVesselType = trim($_POST['last_vessel_type'] ?? '');
        $lastSignOff    = !empty($_POST['last_sign_off']) ? $_POST['last_sign_off'] : null;
        
        // Application fields
        $expectedSalary = !empty($_POST['expected_salary']) ? $_POST['expected_salary'] : null;
        $availableDate  = !empty($_POST['available_date']) ? $_POST['available_date'] : null;
        $coverLetter    = trim($_POST['cover_letter'] ?? '');
        $notes          = trim($_POST['notes'] ?? '');
        
        // Validation
        if (!$vacancyId || !$fullName || !$email || !$phone) {
            flash('error', 'Nama, email, telepon, dan lowongan wajib diisi!');
            redirect(url('/crewing/manual-entry'));
        }
        
        // Check email uniqueness
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            flash('error', 'Email sudah terdaftar di sistem!');
            redirect(url('/crewing/manual-entry'));
        }
        
        try {
            $this->db->begin_transaction();
            
            // 1. Create user
            $password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $roleId = 3;
            $stmt = $this->db->prepare("
                INSERT INTO users (role_id, full_name, email, phone, password, is_active, is_manual_entry, requires_activation, created_at)
                VALUES (?, ?, ?, ?, ?, 1, 1, 1, NOW())
            ");
            $stmt->bind_param('issss', $roleId, $fullName, $email, $phone, $password);
            $stmt->execute();
            $userId = $this->db->insert_id;
            
            // 2. Create full applicant profile
            $stmt = $this->db->prepare("
                INSERT INTO applicant_profiles (
                    user_id, ktp_number, date_of_birth, gender, nationality, place_of_birth,
                    address, city, country, postal_code, blood_type,
                    seaman_book_no, seaman_book_expiry, passport_no, passport_expiry,
                    height_cm, weight_kg, shoe_size, overall_size,
                    emergency_name, emergency_phone, emergency_relation,
                    total_sea_service_months, last_rank, last_vessel_name, last_vessel_type, last_sign_off,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('issssssssssssssiisisssissss',
                $userId, $ktpNumber, $dob, $gender, $nationality, $placeOfBirth,
                $address, $city, $country, $postalCode, $bloodType,
                $seamanBookNo, $seamanBookExpiry, $passportNo, $passportExpiry,
                $heightCm, $weightKg, $shoeSize, $overallSize,
                $emergencyName, $emergencyPhone, $emergencyRelation,
                $totalSeaMonths, $lastRank, $lastVesselName, $lastVesselType, $lastSignOff
            );
            $stmt->execute();
            
            // 3. Create application
            $crewingId = $_SESSION['user_id'];
            $statusId = 1;
            $stmt = $this->db->prepare("
                INSERT INTO applications (
                    user_id, vacancy_id, status_id, cover_letter, expected_salary, available_date,
                    submitted_at, entry_source, entered_by,
                    current_crewing_id, preferred_recruiter_id, recruiter_assignment_type, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'manual', ?, ?, ?, 'manual', NOW())
            ");
            $stmt->bind_param('iiisdsiii',
                $userId, $vacancyId, $statusId, $coverLetter, $expectedSalary, $availableDate,
                $crewingId, $crewingId, $crewingId
            );
            $stmt->execute();
            $applicationId = $this->db->insert_id;
            
            // 4. Create assignment
            $stmt = $this->db->prepare("
                INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status, assigned_at)
                VALUES (?, ?, ?, ?, 'active', NOW())
            ");
            $assignNote = "Manual entry" . ($notes ? " - " . $notes : "");
            $stmt->bind_param('iiis', $applicationId, $crewingId, $crewingId, $assignNote);
            $stmt->execute();
            
            // 5. Handle document uploads
            $this->handleDocumentUploads($userId);
            
            // 6. Log
            logAutomation('manual_entry', 'applications', $applicationId, 'create', [
                'entered_by' => $crewingId, 'candidate_name' => $fullName, 'vacancy_id' => $vacancyId
            ]);
            
            $this->db->commit();
            flash('success', "✓ Data pelamar <strong>{$fullName}</strong> berhasil disimpan dan ditugaskan kepada Anda!");
            redirect(url('/crewing/manual-entries'));
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Manual entry error: " . $e->getMessage());
            flash('error', 'Gagal menyimpan: ' . $e->getMessage());
            redirect(url('/crewing/manual-entry'));
        }
    }
    
    /**
     * List ALL applicants managed by this crewing (manual + assigned + pipeline)
     */
    public function list() {
        $crewingId = $_SESSION['user_id'];
        
        // Get UI mode preference (modern or classic)
        $uiMode = $this->input('ui', 'modern'); // default to modern
        
        $query = "
            SELECT 
                a.id,
                a.user_id,
                a.created_at,
                a.entry_source,
                a.expected_salary,
                a.available_date,
                a.is_synced_to_erp,
                a.synced_at,
                a.erp_employee_id,
                a.status_id,
                u.full_name as candidate_name,
                u.email,
                u.phone,
                u.avatar,
                u.is_manual_entry,
                v.title as vacancy_title,
                d.name as department_name,
                s.name as status_name,
                s.name_id as status_name_id,
                s.color as status_color,
                s.sort_order as status_order,
                ap.nationality,
                ap.seaman_book_no,
                ap.passport_no,
                ap.total_sea_service_months,
                ap.last_rank,
                ap.last_vessel_name,
                ap.gender,
                ap.date_of_birth,
                ap.city,
                ap.blood_type,
                ap.height_cm,
                ap.weight_kg
            FROM applications a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            INNER JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE (a.entered_by = ? OR a.current_crewing_id = ? 
                   OR a.id IN (SELECT application_id FROM application_assignments WHERE assigned_to = ? AND status = 'active'))
            GROUP BY a.id
            ORDER BY a.created_at DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $crewingId, $crewingId, $crewingId);
        $stmt->execute();
        $entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $totalManual = count(array_filter($entries, fn($e) => $e['entry_source'] === 'manual'));
        $totalOnline = count(array_filter($entries, fn($e) => $e['entry_source'] === 'online'));
        $totalApproved = count(array_filter($entries, fn($e) => strtolower($e['status_name']) === 'approved'));
        $totalInProgress = count(array_filter($entries, fn($e) => !in_array(strtolower($e['status_name']), ['approved', 'rejected'])));
        $totalSynced = count(array_filter($entries, fn($e) => !empty($e['is_synced_to_erp'])));
        
        // Choose content view based on UI mode
        $contentView = $uiMode === 'modern' ? 'crewing/manual_entry/list_modern' : 'crewing/manual_entry/list_content';
        
        // Always render through the crewing layout (which includes the sidebar)
        $data = [
            'pageTitle' => 'Semua Pelamar Saya',
            'entries' => $entries,
            'stats' => [
                'total' => count($entries),
                'manual' => $totalManual,
                'online' => $totalOnline,
                'approved' => $totalApproved,
                'in_progress' => $totalInProgress,
                'synced' => $totalSynced
            ],
            'uiMode' => $uiMode
        ];
        
        // Set the content variable and render through the layout
        $data['content'] = $contentView;
        extract($data);
        include APPPATH . 'Views/layouts/crewing.php';
    }
    
    /**
     * Detail view of an applicant
     */
    public function detail($applicationId) {
        $crewingId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT 
                a.*, 
                u.full_name, u.email, u.phone, u.avatar, u.is_manual_entry, u.created_at as user_created_at,
                v.title as vacancy_title, v.id as vacancy_id,
                d.name as department_name,
                s.name as status_name, s.name_id as status_name_id, s.color as status_color,
                ap.*
            FROM applications a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            INNER JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE a.id = ? AND (a.entered_by = ? OR a.current_crewing_id = ?
                   OR a.id IN (SELECT application_id FROM application_assignments WHERE assigned_to = ? AND status = 'active'))
        ");
        $stmt->bind_param('iiii', $applicationId, $crewingId, $crewingId, $crewingId);
        $stmt->execute();
        $entry = $stmt->get_result()->fetch_assoc();
        
        if (!$entry) {
            flash('error', 'Data pelamar tidak ditemukan atau Anda tidak memiliki akses.');
            redirect(url('/crewing/manual-entries'));
        }
        
        // Get documents
        $docStmt = $this->db->prepare("
            SELECT d.*, dt.name as type_name, dt.name_id as type_name_id
            FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            WHERE d.user_id = ?
            ORDER BY dt.sort_order
        ");
        $docStmt->bind_param('i', $entry['user_id']);
        $docStmt->execute();
        $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get statuses for reference
        $statuses = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/manual_entry/detail', [
            'pageTitle' => 'Detail Pelamar - ' . $entry['full_name'],
            'entry' => $entry,
            'documents' => $documents,
            'statuses' => $statuses
        ]);
    }
    
    /**
     * Edit form for an applicant
     */
    public function editForm($applicationId) {
        $crewingId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT 
                a.*, 
                u.full_name, u.email, u.phone, u.id as uid, u.avatar,
                v.id as vacancy_id,
                ap.*
            FROM applications a
            INNER JOIN users u ON a.user_id = u.id
            INNER JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE a.id = ? AND (a.entered_by = ? OR a.current_crewing_id = ?)
        ");
        $stmt->bind_param('iii', $applicationId, $crewingId, $crewingId);
        $stmt->execute();
        $entry = $stmt->get_result()->fetch_assoc();
        
        if (!$entry) {
            flash('error', 'Data tidak ditemukan atau Anda tidak memiliki akses.');
            redirect(url('/crewing/manual-entries'));
        }
        
        $vacancies = $this->db->query("
            SELECT v.*, d.name as department_name
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            WHERE v.status = 'published'
            ORDER BY v.created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/manual_entry/form', [
            'pageTitle' => 'Edit Pelamar - ' . $entry['full_name'],
            'entry' => $entry,
            'vacancy' => null,
            'vacancies' => $vacancies,
            'editMode' => true
        ]);
    }
    
    /**
     * Update applicant data
     */
    public function update($applicationId) {
        validate_csrf();
        $crewingId = $_SESSION['user_id'];
        
        // Verify ownership
        $stmt = $this->db->prepare("SELECT a.*, a.user_id FROM applications a WHERE a.id = ? AND (a.entered_by = ? OR a.current_crewing_id = ?)");
        $stmt->bind_param('iii', $applicationId, $crewingId, $crewingId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Tidak memiliki akses untuk mengedit data ini.');
            redirect(url('/crewing/manual-entries'));
        }
        
        $userId = $app['user_id'];
        
        // Collect fields
        $fullName    = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $ktpNumber   = trim($_POST['ktp_number'] ?? '');
        $vacancyId   = $_POST['vacancy_id'] ?? $app['vacancy_id'];
        
        $dob          = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender       = !empty($_POST['gender']) ? $_POST['gender'] : null;
        $placeOfBirth = trim($_POST['place_of_birth'] ?? '');
        $nationality  = trim($_POST['nationality'] ?? 'Indonesia');
        $bloodType    = !empty($_POST['blood_type']) ? $_POST['blood_type'] : null;
        $address      = trim($_POST['address'] ?? '');
        $city         = trim($_POST['city'] ?? '');
        $country      = trim($_POST['country'] ?? 'Indonesia');
        $postalCode   = trim($_POST['postal_code'] ?? '');
        
        $seamanBookNo     = trim($_POST['seaman_book_no'] ?? '');
        $seamanBookExpiry = !empty($_POST['seaman_book_expiry']) ? $_POST['seaman_book_expiry'] : null;
        $passportNo       = trim($_POST['passport_no'] ?? '');
        $passportExpiry   = !empty($_POST['passport_expiry']) ? $_POST['passport_expiry'] : null;
        
        $heightCm    = !empty($_POST['height_cm']) ? (int)$_POST['height_cm'] : null;
        $weightKg    = !empty($_POST['weight_kg']) ? (int)$_POST['weight_kg'] : null;
        $shoeSize    = trim($_POST['shoe_size'] ?? '');
        $overallSize = trim($_POST['overall_size'] ?? '');
        
        $emergencyName     = trim($_POST['emergency_name'] ?? '');
        $emergencyPhone    = trim($_POST['emergency_phone'] ?? '');
        $emergencyRelation = trim($_POST['emergency_relation'] ?? '');
        
        $totalSeaMonths = !empty($_POST['total_sea_service_months']) ? (int)$_POST['total_sea_service_months'] : 0;
        $lastRank       = trim($_POST['last_rank'] ?? '');
        $lastVesselName = trim($_POST['last_vessel_name'] ?? '');
        $lastVesselType = trim($_POST['last_vessel_type'] ?? '');
        $lastSignOff    = !empty($_POST['last_sign_off']) ? $_POST['last_sign_off'] : null;
        
        $expectedSalary = !empty($_POST['expected_salary']) ? $_POST['expected_salary'] : null;
        $availableDate  = !empty($_POST['available_date']) ? $_POST['available_date'] : null;
        $coverLetter    = trim($_POST['cover_letter'] ?? '');
        
        if (!$fullName || !$email || !$phone) {
            flash('error', 'Nama, email, dan telepon wajib diisi!');
            redirect(url('/crewing/manual-entries/edit/' . $applicationId));
        }
        
        // Check email uniqueness (exclude current user)
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param('si', $email, $userId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            flash('error', 'Email sudah digunakan oleh pengguna lain!');
            redirect(url('/crewing/manual-entries/edit/' . $applicationId));
        }
        
        try {
            $this->db->begin_transaction();
            
            // Update user
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param('sssi', $fullName, $email, $phone, $userId);
            $stmt->execute();
            
            // Update profile
            $stmt = $this->db->prepare("
                UPDATE applicant_profiles SET
                    ktp_number = ?, date_of_birth = ?, gender = ?, nationality = ?, place_of_birth = ?,
                    address = ?, city = ?, country = ?, postal_code = ?, blood_type = ?,
                    seaman_book_no = ?, seaman_book_expiry = ?, passport_no = ?, passport_expiry = ?,
                    height_cm = ?, weight_kg = ?, shoe_size = ?, overall_size = ?,
                    emergency_name = ?, emergency_phone = ?, emergency_relation = ?,
                    total_sea_service_months = ?, last_rank = ?, last_vessel_name = ?, last_vessel_type = ?, last_sign_off = ?
                WHERE user_id = ?
            ");
            $stmt->bind_param('ssssssssssssssiisssissssssi',
                $ktpNumber, $dob, $gender, $nationality, $placeOfBirth,
                $address, $city, $country, $postalCode, $bloodType,
                $seamanBookNo, $seamanBookExpiry, $passportNo, $passportExpiry,
                $heightCm, $weightKg, $shoeSize, $overallSize,
                $emergencyName, $emergencyPhone, $emergencyRelation,
                $totalSeaMonths, $lastRank, $lastVesselName, $lastVesselType, $lastSignOff,
                $userId
            );
            $stmt->execute();
            
            // Check if changing vacancy would create duplicate application
            if ($vacancyId != $app['vacancy_id']) {
                $dupCheck = $this->db->prepare("SELECT id FROM applications WHERE user_id = ? AND vacancy_id = ? AND id != ?");
                $dupCheck->bind_param('iii', $userId, $vacancyId, $applicationId);
                $dupCheck->execute();
                if ($dupCheck->get_result()->fetch_assoc()) {
                    throw new Exception("Pelamar ini sudah memiliki aplikasi untuk lowongan yang dipilih. Silakan pilih lowongan lain.");
                }
            }
            
            // Update application
            $stmt = $this->db->prepare("
                UPDATE applications SET vacancy_id = ?, expected_salary = ?, available_date = ?, cover_letter = ?
                WHERE id = ?
            ");
            $stmt->bind_param('idssi', $vacancyId, $expectedSalary, $availableDate, $coverLetter, $applicationId);
            $stmt->execute();
            
            // Handle document uploads if present
            $this->handleDocumentUploads($userId);
            
            $this->db->commit();
            
            flash('success', "✓ Data pelamar <strong>{$fullName}</strong> berhasil diperbarui!");
            redirect(url('/crewing/manual-entries/detail/' . $applicationId));
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Update entry error: " . $e->getMessage());
            flash('error', 'Gagal memperbarui: ' . $e->getMessage());
            redirect(url('/crewing/manual-entries/edit/' . $applicationId));
        }
    }
    
    /**
     * Delete an applicant entry
     */
    public function deleteEntry($applicationId) {
        validate_csrf();
        $crewingId = $_SESSION['user_id'];
        
        // Verify ownership
        $stmt = $this->db->prepare("SELECT a.user_id, u.full_name FROM applications a JOIN users u ON a.user_id = u.id WHERE a.id = ? AND (a.entered_by = ? OR a.current_crewing_id = ?)");
        $stmt->bind_param('iii', $applicationId, $crewingId, $crewingId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Tidak memiliki akses untuk menghapus data ini.');
            redirect(url('/crewing/manual-entries'));
        }
        
        $userId = $app['user_id'];
        $name = $app['full_name'];
        
        try {
            $this->db->begin_transaction();
            
            // Temporarily disable FK checks for clean cascade delete
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            
            // Delete all related records for this application
            $tables = [
                'application_assignments' => 'application_id',
                'pipeline_requests' => 'application_id',
                'medical_checkups' => 'application_id',
                'status_change_requests' => 'application_id',
                'application_status_history' => 'application_id',
                'archived_applications' => 'application_id',
            ];
            
            foreach ($tables as $table => $col) {
                $delStmt = $this->db->prepare("DELETE FROM `$table` WHERE `$col` = ?");
                $delStmt->bind_param('i', $applicationId);
                $delStmt->execute();
            }
            
            // Delete interview data
            $this->db->query("DELETE ia FROM interview_answers ia JOIN interview_sessions s ON ia.session_id = s.id WHERE s.application_id = $applicationId");
            $delStmt = $this->db->prepare("DELETE FROM interview_sessions WHERE application_id = ?");
            $delStmt->bind_param('i', $applicationId);
            $delStmt->execute();
            
            // Delete notifications for user
            $delStmt = $this->db->prepare("DELETE FROM notifications WHERE user_id = ?");
            $delStmt->bind_param('i', $userId);
            $delStmt->execute();
            
            // Delete document files from disk
            $docStmt = $this->db->prepare("SELECT file_path FROM documents WHERE user_id = ?");
            $docStmt->bind_param('i', $userId);
            $docStmt->execute();
            $docs = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($docs as $doc) {
                $filePath = FCPATH . $doc['file_path'];
                if (file_exists($filePath)) unlink($filePath);
            }
            $delStmt = $this->db->prepare("DELETE FROM documents WHERE user_id = ?");
            $delStmt->bind_param('i', $userId);
            $delStmt->execute();
            
            // Delete ALL applications for this user (handles multiple apps)
            $delStmt = $this->db->prepare("DELETE FROM applications WHERE user_id = ?");
            $delStmt->bind_param('i', $userId);
            $delStmt->execute();
            
            // Delete profile & user
            $delStmt = $this->db->prepare("DELETE FROM applicant_profiles WHERE user_id = ?");
            $delStmt->bind_param('i', $userId);
            $delStmt->execute();
            
            $delStmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $delStmt->bind_param('i', $userId);
            $delStmt->execute();
            
            // Re-enable FK checks
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
            
            $this->db->commit();
            
            flash('success', "✓ Data pelamar <strong>{$name}</strong> berhasil dihapus.");
            
        } catch (Exception $e) {
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
            $this->db->rollback();
            error_log("Delete entry error: " . $e->getMessage());
            flash('error', 'Gagal menghapus: ' . $e->getMessage());
        }

        
        redirect(url('/crewing/manual-entries'));
    }
    
    /**
     * Push approved applicant to ERP system
     * Uses ErpSync library (same as ErpIntegration.sendToErp)
     */
    public function pushToErp($applicationId) {
        validate_csrf();
        $crewingId = $_SESSION['user_id'];
        
        // Get applicant data with full profile
        $stmt = $this->db->prepare("
            SELECT a.*, u.full_name, u.email, u.phone, u.avatar,
                   ap.date_of_birth, ap.place_of_birth, ap.nationality, ap.address,
                   ap.seaman_book_no, ap.passport_no, ap.gender,
                   ap.city, ap.postal_code,
                   ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                   ap.total_sea_service_months, ap.profile_photo,
                   v.title as vacancy_title, s.name as status_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.id = ? AND (a.entered_by = ? OR a.current_crewing_id = ?)
        ");
        $stmt->bind_param('iii', $applicationId, $crewingId, $crewingId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Data tidak ditemukan.');
            redirect(url('/crewing/manual-entries'));
        }
        
        if (!empty($app['is_synced_to_erp'])) {
            flash('warning', 'Data sudah pernah di-push ke ERP.');
            redirect(url('/crewing/manual-entries/detail/' . $applicationId));
        }
        
        // Check status is approved/hired
        if (!in_array(strtolower($app['status_name']), ['approved', 'hired'])) {
            flash('error', 'Hanya pelamar dengan status Approved/Hired yang bisa di-push ke ERP.');
            redirect(url('/crewing/manual-entries/detail/' . $applicationId));
        }
        
        try {
            // Use ErpSync library (same pattern as ErpIntegration.sendToErp)
            $erpSync = new ErpSync($this->db);
            
            // Get documents for this applicant
            $docsStmt = $this->db->prepare("
                SELECT d.*, dt.name as type_name, dt.name_id as type_name_id
                FROM documents d
                LEFT JOIN document_types dt ON d.document_type_id = dt.id
                WHERE d.user_id = ?
            ");
            $docsStmt->bind_param('i', $app['user_id']);
            $docsStmt->execute();
            $documents = $docsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // Prepare crew data
            $crewData = [
                'full_name' => $app['full_name'],
                'email' => $app['email'],
                'phone' => $app['phone'],
                'candidate_id' => $app['user_id'],
                'status' => 'pending_approval',
                'notes' => 'Manual Entry - Recruited for: ' . $app['vacancy_title'],
                'gender' => $app['gender'] ?? 'male',
                'birth_date' => $app['date_of_birth'] ?? null,
                'birth_place' => $app['place_of_birth'] ?? '',
                'nationality' => $app['nationality'] ?? 'Indonesian',
                'address' => $app['address'] ?? '',
                'city' => $app['city'] ?? '',
                'postal_code' => $app['postal_code'] ?? '',
                'emergency_name' => $app['emergency_name'] ?? '',
                'emergency_phone' => $app['emergency_phone'] ?? '',
                'emergency_relation' => $app['emergency_relation'] ?? '',
                'total_sea_time_months' => intval($app['total_sea_service_months'] ?? 0),
            ];
            
            // Check if crew already exists in ERP
            $existingCrewId = $erpSync->getCrewByCandidateId($app['user_id']);
            if ($existingCrewId) {
                $erpSync->updateCrew($existingCrewId, $crewData);
                $crewId = $existingCrewId;
            } else {
                $crewId = $erpSync->createCrew($crewData);
            }
            
            // Sync profile photo
            $photoPath = $app['profile_photo'] ?? $app['avatar'] ?? null;
            if ($photoPath) {
                $erpPhotoPath = $erpSync->syncPhoto($photoPath, $crewId);
                if ($erpPhotoPath) {
                    $erpSync->updateCrew($crewId, ['photo' => $erpPhotoPath]);
                }
            }
            
            // Sync documents
            $docsSynced = 0;
            if (!empty($documents)) {
                $docsSynced = $erpSync->syncDocuments($crewId, $documents, $crewingId);
            }
            
            // Update recruitment application
            $updateStmt = $this->db->prepare("
                UPDATE applications SET is_synced_to_erp = 1, synced_at = NOW(), erp_crew_id = ? WHERE id = ?
            ");
            $updateStmt->bind_param('ii', $crewId, $applicationId);
            $updateStmt->execute();
            
            logAutomation('erp_push', 'applications', $applicationId, 'push_to_erp', [
                'crew_id' => $crewId,
                'pushed_by' => $crewingId,
                'documents_synced' => $docsSynced
            ]);
            
            $message = "✓ <strong>{$app['full_name']}</strong> berhasil di-push ke ERP.";
            if ($docsSynced > 0) {
                $message .= " ($docsSynced dokumen ditransfer)";
            }
            flash('success', $message);
            
        } catch (Exception $e) {
            error_log("ERP Push error: " . $e->getMessage());
            flash('error', 'Gagal push ke ERP: ' . $e->getMessage());
        }
        
        redirect(url('/crewing/manual-entries/detail/' . $applicationId));
    }
    
    /**
     * Handle document file uploads (used in submit and update)
     */
    private function handleDocumentUploads($userId) {
        if (empty($_FILES['doc_file']) || !is_array($_FILES['doc_file']['name'])) {
            return;
        }
        
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg',
                         'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $uploadDir = FCPATH . 'uploads/documents/' . $userId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        foreach ($_FILES['doc_file']['name'] as $typeId => $fileName) {
            if (empty($fileName) || $_FILES['doc_file']['error'][$typeId] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $tmpName  = $_FILES['doc_file']['tmp_name'][$typeId];
            $fileSize = $_FILES['doc_file']['size'][$typeId];
            $fileType = $_FILES['doc_file']['type'][$typeId];
            
            if ($fileSize > $maxSize) continue;
            if (!in_array($fileType, $allowedTypes)) continue;
            
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newName = 'doc_' . $typeId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filePath = $uploadDir . $newName;
            
            if (move_uploaded_file($tmpName, $filePath)) {
                // Delete old document of same type
                $oldDocStmt = $this->db->prepare("SELECT file_path FROM documents WHERE user_id = ? AND document_type_id = ?");
                $oldDocStmt->bind_param('ii', $userId, $typeId);
                $oldDocStmt->execute();
                $oldDocs = $oldDocStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                foreach ($oldDocs as $old) {
                    $oldPath = FCPATH . $old['file_path'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }
                $delDocStmt = $this->db->prepare("DELETE FROM documents WHERE user_id = ? AND document_type_id = ?");
                $delDocStmt->bind_param('ii', $userId, $typeId);
                $delDocStmt->execute();
                
                // Insert new
                $relPath = 'uploads/documents/' . $userId . '/' . $newName;
                $docNumber  = trim($_POST['doc_number'][$typeId] ?? '');
                $expiryDate = !empty($_POST['doc_expiry'][$typeId]) ? $_POST['doc_expiry'][$typeId] : null;
                
                $stmt = $this->db->prepare("
                    INSERT INTO documents (user_id, document_type_id, file_name, file_path, original_name, document_number, expiry_date, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param('iisssss', $userId, $typeId, $newName, $relPath, $fileName, $docNumber, $expiryDate);
                $stmt->execute();
                
                // Special handling: if this is a photo (type 7), also update users.avatar and applicant_profiles.profile_photo
                if ($typeId == 7) {
                    $avatarStmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $avatarStmt->bind_param('si', $relPath, $userId);
                    $avatarStmt->execute();
                    
                    $profilePhotoStmt = $this->db->prepare("UPDATE applicant_profiles SET profile_photo = ? WHERE user_id = ?");
                    $profilePhotoStmt->bind_param('si', $relPath, $userId);
                    $profilePhotoStmt->execute();
                }
            }
        }
    }
}
