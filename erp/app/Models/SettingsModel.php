<?php
/**
 * PT Indo Ocean - ERP System
 * Settings Model
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class SettingsModel extends BaseModel
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['setting_key', 'setting_value', 'setting_group', 'description'];
    
    /**
     * Get setting by key
     */
    public function get($key, $default = null)
    {
        $result = $this->query("SELECT setting_value FROM settings WHERE setting_key = ?", [$key], 's');
        return $result[0]['setting_value'] ?? $default;
    }
    
    /**
     * Set setting value
     */
    public function set($key, $value, $group = 'general', $description = '')
    {
        $existing = $this->query("SELECT id FROM settings WHERE setting_key = ?", [$key], 's');
        
        if (!empty($existing)) {
            return $this->update($existing[0]['id'], ['setting_value' => $value]);
        } else {
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
                'description' => $description
            ]);
        }
    }
    
    /**
     * Get all settings by group
     */
    public function getByGroup($group)
    {
        return $this->query("SELECT * FROM settings WHERE setting_group = ? ORDER BY setting_key", [$group], 's');
    }
    
    /**
     * Get all settings
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM settings ORDER BY setting_group, setting_key");
    }
    
    /**
     * Initialize default settings
     */
    public function initDefaults()
    {
        $defaults = [
            // General
            ['company_name', 'PT Indo Ocean', 'general', 'Company name'],
            ['company_email', 'info@indoocean.com', 'general', 'Company email'],
            ['company_phone', '+62-21-12345678', 'general', 'Company phone'],
            ['company_address', 'Jakarta, Indonesia', 'general', 'Company address'],
            
            // Currency
            ['default_currency', 'USD', 'currency', 'Default currency code'],
            ['currency_position', 'before', 'currency', 'Currency symbol position (before/after)'],
            
            // Tax
            ['default_tax_rate', '5', 'tax', 'Default tax rate percentage'],
            ['tax_calculation', 'gross', 'tax', 'Tax calculation base (gross/net)'],
            
            // Contract
            ['contract_prefix', 'CTR', 'contract', 'Contract number prefix'],
            ['default_duration', '6', 'contract', 'Default contract duration in months'],
            ['expiry_alert_days', '30,14,7', 'contract', 'Days before expiry to alert'],
            
            // Payroll
            ['payroll_day', '25', 'payroll', 'Default payroll date'],
            ['auto_generate_payroll', '0', 'payroll', 'Auto generate payroll monthly'],
            
            // Notifications
            ['email_notifications', '1', 'notification', 'Enable email notifications'],
            ['contract_expiry_notify', '1', 'notification', 'Notify on contract expiry'],
            ['payroll_complete_notify', '1', 'notification', 'Notify on payroll complete'],
        ];
        
        foreach ($defaults as $setting) {
            $this->set($setting[0], $setting[1], $setting[2], $setting[3]);
        }
        
        return true;
    }
}

/**
 * Notification Model
 */
class NotificationModel extends BaseModel
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'type', 'title', 'message', 'link', 
        'is_read', 'read_at', 'data'
    ];
    
    /**
     * Get unread notifications for user
     */
    public function getUnread($userId = null, $limit = 10)
    {
        $sql = "SELECT * FROM notifications WHERE is_read = 0";
        $params = [];
        $types = '';
        
        if ($userId) {
            $sql .= " AND (user_id = ? OR user_id IS NULL)";
            $params[] = $userId;
            $types .= 'i';
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT $limit";
        
        return $this->query($sql, $params, $types);
    }
    
    /**
     * Get all notifications for user
     */
    public function getForUser($userId = null, $limit = 50)
    {
        $sql = "SELECT * FROM notifications";
        $params = [];
        $types = '';
        
        if ($userId) {
            $sql .= " WHERE user_id = ? OR user_id IS NULL";
            $params[] = $userId;
            $types .= 'i';
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT $limit";
        
        return $this->query($sql, $params, $types);
    }
    
    /**
     * Count unread notifications
     */
    public function countUnread($userId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
        $params = [];
        $types = '';
        
        if ($userId) {
            $sql .= " AND (user_id = ? OR user_id IS NULL)";
            $params[] = $userId;
            $types .= 'i';
        }
        
        $result = $this->query($sql, $params, $types);
        return $result[0]['count'] ?? 0;
    }
    
    /**
     * Mark as read
     */
    public function markAsRead($id)
    {
        return $this->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Mark all as read
     */
    public function markAllAsRead($userId = null)
    {
        $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE is_read = 0";
        if ($userId) {
            $sql .= " AND (user_id = $userId OR user_id IS NULL)";
        }
        return $this->db->query($sql);
    }
    
    /**
     * Create notification
     */
    public function notify($type, $title, $message, $link = null, $userId = null, $data = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data ? json_encode($data) : null,
            'is_read' => 0
        ]);
    }
    
    /**
     * Create contract expiry notification
     */
    public function notifyContractExpiry($contract, $daysRemaining)
    {
        $type = $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 30 ? 'warning' : 'info');
        
        return $this->notify(
            $type,
            'Contract Expiring',
            "Contract for {$contract['crew_name']} expires in {$daysRemaining} days",
            "contracts/{$contract['id']}",
            null,
            ['contract_id' => $contract['id'], 'days' => $daysRemaining]
        );
    }
}
