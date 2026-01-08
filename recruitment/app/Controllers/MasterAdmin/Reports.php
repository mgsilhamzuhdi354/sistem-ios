<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Reports Controller
 */
class Reports extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect('/login');
        }
    }
    
    public function index() {
        // Overall statistics
        $stats = $this->getOverallStats();
        
        // Monthly application trends
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Applications by status
        $statusStats = $this->getStatusStats();
        
        // Applications by department
        $departmentStats = $this->getDepartmentStats();
        
        // Top performing crewing
        $crewingPerformance = $this->getCrewingPerformance();
        
        // Recent activity
        $recentActivity = $this->getRecentActivity();
        
        $this->view('master_admin/reports/index', [
            'pageTitle' => 'Reports & Analytics',
            'stats' => $stats,
            'monthlyTrends' => $monthlyTrends,
            'statusStats' => $statusStats,
            'departmentStats' => $departmentStats,
            'crewingPerformance' => $crewingPerformance,
            'recentActivity' => $recentActivity
        ]);
    }
    
    private function getOverallStats() {
        $stats = [];
        
        // Total applications
        $result = $this->db->query("SELECT COUNT(*) as total FROM applications");
        $stats['total_applications'] = $result->fetch_assoc()['total'];
        
        // This month applications
        $result = $this->db->query("SELECT COUNT(*) as total FROM applications WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
        $stats['this_month'] = $result->fetch_assoc()['total'];
        
        // Total users
        $result = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role_id = 3");
        $stats['total_applicants'] = $result->fetch_assoc()['total'];
        
        // Active vacancies
        $result = $this->db->query("SELECT COUNT(*) as total FROM job_vacancies WHERE status = 'active'");
        $stats['active_vacancies'] = $result ? $result->fetch_assoc()['total'] : 0;
        
        // Hired this month
        $result = $this->db->query("SELECT COUNT(*) as total FROM applications WHERE status_id = 6 AND MONTH(status_updated_at) = MONTH(NOW())");
        $stats['hired_this_month'] = $result->fetch_assoc()['total'];
        
        // Pending review
        $result = $this->db->query("SELECT COUNT(*) as total FROM applications WHERE status_id IN (1, 2)");
        $stats['pending_review'] = $result->fetch_assoc()['total'];
        
        return $stats;
    }
    
    private function getMonthlyTrends() {
        $result = $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM applications
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getStatusStats() {
        $result = $this->db->query("
            SELECT s.name, s.color, COUNT(a.id) as count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            GROUP BY s.id, s.name, s.color
            ORDER BY s.sort_order
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getDepartmentStats() {
        $result = $this->db->query("
            SELECT d.name, COUNT(a.id) as count
            FROM departments d
            LEFT JOIN job_vacancies jv ON d.id = jv.department_id
            LEFT JOIN applications a ON jv.id = a.vacancy_id
            GROUP BY d.id, d.name
            ORDER BY count DESC
            LIMIT 5
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getCrewingPerformance() {
        $result = $this->db->query("
            SELECT 
                u.full_name,
                COUNT(DISTINCT aa.application_id) as handled,
                AVG(cr.rating) as avg_rating,
                COUNT(DISTINCT CASE WHEN aa.status = 'completed' THEN aa.id END) as completed
            FROM users u
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN crewing_ratings cr ON u.id = cr.crewing_id
            WHERE u.role_id = 2 AND u.is_active = 1
            GROUP BY u.id, u.full_name
            ORDER BY handled DESC
            LIMIT 5
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getRecentActivity() {
        $result = $this->db->query("
            SELECT 
                'application' as type,
                CONCAT('New application from ', u.full_name) as description,
                a.created_at as time
            FROM applications a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT 10
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
