-- Add ERP tracking columns to applications table
-- This allows tracking when applicants are sent to ERP system

ALTER TABLE applications 
ADD COLUMN sent_to_erp_at TIMESTAMP NULL AFTER updated_at,
ADD COLUMN erp_crew_id INT NULL AFTER sent_to_erp_at,
ADD INDEX idx_sent_to_erp (sent_to_erp_at),
ADD INDEX idx_erp_crew_id (erp_crew_id);

-- Verify columns added
DESCRIBE applications;
