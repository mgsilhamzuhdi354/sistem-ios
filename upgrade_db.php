<?php
/**
 * Complete Database Upgrade Script
 * Runs ALL migrations for complete recruitment system
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3306;

echo "<pre style='font-family: monospace; background: #1a1a2e; color: #0f0; padding: 20px;'>";
echo "========================================\n";
echo "PT INDO OCEAN - DATABASE UPGRADE SCRIPT\n";
echo "========================================\n\n";

// Connect
$conn = new mysqli($host, $user, $pass, '', $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

echo "✓ Connected to MySQL!\n\n";

// Select recruitment_db
$conn->query("CREATE DATABASE IF NOT EXISTS recruitment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('recruitment_db');

echo "=== UPGRADING RECRUITMENT_DB ===\n\n";

// ================================
// 1. CREATE ALL MISSING TABLES
// ================================

echo "1. Creating/Updating Tables...\n";

// Roles table - COMPLETE structure
$conn->query("
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert ALL roles
$roles = [
    [1, 'admin', 'Administrator - Job Vacancy Management'],
    [2, 'crewing_deprecated', 'DEPRECATED - Use role 5'],
    [3, 'applicant', 'Job Applicant'],
    [4, 'leader', 'Leader - Team & Pipeline Management'],
    [5, 'crewing', 'Crewing Staff / Crewing PIC - Application Handler'],
    [11, 'master_admin', 'Master Administrator - Full System Access']
];
foreach ($roles as $role) {
    $conn->query("INSERT INTO roles (id, name, description) VALUES ({$role[0]}, '{$role[1]}', '{$role[2]}') 
                  ON DUPLICATE KEY UPDATE name='{$role[1]}', description='{$role[2]}'");
}
echo "   ✓ Roles table updated\n";

// Users table
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
    last_activity DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
)");

// Add missing columns
$columns_to_add = [
    ['is_online', "TINYINT(1) DEFAULT 0 AFTER is_active"],
    ['last_activity', "DATETIME NULL AFTER is_online"]
];
foreach ($columns_to_add as $col) {
    try {
        $conn->query("ALTER TABLE users ADD COLUMN {$col[0]} {$col[1]}");
    } catch (mysqli_sql_exception $e) {}
}
echo "   ✓ Users table updated\n";

// Permissions table
$conn->query("
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    display_name VARCHAR(150) NULL,
    description VARCHAR(255),
    category VARCHAR(50),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Add missing columns if table already exists
try { $conn->query("ALTER TABLE permissions ADD COLUMN sort_order INT DEFAULT 0 AFTER category"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE permissions ADD COLUMN display_name VARCHAR(150) NULL AFTER name"); } catch (mysqli_sql_exception $e) {}

// Role_permissions
$conn->query("
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
)");

// Insert default permissions with display_name
$permissions = [
    ['user_view', 'Lihat User', 'View Users', 'users', 1],
    ['user_create', 'Buat User', 'Create Users', 'users', 2],
    ['user_edit', 'Edit User', 'Edit Users', 'users', 3],
    ['user_delete', 'Hapus User', 'Delete Users', 'users', 4],
    ['vacancy_view', 'Lihat Lowongan', 'View Job Vacancies', 'vacancies', 5],
    ['vacancy_create', 'Buat Lowongan', 'Create Job Vacancies', 'vacancies', 6],
    ['vacancy_edit', 'Edit Lowongan', 'Edit Job Vacancies', 'vacancies', 7],
    ['vacancy_delete', 'Hapus Lowongan', 'Delete Job Vacancies', 'vacancies', 8],
    ['application_view', 'Lihat Lamaran', 'View Applications', 'applications', 9],
    ['application_process', 'Proses Lamaran', 'Process Applications', 'applications', 10],
    ['application_approve', 'Setujui Lamaran', 'Approve Applications', 'applications', 11],
    ['application_reject', 'Tolak Lamaran', 'Reject Applications', 'applications', 12],
    ['pipeline_view', 'Lihat Pipeline', 'View Pipeline', 'pipeline', 13],
    ['pipeline_manage', 'Kelola Pipeline', 'Manage Pipeline', 'pipeline', 14],
    ['report_view', 'Lihat Laporan', 'View Reports', 'reports', 15],
    ['report_export', 'Export Laporan', 'Export Reports', 'reports', 16],
    ['settings_view', 'Lihat Pengaturan', 'View Settings', 'system', 17],
    ['settings_edit', 'Edit Pengaturan', 'Edit Settings', 'system', 18],
    ['permissions_manage', 'Kelola Hak Akses', 'Manage Permissions', 'system', 19],
    ['email_manage', 'Kelola Email', 'Manage Email Templates', 'system', 20]
];
foreach ($permissions as $p) {
    $conn->query("INSERT INTO permissions (name, display_name, description, category, sort_order) VALUES ('{$p[0]}', '{$p[1]}', '{$p[2]}', '{$p[3]}', {$p[4]}) ON DUPLICATE KEY UPDATE display_name = '{$p[1]}', description = '{$p[2]}', category = '{$p[3]}', sort_order = {$p[4]}");
}
echo "   ✓ Permissions tables updated\n";

// Crewing profiles
$conn->query("
CREATE TABLE IF NOT EXISTS crewing_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    employee_id VARCHAR(50),
    department_ids JSON DEFAULT NULL,
    can_assign_to JSON DEFAULT NULL,
    is_pic TINYINT(1) DEFAULT 0,
    max_applications INT DEFAULT 50,
    specialization VARCHAR(200),
    `rank` VARCHAR(50) NULL,
    company VARCHAR(100) NULL,
    leader_id INT NULL,
    department VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Add columns to crewing_profiles if they don't exist
$crewing_columns = [
    'is_pic' => 'TINYINT(1) DEFAULT 0',
    'max_applications' => 'INT DEFAULT 50',
    'specialization' => 'VARCHAR(200)',
    '`rank`' => 'VARCHAR(50) NULL',
    'company' => 'VARCHAR(100) NULL',
    'leader_id' => 'INT NULL',
    'department' => 'VARCHAR(100) NULL',
    'department_ids' => 'JSON DEFAULT NULL',
    'can_assign_to' => 'JSON DEFAULT NULL'
];
foreach ($crewing_columns as $col => $def) {
    try {
        $conn->query("ALTER TABLE crewing_profiles ADD COLUMN $col $def");
    } catch (mysqli_sql_exception $e) {}
}
echo "   ✓ Crewing profiles table updated\n";

// Leader profiles
$conn->query("
CREATE TABLE IF NOT EXISTS leader_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    employee_id VARCHAR(50) NULL,
    department VARCHAR(100) NULL,
    max_team_members INT DEFAULT 10,
    can_create_crewing BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Add columns to leader_profiles if they don't exist
$leader_columns = [
    'max_team_members' => 'INT DEFAULT 10',
    'can_create_crewing' => 'BOOLEAN DEFAULT TRUE'
];
foreach ($leader_columns as $col => $def) {
    try {
        $conn->query("ALTER TABLE leader_profiles ADD COLUMN $col $def");
    } catch (mysqli_sql_exception $e) {}
}
echo "   ✓ Leader profiles table updated\n";

// Admin profiles (for Master Admin profile page)
$conn->query("
CREATE TABLE IF NOT EXISTS admin_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    employee_id VARCHAR(50) NULL,
    department VARCHAR(100) NULL,
    position VARCHAR(100) NULL,
    phone_extension VARCHAR(20) NULL,
    bio TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");
echo "   ✓ Admin profiles table updated\n";

// Departments
$conn->query("
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_id VARCHAR(100),
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("INSERT IGNORE INTO departments (name, name_id, icon, color) VALUES 
    ('Deck Department', 'Departemen Dek', 'fa-ship', '#0A2463'),
    ('Engine Department', 'Departemen Mesin', 'fa-cogs', '#D4AF37'),
    ('Hotel Department', 'Departemen Hotel', 'fa-hotel', '#1E5AA8'),
    ('Entertainment', 'Hiburan', 'fa-music', '#9B59B6')
");
echo "   ✓ Departments table updated\n";

// Vessel types
$conn->query("
CREATE TABLE IF NOT EXISTS vessel_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1
)");

$conn->query("INSERT IGNORE INTO vessel_types (name) VALUES 
    ('Bulk Carrier'), ('Container Ship'), ('Tanker'), ('Cruise Ship'), 
    ('Offshore Vessel'), ('Passenger Ship'), ('Cargo Ship'), ('LNG Carrier')
");
echo "   ✓ Vessel types table updated\n";

// Job vacancies
$conn->query("
CREATE TABLE IF NOT EXISTS job_vacancies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    vessel_type_id INT,
    title VARCHAR(200) NOT NULL,
    title_id VARCHAR(200),
    slug VARCHAR(200) NOT NULL UNIQUE,
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    salary_currency VARCHAR(10) DEFAULT 'USD',
    contract_duration_months INT,
    joining_date DATE,
    description TEXT,
    description_id TEXT,
    requirements TEXT,
    requirements_id TEXT,
    min_experience_months INT DEFAULT 0,
    min_age INT,
    max_age INT,
    required_certificates JSON,
    status ENUM('draft', 'published', 'closed') DEFAULT 'draft',
    is_featured TINYINT(1) DEFAULT 0,
    application_deadline DATE,
    views_count INT DEFAULT 0,
    applications_count INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
)");
echo "   ✓ Job vacancies table updated\n";

// Application statuses
$conn->query("
CREATE TABLE IF NOT EXISTS application_statuses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    name_id VARCHAR(50),
    description VARCHAR(255),
    color VARCHAR(20),
    sort_order INT DEFAULT 0
)");

$conn->query("INSERT IGNORE INTO application_statuses (id, name, name_id, color, sort_order) VALUES 
    (1, 'New Application', 'Lamaran Baru', '#3498db', 1),
    (2, 'Document Screening', 'Screening Dokumen', '#f39c12', 2),
    (3, 'Interview', 'Interview', '#9b59b6', 3),
    (4, 'Medical Check', 'Medical Check-up', '#e67e22', 4),
    (5, 'Final Review', 'Review Akhir', '#1abc9c', 5),
    (6, 'Approved', 'Diterima', '#27ae60', 6),
    (7, 'Rejected', 'Ditolak', '#e74c3c', 7),
    (8, 'On Hold', 'Ditunda', '#95a5a6', 8)
");
echo "   ✓ Application statuses table updated\n";

// Applications
$conn->query("
CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    vacancy_id INT NOT NULL,
    status_id INT NOT NULL DEFAULT 1,
    current_crewing_id INT NULL,
    auto_assigned TINYINT(1) DEFAULT 0,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    preferred_crewing_id INT NULL,
    cover_letter TEXT,
    expected_salary DECIMAL(10,2),
    available_date DATE,
    document_score INT,
    interview_score INT,
    overall_score INT,
    admin_notes TEXT,
    rejection_reason TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    status_updated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_application (user_id, vacancy_id)
)");

// Add archive columns to applications if missing
try { $conn->query("ALTER TABLE applications ADD COLUMN is_archived TINYINT(1) DEFAULT 0 AFTER admin_notes"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE applications ADD COLUMN archived_at DATETIME NULL AFTER is_archived"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE applications ADD COLUMN archived_by INT NULL AFTER archived_at"); } catch (mysqli_sql_exception $e) {}

echo "   ✓ Applications table updated\n";

// Application assignments
$conn->query("
CREATE TABLE IF NOT EXISTS application_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    assigned_to INT NOT NULL,
    assigned_by INT NOT NULL,
    notes TEXT,
    status ENUM('active', 'transferred', 'completed') DEFAULT 'active',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
)");
echo "   ✓ Application assignments table updated\n";

// Application status history
$conn->query("
CREATE TABLE IF NOT EXISTS application_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    from_status_id INT,
    to_status_id INT NOT NULL,
    notes TEXT,
    changed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Application status history table updated\n";

// Pipeline requests
$conn->query("
CREATE TABLE IF NOT EXISTS pipeline_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    requested_by INT NOT NULL,
    assigned_to INT NOT NULL,
    from_status_id INT NOT NULL,
    to_status_id INT NOT NULL,
    reason TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    response_notes TEXT NULL,
    responded_by INT NULL,
    responded_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Pipeline requests table updated\n";

// Handler transfers
$conn->query("
CREATE TABLE IF NOT EXISTS handler_transfers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    from_crewing_id INT NOT NULL,
    to_crewing_id INT NOT NULL,
    transferred_by INT NOT NULL,
    reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Handler transfers table updated\n";

// Crewing ratings
$conn->query("
CREATE TABLE IF NOT EXISTS crewing_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crewing_id INT NOT NULL,
    applicant_id INT NOT NULL,
    application_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Crewing ratings table updated\n";

// Applicant profiles
$conn->query("
CREATE TABLE IF NOT EXISTS applicant_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    date_of_birth DATE,
    gender ENUM('male', 'female') NULL,
    nationality VARCHAR(100),
    place_of_birth VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    seaman_book_no VARCHAR(50),
    seaman_book_expiry DATE,
    passport_no VARCHAR(50),
    passport_expiry DATE,
    height_cm INT,
    weight_kg INT,
    shoe_size VARCHAR(10),
    overall_size VARCHAR(10),
    blood_type VARCHAR(5),
    emergency_name VARCHAR(100),
    emergency_phone VARCHAR(20),
    emergency_relation VARCHAR(50),
    total_sea_service_months INT DEFAULT 0,
    last_vessel_name VARCHAR(100),
    last_vessel_type VARCHAR(100),
    last_rank VARCHAR(100),
    last_sign_off DATE,
    profile_completion INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
echo "   ✓ Applicant profiles table updated\n";

// Document types
$conn->query("
CREATE TABLE IF NOT EXISTS document_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_id VARCHAR(100),
    is_required TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0
)");

$conn->query("INSERT IGNORE INTO document_types (name, name_id, is_required, sort_order) VALUES 
    ('CV/Resume', 'CV/Resume', 1, 1),
    ('Passport', 'Paspor', 1, 2),
    ('Seaman Book', 'Buku Pelaut', 1, 3),
    ('COC Certificate', 'Sertifikat COC', 1, 4),
    ('STCW Certificates', 'Sertifikat STCW', 1, 5),
    ('Medical Certificate', 'Sertifikat Medis', 1, 6),
    ('Photo', 'Foto', 1, 7),
    ('Other Certificates', 'Sertifikat Lainnya', 0, 8)
");
echo "   ✓ Document types table updated\n";

// Documents
$conn->query("
CREATE TABLE IF NOT EXISTS documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    document_type_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    file_type VARCHAR(50),
    document_number VARCHAR(100),
    issue_date DATE,
    expiry_date DATE,
    issued_by VARCHAR(200),
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verified_by INT NULL,
    verified_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
echo "   ✓ Documents table updated\n";

// Password resets
$conn->query("
CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Password resets table updated\n";

// Notifications
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Notifications table updated\n";

// Audit logs
$conn->query("
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Audit logs table updated\n";

// Automation logs
$conn->query("
CREATE TABLE IF NOT EXISTS automation_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    target_table VARCHAR(50),
    target_id INT,
    action VARCHAR(100),
    details JSON,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Automation logs table updated\n";

// Settings
$conn->query("
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$conn->query("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES 
    ('company_name', 'PT Indo Ocean Crew Services', 'string', 'Company Name'),
    ('company_email', 'recruitment@indoceancrew.com', 'string', 'Recruitment Email'),
    ('auto_assign_new_applications', 'true', 'boolean', 'Auto assign new applications'),
    ('crewing_round_robin', 'true', 'boolean', 'Enable round-robin assignment')
");
echo "   ✓ Settings table updated\n";

// Interview tables
$conn->query("
CREATE TABLE IF NOT EXISTS interview_question_banks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    department_id INT,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("
CREATE TABLE IF NOT EXISTS interview_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_bank_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_text_id TEXT,
    question_type ENUM('multiple_choice', 'text', 'video') DEFAULT 'text',
    options JSON,
    correct_answer VARCHAR(255),
    expected_keywords JSON,
    min_word_count INT DEFAULT 50,
    time_limit_seconds INT DEFAULT 180,
    max_score INT DEFAULT 100,
    weight DECIMAL(3,2) DEFAULT 1.00,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("
CREATE TABLE IF NOT EXISTS interview_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    question_bank_id INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'expired') DEFAULT 'pending',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    total_score INT,
    ai_recommendation ENUM('pass', 'review', 'fail'),
    admin_override_score INT,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("
CREATE TABLE IF NOT EXISTS interview_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_text TEXT,
    video_path VARCHAR(500),
    video_duration_seconds INT,
    selected_option VARCHAR(255),
    ai_score INT,
    keyword_matches JSON,
    relevance_score INT,
    completeness_score INT,
    time_taken_seconds INT,
    answered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Interview tables updated\n";

// Medical checkups
$conn->query("
CREATE TABLE IF NOT EXISTS medical_checkups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    scheduled_date DATE,
    scheduled_time TIME,
    hospital_name VARCHAR(200),
    hospital_address TEXT,
    status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    result ENUM('fit', 'unfit', 'conditional') NULL,
    result_notes TEXT,
    result_document_path VARCHAR(500),
    valid_until DATE,
    processed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
echo "   ✓ Medical checkups table updated\n";

// Email archive
$conn->query("
CREATE TABLE IF NOT EXISTS email_archive (
    id INT PRIMARY KEY AUTO_INCREMENT,
    to_email VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Email archive table updated\n";
// Email templates (for Email Settings page)
$conn->query("
CREATE TABLE IF NOT EXISTS email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables JSON NULL,
    is_active TINYINT(1) DEFAULT 1,
    is_auto_send TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Add is_auto_send column if table already exists
try { $conn->query("ALTER TABLE email_templates ADD COLUMN is_auto_send TINYINT(1) DEFAULT 0 AFTER is_active"); } catch (mysqli_sql_exception $e) {}

// Insert default email templates
$conn->query("INSERT IGNORE INTO email_templates (name, slug, subject, body, is_auto_send) VALUES 
    ('Application Received', 'application_received', 'Your Application Has Been Received', 'Dear {{applicant_name}},\n\nThank you for applying for {{position}} position. We have received your application and will review it shortly.\n\nBest Regards,\nPT Indo Ocean Crew Services', 1),
    ('Interview Invitation', 'interview_invitation', 'Interview Invitation', 'Dear {{applicant_name}},\n\nWe would like to invite you for an interview for the {{position}} position.\n\nBest Regards,\nPT Indo Ocean Crew Services', 1),
    ('Application Approved', 'application_approved', 'Congratulations! Your Application is Approved', 'Dear {{applicant_name}},\n\nWe are pleased to inform you that your application for {{position}} has been approved.\n\nBest Regards,\nPT Indo Ocean Crew Services', 1),
    ('Application Rejected', 'application_rejected', 'Application Status Update', 'Dear {{applicant_name}},\n\nThank you for your interest in {{position}}. After careful consideration, we regret to inform you that we will not be moving forward with your application at this time.\n\nBest Regards,\nPT Indo Ocean Crew Services', 0)
");
echo "   ✓ Email templates table updated\n";

// Job claim requests (for Requests page)
$conn->query("
CREATE TABLE IF NOT EXISTS job_claim_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    requested_by INT NOT NULL,
    current_handler_id INT NULL,
    reason TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    review_notes TEXT NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Add reviewed columns if table already exists
try { $conn->query("ALTER TABLE job_claim_requests ADD COLUMN reviewed_by INT NULL"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE job_claim_requests ADD COLUMN reviewed_at DATETIME NULL"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE job_claim_requests ADD COLUMN review_notes TEXT NULL"); } catch (mysqli_sql_exception $e) {}
echo "   ✓ Job claim requests table updated\n";

// Archived applications (for Archive page)
$conn->query("
CREATE TABLE IF NOT EXISTS archived_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    vacancy_id INT NOT NULL,
    status_id INT NOT NULL,
    final_status ENUM('hired', 'rejected', 'withdrawn', 'expired') NOT NULL,
    archived_by INT NOT NULL,
    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    application_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Archived applications table updated\n";

// Status change requests (for Requests page)
$conn->query("
CREATE TABLE IF NOT EXISTS status_change_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    requested_by INT NOT NULL,
    from_status_id INT NOT NULL,
    to_status_id INT NOT NULL,
    reason TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    review_notes TEXT NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Add/rename columns if table already exists
try { $conn->query("ALTER TABLE status_change_requests ADD COLUMN from_status_id INT NOT NULL AFTER requested_by"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE status_change_requests ADD COLUMN to_status_id INT NOT NULL AFTER from_status_id"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE status_change_requests ADD COLUMN reviewed_by INT NULL"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE status_change_requests ADD COLUMN reviewed_at DATETIME NULL"); } catch (mysqli_sql_exception $e) {}
try { $conn->query("ALTER TABLE status_change_requests ADD COLUMN review_notes TEXT NULL"); } catch (mysqli_sql_exception $e) {}
echo "   ✓ Status change requests table updated\n";

// Email logs (for Email Settings page)
$conn->query("
CREATE TABLE IF NOT EXISTS email_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NULL,
    to_email VARCHAR(255) NOT NULL,
    to_name VARCHAR(100) NULL,
    subject VARCHAR(500) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
    sent_at DATETIME NULL,
    error_message TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "   ✓ Email logs table updated\n";

// ================================
// 2. CREATE/UPDATE DEFAULT USERS
// ================================

echo "\n2. Creating/Updating Default Users...\n";

$defaultUsers = [
    [11, 'masteradmin@indoceancrew.com', 'Master Administrator'],
    [1, 'admin@indoceancrew.com', 'System Admin'],
    [4, 'leader@indoceancrew.com', 'Default Leader'],
    [5, 'crewing@indoceancrew.com', 'Default Crewing']
];

foreach ($defaultUsers as $user) {
    $conn->query("INSERT IGNORE INTO users (role_id, email, password, full_name, is_active, created_at) 
                  VALUES ({$user[0]}, '{$user[1]}', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '{$user[2]}', 1, NOW())");
}
echo "   ✓ Default users created/updated\n";

// Create profiles for crewing and leader
$conn->query("INSERT IGNORE INTO crewing_profiles (user_id, employee_id, is_pic, max_applications, specialization)
              SELECT id, 'CRW001', 1, 50, 'All Departments' FROM users WHERE email = 'crewing@indoceancrew.com'");
$conn->query("INSERT IGNORE INTO leader_profiles (user_id, employee_id, department)
              SELECT id, 'LDR001', 'Recruitment' FROM users WHERE email = 'leader@indoceancrew.com'");
echo "   ✓ Default profiles created\n";

// ================================
// 3. FINAL VERIFICATION
// ================================

echo "\n3. Verifying Database...\n";

$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}
echo "   ✓ Total tables: " . count($tables) . "\n";

// Check roles
$result = $conn->query("SELECT id, name FROM roles ORDER BY id");
echo "\n   Roles:\n";
while ($row = $result->fetch_assoc()) {
    echo "   - ID {$row['id']}: {$row['name']}\n";
}

// Check users with roles
$result = $conn->query("SELECT u.email, r.name as role FROM users u JOIN roles r ON u.role_id = r.id ORDER BY r.id");
echo "\n   Users:\n";
while ($row = $result->fetch_assoc()) {
    echo "   - {$row['email']} ({$row['role']})\n";
}

$conn->close();

echo "\n========================================\n";
echo "✅ DATABASE UPGRADE COMPLETE!\n";
echo "========================================\n\n";
echo "Login Credentials (password: password):\n";
echo "- masteradmin@indoceancrew.com (Master Admin)\n";
echo "- admin@indoceancrew.com (Admin)\n";
echo "- leader@indoceancrew.com (Leader)\n";
echo "- crewing@indoceancrew.com (Crewing)\n";
echo "</pre>";
?>
