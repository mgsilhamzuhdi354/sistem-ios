<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Home Controller - Public Landing Page
 */
class Home extends BaseController {
    
    public function index() {
        // Get featured vacancies with recruiter info
        $stmt = $this->db->prepare("
            SELECT v.*, d.name as department_name, d.icon as department_icon, vt.name as vessel_type,
                   u.full_name as recruiter_name, cp.photo as recruiter_photo
            FROM job_vacancies v
            LEFT JOIN departments d ON v.department_id = d.id
            LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
            LEFT JOIN users u ON v.created_by = u.id
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            WHERE v.status = 'published' AND v.is_featured = 1
            ORDER BY v.created_at DESC
            LIMIT 6
        ");
        $stmt->execute();
        $featuredJobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $stats = [
            'total_vacancies' => $this->getCount("SELECT COUNT(*) FROM job_vacancies WHERE status = 'published'"),
            'total_applicants' => $this->getCount("SELECT COUNT(*) FROM users WHERE role_id = 3"),
            'total_hired' => $this->getCount("SELECT COUNT(*) FROM applications WHERE status_id = 6"),
        ];
        
        // Get departments (excluding Hotel Department and Entertainment)
        $deptStmt = $this->db->query("SELECT * FROM departments WHERE is_active = 1 AND name NOT IN ('Hotel Department', 'Entertainment')");
        $departments = $deptStmt->fetch_all(MYSQLI_ASSOC);
        
        $this->view('home/index', [
            'featuredJobs' => $featuredJobs,
            'stats' => $stats,
            'departments' => $departments,
            'pageTitle' => 'Welcome'
        ]);
    }
    
    private function getCount($query) {
        $result = $this->db->query($query);
        $row = $result->fetch_row();
        return $row[0] ?? 0;
    }
}
