<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Applicant Dashboard Controller
 */
class Dashboard extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect(url('/login'));
        }
        
        if (isAdmin()) {
            redirect(url('/admin/dashboard'));
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get user profile
        $stmt = $this->db->prepare("
            SELECT u.*, ap.*
            FROM users u
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Get applications
        $appStmt = $this->db->prepare("
            SELECT a.*, v.title as vacancy_title, v.salary_min, v.salary_max,
                   d.name as department_name, s.name as status_name, s.color as status_color
            FROM applications a
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ");
        $appStmt->bind_param('i', $userId);
        $appStmt->execute();
        $applications = $appStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $stats = [
            'active_applications' => 0,
            'pending_documents' => 0,
            'scheduled_interviews' => 0,
            'profile_completion' => $user['profile_completion'] ?? 0
        ];
        
        // Count active applications
        $activeStmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM applications 
            WHERE user_id = ? AND status_id NOT IN (6, 7)
        ");
        $activeStmt->bind_param('i', $userId);
        $activeStmt->execute();
        $stats['active_applications'] = $activeStmt->get_result()->fetch_assoc()['count'];
        
        // Count pending documents
        $docStmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM documents 
            WHERE user_id = ? AND verification_status = 'pending'
        ");
        $docStmt->bind_param('i', $userId);
        $docStmt->execute();
        $stats['pending_documents'] = $docStmt->get_result()->fetch_assoc()['count'];
        
        // Count scheduled interviews
        $intStmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            WHERE a.user_id = ? AND is2.status IN ('pending', 'in_progress')
        ");
        $intStmt->bind_param('i', $userId);
        $intStmt->execute();
        $stats['scheduled_interviews'] = $intStmt->get_result()->fetch_assoc()['count'];
        
        // Get notifications
        $notifStmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $notifStmt->bind_param('i', $userId);
        $notifStmt->execute();
        $notifications = $notifStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('applicant/dashboard', [
            'user' => $user,
            'applications' => $applications,
            'stats' => $stats,
            'notifications' => $notifications,
            'pageTitle' => 'Dashboard'
        ]);
    }
}
