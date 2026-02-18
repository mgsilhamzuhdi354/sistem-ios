<?php
// Check profile_photo value in database
$db = new mysqli('localhost', 'root', '', 'recruitment_db');
if ($db->connect_error) die('Connection failed: ' . $db->connect_error);

$result = $db->query("SELECT id, user_id, profile_photo FROM applicant_profiles WHERE user_id = 58");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Database value:\n";
    print_r($row);
    echo "\n\n";
    
    if (!empty($row['profile_photo'])) {
        echo "Profile photo: " . $row['profile_photo'] . "\n";
        $filePath = __DIR__ . '/public/uploads/profiles/' . $row['profile_photo'];
        echo "File path: $filePath\n";
        echo "File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "\n";
    } else {
        echo "No profile_photo in database\n";
    }
} else {
    echo "Query failed: " . $db->error;
}

$db->close();
