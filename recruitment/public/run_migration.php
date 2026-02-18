<?php
/**
 * Migration Script: Add ERP Sync Columns
 * Run this file once via browser to execute migration
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'recruitment_db';  // Actual database name from Config/Database.php

?>
<!DOCTYPE html>
<html>

<head>
    <title>Database Migration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .warning {
            color: orange;
        }

        .info {
            background: #e7f3ff;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <h1>🔧 Database Migration Tool</h1>
    <h2>Add ERP Sync Columns to Applications Table</h2>
    <hr>

    <?php
    try {
        // Connect to database
        $conn = new mysqli($host, $user, $pass, $dbname);

        if ($conn->connect_error) {
            die("<p class='error'>❌ Connection failed: " . $conn->connect_error . "</p>");
        }

        echo "<p class='success'>✅ Connected to database: <strong>$dbname</strong></p>";

        // Check if columns already exist
        $checkQuery = "SHOW COLUMNS FROM applications LIKE 'is_synced_to_erp'";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            echo "<div class='info'>";
            echo "<p class='warning'>⚠️ Column 'is_synced_to_erp' already exists.</p>";
            echo "<p>This migration may have been run before. Checking all columns...</p>";

            // Verify all columns
            $columns = ['is_synced_to_erp', 'synced_at', 'erp_employee_id'];
            foreach ($columns as $col) {
                $check = $conn->query("SHOW COLUMNS FROM applications LIKE '$col'");
                if ($check->num_rows > 0) {
                    echo "<p class='success'>✅ $col - exists</p>";
                } else {
                    echo "<p class='error'>❌ $col - missing</p>";
                }
            }
            echo "</div>";

        } else {
            echo "<p>Starting migration...</p>";

            // Run migration - Add columns
            $sql = "ALTER TABLE applications
            ADD COLUMN is_synced_to_erp TINYINT(1) DEFAULT 0 COMMENT 'Whether candidate has been imported to ERP' AFTER reviewed_at,
            ADD COLUMN synced_at DATETIME NULL COMMENT 'When candidate was synced to ERP' AFTER is_synced_to_erp,
            ADD COLUMN erp_employee_id INT NULL COMMENT 'Employee ID in ERP system' AFTER synced_at";

            if ($conn->query($sql) === TRUE) {
                echo "<p class='success'>✅ Columns added successfully!</p>";

                // Add index
                $indexSql = "CREATE INDEX idx_is_synced ON applications(is_synced_to_erp)";
                if ($conn->query($indexSql) === TRUE) {
                    echo "<p class='success'>✅ Index created successfully!</p>";
                } else {
                    echo "<p class='warning'>⚠️ Index: " . $conn->error . " (may already exist)</p>";
                }

                echo "<div class='info'>";
                echo "<h3 class='success'>🎉 Migration completed successfully!</h3>";
                echo "<p>The following columns have been added to the '<strong>applications</strong>' table:</p>";
                echo "<ul>";
                echo "<li><strong>is_synced_to_erp</strong> - TINYINT(1) DEFAULT 0</li>";
                echo "<li><strong>synced_at</strong> - DATETIME NULL</li>";
                echo "<li><strong>erp_employee_id</strong> - INT NULL</li>";
                echo "</ul>";
                echo "</div>";

            } else {
                echo "<p class='error'>❌ Error adding columns: " . $conn->error . "</p>";
            }
        }

        echo "<hr>";
        echo "<h3>Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Go to <a href='../../erp/recruitment/onboarding'>ERP Onboarding Page</a></li>";
        echo "<li>The 'Unknown column' error should now be fixed</li>";
        echo "<li>You can now import candidates from Recruitment to ERP</li>";
        echo "</ol>";

        $conn->close();

    } catch (Exception $e) {
        echo "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
    }
    ?>

    <hr>
    <p><a href="../../erp/recruitment/pipeline">← Back to ERP Recruitment</a></p>

</body>

</html>