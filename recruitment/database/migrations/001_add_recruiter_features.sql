-- Migration: Add Recruiter Selection Features
-- Date: 2026-02-12
-- Description: Add photo, bio, specialization to crewing profiles and preferred recruiter to applications

-- Step 1: Add fields to crewing_profiles
ALTER TABLE crewing_profiles 
ADD COLUMN photo VARCHAR(255) NULL AFTER employee_id,
ADD COLUMN bio TEXT NULL AFTER photo,
ADD COLUMN specialization VARCHAR(100) NULL AFTER bio;

-- Step 2: Add fields to applications
ALTER TABLE applications 
ADD COLUMN preferred_recruiter_id INT NULL AFTER vacancy_id,
ADD COLUMN recruiter_assignment_type ENUM('manual', 'random', 'auto', 'preferred') DEFAULT 'auto' AFTER preferred_recruiter_id;

-- Step 3: Add foreign key (if not exists)
ALTER TABLE applications 
ADD CONSTRAINT fk_preferred_recruiter 
FOREIGN KEY (preferred_recruiter_id) REFERENCES users(id) ON DELETE SET NULL;

-- Step 4: Simplify workflow - add is_active to statuses
ALTER TABLE application_statuses 
ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER sort_order;

-- Step 5: Deactivate statuses after Approved (status_id > 6)
UPDATE application_statuses SET is_active = 0 WHERE id > 6;

-- Step 6: Add index for performance
CREATE INDEX idx_preferred_recruiter ON applications(preferred_recruiter_id);
CREATE INDEX idx_recruiter_assignment_type ON applications(recruiter_assignment_type);
