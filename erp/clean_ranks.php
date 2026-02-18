<?php
require_once __DIR__ . '/index.php';

echo "Cleaning Duplicate Ranks...\n";

// 1. Get all duplicates
$sql = "SELECT name, COUNT(*) as count, GROUP_CONCAT(id) as ids 
        FROM ranks 
        GROUP BY name 
        HAVING count > 1";

$duplicates = $db->query($sql);

if (empty($duplicates)) {
    echo "No duplicates found.\n";
    exit;
}

foreach ($duplicates as $dup) {
    echo "Processing '{$dup['name']}' (Count: {$dup['count']})...\n";
    
    $ids = explode(',', $dup['ids']);
    $keepId = $ids[0]; // Keep the first ID (usually the oldest)
    
    // Process duplicates
    for ($i = 1; $i < count($ids); $i++) {
        $deleteId = $ids[$i];
        
        // Update related tables to point to $keepId instead of $deleteId
        // Update contracts
        $db->query("UPDATE contracts SET rank_id = ? WHERE rank_id = ?", [$keepId, $deleteId]);
        
        // Update payroll_items
        $db->query("UPDATE payroll_items SET rank_name = (SELECT name FROM ranks WHERE id = ?) WHERE rank_name = (SELECT name FROM ranks WHERE id = ?)", [$keepId, $deleteId]);
        
        // Delete the duplicate rank
        $db->query("DELETE FROM ranks WHERE id = ?", [$deleteId]);
        
        echo "  - Deleted ID {$deleteId} (merged into {$keepId})\n";
    }
}

echo "Done. All duplicates merged.\n";
