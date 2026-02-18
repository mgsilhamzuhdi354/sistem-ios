<?php
/**
 * Debug Login & Reset Password
 * URL: http://localhost/PT_indoocean/recruitment/public/check_users.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'recruitment_db';
$username = 'root';
$password = '';

echo "<h1>🔧 Recruitment System - User Debug Tool</h1>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green;'>✅ Database connection successful!</p>";
    
    // STEP 1: Ensure role ID 11 exists for master_admin
    echo "<h2>Step 1: Check/Create Master Admin Role (ID=11)</h2>";
    
    $checkRole = $pdo->query("SELECT * FROM roles WHERE id = 11")->fetch(PDO::FETCH_ASSOC);
    if (!$checkRole) {
        $pdo->exec("INSERT INTO roles (id, name, description) VALUES (11, 'master_admin', 'Master Administrator - Full Access')");
        echo "<p style='color:green;'>✅ Created role: master_admin (ID: 11)</p>";
    } else {
        echo "<p>✅ Role exists: {$checkRole['name']} (ID: 11)</p>";
    }
    
    // STEP 2: Create/Update Master Admin User
    echo "<h2>Step 2: Create/Update Master Admin User</h2>";
    
    $newPassword = 'admin123';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $checkUser = $pdo->query("SELECT id, email, role_id, is_active, password FROM users WHERE email = 'masteradmin@indoceancrew.com'")->fetch(PDO::FETCH_ASSOC);
    
    if (!$checkUser) {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (role_id, email, password, full_name, is_active, created_at) VALUES (11, 'masteradmin@indoceancrew.com', ?, 'Master Administrator', 1, NOW())");
        $stmt->execute([$hashedPassword]);
        echo "<p style='color:green;'>✅ Created Master Admin user!</p>";
    } else {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role_id = 11, is_active = 1 WHERE email = 'masteradmin@indoceancrew.com'");
        $stmt->execute([$hashedPassword]);
        echo "<p style='color:green;'>✅ Updated Master Admin password!</p>";
        echo "<p>Previous role_id: {$checkUser['role_id']}, is_active: {$checkUser['is_active']}</p>";
    }
    
    // STEP 3: Verify the password works
    echo "<h2>Step 3: Verify Password</h2>";
    
    $user = $pdo->query("SELECT id, email, password, role_id, is_active FROM users WHERE email = 'masteradmin@indoceancrew.com'")->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p>User ID: {$user['id']}</p>";
        echo "<p>Email: {$user['email']}</p>";
        echo "<p>Role ID: {$user['role_id']}</p>";
        echo "<p>Is Active: " . ($user['is_active'] ? 'Yes' : 'No') . "</p>";
        echo "<p>Password Hash: " . substr($user['password'], 0, 40) . "...</p>";
        
        // Test password verification
        if (password_verify($newPassword, $user['password'])) {
            echo "<p style='color:green;font-weight:bold;'>✅ Password verification: SUCCESS!</p>";
        } else {
            echo "<p style='color:red;font-weight:bold;'>❌ Password verification: FAILED!</p>";
        }
    }
    
    // STEP 4: Show all users
    echo "<h2>Step 4: All Users in Database</h2>";
    
    $users = $pdo->query("
        SELECT u.id, u.email, u.full_name, u.role_id, u.is_active, r.name as role_name
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.id
        ORDER BY u.role_id, u.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'><th>ID</th><th>Email</th><th>Full Name</th><th>Role ID</th><th>Role Name</th><th>Active</th></tr>";
    
    foreach ($users as $row) {
        $activeColor = $row['is_active'] ? 'green' : 'red';
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['full_name']}</td>";
        echo "<td>{$row['role_id']}</td>";
        echo "<td>" . ($row['role_name'] ?? 'Unknown') . "</td>";
        echo "<td style='color:{$activeColor};'>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // STEP 5: Show all roles
    echo "<h2>Step 5: All Roles</h2>";
    
    $roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'><th>ID</th><th>Name</th><th>Description</th></tr>";
    foreach ($roles as $role) {
        echo "<tr><td>{$role['id']}</td><td>{$role['name']}</td><td>" . ($role['description'] ?? '') . "</td></tr>";
    }
    echo "</table>";
    
    // Login credentials box
    echo "<div style='margin-top:30px; padding:20px; background:#e8f5e9; border:2px solid #4caf50; border-radius:10px;'>";
    echo "<h2 style='color:#2e7d32; margin-top:0;'>🔑 Login Credentials</h2>";
    echo "<p><strong>Email:</strong> masteradmin@indoceancrew.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login' style='color:#1976d2;'>→ Go to Login Page</a></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>❌ Database Error</h2>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    echo "<h3>Possible Solutions:</h3>";
    echo "<ol>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check if database 'recruitment_db' exists</li>";
    echo "<li>Create the database: <code>CREATE DATABASE recruitment_db;</code></li>";
    echo "<li>Run the schema.sql to create tables</li>";
    echo "</ol>";
}
?>
