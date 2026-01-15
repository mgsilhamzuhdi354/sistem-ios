<?php
/**
 * AUTO DATABASE FIX SCRIPT
 * Run this to fix all database issues automatically
 */

// Suppress mysqli exceptions - we'll handle errors manually
mysqli_report(MYSQLI_REPORT_OFF);

$isProduction = (
    isset($_SERVER['HTTP_HOST']) && 
    strpos($_SERVER['HTTP_HOST'], 'localhost') === false &&
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false
);

$dbConfig = $isProduction 
    ? ['hostname' => 'localhost', 'username' => 'indoocea_deploy', 'password' => 'Ilhamzuhdi90', 'database' => 'indoocea_recruitment', 'port' => 3306]
    : ['hostname' => 'localhost', 'username' => 'root', 'password' => '', 'database' => 'recruitment_db', 'port' => 3308];

$conn = new mysqli($dbConfig['hostname'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database'], $dbConfig['port']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>🔧 Auto Database Fix</h2>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#1a1a2e;color:#eee;}.success{color:#4ade80;}.error{color:#f87171;}.info{color:#60a5fa;}</style>";

$queries = [
    // Add assigned_at column
    "ALTER TABLE application_assignments ADD COLUMN assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP" => "Kolom assigned_at",
    
    // Create archived_applications
    "CREATE TABLE IF NOT EXISTS archived_applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        original_application_id INT,
        user_id INT NOT NULL,
        vacancy_id INT,
        status_id INT,
        archived_by INT,
        archive_reason TEXT,
        original_data JSON,
        archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel archived_applications",
    
    // Create email_templates
    "CREATE TABLE IF NOT EXISTS email_templates (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE,
        subject VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        variables TEXT,
        category VARCHAR(50) DEFAULT 'general',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )" => "Tabel email_templates",
    
    // Create email_settings
    "CREATE TABLE IF NOT EXISTS email_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        description VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel email_settings",
    
    // Create permissions
    "CREATE TABLE IF NOT EXISTS permissions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL UNIQUE,
        display_name VARCHAR(100) NOT NULL,
        category VARCHAR(50) NOT NULL,
        sort_order INT DEFAULT 0
    )" => "Tabel permissions",
    
    // Create role_permissions
    "CREATE TABLE IF NOT EXISTS role_permissions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        UNIQUE KEY unique_role_permission (role_id, permission_id)
    )" => "Tabel role_permissions",
    
    // Create contract_requests
    "CREATE TABLE IF NOT EXISTS contract_requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL,
        requested_by INT NOT NULL,
        contract_type VARCHAR(100),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        processed_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel contract_requests",
    
    // Create email_archive
    "CREATE TABLE IF NOT EXISTS email_archive (
        id INT PRIMARY KEY AUTO_INCREMENT,
        to_email VARCHAR(255) NOT NULL,
        subject VARCHAR(255),
        body TEXT,
        status ENUM('sent', 'failed') DEFAULT 'sent',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel email_archive",
    
    // Drop and recreate email_logs with correct columns for Mailer.php
    "DROP TABLE IF EXISTS email_logs" => "Hapus tabel email_logs lama",
    
    "CREATE TABLE email_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        application_id INT,
        template_id INT,
        subject VARCHAR(255),
        recipient_email VARCHAR(255) NOT NULL,
        recipient_name VARCHAR(255),
        status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
        error_message TEXT,
        sent_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel email_logs (baru)",
    
    // Add specialization column
    "ALTER TABLE crewing_profiles ADD COLUMN specialization VARCHAR(100)" => "Kolom specialization",
    
    // Add priority column to applications
    "ALTER TABLE applications ADD COLUMN priority ENUM('urgent', 'high', 'normal', 'low') DEFAULT 'normal'" => "Kolom priority",
    
    // Add submitted_at column to applications
    "ALTER TABLE applications ADD COLUMN submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP" => "Kolom submitted_at",
    
    // Add reviewed_by column to applications
    "ALTER TABLE applications ADD COLUMN reviewed_by INT" => "Kolom reviewed_by",
    
    // Create departments table
    "CREATE TABLE IF NOT EXISTS departments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description VARCHAR(255),
        color VARCHAR(20) DEFAULT '#6c757d',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel departments",
    
    // Add department_id to job_vacancies
    "ALTER TABLE job_vacancies ADD COLUMN department_id INT" => "Kolom department_id",
    
    // Insert default departments
    "INSERT IGNORE INTO departments (id, name, color) VALUES 
    (1, 'Deck', '#3b82f6'),
    (2, 'Engine', '#ef4444'),
    (3, 'Galley', '#22c55e'),
    (4, 'Steward', '#f59e0b')" => "Data departments",
    
    // Add current_crewing_id to applications
    "ALTER TABLE applications ADD COLUMN current_crewing_id INT" => "Kolom current_crewing_id",
    
    // Add auto_assigned column
    "ALTER TABLE applications ADD COLUMN auto_assigned TINYINT(1) DEFAULT 0" => "Kolom auto_assigned",
    
    // Add position column to leader_profiles
    "ALTER TABLE leader_profiles ADD COLUMN position VARCHAR(100)" => "Kolom position (leader)",
    
    // Add phone_extension column to leader_profiles
    "ALTER TABLE leader_profiles ADD COLUMN phone_extension VARCHAR(20)" => "Kolom phone_extension (leader)",
    
    // Add bio column to leader_profiles  
    "ALTER TABLE leader_profiles ADD COLUMN bio TEXT" => "Kolom bio (leader)",
    
    // Create status_change_requests table for approval workflow
    "CREATE TABLE IF NOT EXISTS status_change_requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL,
        requested_by INT NOT NULL,
        from_status_id INT NOT NULL,
        to_status_id INT NOT NULL,
        reason TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        reviewed_by INT,
        reviewed_at TIMESTAMP NULL,
        review_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel status_change_requests",
    
    // Create job_claim_requests table for Leader/Crewing to claim applicants
    "CREATE TABLE IF NOT EXISTS job_claim_requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL,
        requested_by INT NOT NULL,
        reason TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        reviewed_by INT,
        reviewed_at TIMESTAMP NULL,
        review_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )" => "Tabel job_claim_requests",
];

