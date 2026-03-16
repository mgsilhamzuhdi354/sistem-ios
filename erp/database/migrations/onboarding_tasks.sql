-- Onboarding Tasks Table
-- Tracks onboarding progress for new employees from recruitment

CREATE TABLE IF NOT EXISTS `onboarding_tasks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `candidate_id` INT NULL COMMENT 'Reference to recruitment application ID',
    `employee_id` INT NULL COMMENT 'Reference to ERP employee/crew ID',
    `step_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `status` ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    `assigned_to` INT NULL COMMENT 'User ID of person assigned to complete this task',
    `completed_date` DATETIME NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_employee` (`employee_id`),
    INDEX `idx_candidate` (`candidate_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add columns to applications table in recruitment system (if not exists)
-- Run this in recruitment database
ALTER TABLE `applications` 
ADD COLUMN `is_synced_to_erp` TINYINT(1) DEFAULT 0 COMMENT 'Whether candidate has been synced to ERP',
ADD COLUMN `synced_at` DATETIME NULL COMMENT 'When candidate was synced to ERP',
ADD COLUMN `erp_employee_id` INT NULL COMMENT 'Employee ID in ERP system';

-- Sample onboarding tasks data
INSERT INTO `onboarding_tasks` (`step_name`, `description`, `status`) VALUES
('Document Verification', 'Verify all submitted documents from recruitment system', 'completed'),
('Medical Checkup', 'Complete comprehensive medical examination at designated clinic', 'pending'),
('Contract Signing', 'Review and sign employment contract', 'pending'),
('System Access', 'Create email account and system access credentials', 'pending'),
('Orientation', 'Attend company orientation and policy briefing', 'pending'),
('Equipment Setup', 'Assign laptop, phone, access cards as needed', 'pending');
