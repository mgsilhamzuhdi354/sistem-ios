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
            WHERE id NOT IN (11, 3) 
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
            $rpStmt = $this->db->prepare("
                SELECT permission_id FROM role_permissions WHERE role_id = ?
            ");
            $rpStmt->bind_param('i', $role['id']);
            $rpStmt->execute();
            $result = $rpStmt->get_result();
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
        
        $roleId = intval($roleId);
        
        // Don't allow modifying Master Admin or Applicant
        if ($roleId == 11 || $roleId == 3) {
            flash('error', 'Role ini tidak dapat dimodifikasi');
            $this->redirect(url('/master-admin/permissions'));
        }
        
        // Get submitted permissions
        $permissionIds = $_POST['permissions'] ?? [];
        
        // Delete existing permissions for this role
        $delStmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $delStmt->bind_param('i', $roleId);
        $delStmt->execute();
        
        // Insert new permissions
        if (!empty($permissionIds)) {
            $insertStmt = $this->db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($permissionIds as $permId) {
                $permId = intval($permId);
                $insertStmt->bind_param('ii', $roleId, $permId);
                $insertStmt->execute();
            }
        }
        
        // Get role name for message
        $roleStmt = $this->db->prepare("SELECT name FROM roles WHERE id = ?");
        $roleStmt->bind_param('i', $roleId);
        $roleStmt->execute();
        $role = $roleStmt->get_result()->fetch_assoc();
        
        flash('success', 'Permissions untuk role "' . ucfirst($role['name']) . '" berhasil diupdate');
        $this->redirect(url('/master-admin/permissions'));
    }
}
