<?php
/**
 * Migration Script: Add ERP Sync Columns
 * Run this file once via browser to execute migration
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'pt_indoocean_recruitment';

try {
    // Connect to database
    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    echo "<h2>Running Migration: Add ERP Sync Columns</h2>";
    echo "<hr>";

    // Check if columns already exist
    $checkQuery = "SHOW COLUMNS FROM applications LIKE 'is_synced_to_erp'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Column 'is_synced_to_erp' already exists. Migration may have been run before.</p>";
    } else {
        // Run migration
        $sql = "
            ALTER TABLE applications
            ADD COLUMN is_synced_to_erp TINYINT(1) DEFAULT 0 COMMENT 'Whether candidate has been imported to ERP' AFTER reviewed_at,
            ADD COLUMN synced_at DATETIME NULL COMMENT 'When candidate was synced to ERP' AFTER is_synced_to_erp,
            ADD COLUMN erp_employee_id INT NULL COMMENT 'Employee ID in ERP system' AFTER synced_at
        ";

        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>✅ Columns added successfully!</p>";

            // Add index
            $indexSql = "CREATE INDEX idx_is_synced ON applications(is_synced_to_erp)";
            if ($conn->query($indexSql) === TRUE) {
                echo "<p style='color: green;'>✅ Index created successfully!</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Index creation: " . $conn->error . "</p>";
            }

            echo "<h3 style='color: green;'>🎉 Migration completed successfully!</h3>";
            echo "<p>The following columns have been added to the 'applications' table:</p>";
            echo "<ul>";
            echo "<li><strong>is_synced_to_erp</strong> - TINYINT(1) DEFAULT 0</li>";
            echo "<li><strong>synced_at</strong> - DATETIME NULL</li>";
            echo "<li><strong>erp_employee_id</strong> - INT NULL</li>";
            echo "</ul>";
            echo "<p><a href='" . ($_SERVER['HTTP_REFERER'] ?? '../../../erp/recruitment/onboarding') . "' style='color: blue;'>← Back to ERP</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
        }
    }

    $conn->close();

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}
?>