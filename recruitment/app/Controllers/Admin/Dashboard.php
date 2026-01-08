<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Dashboard Controller
 */
class Dashboard extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isAdmin()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get stats
        $stats = [
            'total_applicants' => $this->getCount("SELECT COUNT(*) FROM users WHERE role_id = 3"),
            'new_today' => $this->getCount("SELECT COUNT(*) FROM applications WHERE DATE(submitted_at) = CURDATE()"),
            'pending_review' => $this->getCount("SELECT COUNT(*) FROM applications WHERE status_id IN (1, 2)"),
            'in_interview' => $this->getCount("SELECT COUNT(*) FROM applications WHERE status_id = 3"),
            'hired_month' => $this->getCount("SELECT COUNT(*) FROM applications WHERE status_id = 6 AND MONTH(status_updated_at) = MONTH(NOW())"),
            'total_vacancies' => $this->getCount("SELECT COUNT(*) FROM job_vacancies WHERE status = 'published'"),
        ];
        
        // Get pipeline data
        $pipelineStmt = $this->db->query("
            SELECT s.id, s.name, s.color, COUNT(a.id) as count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            GROUP BY s.id
            ORDER BY s.sort_order
        ");
        $pipeline = $pipelineStmt->fetch_all(MYSQLI_ASSOC);
        
        // Get department distribution
        $deptStmt = $this->db->query("
            SELECT d.name, d.color, COUNT(a.id) as count
            FROM departments d
            LEFT JOIN job_vacancies v ON d.id = v.department_id
            LEFT JOIN applications a ON v.id = a.vacancy_id
            GROUP BY d.id
        ");
        $departments = $deptStmt->fetch_all(MYSQLI_ASSOC);
        
        // Get recent applications
        $recentStmt = $this->db->query("
            SELECT a.*, u.full_name, u.email, v.title as vacancy_title,
                   s.name as status_name, s.color as status_color
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN application_statuses s ON a.status_id = s.id
            ORDER BY a.submitted_at DESC
            LIMIT 10
        ");
        $recentApplications = $recentStmt->fetch_all(MYSQLI_ASSOC);
        
        // Get weekly applications chart data
        $weeklyStmt = $this->db->query("
            SELECT DATE(submitted_at) as date, COUNT(*) as count
            FROM applications
            WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(submitted_at)
            ORDER BY date
        ");
        $weeklyData = $weeklyStmt->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/dashboard/index', [
            'stats' => $stats,
            'pipeline' => $pipeline,
            'departments' => $departments,
            'recentApplications' => $recentApplications,
            'weeklyData' => $weeklyData,
            'pageTitle' => 'Admin Dashboard'
        ]);
    }
    
    private function getCount($query) {
        $result = $this->db->query($query);
        $row = $result->fetch_row();
        return $row[0] ?? 0;
    }
}
