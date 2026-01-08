<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Documents Controller
 */
class Documents extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        // Allow both Admin and Master Admin access
        if (!isLoggedIn() || (!isAdmin() && !isMasterAdmin())) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get all applicants who have uploaded documents with their document counts
        $applicants = $this->db->query("
            SELECT u.id as user_id, u.full_name, u.email, u.phone, u.avatar,
                   COUNT(d.id) as total_docs,
                   SUM(CASE WHEN d.verification_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                   SUM(CASE WHEN d.verification_status = 'verified' THEN 1 ELSE 0 END) as verified_count,
                   SUM(CASE WHEN d.verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
            FROM users u
            JOIN documents d ON u.id = d.user_id
            GROUP BY u.id
            ORDER BY pending_count DESC, u.full_name ASC
        ")->fetch_all(MYSQLI_ASSOC);
        
        // Get overall stats
        $stats = [
            'pending' => $this->db->query("SELECT COUNT(*) as c FROM documents WHERE verification_status = 'pending'")->fetch_assoc()['c'],
            'verified' => $this->db->query("SELECT COUNT(*) as c FROM documents WHERE verification_status = 'verified'")->fetch_assoc()['c'],
            'rejected' => $this->db->query("SELECT COUNT(*) as c FROM documents WHERE verification_status = 'rejected'")->fetch_assoc()['c'],
        ];
        
        $this->view('admin/documents/index', [
            'applicants' => $applicants,
            'stats' => $stats,
            'pageTitle' => 'Document Verification'
        ]);
    }
    
    public function applicant($userId) {
        // Get applicant info
        $stmt = $this->db->prepare("SELECT id, full_name, email, phone, avatar FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $applicant = $stmt->get_result()->fetch_assoc();
        
        if (!$applicant) {
            flash('error', 'Applicant not found');
            $this->redirect(url('/admin/documents'));
        }
        
        // Get all documents for this applicant with type info
        $docStmt = $this->db->prepare("
            SELECT d.*, dt.name as type_name, dt.is_required,
                   CASE 
                       WHEN dt.name IN ('Passport', 'Seaman Book') THEN 'Identity Documents'
                       WHEN dt.name LIKE '%Certificate%' THEN 'Certificates'
                       WHEN dt.name IN ('CV/Resume', 'Photo') THEN 'Personal Documents'
                       ELSE 'Other Documents'
                   END as category
            FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            WHERE d.user_id = ?
            ORDER BY dt.sort_order, d.created_at DESC
        ");
        $docStmt->bind_param('i', $userId);
        $docStmt->execute();
        $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get document stats for this applicant
        $stats = [
            'pending' => 0,
            'verified' => 0,
            'rejected' => 0
        ];
        foreach ($documents as $doc) {
            $stats[$doc['verification_status']]++;
        }
        
        $this->view('admin/documents/applicant', [
            'applicant' => $applicant,
            'documents' => $documents,
            'stats' => $stats,
            'pageTitle' => 'Documents - ' . $applicant['full_name']
        ]);
    }
    
    public function verify($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/documents'));
        }
        
        validate_csrf();
        
        $action = $this->input('action');
        $applicantId = $this->input('applicant_id');
        $adminId = $_SESSION['user_id'];
        
        if ($action === 'verify') {
            $stmt = $this->db->prepare("
                UPDATE documents SET 
                    verification_status = 'verified',
                    verified_by = ?,
                    verified_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param('ii', $adminId, $id);
            
            if ($stmt->execute()) {
                $doc = $this->db->query("
                    SELECT d.user_id, dt.name as type_name 
                    FROM documents d
                    JOIN document_types dt ON d.document_type_id = dt.id
                    WHERE d.id = $id
                ")->fetch_assoc();
                
                $notifStmt = $this->db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                    VALUES (?, 'Document Verified', ?, 'success', ?, NOW())
                ");
                $message = "Your {$doc['type_name']} has been verified.";
                $actionUrl = url('/applicant/documents');
                $notifStmt->bind_param('iss', $doc['user_id'], $message, $actionUrl);
                $notifStmt->execute();
                
                flash('success', 'Document verified successfully');
            } else {
                flash('error', 'Failed to verify document');
            }
        } elseif ($action === 'reject') {
            $reason = $this->input('rejection_reason') ?: 'Document does not meet requirements';
            
            $stmt = $this->db->prepare("
                UPDATE documents SET 
                    verification_status = 'rejected',
                    rejection_reason = ?,
                    verified_by = ?,
                    verified_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param('sii', $reason, $adminId, $id);
            
            if ($stmt->execute()) {
                $doc = $this->db->query("
                    SELECT d.user_id, dt.name as type_name 
                    FROM documents d
                    JOIN document_types dt ON d.document_type_id = dt.id
                    WHERE d.id = $id
                ")->fetch_assoc();
                
                $notifStmt = $this->db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                    VALUES (?, 'Document Rejected', ?, 'error', ?, NOW())
                ");
                $message = "Your {$doc['type_name']} has been rejected. Reason: $reason. Please upload a new document.";
                $actionUrl = url('/applicant/documents');
                $notifStmt->bind_param('iss', $doc['user_id'], $message, $actionUrl);
                $notifStmt->execute();
                
                flash('success', 'Document rejected');
            } else {
                flash('error', 'Failed to reject document');
            }
        }
        
        // Redirect back to applicant documents if we have applicant_id
        if ($applicantId) {
            $this->redirect(url('/admin/documents/applicant/' . $applicantId));
        } else {
            $this->redirect(url('/admin/documents'));
        }
    }
    
    public function bulkVerify($userId) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/documents/applicant/' . $userId));
        }
        
        validate_csrf();
        
        $adminId = $_SESSION['user_id'];
        
        // Verify all pending documents for this user
        $stmt = $this->db->prepare("
            UPDATE documents SET 
                verification_status = 'verified',
                verified_by = ?,
                verified_at = NOW()
            WHERE user_id = ? AND verification_status = 'pending'
        ");
        $stmt->bind_param('ii', $adminId, $userId);
        
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            
            // Notify user
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Documents Verified', ?, 'success', ?, NOW())
            ");
            $message = "$affected document(s) have been verified.";
            $actionUrl = url('/applicant/documents');
            $notifStmt->bind_param('iss', $userId, $message, $actionUrl);
            $notifStmt->execute();
            
            flash('success', "$affected document(s) verified successfully");
        } else {
            flash('error', 'Failed to verify documents');
        }
        
        $this->redirect(url('/admin/documents/applicant/' . $userId));
    }
}
