<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Profile Controller
 */
class Profile extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || (!isCrewing() && !isCrewingPIC())) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, cp.* 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $this->view('crewing/profile/index', [
            'pageTitle' => 'My Profile',
            'user' => $user
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            redirect(url('/crewing/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        
        $fullName = trim($this->input('full_name'));
        $phone = trim($this->input('phone'));
        $employeeId = trim($this->input('employee_id'));
        $specialization = trim($this->input('specialization'));
        $maxApplications = intval($this->input('max_applications') ?: 50);
        
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('ssi', $fullName, $phone, $userId);
        $stmt->execute();
        
        $checkStmt = $this->db->prepare("SELECT id FROM crewing_profiles WHERE user_id = ?");
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();
        
        if ($exists) {
            $stmt = $this->db->prepare("
                UPDATE crewing_profiles SET 
                    employee_id = ?, specialization = ?, max_applications = ?, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->bind_param('ssii', $employeeId, $specialization, $maxApplications, $userId);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO crewing_profiles (user_id, employee_id, specialization, max_applications)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('issi', $userId, $employeeId, $specialization, $maxApplications);
        }
        $stmt->execute();
        
        // Update session name (for sidebar/header display)
        $_SESSION['user_name'] = $fullName;
        $_SESSION['full_name'] = $fullName;
        
        flash('success', 'Profile updated successfully!');
        redirect(url('/crewing/profile'));
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
        
        if ($file['size'] > 10 * 1024 * 1024) {
            return $this->json(['success' => false, 'message' => 'File too large. Max 10MB']);
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
    
    /**
     * Upload recruiter photo (NEW - for recruiter selection feature)
     * Optimized with image resizing for performance
     */
    public function uploadPhoto() {
        if (!$this->isPost()) {
            flash('error', 'Invalid request');
            redirect(url('/crewing/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'No file uploaded');
            redirect(url('/crewing/profile'));
        }
        
        $file = $_FILES['photo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            flash('error', 'Invalid file type. Please upload JPG or PNG only.');
            redirect(url('/crewing/profile'));
        }
        
        // Validate file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            flash('error', 'File too large. Maximum size is 10MB.');
            redirect(url('/crewing/profile'));
        }
        
        $uploadDir = FCPATH . 'uploads/recruiters/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'recruiter_' . $userId . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;
        
        // Simple upload (resize can be added later if needed)
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update crewing_profiles table
            $checkStmt = $this->db->prepare("SELECT id FROM crewing_profiles WHERE user_id = ?");
            $checkStmt->bind_param('i', $userId);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->fetch_assoc();
            
            if ($exists) {
                $stmt = $this->db->prepare("UPDATE crewing_profiles SET photo = ?, updated_at = NOW() WHERE user_id = ?");
                $stmt->bind_param('si', $filename, $userId);
            } else {
                $stmt = $this->db->prepare("INSERT INTO crewing_profiles (user_id, photo, created_at) VALUES (?, ?, NOW())");
                $stmt->bind_param('is', $userId, $filename);
            }
            $stmt->execute();
            
            flash('success', '✓ Recruiter photo uploaded successfully!');
            redirect(url('/crewing/profile'));
        }
        
        flash('error', 'Failed to upload photo. Please try again.');
        redirect(url('/crewing/profile'));
    }
    
    public function changePassword() {
        if (!$this->isPost()) {
            redirect(url('/crewing/profile'));
        }
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        if ($newPassword !== $confirmPassword) {
            flash('error', 'Passwords do not match');
            redirect(url('/crewing/profile'));
        }
        
        if (strlen($newPassword) < 6) {
            flash('error', 'Password must be at least 6 characters');
            redirect(url('/crewing/profile'));
        }
        
        // Verify current password (using prepared statement for security)
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!password_verify($currentPassword, $user['password'])) {
            flash('error', 'Current password is incorrect');
            redirect(url('/crewing/profile'));
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hashedPassword, $userId);
        $stmt->execute();
        
        flash('success', 'Password changed successfully!');
        redirect(url('/crewing/profile'));
    }
}
