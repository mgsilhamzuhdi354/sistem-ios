<?php
/**
 * PT Indo Ocean - ERP System
 * Crew Management Migration Runner
 * Execute this file via browser to run the migration
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'erp_db';

// Read SQL file
$sqlFile = __DIR__ . '/crew_management_tables.sql';

if (!file_exists($sqlFile)) {
    die("❌ ERROR: Migration file not found: $sqlFile");
}

$sql = file_get_contents($sqlFile);

if (empty($sql)) {
    die("❌ ERROR: Migration file is empty");
}

// Connect to database
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Crew Management Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #2c3e50; }
        .success { color: #27ae60; padding: 10px; background: #d5f4e6; border-left: 4px solid #27ae60; margin: 10px 0; }
        .error { color: #e74c3c; padding: 10px; background: #fadbd8; border-left: 4px solid #e74c3c; margin: 10px 0; }
        .info { color: #3498db; padding: 10px; background: #d6eaf8; border-left: 4px solid #3498db; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #27ae60; color: white; }
        .badge-error { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <h1>🚢 Crew Management Migration</h1>
    <p>Database: <strong>$database</strong></p>
    <hr>
";

// Execute migration
$conn->multi_query($sql);

$success = true;
$errors = [];

// Process all results
do {
    if ($result = $conn->store_result()) {
        $result->free();
    }

    if ($conn->error) {
        $errors[] = $conn->error;
        $success = false;
    }
} while ($conn->more_results() && $conn->next_result());

if ($success && empty($errors)) {
    echo "<div class='success'>";
    echo "<h2>✅ Migration Successful!</h2>";
    echo "<p>All crew management tables have been created successfully.</p>";
    echo "</div>";

    echo "<div class='info'>";
    echo "<h3>📊 Tables Created:</h3>";
    echo "<ul>";

    $tables = ['crews', 'crew_skills', 'crew_experiences', 'crew_documents', 'document_types'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            // Get column count
            $columns = $conn->query("SHOW COLUMNS FROM $table");
            $colCount = $columns->num_rows;

            echo "<li><span class='badge badge-success'>✓</span> <strong>$table</strong> ($colCount columns)</li>";
        } else {
            echo "<li><span class='badge badge-error'>✗</span> <strong>$table</strong> (NOT FOUND)</li>";
        }
    }
    echo "</ul>";
    echo "</div>";

    // Check seed data
    $docCount = $conn->query("SELECT COUNT(*) as count FROM document_types")->fetch_assoc()['count'];
    echo "<div class='info'>";
    echo "<h3>📝 Seed Data:</h3>";
    echo "<p>Document Types inserted: <strong>$docCount</strong></p>";
    echo "</div>";

    echo "<div class='success'>";
    echo "<h3>✅ Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Test crew list page: <a href='/PT_indoocean/erp/crews' target='_blank'>/erp/crews</a></li>";
    echo "<li>Test skill matrix: <a href='/PT_indoocean/erp/crews/skill-matrix' target='_blank'>/erp/crews/skill-matrix</a></li>";
    echo "<li>Test documents page: <a href='/PT_indoocean/erp/documents' target='_blank'>/erp/documents</a></li>";
    echo "</ol>";
    echo "</div>";

} else {
    echo "<div class='error'>";
    echo "<h2>❌ Migration Failed</h2>";
    echo "<p>The following errors occurred:</p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

$conn->close();

echo "</body></html>";
?>