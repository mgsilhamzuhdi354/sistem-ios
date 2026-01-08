<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Requests Controller
 * Handle approval/rejection of pipeline status change requests
 */
class Requests extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get pending requests
        $pendingResult = $this->db->query("
            SELECT pr.*, 
                   a.id as app_id,
                   u.full_name as applicant_name,
                   u.email as applicant_email,
                   jv.title as vacancy_title,
                   req.full_name as requested_by_name,
                   fs.name as from_status_name, fs.color as from_status_color,
                   ts.name as to_status_name, ts.color as to_status_color
            FROM pipeline_requests pr
            JOIN applications a ON pr.application_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            JOIN users req ON pr.requested_by = req.id
            JOIN application_statuses fs ON pr.from_status_id = fs.id
            JOIN application_statuses ts ON pr.to_status_id = ts.id
            WHERE pr.status = 'pending'
            ORDER BY pr.created_at DESC
        ");
        $pendingRequests = $pendingResult ? $pendingResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get recent history (approved/rejected)
        $historyResult = $this->db->query("
            SELECT pr.*, 
                   u.full_name as applicant_name,
                   req.full_name as requested_by_name,
                   resp.full_name as responded_by_name,
                   fs.name as from_status_name,
                   ts.name as to_status_name
            FROM pipeline_requests pr
            JOIN applications a ON pr.application_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN users req ON pr.requested_by = req.id
            LEFT JOIN users resp ON pr.responded_by = resp.id
            JOIN application_statuses fs ON pr.from_status_id = fs.id
            JOIN application_statuses ts ON pr.to_status_id = ts.id
            WHERE pr.status IN ('approved', 'rejected')
            ORDER BY pr.responded_at DESC
            LIMIT 20
        ");
        $historyRequests = $historyResult ? $historyResult->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('master_admin/requests/index', [
            'pageTitle' => 'Pipeline Requests',
            'pendingRequests' => $pendingRequests,
            'historyRequests' => $historyRequests
        ]);
    }
    
    public function approve($id) {
        if (!$this->isPost()) {
            redirect(url('/master-admin/requests'));
        }
        
        $id = intval($id);
        $notes = trim($this->input('notes') ?? '');
        $adminId = $_SESSION['user_id'];
        
        // Get the request
        $stmt = $this->db->prepare("SELECT * FROM pipeline_requests WHERE id = ? AND status = 'pending'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        
        if (!$request) {
            flash('error', 'Request not found or already processed');
            redirect(url('/master-admin/requests'));
        }
        
        // Update request status
        $updateStmt = $this->db->prepare("
            UPDATE pipeline_requests 
            SET status = 'approved', response_notes = ?, responded_by = ?, responded_at = NOW() 
            WHERE id = ?
        ");
        $updateStmt->bind_param('sii', $notes, $adminId, $id);
        
        if ($updateStmt->execute()) {
            // Update application status
            $appStmt = $this->db->prepare("
                UPDATE applications 
                SET status_id = ?, status_updated_at = NOW(), reviewed_by = ? 
                WHERE id = ?
            ");
            $appStmt->bind_param('iii', $request['to_status_id'], $adminId, $request['application_id']);
            $appStmt->execute();
            
            // Add to history
            $historyStmt = $this->db->prepare("
                INSERT INTO application_status_history 
                (application_id, from_status_id, to_status_id, notes, changed_by, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $historyNotes = "Approved by Master Admin" . ($notes ? ": $notes" : "");
            $historyStmt->bind_param('iiisi', 
                $request['application_id'], 
                $request['from_status_id'], 
                $request['to_status_id'], 
                $historyNotes, 
                $adminId
            );
            $historyStmt->execute();
            
            // Notify the requester
            notifyUser($request['requested_by'], 
                'Request Approved', 
                'Your pipeline status change request has been approved.',
                'success',
                url('/crewing/pipeline')
            );
            
            flash('success', 'Request approved successfully');
        } else {
            flash('error', 'Failed to approve request');
        }
        
        redirect(url('/master-admin/requests'));
    }
    
    public function reject($id) {
        if (!$this->isPost()) {
            redirect(url('/master-admin/requests'));
        }
        
        $id = intval($id);
        $notes = trim($this->input('notes') ?? '');
        $adminId = $_SESSION['user_id'];
        
        if (empty($notes)) {
            flash('error', 'Please provide a reason for rejection');
            redirect(url('/master-admin/requests'));
        }
        
        // Get the request
        $stmt = $this->db->prepare("SELECT * FROM pipeline_requests WHERE id = ? AND status = 'pending'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        
        if (!$request) {
            flash('error', 'Request not found or already processed');
            redirect(url('/master-admin/requests'));
        }
        
        // Update request status
        $updateStmt = $this->db->prepare("
            UPDATE pipeline_requests 
            SET status = 'rejected', response_notes = ?, responded_by = ?, responded_at = NOW() 
            WHERE id = ?
        ");
        $updateStmt->bind_param('sii', $notes, $adminId, $id);
        
        if ($updateStmt->execute()) {
            // Notify the requester
            notifyUser($request['requested_by'], 
                'Request Rejected', 
                "Your pipeline status change request has been rejected. Reason: $notes",
                'danger',
                url('/crewing/pipeline')
            );
            
            flash('success', 'Request rejected');
        } else {
            flash('error', 'Failed to reject request');
        }
        
        redirect(url('/master-admin/requests'));
    }
}
