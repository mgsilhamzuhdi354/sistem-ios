-- Add profile_photo column to applicant_profiles table
ALTER TABLE applicant_profiles 
ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL AFTER user_id;
