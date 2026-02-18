<?php
/**
 * Database Migration for ERP Central Hub
 * Run this file once to create required tables
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'erp_db';
$port = 3306;

// Connect
$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database: $dbname\n\n";

// 1. Karyawan sync from HRIS
echo "Creating hris_karyawan table...\n";
$conn->query("
CREATE TABLE IF NOT EXISTS hris_karyawan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hris_id INT NOT NULL UNIQUE COMMENT 'ID from HRIS system',
    nik VARCHAR(50),
    nama_lengkap VARCHAR(100),
    email VARCHAR(100),
    jabatan VARCHAR(100),
    departemen VARCHAR(100),
    status_karyawan ENUM('aktif', 'resign', 'cuti') DEFAULT 'aktif',
    tanggal_bergabung DATE,
    synced_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_hris_id (hris_id),
    INDEX idx_status (status_karyawan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ hris_karyawan table created\n\n";

// 2. Visitor tracking for Company Profile
echo "Creating visitor_logs table...\n";
$conn->query("
CREATE TABLE IF NOT EXISTS visitor_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    page_url VARCHAR(500),
    referrer VARCHAR(500),
    country VARCHAR(100),
    city VARCHAR(100),
    device_type VARCHAR(50),
    browser VARCHAR(100),
    visit_duration INT DEFAULT 0 COMMENT 'in seconds',
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visited_at (visited_at),
    INDEX idx_ip (ip_address),
    INDEX idx_country (country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ visitor_logs table created\n\n";

// 3. Integration logs from all systems
echo "Creating integration_logs table...\n";
$conn->query("
CREATE TABLE IF NOT EXISTS integration_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source_system ENUM('erp', 'hris', 'recruitment', 'company_profile') NOT NULL,
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id INT,
    details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_system (source_system),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ integration_logs table created\n\n";

// 4. Recruitment sync tracking
echo "Creating recruitment_sync table...\n";
$conn->query("
CREATE TABLE IF NOT EXISTS recruitment_sync (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_applicant_id INT NOT NULL,
    crew_id INT,
    contract_id INT,
    sync_status ENUM('pending', 'synced', 'onboarded', 'failed') DEFAULT 'pending',
    synced_at TIMESTAMP NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_applicant (recruitment_applicant_id),
    INDEX idx_status (sync_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ recruitment_sync table created\n\n";

// Check tables
echo "Checking created tables...\n";
$result = $conn->query("SHOW TABLES LIKE 'hris_karyawan'");
echo ($result->num_rows > 0 ? "✓" : "✗") . " hris_karyawan\n";

$result = $conn->query("SHOW TABLES LIKE 'visitor_logs'");
echo ($result->num_rows > 0 ? "✓" : "✗") . " visitor_logs\n";

$result = $conn->query("SHOW TABLES LIKE 'integration_logs'");
echo ($result->num_rows > 0 ? "✓" : "✗") . " integration_logs\n";

$result = $conn->query("SHOW TABLES LIKE 'recruitment_sync'");
echo ($result->num_rows > 0 ? "✓" : "✗") . " recruitment_sync\n";

$conn->close();
echo "\n========== MIGRATION COMPLETE ==========\n";
