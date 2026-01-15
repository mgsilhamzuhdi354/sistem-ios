<?php
// Debug Script - Check database directly
$conn = new mysqli('localhost', 'root', '', 'recruitment_db', 3308);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Check</h2>";

// Check user
$result = $conn->query("SELECT id, email, password, role_id, is_active FROM users WHERE email = 'ilham@indoceancrew.com'");

if ($result->num_rows === 0) {
    echo "<p style='color:red'>❌ User NOT FOUND!</p>";
    
    // Show all users
    echo "<h3>All users:</h3>";
    $all = $conn->query("SELECT id, email, role_id, is_active FROM users");
    while ($r = $all->fetch_assoc()) {
        echo "ID: {$r['id']}, Email: {$r['email']}, Role: {$r['role_id']}, Active: {$r['is_active']}<br>";
    }
} else {
    $user = $result->fetch_assoc();
    echo "<p>✅ User found: ID={$user['id']}, Role={$user['role_id']}, Active={$user['is_active']}</p>";
    echo "<p>Password hash: <code>" . htmlspecialchars($user['password']) . "</code></p>";
    
    // Test password
    $testPass = 'admin123';
    if (password_verify($testPass, $user['password'])) {
        echo "<p style='color:green'>✅ Password 'admin123' is CORRECT!</p>";
    } else {
        echo "<p style='color:red'>❌ Password 'admin123' is WRONG!</p>";
        
        // Try to generate new hash and update
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        echo "<p>New hash: <code>$newHash</code></p>";
        
        // Auto-fix
        echo "<hr><h3>Auto-fixing password...</h3>";
        $updateResult = $conn->query("UPDATE users SET password = '$newHash' WHERE email = 'ilham@indoceancrew.com'");
        if ($updateResult) {
            echo "<p style='color:green'>✅ Password updated! Try login again.</p>";
        } else {
            echo "<p style='color:red'>❌ Update failed: " . $conn->error . "</p>";
        }
    }
}

// Show roles
echo "<h3>Roles:</h3>";
$roles = $conn->query("SELECT * FROM roles ORDER BY id");
while ($r = $roles->fetch_assoc()) {
    echo "ID: {$r['id']}, Name: {$r['name']}<br>";
}

$conn->close();
?>
<hr>
<p><a href="<?= str_replace('/check_db.php', '/login', $_SERVER['REQUEST_URI']) ?>">🔙 Back to Login</a></p>
<p style="color:orange">⚠️ DELETE THIS FILE AFTER USE!</p>
