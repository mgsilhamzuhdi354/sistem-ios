<?php
/**
 * PT Indo Ocean - ERP System
 * Settings Controller
 */

namespace App\Controllers;

require_once APPPATH . 'Models/SettingsModel.php';

use App\Models\SettingsModel;
use App\Models\NotificationModel;

class Settings extends BaseController
{
    private $settingsModel;
    private $notificationModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->settingsModel = new SettingsModel($this->db);
        $this->notificationModel = new NotificationModel($this->db);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Settings',
            'currentPage' => 'settings',
            'settings' => [
                'general' => $this->settingsModel->getByGroup('general'),
                'currency' => $this->settingsModel->getByGroup('currency'),
                'tax' => $this->settingsModel->getByGroup('tax'),
                'contract' => $this->settingsModel->getByGroup('contract'),
                'payroll' => $this->settingsModel->getByGroup('payroll'),
                'notification' => $this->settingsModel->getByGroup('notification'),
            ],
            'flash' => $this->getFlash()
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'settings/index_modern' : 'settings/index';

        return $this->view($view, $data);
    }
    
    public function save()
    {
        if (!$this->isPost()) {
            $this->redirect('settings');
        }
        
        $settings = $_POST['settings'] ?? [];
        
        foreach ($settings as $key => $value) {
            $this->settingsModel->set($key, $value);
        }
        
        $this->setFlash('success', 'Settings saved successfully');
        $this->redirect('settings');
    }
    
    public function init()
    {
        // Initialize default settings
        $this->settingsModel->initDefaults();
        $this->setFlash('success', 'Default settings initialized');
        $this->redirect('settings');
    }
    
    public function deleteData()
    {
        if (!$this->isPost()) {
            $this->redirect('settings');
        }
        
        $deleteType = $_POST['delete_type'] ?? '';
        $confirmCode = $_POST['confirm_code'] ?? '';
        
        // Verify confirmation code
        if (strtoupper($confirmCode) !== 'HAPUS') {
            $this->setFlash('error', 'Konfirmasi tidak valid');
            $this->redirect('settings');
            return;
        }
        
        $deletedItems = [];
        
        switch ($deleteType) {
            case 'payroll':
                $this->db->query("DELETE FROM payroll_items");
                $this->db->query("DELETE FROM payroll_periods");
                $deletedItems[] = 'Payroll data';
                break;
                
            case 'contracts':
                // Delete in correct order due to foreign keys
                $this->db->query("DELETE FROM payroll_items");
                $this->db->query("DELETE FROM contract_logs");
                $this->db->query("DELETE FROM contract_documents");
                $this->db->query("DELETE FROM contract_approvals");
                $this->db->query("DELETE FROM contract_deductions");
                $this->db->query("DELETE FROM contract_taxes");
                $this->db->query("DELETE FROM contract_salaries");
                $this->db->query("DELETE FROM contracts");
                $deletedItems[] = 'All contracts and related data';
                break;
                
            case 'notifications':
                $this->db->query("DELETE FROM notifications");
                $deletedItems[] = 'All notifications';
                break;
                
            case 'all':
                // Delete all transactional data
                $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                
                // Payroll
                $this->db->query("TRUNCATE TABLE payroll_items");
                $this->db->query("TRUNCATE TABLE payroll_periods");
                
                // Contract related
                $this->db->query("TRUNCATE TABLE contract_logs");
                $this->db->query("TRUNCATE TABLE contract_documents");
                $this->db->query("TRUNCATE TABLE contract_approvals");
                $this->db->query("TRUNCATE TABLE contract_deductions");
                $this->db->query("TRUNCATE TABLE contract_taxes");
                $this->db->query("TRUNCATE TABLE contract_salaries");
                $this->db->query("TRUNCATE TABLE contracts");
                
                // Notifications & Settings
                $this->db->query("TRUNCATE TABLE notifications");
                $this->db->query("DELETE FROM settings");
                
                // Exchange rates
                $this->db->query("DELETE FROM exchange_rates");
                
                $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                
                // Reinitialize default settings
                $this->settingsModel->initDefaults();
                
                $deletedItems[] = 'All data (contracts, payroll, notifications, settings)';
                break;
                
            default:
                $this->setFlash('error', 'Tipe penghapusan tidak valid');
                $this->redirect('settings');
                return;
        }
        
        $message = 'Berhasil menghapus: ' . implode(', ', $deletedItems);
        $this->setFlash('success', $message);
        $this->redirect('settings');
    }
    
    /**
     * Export all data to JSON file
     */
    public function export()
    {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $exportData = [
            'export_info' => [
                'system' => 'PT Indo Ocean ERP',
                'version' => '1.0',
                'exported_at' => date('Y-m-d H:i:s'),
                'exported_by' => 'System'
            ],
            'data' => []
        ];
        
        // Tables to export
        $tables = [
            'contracts',
            'contract_salaries',
            'contract_taxes',
            'contract_deductions',
            'contract_approvals',
            'contract_documents',
            'contract_logs',
            'payroll_periods',
            'payroll_items',
            'notifications',
            'settings',
            'exchange_rates'
        ];
        
        foreach ($tables as $table) {
            $result = $this->db->query("SELECT * FROM $table");
            if ($result) {
                $exportData['data'][$table] = $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        
        $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'erp_backup_' . date('Y-m-d_His') . '.json';
        
        // Set headers for download
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($jsonContent));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo $jsonContent;
        exit;
    }
    
    /**
     * Import data from JSON file
     */
    public function import()
    {
        if (!$this->isPost()) {
            $this->redirect('settings');
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'File tidak valid atau gagal diupload');
            $this->redirect('settings');
            return;
        }
        
        $file = $_FILES['import_file'];
        
        // Validate file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'json') {
            $this->setFlash('error', 'Format file harus JSON');
            $this->redirect('settings');
            return;
        }
        
        // Read and parse file
        $content = file_get_contents($file['tmp_name']);
        $importData = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->setFlash('error', 'File JSON tidak valid: ' . json_last_error_msg());
            $this->redirect('settings');
            return;
        }
        
        // Verify export format
        if (!isset($importData['export_info']) || !isset($importData['data'])) {
            $this->setFlash('error', 'Format backup tidak valid');
            $this->redirect('settings');
            return;
        }
        
        // Import data
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        
        $importedTables = [];
        $importOrder = [
            'settings',
            'exchange_rates',
            'contracts',
            'contract_salaries',
            'contract_taxes',
            'contract_deductions',
            'contract_approvals',
            'contract_documents',
            'contract_logs',
            'payroll_periods',
            'payroll_items',
            'notifications'
        ];
        
        foreach ($importOrder as $table) {
            if (isset($importData['data'][$table]) && !empty($importData['data'][$table])) {
                // Clear existing data
                $this->db->query("TRUNCATE TABLE $table");
                
                // Insert new data
                foreach ($importData['data'][$table] as $row) {
                    $columns = array_keys($row);
                    $values = array_map(function($v) {
                        if ($v === null) return 'NULL';
                        return "'" . $this->db->real_escape_string($v) . "'";
                    }, array_values($row));
                    
                    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
                    $this->db->query($sql);
                }
                
                $importedTables[] = $table . ' (' . count($importData['data'][$table]) . ')';
            }
        }
        
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        
        $exportInfo = $importData['export_info'];
        $message = 'Berhasil import data dari backup ' . ($exportInfo['exported_at'] ?? 'unknown') . '. Tables: ' . implode(', ', $importedTables);
        $this->setFlash('success', $message);
        $this->redirect('settings');
    }
}

