<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Crewing Management Controller
 */
class CrewingManagement extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isAdmin()) {
            flash('error', 'Access denied. Admin only.');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get all crewing staff
        $crewingStaff = $this->db->query("
            SELECT u.*, cp.employee_id, cp.is_pic, cp.max_applications, 
                   cp.specialization, cp.department_ids,
                   COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments,
                   COUNT(DISTINCT CASE WHEN aa.status = 'completed' THEN aa.id END) as total_completed
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.role_id = (SELECT id FROM roles WHERE name = 'crewing')
            GROUP BY u.id
            ORDER BY u.full_name
        ")->fetch_all(MYSQLI_ASSOC);
        
        // Get departments for reference
        $departments = $this->db->query("SELECT id, name FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/crewing/index', [
            'crewingStaff' => $crewingStaff,
            'departments' => $departments,
            'pageTitle' => 'Crewing Management'
        ]);
    }
    
    public function create() {
        $departments = $this->db->query("SELECT id, name FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $existingCrewing = getAllCrewingStaff();
        
        $this->view('admin/crewing/create', [
            'departments' => $departments,
            'existingCrewing' => $existingCrewing,
            'pageTitle' => 'Add Crewing Staff'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/crewing'));
        }
        
        validate_csrf();
        
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $phone = trim($this->input('phone'));
        $password = $this->input('password');
        $employeeId = trim($this->input('employee_id'));
        $isPic = $this->input('is_pic') ? 1 : 0;
        $maxApplications = intval($this->input('max_applications')) ?: 50;
        $specialization = trim($this->input('specialization'));
        $departmentIds = $this->input('department_ids'); // Array
        
        $errors = [];
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } else {
            // Check if email exists
            $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            if ($checkStmt->get_result()->fetch_assoc()) {
                $errors[] = 'Email already exists';
            }
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect(url('/admin/crewing/create'));
        }
        
        // Get crewing role ID
        $roleResult = $this->db->query("SELECT id FROM roles WHERE name = 'crewing'");
        $crewingRole = $roleResult->fetch_assoc();
        
        if (!$crewingRole) {
            flash('error', 'Crewing role not found. Please run the migration first.');
            $this->redirect(url('/admin/crewing/create'));
        }
        
        $roleId = $crewingRole['id'];
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, email, password, full_name, phone, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, 1, NOW())
        ");
        $stmt->bind_param('issss', $roleId, $email, $hashedPassword, $fullName, $phone);
        
        if ($stmt->execute()) {
            $userId = $this->db->insert_id;
            
            // Create crewing profile
            $deptIdsJson = $departmentIds ? json_encode($departmentIds) : null;
            
            $profileStmt = $this->db->prepare("
                INSERT INTO crewing_profiles (user_id, employee_id, is_pic, max_applications, specialization, department_ids)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $profileStmt->bind_param('isiiss', $userId, $employeeId, $isPic, $maxApplications, $specialization, $deptIdsJson);
            $profileStmt->execute();
            
            // Auto-create default SMTP config for new crewing user
            $emailDomain = substr($email, strpos($email, '@') + 1);
            $smtpHost = 'mail.' . $emailDomain;
            $smtpPort = 465;
            $smtpEncryption = 'ssl';
            $fromName = 'PT Indo Ocean Crew Services';
            
            $smtpStmt = $this->db->prepare("
                INSERT INTO user_smtp_configs 
                (user_id, smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, smtp_from_email, smtp_from_name, is_active)
                VALUES (?, ?, ?, ?, '', ?, ?, ?, 1)
            ");
            $smtpStmt->bind_param('isissss', $userId, $smtpHost, $smtpPort, $email, $smtpEncryption, $email, $fromName);
            $smtpStmt->execute();
            
            flash('success', 'Crewing staff added successfully. SMTP email auto-configured - password needs to be set in Settings.');
            $this->redirect(url('/admin/crewing'));
        } else {
            flash('error', 'Failed to add crewing staff');
            $this->redirect(url('/admin/crewing/create'));
        }
    }
    
    public function edit($id) {
        $stmt = $this->db->prepare("
            SELECT u.*, cp.employee_id, cp.is_pic, cp.max_applications, 
                   cp.specialization, cp.department_ids, cp.can_assign_to
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $crewing = $stmt->get_result()->fetch_assoc();
        
        if (!$crewing) {
            flash('error', 'Crewing staff not found');
            $this->redirect(url('/admin/crewing'));
        }
        
        $departments = $this->db->query("SELECT id, name FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $existingCrewing = getAllCrewingStaff();
        
        $this->view('admin/crewing/edit', [
            'crewing' => $crewing,
            'departments' => $departments,
            'existingCrewing' => $existingCrewing,
            'pageTitle' => 'Edit Crewing Staff'
        ]);
    }
    
    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/crewing'));
        }
        
        validate_csrf();
        
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $phone = trim($this->input('phone'));
        $password = $this->input('password');
        $employeeId = trim($this->input('employee_id'));
        $isPic = $this->input('is_pic') ? 1 : 0;
        $maxApplications = intval($this->input('max_applications')) ?: 50;
        $specialization = trim($this->input('specialization'));
        $departmentIds = $this->input('department_ids');
        $canAssignTo = $this->input('can_assign_to');
        $isActive = $this->input('is_active') ? 1 : 0;
        
        $errors = [];
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } else {
            // Check if email exists for other users
            $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->bind_param('si', $email, $id);
            $checkStmt->execute();
            if ($checkStmt->get_result()->fetch_assoc()) {
                $errors[] = 'Email already exists';
            }
        }
        
        if (!empty($password) && strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect(url('/admin/crewing/edit/' . $id));
        }
        
        // Update user
        $userQuery = "UPDATE users SET full_name = ?, email = ?, phone = ?, is_active = ?, updated_at = NOW()";
        $params = [$fullName, $email, $phone, $isActive];
        $types = 'sssi';
        
        if (!empty($password)) {
            $userQuery .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
            $types .= 's';
        }
        
        $userQuery .= " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';
        
        $stmt = $this->db->prepare($userQuery);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            // Update crewing profile
            $deptIdsJson = $departmentIds ? json_encode($departmentIds) : null;
            $canAssignToJson = $canAssignTo ? json_encode($canAssignTo) : null;
            
            // Check if profile exists
            $checkProfile = $this->db->prepare("SELECT id FROM crewing_profiles WHERE user_id = ?");
            $checkProfile->bind_param('i', $id);
            $checkProfile->execute();
            
            if ($checkProfile->get_result()->fetch_assoc()) {
                // Update
                $profileStmt = $this->db->prepare("
                    UPDATE crewing_profiles 
                    SET employee_id = ?, is_pic = ?, max_applications = ?, specialization = ?, 
                        department_ids = ?, can_assign_to = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $profileStmt->bind_param('siisssi', $employeeId, $isPic, $maxApplications, $specialization, $deptIdsJson, $canAssignToJson, $id);
            } else {
                // Insert
                $profileStmt = $this->db->prepare("
                    INSERT INTO crewing_profiles (user_id, employee_id, is_pic, max_applications, specialization, department_ids, can_assign_to)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $profileStmt->bind_param('isiisss', $id, $employeeId, $isPic, $maxApplications, $specialization, $deptIdsJson, $canAssignToJson);
            }
            $profileStmt->execute();
            
            flash('success', 'Crewing staff updated successfully');
        } else {
            flash('error', 'Failed to update crewing staff');
        }
        
        $this->redirect(url('/admin/crewing'));
    }
    
    public function delete($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/crewing'));
        }
        
        validate_csrf();
        
        // Check if has active assignments
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM application_assignments WHERE assigned_to = ? AND status = 'active'");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        
        if ($result['count'] > 0) {
            flash('error', 'Cannot delete crewing staff with active assignments. Please reassign first.');
            $this->redirect(url('/admin/crewing'));
        }
        
        // Delete crewing profile first
        $delProfile = $this->db->prepare("DELETE FROM crewing_profiles WHERE user_id = ?");
        $delProfile->bind_param('i', $id);
        $delProfile->execute();
        
        // Delete SMTP config
        $delSmtp = $this->db->prepare("DELETE FROM user_smtp_configs WHERE user_id = ?");
        $delSmtp->bind_param('i', $id);
        $delSmtp->execute();
        
        // Delete user
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            flash('success', 'Crewing staff deleted successfully');
        } else {
            flash('error', 'Failed to delete crewing staff');
        }
        
        $this->redirect(url('/admin/crewing'));
    }
    
    public function workload() {
        // Get workload summary
        $workloadData = $this->db->query("
            SELECT 
                u.id, u.full_name, cp.employee_id, cp.max_applications,
                COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.application_id END) as active,
                COUNT(DISTINCT CASE WHEN aa.status = 'completed' AND MONTH(aa.completed_at) = MONTH(NOW()) THEN aa.application_id END) as completed_month,
                COUNT(DISTINCT CASE WHEN a.status_id = 6 AND MONTH(a.status_updated_at) = MONTH(NOW()) THEN a.id END) as hired_month,
                AVG(CASE WHEN aa.status = 'completed' THEN DATEDIFF(aa.completed_at, aa.assigned_at) END) as avg_days_to_complete
            FROM users u
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            LEFT JOIN applications a ON aa.application_id = a.id
            WHERE u.role_id = (SELECT id FROM roles WHERE name = 'crewing')
            AND u.is_active = 1
            GROUP BY u.id
            ORDER BY active DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/crewing/workload', [
            'workloadData' => $workloadData,
            'pageTitle' => 'Crewing Workload Report'
        ]);
    }
}
