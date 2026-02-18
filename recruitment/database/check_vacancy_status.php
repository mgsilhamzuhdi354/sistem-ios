<?php
/**
 * Check actual ENUM values for job_vacancies.status column
 */

require_once __DIR__ . '/../public/index.php';

$db = getDB();

// Get column definition
$result = $db->query("SHOW COLUMNS FROM job_vacancies LIKE 'status'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "📋 Current status column definition:\n";
    echo "Type: " . $row['Type'] . "\n";
    echo "Default: " . ($row['Default'] ?: 'NULL') . "\n\n";
}

// Get all vacancies with their current status
$vacancies = $db->query("SELECT id, title, status FROM job_vacancies LIMIT 5");
echo "📌 Sample vacancies:\n";
while ($vac = $vacancies->fetch_assoc()) {
    echo "ID {$vac['id']}: '{$vac['status']}' - {$vac['title']}\n";
}

echo "\n✅ Valid ENUM values: 'draft', 'published', 'closed'\n";

$db->close();
