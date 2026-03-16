-- Migration: Add ship_photo column to job_vacancies table
-- Created: 2026-02-13

-- Add ship_photo column to store ship photo path
ALTER TABLE job_vacancies 
ADD COLUMN ship_photo VARCHAR(500) NULL AFTER vessel_type_id;

-- Add comment to column
ALTER TABLE job_vacancies 
MODIFY COLUMN ship_photo VARCHAR(500) NULL COMMENT 'Path to ship photo image';
