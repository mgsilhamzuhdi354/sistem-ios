<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Jobs Controller - Job Listings
 */
class Jobs extends BaseController {
    
    public function index() {
        $department = $this->input('department');
        $vesselType = $this->input('vessel_type');
        $search = $this->input('search');
        
        $query = "
            SELECT v.*, d.name as department_name, d.icon as department_icon, 
                   d.color as department_color, vt.name as vessel_type
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
            WHERE v.status = 'published'
        ";
        
        $params = [];
        $types = '';
        
        if ($department) {
            $query .= " AND v.department_id = ?";
            $params[] = $department;
            $types .= 'i';
        }
        
        if ($vesselType) {
            $query .= " AND v.vessel_type_id = ?";
            $params[] = $vesselType;
            $types .= 'i';
        }
        
        if ($search) {
            $query .= " AND (v.title LIKE ? OR v.description LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }
        
        $query .= " ORDER BY v.is_featured DESC, v.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get filter options
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $vesselTypes = $this->db->query("SELECT * FROM vessel_types WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('jobs/index', [
            'jobs' => $jobs,
            'departments' => $departments,
            'vesselTypes' => $vesselTypes,
            'filters' => [
                'department' => $department,
                'vessel_type' => $vesselType,
                'search' => $search
            ],
            'pageTitle' => 'Job Vacancies'
        ]);
    }
    
    public function detail($id) {
        $stmt = $this->db->prepare("
            SELECT v.*, d.name as department_name, d.icon as department_icon, 
                   d.color as department_color, vt.name as vessel_type
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
            WHERE v.id = ? AND v.status = 'published'
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $job = $stmt->get_result()->fetch_assoc();
        
        if (!$job) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        // Increment view count
        $this->db->query("UPDATE job_vacancies SET views_count = views_count + 1 WHERE id = " . intval($id));
        
        // Check if user already applied
        $hasApplied = false;
        if (isLoggedIn()) {
            $checkStmt = $this->db->prepare("SELECT id FROM applications WHERE user_id = ? AND vacancy_id = ?");
            $checkStmt->bind_param('ii', $_SESSION['user_id'], $id);
            $checkStmt->execute();
            $hasApplied = $checkStmt->get_result()->num_rows > 0;
        }
        
        // Get similar jobs
        $similarStmt = $this->db->prepare("
            SELECT v.*, d.name as department_name
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            WHERE v.department_id = ? AND v.id != ? AND v.status = 'published'
            LIMIT 3
        ");
        $similarStmt->bind_param('ii', $job['department_id'], $id);
        $similarStmt->execute();
        $similarJobs = $similarStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('jobs/detail', [
            'job' => $job,
            'hasApplied' => $hasApplied,
            'similarJobs' => $similarJobs,
            'pageTitle' => $job['title']
        ]);
    }
}
