-- Create vacancy_shares tracking table
-- Run this in your recruitment database

CREATE TABLE IF NOT EXISTS vacancy_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vacancy_id INT NOT NULL,
    shared_by INT NOT NULL COMMENT 'Crewing user_id',
    share_method ENUM('link', 'whatsapp', 'email', 'qr') DEFAULT 'link',
    share_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_vacancy (vacancy_id),
    INDEX idx_crewing (shared_by),
    FOREIGN KEY (vacancy_id) REFERENCES job_vacancies(id) ON DELETE CASCADE,
    FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
