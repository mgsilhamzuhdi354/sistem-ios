<?php
/**
 * Debug ERP Login & Unlock Account
 * URL: http://localhost/PT_indoocean/erp/check_erp_users.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'erp_db';
$username = 'root';
$password = '';

// Try to get from env if available
if (file_exists(__DIR__ . '/.env')) {
    $envLines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos($line, '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        
        if (trim($name) == 'DB_DATABASE') $dbname = trim($value);
        if (trim($name) == 'DB_USERNAME') $username = trim($value);
        if (trim($name) == 'DB_PASSWORD') $password = trim($value);
    }
}

echo "<h1>🔧 ERP System - User Access Tool</h1>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green;'>✅ Database Connected ($dbname)</p>";

    // UNLOCK ACCOUNT ACTION
    if (isset($_POST['unlock_all'])) {
        $pdo->query("UPDATE users SET login_attempts = 0, locked_until = NULL, is_active = 1");
        echo "<div style='background:#d4edda; color:#155724; padding:10px; margin:10px 0; border:1px solid #c3e6cb; border-radius:5px;'>
              ✅ <strong>Semua akun berhasil di-unlock!</strong> Silakan coba login kembali.
              </div>";
    }

    // CREATE/FIX MASTERADMIN ACTION
    if (isset($_POST['fix_masteradmin'])) {
        $email = 'masteradmin@indoceancrew.com';
        $username = 'masteradmin'; // ERP might use username
        $newPass = 'admin123';
        $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        $role = 'super_admin'; // FIX: Use super_admin instead of admin
        
        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $pdo->prepare("UPDATE users SET password = ?, is_active = 1, login_attempts = 0, locked_until = NULL, role = ? WHERE id = ?")->execute([$hash, $role, $user['id']]);
            echo "<p style='color:green;'>Updated <strong>$email</strong> password to: <strong>admin123</strong> and role to: <strong>$role</strong></p>";
        } else {
            // Check if username exists but different email
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $uUser = $stmt->fetch();
            
            if ($uUser) {
                 $pdo->prepare("UPDATE users SET email = ?, password = ?, is_active = 1, login_attempts = 0, locked_until = NULL, role = ? WHERE id = ?")->execute([$email, $hash, $role, $uUser['id']]);
                 echo "<p style='color:green;'>Updated user <strong>$username</strong> to email <strong>$email</strong>, password: <strong>admin123</strong>, role: <strong>$role</strong></p>";
            } else {
                $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, is_active, created_at) VALUES (?, ?, ?, ?, 'Master Administrator', 1, NOW())")->execute([$username, $email, $hash, $role]);
                echo "<p style='color:green;'>Created new user. Email: <strong>$email</strong>, Pass: <strong>admin123</strong>, Role: <strong>$role</strong></p>";
            }
        }
    }
    
    // DASHBOARD
    echo "<div style='display:flex; gap:20px; margin-bottom:20px;'>";
    
    // Unlock Form
    echo "<form method='POST' style='border:1px solid #ccc; padding:15px; border-radius:5px;'>";
    echo "<h3 style='margin-top:0;'>🔓 Akun Terkunci?</h3>";
    echo "<p>Klik tombol ini untuk membuka kunci semua akun.</p>";
    echo "<button type='submit' name='unlock_all' style='cursor:pointer; background:#ffc107; border:none; padding:10px 20px; font-weight:bold; border-radius:4px;'>UNLOCK SEMUA AKUN</button>";
    echo "</form>";

    // Fix MasterAdmin Form
    echo "<form method='POST' style='border:1px solid #ccc; padding:15px; border-radius:5px;'>";
    echo "<h3 style='margin-top:0;'>🔑 Buat Login Master Admin (Super Admin)</h3>";
    echo "<p>Email: masteradmin@indoceancrew.com<br>Pass: admin123</p>";
    echo "<button type='submit' name='fix_masteradmin' style='cursor:pointer; background:#007bff; color:white; border:none; padding:10px 20px; font-weight:bold; border-radius:4px;'>BUAT / RESET & JADIKAN SUPER ADMIN</button>";
    echo "</form>";
    
    echo "</div>";
    
    // Show Users Table
    echo "<h2>Daftar User di Database ERP</h2>";
    $users = $pdo->query("SELECT id, username, email, role, full_name, is_active, login_attempts, locked_until FROM users")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background:#f8f9fa;'><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Percobaan Gagal</th><th>Terkunci Sampai</th></tr>";
    foreach ($users as $u) {
        $status = $u['is_active'] ? '<span style="color:green">Aktif</span>' : '<span style="color:red">Nonaktif</span>';
        $locked = $u['locked_until'] ? "<span style='color:red; font-weight:bold;'>{$u['locked_until']}</span>" : "-";
        $attempts = $u['login_attempts'] > 0 ? "<span style='color:red;'>{$u['login_attempts']}</span>" : "0";
        $roleStyle = $u['role'] === 'super_admin' ? 'font-weight:bold; color:blue;' : '';
        
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['username']}</td>";
        echo "<td>{$u['email']}</td>";
        echo "<td style='$roleStyle'>{$u['role']}</td>";
        echo "<td>$status</td>";
        echo "<td>$attempts</td>";
        echo "<td>$locked</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>Database Error</h2>";
    echo $e->getMessage();
}
?>
