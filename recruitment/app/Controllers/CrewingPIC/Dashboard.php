<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing PIC Dashboard Controller
 */
class Dashboard extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || (!isCrewingPIC() && !isLeaderOrAbove())) {
            redirect('/login');
        }
    }
    
    public function index() {
        $picId = $_SESSION['user_id'];
        
        // Get team stats (crewing under this PIC)
        $teamStats = $this->getTeamStats();
        
        // Get pending requests from crewing
        $pendingRequests = getPendingRequests($picId);
        
        // Get pipeline overview
        $pipelineStats = $this->getPipelineStats();
        
        // Get recent applications
        $recentApps = $this->getRecentApplications();
        
        // Get online crewing
        $onlineCrewingStaff = getOnlineCrewingStaff();
        
        $this->view('crewing_pic/dashboard/index', [
            'pageTitle' => 'Crewing PIC Dashboard',
            'teamStats' => $teamStats,
            'pendingRequests' => $pendingRequests,
            'pipelineStats' => $pipelineStats,
            'recentApps' => $recentApps,
            'onlineCrewingStaff' => $onlineCrewingStaff
        ]);
    }
    
    private function getTeamStats() {
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
                COUNT(a.id) as count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            GROUP BY s.id, s.name
            ORDER BY s.sort_order
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getRecentApplications() {
        $result = $this->db->query("
            SELECT a.*, 
                   u.full_name as applicant_name, u.email as applicant_email,
                   jv.title as vacancy_title,
                   s.name as status_name, s.color as status_color,
                   uc.full_name as crewing_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN users uc ON a.current_crewing_id = uc.id
            ORDER BY a.created_at DESC
            LIMIT 10
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
