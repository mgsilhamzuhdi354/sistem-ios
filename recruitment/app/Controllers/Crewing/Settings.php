<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Settings Controller
 * SMTP configuration for crewing users
 */
class Settings extends BaseController {

    public function __construct() {
        parent::__construct();
        if (!isLoggedIn()) {
            redirect(url('/login'));
        }
    }

    /**
     * Settings page
     */
    public function index() {
        $tab = $this->input('tab') ?: 'smtp';
        
        // Get user profile
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
        
        // Get per-user SMTP settings from user_smtp_configs
        $smtpSettings = [
            'smtp_host' => '',
            'smtp_port' => '465',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'ssl',
            'smtp_from_email' => '',
            'smtp_from_name' => 'PT Indo Ocean Crew Services'
        ];
        $stmtSmtp = $this->db->prepare("SELECT * FROM user_smtp_configs WHERE user_id = ? LIMIT 1");
        $stmtSmtp->bind_param('i', $userId);
        $stmtSmtp->execute();
        $userSmtp = $stmtSmtp->get_result()->fetch_assoc();
        if ($userSmtp) {
            $smtpSettings['smtp_host'] = $userSmtp['smtp_host'] ?? '';
            $smtpSettings['smtp_port'] = $userSmtp['smtp_port'] ?? '465';
            $smtpSettings['smtp_username'] = $userSmtp['smtp_username'] ?? '';
            $smtpSettings['smtp_password'] = $userSmtp['smtp_password'] ?? '';
            $smtpSettings['smtp_encryption'] = $userSmtp['smtp_encryption'] ?? 'ssl';
            $smtpSettings['smtp_from_email'] = $userSmtp['smtp_from_email'] ?? '';
            $smtpSettings['smtp_from_name'] = $userSmtp['smtp_from_name'] ?? 'PT Indo Ocean Crew Services';
        }

        $isSmtpActive = !empty($smtpSettings['smtp_host']) && !empty($smtpSettings['smtp_username']) && !empty($smtpSettings['smtp_password']);
        
        // Store ui_scale in session if not already set
        if (!isset($_SESSION['ui_scale']) && isset($user['ui_scale'])) {
            $_SESSION['ui_scale'] = $user['ui_scale'];
        }

        $this->view('crewing/settings/index', [
            'pageTitle' => 'Settings',
            'smtpSettings' => $smtpSettings,
            'isSmtpActive' => $isSmtpActive,
            'user' => $user,
            'activeTab' => $tab
        ]);
    }

