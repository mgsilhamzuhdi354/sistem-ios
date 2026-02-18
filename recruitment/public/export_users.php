<?php
/**
 * Export Users - PT Indo Ocean Crew Services Recruitment
 * Menampilkan semua data user dari database recruitment
 */

// Database credentials (Laragon local)
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'recruitment_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Export Users - Recruitment System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #0A2463; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0A2463; color: white; }
        tr:hover { background: #f9f9f9; }
        .password-note { background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>📋 Data User Recruitment System</h1>
    <div class='password-note'>
        <strong>⚠️ Catatan:</strong> Password disimpan dalam format hash (bcrypt) untuk keamanan. 
        Jika perlu reset password, gunakan fitur forgot password atau ubah langsung di database.
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Nama Lengkap</th>
            <th>Telepon</th>
            <th>Role</th>
            <th>Status</th>
            <th>Dibuat</th>
        </tr>";

$result = $conn->query("SELECT id, email, full_name, phone, role, status, created_at FROM users ORDER BY id");

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status = $row["status"] == 1 ? '✅ Active' : '❌ Inactive';
        echo "<tr>
            <td>{$row["id"]}</td>
            <td>{$row["email"]}</td>
            <td>{$row["full_name"]}</td>
            <td>{$row["phone"]}</td>
            <td>{$row["role"]}</td>
            <td>{$status}</td>
            <td>{$row["created_at"]}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>Tidak ada data user</td></tr>";
}

echo "</table>
<br>
<p><strong>Total Users:</strong> " . $result->num_rows . "</p>
</body>
</html>";

$conn->close();
?>
