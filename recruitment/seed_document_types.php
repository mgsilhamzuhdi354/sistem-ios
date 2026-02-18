<?php
// Seed document_types table with correct columns
$db = new mysqli('localhost', 'root', '', 'recruitment_db');

echo "=== SEEDING DOCUMENT_TYPES TABLE ===\n\n";

$docTypes = [
    ['id' => 1, 'name' => 'CV / Resume', 'name_id' => 'CV / Resume', 'sort_order' => 1],
    ['id' => 2, 'name' => 'Passport', 'name_id' => 'Paspor', 'sort_order' => 2],
    ['id' => 3, 'name' => 'Seaman Book', 'name_id' => 'Buku Pelaut', 'sort_order' => 3],
    ['id' => 4, 'name' => 'COC Certificate', 'name_id' => 'Sertifikat COC', 'sort_order' => 4],
    ['id' => 5, 'name' => 'COP / STCW Certificates', 'name_id' => 'Sertifikat COP / STCW', 'sort_order' => 5],
    ['id' => 6, 'name' => 'Medical Certificate', 'name_id' => 'Sertifikat Medis', 'sort_order' => 6],
    ['id' => 7, 'name' => 'Photo', 'name_id' => 'Foto', 'sort_order' => 7],
    ['id' => 8, 'name' => 'Other Certificates', 'name_id' => 'Sertifikat Lainnya', 'sort_order' => 8],
];

$db->begin_transaction();

try {
    foreach ($docTypes as $dt) {
        $stmt = $db->prepare("
            INSERT INTO document_types (id, name, name_id, is_required, sort_order) 
            VALUES (?, ?, ?, 0, ?)
            ON DUPLICATE KEY UPDATE 
                name = VALUES(name),
                name_id = VALUES(name_id),
                sort_order = VALUES(sort_order)
        ");
        $stmt->bind_param('issi', $dt['id'], $dt['name'], $dt['name_id'], $dt['sort_order']);
        $stmt->execute();
        echo "✓ Inserted: ID {$dt['id']} - {$dt['name']} ({$dt['name_id']})\n";
    }
    
    $db->commit();
    echo "\n✅ SUCCESS! All document types have been seeded.\n";
    
    // Verify
    echo "\n=== VERIFICATION ===\n";
    $r = $db->query("SELECT * FROM document_types ORDER BY sort_order");
    while ($row = $r->fetch_assoc()) {
        echo "ID:{$row['id']} | {$row['name']} ({$row['name_id']})\n";
    }
    
    echo "\n📸 Type 7 (Foto/Photo) is now available!\n";
    echo "🎉 Photo uploads should work now!\n";
    
} catch (Exception $e) {
    $db->rollback();
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

$db->close();
