<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * MasterAdmin Interviews Controller
 * Own controller so MasterAdmin stays in master_admin layout
 */
class Interviews extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect('/login');
        }
    }
    
    public function index() {
        $status = $this->input('status');
        
        $query = "
            SELECT is2.*, a.id as application_id, u.full_name, u.email,
                   v.title as vacancy_title, qb.name as question_bank_name
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE 1=1
        ";
        
        if ($status) {
            $query .= " AND is2.status = '" . $this->db->real_escape_string($status) . "'";
        }
        
        $query .= " ORDER BY is2.created_at DESC";
        
        $sessions = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        
        // Get question banks with count
        $questionBanks = $this->db->query("
            SELECT qb.*, 
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = qb.id) as question_count
            FROM interview_question_banks qb
            ORDER BY qb.name
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('master_admin/interviews/index', [
            'sessions' => $sessions,
            'questionBanks' => $questionBanks,
            'filter_status' => $status,
            'pageTitle' => 'AI Interviews'
        ]);
    }
    
    public function review($sessionId) {
        // Load Admin Interviews for shared logic
        require_once APPPATH . 'Controllers/Admin/Interviews.php';
        $adminCtrl = new \Interviews();
        $adminCtrl->review($sessionId);
    }
    
    public function score($sessionId) {
        require_once APPPATH . 'Controllers/Admin/Interviews.php';
        $adminCtrl = new \Interviews();
        $adminCtrl->score($sessionId);
    }
    
    public function resetInterview($sessionId) {
        require_once APPPATH . 'Controllers/Admin/Interviews.php';
        $adminCtrl = new \Interviews();
        $adminCtrl->resetInterview($sessionId);
    }
    
    public function assignInterview() {
        require_once APPPATH . 'Controllers/Admin/Interviews.php';
        $adminCtrl = new \Interviews();
        $adminCtrl->assignInterview();
    }
}
