<?php
/**
 * Standalone Migration Script: Convert Full URL to Relative Path for Vessel Images
 * Run this once via browser to fix existing vessel image_url values
 */

// Database configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'erp_db';

// Connect to database
$db = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<h2>Vessel Image URL Migration</h2>";
echo "<pre>";

// Get all vessels with image_url
$sql = "SELECT id, name, image_url FROM vessels WHERE image_url IS NOT NULL AND image_url != ''";
$result = $db->query($sql);

if (!$result) {
    die("Query failed: " . $db->error);
}

$updated = 0;
$skipped = 0;

echo "\nProcessing " . $result->num_rows . " vessel(s) with images...\n\n";

while ($row = $result->fetch_assoc()) {
    $oldUrl = $row['image_url'];

    // Skip if already a relative path (doesn't start with http)
    if (!str_starts_with($oldUrl, 'http')) {
        echo "✓ Vessel #{$row['id']} ({$row['name']}): Already relative path\n";
        $skipped++;
        continue;
    }

    // Extract path after 'public/uploads/'
    if (preg_match('#public/uploads/vessels/(.+)$#', $oldUrl, $matches)) {
        $newUrl = 'public/uploads/vessels/' . $matches[1];

        $updateSql = "UPDATE vessels SET image_url = ? WHERE id = ?";
        $stmt = $db->prepare($updateSql);
        $stmt->bind_param('si', $newUrl, $row['id']);

        if ($stmt->execute()) {
            echo "✓ Vessel #{$row['id']} ({$row['name']}): Updated\n";
            echo "  OLD: {$oldUrl}\n";
            echo "  NEW: {$newUrl}\n\n";
            $updated++;
        } else {
            echo "✗ Vessel #{$row['id']} ({$row['name']}): Update failed - " . $stmt->error . "\n\n";
        }

        $stmt->close();
    } else {
        echo "⚠ Vessel #{$row['id']} ({$row['name']}): Could not parse URL: {$oldUrl}\n\n";
    }
}

echo "\n=== Migration Complete ===\n";
echo "Updated: {$updated} vessel(s)\n";
echo "Skipped: {$skipped} vessel(s) (already relative)\n";
echo "</pre>";

$db->close();
?>