<?php
// Add photo column to crewing_profiles table

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'recruitment_db');

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if column exists
$result = $db->query("SHOW COLUMNS FROM crewing_profiles LIKE 'photo'");

if ($result->num_rows > 0) {
    echo "✓ Column 'photo' already exists in crewing_profiles table\n";
} else {
    echo "Adding 'photo' column to crewing_profiles table...\n";
    
    // Add column
    $sql = "ALTER TABLE crewing_profiles ADD COLUMN photo VARCHAR(255) NULL AFTER max_applications";
    
    if ($db->query($sql)) {
        echo "✓ Column 'photo' added successfully!\n";
    } else {
        echo "✗ Error adding column: " . $db->error . "\n";
    }
}

$db->close();
echo "\nDone!\n";
