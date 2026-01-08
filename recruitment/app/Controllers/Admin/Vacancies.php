<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Vacancies Controller
 */
class Vacancies extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        // Allow both Admin (role 1) and Master Admin (role 11)
        if (!isLoggedIn() || !canManageVacancy()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
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
    
    public function create() {
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT * FROM vessel_types WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/vacancies/form', [
            'vacancy' => null,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Create Job Vacancy'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/vacancies'));
        }
        
        validate_csrf();
        
        $title = trim($this->input('title'));
        $titleId = trim($this->input('title_id'));
        $departmentId = $this->input('department_id');
        $vesselTypeId = $this->input('vessel_type_id');
        $salaryMin = $this->input('salary_min');
        $salaryMax = $this->input('salary_max');
        $salaryCurrency = $this->input('salary_currency') ?: 'USD';
        $contractDuration = $this->input('contract_duration_months');
        $joiningDate = $this->input('joining_date');
        $description = $this->input('description');
        $descriptionId = $this->input('description_id');
        $requirements = $this->input('requirements');
        $requirementsId = $this->input('requirements_id');
        $minExperience = $this->input('min_experience_months') ?: 0;
        $minAge = $this->input('min_age');
        $maxAge = $this->input('max_age');
        $requiredCerts = $this->input('required_certificates') ?: [];
        $status = $this->input('status') ?: 'draft';
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $deadline = $this->input('application_deadline');
        
        // Generate slug
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)) . '-' . time();
        
        // JSON encode certificates
        $certsJson = json_encode($requiredCerts);
        
        $stmt = $this->db->prepare("
            INSERT INTO job_vacancies (
                department_id, vessel_type_id, title, title_id, slug,
                salary_min, salary_max, salary_currency, contract_duration_months,
                joining_date, description, description_id, requirements, requirements_id,
                min_experience_months, min_age, max_age, required_certificates,
                status, is_featured, application_deadline, created_by, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $userId = $_SESSION['user_id'];
        $stmt->bind_param('iisssddsssssssiiisssis',
            $departmentId, $vesselTypeId, $title, $titleId, $slug,
            $salaryMin, $salaryMax, $salaryCurrency, $contractDuration,
            $joiningDate, $description, $descriptionId, $requirements, $requirementsId,
            $minExperience, $minAge, $maxAge, $certsJson,
            $status, $isFeatured, $deadline, $userId
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
    
    public function edit($id) {
        $stmt = $this->db->prepare("SELECT * FROM job_vacancies WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();
        
        if (!$vacancy) {
            flash('error', 'Vacancy not found');
            $this->redirect(url('/admin/vacancies'));
        }
        
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT * FROM vessel_types WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/vacancies/form', [
            'vacancy' => $vacancy,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Edit Job Vacancy'
        ]);
    }
    
    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/vacancies'));
        }
        
        validate_csrf();
        
        $title = trim($this->input('title'));
        $titleId = trim($this->input('title_id'));
        $departmentId = $this->input('department_id');
        $vesselTypeId = $this->input('vessel_type_id');
        $salaryMin = $this->input('salary_min');
        $salaryMax = $this->input('salary_max');
        $salaryCurrency = $this->input('salary_currency') ?: 'USD';
        $contractDuration = $this->input('contract_duration_months');
        $joiningDate = $this->input('joining_date');
        $description = $this->input('description');
        $descriptionId = $this->input('description_id');
        $requirements = $this->input('requirements');
        $requirementsId = $this->input('requirements_id');
        $minExperience = $this->input('min_experience_months') ?: 0;
        $minAge = $this->input('min_age');
        $maxAge = $this->input('max_age');
        $requiredCerts = $this->input('required_certificates') ?: [];
        $status = $this->input('status') ?: 'draft';
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $deadline = $this->input('application_deadline');
        
        $certsJson = json_encode($requiredCerts);
        
        $stmt = $this->db->prepare("
            UPDATE job_vacancies SET
                department_id = ?, vessel_type_id = ?, title = ?, title_id = ?,
                salary_min = ?, salary_max = ?, salary_currency = ?, contract_duration_months = ?,
                joining_date = ?, description = ?, description_id = ?, requirements = ?, requirements_id = ?,
                min_experience_months = ?, min_age = ?, max_age = ?, required_certificates = ?,
                status = ?, is_featured = ?, application_deadline = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param('iissddsssssssiiisssisi',
            $departmentId, $vesselTypeId, $title, $titleId,
            $salaryMin, $salaryMax, $salaryCurrency, $contractDuration,
            $joiningDate, $description, $descriptionId, $requirements, $requirementsId,
            $minExperience, $minAge, $maxAge, $certsJson,
            $status, $isFeatured, $deadline, $id
        );
        
        if ($stmt->execute()) {
            flash('success', 'Job vacancy updated successfully');
        } else {
            flash('error', 'Failed to update vacancy');
        }
        
        $this->redirect(url('/admin/vacancies/edit/' . $id));
    }
    
    public function delete($id) {
        // Check if has applications
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as c FROM applications WHERE vacancy_id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $count = $checkStmt->get_result()->fetch_assoc()['c'];
        
        if ($count > 0) {
            flash('error', 'Cannot delete vacancy with existing applications');
        } else {
            $this->db->query("DELETE FROM job_vacancies WHERE id = " . intval($id));
            flash('success', 'Vacancy deleted');
        }
        
        $this->redirect(url('/admin/vacancies'));
    }
}
