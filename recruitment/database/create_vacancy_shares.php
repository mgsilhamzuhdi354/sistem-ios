<?php
/**
 * Create vacancy_shares table for tracking share functionality
 * Run this file once: php create_vacancy_shares.php
 */

require_once __DIR__ . '/../public/index.php';

$db = getDB();

$sql = "CREATE TABLE IF NOT EXISTS vacancy_shares (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($db->query($sql)) {
    echo "✅ Table 'vacancy_shares' created successfully!\n";
} else {
    echo "❌ Error creating table: " . $db->error . "\n";
}

$db->close();
