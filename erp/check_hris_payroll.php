<?php
/**
 * Check absensi_laravel database for employee and payroll data
 */

$host = '127.0.0.1';
$port = '3306';

try {
    $db = @new mysqli($host, 'root', '', 'absensi_laravel', $port);

    if ($db->connect_error) {
        // Try alternative name
        $db = @new mysqli($host, 'root', '', 'absensi-laravel', $port);
        if ($db->connect_error) {
            echo "Connection failed\n";
            exit;
        }
    }

    echo "=== Connected to absensi_laravel ===\n\n";

    // List all tables
    echo "=== ALL TABLES ===\n";
    $result = $db->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
        echo "- " . $row[0] . "\n";
    }

    // Check karyawan/users table
    echo "\n=== KARYAWAN/EMPLOYEE DATA ===\n";
    $empKeywords = ['karyawan', 'users', 'employee', 'pegawai'];
    foreach ($tables as $table) {
        foreach ($empKeywords as $kw) {
            if (stripos($table, $kw) !== false) {
                echo "Table: $table\n";

                $cols = $db->query("DESCRIBE `$table`");
                $colNames = [];
                while ($col = $cols->fetch_assoc()) {
                    $colNames[] = $col['Field'];
                }
                echo "  Columns: " . implode(', ', $colNames) . "\n";

                $count = $db->query("SELECT COUNT(*) as cnt FROM `$table`")->fetch_assoc();
                echo "  Total: " . $count['cnt'] . "\n";

                if ($count['cnt'] > 0) {
                    $sample = $db->query("SELECT * FROM `$table` LIMIT 5");
                    echo "  Data:\n";
                    while ($row = $sample->fetch_assoc()) {
                        // Remove password for security
                        unset($row['password']);
                        echo "    " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
                    }
                }
                echo "\n";
                break;
            }
        }
    }

    // Check for payroll/gaji tables
    echo "\n=== PAYROLL/GAJI DATA ===\n";
    $payrollKeywords = ['payroll', 'gaji', 'salary', 'penggajian', 'slip'];
    foreach ($tables as $table) {
        foreach ($payrollKeywords as $kw) {
            if (stripos($table, $kw) !== false) {
                echo "Table: $table\n";

                $cols = $db->query("DESCRIBE `$table`");
                $colNames = [];
                while ($col = $cols->fetch_assoc()) {
                    $colNames[] = $col['Field'];
                }
                echo "  Columns: " . implode(', ', $colNames) . "\n";

                $count = $db->query("SELECT COUNT(*) as cnt FROM `$table`")->fetch_assoc();
                echo "  Total: " . $count['cnt'] . "\n";

                if ($count['cnt'] > 0) {
                    $sample = $db->query("SELECT * FROM `$table` LIMIT 5");
                    echo "  Data:\n";
                    while ($row = $sample->fetch_assoc()) {
                        echo "    " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
                    }
                }
                echo "\n";
                break;
            }
        }
    }

    $db->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
