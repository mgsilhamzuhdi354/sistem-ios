<?php
/**
 * PT Indo Ocean - Database Connection Diagnostics
 * Deploy ke NAS dan akses via browser untuk debug koneksi database
 * HAPUS FILE INI SETELAH SELESAI DEBUG!
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>DB Diagnostics - PT Indo Ocean</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #0f172a; color: #e2e8f0; }
        h1 { color: #38bdf8; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; }
        h2 { color: #7dd3fc; margin-top: 30px; }
        .card { background: #1e293b; border-radius: 12px; padding: 20px; margin: 15px 0; border: 1px solid #334155; }
        .success { border-left: 4px solid #22c55e; }
        .error { border-left: 4px solid #ef4444; }
        .warning { border-left: 4px solid #f59e0b; }
        .info { border-left: 4px solid #3b82f6; }
        code { background: #0f172a; padding: 2px 8px; border-radius: 4px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td, th { padding: 8px 12px; text-align: left; border-bottom: 1px solid #334155; }
        th { color: #94a3b8; font-weight: 600; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-ok { background: #166534; color: #86efac; }
        .badge-fail { background: #7f1d1d; color: #fca5a5; }
        .badge-warn { background: #78350f; color: #fcd34d; }
        pre { background: #0f172a; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
    </style>
</head>
<body>
    <h1>🔧 Database Connection Diagnostics</h1>
    <p>PT Indo Ocean Crew Services — NAS Deployment Debug Tool</p>

    <?php
    // ======================================================
    // 1. SYSTEM INFO
    // ======================================================
    ?>
    <h2>1. System Information</h2>
    <div class="card info">
        <table>
            <tr><th>Item</th><th>Value</th></tr>
            <tr><td>PHP Version</td><td><code><?= PHP_VERSION ?></code></td></tr>
            <tr><td>OS</td><td><code><?= PHP_OS ?> (<?= PHP_OS_FAMILY ?>)</code></td></tr>
            <tr><td>Server</td><td><code><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></code></td></tr>
            <tr><td>Hostname</td><td><code><?= gethostname() ?></code></td></tr>
            <tr><td>Container IP</td><td><code><?= gethostbyname(gethostname()) ?></code></td></tr>
            <tr><td>MySQLi Extension</td><td><?= extension_loaded('mysqli') ? '<span class="badge badge-ok">LOADED ✓</span>' : '<span class="badge badge-fail">NOT LOADED ✗</span>' ?></td></tr>
            <tr><td>Current Time</td><td><code><?= date('Y-m-d H:i:s T') ?></code></td></tr>
        </table>
    </div>

    <?php
    // ======================================================
    // 2. ENVIRONMENT VARIABLES
    // ======================================================
    ?>
    <h2>2. Environment Variables</h2>
    <div class="card info">
        <table>
            <tr><th>Variable</th><th>$_ENV</th><th>getenv()</th><th>$_SERVER</th></tr>
            <?php
            $envVars = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_PORT', 'DB_ROOT_PASS', 'DB_USERNAME', 'DB_PASSWORD', 'ERP_DB_NAME', 'RECRUITMENT_DB_NAME', 'APP_ENV'];
            foreach ($envVars as $var) {
                $envVal = isset($_ENV[$var]) ? $_ENV[$var] : '<em style="color:#64748b">not set</em>';
                $getenvVal = getenv($var) !== false ? getenv($var) : '<em style="color:#64748b">not set</em>';
                $serverVal = isset($_SERVER[$var]) ? $_SERVER[$var] : '<em style="color:#64748b">not set</em>';
                
                // Mask passwords
                if (strpos($var, 'PASS') !== false || strpos($var, 'PASSWORD') !== false) {
                    if (is_string($envVal) && strlen($envVal) > 0 && $envVal !== '<em style="color:#64748b">not set</em>') 
                        $envVal = substr($envVal, 0, 2) . str_repeat('*', max(0, strlen($envVal)-2)) . ' (' . strlen($envVal) . ' chars)';
                    if (is_string($getenvVal) && strlen($getenvVal) > 0 && $getenvVal !== '<em style="color:#64748b">not set</em>') 
                        $getenvVal = substr($getenvVal, 0, 2) . str_repeat('*', max(0, strlen($getenvVal)-2)) . ' (' . strlen($getenvVal) . ' chars)';
                    if (is_string($serverVal) && strlen($serverVal) > 0 && $serverVal !== '<em style="color:#64748b">not set</em>') 
                        $serverVal = substr($serverVal, 0, 2) . str_repeat('*', max(0, strlen($serverVal)-2)) . ' (' . strlen($serverVal) . ' chars)';
                }
                
                echo "<tr><td><code>$var</code></td><td>$envVal</td><td>$getenvVal</td><td>$serverVal</td></tr>";
            }
            ?>
        </table>
    </div>

    <?php
    // ======================================================
    // 3. CONNECTION TESTS
    // ======================================================
    ?>
    <h2>3. Connection Tests</h2>
    <?php
    // Define all possible credential combinations
    $credentialSets = [
        ['label' => 'ENV DB_USER/DB_PASS', 'user' => getenv('DB_USER') ?: '', 'pass' => getenv('DB_PASS') ?: ''],
        ['label' => 'root / rahasia123', 'user' => 'root', 'pass' => 'rahasia123'],
        ['label' => 'root / (empty)', 'user' => 'root', 'pass' => ''],
        ['label' => 'indoocean / indoocean123', 'user' => 'indoocean', 'pass' => 'indoocean123'],
    ];

    // Remove duplicates
    $uniqueCreds = [];
    foreach ($credentialSets as $cred) {
        $key = $cred['user'] . '|' . $cred['pass'];
        if (!isset($uniqueCreds[$key])) {
            $uniqueCreds[$key] = $cred;
        }
    }

    $hostsToTest = [
        'mysql',
        'mariadb-1',
        'mariadb',
        '192.168.18.44',
        '172.17.0.1',
        '172.17.0.2',
        '172.17.0.3',
        '172.17.0.4',
        '172.17.0.5',
        'localhost',
        '127.0.0.1',
    ];

    $port = (int)(getenv('DB_PORT') ?: 3306);
    $successfulConnection = null;
    
    mysqli_report(MYSQLI_REPORT_OFF);

    foreach ($uniqueCreds as $cred) {
        echo '<div class="card info">';
        echo '<h3>Credentials: ' . htmlspecialchars($cred['label']) . '</h3>';
        echo "<p>User: <code>" . htmlspecialchars($cred['user']) . "</code> | Password: <code>" . (strlen($cred['pass']) > 0 ? substr($cred['pass'], 0, 2) . '***' : '(empty)') . "</code> | Port: <code>$port</code></p>";
        echo '<table><tr><th>Host</th><th>Status</th><th>Detail</th></tr>';
        
        foreach ($hostsToTest as $host) {
            echo "<tr><td><code>$host</code></td>";
            
            try {
                $conn = @new mysqli($host, $cred['user'], $cred['pass'], '', $port);
                if (!$conn->connect_error) {
                    echo '<td><span class="badge badge-ok">CONNECTED ✓</span></td>';
                    
                    // Check databases
                    $dbs = [];
                    $result = $conn->query("SHOW DATABASES");
                    while ($row = $result->fetch_row()) {
                        $dbs[] = $row[0];
                    }
                    $hasErp = in_array('erp_db', $dbs);
                    $hasRecruit = in_array('recruitment_db', $dbs);
                    
                    $detail = "Server: " . $conn->server_info;
                    $detail .= " | erp_db: " . ($hasErp ? '✓' : '✗');
                    $detail .= " | recruitment_db: " . ($hasRecruit ? '✓' : '✗');
                    echo "<td>$detail</td>";
                    
                    if (!$successfulConnection) {
                        $successfulConnection = [
                            'host' => $host,
                            'user' => $cred['user'],
                            'pass' => $cred['pass'],
                            'label' => $cred['label'],
                            'hasErp' => $hasErp,
                            'hasRecruit' => $hasRecruit,
                        ];
                    }
                    
                    $conn->close();
                } else {
                    echo '<td><span class="badge badge-fail">FAILED ✗</span></td>';
                    echo '<td>' . htmlspecialchars($conn->connect_error) . '</td>';
                }
            } catch (Exception $e) {
                echo '<td><span class="badge badge-fail">ERROR ✗</span></td>';
                echo '<td>' . htmlspecialchars($e->getMessage()) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table></div>';
    }
    ?>

    <?php
    // ======================================================
    // 4. RECOMMENDATION
    // ======================================================
    ?>
    <h2>4. Recommendation</h2>
    <?php if ($successfulConnection): ?>
    <div class="card success">
        <h3>✅ Koneksi berhasil ditemukan!</h3>
        <p>Gunakan konfigurasi berikut:</p>
        <table>
            <tr><td>Host</td><td><code><?= htmlspecialchars($successfulConnection['host']) ?></code></td></tr>
            <tr><td>User</td><td><code><?= htmlspecialchars($successfulConnection['user']) ?></code></td></tr>
            <tr><td>Password</td><td><code><?= htmlspecialchars($successfulConnection['label']) ?></code></td></tr>
            <tr><td>Port</td><td><code><?= $port ?></code></td></tr>
            <tr><td>erp_db exists</td><td><?= $successfulConnection['hasErp'] ? '✓ Yes' : '✗ No — PERLU DIBUAT!' ?></td></tr>
            <tr><td>recruitment_db exists</td><td><?= $successfulConnection['hasRecruit'] ? '✓ Yes' : '✗ No — PERLU DIBUAT!' ?></td></tr>
        </table>
        
        <h3 style="margin-top:20px">Environment Variables yang perlu di-set di container app:</h3>
        <pre>DB_HOST=<?= htmlspecialchars($successfulConnection['host']) ?>

DB_USER=<?= htmlspecialchars($successfulConnection['user']) ?>

DB_PASS=<?= htmlspecialchars($successfulConnection['pass']) ?>

DB_PORT=<?= $port ?>

ERP_DB_NAME=erp_db
RECRUITMENT_DB_NAME=recruitment_db</pre>
    </div>
    <?php else: ?>
    <div class="card error">
        <h3>❌ Tidak ada koneksi yang berhasil</h3>
        <p>Kemungkinan masalah:</p>
        <ul>
            <li>Container MariaDB tidak running</li>
            <li>Container app dan MariaDB tidak di network yang sama</li>
            <li>Password yang dicoba semua salah</li>
            <li>MariaDB tidak mengizinkan koneksi dari IP container app</li>
        </ul>
        <p><strong>Solusi:</strong> Pastikan MariaDB running, lalu cek password root dengan masuk ke terminal MariaDB container.</p>
    </div>
    <?php endif; ?>

    <?php
    // ======================================================
    // 5. NETWORK INFO
    // ======================================================
    ?>
    <h2>5. Network Information</h2>
    <div class="card info">
        <?php
        // DNS resolution test
        echo '<h3>DNS Resolution</h3>';
        echo '<table><tr><th>Hostname</th><th>Resolved IP</th></tr>';
        foreach (['mysql', 'mariadb-1', 'mariadb', 'localhost'] as $h) {
            $ip = gethostbyname($h);
            $resolved = ($ip !== $h) ? $ip : '<em style="color:#ef4444">Cannot resolve</em>';
            echo "<tr><td><code>$h</code></td><td>$resolved</td></tr>";
        }
        echo '</table>';
        ?>
    </div>

    <div class="card warning" style="margin-top: 30px;">
        <p>⚠️ <strong>HAPUS FILE INI SETELAH SELESAI DEBUG!</strong> File ini menampilkan informasi sensitif.</p>
    </div>
</body>
</html>
