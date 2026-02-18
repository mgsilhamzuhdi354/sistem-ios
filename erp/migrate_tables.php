<?php
/**
 * ERP Database Migration - Add ALL Missing Tables
 * Run this to create tables that are missing from the database
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<!DOCTYPE html><html><head><title>ERP Migration</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1 { color: #0A2463; }
.success { color: green; background: #d4edda; padding: 10px; margin: 5px 0; border-radius: 5px; }
.error { color: red; background: #f8d7da; padding: 10px; margin: 5px 0; border-radius: 5px; }
.info { color: #0c5460; background: #d1ecf1; padding: 10px; margin: 5px 0; border-radius: 5px; }
.skip { color: #856404; background: #fff3cd; padding: 10px; margin: 5px 0; border-radius: 5px; }
</style>
</head><body><h1>📦 ERP Database Migration (Complete)</h1>";

// Check existing tables
$existing = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $existing[] = $row[0];
}
echo "<div class='info'>📋 Existing tables: " . implode(", ", $existing) . "</div>";

// All tables that should exist
$migrations = [];

// Settings table
if (!in_array('settings', $existing)) {
    $migrations['settings'] = "
    CREATE TABLE settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) DEFAULT 'general',
        description VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Notifications table
if (!in_array('notifications', $existing)) {
    $migrations['notifications'] = "
    CREATE TABLE notifications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NULL,
        type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
        title VARCHAR(255) NOT NULL,
        message TEXT,
        link VARCHAR(500),
        data JSON,
        is_read TINYINT(1) DEFAULT 0,
        read_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contract salaries table
if (!in_array('contract_salaries', $existing)) {
    $migrations['contract_salaries'] = "
    CREATE TABLE contract_salaries (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_id INT NOT NULL,
        currency_id INT NOT NULL DEFAULT 1,
        exchange_rate DECIMAL(15,4) DEFAULT 1.0000,
        basic_salary DECIMAL(12,2) NOT NULL DEFAULT 0,
        overtime_allowance DECIMAL(12,2) DEFAULT 0,
        leave_pay DECIMAL(12,2) DEFAULT 0,
        bonus DECIMAL(12,2) DEFAULT 0,
        other_allowance DECIMAL(12,2) DEFAULT 0,
        total_monthly DECIMAL(12,2) GENERATED ALWAYS AS (basic_salary + overtime_allowance + leave_pay + bonus + other_allowance) STORED,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
        FOREIGN KEY (currency_id) REFERENCES currencies(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contracts table
if (!in_array('contracts', $existing)) {
    $migrations['contracts'] = "
    CREATE TABLE contracts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_no VARCHAR(50) NOT NULL UNIQUE,
        crew_id INT NOT NULL,
        crew_name VARCHAR(100) NOT NULL,
        vessel_id INT NOT NULL,
        client_id INT NOT NULL,
        rank_id INT NOT NULL,
        contract_type ENUM('temporary', 'fixed', 'permanent') DEFAULT 'fixed',
        status ENUM('draft', 'pending_approval', 'active', 'onboard', 'completed', 'terminated', 'cancelled') DEFAULT 'draft',
        sign_on_date DATE,
        sign_off_date DATE,
        duration_months INT,
        actual_sign_off_date DATE,
        embarkation_port VARCHAR(200),
        disembarkation_port VARCHAR(200),
        is_renewal TINYINT(1) DEFAULT 0,
        previous_contract_id INT,
        notes TEXT,
        termination_reason TEXT,
        created_by INT,
        updated_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (vessel_id) REFERENCES vessels(id),
        FOREIGN KEY (client_id) REFERENCES clients(id),
        FOREIGN KEY (rank_id) REFERENCES ranks(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contract taxes table
if (!in_array('contract_taxes', $existing)) {
    $migrations['contract_taxes'] = "
    CREATE TABLE contract_taxes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_id INT NOT NULL,
        tax_type ENUM('pph21', 'pph21_non_npwp', 'exempt', 'foreign') DEFAULT 'pph21',
        npwp_number VARCHAR(30),
        tax_rate DECIMAL(5,2) DEFAULT 5.00,
        monthly_tax_amount DECIMAL(12,2) DEFAULT 0,
        effective_from DATE,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contract deductions table
if (!in_array('contract_deductions', $existing)) {
    $migrations['contract_deductions'] = "
    CREATE TABLE contract_deductions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_id INT NOT NULL,
        deduction_type ENUM('insurance', 'medical', 'training', 'advance', 'loan', 'other') NOT NULL,
        description VARCHAR(255),
        amount DECIMAL(12,2) NOT NULL,
        currency_id INT DEFAULT 1,
        is_recurring TINYINT(1) DEFAULT 1,
        recurring_months INT DEFAULT NULL,
        deducted_count INT DEFAULT 0,
        start_date DATE,
        end_date DATE,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE,
        FOREIGN KEY (currency_id) REFERENCES currencies(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Payroll periods table
if (!in_array('payroll_periods', $existing)) {
    $migrations['payroll_periods'] = "
    CREATE TABLE payroll_periods (
        id INT PRIMARY KEY AUTO_INCREMENT,
        period_month INT NOT NULL,
        period_year INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status ENUM('draft', 'processing', 'completed', 'locked') DEFAULT 'draft',
        total_crew INT DEFAULT 0,
        total_gross DECIMAL(15,2) DEFAULT 0,
        total_deductions DECIMAL(15,2) DEFAULT 0,
        total_tax DECIMAL(15,2) DEFAULT 0,
        total_net DECIMAL(15,2) DEFAULT 0,
        processed_by INT,
        processed_at TIMESTAMP NULL,
        locked_by INT,
        locked_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_period (period_month, period_year)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Payroll items table
if (!in_array('payroll_items', $existing)) {
    $migrations['payroll_items'] = "
    CREATE TABLE payroll_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        payroll_period_id INT NOT NULL,
        contract_id INT NOT NULL,
        crew_name VARCHAR(100),
        rank_name VARCHAR(100),
        vessel_name VARCHAR(200),
        basic_salary DECIMAL(12,2) DEFAULT 0,
        overtime DECIMAL(12,2) DEFAULT 0,
        leave_pay DECIMAL(12,2) DEFAULT 0,
        bonus DECIMAL(12,2) DEFAULT 0,
        other_allowance DECIMAL(12,2) DEFAULT 0,
        gross_salary DECIMAL(12,2) DEFAULT 0,
        insurance DECIMAL(12,2) DEFAULT 0,
        medical DECIMAL(12,2) DEFAULT 0,
        advance DECIMAL(12,2) DEFAULT 0,
        other_deductions DECIMAL(12,2) DEFAULT 0,
        total_deductions DECIMAL(12,2) DEFAULT 0,
        tax_type VARCHAR(20),
        tax_amount DECIMAL(12,2) DEFAULT 0,
        net_salary DECIMAL(12,2) DEFAULT 0,
        currency_code VARCHAR(3) DEFAULT 'USD',
        status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
        payment_date DATE,
        payment_reference VARCHAR(100),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (payroll_period_id) REFERENCES payroll_periods(id),
        FOREIGN KEY (contract_id) REFERENCES contracts(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contract logs table
if (!in_array('contract_logs', $existing)) {
    $migrations['contract_logs'] = "
    CREATE TABLE contract_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        field_changed VARCHAR(100),
        old_value TEXT,
        new_value TEXT,
        user_id INT,
        user_name VARCHAR(100),
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contract approvals table
if (!in_array('contract_approvals', $existing)) {
    $migrations['contract_approvals'] = "
    CREATE TABLE contract_approvals (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_id INT NOT NULL,
        approval_level ENUM('crewing', 'hr', 'director') NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        approver_id INT,
        approver_name VARCHAR(100),
        approved_at TIMESTAMP NULL,
        notes TEXT,
        rejection_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Contract documents table
if (!in_array('contract_documents', $existing)) {
    $migrations['contract_documents'] = "
    CREATE TABLE contract_documents (
        id INT PRIMARY KEY AUTO_INCREMENT,
        contract_id INT NOT NULL,
        document_type ENUM('contract', 'amendment', 'termination', 'other') DEFAULT 'contract',
        language ENUM('id', 'en') DEFAULT 'id',
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size INT,
        is_signed TINYINT(1) DEFAULT 0,
        signed_at TIMESTAMP NULL,
        signature_type ENUM('manual', 'digital') DEFAULT 'manual',
        generated_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
}

// Run migrations
echo "<h2>🔧 Running Migrations...</h2>";

if (empty($migrations)) {
    echo "<div class='success'>✅ All tables already exist! Database is up to date.</div>";
} else {
    foreach ($migrations as $table => $sql) {
        echo "<div class='info'>Creating table: <strong>$table</strong>...</div>";
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Table <strong>$table</strong> created successfully!</div>";
        } else {
            echo "<div class='error'>❌ Error creating <strong>$table</strong>: " . $conn->error . "</div>";
        }
    }
}

// Insert default settings if settings table was just created
if (in_array('settings', $migrations) || (in_array('settings', $existing) && $conn->query("SELECT COUNT(*) as c FROM settings")->fetch_assoc()['c'] == 0)) {
    echo "<h2>📝 Inserting Default Settings...</h2>";
    $defaultSettings = [
        ['company_name', 'PT Indo Ocean', 'general', 'Company name'],
        ['company_email', 'info@indoocean.com', 'general', 'Company email'],
        ['company_phone', '+62-21-12345678', 'general', 'Company phone'],
        ['company_address', 'Jakarta, Indonesia', 'general', 'Company address'],
        ['default_currency', 'USD', 'currency', 'Default currency code'],
        ['currency_position', 'before', 'currency', 'Currency symbol position'],
        ['default_tax_rate', '5', 'tax', 'Default tax rate percentage'],
        ['contract_prefix', 'CTR', 'contract', 'Contract number prefix'],
        ['default_duration', '6', 'contract', 'Default contract duration in months'],
        ['expiry_alert_days', '30,14,7', 'contract', 'Days before expiry to alert'],
        ['payroll_day', '25', 'payroll', 'Default payroll date'],
        ['email_notifications', '1', 'notification', 'Enable email notifications'],
    ];
    
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES (?, ?, ?, ?)");
    foreach ($defaultSettings as $setting) {
        $stmt->bind_param("ssss", $setting[0], $setting[1], $setting[2], $setting[3]);
        $stmt->execute();
    }
    echo "<div class='success'>✅ Default settings inserted!</div>";
}

echo "<br><div class='success'><h3>🎉 Migration Complete!</h3>
<p>All required tables have been created or verified.</p>
<a href='/erp/' style='color: #0A2463; font-weight: bold;'>← Back to ERP Dashboard</a></div>";
echo "</body></html>";

$conn->close();
?>
