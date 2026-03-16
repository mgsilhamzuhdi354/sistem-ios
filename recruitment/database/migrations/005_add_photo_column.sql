-- Add photo column to crewing_profiles table
ALTER TABLE crewing_profiles 
ADD COLUMN photo VARCHAR(255) NULL 
AFTER max_applications;

-- Add index for faster queries
CREATE INDEX idx_photo ON crewing_profiles(photo);
