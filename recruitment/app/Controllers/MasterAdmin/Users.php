<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Users Controller - Manage Admin and Leader accounts
 */
class Users extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            flash('error', 'Access denied. Master Admin only.');
            redirect('/login');
        }
    }
    
    public function index() {
        // Get master admins
        $masterAdminsResult = $this->db->query("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.role_id = " . ROLE_MASTER_ADMIN . "
            ORDER BY u.full_name
        ");
        $masterAdmins = $masterAdminsResult ? $masterAdminsResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get admins
        $adminsResult = $this->db->query("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.role_id = " . ROLE_ADMIN . "
            ORDER BY u.full_name
        ");
        $admins = $adminsResult ? $adminsResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get leaders
        $leadersResult = $this->db->query("
            SELECT u.*, r.name as role_name, lp.department, lp.employee_id
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN leader_profiles lp ON u.id = lp.user_id
            WHERE u.role_id = " . ROLE_LEADER . "
            ORDER BY u.full_name
        ");
        $leaders = $leadersResult ? $leadersResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get crewing staff
        $crewingResult = $this->db->query("
            SELECT u.*, r.name as role_name, cp.employee_id, cp.specialization,
                   COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.role_id = " . ROLE_CREWING . "
            GROUP BY u.id
            ORDER BY u.full_name
        ");
        $crewingStaff = $crewingResult ? $crewingResult->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('master_admin/users/index', [
            'pageTitle' => 'User Management',
            'masterAdmins' => $masterAdmins,
            'admins' => $admins,
            'leaders' => $leaders,
            'crewingStaff' => $crewingStaff
        ]);
    }
    
    public function create() {
        $this->view('master_admin/users/create', [
            'pageTitle' => 'Create User'
        ]);
    }
    
    public function store() {
        if (!$this->isPost()) {
            redirect('/master-admin/users');
        }
        
        validate_csrf();
        
        $roleId = intval($this->input('role_id'));
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $password = $this->input('password');
        $department = trim($this->input('department'));
        $employeeId = trim($this->input('employee_id'));
        
        // Validation
        if (!in_array($roleId, [1, 4])) {
            flash('error', 'Invalid role. Only Admin and Leader can be created here.');
            redirect('/master-admin/users/create');
        }
        
        if (empty($fullName) || empty($email) || empty($password)) {
            flash('error', 'All fields are required.');
            redirect('/master-admin/users/create');
        }
        
        // Check duplicate email
        $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            flash('error', 'Email already exists.');
            redirect('/master-admin/users/create');
        }
        
        // Create user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, full_name, email, password, is_active, created_at) 
            VALUES (?, ?, ?, ?, 1, NOW())
        ");
        $stmt->bind_param('isss', $roleId, $fullName, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            $userId = $this->db->insert_id;
            
            // Create leader profile if role is Leader
            if ($roleId == 4) {
                $profileStmt = $this->db->prepare("
                    INSERT INTO leader_profiles (user_id, department, employee_id) 
                    VALUES (?, ?, ?)
                ");
                $profileStmt->bind_param('iss', $userId, $department, $employeeId);
                $profileStmt->execute();
            }
            
            flash('success', 'User created successfully.');
        } else {
            flash('error', 'Failed to create user.');
        }
        
        redirect('/master-admin/users');
    }
    
    public function edit($id) {
        // Get user with all possible profile data
        $stmt = $this->db->prepare("
            SELECT u.*, 
                   lp.department, lp.employee_id as leader_employee_id,
                   cp.employee_id as crewing_employee_id, cp.specialization
            FROM users u
            LEFT JOIN leader_profiles lp ON u.id = lp.user_id
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            WHERE u.id = ? AND u.role_id IN (?, ?, ?, ?)
        ");
        $masterAdmin = ROLE_MASTER_ADMIN;
        $admin = ROLE_ADMIN;
        $leader = ROLE_LEADER;
        $crewing = ROLE_CREWING;
        $stmt->bind_param('iiiii', $id, $masterAdmin, $admin, $leader, $crewing);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            flash('error', 'User not found.');
            redirect(url('/master-admin/users'));
        }
        
        // Normalize employee_id based on role
        if ($user['role_id'] == ROLE_LEADER) {
            $user['employee_id'] = $user['leader_employee_id'];
        } elseif ($user['role_id'] == ROLE_CREWING) {
            $user['employee_id'] = $user['crewing_employee_id'];
        }
        
        $this->view('master_admin/users/edit', [
            'pageTitle' => 'Edit User',
            'user' => $user
        ]);
    }
    
    public function update($id) {
        if (!$this->isPost()) {
            redirect(url('/master-admin/users'));
        }
        
        validate_csrf();
        
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $password = $this->input('password');
        $isActive = $this->input('is_active') ? 1 : 0;
        $department = trim($this->input('department'));
        $employeeId = trim($this->input('employee_id'));
        $specialization = trim($this->input('specialization'));
        
        // Get current user - allow all staff roles
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND role_id IN (?, ?, ?, ?)");
        $masterAdmin = ROLE_MASTER_ADMIN;
        $admin = ROLE_ADMIN;
        $leader = ROLE_LEADER;
        $crewing = ROLE_CREWING;
        $stmt->bind_param('iiiii', $id, $masterAdmin, $admin, $leader, $crewing);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            flash('error', 'User not found.');
            redirect(url('/master-admin/users'));
        }
        
        // Update user
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, password = ?, is_active = ? WHERE id = ?");
            $updateStmt->bind_param('sssii', $fullName, $email, $hashedPassword, $isActive, $id);
        } else {
            $updateStmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, is_active = ? WHERE id = ?");
            $updateStmt->bind_param('ssii', $fullName, $email, $isActive, $id);
        }
        
        if ($updateStmt->execute()) {
            // Update leader profile if applicable
            if ($user['role_id'] == ROLE_LEADER) {
                $profileStmt = $this->db->prepare("
                    INSERT INTO leader_profiles (user_id, department, employee_id) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE department = ?, employee_id = ?
                ");
                $profileStmt->bind_param('issss', $id, $department, $employeeId, $department, $employeeId);
                $profileStmt->execute();
            }
            
            // Update crewing profile if applicable
            if ($user['role_id'] == ROLE_CREWING) {
                $profileStmt = $this->db->prepare("
                    INSERT INTO crewing_profiles (user_id, employee_id, specialization) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE employee_id = ?, specialization = ?
                ");
                $profileStmt->bind_param('issss', $id, $employeeId, $specialization, $employeeId, $specialization);
                $profileStmt->execute();
            }
            
            flash('success', 'User updated successfully.');
        } else {
            flash('error', 'Failed to update user.');
        }
        
        redirect(url('/master-admin/users'));
    }
    
    public function delete($id) {
        if (!$this->isPost()) {
            redirect('/master-admin/users');
        }
        
        validate_csrf();
        
        // Cannot delete yourself
        if ($id == $_SESSION['user_id']) {
            flash('error', 'Cannot delete your own account.');
            redirect('/master-admin/users');
        }
        
        // Check if user exists and is deletable (Admin or Leader only)
        $checkStmt = $this->db->prepare("SELECT id, role_id FROM users WHERE id = ? AND role_id IN (1, 4, 5)");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $user = $checkStmt->get_result()->fetch_assoc();
        
        if (!$user) {
            flash('error', 'User not found or cannot be deleted.');
            redirect('/master-admin/users');
        }
        
        try {
            // Start transaction
            $this->db->begin_transaction();
            
            // Delete related application_assignments first
            $deleteAssignments = $this->db->prepare("DELETE FROM application_assignments WHERE assigned_to = ?");
            $deleteAssignments->bind_param('i', $id);
            $deleteAssignments->execute();
            
            // Delete leader_profiles if exists
            $deleteLeaderProfile = $this->db->prepare("DELETE FROM leader_profiles WHERE user_id = ?");
            $deleteLeaderProfile->bind_param('i', $id);
            $deleteLeaderProfile->execute();
            
            // Delete crewing_profiles if exists
            $deleteCrewingProfile = $this->db->prepare("DELETE FROM crewing_profiles WHERE user_id = ?");
            $deleteCrewingProfile->bind_param('i', $id);
            $deleteCrewingProfile->execute();
            
            // Now delete the user
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            
            // Commit transaction
            $this->db->commit();
            
            flash('success', 'User deleted successfully.');
        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            flash('error', 'Failed to delete user. The user may have related data that cannot be deleted.');
        }
        
        redirect('/master-admin/users');
    }
    
    /**
     * Show online/offline status of all users
     */
    public function online() {
        // Get all users with their online status grouped by role
        $usersResult = $this->db->query("
            SELECT u.id, u.full_name, u.email, u.avatar, u.role_id, u.is_online, 
                   u.last_activity, u.last_login, u.is_active,
                   r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.is_active = 1
            ORDER BY u.is_online DESC, u.last_activity DESC
        ");
        $allUsers = $usersResult ? $usersResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Group by role
        $usersByRole = [];
        $onlineCount = 0;
        $offlineCount = 0;
        
        foreach ($allUsers as $user) {
            $roleName = $user['role_name'];
            if (!isset($usersByRole[$roleName])) {
                $usersByRole[$roleName] = [];
            }
            $usersByRole[$roleName][] = $user;
            
            if ($user['is_online']) {
                $onlineCount++;
            } else {
                $offlineCount++;
            }
        }
        
        // Get stats
        $stats = [
            'total' => count($allUsers),
            'online' => $onlineCount,
            'offline' => $offlineCount
        ];
        
        $this->view('master_admin/users/online', [
            'pageTitle' => 'User Activity',
            'usersByRole' => $usersByRole,
            'allUsers' => $allUsers,
            'stats' => $stats
        ]);
    }
}

