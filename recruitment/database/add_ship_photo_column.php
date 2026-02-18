<?php
/**
 * Add ship_photo column to job_vacancies table
 * Run once: php add_ship_photo_column.php
 */

require_once __DIR__ . '/../public/index.php';

$db = getDB();

// Add ship_photo column
$sql = "ALTER TABLE job_vacancies ADD COLUMN ship_photo VARCHAR(500) NULL AFTER vessel_type_id";

if ($db->query($sql)) {
    echo "✅ Column 'ship_photo' added successfully!\n";
} else {
    if (strpos($db->error, "Duplicate column name") !== false) {
        echo "ℹ️  Column 'ship_photo' already exists.\n";
    } else {
        echo "❌ Error: " . $db->error . "\n";
    }
}

// Show current enum values for status
$result = $db->query("SHOW COLUMNS FROM job_vacancies LIKE 'status'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "\n📋 Current status ENUM values:\n";
    echo $row['Type'] . "\n";
}

$db->close();
