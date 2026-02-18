<?php
// Database Connection Checker
// Upload this file to your server root (public_html) and run it
// e.g. http://your-domain.com/db_check.php

header('Content-Type: text/plain');

echo "=== Database Connection Check ===\n\n";

// 1. Check for .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "[OK] Found .env file at $envFile\n";
    
    // Simple env parser
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
} else {
    echo "[WARNING] .env file NOT FOUND at $envFile\n";
    echo "Using default/hardcoded values if available in Config.\n";
}

echo "\n--- Attempting Connection ---\n";

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$db   = $_ENV['DB_DATABASE'] ?? 'erp_db'; 

echo "Host: $host\n";
echo "User: $user\n";
echo "Pass: " . (empty($pass) ? '(empty)' : '********') . "\n";
echo "DB  : $db\n\n";

try {
    $mysqli = new mysqli($host, $user, $pass, $db);
    
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    
    echo "[SUCCESS] Connected to database successfully!\n";
    echo "Server Info: " . $mysqli->server_info . "\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "[ERROR] Connection Failed:\n";
    echo $e->getMessage() . "\n";
    
    echo "\nPossible Solutions:\n";
    echo "1. Check if DB_HOST is correct (sometimes connection strings like 'localhost:/tmp/mysql.sock' are needed)\n";
    echo "2. Verify DB_USERNAME and DB_PASSWORD\n";
    echo "3. Ensure the database '$db' actually exists via phpMyAdmin\n";
    echo "4. Check if user '$user' has permissions for database '$db'\n";
}

echo "\n=== End Check ===";
?>
