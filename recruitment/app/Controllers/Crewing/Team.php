<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Team Controller
 */
class Team extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get all crewing staff with their workload
        $crewingStaff = $this->db->query("
            SELECT 
                u.id, u.full_name, u.email, u.avatar, u.is_active, u.last_login,
                cp.employee_id, cp.is_pic, cp.max_applications, cp.specialization,
                cp.department_ids,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.application_id END) as active_assignments,
                COUNT(DISTINCT CASE WHEN aa.status = 'completed' AND MONTH(aa.completed_at) = MONTH(NOW()) THEN aa.application_id END) as completed_month,
                COUNT(DISTINCT CASE WHEN a.status_id IN (1, 2) AND aa.status = 'active' THEN a.id END) as pending_review,
                COUNT(DISTINCT CASE WHEN a.status_id = 3 AND aa.status = 'active' THEN a.id END) as in_interview,
                COUNT(DISTINCT CASE WHEN a.status_id = 6 AND MONTH(a.status_updated_at) = MONTH(NOW()) THEN a.id END) as hired_month
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN applications a ON aa.application_id = a.id
            WHERE u.role_id = (SELECT id FROM roles WHERE name = 'crewing')
            GROUP BY u.id
            ORDER BY u.full_name
        ")->fetch_all(MYSQLI_ASSOC);
        
        // Get departments for reference
        $departments = $this->db->query("SELECT id, name FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $departmentsMap = [];
        foreach ($departments as $dept) {
            $departmentsMap[$dept['id']] = $dept['name'];
        }
        
        // Get unassigned applications count
        $unassignedCount = $this->db->query("
            SELECT COUNT(*) as count 
            FROM applications a
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE aa.id IS NULL AND a.status_id NOT IN (6, 7)
        ")->fetch_assoc()['count'];
        
        // Get unassigned applications list
        $unassignedApps = $this->db->query("
            SELECT a.*, u.full_name, v.title as vacancy_title, s.name as status_name, s.color as status_color
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE aa.id IS NULL AND a.status_id NOT IN (6, 7)
            ORDER BY a.submitted_at DESC
            LIMIT 20
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/team/index', [
            'crewingStaff' => $crewingStaff,
            'departments' => $departments,
            'departmentsMap' => $departmentsMap,
            'unassignedCount' => $unassignedCount,
            'unassignedApps' => $unassignedApps,
            'pageTitle' => 'Team Workload'
        ]);
    }
    
    public function bulkAssign() {
        if (!$this->isPost()) {
            $this->redirect(url('/crewing/team'));
        }
        
        validate_csrf();
        
        $applicationIds = $this->input('application_ids'); // Array
        $assignTo = intval($this->input('assign_to'));
        $notes = $this->input('notes') ?: 'Bulk assignment';
        $crewingId = $_SESSION['user_id'];
        
        if (empty($applicationIds) || !$assignTo) {
            flash('error', 'Please select applications and a crewing staff');
            $this->redirect(url('/crewing/team'));
        }
        
        $successCount = 0;
        
        foreach ($applicationIds as $appId) {
            $appId = intval($appId);
            
            // Mark current assignment as transferred (if exists)
            $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE application_id = $appId AND status = 'active'");
            
            // Create new assignment
            $stmt = $this->db->prepare("
                INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
                VALUES (?, ?, ?, ?, 'active')
            ");
            $stmt->bind_param('iiis', $appId, $assignTo, $crewingId, $notes);
            
            if ($stmt->execute()) {
                // Update application
                $this->db->query("UPDATE applications SET current_crewing_id = $assignTo WHERE id = $appId");
                $successCount++;
            }
        }
        
        if ($successCount > 0) {
            // Notify the assigned crewing
            if ($assignTo != $crewingId) {
                notifyUser($assignTo, 'Bulk Applications Assigned',
                    $successCount . ' applications have been assigned to you by ' . $_SESSION['user_name'],
                    'info', url('/crewing/applications'));
            }
            
            logAutomation('assignment', 'applications', 0, 'bulk_assign', [
                'count' => $successCount,
                'assigned_to' => $assignTo,
                'assigned_by' => $crewingId
            ]);
            
            flash('success', $successCount . ' applications assigned successfully');
        } else {
            flash('error', 'Failed to assign applications');
        }
        
        $this->redirect(url('/crewing/team'));
    }
    
    public function autoAssignAll() {
        if (!$this->isPost()) {
            $this->redirect(url('/crewing/team'));
        }
        
        validate_csrf();
        
        // Get all unassigned applications
        $unassigned = $this->db->query("
            SELECT a.id
            FROM applications a
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE aa.id IS NULL AND a.status_id NOT IN (6, 7)
        ")->fetch_all(MYSQLI_ASSOC);
        
        $successCount = 0;
        
        foreach ($unassigned as $app) {
            if (autoAssignApplication($app['id'], $_SESSION['user_id'])) {
                $successCount++;
            }
        }
        
        if ($successCount > 0) {
            flash('success', $successCount . ' applications auto-assigned successfully');
        } else {
            flash('warning', 'No applications were assigned. Please check if there are available crewing staff.');
        }
        
        $this->redirect(url('/crewing/team'));
    }
}
