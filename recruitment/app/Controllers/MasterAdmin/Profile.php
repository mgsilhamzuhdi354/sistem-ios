<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Profile Controller
 * Manage profile, avatar, and password
 */
class Profile extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }
    
    /**
     * Display profile page
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get user data
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, ap.* 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN admin_profiles ap ON u.id = ap.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $this->view('master_admin/profile/index', [
            'pageTitle' => 'My Profile',
            'user' => $user
        ]);
    }
    
    /**
     * Update profile data
     */
    public function update() {
        if (!$this->isPost()) {
            redirect(url('/master-admin/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get form data
        $fullName = trim($this->input('full_name'));
        $phone = trim($this->input('phone'));
        $employeeId = trim($this->input('employee_id'));
        $department = trim($this->input('department'));
        $position = trim($this->input('position'));
        $phoneExtension = trim($this->input('phone_extension'));
        $bio = trim($this->input('bio'));
        
        // Update users table
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('ssi', $fullName, $phone, $userId);
        $stmt->execute();
        
        // Update or insert admin_profiles
        $checkStmt = $this->db->prepare("SELECT id FROM admin_profiles WHERE user_id = ?");
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();
        
        if ($exists) {
            $stmt = $this->db->prepare("
                UPDATE admin_profiles SET 
                    employee_id = ?, department = ?, position = ?, 
                    phone_extension = ?, bio = ?, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->bind_param('sssssi', $employeeId, $department, $position, $phoneExtension, $bio, $userId);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO admin_profiles (user_id, employee_id, department, position, phone_extension, bio)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('isssss', $userId, $employeeId, $department, $position, $phoneExtension, $bio);
        }
        $stmt->execute();
        
        // Update session name (for sidebar/header display)
        $_SESSION['user_name'] = $fullName;
        $_SESSION['full_name'] = $fullName;
        
        flash('success', 'Profile updated successfully!');
        redirect(url('/master-admin/profile'));
    }
    
    /**
     * Upload avatar
     */
    public function uploadAvatar() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $userId = $_SESSION['user_id'];
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'No file uploaded or upload error']);
        }
        
        $file = $_FILES['avatar'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return $this->json(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.']);
        }
        
        // Validate file size (max 2MB)
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return $this->json(['success' => false, 'message' => 'File too large. Max 2MB allowed.']);
        }
        
        // Create upload directory
        $uploadDir = FCPATH . 'uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Delete old avatar if exists
            $oldAvatar = $this->db->query("SELECT avatar FROM users WHERE id = $userId")->fetch_assoc()['avatar'];
            if ($oldAvatar && file_exists(FCPATH . 'uploads/avatars/' . $oldAvatar)) {
                unlink(FCPATH . 'uploads/avatars/' . $oldAvatar);
            }
            
            // Update database
            $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param('si', $filename, $userId);
            $stmt->execute();
            
            // Update session
            $_SESSION['avatar'] = $filename;
            
            return $this->json([
                'success' => true, 
                'message' => 'Avatar uploaded successfully!',
                'avatar_url' => url('/uploads/avatars/' . $filename)
            ]);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to upload file']);
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        if (!$this->isPost()) {
            redirect(url('/master-admin/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        // Validate
        if ($newPassword !== $confirmPassword) {
            flash('error', 'New passwords do not match');
            redirect(url('/master-admin/profile'));
        }
        
        if (strlen($newPassword) < 6) {
            flash('error', 'Password must be at least 6 characters');
            redirect(url('/master-admin/profile'));
        }
        
        // Verify current password (using prepared statement for security)
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!password_verify($currentPassword, $user['password'])) {
            flash('error', 'Current password is incorrect');
            redirect(url('/master-admin/profile'));
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hashedPassword, $userId);
        $stmt->execute();
        
        flash('success', 'Password changed successfully!');
        redirect(url('/master-admin/profile'));
    }
}
