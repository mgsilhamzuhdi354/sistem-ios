-- Add image_url column to vessels table for photo upload
ALTER TABLE vessels 
ADD COLUMN image_url VARCHAR(500) NULL AFTER notes
COMMENT 'URL path to vessel photo';
