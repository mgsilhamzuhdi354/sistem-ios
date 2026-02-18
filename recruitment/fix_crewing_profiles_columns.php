<?php
// Add missing columns to crewing_profiles table

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'recruitment_db');

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "Checking and fixing crewing_profiles columns...\n\n";

// Check and add created_at column
$result = $db->query("SHOW COLUMNS FROM crewing_profiles LIKE 'created_at'");
if ($result->num_rows == 0) {
    echo "Adding 'created_at' column...\n";
    $db->query("ALTER TABLE crewing_profiles ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER photo");
    echo "✓ 'created_at' column added!\n";
} else {
    echo "✓ 'created_at' column already exists\n";
}

// Check and add updated_at column
$result = $db->query("SHOW COLUMNS FROM crewing_profiles LIKE 'updated_at'");
if ($result->num_rows == 0) {
    echo "Adding 'updated_at' column...\n";
    $db->query("ALTER TABLE crewing_profiles ADD COLUMN updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
    echo "✓ 'updated_at' column added!\n";
} else {
    echo "✓ 'updated_at' column already exists\n";
}

$db->close();
echo "\nAll columns are ready!\n";
