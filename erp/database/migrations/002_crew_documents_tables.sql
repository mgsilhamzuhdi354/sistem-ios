-- =====================================================
-- PT Indo Ocean ERP - Phase 2: Crew & Document Tables
-- Run this SQL in phpMyAdmin
-- =====================================================

USE erp_db;

-- =====================================================
-- 1. CREWS TABLE - Data lengkap kru
-- =====================================================
CREATE TABLE IF NOT EXISTS crews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id VARCHAR(20) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    nickname VARCHAR(50),
    gender ENUM('male', 'female') DEFAULT 'male',
    birth_date DATE,
    birth_place VARCHAR(100),
    nationality VARCHAR(50) DEFAULT 'Indonesia',
    religion VARCHAR(50),
    marital_status ENUM('single', 'married', 'divorced', 'widowed') DEFAULT 'single',
    
    -- Contact Information
    email VARCHAR(100),
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    
    -- Emergency Contact
    emergency_name VARCHAR(100),
    emergency_relation VARCHAR(50),
    emergency_phone VARCHAR(20),
    
    -- Banking Information
    bank_name VARCHAR(50),
    bank_account VARCHAR(30),
    bank_holder VARCHAR(100),
    
    -- Professional Information
    current_rank_id INT,
    years_experience INT DEFAULT 0,
    total_sea_time_months INT DEFAULT 0,
    
    -- Files
    photo VARCHAR(255),
    
    -- Status
    status ENUM('available', 'onboard', 'leave', 'blacklisted', 'retired') DEFAULT 'available',
    notes TEXT,
    
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_employee_id (employee_id),
    INDEX idx_status (status),
    INDEX idx_full_name (full_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. CREW SKILLS TABLE - Skill matrix
-- =====================================================
CREATE TABLE IF NOT EXISTS crew_skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crew_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    skill_level ENUM('basic', 'intermediate', 'advanced', 'expert') DEFAULT 'basic',
    certificate_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_crew_id (crew_id),
    FOREIGN KEY (crew_id) REFERENCES crews(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. CREW EXPERIENCES TABLE - Pengalaman kerja
-- =====================================================
CREATE TABLE IF NOT EXISTS crew_experiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crew_id INT NOT NULL,
    vessel_name VARCHAR(100),
    vessel_type VARCHAR(50),
    vessel_flag VARCHAR(50),
    gross_tonnage INT,
    engine_type VARCHAR(50),
    company_name VARCHAR(100),
    rank_position VARCHAR(50),
    start_date DATE,
    end_date DATE,
    reason_leaving VARCHAR(255),
    reference_contact VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_crew_id (crew_id),
    FOREIGN KEY (crew_id) REFERENCES crews(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. CREW DOCUMENTS TABLE - Dokumen kru
-- =====================================================
CREATE TABLE IF NOT EXISTS crew_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crew_id INT NOT NULL,
    document_type ENUM('ktp', 'passport', 'seaman_book', 'coc', 'goc', 'medical', 'bst', 'sat', 'mfa', 'afa', 'pscrb', 'sso', 'other') NOT NULL,
    document_name VARCHAR(100) NOT NULL,
    document_number VARCHAR(50),
    file_path VARCHAR(255),
    file_name VARCHAR(255),
    file_size INT,
    mime_type VARCHAR(50),
    issue_date DATE,
    expiry_date DATE,
    issuing_authority VARCHAR(100),
    issuing_place VARCHAR(100),
    status ENUM('valid', 'expiring_soon', 'expired', 'pending') DEFAULT 'valid',
    verified_by INT,
    verified_at DATETIME,
    notes TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_crew_id (crew_id),
    INDEX idx_document_type (document_type),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_status (status),
    FOREIGN KEY (crew_id) REFERENCES crews(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. DOCUMENT TYPES REFERENCE TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS document_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_id VARCHAR(100),
    description TEXT,
    is_mandatory BOOLEAN DEFAULT FALSE,
    validity_years INT DEFAULT 5,
    reminder_days INT DEFAULT 90,
    category ENUM('identity', 'license', 'certificate', 'medical', 'training', 'other') DEFAULT 'other',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default document types
INSERT INTO document_types (code, name, name_id, is_mandatory, validity_years, reminder_days, category, sort_order) VALUES
('ktp', 'National ID Card', 'KTP', TRUE, 5, 90, 'identity', 1),
('passport', 'Passport', 'Paspor', TRUE, 5, 180, 'identity', 2),
('seaman_book', 'Seaman Book', 'Buku Pelaut', TRUE, 5, 180, 'identity', 3),
('coc', 'Certificate of Competency', 'Sertifikat Keahlian', TRUE, 5, 180, 'license', 4),
('goc', 'General Operator Certificate', 'Sertifikat Radio Operator', FALSE, 5, 90, 'license', 5),
('medical', 'Medical Certificate', 'Surat Keterangan Sehat', TRUE, 2, 60, 'medical', 6),
('bst', 'Basic Safety Training', 'Pelatihan Keselamatan Dasar', TRUE, 5, 90, 'training', 7),
('sat', 'Security Awareness Training', 'Pelatihan Kesadaran Keamanan', FALSE, 5, 90, 'training', 8),
('mfa', 'Medical First Aid', 'Pertolongan Pertama Medis', FALSE, 5, 90, 'training', 9),
('afa', 'Advanced Fire Fighting', 'Penanggulangan Kebakaran Lanjut', FALSE, 5, 90, 'training', 10),
('pscrb', 'Proficiency in Survival Craft', 'Keahlian Sekoci Penolong', FALSE, 5, 90, 'training', 11),
('sso', 'Ship Security Officer', 'Petugas Keamanan Kapal', FALSE, 5, 90, 'training', 12)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- =====================================================
-- 6. Add permissions for crews and documents
-- =====================================================
INSERT INTO role_permissions (role, module, can_view, can_create, can_edit, can_delete) VALUES
('super_admin', 'crews', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'documents', TRUE, TRUE, TRUE, TRUE),
('admin', 'crews', TRUE, TRUE, TRUE, TRUE),
('admin', 'documents', TRUE, TRUE, TRUE, TRUE),
('hr', 'crews', TRUE, TRUE, TRUE, FALSE),
('hr', 'documents', TRUE, TRUE, TRUE, FALSE),
('manager', 'crews', TRUE, FALSE, FALSE, FALSE),
('manager', 'documents', TRUE, FALSE, FALSE, FALSE),
('viewer', 'crews', TRUE, FALSE, FALSE, FALSE)
ON DUPLICATE KEY UPDATE can_view = VALUES(can_view);

-- =====================================================
-- DONE! Tables created:
-- - crews
-- - crew_skills
-- - crew_experiences
-- - crew_documents
-- - document_types
-- =====================================================
