<?php
/**
 * Fix Pipeline - Add missing columns
 */
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli('localhost', 'root', '', 'recruitment_db', 3308);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "<h2>🔧 Fix Pipeline Database</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#1a1a2e;color:#eee;}.ok{color:#4ade80;}.err{color:#f87171;}.skip{color:#60a5fa;}</style>";

$fixes = [
    "ALTER TABLE applications ADD COLUMN priority ENUM('urgent','high','normal','low') DEFAULT 'normal'" => "priority",
    "ALTER TABLE applications ADD COLUMN submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP" => "submitted_at",
    "ALTER TABLE applications ADD COLUMN reviewed_by INT" => "reviewed_by",
    "ALTER TABLE job_vacancies ADD COLUMN department_id INT" => "department_id",
    "CREATE TABLE IF NOT EXISTS departments (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), color VARCHAR(20) DEFAULT '#6c757d')" => "departments table",
];

foreach ($fixes as $sql => $name) {
    $result = @$conn->query($sql);
    if ($result) {
        echo "<p class='ok'>✅ $name - Added</p>";
    } else {
        if (strpos($conn->error, 'Duplicate') !== false) {
            echo "<p class='skip'>ℹ️ $name - Already exists</p>";
        } else {
            echo "<p class='err'>❌ $name - " . $conn->error . "</p>";
        }
    }
}

// Insert default departments
@$conn->query("INSERT IGNORE INTO departments (id, name, color) VALUES (1,'Deck','#3b82f6'),(2,'Engine','#ef4444'),(3,'Galley','#22c55e')");

echo "<hr><p style='color:#4ade80'>✅ Done! Now try Pipeline.</p>";
echo "<p><a href='".str_replace('/fix_pipeline.php','/master-admin/pipeline',$_SERVER['REQUEST_URI'])."' style='color:#60a5fa'>🔗 Go to Pipeline</a></p>";
$conn->close();
?>
