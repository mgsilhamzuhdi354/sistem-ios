-- =============================================
-- PT Indo Ocean - ERP System
-- Crew Management Database Migration
-- Created: 2026-01-31
-- =============================================

-- Disable foreign key checks during migration
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- TABLE 1: crews (Main crew database)
-- =============================================
CREATE TABLE IF NOT EXISTS `crews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Format: IO20260001',
  
  -- Personal Information
  `full_name` VARCHAR(100) NOT NULL,
  `nickname` VARCHAR(50) DEFAULT NULL,
  `gender` ENUM('male', 'female') NOT NULL,
  `birth_date` DATE DEFAULT NULL,
  `birth_place` VARCHAR(100) DEFAULT NULL,
  `nationality` VARCHAR(50) DEFAULT 'Indonesia',
  `religion` VARCHAR(30) DEFAULT NULL,
  `marital_status` ENUM('single', 'married', 'divorced', 'widowed') DEFAULT 'single',
  
  -- Contact Information
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `province` VARCHAR(100) DEFAULT NULL,
  `postal_code` VARCHAR(10) DEFAULT NULL,
  
  -- Emergency Contact
  `emergency_name` VARCHAR(100) DEFAULT NULL,
  `emergency_relation` VARCHAR(50) DEFAULT NULL,
  `emergency_phone` VARCHAR(20) DEFAULT NULL,
  
  -- Banking Information
  `bank_name` VARCHAR(100) DEFAULT NULL,
  `bank_account` VARCHAR(50) DEFAULT NULL,
  `bank_holder` VARCHAR(100) DEFAULT NULL,
  
  -- Professional Information
  `current_rank_id` INT DEFAULT NULL COMMENT 'FK to ranks.id',
  `years_experience` INT DEFAULT 0,
  `total_sea_time_months` INT DEFAULT 0,
  
  -- Status & Media
  `status` ENUM('available', 'onboard', 'standby', 'terminated') DEFAULT 'available',
  `photo` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  
  -- Tracking
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT DEFAULT NULL COMMENT 'FK to users.id',
  
  -- Indexes
  INDEX `idx_employee_id` (`employee_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_rank` (`current_rank_id`),
  INDEX `idx_full_name` (`full_name`),
  
  -- Foreign Keys
  CONSTRAINT `fk_crews_rank` FOREIGN KEY (`current_rank_id`) 
    REFERENCES `ranks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_crews_creator` FOREIGN KEY (`created_by`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE 2: crew_skills
-- =============================================
CREATE TABLE IF NOT EXISTS `crew_skills` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `crew_id` INT UNSIGNED NOT NULL COMMENT 'FK to crews.id',
  `skill_name` VARCHAR(100) NOT NULL COMMENT 'e.g. Navigation, Engineering, Safety',
  `skill_level` ENUM('basic', 'intermediate', 'advanced', 'expert') DEFAULT 'basic',
  `certificate_id` VARCHAR(50) DEFAULT NULL COMMENT 'Certificate number if applicable',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_crew_id` (`crew_id`),
  INDEX `idx_skill_name` (`skill_name`),
  
  CONSTRAINT `fk_crew_skills_crew` FOREIGN KEY (`crew_id`) 
    REFERENCES `crews` (`id`) ON DELETE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE 3: crew_experiences
