<?php
/**
 * PT Indo Ocean - ERP System
 * Activity Model - for notification system
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class ActivityModel extends BaseModel
{
    protected $table = 'activity_logs';
    protected $allowedFields = ['user_id', 'action', 'entity_type', 'entity_id', 'description'];
    
    /**
     * Log activity
     */
    public function log($userId, $action, $entityType, $entityId, $description = null)
    {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description
        ]);
    }
    
    /**
     * Get recent activities (for notifications)
     */
    public function getRecent($limit = 10)
    {
        // Check if table exists first
        $check = $this->query("SHOW TABLES LIKE '{$this->table}'");
        if (empty($check)) {
            return [];
        }

        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?";
        return $this->query($sql, [$limit], 'i');
    }

    /**
     * Get activities by entity
     */
    public function getByEntity($entityType, $entityId, $limit = 50)
    {
        $check = $this->query("SHOW TABLES LIKE '{$this->table}'");
        if (empty($check)) {
            return [];
        }
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE entity_type = ? AND entity_id = ? 
                ORDER BY created_at DESC LIMIT ?";
        return $this->query($sql, [$entityType, $entityId, $limit], 'sii');
    }
}
