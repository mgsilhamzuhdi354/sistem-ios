-- Migration: Add Pipeline Status Tracking
-- Date: 2026-03-10
-- Description: Add Admin Review, Processing, On Board statuses for pipeline auto-sync

-- Step 1: Add new statuses (check if they exist first)
INSERT INTO application_statuses (id, name, name_id, color, sort_order, is_active) VALUES 
(9, 'Admin Review', 'Review Admin', '#3498db', 9, 1),
(10, 'Processing', 'Diproses', '#f39c12', 10, 1),
(11, 'On Board', 'On Board', '#2ecc71', 11, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), color = VALUES(color), is_active = 1;

-- Step 2: Ensure applications has checklist_progress and erp tracking columns
ALTER TABLE applications 
ADD COLUMN IF NOT EXISTS sent_to_erp_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS erp_crew_id INT NULL,
ADD COLUMN IF NOT EXISTS checklist_progress TINYINT DEFAULT 0,
ADD COLUMN IF NOT EXISTS checklist_updated_at DATETIME NULL;