/**
 * Notification Controller
 */
class Notification extends BaseController
{
    private $notificationModel;
    
    public function __construct()
    {
        parent::__construct();
        require_once APPPATH . 'Models/SettingsModel.php';
        $this->notificationModel = new NotificationModel($this->db);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Notifications',
            'notifications' => $this->notificationModel->getForUser(null, 100),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('notifications/index', $data);
    }
    
    public function getUnread()
    {
        $notifications = $this->notificationModel->getUnread(null, 10);
        $count = $this->notificationModel->countUnread();
        
        $this->json([
            'success' => true,
            'count' => $count,
            'notifications' => $notifications
        ]);
    }
    
    public function markRead($id)
    {
        $this->notificationModel->markAsRead($id);
        
        if ($this->isAjax()) {
            $this->json(['success' => true]);
        } else {
            $this->redirect('notifications');
        }
    }
    
    public function markAllRead()
    {
        $this->notificationModel->markAllAsRead();
        
        if ($this->isAjax()) {
            $this->json(['success' => true]);
        } else {
            $this->setFlash('success', 'All notifications marked as read');
            $this->redirect('notifications');
        }
    }
    
    public function generate()
    {
        // Generate notifications for expiring contracts
        require_once APPPATH . 'Models/ContractModel.php';
        $contractModel = new \App\Models\ContractModel($this->db);
        
        $expiringContracts = $contractModel->getExpiring(30);
        $count = 0;
        
        foreach ($expiringContracts as $contract) {
            $days = $contract['days_remaining'];
            
            // Check if notification already exists
            $existing = $this->db->query("SELECT id FROM notifications WHERE data LIKE '%\"contract_id\":{$contract['id']}%' AND DATE(created_at) = CURDATE()");
            if ($existing->num_rows == 0) {
                $this->notificationModel->notifyContractExpiry($contract, $days);
                $count++;
            }
        }
        
        $this->setFlash('success', "Generated $count notifications");
        $this->redirect('notifications');
    }
}
