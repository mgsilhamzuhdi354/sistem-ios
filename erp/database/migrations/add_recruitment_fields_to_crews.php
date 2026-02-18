<?php
/**
 * Migration: Add recruitment fields to crews table
 * This enables tracking of crew members imported from recruitment system
 */

// Load environment variables
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Database connection
$db = new mysqli(
    $_ENV['DB_HOST'] ?? 'localhost',
    $_ENV['DB_USERNAME'] ?? 'root',
    $_ENV['DB_PASSWORD'] ?? '',
    $_ENV['DB_DATABASE'] ?? 'erp_indoocean',
    $_ENV['DB_PORT'] ?? 3306
);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "Starting migration: Add recruitment fields to crews table...\n";

try {
    // Check if columns already exist
    $result = $db->query("SHOW COLUMNS FROM crews LIKE 'source'");

    if ($result->num_rows > 0) {
        echo "✓ Migration already applied. Columns exist.\n";
        exit(0);
    }

    // Add new columns
    $sql = "
        ALTER TABLE crews
        ADD COLUMN source ENUM('manual', 'recruitment') DEFAULT 'manual' AFTER status,
        ADD COLUMN candidate_id INT NULL AFTER source,
        ADD COLUMN approved_at TIMESTAMP NULL AFTER candidate_id,
        ADD INDEX idx_source (source),
        ADD INDEX idx_candidate_id (candidate_id)
    ";

    if ($db->query($sql)) {
        echo "✓ Successfully added recruitment fields to crews table:\n";
        echo "  - source (ENUM: manual/recruitment)\n";
        echo "  - candidate_id (INT)\n";
        echo "  - approved_at (TIMESTAMP)\n";
        echo "  - Indexes created for source and candidate_id\n";
    } else {
        throw new Exception("Error adding columns: " . $db->error);
    }

    // Update existing records to have source = 'manual'
    $updateSql = "UPDATE crews SET source = 'manual' WHERE source IS NULL";
    if ($db->query($updateSql)) {
        echo "✓ Updated existing crew records to source='manual'\n";
    }

    echo "\n✅ Migration completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

$db->close();
