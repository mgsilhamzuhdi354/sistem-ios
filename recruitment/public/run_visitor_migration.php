<?php
/**
 * Visitor Tracking Migration Runner
 * Run this file once to create visitor_logs table
 */

// Database credentials
$host = 'localhost';
$dbname = 'recruitment_db';
$username = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Visitor Tracking Migration</h2>";
    echo "<p>Starting migration...</p>";

    // Read SQL file
    $sqlFile = __DIR__ . '/../database/migrations/create_visitor_tracking.sql';

    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "<pre>";
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0 || strpos($statement, 'SELECT') === 0) {
            continue;
        }

        try {
            $pdo->exec($statement);
            echo "✅ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (PDOException $e) {
            // Ignore "Duplicate key" errors for indexes (already exists)
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "⚠️  Already exists, skipping...\n";
            } else {
                echo "❌ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "</pre>";

    echo "<h3>✅ Migration Completed Successfully!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Verify tables created in phpMyAdmin</li>";
    echo "<li>Implement VisitorTracker library</li>";
    echo "<li>Add tracking to public pages</li>";
    echo "</ol>";

} catch (PDOException $e) {
    echo "<h3>❌ Database Connection Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database credentials.</p>";
} catch (Exception $e) {
    echo "<h3>❌ Migration Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Visitor Tracking Migration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        h2 {
            color: #333;
        }

        pre {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }
    </style>
</head>

</html>