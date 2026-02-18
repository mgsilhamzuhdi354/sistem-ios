<?php
/**
 * Migration Script: Convert Full URL to Relative Path for Vessel Images
 * Run this once to fix existing vessel image_url values
 */

require_once __DIR__ . '/../../index.php';

echo "=== Vessel Image URL Migration ===\n\n";

// Get all vessels with image_url
$sql = "SELECT id, name, image_url FROM vessels WHERE image_url IS NOT NULL AND image_url != ''";
$result = $db->query($sql);

$updated = 0;
$skipped = 0;

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
            echo "✓ Vessel #{$row['id']} ({$row['name']}): {$oldUrl} → {$newUrl}\n";
            $updated++;
        } else {
            echo "✗ Vessel #{$row['id']} ({$row['name']}): Update failed\n";
        }

        $stmt->close();
    } else {
        echo "⚠ Vessel #{$row['id']} ({$row['name']}): Could not parse URL: {$oldUrl}\n";
    }
}

echo "\n=== Migration Complete ===\n";
echo "Updated: {$updated} vessels\n";
echo "Skipped: {$skipped} vessels (already relative)\n";
?>