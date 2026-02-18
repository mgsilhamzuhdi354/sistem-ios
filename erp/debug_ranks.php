<?php
require_once __DIR__ . '/index.php'; // Bootstrap

echo "Dumping Ranks Table:\n";
$ranks = $db->query("SELECT * FROM ranks ORDER BY name");

if ($ranks) {
    foreach ($ranks as $rank) {
        echo "[{$rank['id']}] {$rank['name']}\n";
    }
} else {
    echo "Failed to query ranks. Table might not exist.\n";
    echo "Error: " . $db->error . "\n";
}
