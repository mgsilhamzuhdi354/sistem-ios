<?php
$c = new mysqli('localhost', 'root', '', 'erp_db');
if ($c->connect_error) die('Connection failed: ' . $c->connect_error);

$sql = "ALTER TABLE crews MODIFY COLUMN status ENUM('available','onboard','standby','terminated','pending_approval','rejected','contracted') DEFAULT 'available'";
if ($c->query($sql)) {
    echo "OK: crews.status column altered successfully\n";
} else {
    echo "ERROR: " . $c->error . "\n";
}

// Also check crew_documents table exists
$result = $c->query("SHOW TABLES LIKE 'crew_documents'");
if ($result->num_rows == 0) {
    echo "Creating crew_documents table...\n";
    $createSql = "CREATE TABLE crew_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crew_id INT NOT NULL,
        document_type VARCHAR(50) NOT NULL,
        document_name VARCHAR(255) DEFAULT NULL,
        document_number VARCHAR(100) DEFAULT NULL,
        file_path VARCHAR(500) DEFAULT NULL,
        file_name VARCHAR(255) DEFAULT NULL,
        file_size INT DEFAULT NULL,
        mime_type VARCHAR(100) DEFAULT NULL,
        issue_date DATE DEFAULT NULL,
        expiry_date DATE DEFAULT NULL,
        issuing_authority VARCHAR(255) DEFAULT NULL,
        status ENUM('pending','valid','expired','expiring_soon') DEFAULT 'pending',
        uploaded_by INT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_crew_id (crew_id),
        INDEX idx_document_type (document_type),
        INDEX idx_expiry_date (expiry_date),
        FOREIGN KEY (crew_id) REFERENCES crews(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($c->query($createSql)) {
        echo "OK: crew_documents table created\n";
    } else {
        echo "ERROR creating crew_documents: " . $c->error . "\n";
    }
} else {
    echo "OK: crew_documents table already exists\n";
}

$c->close();
echo "\nDone!\n";
