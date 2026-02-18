<?php
/**
 * Fix recruiter_assignment_type column and ensure applications table has all needed columns
 */

$host = 'localhost';
$dbname = 'recruitment_db';
$user = 'root';
$pass = '';

$db = new mysqli($host, $user, $pass, $dbname);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<h3>Fixing applications table columns...</h3>";

$fixes = [
    // Ensure preferred_recruiter_id column exists
    "ALTER TABLE applications ADD COLUMN preferred_recruiter_id INT(11) NULL AFTER current_crewing_id" 
        => "preferred_recruiter_id column",
    
    // Change recruiter_assignment_type to VARCHAR to avoid ENUM truncation
    "ALTER TABLE applications MODIFY COLUMN recruiter_assignment_type VARCHAR(20) DEFAULT 'auto'" 
        => "recruiter_assignment_type → VARCHAR(20)",
    
    // Add column if it doesn't exist at all
    "ALTER TABLE applications ADD COLUMN recruiter_assignment_type VARCHAR(20) DEFAULT 'auto' AFTER preferred_recruiter_id"
        => "recruiter_assignment_type column (if missing)",
];

foreach ($fixes as $sql => $desc) {
    if ($db->query($sql)) {
        echo "✅ Fixed: $desc<br>";
    } else {
        // Check if it's just a duplicate column error (already exists)
        if (strpos($db->error, 'Duplicate column') !== false) {
            echo "⏩ Already exists: $desc<br>";
        } else {
            echo "⚠️ Note ($desc): " . $db->error . "<br>";
        }
    }
}

// Also ensure crewing_ratings table exists
$db->query("
    CREATE TABLE IF NOT EXISTS crewing_ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crewing_id INT NOT NULL,
        applicant_id INT NULL,
        application_id INT NULL,
        rating DECIMAL(3,1) DEFAULT 0,
        comment TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
echo "✅ crewing_ratings table ensured<br>";

// Ensure application_assignments table exists
$db->query("
    CREATE TABLE IF NOT EXISTS application_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_id INT NOT NULL,
        assigned_to INT NOT NULL,
        assigned_by INT NULL,
        notes TEXT NULL,
        status VARCHAR(20) DEFAULT 'active',
        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
echo "✅ application_assignments table ensured<br>";

echo "<br><h3>✅ All fixes applied! <a href='/recruitment/public/jobs/2'>Go back to test</a></h3>";

$db->close();
