<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Leader Requests Controller - Handle pipeline approval requests
 */
class Requests extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || (!isLeader() && !isMasterAdmin())) {
            redirect('/login');
        }
    }
    
    public function index() {
        $leaderId = $_SESSION['user_id'];
        
        // Get ALL pending requests (not filtered by assigned_to)
        // Leader can approve/reject all requests like Master Admin
        $pendingRequests = getPendingRequests(null);
        
        // Get request history (all, not just for this leader)
        $historyResult = $this->db->query("
            SELECT pr.*, 
                   a.id as app_id, u_applicant.full_name as applicant_name,
                   u_crewing.full_name as crewing_name,
                   u_responder.full_name as responder_name,
                   fs.name as from_status_name, ts.name as to_status_name
            FROM pipeline_requests pr
            JOIN applications a ON pr.application_id = a.id
            JOIN users u_applicant ON a.user_id = u_applicant.id
            JOIN users u_crewing ON pr.requested_by = u_crewing.id
            LEFT JOIN users u_responder ON pr.responded_by = u_responder.id
            JOIN application_statuses fs ON pr.from_status_id = fs.id
            JOIN application_statuses ts ON pr.to_status_id = ts.id
            WHERE pr.status != 'pending'
            ORDER BY pr.responded_at DESC
            LIMIT 50
        ");
        $history = $historyResult ? $historyResult->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('leader/requests/index', [
            'pageTitle' => 'Pipeline Requests',
            'pendingRequests' => $pendingRequests,
            'history' => $history
        ]);
    }
    
    public function approve($id) {
        if (!$this->isPost()) {
            redirect('/leader/requests');
        }
        
        validate_csrf();
        
        $notes = $this->input('notes');
        
        if (approvePipelineRequest($id, $notes)) {
            flash('success', 'Request approved successfully.');
        } else {
            flash('error', 'Failed to approve request.');
        }
        
        redirect('/leader/requests');
    }
    
    public function reject($id) {
        if (!$this->isPost()) {
            redirect('/leader/requests');
        }
        
        validate_csrf();
        
        $notes = $this->input('notes');
        
        if (empty($notes)) {
            flash('error', 'Please provide a reason for rejection.');
            redirect('/leader/requests');
        }
        
        if (rejectPipelineRequest($id, $notes)) {
            flash('success', 'Request rejected.');
        } else {
            flash('error', 'Failed to reject request.');
        }
        
        redirect('/leader/requests');
    }
}
