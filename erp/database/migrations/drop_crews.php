<?php
/**
 * Drop crews table and retry migration
 * This fixes foreign key issues
 */

$conn = new mysqli('localhost', 'root', '', 'erp_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Dropping existing crews table...</h2>";

// Drop table if exists
$conn->query("DROP TABLE IF EXISTS crews");

echo "<p style='color: green;'>✓ Table dropped</p>";
echo "<p><a href='run_crew_migration.php'>→ Click here to run migration again</a></p>";

$conn->close();
?>