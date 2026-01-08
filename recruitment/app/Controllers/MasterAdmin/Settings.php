<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Settings Controller
 */
class Settings extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect('/login');
        }
    }
    
    public function index() {
        // Get all settings
        $result = $this->db->query("SELECT * FROM settings ORDER BY setting_key");
        $settings = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        // Group settings by category
        $groupedSettings = [];
        foreach ($settings as $setting) {
            $category = $this->getCategoryFromKey($setting['setting_key']);
            if (!isset($groupedSettings[$category])) {
                $groupedSettings[$category] = [];
            }
            $groupedSettings[$category][] = $setting;
        }
        
        $this->view('master_admin/settings/index', [
            'pageTitle' => 'System Settings',
            'groupedSettings' => $groupedSettings,
            'settings' => $settings
        ]);
    }
    
    private function getCategoryFromKey($key) {
        if (strpos($key, 'email_') === 0) return 'Email Settings';
        if (strpos($key, 'auto_') === 0) return 'Automation';
        if (strpos($key, 'interview_') === 0) return 'Interview Settings';
        if (strpos($key, 'notification_') === 0) return 'Notifications';
        return 'General';
    }
    
    public function update() {
        if (!$this->isPost()) {
            redirect('/master-admin/settings');
        }
        
        validate_csrf();
        
        $settings = $_POST['settings'] ?? [];
        $updatedCount = 0;
        
        foreach ($settings as $key => $value) {
            $key = $this->db->real_escape_string($key);
            $value = $this->db->real_escape_string($value);
            
            $this->db->query("INSERT INTO settings (setting_key, setting_value) 
                             VALUES ('$key', '$value') 
                             ON DUPLICATE KEY UPDATE setting_value = '$value'");
            $updatedCount++;
        }
        
        flash('success', "$updatedCount settings updated successfully.");
        redirect('/master-admin/settings');
    }
}
