<?php
/**
 * PT Indo Ocean - ERP System
 * User Management Controller
 */

namespace App\Controllers;

require_once APPPATH . 'Models/UserModel.php';

use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Models\LoginHistoryModel;

class UserManagement extends BaseController
{
    private $userModel;
    private $activityModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel($this->db);
        $this->activityModel = new ActivityLogModel($this->db);
    }
    
    /**
     * List all users
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('users', 'view');
        
        $statusInput = $this->input('status');
        $filters = [
            'role' => $this->input('role'),
            'search' => $this->input('search'),
            'is_active' => ($statusInput !== null && $statusInput !== '') ? (int)$statusInput : null
        ];
        
        $page = (int)($this->input('page') ?? 1);
        
        $data = [
            'title' => 'User Management',
            'currentPage' => 'users',
            'users' => $this->userModel->getList($filters, $page, 20),
            'total' => $this->userModel->countUsers($filters),
            'filters' => $filters,
            'page' => $page,
            'flash' => $this->getFlash()
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'users/index_modern' : 'users/index';

        return $this->view($view, $data);
    }
    
    /**
     * Create user form
     */
    public function create()
    {
        $this->requireAuth();
        $this->requirePermission('users', 'create');
        
        $data = [
            'title' => 'Add New User',
            'currentPage' => 'users',
            'user' => null,
            'roles' => $this->getRoles(),
            'csrf_token' => $this->getCsrfToken(),
            'flash' => $this->getFlash()
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'users/form_modern' : 'users/form';

        return $this->view($view, $data);
    }
    
    /**
     * Store new user
     */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('users', 'create');
        
        if (!$this->isPost()) {
            $this->redirect('users');
            return;
        }
        
        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('users/create');
            return;
        }
        
        $username = trim($this->input('username'));
        $email = trim($this->input('email'));
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        $fullName = trim($this->input('full_name'));
        $role = $this->input('role');
        $phone = trim($this->input('phone'));
        
        // Validation
        $errors = [];
        
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }
        
        if ($this->userModel->usernameExists($username)) {
            $errors[] = 'Username sudah digunakan';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid';
        }
        
        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Email sudah terdaftar';
        }
        
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Konfirmasi password tidak cocok';
        }
        
        if (empty($fullName)) {
            $errors[] = 'Nama lengkap wajib diisi';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('users/create');
            return;
        }
        
        // Create user
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $this->userModel->hashPassword($password),
            'full_name' => $fullName,
            'role' => $role,
            'phone' => $phone,
            'is_active' => 1
        ]);
        
        // Log activity
        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'create',
            'user',
            $userId,
            "Created new user: {$username}"
        );
        
        $this->setFlash('success', 'User berhasil ditambahkan');
        $this->redirect('users');
    }
    
    /**
     * Edit user form
     */
    public function edit($id)
    {
        $this->requireAuth();
        $this->requirePermission('users', 'edit');
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User tidak ditemukan');
            $this->redirect('users');
            return;
        }
        
        $data = [
            'title' => 'Edit User',
            'currentPage' => 'users',
            'user' => $user,
            'roles' => $this->getRoles(),
            'csrf_token' => $this->getCsrfToken(),
            'flash' => $this->getFlash()
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'users/form_modern' : 'users/form';

        return $this->view($view, $data);
    }
    
    /**
     * Update user
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->requirePermission('users', 'edit');
        
        if (!$this->isPost()) {
            $this->redirect('users');
            return;
        }
        
        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('users/edit/' . $id);
            return;
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User tidak ditemukan');
            $this->redirect('users');
            return;
        }
        
        $username = trim($this->input('username'));
        $email = trim($this->input('email'));
        $password = $this->input('password');
        $fullName = trim($this->input('full_name'));
        $role = $this->input('role');
        $phone = trim($this->input('phone'));
        $isActive = (int)$this->input('is_active');
        
        // Validation
        $errors = [];
        
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }
        
        if ($this->userModel->usernameExists($username, $id)) {
            $errors[] = 'Username sudah digunakan';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid';
        }
        
        if ($this->userModel->emailExists($email, $id)) {
            $errors[] = 'Email sudah terdaftar';
        }
        
        if (!empty($password) && strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter';
        }
        
        if (empty($fullName)) {
            $errors[] = 'Nama lengkap wajib diisi';
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('users/edit/' . $id);
            return;
        }
        
        // Update data
        $updateData = [
            'username' => $username,
            'email' => $email,
            'full_name' => $fullName,
            'role' => $role,
            'phone' => $phone,
            'is_active' => $isActive
        ];
        
        // Only update password if provided
        if (!empty($password)) {
            $updateData['password'] = $this->userModel->hashPassword($password);
        }
        
        $this->userModel->update($id, $updateData);
        
        // Log activity
        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'update',
            'user',
            $id,
            "Updated user: {$username}",
            ['old' => $user],
            ['new' => $updateData]
        );
        
        $this->setFlash('success', 'User berhasil diupdate');
        $this->redirect('users');
    }
    
    /**
     * Delete user
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->requirePermission('users', 'delete');
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User tidak ditemukan');
            $this->redirect('users');
            return;
        }
        
        // Cannot delete self
        if ($user['id'] == $this->getCurrentUser()['id']) {
            $this->setFlash('error', 'Tidak dapat menghapus akun sendiri');
            $this->redirect('users');
            return;
        }
        
        // Soft delete - just deactivate
        $this->userModel->update($id, ['is_active' => 0]);
        
        // Log activity
        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'delete',
            'user',
            $id,
            "Deactivated user: {$user['username']}"
        );
        
        $this->setFlash('success', 'User berhasil dinonaktifkan');
        $this->redirect('users');
    }
    
    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $this->requireAuth();
        $this->requirePermission('users', 'edit');
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->json(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        $newStatus = !$user['is_active'];
        $this->userModel->update($id, ['is_active' => $newStatus]);
        
        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'toggle_status',
            'user',
            $id,
            ($newStatus ? 'Activated' : 'Deactivated') . " user: {$user['username']}"
        );
        
        $this->json(['success' => true, 'is_active' => $newStatus]);
    }
    
    /**
     * View user details
     */
    public function show($id)
    {
        $this->requireAuth();
        $this->requirePermission('users', 'view');
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User tidak ditemukan');
            $this->redirect('users');
            return;
        }
        
        $loginHistoryModel = new LoginHistoryModel($this->db);
        
        $data = [
            'title' => 'User Detail: ' . $user['full_name'],
            'currentPage' => 'users',
            'user' => $user,
            'loginHistory' => $loginHistoryModel->getByUser($id, 20),
            'activities' => $this->activityModel->getByEntity('user', $id, 50),
            'flash' => $this->getFlash()
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'users/view_modern' : 'users/view';

        return $this->view($view, $data);
    }
    
    /**
     * Get available roles
     */
    private function getRoles()
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'hr' => 'HR',
            'finance' => 'Finance',
            'manager' => 'Manager',
            'viewer' => 'Viewer'
        ];
    }
    
    /**
     * Get CSRF token
     */
    private function getCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF
     */
    private function validateCsrf()
    {
        $token = $this->input('csrf_token');
        return !empty($_SESSION['csrf_token']) && !empty($token) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
}
