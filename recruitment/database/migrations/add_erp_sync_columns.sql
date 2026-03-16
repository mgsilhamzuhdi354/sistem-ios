-- Add ERP sync tracking to recruitment applications table
USE recruitment_db;

ALTER TABLE applications
ADD COLUMN is_synced_to_erp TINYINT(1) DEFAULT 0 COMMENT 'Whether candidate has been imported to ERP' AFTER reviewed_at,
ADD COLUMN synced_at DATETIME NULL COMMENT 'When candidate was synced to ERP' AFTER is_synced_to_erp,
ADD COLUMN erp_employee_id VARCHAR(50) NULL COMMENT 'Employee ID in ERP system' AFTER synced_at;

CREATE INDEX idx_is_synced ON applications(is_synced_to_erp);
