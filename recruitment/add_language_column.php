<?php
// Add language column to users table
define('FCPATH', __DIR__ . '/public/');
define('APPPATH', __DIR__ . '/app/');

$dbConfig = include APPPATH . 'Config/Database.php';
$config = $dbConfig['default'];

$db = new mysqli(
    $config['hostname'],
    $config['username'],
    $config['password'],
    $config['database']
);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "Adding language column to users table...\n";

// Check if column exists
$checkSql = "SHOW COLUMNS FROM users LIKE 'language'";
$result = $db->query($checkSql);

if ($result->num_rows == 0) {
    $sql = "ALTER TABLE users ADD COLUMN language VARCHAR(5) DEFAULT 'id' AFTER ui_scale";
    
    if ($db->query($sql)) {
        echo "✓ Language column added successfully!\n";
    } else {
        echo "Error: " . $db->error . "\n";
    }
} else {
    echo "✓ Language column already exists\n";
}

// Set all existing users to Indonesian by default
$updateSql = "UPDATE users SET language = 'id' WHERE language IS NULL OR language = ''";
if ($db->query($updateSql)) {
    $affected = $db->affected_rows;
    echo "✓ Default language set to 'id' for $affected users\n";
}

$db->close();
echo "\nDone!\n";
