-- =====================================================
-- PT Indo Ocean ERP - Phase 1: Authentication Schema
-- Run this SQL in phpMyAdmin or MySQL CLI
-- =====================================================

USE erp_db;

-- =====================================================
-- 1. USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'hr', 'finance', 'manager', 'viewer') DEFAULT 'viewer',
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    password_reset_token VARCHAR(100),
    password_reset_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. ACTIVITY LOGS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. LOGIN HISTORY TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('success', 'failed') DEFAULT 'success',
    failure_reason VARCHAR(100),
    
    INDEX idx_user_id (user_id),
    INDEX idx_login_at (login_at),
    INDEX idx_status (status),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. SESSIONS TABLE (for secure session management)
-- =====================================================
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. ROLE PERMISSIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    can_view BOOLEAN DEFAULT FALSE,
    can_create BOOLEAN DEFAULT FALSE,
    can_edit BOOLEAN DEFAULT FALSE,
    can_delete BOOLEAN DEFAULT FALSE,
    
    UNIQUE KEY unique_role_module (role, module),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. INSERT DEFAULT ADMIN USER
-- Password: admin123 (hashed with password_hash)
-- =====================================================
INSERT INTO users (username, email, password, role, full_name, is_active) VALUES
('admin', 'admin@ptindoocean.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'Super Administrator', TRUE),
('hr_manager', 'hr@ptindoocean.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr', 'HR Manager', TRUE),
('finance', 'finance@ptindoocean.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finance', 'Finance Staff', TRUE)
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- =====================================================
-- 7. INSERT DEFAULT ROLE PERMISSIONS
-- =====================================================
INSERT INTO role_permissions (role, module, can_view, can_create, can_edit, can_delete) VALUES
-- Super Admin - Full Access
('super_admin', 'dashboard', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'contracts', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'vessels', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'clients', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'payroll', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'reports', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'settings', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'users', TRUE, TRUE, TRUE, TRUE),
('super_admin', 'crews', TRUE, TRUE, TRUE, TRUE),

-- Admin - Almost Full Access
('admin', 'dashboard', TRUE, TRUE, TRUE, TRUE),
('admin', 'contracts', TRUE, TRUE, TRUE, TRUE),
('admin', 'vessels', TRUE, TRUE, TRUE, TRUE),
('admin', 'clients', TRUE, TRUE, TRUE, TRUE),
('admin', 'payroll', TRUE, TRUE, TRUE, FALSE),
('admin', 'reports', TRUE, TRUE, TRUE, FALSE),
('admin', 'settings', TRUE, FALSE, FALSE, FALSE),
('admin', 'users', TRUE, TRUE, TRUE, FALSE),
('admin', 'crews', TRUE, TRUE, TRUE, TRUE),

-- HR - Contract & Crew Access
('hr', 'dashboard', TRUE, FALSE, FALSE, FALSE),
('hr', 'contracts', TRUE, TRUE, TRUE, FALSE),
('hr', 'vessels', TRUE, FALSE, FALSE, FALSE),
('hr', 'clients', TRUE, FALSE, FALSE, FALSE),
('hr', 'payroll', TRUE, FALSE, FALSE, FALSE),
('hr', 'reports', TRUE, FALSE, FALSE, FALSE),
('hr', 'crews', TRUE, TRUE, TRUE, FALSE),

-- Finance - Payroll Access
('finance', 'dashboard', TRUE, FALSE, FALSE, FALSE),
('finance', 'contracts', TRUE, FALSE, FALSE, FALSE),
('finance', 'payroll', TRUE, TRUE, TRUE, FALSE),
('finance', 'reports', TRUE, TRUE, FALSE, FALSE),

-- Manager - View Only with Approval
('manager', 'dashboard', TRUE, FALSE, FALSE, FALSE),
('manager', 'contracts', TRUE, FALSE, TRUE, FALSE),
('manager', 'vessels', TRUE, FALSE, FALSE, FALSE),
('manager', 'clients', TRUE, FALSE, FALSE, FALSE),
('manager', 'payroll', TRUE, FALSE, TRUE, FALSE),
('manager', 'reports', TRUE, FALSE, FALSE, FALSE),

-- Viewer - View Only
('viewer', 'dashboard', TRUE, FALSE, FALSE, FALSE),
('viewer', 'contracts', TRUE, FALSE, FALSE, FALSE),
('viewer', 'vessels', TRUE, FALSE, FALSE, FALSE),
('viewer', 'reports', TRUE, FALSE, FALSE, FALSE)
ON DUPLICATE KEY UPDATE can_view = VALUES(can_view);

-- =====================================================
-- DONE! Default login:
-- Username: admin
-- Password: password
-- =====================================================
