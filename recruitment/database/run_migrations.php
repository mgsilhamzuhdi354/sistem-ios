<?php
/**
 * Database Migration Runner
 * Run this file to apply all pending migrations
 */

// Include database config
require_once __DIR__ . '/../app/Config/Database.php';

$config = $dbConfig['default'];

// Connect to database
$conn = new mysqli(
    $config['hostname'],
    $config['username'],
    $config['password'],
    $config['database'],
    $config['port']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>🚀 Running Database Migrations</h2>";
echo "<hr>";

// Migration files
$migrations = [
    '001_add_recruiter_features.sql',
    '002_add_manual_entry.sql',
    'add_erp_sync_columns.sql',
    '003_add_medical_email_templates.sql'
];

$migrationsPath = __DIR__ . '/migrations/';

foreach ($migrations as $migration) {
    $filePath = $migrationsPath . $migration;
    
    if (!file_exists($filePath)) {
        echo "<p style='color: orange'>⚠️ Migration file not found: {$migration}</p>";
        continue;
    }
    
    echo "<h3>📝 Running: {$migration}</h3>";
    
    $sql = file_get_contents($filePath);
    
    // Split by semicolon for multiple statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && strpos($stmt, '--') !== 0;
        }
    );
    
    $success = true;
    foreach ($statements as $statement) {
        if (stripos($statement, 'CREATE INDEX') !== false || 
            stripos($statement, 'CREATE UNIQUE INDEX') !== false) {
            // Check if index already exists
            preg_match('/CREATE\s+(?:UNIQUE\s+)?INDEX\s+(\w+)/i', $statement, $matches);
            if ($matches) {
                $indexName = $matches[1];
                $checkIndex = $conn->query("SHOW INDEX FROM applications WHERE Key_name = '{$indexName}'");
                if ($checkIndex && $checkIndex->num_rows > 0) {
                    echo "<p style='color: gray'>  ↳ Index {$indexName} already exists, skipping...</p>";
                    continue;
                }
            }
        }
        
        if ($conn->query($statement)) {
            echo "<p style='color: green'>  ✓ Statement executed successfully</p>";
        } else {
            // Check if it's just a duplicate column/key error (already exists)
            if (strpos($conn->error, 'Duplicate') !== false || 
                strpos($conn->error, 'already exists') !== false) {
                echo "<p style='color: gray'>  ↳ Already exists, skipping: " . substr($statement, 0, 60) . "...</p>";
            } else {
                echo "<p style='color: red'>  ✗ Error: " . $conn->error . "</p>";
                echo "<pre>" . htmlspecialchars(substr($statement, 0, 200)) . "</pre>";
                $success = false;
            }
        }
    }
    
    if ($success) {
        echo "<p style='color: green; font-weight: bold'>✅ {$migration} completed successfully!</p>";
    } else {
        echo "<p style='color: orange; font-weight: bold'>⚠️ {$migration} completed with some warnings</p>";
    }
    
    echo "<hr>";
}

$conn->close();

echo "<h3 style='color: green'>🎉 All migrations processed!</h3>";
echo "<p><a href='/recruitment/public'>← Back to Application</a></p>";
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background: #f5f5f5;
    }
    h2, h3 {
        color: #333;
    }
    p {
        margin: 0.5rem 0;
        padding-left: 1rem;
    }
    pre {
        background: #272822;
        color: #f8f8f2;
        padding: 1rem;
        border-radius: 5px;
        overflow-x: auto;
    }
    hr {
        border: none;
        border-top: 2px solid #ddd;
        margin: 1.5rem 0;
    }
</style>
