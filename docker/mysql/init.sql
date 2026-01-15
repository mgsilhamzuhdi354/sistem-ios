-- ================================================
-- PT Indoocean - MySQL Initialization Script
-- This script runs when MySQL container starts
-- ================================================

-- Create databases
CREATE DATABASE IF NOT EXISTS erp_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS recruitment_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Grant permissions to application user
GRANT ALL PRIVILEGES ON erp_db.* TO 'indoocean'@'%';
GRANT ALL PRIVILEGES ON recruitment_db.* TO 'indoocean'@'%';

-- Also grant SELECT on erp_db to recruitment (for integration)
-- This allows Recruitment system to INSERT crew data to ERP
GRANT SELECT, INSERT, UPDATE ON erp_db.crews TO 'indoocean'@'%';
GRANT SELECT, INSERT, UPDATE ON erp_db.crew_documents TO 'indoocean'@'%';
GRANT SELECT, INSERT, UPDATE ON erp_db.crew_experiences TO 'indoocean'@'%';
GRANT SELECT, INSERT, UPDATE ON erp_db.crew_skills TO 'indoocean'@'%';

FLUSH PRIVILEGES;

-- ================================================
-- NOTE: 
-- Import your existing data separately using:
-- docker exec -i indoocean-mysql mysql -u indoocean -p erp_db < erp_backup.sql
-- docker exec -i indoocean-mysql mysql -u indoocean -p recruitment_db < recruitment_backup.sql
-- ================================================
