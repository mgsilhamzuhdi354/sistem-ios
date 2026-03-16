-- Migration: Add Manual Entry Features
-- Date: 2026-02-12
-- Description: Allow crewing to manually enter candidates with source tracking

-- Step 1: Add entry source tracking to applications
ALTER TABLE applications 
ADD COLUMN entry_source ENUM('online', 'manual', 'import') DEFAULT 'online' AFTER submitted_at,
ADD COLUMN entered_by INT NULL AFTER entry_source;

-- Step 2: Add foreign key for entered_by
ALTER TABLE applications 
ADD CONSTRAINT fk_entered_by 
FOREIGN KEY (entered_by) REFERENCES users(id) ON DELETE SET NULL;

-- Step 3: Add manual entry flags to users
ALTER TABLE users 
ADD COLUMN is_manual_entry TINYINT(1) DEFAULT 0 AFTER is_active,
ADD COLUMN requires_activation TINYINT(1) DEFAULT 0 AFTER is_manual_entry;

-- Step 4: Add indexes for performance
CREATE INDEX idx_entry_source ON applications(entry_source);
CREATE INDEX idx_entered_by ON applications(entered_by);
CREATE INDEX idx_is_manual_entry ON users(is_manual_entry);