-- =============================================
CREATE TABLE IF NOT EXISTS `crew_experiences` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `crew_id` INT UNSIGNED NOT NULL COMMENT 'FK to crews.id',
  
  -- Vessel Information
  `vessel_name` VARCHAR(150) NOT NULL,
  `vessel_type` VARCHAR(100) DEFAULT NULL COMMENT 'e.g. Tanker, Container, Bulk Carrier',
  `vessel_flag` VARCHAR(50) DEFAULT NULL,
  `gross_tonnage` INT DEFAULT NULL,
  `engine_type` VARCHAR(100) DEFAULT NULL,
  
  -- Employment Details
  `company_name` VARCHAR(150) DEFAULT NULL,
  `rank_position` VARCHAR(100) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `reason_leaving` TEXT DEFAULT NULL,
  `reference_contact` VARCHAR(150) DEFAULT NULL,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_crew_id` (`crew_id`),
  INDEX `idx_dates` (`start_date`, `end_date`),
  
  CONSTRAINT `fk_crew_experiences_crew` FOREIGN KEY (`crew_id`) 
    REFERENCES `crews` (`id`) ON DELETE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE 4: crew_documents
-- =============================================
CREATE TABLE IF NOT EXISTS `crew_documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `crew_id` INT UNSIGNED NOT NULL COMMENT 'FK to crews.id',
  `document_type` VARCHAR(20) NOT NULL COMMENT 'FK to document_types.code',
  `document_name` VARCHAR(150) NOT NULL,
  `document_number` VARCHAR(100) DEFAULT NULL,
  
  -- File Information
  `file_path` VARCHAR(255) DEFAULT NULL,
  `file_name` VARCHAR(255) DEFAULT NULL,
  `file_size` INT DEFAULT NULL COMMENT 'in bytes',
  `mime_type` VARCHAR(100) DEFAULT NULL,
  
  -- Document Details
  `issue_date` DATE DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `issuing_authority` VARCHAR(150) DEFAULT NULL,
  `issuing_place` VARCHAR(100) DEFAULT NULL,
  
  -- Status & Verification
  `status` ENUM('valid', 'expiring_soon', 'expired', 'pending') DEFAULT 'pending',
  `verified_by` INT DEFAULT NULL COMMENT 'FK to users.id',
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  
  -- Tracking
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uploaded_by` INT DEFAULT NULL COMMENT 'FK to users.id',
  
  INDEX `idx_crew_id` (`crew_id`),
  INDEX `idx_document_type` (`document_type`),
  INDEX `idx_expiry_date` (`expiry_date`),
  INDEX `idx_status` (`status`),
  
  CONSTRAINT `fk_crew_documents_crew` FOREIGN KEY (`crew_id`) 
    REFERENCES `crews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_crew_documents_verifier` FOREIGN KEY (`verified_by`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_crew_documents_uploader` FOREIGN KEY (`uploaded_by`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLE 5: document_types (Master Data)
-- =============================================
CREATE TABLE IF NOT EXISTS `document_types` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'e.g. PASSPORT, COC, BST',
  `name` VARCHAR(100) NOT NULL COMMENT 'English name',
  `name_id` VARCHAR(100) NOT NULL COMMENT 'Indonesian name',
  `description` TEXT DEFAULT NULL,
  `is_mandatory` TINYINT(1) DEFAULT 0 COMMENT '1 = mandatory for seafarers',
  `validity_years` INT DEFAULT NULL COMMENT 'Standard validity period in years',
  `reminder_days` INT DEFAULT 90 COMMENT 'Days before expiry to send reminder',
  `category` ENUM('identity', 'seafarer', 'medical', 'training', 'visa', 'other') DEFAULT 'other',
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_code` (`code`),
  INDEX `idx_category` (`category`),
  INDEX `idx_sort_order` (`sort_order`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SEED DATA: Document Types
-- Indonesian Maritime Document Standards
-- =============================================

INSERT INTO `document_types` (`code`, `name`, `name_id`, `description`, `is_mandatory`, `validity_years`, `reminder_days`, `category`, `sort_order`) VALUES
-- Identity Documents
('KTP', 'National ID Card', 'KTP', 'Kartu Tanda Penduduk Indonesia', 1, 5, 180, 'identity', 10),
('PASSPORT', 'Passport', 'Paspor', 'International passport', 1, 5, 180, 'identity', 20),
('KK', 'Family Card', 'Kartu Keluarga', 'Kartu Keluarga Indonesia', 0, NULL, NULL, 'identity', 30),

-- Seafarer Documents
('SEAMAN_BOOK', 'Seaman Book', 'Buku Pelaut', 'Seafarer identification and discharge book', 1, 5, 180, 'seafarer', 100),
('COC', 'Certificate of Competency', 'Sertifikat Kecakapan (COC)', 'Professional competency certificate for ship officers', 1, 5, 180, 'seafarer', 110),
('COE', 'Certificate of Endorsement', 'Sertifikat Endorsement (COE)', 'Indonesian endorsement of COC', 1, 5, 180, 'seafarer', 120),
('SID', 'Seafarer Identity Document', 'SID', 'International seafarer identity document', 1, 5, 180, 'seafarer', 130),

-- Medical Certificates
('MEDICAL', 'Medical Certificate', 'Sertifikat Kesehatan', 'Seafarer medical fitness certificate', 1, 1, 60, 'medical', 200),
('YELLOW_FEVER', 'Yellow Fever Vaccination', 'Vaksinasi Yellow Fever', 'Yellow fever vaccination certificate', 0, 10, 365, 'medical', 210),
('COVID_VAX', 'COVID-19 Vaccination', 'Vaksinasi COVID-19', 'COVID-19 vaccination certificate', 1, NULL, NULL, 'medical', 220),

-- Training Certificates
('BST', 'Basic Safety Training', 'BST', 'STCW Basic Safety Training', 1, 5, 180, 'training', 300),
('AFF', 'Advanced Fire Fighting', 'AFF', 'STCW Advanced Fire Fighting', 0, 5, 180, 'training', 310),
('PST', 'Proficiency in Survival Craft', 'PST', 'STCW Proficiency in Survival Craft and Rescue Boats', 0, 5, 180, 'training', 320),
('MFA', 'Medical First Aid', 'MFA/MEFA', 'STCW Medical First Aid', 0, 5, 180, 'training', 330),
('GMDSS', 'GMDSS Radio Operator', 'GMDSS', 'Global Maritime Distress Safety System operator certificate', 0, 5, 180, 'training', 340),
('RADAR', 'Radar Navigation', 'Radar Navigation', 'Radar observer and navigation', 0, 5, 180, 'training', 350),
('ARPA', 'ARPA Training', 'ARPA', 'Automatic Radar Plotting Aid', 0, 5, 180, 'training', 360),
('ECDIS', 'ECDIS Training', 'ECDIS', 'Electronic Chart Display and Information System', 0, 5, 180, 'training', 370),
('SSO', 'Ship Security Officer', 'SSO', 'Ship Security Officer training', 0, 5, 180, 'training', 380),
('HAZMAT', 'Dangerous Goods Training', 'Dangerous Goods', 'Handling and transport of dangerous goods', 0, 5, 180, 'training', 390),

-- Visa Documents
('US_VISA', 'US C1/D Visa', 'Visa Amerika (C1/D)', 'United States C1/D seafarer visa', 0, 10, 180, 'visa', 400),
('SCHENGEN', 'Schengen Visa', 'Visa Schengen', 'Schengen area visa', 0, 5, 90, 'visa', 410),
('SG_VISA', 'Singapore Visa', 'Visa Singapura', 'Singapore visa', 0, 2, 90, 'visa', 420),
('UK_VISA', 'UK Visa', 'Visa Inggris', 'United Kingdom visa', 0, 10, 180, 'visa', 430);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- Migration Complete
-- Tables created: crews, crew_skills, crew_experiences, crew_documents, document_types
-- Total document types seeded: 22
-- =============================================
