<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * MasterAdmin Medical Controller
 * Own controller so MasterAdmin stays in master_admin layout
 */
class Medical extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect('/login');
        }
    }
    
    public function index() {
        $status = $this->input('status');
        
        $query = "
            SELECT mc.*, u.full_name, u.email, v.title as vacancy_title,
                   p.full_name as processed_by_name
            FROM medical_checkups mc
            JOIN applications a ON mc.application_id = a.id
            JOIN users u ON mc.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN users p ON mc.processed_by = p.id
            WHERE 1=1
        ";
        
        if ($status) {
            $query .= " AND mc.status = '" . $this->db->real_escape_string($status) . "'";
        }
        
        $query .= " ORDER BY mc.scheduled_date DESC";
        
        $checkups = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $stats = [
            'scheduled' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE status = 'scheduled'")->fetch_assoc()['c'],
            'in_progress' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE status = 'in_progress'")->fetch_assoc()['c'],
            'fit' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE result = 'fit'")->fetch_assoc()['c'],
            'unfit' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE result = 'unfit'")->fetch_assoc()['c'],
        ];
        
        $this->view('master_admin/medical/index', [
            'checkups' => $checkups,
            'stats' => $stats,
            'filter_status' => $status,
            'pageTitle' => 'Medical Check-ups'
        ]);
    }
    
    public function schedule($applicationId) {
        require_once APPPATH . 'Controllers/Admin/Medical.php';
        $adminCtrl = new \Medical();
        $adminCtrl->schedule($applicationId);
    }
    
    public function result($checkupId) {
        require_once APPPATH . 'Controllers/Admin/Medical.php';
        $adminCtrl = new \Medical();
        $adminCtrl->result($checkupId);
    }
}
