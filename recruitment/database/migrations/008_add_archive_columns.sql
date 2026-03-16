-- Migration: Add archive columns to applications table
-- Date: 2026-02-19

ALTER TABLE applications ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER admin_notes;
ALTER TABLE applications ADD COLUMN archived_at DATETIME NULL AFTER is_archived;
ALTER TABLE applications ADD COLUMN archive_notes TEXT NULL AFTER archived_at;
CREATE INDEX idx_is_archived ON applications(is_archived);
