<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Dashboard Controller
 */
class Dashboard extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            flash('error', 'Access denied. You must be a crewing staff to access this page.');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $crewingId = $_SESSION['user_id'];
        
        // Get crewing profile
        $crewingProfile = getCrewingProfile($crewingId);
        
        // Get stats for this crewing
        $stats = [
            'total_assigned' => $this->getCount("
                SELECT COUNT(DISTINCT aa.application_id) 
                FROM application_assignments aa 
                WHERE aa.assigned_to = $crewingId AND aa.status = 'active'
            "),
            'pending_review' => $this->getCount("
                SELECT COUNT(DISTINCT a.id) 
                FROM applications a 
                JOIN application_assignments aa ON a.id = aa.application_id 
                WHERE aa.assigned_to = $crewingId AND aa.status = 'active' AND a.status_id IN (1, 2)
            "),
            'in_interview' => $this->getCount("
                SELECT COUNT(DISTINCT a.id) 
                FROM applications a 
                JOIN application_assignments aa ON a.id = aa.application_id 
                WHERE aa.assigned_to = $crewingId AND aa.status = 'active' AND a.status_id = 3
            "),
            'documents_pending' => $this->getCount("
                SELECT COUNT(DISTINCT d.id) 
                FROM documents d
                JOIN applications a ON d.user_id = a.user_id
                JOIN application_assignments aa ON a.id = aa.application_id 
                WHERE aa.assigned_to = $crewingId AND aa.status = 'active' AND d.verification_status = 'pending'
            "),
            'completed_month' => $this->getCount("
                SELECT COUNT(DISTINCT aa.application_id) 
                FROM application_assignments aa 
                WHERE aa.assigned_to = $crewingId 
                AND aa.status = 'completed' 
                AND MONTH(aa.completed_at) = MONTH(NOW())
            "),
            'new_today' => $this->getCount("
                SELECT COUNT(DISTINCT aa.application_id) 
                FROM application_assignments aa 
                WHERE aa.assigned_to = $crewingId 
                AND DATE(aa.assigned_at) = CURDATE()
            "),
        ];
        
        // Get pipeline data for assigned applications
        $pipelineStmt = $this->db->prepare("
            SELECT s.id, s.name, s.color, COUNT(a.id) as count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE aa.assigned_to = ? OR aa.assigned_to IS NULL
            GROUP BY s.id
            ORDER BY s.sort_order
        ");
        $pipelineStmt->bind_param('i', $crewingId);
        $pipelineStmt->execute();
        $pipeline = $pipelineStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get my assigned applications (recent)
        $recentStmt = $this->db->prepare("
            SELECT a.*, u.full_name, u.email, v.title as vacancy_title,
                   s.name as status_name, s.color as status_color,
                   aa.assigned_at, aa.notes as assignment_notes
            FROM applications a
            JOIN application_assignments aa ON a.id = aa.application_id
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE aa.assigned_to = ? AND aa.status = 'active'
            ORDER BY aa.assigned_at DESC
            LIMIT 10
        ");
        $recentStmt->bind_param('i', $crewingId);
        $recentStmt->execute();
        $recentApplications = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get team workload (if admin or PIC)
        $teamWorkload = [];
        if (isAdmin() || ($crewingProfile && $crewingProfile['is_pic'])) {
            $teamWorkload = getAllCrewingStaff();
        }
        
        // Get pending interviews that need attention
        $interviewsStmt = $this->db->prepare("
            SELECT iis.*, a.id as app_id, u.full_name, v.title as vacancy_title,
                   DATEDIFF(iis.expires_at, NOW()) as days_left
            FROM interview_sessions iis
            JOIN applications a ON iis.application_id = a.id
            JOIN application_assignments aa ON a.id = aa.application_id
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            WHERE aa.assigned_to = ? AND aa.status = 'active'
            AND iis.status IN ('pending', 'in_progress')
            ORDER BY iis.expires_at ASC
            LIMIT 5
        ");
        $interviewsStmt->bind_param('i', $crewingId);
        $interviewsStmt->execute();
        $pendingInterviews = $interviewsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/dashboard/index', [
            'stats' => $stats,
            'pipeline' => $pipeline,
            'recentApplications' => $recentApplications,
            'teamWorkload' => $teamWorkload,
            'pendingInterviews' => $pendingInterviews,
            'crewingProfile' => $crewingProfile,
            'pageTitle' => 'Crewing Dashboard'
        ]);
    }
    
    private function getCount($query) {
        $result = $this->db->query($query);
        $row = $result->fetch_row();
        return $row[0] ?? 0;
    }
}
