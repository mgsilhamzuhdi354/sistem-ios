<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Vacancies Controller
 */
class Vacancies extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get all vacancies with department info
        $result = $this->db->query("
            SELECT jv.*, d.name as department_name,
                   (SELECT COUNT(*) FROM applications WHERE vacancy_id = jv.id) as application_count
            FROM job_vacancies jv
            LEFT JOIN departments d ON jv.department_id = d.id
            ORDER BY jv.created_at DESC
        ");
        $vacancies = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get departments for filter
        $deptResult = $this->db->query("SELECT * FROM departments ORDER BY name");
        $departments = $deptResult ? $deptResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Stats
        $stats = [
            'total' => count($vacancies),
            'active' => count(array_filter($vacancies, fn($v) => $v['status'] === 'published')),
            'closed' => count(array_filter($vacancies, fn($v) => $v['status'] !== 'published'))
        ];
        
        $this->view('master_admin/vacancies/index', [
            'pageTitle' => 'Job Vacancies',
            'vacancies' => $vacancies,
            'departments' => $departments,
            'stats' => $stats
        ]);
    }
    
    public function toggleStatus($id) {
        if (!$this->isPost()) {
            redirect(url('/master-admin/vacancies'));
        }
        
        $id = intval($id);
        
        // Get current status
        $stmt = $this->db->prepare("SELECT status FROM job_vacancies WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();
        
        if ($vacancy) {
            $newStatus = $vacancy['status'] === 'published' ? 'closed' : 'published';
            $updateStmt = $this->db->prepare("UPDATE job_vacancies SET status = ? WHERE id = ?");
            $updateStmt->bind_param('si', $newStatus, $id);
            
            if ($updateStmt->execute()) {
                flash('success', 'Vacancy status updated to ' . $newStatus);
            } else {
                flash('error', 'Failed to update status');
            }
        }
        
        redirect(url('/master-admin/vacancies'));
    }
}
