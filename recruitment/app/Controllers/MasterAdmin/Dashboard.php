<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Dashboard Controller
 */
class Dashboard extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            flash('error', 'Access denied. Master Admin only.');
            redirect('/login');
        }
    }
    
    public function index() {
        // Get system overview stats
        $stats = $this->getSystemStats();
        
        // Get all users by role
        $usersByRole = $this->getUsersByRole();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get leaders and admins
        $leaders = getAllLeaders();
        $admins = $this->getAdmins();
        
        // Get crewing performance chart data
        $crewingPerformance = $this->getCrewingPerformanceChart();
        
        $this->view('master_admin/dashboard/index', [
            'pageTitle' => 'Master Admin Dashboard',
            'stats' => $stats,
            'usersByRole' => $usersByRole,
            'recentActivities' => $recentActivities,
            'leaders' => $leaders,
            'admins' => $admins,
            'crewingPerformance' => $crewingPerformance
        ]);
    }
    
    private function getSystemStats() {
        $result = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM users WHERE role_id = 0) as total_master_admins,
                (SELECT COUNT(*) FROM users WHERE role_id = 1) as total_admins,
                (SELECT COUNT(*) FROM users WHERE role_id = 4) as total_leaders,
                (SELECT COUNT(*) FROM users WHERE role_id = 2) as total_crewing,
                (SELECT COUNT(*) FROM users WHERE role_id = 3) as total_applicants,
                (SELECT COUNT(*) FROM applications) as total_applications,
                (SELECT COUNT(*) FROM applications WHERE status_id = 6) as total_hired,
                (SELECT COUNT(*) FROM job_vacancies WHERE status = 'active') as active_vacancies,
                (SELECT COUNT(*) FROM pipeline_requests WHERE status = 'pending') as pending_requests
        ");
        return $result->fetch_assoc();
    }
    
    private function getUsersByRole() {
        $result = $this->db->query("
            SELECT r.name as role_name, COUNT(u.id) as count
            FROM roles r
            LEFT JOIN users u ON r.id = u.role_id
            GROUP BY r.id
            ORDER BY r.id
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getRecentActivities() {
        $result = $this->db->query("
            SELECT al.*, u.full_name as user_name
            FROM automation_logs al
            LEFT JOIN users u ON al.created_by = u.id
            ORDER BY al.created_at DESC
            LIMIT 10
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getAdmins() {
        $result = $this->db->query("
            SELECT u.*, 
                   (SELECT COUNT(*) FROM job_vacancies WHERE created_by = u.id) as vacancies_created
            FROM users u
            WHERE u.role_id = 1 AND u.is_active = 1
            ORDER BY u.full_name
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getCrewingPerformanceChart() {
        $result = $this->db->query("
            SELECT 
                u.id, u.full_name,
                COUNT(DISTINCT CASE WHEN aa.status = 'completed' THEN aa.id END) as completed,
                COUNT(DISTINCT CASE WHEN a.status_id = 6 AND aa.status = 'completed' THEN a.id END) as hired
            FROM users u
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN applications a ON aa.application_id = a.id
            WHERE u.role_id = 2 AND u.is_active = 1
            GROUP BY u.id
            ORDER BY completed DESC
            LIMIT 10
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