$success = 0;
$skipped = 0;

foreach ($queries as $sql => $label) {
    $result = @$conn->query($sql);
    if ($result) {
        echo "<p class='success'>✅ $label - OK</p>";
        $success++;
    } else {
        if (strpos($conn->error, 'Duplicate') !== false || strpos($conn->error, 'already exists') !== false) {
            echo "<p class='info'>ℹ️ $label - Sudah ada (skip)</p>";
            $skipped++;
        } else {
            echo "<p class='error'>❌ $label - " . $conn->error . "</p>";
        }
    }
}

// Insert default email templates
$conn->query("INSERT IGNORE INTO email_templates (name, subject, body, category) VALUES 
('welcome', 'Selamat Datang', 'Halo {name}, Selamat datang!', 'auth'),
('otp', 'Kode OTP Anda', 'Kode OTP: {otp}', 'auth'),
('application_received', 'Lamaran Diterima', 'Lamaran Anda telah diterima', 'application')");

// Insert default permissions
$conn->query("INSERT IGNORE INTO permissions (name, display_name, category, sort_order) VALUES 
('dashboard.view', 'Lihat Dashboard', 'Dashboard', 1),
('users.view', 'Lihat User', 'Users', 10),
('users.create', 'Buat User', 'Users', 11),
('vacancies.view', 'Lihat Lowongan', 'Vacancies', 20),
('applications.view', 'Lihat Lamaran', 'Applications', 30),
('pipeline.view', 'Lihat Pipeline', 'Pipeline', 40),
('pipeline.request_claim', 'Request Ambil Job', 'Pipeline', 41),
('pipeline.request_status', 'Request Ubah Status', 'Pipeline', 42),
('reports.view', 'Lihat Laporan', 'Reports', 80),
('settings.view', 'Lihat Settings', 'Settings', 90)");

echo "<hr>";
echo "<h3>📊 Hasil:</h3>";
echo "<p>✅ Berhasil: $success</p>";
echo "<p>ℹ️ Dilewati: $skipped</p>";

echo "<h3>📋 Daftar Tabel Sekarang:</h3>";
$tables = $conn->query("SHOW TABLES");
echo "<ul>";
while ($row = $tables->fetch_array()) {
    echo "<li>{$row[0]}</li>";
}
echo "</ul>";

$conn->close();
?>
<hr>
<p><a href="<?= str_replace('/auto_fix_db.php', '/master-admin/dashboard', $_SERVER['REQUEST_URI']) ?>" style="color:#60a5fa;">🏠 Kembali ke Dashboard</a></p>
<p style="color:#f87171;">⚠️ HAPUS FILE INI SETELAH SELESAI!</p>
