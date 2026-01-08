<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Leader Profile Controller
 */
class Profile extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isLeader()) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, lp.* 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN leader_profiles lp ON u.id = lp.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $this->view('leader/profile/index', [
            'pageTitle' => 'My Profile',
            'user' => $user
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            redirect(url('/leader/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        
        $fullName = trim($this->input('full_name'));
        $phone = trim($this->input('phone'));
        $employeeId = trim($this->input('employee_id'));
        $department = trim($this->input('department'));
        $position = trim($this->input('position'));
        $bio = trim($this->input('bio'));
        
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('ssi', $fullName, $phone, $userId);
        $stmt->execute();
        
        $checkStmt = $this->db->prepare("SELECT id FROM leader_profiles WHERE user_id = ?");
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();
        
        if ($exists) {
            $stmt = $this->db->prepare("
                UPDATE leader_profiles SET 
                    employee_id = ?, department = ?, position = ?, bio = ?, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->bind_param('ssssi', $employeeId, $department, $position, $bio, $userId);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO leader_profiles (user_id, employee_id, department, position, bio)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('issss', $userId, $employeeId, $department, $position, $bio);
        }
        $stmt->execute();
        
        // Update session name (for sidebar/header display)
        $_SESSION['user_name'] = $fullName;
        $_SESSION['full_name'] = $fullName;
        
        flash('success', 'Profile updated successfully!');
        redirect(url('/leader/profile'));
    }
    
    public function uploadAvatar() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $userId = $_SESSION['user_id'];
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'No file uploaded']);
        }
        
        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return $this->json(['success' => false, 'message' => 'Invalid file type']);
        }
        
        if ($file['size'] > 2 * 1024 * 1024) {
            return $this->json(['success' => false, 'message' => 'File too large. Max 2MB']);
        }
        
        $uploadDir = FCPATH . 'uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param('si', $filename, $userId);
            $stmt->execute();
            
            $_SESSION['avatar'] = $filename;
            
            return $this->json(['success' => true, 'message' => 'Avatar uploaded!']);
        }
        
        return $this->json(['success' => false, 'message' => 'Upload failed']);
    }
    
    public function changePassword() {
        if (!$this->isPost()) {
            redirect(url('/leader/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        if ($newPassword !== $confirmPassword) {
            flash('error', 'Passwords do not match');
            redirect(url('/leader/profile'));
        }
        
        if (strlen($newPassword) < 6) {
            flash('error', 'Password must be at least 6 characters');
            redirect(url('/leader/profile'));
        }
        
        // Verify current password (using prepared statement for security)
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!password_verify($currentPassword, $user['password'])) {
            flash('error', 'Current password is incorrect');
            redirect(url('/leader/profile'));
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hashedPassword, $userId);
        $stmt->execute();
        
        flash('success', 'Password changed successfully!');
        redirect(url('/leader/profile'));
    }
}
