<?php
$conn = new mysqli('localhost', 'root', '', 'erp_db');
$result = $conn->query("SHOW COLUMNS FROM crews WHERE Field = 'status'");
$row = $result->fetch_assoc();
echo json_encode($row, JSON_PRETTY_PRINT);
$conn->close();
