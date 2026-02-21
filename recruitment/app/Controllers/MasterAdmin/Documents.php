<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * MasterAdmin Documents Controller
 * Own controller so MasterAdmin stays in master_admin layout
 */
class Documents extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect('/login');
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
        
        $this->view('master_admin/documents/index', [
            'applicants' => $applicants,
            'stats' => $stats,
            'pageTitle' => 'Document Verification'
        ]);
    }
    
    public function applicant($userId) {
        require_once APPPATH . 'Controllers/Admin/Documents.php';
        $adminCtrl = new \Documents();
        $adminCtrl->applicant($userId);
    }
    
    public function verify($id) {
        require_once APPPATH . 'Controllers/Admin/Documents.php';
        $adminCtrl = new \Documents();
        $adminCtrl->verify($id);
    }
    
    public function bulkVerify($userId) {
        require_once APPPATH . 'Controllers/Admin/Documents.php';
        $adminCtrl = new \Documents();
        $adminCtrl->bulkVerify($userId);
    }
}
