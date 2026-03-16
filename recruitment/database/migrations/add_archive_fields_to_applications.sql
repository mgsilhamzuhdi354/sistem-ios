-- Migration: Add Archive Fields to Applications Table
-- Purpose: Enable archiving of applications with restore and permanent delete
-- Date: 2026-02-14

ALTER TABLE applications 
ADD COLUMN is_archived TINYINT(1) DEFAULT 0 COMMENT 'Whether application is archived',
ADD COLUMN archived_at TIMESTAMP NULL COMMENT 'When application was archived',
ADD COLUMN archived_by INT NULL COMMENT 'User ID who archived this application',
ADD INDEX idx_archived (is_archived, archived_at);

-- Add foreign key for archived_by
ALTER TABLE applications
ADD CONSTRAINT fk_archived_by 
FOREIGN KEY (archived_by) REFERENCES users(id) ON DELETE SET NULL;
