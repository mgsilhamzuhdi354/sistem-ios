<?php
/**
 * Database Initialization Script
 * Run this once to create all required tables
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3306;

// Connect
$conn = new mysqli($host, $user, $pass, '', $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL!\n";

// Create databases
$conn->query("CREATE DATABASE IF NOT EXISTS recruitment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->query("CREATE DATABASE IF NOT EXISTS erp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

echo "Databases created!\n";

// Switch to recruitment_db
$conn->select_db('recruitment_db');

// Create roles table
$conn->query("
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert roles
$conn->query("INSERT IGNORE INTO roles (id, name, description) VALUES 
    (1, 'admin', 'System Administrator'),
    (2, 'hr_staff', 'HR Staff'),
    (3, 'applicant', 'Job Applicant'),
    (4, 'leader', 'Leader'),
    (5, 'crewing', 'Crewing Staff'),
    (11, 'master_admin', 'Master Admin - Full Access')
");

// Create users table
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL DEFAULT 3,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    is_online TINYINT(1) DEFAULT 0,
    last_login TIMESTAMP NULL,
    last_activity TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
)");

// Create default admin user (password: admin123)
$conn->query("INSERT IGNORE INTO users (id, role_id, email, password, full_name, is_active) VALUES 
    (1, 11, 'admin@indoceancrew.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Master Admin', 1)
");

// Create permissions table
$conn->query("
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create role_permissions table
$conn->query("
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
)");

// Create crewing_profiles table
$conn->query("
CREATE TABLE IF NOT EXISTS crewing_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    employee_id VARCHAR(50),
    max_applications INT DEFAULT 50,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Create leader_profiles table
$conn->query("
CREATE TABLE IF NOT EXISTS leader_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    employee_id VARCHAR(50),
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Create notifications table
$conn->query("
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    action_url VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Create settings table
$conn->query("
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Insert default settings
$conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES 
    ('company_name', 'PT Indo Ocean Crew Services', 'string', 'Company Name'),
    ('auto_assign_new_applications', 'false', 'boolean', 'Auto assign new applications')
");

// Create automation_logs table
$conn->query("
CREATE TABLE IF NOT EXISTS automation_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    target_table VARCHAR(100),
    target_id INT,
    action VARCHAR(100),
    details JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
)");

echo "Tables created in recruitment_db!\n";

// Add missing columns to users table (use try-catch since ALTER TABLE doesn't support IF NOT EXISTS)
try {
    $conn->query("ALTER TABLE users ADD COLUMN is_online TINYINT(1) DEFAULT 0 AFTER is_active");
    echo "Added is_online column!\n";
} catch (mysqli_sql_exception $e) {
    echo "is_online column already exists\n";
}

try {
    $conn->query("ALTER TABLE users ADD COLUMN last_activity DATETIME NULL AFTER is_online");
    echo "Added last_activity column!\n";
} catch (mysqli_sql_exception $e) {
    echo "last_activity column already exists\n";
}

// Check tables
$result = $conn->query("SHOW TABLES");
echo "\nTables in recruitment_db:\n";
while ($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

// Show users table structure
echo "\nUsers table columns:\n";
$result = $conn->query("DESCRIBE users");
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

// =====================================================
// NOW CREATE ERP_DB TABLES
// =====================================================
echo "\n\n========== ERP DATABASE ==========\n";

$conn->select_db('erp_db');

// Users table for ERP
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'hr', 'finance', 'manager', 'viewer') DEFAULT 'viewer',
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    password_reset_token VARCHAR(100),
    password_reset_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Insert default admin users
$conn->query("INSERT IGNORE INTO users (username, email, password, role, full_name, is_active) VALUES
    ('admin', 'admin@ptindoocean.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'Super Administrator', TRUE)
");

// Add 2FA columns to users table
try {
    $conn->query("ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 0");
    echo "Added two_factor_enabled column to ERP users!\n";
} catch (mysqli_sql_exception $e) {
    echo "two_factor_enabled column already exists\n";
}

// OTP Codes table for 2FA Authentication
$conn->query("
CREATE TABLE IF NOT EXISTS otp_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    type ENUM('login', 'password_reset', 'verification') DEFAULT 'login',
    expires_at DATETIME NOT NULL,
    attempts INT DEFAULT 0,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Activity logs table
$conn->query("
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Login history table
$conn->query("
CREATE TABLE IF NOT EXISTS login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('success', 'failed') DEFAULT 'success',
    failure_reason VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// User sessions table
$conn->query("
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Role permissions table
$conn->query("
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    can_view BOOLEAN DEFAULT FALSE,
    can_create BOOLEAN DEFAULT FALSE,
    can_edit BOOLEAN DEFAULT FALSE,
    can_delete BOOLEAN DEFAULT FALSE,
    UNIQUE KEY unique_role_module (role, module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Insert default role permissions
$conn->query("INSERT IGNORE INTO role_permissions (role, module, can_view, can_create, can_edit, can_delete) VALUES
    ('super_admin', 'dashboard', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'contracts', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'vessels', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'clients', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'payroll', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'reports', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'settings', TRUE, TRUE, TRUE, TRUE),
    ('super_admin', 'users', TRUE, TRUE, TRUE, TRUE)
");

// ERP Master Tables
$conn->query("
CREATE TABLE IF NOT EXISTS currencies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(3) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    symbol VARCHAR(5),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("INSERT IGNORE INTO currencies (code, name, symbol) VALUES
    ('USD', 'US Dollar', '\$'),
    ('IDR', 'Indonesian Rupiah', 'Rp'),
    ('SGD', 'Singapore Dollar', 'S\$'),
    ('EUR', 'Euro', '€')
");

$conn->query("
CREATE TABLE IF NOT EXISTS vessel_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("INSERT IGNORE INTO vessel_types (name) VALUES
    ('Container Ship'), ('Bulk Carrier'), ('Oil Tanker'), ('LNG Carrier'),
    ('Cargo Ship'), ('Cruise Ship'), ('Offshore Vessel'), ('Passenger Ship')
");

$conn->query("
CREATE TABLE IF NOT EXISTS flag_states (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(3) NOT NULL,
    name VARCHAR(100) NOT NULL,
    emoji VARCHAR(10),
    is_active TINYINT(1) DEFAULT 1
)");

$conn->query("INSERT IGNORE INTO flag_states (code, name) VALUES
    ('ID', 'Indonesia'),
    ('SG', 'Singapore'),
    ('PA', 'Panama'),
    ('LR', 'Liberia')
");

$conn->query("
CREATE TABLE IF NOT EXISTS ranks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    department ENUM('deck', 'engine', 'catering', 'other') NOT NULL,
    level INT DEFAULT 0,
    is_officer TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("INSERT IGNORE INTO ranks (name, department, level, is_officer) VALUES
    ('Captain', 'deck', 1, 1),
    ('Chief Officer', 'deck', 2, 1),
    ('Chief Engineer', 'engine', 1, 1),
    ('2nd Engineer', 'engine', 2, 1)
");

$conn->query("
CREATE TABLE IF NOT EXISTS clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    short_name VARCHAR(50),
    country VARCHAR(100),
    address TEXT,
    email VARCHAR(255),
    phone VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$conn->query("
CREATE TABLE IF NOT EXISTS vessels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    imo_number VARCHAR(20),
    vessel_type_id INT,
    flag_state_id INT,
    client_id INT,
    status ENUM('active', 'maintenance', 'laid_up', 'sold') DEFAULT 'active',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vessel_type_id) REFERENCES vessel_types(id),
    FOREIGN KEY (flag_state_id) REFERENCES flag_states(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
)");

$conn->query("
CREATE TABLE IF NOT EXISTS contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contract_no VARCHAR(50) NOT NULL UNIQUE,
    crew_id INT NOT NULL,
    crew_name VARCHAR(100) NOT NULL,
    vessel_id INT NOT NULL,
    client_id INT NOT NULL,
    rank_id INT NOT NULL,
    contract_type ENUM('temporary', 'fixed', 'permanent') DEFAULT 'fixed',
    status ENUM('draft', 'pending_approval', 'active', 'onboard', 'completed', 'terminated', 'cancelled') DEFAULT 'draft',
    sign_on_date DATE,
    sign_off_date DATE,
    duration_months INT,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vessel_id) REFERENCES vessels(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (rank_id) REFERENCES ranks(id)
)");

$conn->query("
CREATE TABLE IF NOT EXISTS erp_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

echo "Tables created in erp_db!\n";

// Check ERP tables
$result = $conn->query("SHOW TABLES");
echo "\nTables in erp_db:\n";
while ($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

$conn->close();
echo "\n========== ALL DONE! ==========\n";
echo "Login ERP:\n";
echo "  - Username: admin\n";
echo "  - Password: password\n";
echo "\nLogin Recruitment:\n";
echo "  - Email: admin@indoceancrew.com\n";
echo "  - Password: password\n";
?>