    /**
     * Save SMTP settings (per-user to user_smtp_configs)
     */
    public function saveSmtp() {
        if (!$this->isPost()) {
            redirect(url('/crewing/settings'));
        }

        $userId = $_SESSION['user_id'];
        $host = trim($this->input('smtp_host'));
        $port = intval($this->input('smtp_port') ?: 465);
        $username = trim($this->input('smtp_username'));
        $password = trim($this->input('smtp_password'));
        $encryption = trim($this->input('smtp_encryption') ?: 'ssl');
        $fromEmail = trim($this->input('smtp_from_email'));
        $fromName = trim($this->input('smtp_from_name'));

        // Check if user already has a config
        $stmt = $this->db->prepare("SELECT id, smtp_password FROM user_smtp_configs WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        // Keep old password if not provided
        if (empty($password) && $existing) {
            $password = $existing['smtp_password'];
        }

        if ($existing) {
            // Update existing per-user config
            $stmt = $this->db->prepare("
                UPDATE user_smtp_configs 
                SET smtp_host = ?, smtp_port = ?, smtp_username = ?, smtp_password = ?,
                    smtp_encryption = ?, smtp_from_email = ?, smtp_from_name = ?,
                    is_active = 1, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->bind_param('sisssssi', $host, $port, $username, $password, 
                              $encryption, $fromEmail, $fromName, $userId);
        } else {
            // Insert new per-user config
            $stmt = $this->db->prepare("
                INSERT INTO user_smtp_configs 
                (user_id, smtp_host, smtp_port, smtp_username, smtp_password, 
                 smtp_encryption, smtp_from_email, smtp_from_name, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->bind_param('isissss', $userId, $host, $port, $username, $password,
                             $encryption, $fromEmail, $fromName);
        }
        $stmt->execute();

        $_SESSION['flash_success'] = 'Pengaturan SMTP pribadi berhasil disimpan!';
        redirect(url('/crewing/settings'));
    }

    /**
     * Test SMTP connection
     */
    public function testSmtp() {
        try {
            require_once APPPATH . 'Libraries/Mailer.php';
            $mailer = new Mailer($this->db);

            $testEmail = trim($this->input('test_email'));
            if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                return $this->json(['success' => false, 'message' => 'Masukkan email yang valid untuk test']);
            }

            $result = $mailer->send(
                $testEmail,
                'Test User',
                'Test Email dari PT Indo Ocean Crew Services',
                '<h2>✅ Email Test Berhasil!</h2><p>Jika Anda menerima email ini, berarti konfigurasi SMTP sudah benar.</p><p>Dikirim pada: ' . date('d M Y H:i:s') . '</p>',
                null, null, null, []
            );

            return $this->json($result);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Update profile
     */
    public function updateProfile() {
        if (!$this->isPost()) {
            redirect(url('/crewing/settings?tab=profile'));
        }
        
        $userId = $_SESSION['user_id'];
        
        $fullName = trim($this->input('full_name'));
        $phone = trim($this->input('phone'));
        $employeeId = trim($this->input('employee_id'));
        $specialization = trim($this->input('specialization'));
        $maxApplications = intval($this->input('max_applications') ?: 50);
        
        // Handle photo upload
        $photoFilename = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = FCPATH . 'uploads/recruiters/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($ext, $allowedExts)) {
                $photoFilename = 'recruiter_' . $userId . '_' . time() . '.' . $ext;
                $uploadPath = $uploadDir . $photoFilename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                    // Delete old photo if exists
                    $oldPhotoStmt = $this->db->prepare("SELECT photo FROM crewing_profiles WHERE user_id = ?");
                    $oldPhotoStmt->bind_param('i', $userId);
                    $oldPhotoStmt->execute();
                    $oldPhoto = $oldPhotoStmt->get_result()->fetch_assoc();
                    
                    if ($oldPhoto && !empty($oldPhoto['photo'])) {
                        $oldFilePath = $uploadDir . $oldPhoto['photo'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                } else {
                    $photoFilename = null;
                }
            }
        }
        
        // Update users table (including avatar if photo was uploaded)
        if ($photoFilename) {
            $avatarPath = 'uploads/recruiters/' . $photoFilename;
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ?, avatar = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('sssi', $fullName, $phone, $avatarPath, $userId);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('ssi', $fullName, $phone, $userId);
        }
        $stmt->execute();
        
        $checkStmt = $this->db->prepare("SELECT id FROM crewing_profiles WHERE user_id = ?");
        $checkStmt->bind_param('i', $userId);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();
        
        if ($exists) {
            if ($photoFilename) {
                $stmt = $this->db->prepare("
                    UPDATE crewing_profiles SET 
                        employee_id = ?, specialization = ?, max_applications = ?, photo = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->bind_param('ssisi', $employeeId, $specialization, $maxApplications, $photoFilename, $userId);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE crewing_profiles SET 
                        employee_id = ?, specialization = ?, max_applications = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->bind_param('ssii', $employeeId, $specialization, $maxApplications, $userId);
            }
        } else {
            if ($photoFilename) {
                $stmt = $this->db->prepare("
                    INSERT INTO crewing_profiles (user_id, employee_id, specialization, max_applications, photo)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param('issis', $userId, $employeeId, $specialization, $maxApplications, $photoFilename);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO crewing_profiles (user_id, employee_id, specialization, max_applications)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param('issi', $userId, $employeeId, $specialization, $maxApplications);
            }
        }
        $stmt->execute();
        
        // Update session variables
        $_SESSION['user_name'] = $fullName;
        $_SESSION['full_name'] = $fullName;
        if ($photoFilename) {
            $_SESSION['user_avatar'] = 'uploads/recruiters/' . $photoFilename;
        }
        
        flash('success', 'Profile updated successfully!');
        redirect(url('/crewing/settings?tab=profile'));
    }
    
    /**
     * Backup database
     */
    public function backupDatabase() {
        try {
            $filename = 'recruitment_db_backup_' . date('Y-m-d_His') . '.sql';
            $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
            
            // Get database credentials from config
            $dbConfig = require APPPATH . 'Config/Database.php';
            $config = $dbConfig['default'];
            
            $host = $config['hostname'];
            $user = $config['username'];
            $pass = $config['password'];
            $dbname = $config['database'];
            
            // Try to find mysqldump - scan Laragon directory
            $mysqldumpPath = null;
            $laragonMysqlDir = 'C:\\laragon\\bin\\mysql';
            
            if (is_dir($laragonMysqlDir)) {
                $versions = glob($laragonMysqlDir . '\\*', GLOB_ONLYDIR);
                if (!empty($versions)) {
                    // Use the first available version
                    $mysqldumpPath = $versions[0] . '\\bin\\mysqldump.exe';
                }
            }
            
            // If not found in Laragon, try other common locations
            if (!$mysqldumpPath || !file_exists($mysqldumpPath)) {
                $possiblePaths = [
                    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                ];
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $mysqldumpPath = $path;
                        break;
                    }
                }
            }
            
            // If still not found, return error
            if (!$mysqldumpPath || !file_exists($mysqldumpPath)) {
                flash('error', 'mysqldump command not found. Please ensure MySQL is installed properly.');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            // Build command - use PowerShell for better error handling
            $psCommand = "& '{$mysqldumpPath}' -h '{$host}' -u '{$user}'";
            if (!empty($pass)) {
                $psCommand .= " -p'{$pass}'";
            }
            $psCommand .= " '{$dbname}' | Out-File -FilePath '{$filepath}' -Encoding UTF8";
            
            $command = "powershell -Command \"{$psCommand}\"";
            
            // Execute command
            exec($command, $output, $returnVar);
            
            // Check if backup was successful
            if (!file_exists($filepath) || filesize($filepath) < 100) {
                $errorMsg = 'Backup failed. ';
                if (!empty($output)) {
                    $errorMsg .= 'Error: ' . implode(' ', $output);
                }
                error_log('Backup error: ' . $errorMsg . ' | Command: ' . $command);
                throw new Exception($errorMsg);
            }
            
            // Download file
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            
            // Delete temp file
            unlink($filepath);
            exit;
        } catch (Exception $e) {
            error_log('Database backup error: ' . $e->getMessage());
            flash('error', $e->getMessage());
            redirect(url('/crewing/settings?tab=backup'));
        }
    }
    
    /**
     * Personal SMTP Configuration Form (Per-User)
     */
    public function smtpPersonal()
    {
        $userId = $_SESSION['user_id'];
        
        // Get existing personal SMTP config for this user
        $stmt = $this->db->prepare("
            SELECT * FROM user_smtp_configs 
            WHERE user_id = ? AND is_active = 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $existingConfig = $stmt->get_result()->fetch_assoc();
        
        // Password stored as plain text (no encryption)
        if ($existingConfig && !empty($existingConfig['smtp_password'])) {
            $existingConfig['smtp_password_decrypted'] = $existingConfig['smtp_password'];
        }
        
        // Get global SMTP settings for reference
        $globalSettings = [];
        $result = $this->db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%'");
        while ($row = $result->fetch_assoc()) {
            $globalSettings[$row['setting_key']] = $row['setting_value'];
        }
        
        $this->view('crewing/settings/smtp_personal', [
            'pageTitle' => 'Personal SMTP Configuration',
            'existingConfig' => $existingConfig,
            'globalSettings' => $globalSettings,
            'hasConfig' => !empty($existingConfig)
        ]);
    }

    /**
     * Save/Update Personal SMTP Configuration
     */
    public function smtpPersonalSave()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $userId = $_SESSION['user_id'];
        $host = trim($this->input('smtp_host'));
        $port = intval($this->input('smtp_port'));
        $username = trim($this->input('smtp_username'));
        $password = trim($this->input('smtp_password'));
        $encryption = $this->input('smtp_encryption');
        $fromEmail = trim($this->input('smtp_from_email'));
        $fromName = trim($this->input('smtp_from_name'));

        // Validation
        if (empty($host) || empty($username) || empty($password) || empty($fromEmail) || empty($fromName)) {
            return $this->json(['success' => false, 'message' => 'Semua field harus diisi']);
        }

        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Format email tidak valid']);
        }

        if (!in_array($encryption, ['ssl', 'tls'])) {
            return $this->json(['success' => false, 'message' => 'Encryption harus SSL atau TLS']);
        }

        if ($port < 1 || $port > 65535) {
            return $this->json(['success' => false, 'message' => 'Port harus antara 1-65535']);
        }

        // Store password directly (no encryption)
        $encryptedPassword = $password;

        // Check if config exists
        $stmt = $this->db->prepare("SELECT id FROM user_smtp_configs WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            // Update existing config
            $stmt = $this->db->prepare("
                UPDATE user_smtp_configs 
                SET smtp_host = ?, smtp_port = ?, smtp_username = ?, smtp_password = ?,
                    smtp_encryption = ?, smtp_from_email = ?, smtp_from_name = ?,
                    is_active = 1, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->bind_param('sisssssi', $host, $port, $username, $encryptedPassword, 
                              $encryption, $fromEmail, $fromName, $userId);
        } else {
            // Insert new config
            $stmt = $this->db->prepare("
                INSERT INTO user_smtp_configs 
                (user_id, smtp_host, smtp_port, smtp_username, smtp_password, 
                 smtp_encryption, smtp_from_email, smtp_from_name, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->bind_param('isissss', $userId, $host, $port, $username, $encryptedPassword,
                             $encryption, $fromEmail, $fromName);
        }

        if ($stmt->execute()) {
            flash('success', 'Konfigurasi SMTP pribadi berhasil disimpan!');
            return $this->json(['success' => true, 'message' => 'Konfigurasi SMTP pribadi berhasil disimpan!']);
        } else {
            return $this->json(['success' => false, 'message' => 'Gagal menyimpan konfigurasi']);
        }
    }

    /**
     * Test Personal SMTP Connection (AJAX)
     */
    public function smtpPersonalTest()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $host = trim($this->input('smtp_host'));
        $port = intval($this->input('smtp_port'));
        $username = trim($this->input('smtp_username'));
        $password = trim($this->input('smtp_password'));
        $encryption = $this->input('smtp_encryption');

        // Basic validation
        if (empty($host) || empty($username) || empty($password)) {
            return $this->json(['success' => false, 'message' => 'Host, username, dan password harus diisi']);
        }

        // Test connection using PHPMailer
        try {
            if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                return $this->json(['success' => false, 'message' => 'PHPMailer tidak tersedia']);
            }

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->Timeout = 10;

            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port = $port;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Try to connect
            if ($mail->smtpConnect()) {
                $mail->smtpClose();
                return $this->json(['success' => true, 'message' => '✓ Koneksi SMTP berhasil!']);
            } else {
                return $this->json(['success' => false, 'message' => 'Gagal terhubung ke server SMTP']);
            }
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Import/Restore database from uploaded SQL file
     */
    public function importDatabase()
    {
        try {
            // Check if file was uploaded
            if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
                flash('error', 'No file uploaded or upload error occurred');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            $file = $_FILES['sql_file'];
            
            // Validate file extension
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (strtolower($extension) !== 'sql') {
                flash('error', 'Only SQL files are allowed');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            // Validate SQL file content (check first line isn't an error)
            $firstLine = fgets(fopen($file['tmp_name'], 'r'));
            if (stripos($firstLine, 'error') !== false || stripos($firstLine, 'mysqldump') !== false) {
                flash('error', 'Invalid SQL file. The file appears to contain error messages instead of SQL commands.');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            // Get database credentials
            $dbConfig = require APPPATH . 'Config/Database.php';
            $config = $dbConfig['default'];
            
            $host = $config['hostname'];
            $user = $config['username'];
            $pass = $config['password'];
            $dbname = $config['database'];
            
            // Try to find mysql command - scan Laragon directory
            $mysqlPath = null;
            $laragonMysqlDir = 'C:\\laragon\\bin\\mysql';
            
            if (is_dir($laragonMysqlDir)) {
                $versions = glob($laragonMysqlDir . '\\*', GLOB_ONLYDIR);
                if (!empty($versions)) {
                    // Use the first available version
                    $mysqlPath = $versions[0] . '\\bin\\mysql.exe';
                }
            }
            
            // If not found in Laragon, try other common locations
            if (!$mysqlPath || !file_exists($mysqlPath)) {
                $possiblePaths = [
                    'C:\\xampp\\mysql\\bin\\mysql.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
                ];
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $mysqlPath = $path;
                        break;
                    }
                }
            }
            
            // If still not found, return error
            if (!$mysqlPath || !file_exists($mysqlPath)) {
                flash('error', 'MySQL command not found. Please ensure MySQL is installed properly.');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            // Build command using PowerShell for better file handling
            $psCommand = "Get-Content '{$file['tmp_name']}' -Raw | & '{$mysqlPath}' -h '{$host}' -u '{$user}'";
            if (!empty($pass)) {
                $psCommand .= " -p'{$pass}'";
            }
            $psCommand .= " '{$dbname}' 2>&1";
            
            $command = "powershell -Command \"{$psCommand}\"";
            
            // Execute command
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                $errorMsg = 'Import failed. ';
                if (!empty($output)) {
                    // Filter out warning messages, only show errors
                    $errors = array_filter($output, function($line) {
                        return stripos($line, 'error') !== false;
                    });
                    if (!empty($errors)) {
                        $errorMsg .= implode(' ', $errors);
                    } else {
                        $errorMsg .= implode(' ', $output);
                    }
                }
                error_log('Database import error: ' . $errorMsg);
                flash('error', $errorMsg);
            } else {
                flash('success', 'Database imported successfully! All data has been restored.');
            }
            
            return redirect(url('/crewing/settings?tab=backup'));
            
        } catch (Exception $e) {
            error_log('Database import error: ' . $e->getMessage());
            flash('error', 'Import failed: ' . $e->getMessage());
            return redirect(url('/crewing/settings?tab=backup'));
        }
    }
    
    /**
     * Delete all database data (truncate all tables)
     * CRITICAL: Preserves users, roles, and sessions tables
     */
    public function deleteAllData()
    {
        try {
            // Extra security check
            if (!$this->isPost()) {
                flash('error', 'Invalid request');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            // Get confirmation
            $confirmation = $this->input('confirmation');
            if ($confirmation !== 'DELETE ALL DATA') {
                flash('error', 'Confirmation text does not match. Data deletion cancelled.');
                return redirect(url('/crewing/settings?tab=backup'));
            }
            
            // Get all tables
            $result = $this->db->query("SHOW TABLES");
            $tables = [];
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            // Tables to preserve (critical system tables)
            $preserveTables = ['sessions', 'users', 'roles', 'settings'];
            
            // Disable foreign key checks
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            
            // Truncate each table except preserved ones
            $truncatedCount = 0;
            $preservedCount = 0;
            foreach ($tables as $table) {
                // Skip critical tables
                if (in_array($table, $preserveTables)) {
                    $preservedCount++;
                    continue;
                }
                
                $this->db->query("TRUNCATE TABLE `{$table}`");
                $truncatedCount++;
            }
            
            // Re-enable foreign key checks
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
            
            flash('success', "Data deleted successfully! {$truncatedCount} tables truncated. {$preservedCount} system tables preserved (users, roles, settings).");
            return redirect(url('/crewing/settings?tab=backup'));
            
        } catch (Exception $e) {
            error_log('Delete all data error: ' . $e->getMessage());
            flash('error', 'Delete failed: ' . $e->getMessage());
            return redirect(url('/crewing/settings?tab=backup'));
        }
    }

    /**
     * Delete Personal SMTP Configuration
     */
    public function smtpPersonalDelete()
    {
        if (!$this->isPost()) {
            redirect(url('/crewing/settings/smtp-personal'));
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("DELETE FROM user_smtp_configs WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        
        if ($stmt->execute()) {
            flash('success', 'Konfigurasi SMTP pribadi berhasil dihapus. Sistem akan menggunakan SMTP global.');
            redirect(url('/crewing/settings/smtp-personal'));
        } else {
            flash('error', 'Gagal menghapus konfigurasi');
            redirect(url('/crewing/settings/smtp-personal'));
        }
    }
    
    /**
     * Save UI Scale preference (AJAX)
     */
    public function saveUiScale() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $userId = $_SESSION['user_id'];
        $scale = floatval($this->input('scale'));
        
        // Validate scale range
        if ($scale < 0.75 || $scale > 1.20) {
            return $this->json(['success' => false, 'message' => 'Invalid scale value. Must be between 0.75 and 1.20']);
        }
        
        // Update user's ui_scale
        $stmt = $this->db->prepare("UPDATE users SET ui_scale = ? WHERE id = ?");
        $stmt->bind_param('di', $scale, $userId);
        
        if ($stmt->execute()) {
            // Update session
            $_SESSION['ui_scale'] = $scale;
            return $this->json(['success' => true, 'message' => 'UI scale saved successfully']);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to save UI scale']);
    }
    
    /**
     * Delete profile photo
     */
    public function deletePhoto() {
        
        $userId = $_SESSION['user_id'];
        
        // Get current photo
        $stmt = $this->db->prepare("SELECT photo FROM crewing_profiles WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result && !empty($result['photo'])) {
            // Delete file
            $photoPath = FCPATH . 'uploads/recruiters/' . $result['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
            
            // Update database
            $updateStmt = $this->db->prepare("UPDATE crewing_profiles SET photo = NULL WHERE user_id = ?");
            $updateStmt->bind_param('i', $userId);
            $updateStmt->execute();
            
            flash('success', 'Photo deleted successfully!');
        } else {
            flash('error', 'No photo to delete');
        }
        
        redirect(url('/crewing/settings?tab=profile'));
    }
    
    /**
     * Save Language preference (AJAX)
     */
    public function saveLanguage() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $userId = $_SESSION['user_id'];
        $language = trim($this->input('language'));
        
        // Validate language
        $allowedLangs = ['id', 'en'];
        if (!in_array($language, $allowedLangs)) {
            return $this->json(['success' => false, 'message' => 'Invalid language']);
        }
        
        // Update user's language in database
        $stmt = $this->db->prepare("UPDATE users SET language = ? WHERE id = ?");
        $stmt->bind_param('si', $language, $userId);
        
        if ($stmt->execute()) {
            // Update session
            $_SESSION['user_language'] = $language;
            $_SESSION['language'] = $language;
            return $this->json(['success' => true, 'message' => 'Language saved']);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to save language']);
    }
}
