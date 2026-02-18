<?php
/**
 * Export ERP Users - PT Indo Ocean Crew Services
 * Menampilkan semua data user dari database ERP
 */

// Database credentials (Laragon local)
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// First, let's discover what tables exist
$tables_result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $tables_result->fetch_array()) {
    $tables[] = $row[0];
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Export ERP Users</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1, h2 { color: #0A2463; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0A2463; color: white; }
        tr:hover { background: #f9f9f9; }
        .info-box { background: #d4edda; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .warning-box { background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .tables-box { background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔐 ERP System - User Data</h1>
    
    <div class='tables-box'>
        <strong>📁 Tables in ERP Database:</strong><br>
        " . implode(", ", $tables) . "
    </div>
    
    <div class='warning-box'>
        <strong>⚠️ Security Note:</strong> Passwords are hashed for security. 
        If you need to reset, update directly in database or use reset feature.
    </div>";

// Try to find users table (could be 'users', 'admin', 'user', etc.)
$user_tables = ['users', 'admin', 'admins', 'user', 'accounts', 'login', 'tb_users', 'tb_admin'];
$found_table = null;

foreach ($user_tables as $table) {
    if (in_array($table, $tables)) {
        $found_table = $table;
        break;
    }
}

if ($found_table) {
    // Get table structure
    $columns_result = $conn->query("DESCRIBE $found_table");
    $columns = [];
    while ($col = $columns_result->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    echo "<h2>📋 Table: <code>$found_table</code></h2>";
    echo "<p><strong>Columns:</strong> " . implode(", ", $columns) . "</p>";
    
    // Display all data from users table
    $result = $conn->query("SELECT * FROM $found_table ORDER BY 1");
    
    if ($result && $result->num_rows > 0) {
        echo "<table><tr>";
        foreach ($columns as $col) {
            echo "<th>$col</th>";
        }
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($columns as $col) {
                $value = htmlspecialchars($row[$col] ?? '');
                // Truncate long values (like hashed passwords)
                if (strlen($value) > 50) {
                    $value = substr($value, 0, 50) . "...";
                }
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Total Records:</strong> " . $result->num_rows . "</p>";
    } else {
        echo "<p>No data found in table '$found_table'</p>";
    }
} else {
    echo "<div class='warning-box'>Could not find a standard user table. Checking all tables...</div>";
    
    // Show structure and data of all tables
    foreach ($tables as $table) {
        $count = $conn->query("SELECT COUNT(*) as cnt FROM $table")->fetch_assoc()['cnt'];
        echo "<h3>Table: <code>$table</code> ($count records)</h3>";
        
        if ($count > 0 && $count <= 50) {
            $columns_result = $conn->query("DESCRIBE $table");
            $columns = [];
            while ($col = $columns_result->fetch_assoc()) {
                $columns[] = $col['Field'];
            }
            
            $result = $conn->query("SELECT * FROM $table LIMIT 20");
            echo "<table><tr>";
            foreach ($columns as $col) {
                echo "<th>$col</th>";
            }
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $col) {
                    $value = htmlspecialchars($row[$col] ?? '');
                    if (strlen($value) > 40) {
                        $value = substr($value, 0, 40) . "...";
                    }
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
}

echo "</body></html>";
$conn->close();
?>
