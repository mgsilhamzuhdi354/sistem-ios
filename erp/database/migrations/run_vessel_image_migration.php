<?php
/**
 * Migration: Add image_url column to vessels table
 */

require_once __DIR__ . '/../config/database.php';

$db = getDB Connection();

try {
    // Check if column exists
    $checkSql = "SHOW COLUMNS FROM vessels LIKE 'image_url'";
    $stmt = $db->prepare($checkSql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "✓ Column 'image_url' already exists in vessels table.\n";
    } else {
        // Add column
        $alterSql = "ALTER TABLE vessels ADD COLUMN image_url VARCHAR(500) NULL COMMENT 'URL path to vessel photo'";
        if ($db->query($alterSql)) {
            echo "✓ Successfully added 'image_url' column to vessels table.\n";
        } else {
            echo "✗ Error adding column: " . $db->error . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
}

$db->close();
?>
