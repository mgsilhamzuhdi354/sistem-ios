-- Migration: Add KTP Number (NIK) to applicant_profiles
-- Date: 2026-02-19
-- Description: Allow KTP-based auto-fill lookup for manual entry

ALTER TABLE applicant_profiles 
ADD COLUMN ktp_number VARCHAR(16) NULL AFTER user_id;

-- Index for fast lookup
CREATE INDEX idx_ktp_number ON applicant_profiles(ktp_number);
