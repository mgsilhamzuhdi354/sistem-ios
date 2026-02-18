<?php
// Add profile_photo column to applicant_profiles
$db = new mysqli('localhost', 'root', '', 'recruitment_db');
if ($db->connect_error) die('Connection failed: ' . $db->connect_error);

// Check if column already exists
$result = $db->query("SHOW COLUMNS FROM applicant_profiles LIKE 'profile_photo'");
if ($result->num_rows > 0) {
    echo"Column 'profile_photo' already exists.\n";
} else {
    // Add column
    $sql = "ALTER TABLE applicant_profiles ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL AFTER user_id";
    if ($db->query($sql)) {
        echo "Column 'profile_photo' added successfully.\n";
    } else {
        echo "Error adding column: " . $db->error . "\n";
    }
}

$db->close();
echo "Done!\n";
