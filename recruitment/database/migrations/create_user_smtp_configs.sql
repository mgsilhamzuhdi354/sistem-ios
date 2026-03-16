-- Migration: Create user_smtp_configs table
-- Purpose: Store individual SMTP configurations for each user
-- Date: 2026-02-13

CREATE TABLE IF NOT EXISTS user_smtp_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    smtp_host VARCHAR(255) NOT NULL COMMENT 'SMTP server hostname (e.g., smtp.gmail.com)',
    smtp_port INT DEFAULT 465 COMMENT 'SMTP port (465 for SSL, 587 for TLS)',
    smtp_username VARCHAR(255) NOT NULL COMMENT 'SMTP authentication username',
    smtp_password TEXT NOT NULL COMMENT 'Encrypted SMTP password',
    smtp_encryption ENUM('ssl', 'tls') DEFAULT 'ssl' COMMENT 'Encryption type',
    smtp_from_email VARCHAR(255) NOT NULL COMMENT 'From email address',
    smtp_from_name VARCHAR(255) NOT NULL COMMENT 'From name displayed in emails',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Enable/disable without deleting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id),
    INDEX idx_user_active (user_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Per-user SMTP email configurations';
