-- Migration: Add Referral Code System
-- Date: 2026-03-08
-- Description: Each recruiter gets a unique referral code. Applicants can enter this code to auto-assign themselves.

-- Step 1: Add referral_code to users table
ALTER TABLE users 
ADD COLUMN referral_code VARCHAR(10) NULL UNIQUE AFTER avatar;

-- Step 2: Add referral_code_used to applications table
ALTER TABLE applications 
ADD COLUMN referral_code_used VARCHAR(10) NULL AFTER recruiter_assignment_type;

-- Step 3: Auto-generate referral codes for all existing crewing staff (role_id = 5)
-- Using a procedure to generate unique codes
DELIMITER //
CREATE PROCEDURE generate_referral_codes()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE uid INT;
    DECLARE new_code VARCHAR(10);
    DECLARE cur CURSOR FOR SELECT id FROM users WHERE role_id = 5 AND referral_code IS NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO uid;
        IF done THEN LEAVE read_loop; END IF;
        
        SET new_code = CONCAT('REF-', UPPER(SUBSTRING(MD5(CONCAT(uid, NOW(), RAND())), 1, 5)));
        
        -- Ensure uniqueness
        WHILE EXISTS (SELECT 1 FROM users WHERE referral_code = new_code) DO
            SET new_code = CONCAT('REF-', UPPER(SUBSTRING(MD5(CONCAT(uid, NOW(), RAND())), 1, 5)));
        END WHILE;
        
        UPDATE users SET referral_code = new_code WHERE id = uid;
    END LOOP;
    CLOSE cur;
END //
DELIMITER ;

CALL generate_referral_codes();
DROP PROCEDURE IF EXISTS generate_referral_codes;

-- Step 4: Add index
CREATE INDEX idx_referral_code ON users(referral_code);
CREATE INDEX idx_referral_code_used ON applications(referral_code_used);
