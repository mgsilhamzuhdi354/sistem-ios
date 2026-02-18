<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Vacancies Controller
 * Allows crewing staff to view and share job vacancies
 */
class Vacancies extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isCrewing()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    /**
     * List all active vacancies
     */
    public function index() {
        $search = $_GET['search'] ?? '';
        $department = $_GET['department'] ?? '';
        $vesselType = $_GET['vessel_type'] ?? '';
        
        // Get departments for filter
        $departments = $this->db->query("SELECT id, name FROM departments ORDER BY name")->fetch_all(MYSQLI_ASSOC);
        
        // Get vessel types for filter
        $vesselTypes = $this->db->query("SELECT id, name FROM vessel_types ORDER BY name")->fetch_all(MYSQLI_ASSOC);
        
        // Build query
        $query = "
            SELECT jv.*, 
                   d.name as department_name,
                   vt.name as vessel_type_name,
                   COUNT(DISTINCT a.id) as applications_count
            FROM job_vacancies jv
            LEFT JOIN departments d ON jv.department_id = d.id
            LEFT JOIN vessel_types vt ON jv.vessel_type_id = vt.id
            LEFT JOIN applications a ON jv.id = a.vacancy_id
            WHERE jv.status = 'published'
        ";
        
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $query .= " AND (jv.title LIKE ? OR jv.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
        }
        
        if (!empty($department)) {
            $query .= " AND jv.department_id = ?";
            $params[] = $department;
            $types .= 'i';
        }
        
        if (!empty($vesselType)) {
            $query .= " AND jv.vessel_type_id = ?";
            $params[] = $vesselType;
            $types .= 'i';
        }
        
        $query .= " GROUP BY jv.id ORDER BY jv.created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $vacancies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $vacancies = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        }
        
        $data = [
            'pageTitle' => 'Lowongan Kerja',
            'vacancies' => $vacancies,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes
        ];
        
        $this->view('crewing/vacancies/index', $data);
    }
    
    /**
     * View vacancy detail with share options
     */
    public function detail($id) {
        // Get vacancy details
        $stmt = $this->db->prepare("
            SELECT jv.*, 
                   d.name as department_name,
                   vt.name as vessel_type_name,
                   COUNT(DISTINCT a.id) as applications_count
            FROM job_vacancies jv
            LEFT JOIN departments d ON jv.department_id = d.id
            LEFT JOIN vessel_types vt ON jv.vessel_type_id = vt.id
            LEFT JOIN applications a ON jv.id = a.vacancy_id
            WHERE jv.id = ? AND jv.status = 'published'
            GROUP BY jv.id
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();
        
        if (!$vacancy) {
            flash('error', 'Lowongan tidak ditemukan');
            redirect(url('/crewing/vacancies'));
        }
        
        // Generate share link with recruiter tracking
        $crewingId = $_SESSION['user_id'];
        $shareUrl = url("/jobs/{$id}?ref=crewing&recruiter_id={$crewingId}");
        
        // Get share statistics for this vacancy by current crewing
        $statsStmt = $this->db->prepare("
            SELECT COUNT(*) as total_shares
            FROM vacancy_shares 
            WHERE vacancy_id = ? AND shared_by = ?
        ");
        $statsStmt->bind_param('ii', $id, $crewingId);
        $statsStmt->execute();
        $stats = $statsStmt->get_result()->fetch_assoc();
        
        $data = [
            'pageTitle' => $vacancy['title'],
            'vacancy' => $vacancy,
            'shareUrl' => $shareUrl,
            'stats' => $stats
        ];
        
        $this->view('crewing/vacancies/detail', $data);
    }
    
    /**
     * Generate and track share link
     */
    public function generateShareLink($id) {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        validate_csrf();
        
        $shareMethod = $this->input('method') ?? 'link'; // link, whatsapp, email, qr
        $crewingId = $_SESSION['user_id'];
        
        // Check vacancy exists
        $stmt = $this->db->prepare("SELECT id, title FROM job_vacancies WHERE id = ? AND status = 'published'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();
        
        if (!$vacancy) {
            return $this->json(['success' => false, 'message' => 'Lowongan tidak ditemukan']);
        }
        
        // Generate share URL
        $shareUrl = url("/jobs/{$id}?ref=crewing&recruiter_id={$crewingId}");
        
        // Track the share
        $trackStmt = $this->db->prepare("
            INSERT INTO vacancy_shares (vacancy_id, shared_by, share_method, share_url, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $trackStmt->bind_param('iiss', $id, $crewingId, $shareMethod, $shareUrl);
        $trackStmt->execute();
        
        // Generate WhatsApp message
        $whatsappMessage = "🚢 *Lowongan Kerja PT Indo Ocean*\n\n";
        $whatsappMessage .= "*{$vacancy['title']}*\n\n";
        $whatsappMessage .= "Tertarik? Lihat detail dan apply di:\n{$shareUrl}";
        $whatsappUrl = "https://wa.me/?text=" . urlencode($whatsappMessage);
        
        return $this->json([
            'success' => true,
            'shareUrl' => $shareUrl,
            'whatsappUrl' => $whatsappUrl,
            'message' => 'Link berhasil dibuat'
        ]);
    }
    
    /**
     * Show create vacancy form
     */
    public function create() {
        $departments = $this->db->query("SELECT MIN(id) as id, name FROM departments WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT MIN(id) as id, name FROM vessel_types WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->view('crewing/vacancies/form', [
            'vacancy' => null,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Buat Lowongan Baru'
        ]);
    }
    
    /**
     * Store new vacancy
     */
    public function store() {
        if (!$this->isPost()) {
            redirect(url('/crewing/vacancies'));
            return;
        }

        validate_csrf();

        $title = trim($this->input('title') ?? '');
        $titleId = trim($this->input('title_id') ?? '');
        $departmentId = $this->input('department_id') ?: null;
        $vesselTypeId = $this->input('vessel_type_id') ?: null;
        $salaryMin = $this->input('salary_min') !== '' ? $this->input('salary_min') : null;
        $salaryMax = $this->input('salary_max') !== '' ? $this->input('salary_max') : null;
        $salaryCurrency = $this->input('salary_currency') ?: 'USD';
        $contractDuration = $this->input('contract_duration_months') !== '' ? $this->input('contract_duration_months') : null;
        $joiningDate = $this->input('joining_date') ?: null;
        $description = $this->input('description') ?? '';
        $descriptionId = $this->input('description_id') ?? '';
        $requirements = $this->input('requirements') ?? '';
        $requirementsId = $this->input('requirements_id') ?? '';
        $minExperience = $this->input('min_experience_months') !== '' ? $this->input('min_experience_months') : 0;
        $minAge = $this->input('min_age') !== '' ? $this->input('min_age') : null;
        $maxAge = $this->input('max_age') !== '' ? $this->input('max_age') : null;
        $status = trim($this->input('status') ?: 'published');
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $deadline = $this->input('application_deadline') ?: null;

        // Handle ship photo upload
        $shipPhotoPath = null;
        if (isset($_FILES['ship_photo']) && $_FILES['ship_photo']['error'] === UPLOAD_ERR_OK) {
            $shipPhotoPath = $this->handleShipPhotoUpload($_FILES['ship_photo']);
            if (!$shipPhotoPath) {
                flash('error', 'Gagal upload foto kapal');
                $_SESSION['old'] = $_POST;
                redirect(url('/crewing/vacancies/create'));
                return;
            }
        }

        // Generate slug
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)) . '-' . time();

        // Handle required certificates
        $requiredCertsInput = trim($this->input('required_certificates') ?? '');
        if (empty($requiredCertsInput)) {
            $certsJson = null;
        } else {
            $certsArray = array_filter(array_map('trim', explode("\n", $requiredCertsInput)));
            $certsJson = json_encode(array_values($certsArray));
        }
        
        // Auto-fix: Convert JSON column to TEXT to avoid validation errors
        try {
            $this->db->query("ALTER TABLE job_vacancies MODIFY COLUMN required_certificates TEXT NULL");
        } catch (\Exception $e) {
            error_log("Failed to alter required_certificates column: " . $e->getMessage());
        }

        $stmt = $this->db->prepare("
            INSERT INTO job_vacancies (
                department_id, vessel_type_id, ship_photo, title, title_id, slug,
                salary_min, salary_max, salary_currency, contract_duration_months,
                joining_date, description, description_id, requirements, requirements_id,
                min_experience_months, min_age, max_age, required_certificates,
                status, is_featured, application_deadline, created_by, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $userId = $_SESSION['user_id'];
        $stmt->bind_param(
            'iissssddssssssiiisisssi',
            $departmentId,
            $vesselTypeId,
            $shipPhotoPath,
            $title,
            $titleId,
            $slug,
            $salaryMin,
            $salaryMax,
            $salaryCurrency,
            $contractDuration,
            $joiningDate,
            $description,
            $descriptionId,
            $requirements,
            $requirementsId,
            $minExperience,
            $minAge,
            $maxAge,
            $certsJson,
            $status,
            $isFeatured,
            $deadline,
            $userId
        );

        if ($stmt->execute()) {
            flash('success', 'Lowongan berhasil dibuat!');
            redirect(url('/crewing/vacancies'));
        } else {
            flash('error', 'Gagal membuat lowongan: ' . $this->db->error);
            $_SESSION['old'] = $_POST;
            redirect(url('/crewing/vacancies/create'));
        }
    }
    
    /**
     * Show edit vacancy form
     */
    public function edit($id) {
        $stmt = $this->db->prepare("SELECT * FROM job_vacancies WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();

        if (!$vacancy) {
            flash('error', 'Lowongan tidak ditemukan');
            redirect(url('/crewing/vacancies'));
            return;
        }

        $departments = $this->db->query("SELECT MIN(id) as id, name FROM departments WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT MIN(id) as id, name FROM vessel_types WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->view('crewing/vacancies/form', [
            'vacancy' => $vacancy,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Edit Lowongan'
        ]);
    }
    
    /**
     * Update vacancy
     */
    public function update($id) {
        if (!$this->isPost()) {
            redirect(url('/crewing/vacancies'));
            return;
        }

        validate_csrf();

        $title = trim($this->input('title') ?? '');
        $titleId = trim($this->input('title_id') ?? '');
        $departmentId = $this->input('department_id') ?: null;
        $vesselTypeId = $this->input('vessel_type_id') ?: null;
        $salaryMin = $this->input('salary_min') !== '' ? $this->input('salary_min') : null;
        $salaryMax = $this->input('salary_max') !== '' ? $this->input('salary_max') : null;
        $salaryCurrency = $this->input('salary_currency') ?: 'USD';
        $contractDuration = $this->input('contract_duration_months') !== '' ? $this->input('contract_duration_months') : null;
        $joiningDate = $this->input('joining_date') ?: null;
        $description = $this->input('description') ?? '';
        $descriptionId = $this->input('description_id') ?? '';
        $requirements = $this->input('requirements') ?? '';
        $requirementsId = $this->input('requirements_id') ?? '';
        $minExperience = $this->input('min_experience_months') !== '' ? $this->input('min_experience_months') : 0;
        $minAge = $this->input('min_age') !== '' ? $this->input('min_age') : null;
        $maxAge = $this->input('max_age') !== '' ? $this->input('max_age') : null;
        $status = trim($this->input('status') ?: 'published');
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $deadline = $this->input('application_deadline') ?: null;
        
        // Debug and auto-fix status column
        error_log("DEBUG: Status value = '" . $status . "' (length: " . strlen($status) . ")");
        try { $this->db->query("ALTER TABLE job_vacancies MODIFY COLUMN status VARCHAR(20) DEFAULT 'draft'"); } catch (\Exception $e) {}

        // Handle ship photo upload
        $shipPhotoPath = null;
        if (isset($_FILES['ship_photo']) && $_FILES['ship_photo']['error'] === UPLOAD_ERR_OK) {
            // Get old photo
            $oldPhotoStmt = $this->db->prepare("SELECT ship_photo FROM job_vacancies WHERE id = ?");
            $oldPhotoStmt->bind_param('i', $id);
            $oldPhotoStmt->execute();
            $oldPhoto = $oldPhotoStmt->get_result()->fetch_assoc()['ship_photo'] ?? null;

            $shipPhotoPath = $this->handleShipPhotoUpload($_FILES['ship_photo']);
            if (!$shipPhotoPath) {
                flash('error', 'Gagal upload foto kapal');
                redirect(url('/crewing/vacancies/edit/' . $id));
                return;
            }

            // Delete old photo
            if ($oldPhoto && file_exists(dirname(APPPATH) . '/public/' . $oldPhoto)) {
                @unlink(dirname(APPPATH) . '/public/' . $oldPhoto);
            }
        }

        // Handle required certificates
        $requiredCertsInput = trim($this->input('required_certificates') ?? '');
        if (empty($requiredCertsInput)) {
            $certsJson = null;
        } else {
            $certsArray = array_filter(array_map('trim', explode("\n", $requiredCertsInput)));
            $certsJson = json_encode(array_values($certsArray));
        }
        
        // Auto-fix: Convert JSON column to TEXT to avoid validation errors
        try {
            $this->db->query("ALTER TABLE job_vacancies MODIFY COLUMN required_certificates TEXT NULL");
        } catch (\Exception $e) {
            error_log("Failed to alter required_certificates column: " . $e->getMessage());
        }

        // Build UPDATE query
        if ($shipPhotoPath) {
            $stmt = $this->db->prepare("
                UPDATE job_vacancies SET
                    department_id = ?, vessel_type_id = ?, ship_photo = ?, title = ?, title_id = ?,
                    salary_min = ?, salary_max = ?, salary_currency = ?, contract_duration_months = ?,
                    joining_date = ?, description = ?, description_id = ?, requirements = ?, requirements_id = ?,
                    min_experience_months = ?, min_age = ?, max_age = ?, required_certificates = ?,
                    status = ?, is_featured = ?, application_deadline = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param(
                'iisssddssssssiiississi',
                $departmentId, $vesselTypeId, $shipPhotoPath, $title, $titleId,
                $salaryMin, $salaryMax, $salaryCurrency, $contractDuration, $joiningDate,
                $description, $descriptionId, $requirements, $requirementsId,
                $minExperience, $minAge, $maxAge, $certsJson,
                $status, $isFeatured, $deadline, $id
            );
        } else {
            $stmt = $this->db->prepare("
                UPDATE job_vacancies SET
                    department_id = ?, vessel_type_id = ?, title = ?, title_id = ?,
                    salary_min = ?, salary_max = ?, salary_currency = ?, contract_duration_months = ?,
                    joining_date = ?, description = ?, description_id = ?, requirements = ?, requirements_id = ?,
                    min_experience_months = ?, min_age = ?, max_age = ?, required_certificates = ?,
                    status = ?, is_featured = ?, application_deadline = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param(
                'iissddssssssiiississi',
                $departmentId, $vesselTypeId, $title, $titleId,
                $salaryMin, $salaryMax, $salaryCurrency, $contractDuration, $joiningDate,
                $description, $descriptionId, $requirements, $requirementsId,
                $minExperience, $minAge, $maxAge, $certsJson,
                $status, $isFeatured, $deadline, $id
            );
        }

        if ($stmt->execute()) {
            error_log("SUCCESS: Vacancy updated successfully. ID: " . $id);
            flash('success', 'Lowongan berhasil diupdate!');
            redirect(url('/crewing/vacancies/detail/' . $id));
        } else {
            error_log("ERROR: Failed to update vacancy. Error: " . $this->db->error . " | SQL Error: " . $stmt->error);
            error_log("DEBUG: Department ID: " . $departmentId . ", Vessel Type ID: " . $vesselTypeId);
            error_log("DEBUG: Status: '" . $status . "', Featured: " . $isFeatured);
            flash('error', 'Gagal update lowongan: ' . $stmt->error);
            $_SESSION['old'] = $_POST;
            redirect(url('/crewing/vacancies/edit/' . $id));
        }
    }
    
    /**
     * Handle ship photo upload
     */
    private function handleShipPhotoUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) return false;
        if ($file['size'] > 5 * 1024 * 1024) return false;
        
        $uploadDir = dirname(APPPATH) . '/public/uploads/ships/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'ship_' . time() . '_' . uniqid() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return 'uploads/ships/' . $filename;
        }
        return false;
    }
}
