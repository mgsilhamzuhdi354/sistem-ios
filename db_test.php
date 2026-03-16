<?php
// Raw MariaDB Connection Tester
// This script bypasses CodeIgniter and tests raw mysqli connections to find the exact host/port

header('Content-Type: text/plain');

echo "=== RAW DOCKER MARIADB CONNECTION TEST ===\n\n";

// The typical hosts we should try
$hosts = [
    'mariadb-1',
    'mysql',
    '172.17.0.1',
    '192.168.18.44',
    '127.0.0.1',
    'localhost'
];

$user = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : 'root');
$pass = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : (isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : 'root');
$port = 3306;

echo "Using Credentials:\n";
echo "User: " . $user . "\n";
echo "Pass: " . (empty($pass) ? '(empty)' : str_repeat('*', strlen($pass))) . "\n\n";

$success = false;

foreach ($hosts as $host) {
    echo "Testing Host: " . str_pad($host, 18) . " ... ";
    
    // Disable exceptions, use traditional error reporting for testing
    mysqli_report(MYSQLI_REPORT_OFF);
    
    $start = microtime(true);
    // Suppress warnings for cleaner output
    $conn = @new mysqli($host, $user, $pass, '', $port);
    $time = round((microtime(true) - $start) * 1000, 2);
    
    if ($conn->connect_error) {
        $err = $conn->connect_error;
        if (strpos($err, 'Connection refused') !== false) {
             echo "FAILED (Connection Refused - Port is closed or not exposed)\n";
        } elseif (strpos($err, 'Access denied') !== false) {
             echo "PARTIAL SUCCESS (Host Reached, but Password/User is WRONG!)\n";
             $success = true; // We found the host, just wrong credentials
        } elseif (strpos($err, 'Name or service not known') !== false || strpos($err, 'Unknown MySQL server host') !== false) {
             echo "FAILED (Cannot Resolve Hostname - Network Isolation)\n";
        } else {
             echo "FAILED ({$err})\n";
        }
    } else {
        echo "SUCCESS! Connected in {$time}ms\n";
        echo "Server Version: " . $conn->server_info . "\n\n";
        $success = true;
        $conn->close();
        break; // Stop on first full success
    }
}

echo "\n\n=== DIAGNOSIS ===\n";
if (!$success) {
    echo "CRITICAL: No connection possible.\n";
    echo "1. If all IP addresses say 'Connection Refused', MariaDB on the NAS is NOT exposing port 3306, OR it is stopped.\n";
    echo "2. If 'mariadb-1' says 'Cannot Resolve Hostname', this PHP container is still NOT in the same Docker Network as MariaDB.\n";
    echo "3. Please verify MariaDB is actually running in the Synology/Ugreen Docker interface.\n";
} else {
    echo "Host found. Please ensure the correct host and working credentials are in your .env or Docker Environment Variables.\n";
}
?>
