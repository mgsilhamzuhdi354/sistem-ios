<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Leader Dashboard Controller
 */
class Dashboard extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || (!isLeader() && !isMasterAdmin())) {
            redirect('/login');
        }
    }
    
    public function index() {
        $leaderId = $_SESSION['user_id'];
        
        // Get team stats
        $teamStats = $this->getTeamStats($leaderId);
        
        // Get pending requests
        $pendingRequests = getPendingRequests($leaderId);
        
        // Get pipeline overview
        $pipelineStats = $this->getPipelineStats();
        
        // Get recent applications handled by team
        $recentApps = $this->getRecentApplications();
        
        // Get crewing performance
        $crewingPerformance = $this->getCrewingPerformance();
        
        // Get online crewing
        $onlineCrewingStaff = getOnlineCrewingStaff();
        
        $this->view('leader/dashboard/index', [
            'pageTitle' => 'Leader Dashboard',
            'teamStats' => $teamStats,
            'pendingRequests' => $pendingRequests,
            'pipelineStats' => $pipelineStats,
            'recentApps' => $recentApps,
            'crewingPerformance' => $crewingPerformance,
            'onlineCrewingStaff' => $onlineCrewingStaff
        ]);
    }
    
    private function getTeamStats($leaderId) {
        $result = $this->db->query("
            SELECT 
                COUNT(DISTINCT u.id) as total_crewing,
                COUNT(DISTINCT CASE WHEN u.is_online = 1 THEN u.id END) as online_crewing,
                COUNT(DISTINCT aa.id) as total_assignments,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.role_id = 2 AND u.is_active = 1
        ");
        return $result->fetch_assoc();
    }
    
    private function getPipelineStats() {
        $result = $this->db->query("
            SELECT 
                s.id, s.name,
                COUNT(a.id) as count,
                COALESCE(SUM(CASE WHEN aa.status = 'active' THEN 1 ELSE 0 END), 0) as assigned_count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            GROUP BY s.id, s.name
            ORDER BY s.sort_order
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getRecentApplications() {
        $result = $this->db->query("
            SELECT a.*, 
                   u.full_name as applicant_name, u.email as applicant_email,
                   jv.title as vacancy_title, d.name as department_name,
                   s.name as status_name, s.color as status_color,
                   uc.full_name as crewing_name, uc.id as crewing_id,
                   cp.rank as crewing_rank, cp.company as crewing_company
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN departments d ON jv.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN users uc ON a.current_crewing_id = uc.id
            LEFT JOIN crewing_profiles cp ON uc.id = cp.user_id
            ORDER BY a.created_at DESC
            LIMIT 10
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getCrewingPerformance() {
        $result = $this->db->query("
            SELECT 
                u.id, u.full_name, u.email, u.is_online,
                cp.rank, cp.company,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_count,
                COUNT(DISTINCT CASE WHEN aa.status = 'completed' THEN aa.id END) as completed_count,
                COUNT(DISTINCT CASE WHEN a.status_id = 6 THEN a.id END) as hired_count,
                COALESCE(AVG(cr.rating), 0) as avg_rating
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN applications a ON aa.application_id = a.id
            LEFT JOIN crewing_ratings cr ON u.id = cr.crewing_id
            WHERE u.role_id = 2 AND u.is_active = 1
            GROUP BY u.id
            ORDER BY completed_count DESC, hired_count DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
