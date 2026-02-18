<?php
/**
 * Quick Migration: Add image_url column to vessels table
 * Access via: http://127.0.0.1/PT_indoocean/erp/migrate_vessel_image.php
 * DELETE THIS FILE AFTER RUNNING!
 */

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'erp_db';  // Corrected database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Vessel Image URL Migration</h2>";
echo "<hr>";

// Check if column exists
$checkSql = "SHOW COLUMNS FROM vessels LIKE 'image_url'";
$result = $conn->query($checkSql);

if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Column 'image_url' already exists in vessels table.</p>";
} else {
    // Add column
    $alterSql = "ALTER TABLE vessels ADD COLUMN image_url VARCHAR(500) NULL COMMENT 'URL path to vessel photo'";

    if ($conn->query($alterSql) === TRUE) {
        echo "<p style='color: green;'>✓ <strong>SUCCESS!</strong> Column 'image_url' has been added to vessels table.</p>";
        echo "<p>You can now upload vessel photos!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding column: " . $conn->error . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Delete this file (migrate_vessel_image.php) for security</li>";
echo "<li>Go to <a href='vessels'>Vessel Management</a></li>";
echo "<li>Click Edit on any vessel</li>";
echo "<li>Upload a photo and save</li>";
echo "</ol>";

$conn->close();
?>