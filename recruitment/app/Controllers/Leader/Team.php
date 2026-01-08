<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Leader Team Controller - Manage crewing team and transfers
 */
class Team extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || (!isLeader() && !isMasterAdmin())) {
            redirect('/login');
        }
    }
    
    public function index() {
        // Get all crewing with their workload and online status
        $crewingResult = $this->db->query("
            SELECT 
                u.id, u.full_name, u.email, u.is_online, u.last_activity,
                cp.rank, cp.company, cp.employee_id, cp.max_applications,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_count,
                COUNT(DISTINCT CASE WHEN aa.status = 'completed' THEN aa.id END) as completed_count,
                COALESCE(AVG(cr.rating), 0) as avg_rating,
                COUNT(DISTINCT cr.id) as total_ratings
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN crewing_ratings cr ON u.id = cr.crewing_id
            WHERE u.role_id = 2 AND u.is_active = 1
            GROUP BY u.id
            ORDER BY u.full_name
        ");
        $crewingStaff = $crewingResult ? $crewingResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get applications that can be transferred
        $appsResult = $this->db->query("
            SELECT a.*, 
                   u.full_name as applicant_name,
                   jv.title as vacancy_title,
                   s.name as status_name,
                   uc.full_name as crewing_name, uc.id as crewing_id
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN users uc ON a.current_crewing_id = uc.id
            WHERE a.current_crewing_id IS NOT NULL
            AND a.status_id NOT IN (6, 7)
            ORDER BY a.created_at DESC
        ");
        $applications = $appsResult ? $appsResult->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('leader/team/index', [
            'pageTitle' => 'Team Management',
            'crewingStaff' => $crewingStaff,
            'applications' => $applications
        ]);
    }
    
    public function transfer() {
        if (!$this->isPost()) {
            redirect('/leader/team');
        }
        
        validate_csrf();
        
        $applicationId = $this->input('application_id');
        $fromCrewingId = $this->input('from_crewing_id');
        $toCrewingId = $this->input('to_crewing_id');
        $reason = $this->input('reason');
        
        if (empty($applicationId) || empty($toCrewingId)) {
            flash('error', 'Please select application and target crewing.');
            redirect('/leader/team');
        }
        
        if ($fromCrewingId == $toCrewingId) {
            flash('error', 'Cannot transfer to the same crewing.');
            redirect('/leader/team');
        }
        
        if (transferHandler($applicationId, $fromCrewingId, $toCrewingId, $reason)) {
            flash('success', 'Application transferred successfully.');
        } else {
            flash('error', 'Failed to transfer application.');
        }
        
        redirect('/leader/team');
    }
    
    public function getCrewingDetails($id) {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, u.full_name, u.email, u.is_online, u.last_activity,
                cp.*,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_count
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $crewing = $stmt->get_result()->fetch_assoc();
        
        // Get current assignments
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.full_name as applicant_name,
                   jv.title as vacancy_title,
                   s.name as status_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.current_crewing_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return $this->json([
            'crewing' => $crewing,
            'assignments' => $assignments
        ]);
    }
}
