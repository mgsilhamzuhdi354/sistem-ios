<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Permissions Controller
 * Master Admin only - Manage role permissions
 */
class Permissions extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            flash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect(url('/login'));
        }
    }
    
    /**
     * Display permissions management page
     */
    public function index() {
        // Get all roles (except Master Admin and Applicant)
        $roles = $this->db->query("
            SELECT * FROM roles 
            WHERE id NOT IN (1, 5) 
            ORDER BY id
        ")->fetch_all(MYSQLI_ASSOC);
        
        // Get all permissions grouped by category
        $permissionsResult = $this->db->query("
            SELECT * FROM permissions ORDER BY sort_order, category
        ");
        
        $permissions = [];
        $categories = [];
        while ($row = $permissionsResult->fetch_assoc()) {
            $permissions[] = $row;
            if (!in_array($row['category'], $categories)) {
                $categories[] = $row['category'];
            }
        }
        
        // Get current role permissions
        $rolePermissions = [];
        foreach ($roles as $role) {
            $result = $this->db->query("
                SELECT permission_id FROM role_permissions WHERE role_id = {$role['id']}
            ");
            $perms = [];
            while ($row = $result->fetch_assoc()) {
                $perms[] = $row['permission_id'];
            }
            $rolePermissions[$role['id']] = $perms;
        }
        
        $this->view('master_admin/permissions', [
            'pageTitle' => 'Kelola Permissions',
            'roles' => $roles,
            'permissions' => $permissions,
            'categories' => $categories,
            'rolePermissions' => $rolePermissions
        ]);
    }
    
    /**
     * Update permissions for a role
     */
    public function updateRolePermissions($roleId) {
        validate_csrf();
        
        // Don't allow modifying Master Admin or Applicant
        if ($roleId == 1 || $roleId == 5) {
            flash('error', 'Role ini tidak dapat dimodifikasi');
            $this->redirect(url('/master-admin/permissions'));
        }
        
        // Get submitted permissions
        $permissionIds = $_POST['permissions'] ?? [];
        
        // Delete existing permissions for this role
        $this->db->query("DELETE FROM role_permissions WHERE role_id = $roleId");
        
        // Insert new permissions
        if (!empty($permissionIds)) {
            $values = [];
            foreach ($permissionIds as $permId) {
                $permId = (int)$permId;
                $values[] = "($roleId, $permId)";
            }
            $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES " . implode(',', $values);
            $this->db->query($sql);
        }
        
        // Get role name for message
        $role = $this->db->query("SELECT name FROM roles WHERE id = $roleId")->fetch_assoc();
        
        flash('success', 'Permissions untuk role "' . ucfirst($role['name']) . '" berhasil diupdate');
        $this->redirect(url('/master-admin/permissions'));
    }
}
