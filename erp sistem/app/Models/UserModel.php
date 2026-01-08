<?php
/**
 * PT Indo Ocean - ERP System
 * User Model with Authentication
 */

namespace App\Models;

require_once APPPATH . 'Models/BaseModel.php';

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $allowedFields = [
        'username', 'email', 'password', 'role', 'full_name', 'phone', 'avatar',
        'is_active', 'last_login', 'login_attempts', 'locked_until',
        'password_reset_token', 'password_reset_expires'
    ];
    
    /**
     * Find user by username or email
     */
    public function findByLogin($login)
    {
        $sql = "SELECT * FROM {$this->table} WHERE (username = ? OR email = ?) AND is_active = 1";
        $result = $this->query($sql, [$login, $login], 'ss');
        return $result[0] ?? null;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
    
    /**
     * Hash password
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Check if account is locked
     */
    public function isLocked($userId)
    {
        $user = $this->find($userId);
        if (!$user) return false;
        
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return true;
        }
        return false;
    }
    
    /**
     * Increment login attempts
     */
    public function incrementLoginAttempts($userId)
    {
        $user = $this->find($userId);
        $attempts = ($user['login_attempts'] ?? 0) + 1;
        
        // Lock account after 5 failed attempts for 15 minutes
        if ($attempts >= 5) {
            $this->update($userId, [
                'login_attempts' => $attempts,
                'locked_until' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
            ]);
        } else {
            $this->update($userId, ['login_attempts' => $attempts]);
        }
        
        return $attempts;
    }
    
    /**
     * Reset login attempts on successful login
     */
    public function resetLoginAttempts($userId)
    {
        $this->update($userId, [
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Generate password reset token
     */
    public function generateResetToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->update($userId, [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires
        ]);
        
        return $token;
    }
    
    /**
     * Verify reset token
     */
    public function verifyResetToken($token)
    {
        // Find user with this token
        $sql = "SELECT * FROM {$this->table} WHERE password_reset_token = ?";
        $result = $this->query($sql, [$token], 's');
        
        if (empty($result)) {
            return null;
        }
        
        $user = $result[0];
        
        // Check expiration using PHP time (avoid timezone mismatch with MySQL)
        if (!$user['password_reset_expires'] || strtotime($user['password_reset_expires']) < time()) {
            return null;
        }
        
        return $user;
    }
    
    /**
     * Clear reset token after use
     */
    public function clearResetToken($userId)
    {
        $this->update($userId, [
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);
    }
    
    /**
     * Get all users with filters
     */
    public function getList($filters = [], $page = 1, $perPage = 20)
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['role'])) {
            $where[] = 'role = ?';
            $params[] = $filters['role'];
            $types .= 's';
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(username LIKE ? OR email LIKE ? OR full_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $types .= 'sss';
        }
        
        if (isset($filters['is_active'])) {
            $where[] = 'is_active = ?';
            $params[] = $filters['is_active'];
            $types .= 'i';
        }
        
        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        
        return $this->query($sql, $params, $types);
    }
    
    /**
     * Count users
     */
    public function countUsers($filters = [])
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['role'])) {
            $where[] = 'role = ?';
            $params[] = $filters['role'];
            $types .= 's';
        }
        
        if (isset($filters['is_active'])) {
            $where[] = 'is_active = ?';
            $params[] = $filters['is_active'];
            $types .= 'i';
        }
        
        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";
        
        $result = $this->query($sql, $params, $types);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Get user by email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $result = $this->query($sql, [$email], 's');
        return $result[0] ?? null;
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null)
    {
        $sql = "SELECT id FROM {$this->table} WHERE username = ?";
        $params = [$username];
        $types = 's';
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }
        
        $result = $this->query($sql, $params, $types);
        return !empty($result);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = ?";
        $params = [$email];
        $types = 's';
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }
        
        $result = $this->query($sql, $params, $types);
        return !empty($result);
    }
}

/**
 * Permission Model
 */
class PermissionModel extends BaseModel
{
    protected $table = 'role_permissions';
    protected $allowedFields = ['role', 'module', 'can_view', 'can_create', 'can_edit', 'can_delete'];
    
    /**
     * Get permissions for role
     */
    public function getByRole($role)
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = ?";
        return $this->query($sql, [$role], 's');
    }
    
    /**
     * Check if role can access module with action
     */
    public function canAccess($role, $module, $action = 'view')
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = ? AND module = ?";
        $result = $this->query($sql, [$role, $module], 'ss');
        
        if (empty($result)) return false;
        
        $permission = $result[0];
        
        switch ($action) {
            case 'view': return (bool)$permission['can_view'];
            case 'create': return (bool)$permission['can_create'];
            case 'edit': return (bool)$permission['can_edit'];
            case 'delete': return (bool)$permission['can_delete'];
            default: return false;
        }
    }
}

/**
 * Login History Model
 */
class LoginHistoryModel extends BaseModel
{
    protected $table = 'login_history';
    protected $allowedFields = ['user_id', 'ip_address', 'user_agent', 'status', 'failure_reason'];
    
    /**
     * Log login attempt
     */
    public function logAttempt($userId, $status, $failureReason = null)
    {
        return $this->create([
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'status' => $status,
            'failure_reason' => $failureReason
        ]);
    }
    
    /**
     * Get login history for user
     */
    public function getByUser($userId, $limit = 20)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY login_at DESC LIMIT ?";
        return $this->query($sql, [$userId, $limit], 'ii');
    }
    
    /**
     * Get recent failed attempts
     */
    public function getRecentFailedAttempts($userId, $minutes = 15)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = ? AND status = 'failed' 
                AND login_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        $result = $this->query($sql, [$userId, $minutes], 'ii');
        return $result[0]['count'] ?? 0;
    }
}

/**
 * Activity Log Model
 */
class ActivityLogModel extends BaseModel
{
    protected $table = 'activity_logs';
    protected $allowedFields = ['user_id', 'action', 'entity_type', 'entity_id', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent'];
    
    /**
     * Log activity
     */
    public function log($userId, $action, $entityType = null, $entityId = null, $description = null, $oldValues = null, $newValues = null)
    {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    /**
     * Get activity logs with filters
     */
    public function getList($filters = [], $page = 1, $perPage = 50)
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['user_id'])) {
            $where[] = 'al.user_id = ?';
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['action'])) {
            $where[] = 'al.action = ?';
            $params[] = $filters['action'];
            $types .= 's';
        }
        
        if (!empty($filters['entity_type'])) {
            $where[] = 'al.entity_type = ?';
            $params[] = $filters['entity_type'];
            $types .= 's';
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(al.created_at) >= ?';
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(al.created_at) <= ?';
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        
        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT al.*, u.full_name as user_name, u.username 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE {$whereClause} 
                ORDER BY al.created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        
        return $this->query($sql, $params, $types);
    }
    
    /**
     * Get activity for entity
     */
    public function getByEntity($entityType, $entityId, $limit = 50)
    {
        $sql = "SELECT al.*, u.full_name as user_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.entity_type = ? AND al.entity_id = ?
                ORDER BY al.created_at DESC LIMIT ?";
        return $this->query($sql, [$entityType, $entityId, $limit], 'sii');
    }
}
