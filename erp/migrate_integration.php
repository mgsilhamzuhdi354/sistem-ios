<?php
/**
 * ERP Database Migration - Integration Support Tables
 * Adds activity_logs table, fixes notifications schema, adds crew approval columns
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'erp_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<!DOCTYPE html><html><head><title>ERP Integration Migration</title>
<style>
body { font-family: 'Inter', Arial, sans-serif; padding: 20px; background: #f5f5f5; max-width: 800px; margin: 0 auto; }
h1 { color: #0A2463; }
h2 { color: #1e3a5f; margin-top: 24px; }
.success { color: green; background: #d4edda; padding: 10px; margin: 5px 0; border-radius: 5px; }
.error { color: red; background: #f8d7da; padding: 10px; margin: 5px 0; border-radius: 5px; }
.info { color: #0c5460; background: #d1ecf1; padding: 10px; margin: 5px 0; border-radius: 5px; }
.skip { color: #856404; background: #fff3cd; padding: 10px; margin: 5px 0; border-radius: 5px; }
</style>
</head><body><h1>🔗 ERP Integration Migration</h1>";

// Helper function
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

// ============================================
// 1. Create activity_logs table
// ============================================
echo "<h2>📋 Activity Logs Table</h2>";

if (!tableExists($conn, 'activity_logs')) {
    $sql = "
    CREATE TABLE activity_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NULL,
        action VARCHAR(100) NOT NULL,
        description TEXT,
        entity_type VARCHAR(50) NULL COMMENT 'e.g. crew, contract, notification',
        entity_id INT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_action (action),
        INDEX idx_entity (entity_type, entity_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Created <strong>activity_logs</strong> table</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='skip'>⚠️ Table <strong>activity_logs</strong> already exists</div>";
}

// ============================================
// 2. Fix notifications table - alter type column to VARCHAR
// ============================================
echo "<h2>🔔 Notifications Table Fix</h2>";

if (tableExists($conn, 'notifications')) {
    // Check current type column definition
    $colResult = $conn->query("SHOW COLUMNS FROM notifications LIKE 'type'");
    if ($colResult && $colResult->num_rows > 0) {
        $colInfo = $colResult->fetch_assoc();
        if (strpos($colInfo['Type'], 'enum') !== false) {
            // Alter from ENUM to VARCHAR to support custom notification types
            $sql = "ALTER TABLE notifications MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'info'";
            if ($conn->query($sql)) {
                echo "<div class='success'>✅ Changed <strong>notifications.type</strong> from ENUM to VARCHAR(50)</div>";
            } else {
                echo "<div class='error'>❌ Error altering type: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='skip'>⚠️ <strong>notifications.type</strong> already VARCHAR</div>";
        }
    }

    // Ensure 'link' column exists (for action URLs)
    if (!columnExists($conn, 'notifications', 'link')) {
        if (!columnExists($conn, 'notifications', 'action_url')) {
            $sql = "ALTER TABLE notifications ADD COLUMN link VARCHAR(500) NULL AFTER message";
            if ($conn->query($sql)) {
                echo "<div class='success'>✅ Added <strong>link</strong> column to notifications</div>";
            } else {
                echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
            }
        } else {
            // Rename action_url to link
            $sql = "ALTER TABLE notifications CHANGE COLUMN action_url link VARCHAR(500) NULL";
            if ($conn->query($sql)) {
                echo "<div class='success'>✅ Renamed <strong>action_url</strong> to <strong>link</strong></div>";
            } else {
                echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
            }
        }
    } else {
        echo "<div class='skip'>⚠️ <strong>notifications.link</strong> column already exists</div>";
    }

    // Ensure 'data' column exists for JSON metadata
    if (!columnExists($conn, 'notifications', 'data')) {
        $sql = "ALTER TABLE notifications ADD COLUMN data JSON NULL AFTER link";
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Added <strong>data</strong> column to notifications</div>";
        } else {
            echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='skip'>⚠️ <strong>notifications.data</strong> already exists</div>";
    }
} else {
    // Create the full notifications table
    $sql = "
    CREATE TABLE notifications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NULL,
        type VARCHAR(50) NOT NULL DEFAULT 'info',
        title VARCHAR(255) NOT NULL,
        message TEXT,
        link VARCHAR(500) NULL,
        data JSON NULL,
        is_read TINYINT(1) DEFAULT 0,
        read_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_read (is_read),
        INDEX idx_type (type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Created <strong>notifications</strong> table</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
    }
}

// ============================================
// 3. Add crew approval columns
// ============================================
echo "<h2>👥 Crews Table - Approval Columns</h2>";

if (tableExists($conn, 'crews')) {
    $crewColumns = [
        ['approved_at', 'TIMESTAMP NULL'],
        ['approved_by', 'INT NULL'],
        ['rejected_at', 'TIMESTAMP NULL'],
        ['rejected_by', 'INT NULL'],
        ['rejection_reason', 'TEXT NULL'],
    ];

    foreach ($crewColumns as $col) {
        if (!columnExists($conn, 'crews', $col[0])) {
            $sql = "ALTER TABLE crews ADD COLUMN {$col[0]} {$col[1]}";
            if ($conn->query($sql)) {
                echo "<div class='success'>✅ Added <strong>{$col[0]}</strong> to crews</div>";
            } else {
                echo "<div class='error'>❌ Error adding {$col[0]}: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='skip'>⚠️ <strong>crews.{$col[0]}</strong> already exists</div>";
        }
    }
} else {
    echo "<div class='error'>❌ crews table does not exist!</div>";
}

// ============================================
// 4. Create recruitment_sync table if not exists
// ============================================
echo "<h2>🔄 Recruitment Sync Table</h2>";

if (!tableExists($conn, 'recruitment_sync')) {
    $sql = "
    CREATE TABLE recruitment_sync (
        id INT PRIMARY KEY AUTO_INCREMENT,
        recruitment_applicant_id INT NOT NULL,
        crew_id INT NULL,
        contract_id INT NULL,
        sync_status ENUM('pending', 'synced', 'onboarded', 'failed') DEFAULT 'pending',
        synced_at TIMESTAMP NULL,
        error_message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_applicant (recruitment_applicant_id),
        INDEX idx_crew (crew_id),
        INDEX idx_status (sync_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Created <strong>recruitment_sync</strong> table</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='skip'>⚠️ <strong>recruitment_sync</strong> already exists</div>";
}

echo "<br><div class='success'><h3>🎉 Integration Migration Complete!</h3>
<a href='/erp/' style='color: #0A2463; font-weight: bold;'>← Back to ERP Dashboard</a> |
<a href='/erp/recruitment/approval' style='color: #0A2463; font-weight: bold;'>→ Approval Center</a>
</div>";
echo "</body></html>";

$conn->close();
?>
