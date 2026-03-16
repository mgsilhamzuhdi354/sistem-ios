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
                'email' => $this->settingsModel->getByGroup('email'),
                'whatsapp' => $this->settingsModel->getByGroup('whatsapp'),
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
    
    /**
     * Save email SMTP settings
     */
    public function saveEmail()
    {
        if (!$this->isPost()) {
            $this->redirect('settings');
        }
        
        $emailFields = [
            'smtp_host', 'smtp_port', 'smtp_secure',
            'smtp_user', 'smtp_pass', 'smtp_from_email', 'smtp_from_name'
        ];
        
        foreach ($emailFields as $field) {
            if (isset($_POST[$field])) {
                $this->settingsModel->set($field, $_POST[$field], 'email', ucfirst(str_replace('_', ' ', $field)));
            }
        }
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Email settings saved successfully']);
        } else {
            $this->setFlash('success', 'Email SMTP settings saved successfully');
            $this->redirect('settings');
        }
    }
    
    /**
     * Save WhatsApp settings
     */
    public function saveWhatsApp()
    {
        if (!$this->isPost()) {
            $this->redirect('settings');
        }
        
        $waFields = [
            'wa_enabled', 'wa_api_token', 'wa_target_phone',
            'wa_notify_contract', 'wa_notify_payroll', 'wa_notify_system'
        ];
        
        foreach ($waFields as $field) {
            if (isset($_POST[$field])) {
                $this->settingsModel->set($field, $_POST[$field], 'whatsapp', ucfirst(str_replace('_', ' ', $field)));
            }
        }
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'WhatsApp settings saved successfully']);
        } else {
            $this->setFlash('success', 'WhatsApp settings saved successfully');
            $this->redirect('settings');
        }
    }
    
    /**
     * Test WhatsApp sending
     */
    public function testWhatsApp()
    {
        $testPhone = $_POST['test_phone'] ?? $_GET['test_phone'] ?? '';
        if (empty($testPhone)) {
            $this->json(['success' => false, 'message' => 'Masukkan nomor HP tujuan test']);
            return;
        }
        
        try {
            require_once APPPATH . 'Libraries/WhatsAppService.php';
            
            // First save current form values
            $waFields = ['wa_enabled', 'wa_api_token', 'wa_target_phone', 'wa_notify_contract', 'wa_notify_payroll', 'wa_notify_system'];
            foreach ($waFields as $field) {
                if (isset($_POST[$field])) {
                    $this->settingsModel->set($field, $_POST[$field], 'whatsapp');
                }
            }
            
            $apiToken = $_POST['wa_api_token'] ?? $this->settingsModel->get('wa_api_token', '');
            
            if (empty($apiToken)) {
                $this->json(['success' => false, 'message' => 'API Token Fonnte belum diisi']);
                return;
            }
            
            $wa = new \App\Libraries\WhatsAppService($apiToken);
            
            // Count how many numbers
            $phoneList = array_filter(array_map('trim', explode(',', $testPhone)));
            $phoneCount = count($phoneList);
            
            $message = "✅ *Test WhatsApp - IndoOcean ERP*\n\n"
                     . "Pesan ini dikirim sebagai test dari halaman Settings ERP.\n"
                     . "Konfigurasi WhatsApp Anda sudah benar!\n\n"
                     . "⏰ " . date('d M Y, H:i:s') . "\n"
                     . "— _IndoOcean ERP System_";
            
            $result = $wa->sendBulk($testPhone, $message);
            
            if ($result) {
                $label = $phoneCount > 1 ? "Test WA berhasil dikirim ke {$phoneCount} nomor" : "Test WA berhasil dikirim ke {$testPhone}";
                $this->json(['success' => true, 'message' => $label]);
            } else {
                $errors = $wa->getErrors();
                $this->json(['success' => false, 'message' => 'Gagal mengirim WA: ' . implode(', ', $errors)]);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Test ALL WhatsApp notification types (8 samples)
     */
    public function testAllWhatsApp()
    {
        $testPhone = $_POST['test_phone'] ?? '';
        if (empty($testPhone)) {
            $this->json(['success' => false, 'message' => 'Masukkan nomor HP tujuan test']);
            return;
        }
        
        try {
            require_once APPPATH . 'Libraries/WhatsAppService.php';
            
            // Save settings first
            $waFields = ['wa_enabled', 'wa_api_token', 'wa_target_phone', 'wa_notify_contract', 'wa_notify_payroll', 'wa_notify_system'];
            foreach ($waFields as $field) {
                if (isset($_POST[$field])) {
                    $this->settingsModel->set($field, $_POST[$field], 'whatsapp');
                }
            }
            
            $apiToken = $_POST['wa_api_token'] ?? $this->settingsModel->get('wa_api_token', '');
            if (empty($apiToken)) {
                $this->json(['success' => false, 'message' => 'API Token Fonnte belum diisi']);
                return;
            }
            
            $wa = new \App\Libraries\WhatsAppService($apiToken);
            $now = date('d M Y, H:i');
            $today = date('d M Y');
            $sentCount = 0;
            $errors = [];
            
            // ═══ 1. Kontrak Baru Dibuat ═══
            $msg1 = "📢 *KONTRAK BARU DIBUAT*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "👤 *Crew:* Ahmad Rasyid\n"
                  . "🎖️ *Rank:* Chief Officer\n"
                  . "🚢 *Vessel:* MV Indo Pacific\n"
                  . "🏢 *Client:* PT Pelayaran Nusantara\n"
                  . "📋 *No. Kontrak:* IOC-2026-0315\n"
                  . "📅 *Sign On:* " . date('d M Y') . "\n"
                  . "📅 *Sign Off:* " . date('d M Y', strtotime('+9 months')) . "\n"
                  . "⏱️ *Durasi:* 9 bulan\n\n"
                  . "🔗 _Buka ERP untuk detail_\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg1)) $sentCount++; else $errors[] = '1-Kontrak Baru';
            sleep(1);
            
            // ═══ 2. Kontrak Diapprove (ON Board) ═══
            $msg2 = "✅ *CREW ON BOARD*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "👤 *Crew:* Budi Setiawan\n"
                  . "🎖️ *Rank:* 2nd Engineer\n"
                  . "🚢 *Vessel:* MT Oceanic Star\n"
                  . "📋 *Kontrak:* IOC-2026-0298\n"
                  . "📅 *Tanggal Aktif:* {$today}\n"
                  . "📊 *Status:* ACTIVE ✅\n\n"
                  . "Kontrak telah disetujui semua pihak.\n"
                  . "Crew sudah ON BOARD.\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg2)) $sentCount++; else $errors[] = '2-Crew ON';
            sleep(1);
            
            // ═══ 3. Kontrak Ditolak ═══
            $msg3 = "⚠️ *KONTRAK DITOLAK*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "👤 *Crew:* Deni Pratama\n"
                  . "🎖️ *Rank:* Able Seaman\n"
                  . "📋 *Kontrak:* IOC-2026-0301\n"
                  . "❌ *Status:* REJECTED\n\n"
                  . "📝 *Alasan Penolakan:*\n"
                  . "Dokumen COC belum lengkap, sertifikat BST expired.\n\n"
                  . "👨‍💼 *Ditolak oleh:* Manager Crewing\n"
                  . "📅 *Tanggal:* {$today}\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg3)) $sentCount++; else $errors[] = '3-Ditolak';
            sleep(1);
            
            // ═══ 4. Kontrak Diperpanjang ═══
            $msg4 = "🔄 *KONTRAK DIPERPANJANG*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "👤 *Crew:* Eko Wijaya\n"
                  . "🎖️ *Rank:* Bosun\n"
                  . "🚢 *Vessel:* MV Indo Pacific\n\n"
                  . "📋 *Kontrak Lama:* IOC-2025-0187\n"
                  . "   └ Sign Off: " . date('d M Y', strtotime('-1 day')) . "\n\n"
                  . "📋 *Kontrak Baru:* IOC-2026-0316\n"
                  . "   ├ Sign On: {$today}\n"
                  . "   ├ Sign Off: " . date('d M Y', strtotime('+9 months')) . "\n"
                  . "   └ Durasi: 9 bulan\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg4)) $sentCount++; else $errors[] = '4-Perpanjang';
            sleep(1);
            
            // ═══ 5. Kontrak Diterminasi (OFF Board) ═══
            $msg5 = "⛔ *CREW OFF BOARD*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "👤 *Crew:* Fajar Nugroho\n"
                  . "🎖️ *Rank:* Oiler\n"
                  . "🚢 *Vessel:* MT Oceanic Star\n"
                  . "📋 *Kontrak:* IOC-2025-0245\n\n"
                  . "❌ *Status:* TERMINATED\n"
                  . "📅 *Tanggal Sign Off:* {$today}\n"
                  . "🏗️ *Pelabuhan:* Tanjung Priok, Jakarta\n\n"
                  . "📝 *Alasan:*\n"
                  . "Kontrak selesai, crew akan repatriasi.\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg5)) $sentCount++; else $errors[] = '5-Crew OFF';
            sleep(1);
            
            // ═══ 6. Payroll Di-generate ═══
            $monthName = date('F Y');
            $msg6 = "💰 *PAYROLL DI-GENERATE*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "📅 *Periode:* {$monthName}\n"
                  . "👥 *Total Crew:* 24 orang\n"
                  . "📊 *Status:* Processing\n\n"
                  . "Payroll telah di-generate otomatis.\n"
                  . "Silakan review dan lakukan finalisasi\n"
                  . "sebelum tanggal 15.\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg6)) $sentCount++; else $errors[] = '6-Payroll Generate';
            sleep(1);
            
            // ═══ 7. Payroll Selesai (PAID) ═══
            $msg7 = "✅ *PAYROLL SELESAI*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "📅 *Periode:* {$monthName}\n"
                  . "📊 *Status:* COMPLETED ✅\n\n"
                  . "┌─────────────────────\n"
                  . "│ 👥 Total Crew : 24\n"
                  . "│ 💵 Gross      : \$48,500.00\n"
                  . "│ 🏦 Tax        : \$2,425.00\n"
                  . "│ ✅ Net Pay    : \$46,075.00\n"
                  . "└─────────────────────\n\n"
                  . "Semua payslip ditandai *PAID*.\n"
                  . "Tanggal bayar: 15 " . date('M Y') . "\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg7)) $sentCount++; else $errors[] = '7-Payroll Selesai';
            sleep(1);
            
            // ═══ 8. Kontrak Expiring (Cron Alert) ═══
            $msg8 = "⚠️ *CONTRACT EXPIRATION ALERT*\n"
                  . "━━━━━━━━━━━━━━━━━━\n\n"
                  . "🔴 *KRITIS — Expired ≤7 hari (2 kontrak):*\n"
                  . "┌─────────────────────\n"
                  . "│ • Ahmad Rasyid — MV Indo Pacific\n"
                  . "│   Rank: Chief Officer | Sisa: *3 hari*\n"
                  . "│   Sign Off: " . date('d M Y', strtotime('+3 days')) . "\n"
                  . "│\n"
                  . "│ • Budi Setiawan — MT Oceanic Star\n"
                  . "│   Rank: 2nd Engineer | Sisa: *5 hari*\n"
                  . "│   Sign Off: " . date('d M Y', strtotime('+5 days')) . "\n"
                  . "└─────────────────────\n\n"
                  . "🟡 *WARNING — Expired ≤30 hari (3 kontrak):*\n"
                  . "┌─────────────────────\n"
                  . "│ • Eko Wijaya — MV Indo Pacific\n"
                  . "│   Rank: Bosun | Sisa: *14 hari*\n"
                  . "│ • Deni Pratama — TB Ocean 01\n"
                  . "│   Rank: AB | Sisa: *21 hari*\n"
                  . "│ • Fajar Nugroho — MT Oceanic Star\n"
                  . "│   Rank: Oiler | Sisa: *28 hari*\n"
                  . "└─────────────────────\n\n"
                  . "📊 Total: *5 kontrak* perlu ditindaklanjuti\n\n"
                  . "⏰ {$now}\n"
                  . "━━━━━━━━━━━━━━━━━━\n"
                  . "— _IndoOcean ERP_ 🌊";
            if ($wa->sendBulk($testPhone, $msg8)) $sentCount++; else $errors[] = '8-Expiring';
            
            if ($sentCount === 8) {
                $this->json(['success' => true, 'message' => "✅ Semua {$sentCount}/8 notifikasi test berhasil dikirim!"]);
            } elseif ($sentCount > 0) {
                $this->json(['success' => true, 'message' => "⚠️ {$sentCount}/8 berhasil, gagal: " . implode(', ', $errors)]);
            } else {
                $allErrors = $wa->getErrors();
                $this->json(['success' => false, 'message' => 'Gagal semua: ' . implode(', ', $allErrors)]);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Test email sending
     */
    public function testEmail()
    {
        $testTo = $_POST['test_email'] ?? $_GET['test_email'] ?? '';
        if (empty($testTo)) {
            $this->json(['success' => false, 'message' => 'Masukkan alamat email tujuan test']);
            return;
        }
        
        try {
            require_once APPPATH . 'Libraries/Mailer.php';
            $mailer = new \App\Libraries\Mailer();
            
            $subject = 'Test Email - PT Indo Ocean ERP';
            $body = '
                <html><body>
                <div style="max-width:500px;margin:20px auto;font-family:Arial,sans-serif;">
                    <div style="background:#0A2463;color:#D4AF37;padding:20px;text-align:center;border-radius:12px 12px 0 0;">
                        <h2 style="margin:0;">PT Indo Ocean ERP</h2>
                        <p style="margin:5px 0 0;font-size:13px;opacity:.8;">Email Test</p>
                    </div>
                    <div style="padding:25px;background:#f9fafb;border:1px solid #e5e7eb;">
                        <p style="color:#059669;font-weight:bold;font-size:16px;">✅ Email berhasil terkirim!</p>
                        <p style="color:#6b7280;font-size:14px;">Konfigurasi SMTP Anda sudah benar. Email ini dikirim sebagai test dari halaman Settings ERP.</p>
                        <p style="color:#9ca3af;font-size:12px;margin-top:20px;">Waktu: ' . date('d M Y, H:i:s') . '</p>
                    </div>
                    <div style="padding:12px;text-align:center;font-size:11px;color:#9ca3af;">
                        PT Indo Ocean Crew Services
                    </div>
                </div>
                </body></html>
            ';
            
            $result = $mailer->send($testTo, $subject, $body, true);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Test email berhasil dikirim ke ' . $testTo]);
            } else {
                $errors = $mailer->getErrors();
                $this->json(['success' => false, 'message' => 'Gagal mengirim email: ' . implode(', ', $errors)]);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
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
                
                // All tables to truncate (order doesn't matter with FK checks off)
                $tablesToTruncate = [
                    // Payroll
                    'payroll_items',
                    'payroll_periods',
                    // Contract related
                    'contract_logs',
                    'contract_documents',
                    'contract_approvals',
                    'contract_deductions',
                    'contract_taxes',
                    'contract_salaries',
                    'contracts',
                    // Crew related
                    'crew_documents',
                    'crew_skills',
                    'crew_experiences',
                    'crew_operationals',
                    'onboarding_tasks',
                    'admin_checklists',
                    'crews',
                    // Notifications
                    'notifications',
                ];
                
                foreach ($tablesToTruncate as $table) {
                    try {
                        $this->db->query("TRUNCATE TABLE `$table`");
                    } catch (\Throwable $e) {
                        // Skip if table doesn't exist
                    }
                }
                
                // Settings - delete and reinitialize
                $this->db->query("DELETE FROM settings");
                
                $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                
                // Reinitialize default settings
                $this->settingsModel->initDefaults();
                
                $deletedItems[] = 'All data (crews, contracts, payroll, notifications, settings)';
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
            'settings'
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
    /**
     * Change language via AJAX
     */
    public function changeLanguage()
    {
        $lang = $_POST['language'] ?? $_GET['language'] ?? 'en';
        
        if (!in_array($lang, ['en', 'id'])) {
            $lang = 'en';
        }
        
        // Set in session
        setLanguage($lang);
        
        // Persist to settings table
        $this->settingsModel->set('app_language', $lang);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'language' => $lang, 'message' => __('settings.language_saved')]);
        } else {
            $this->setFlash('success', __('settings.language_saved'));
            $this->redirect('settings');
        }
    }
    
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
