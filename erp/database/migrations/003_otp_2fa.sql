-- OTP Codes Table for 2FA Authentication
-- Run this in phpMyAdmin for erp_db

CREATE TABLE IF NOT EXISTS `otp_codes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `type` ENUM('login', 'password_reset', 'verification') DEFAULT 'login',
    `expires_at` DATETIME NOT NULL,
    `attempts` INT DEFAULT 0,
    `used` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_code` (`user_id`, `code`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add 2FA column to users table if not exists
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `two_factor_enabled` TINYINT(1) DEFAULT 1;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_login_ip` VARCHAR(45) NULL;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `last_login_at` DATETIME NULL;
