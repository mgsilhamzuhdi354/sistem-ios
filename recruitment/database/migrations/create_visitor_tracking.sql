-- =============================================
-- Phase 4: Monitoring Center - Database Schema
-- Purpose: Track visitor analytics for company profile
-- =============================================

-- Create visitor_logs table
CREATE TABLE IF NOT EXISTS visitor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    referrer_url VARCHAR(500),
    page_visited VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    city VARCHAR(100),
    device_type ENUM('desktop', 'mobile', 'tablet') DEFAULT 'desktop',
    visited_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    session_id VARCHAR(100),
    
    -- Indexes for performance
    INDEX idx_visited_at (visited_at),
    INDEX idx_page (page_visited),
    INDEX idx_session (session_id),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add analytics indexes to applications table for faster queries
ALTER TABLE applications 
    ADD INDEX IF NOT EXISTS idx_status_created (status_id, created_at),
    ADD INDEX IF NOT EXISTS idx_vacancy_created (vacancy_id, created_at);

-- Success message
SELECT 'Visitor tracking tables created successfully!' AS message;
