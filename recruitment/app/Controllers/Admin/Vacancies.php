<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Vacancies Controller
 */
class Vacancies extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        // Allow both Admin (role 1) and Master Admin (role 11)
        if (!isLoggedIn() || !canManageVacancy()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }

    public function index()
    {
        $status = $this->input('status');
        $department = $this->input('department');

        $query = "
            SELECT v.*, d.name as department_name, vt.name as vessel_type,
                   u.full_name as created_by_name
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
            LEFT JOIN users u ON v.created_by = u.id
            WHERE 1=1
        ";

        if ($status) {
            $query .= " AND v.status = '" . $this->db->real_escape_string($status) . "'";
        }

        if ($department) {
            $query .= " AND v.department_id = " . intval($department);
        }

        $query .= " ORDER BY v.created_at DESC";

        $vacancies = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);

        $this->view('admin/vacancies/index', [
            'vacancies' => $vacancies,
            'departments' => $departments,
            'filters' => ['status' => $status, 'department' => $department],
            'pageTitle' => 'Job Vacancies'
        ]);
    }

    public function create()
    {
        // Group by name to avoid duplicates in dropdown - strictly selecting columns to satisfy only_full_group_by
        $departments = $this->db->query("SELECT MIN(id) as id, name FROM departments WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT MIN(id) as id, name FROM vessel_types WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->view('admin/vacancies/form', [
            'vacancy' => null,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Create Job Vacancy'
        ]);
    }

    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/vacancies'));
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
        $status = $this->input('status') ?: 'draft';
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $deadline = $this->input('application_deadline') ?: null;

        // Handle ship photo upload
        $shipPhotoPath = null;
        if (isset($_FILES['ship_photo']) && $_FILES['ship_photo']['error'] === UPLOAD_ERR_OK) {
            $shipPhotoPath = $this->handleShipPhotoUpload($_FILES['ship_photo']);
            if (!$shipPhotoPath) {
                flash('error', 'Failed to upload ship photo');
                $_SESSION['old'] = $_POST;
                $this->redirect(url('/admin/vacancies/create'));
                return;
            }
        }

        // Generate slug
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)) . '-' . time();

        // Handle required certificates - convert textarea input to JSON or NULL
        $requiredCertsInput = trim($this->input('required_certificates') ?? '');
        if (empty($requiredCertsInput)) {
            $certsJson = null; // NULL for empty
        } else {
            // Split by newline and trim each line
            $certsArray = array_filter(array_map('trim', explode("\n", $requiredCertsInput)));
            $certsJson = json_encode(array_values($certsArray));
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
            flash('success', 'Job vacancy created successfully');
            $this->redirect(url('/admin/vacancies'));
        } else {
            flash('error', 'Failed to create vacancy');
            $_SESSION['old'] = $_POST;
            $this->redirect(url('/admin/vacancies/create'));
        }
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM job_vacancies WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();

        if (!$vacancy) {
            flash('error', 'Vacancy not found');
            $this->redirect(url('/admin/vacancies'));
        }

        // Group by name to avoid duplicates in dropdown - strictly selecting columns to satisfy only_full_group_by
        $departments = $this->db->query("SELECT MIN(id) as id, name FROM departments WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT MIN(id) as id, name FROM vessel_types WHERE is_active = 1 GROUP BY name ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->view('admin/vacancies/form', [
            'vacancy' => $vacancy,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Edit Job Vacancy'
        ]);
    }

    public function update($id)
    {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/vacancies'));
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
        $status = trim($this->input('status') ?: 'draft');
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $deadline = $this->input('deadline') ?: null;
        
        // Debug: log the status value
        error_log("DEBUG: Status value = '" . $status . "' (length: " . strlen($status) . ")");

        // Handle ship photo upload
        $shipPhotoPath = null;
        if (isset($_FILES['ship_photo']) && $_FILES['ship_photo']['error'] === UPLOAD_ERR_OK) {
            // Get old photo for deletion
            $oldPhotoStmt = $this->db->prepare("SELECT ship_photo FROM job_vacancies WHERE id = ?");
            $oldPhotoStmt->bind_param('i', $id);
            $oldPhotoStmt->execute();
            $oldPhoto = $oldPhotoStmt->get_result()->fetch_assoc()['ship_photo'] ?? null;

            $shipPhotoPath = $this->handleShipPhotoUpload($_FILES['ship_photo']);
            if (!$shipPhotoPath) {
                flash('error', 'Failed to upload ship photo');
                $this->redirect(url('/admin/vacancies/edit/' . $id));
                return;
            }

            // Delete old photo if exists
            if ($oldPhoto && file_exists($oldPhoto)) {
                @unlink($oldPhoto);
            }
        }

        // Handle required certificates - convert textarea input to JSON or NULL
        $requiredCertsInput = trim($this->input('required_certificates') ?? '');
        if (empty($requiredCertsInput)) {
            $certsJson = null; // NULL for empty
        } else {
            // Split by newline and trim each line
            $certsArray = array_filter(array_map('trim', explode("\n", $requiredCertsInput)));
            $certsJson = json_encode(array_values($certsArray));
        }

        // Build UPDATE query based on whether new photo uploaded
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
        }

        if ($shipPhotoPath) {
            $stmt->bind_param(
                'iisssddssssssiiississi',
                $departmentId,
                $vesselTypeId,
                $shipPhotoPath,
                $title,
                $titleId,
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
                $id
            );
        } else {
            $stmt->bind_param(
                'iissddssssssiiississi',
                $departmentId,
                $vesselTypeId,
                $title,
                $titleId,
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
                $id
            );
        }

        if ($stmt->execute()) {
            flash('success', 'Job vacancy updated successfully');
        } else {
            flash('error', 'Failed to update vacancy');
        }

        $this->redirect(url('/admin/vacancies/edit/' . $id));
    }

    public function delete($id)
    {
        // Check if has applications
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as c FROM applications WHERE vacancy_id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $count = $checkStmt->get_result()->fetch_assoc()['c'];

        if ($count > 0) {
            flash('error', 'Cannot delete vacancy with existing applications');
        } else {
            $delStmt = $this->db->prepare("DELETE FROM job_vacancies WHERE id = ?");
            $delStmt->bind_param('i', $id);
            $delStmt->execute();
            flash('success', 'Vacancy deleted');
        }

        $this->redirect(url('/admin/vacancies'));
    }

    /**
     * Handle ship photo upload
     * @param array $file $_FILES['ship_photo']
     * @return string|false Path to uploaded file or false on failure
     */
    private function handleShipPhotoUpload($file)
    {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Create upload directory if not exists
        $uploadDir = 'uploads/ship_photos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'ship_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination;
        }

        return false;
    }
}
