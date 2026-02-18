<?php
// Test photo upload database functionality
$db = new mysqli('localhost', 'root', '', 'recruitment_db');

echo "=== CHECKING PHOTO UPLOAD DATABASE FUNCTIONALITY ===\n\n";

// 1. Check if document_types has type 7 (Foto)
echo "1. Checking document_types table:\n";
$r = $db->query("SELECT * FROM document_types WHERE id = 7");
if ($row = $r->fetch_assoc()) {
    echo "   ✓ Type 7 exists: {$row['name']}\n";
} else {
    echo "   ✗ Type 7 (Foto) NOT FOUND in document_types!\n";
    echo "   This might be why uploads aren't working.\n";
    
    // Show all document types
    echo "\n   Available document types:\n";
    $all = $db->query("SELECT * FROM document_types ORDER BY id");
    while ($dt = $all->fetch_assoc()) {
        echo "   - ID:{$dt['id']} | {$dt['name']}\n";
    }
}

// 2. Check documents table
echo "\n2. Checking documents table:\n";
$count = $db->query("SELECT COUNT(*) as cnt FROM documents")->fetch_assoc();
echo "   Total documents: {$count['cnt']}\n";

if ($count['cnt'] > 0) {
    echo "   Recent uploads:\n";
    $r = $db->query("SELECT d.*, u.full_name FROM documents d JOIN users u ON d.user_id = u.id ORDER BY d.id DESC LIMIT 5");
    while ($doc = $r->fetch_assoc()) {
        $exists = file_exists(__DIR__ . '/public/' . $doc['file_path']) ? '✓' : '✗';
        echo "   - DocID:{$doc['id']} | User:{$doc['full_name']} | Type:{$doc['document_type_id']} | Path:{$doc['file_path']} | File:{$exists}\n";
    }
}

// 3. Check users with avatars
echo "\n3. Checking users.avatar field:\n";
$r = $db->query("SELECT COUNT(*) as cnt FROM users WHERE avatar IS NOT NULL AND avatar != ''");
$avatarCount = $r->fetch_assoc()['cnt'];
echo "   Users with avatars: {$avatarCount}\n";

if ($avatarCount > 0) {
    echo "   Users with avatar set:\n";
    $r = $db->query("SELECT id, full_name, avatar FROM users WHERE avatar IS NOT NULL AND avatar != '' LIMIT 5");
    while ($u = $r->fetch_assoc()) {
        $exists = file_exists(__DIR__ . '/public/' . $u['avatar']) ? '✓' : '✗';
        echo "   - UserID:{$u['id']} | {$u['full_name']} | {$u['avatar']} | File:{$exists}\n";
    }
}

// 4. Check upload directory permissions
echo "\n4. Checking upload directory:\n";
$uploadDir = __DIR__ . '/public/uploads/documents';
if (is_dir($uploadDir)) {
    echo "   ✓ Directory exists: $uploadDir\n";
    echo "   Writable: " . (is_writable($uploadDir) ? '✓ YES' : '✗ NO') . "\n";
    
    // Count files
    $files = glob($uploadDir . '/*/doc_7_*');
    echo "   Photo files (doc_7_*): " . count($files) . "\n";
    if (count($files) > 0) {
        echo "   Recent photo files:\n";
        $files = array_slice($files, -5);
        foreach ($files as $f) {
            $size = filesize($f);
            $date = date('Y-m-d H:i', filemtime($f));
            echo "   - " . basename(dirname($f)) . "/" . basename($f) . " ({$size} bytes, {$date})\n";
        }
    }
} else {
    echo "   ✗ Directory does not exist!\n";
}

// 5. Test INSERT permission
echo "\n5. Testing database INSERT permission:\n";
try {
    $db->query("START TRANSACTION");
    $stmt = $db->prepare("INSERT INTO documents (user_id, document_type_id, file_name, file_path, original_name, created_at) VALUES (999999, 7, 'test.jpg', 'test/test.jpg', 'test.jpg', NOW())");
    if ($stmt->execute()) {
        $testId = $db->insert_id;
        echo "   ✓ INSERT works (test ID: $testId)\n";
        $db->query("ROLLBACK");
        echo "   ✓ Rolled back test insert\n";
    } else {
        echo "   ✗ INSERT failed: " . $stmt->error . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    $db->query("ROLLBACK");
}

echo "\n=== DIAGNOSIS ===\n";
if ($count['cnt'] == 0) {
    echo "⚠ No documents in database - uploads may not be working.\n";
    echo "  Possible causes:\n";
    echo "  1. Form not submitting files (check browser console)\n";
    echo "  2. PHP upload_max_filesize too small\n";
    echo "  3. Document type 7 doesn't exist in document_types\n";
    echo "  4. Error in handleDocumentUploads() method\n";
} else {
    echo "✓ Documents table has records - upload mechanism is working!\n";
}

$db->close();
