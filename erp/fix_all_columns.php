<?php
/**
 * ERP Database Fix - Add ALL Missing Columns
 * Fixes missing columns in existing tables
 * SECURITY: Can only be run from CLI
 */
if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    echo 'Page not found';
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'erp_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<!DOCTYPE html><html><head><title>ERP Database Fix</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #0A2463; }
.success { color: green; background: #d4edda; padding: 10px; margin: 5px 0; border-radius: 5px; }
.error { color: red; background: #f8d7da; padding: 10px; margin: 5px 0; border-radius: 5px; }
.info { color: #0c5460; background: #d1ecf1; padding: 10px; margin: 5px 0; border-radius: 5px; }
.skip { color: #856404; background: #fff3cd; padding: 10px; margin: 5px 0; border-radius: 5px; }
</style>
</head><body><h1>🔧 ERP Database Fix - Missing Columns</h1>";

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result && $result->num_rows > 0;
}

// Function to add column if not exists
function addColumn($conn, $table, $column, $definition) {
    if (!columnExists($conn, $table, $column)) {
        $sql = "ALTER TABLE $table ADD COLUMN $column $definition";
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Added column <strong>$column</strong> to <strong>$table</strong></div>";
            return true;
        } else {
            echo "<div class='error'>❌ Failed to add $column to $table: " . $conn->error . "</div>";
            return false;
        }
    } else {
        echo "<div class='skip'>⚠️ Column <strong>$column</strong> already exists in <strong>$table</strong></div>";
        return true;
    }
}

// ============================================
// FIX CLIENTS TABLE
// ============================================
echo "<h2>📋 Fixing 'clients' table...</h2>";

$clientColumns = [
    ['city', 'VARCHAR(100) NULL AFTER address'],
    ['postal_code', 'VARCHAR(20) NULL AFTER city'],
    ['country', 'VARCHAR(100) NULL AFTER postal_code'],
    ['email', 'VARCHAR(255) NULL AFTER country'],
    ['phone', 'VARCHAR(50) NULL AFTER email'],
    ['website', 'VARCHAR(255) NULL AFTER phone'],
    ['contact_person', 'VARCHAR(100) NULL AFTER website'],
    ['contact_email', 'VARCHAR(255) NULL AFTER contact_person'],
    ['contact_phone', 'VARCHAR(50) NULL AFTER contact_email'],
    ['notes', 'TEXT NULL'],
    ['is_active', 'TINYINT(1) DEFAULT 1'],
    ['short_name', 'VARCHAR(50) NULL AFTER name'],
    ['address', 'TEXT NULL AFTER short_name'],
    ['client_rate', 'DECIMAL(10,2) DEFAULT 0.00'],
];

foreach ($clientColumns as $col) {
    addColumn($conn, 'clients', $col[0], $col[1]);
}

// ============================================
// FIX VESSELS TABLE
// ============================================
echo "<h2>🚢 Fixing 'vessels' table...</h2>";

$vesselColumns = [
    ['imo_number', 'VARCHAR(20) NULL'],
    ['vessel_type_id', 'INT NULL'],
    ['flag_state_id', 'INT NULL'],
    ['client_id', 'INT NULL'],
    ['gross_tonnage', 'DECIMAL(12,2) NULL'],
    ['dwt', 'DECIMAL(12,2) NULL'],
    ['year_built', 'INT NULL'],
    ['call_sign', 'VARCHAR(20) NULL'],
    ['mmsi', 'VARCHAR(20) NULL'],
    ['engine_type', 'VARCHAR(100) NULL'],
    ['crew_capacity', 'INT DEFAULT 25'],
    ['status', "ENUM('active', 'maintenance', 'laid_up', 'sold') DEFAULT 'active'"],
    ['notes', 'TEXT NULL'],
    ['is_active', 'TINYINT(1) DEFAULT 1'],
];

foreach ($vesselColumns as $col) {
    addColumn($conn, 'vessels', $col[0], $col[1]);
}

// ============================================
// FIX CONTRACTS TABLE
// ============================================
echo "<h2>📝 Fixing 'contracts' table...</h2>";

$contractColumns = [
    ['contract_type', "ENUM('temporary', 'fixed', 'permanent') DEFAULT 'fixed'"],
    ['duration_months', 'INT NULL'],
    ['actual_sign_off_date', 'DATE NULL'],
    ['embarkation_port', 'VARCHAR(200) NULL'],
    ['disembarkation_port', 'VARCHAR(200) NULL'],
    ['is_renewal', 'TINYINT(1) DEFAULT 0'],
    ['previous_contract_id', 'INT NULL'],
    ['termination_reason', 'TEXT NULL'],
    ['created_by', 'INT NULL'],
    ['updated_by', 'INT NULL'],
];

foreach ($contractColumns as $col) {
    addColumn($conn, 'contracts', $col[0], $col[1]);
}

// ============================================
// FIX CONTRACT_SALARIES TABLE
// ============================================
echo "<h2>💰 Fixing 'contract_salaries' table...</h2>";

$salaryColumns = [
    ['exchange_rate', 'DECIMAL(15,4) DEFAULT 1.0000'],
    ['overtime_allowance', 'DECIMAL(12,2) DEFAULT 0'],
    ['leave_pay', 'DECIMAL(12,2) DEFAULT 0'],
    ['bonus', 'DECIMAL(12,2) DEFAULT 0'],
    ['other_allowance', 'DECIMAL(12,2) DEFAULT 0'],
    ['notes', 'TEXT NULL'],
    ['client_rate', 'DECIMAL(12,2) DEFAULT 0 COMMENT "Rate charged to client"'],
    ['currency_id', 'INT DEFAULT 1'],
    ['basic_salary', 'DECIMAL(12,2) DEFAULT 0'],
    ['total_monthly', 'DECIMAL(12,2) DEFAULT 0'],
];

foreach ($salaryColumns as $col) {
    addColumn($conn, 'contract_salaries', $col[0], $col[1]);
}

// ============================================
// FIX USERS TABLE
// ============================================
echo "<h2>👤 Fixing 'users' table...</h2>";

$userColumns = [
    ['full_name', 'VARCHAR(100) NULL'],
    ['role', "VARCHAR(50) DEFAULT 'user'"],
    ['status', "VARCHAR(20) DEFAULT 'active'"],
    ['is_active', 'TINYINT(1) DEFAULT 1'],
    ['last_login', 'TIMESTAMP NULL'],
    ['login_attempts', 'INT DEFAULT 0'],
    ['locked_until', 'TIMESTAMP NULL'],
    ['password_reset_token', 'VARCHAR(255) NULL'],
    ['password_reset_expires', 'TIMESTAMP NULL'],
    ['two_factor_enabled', 'TINYINT(1) DEFAULT 0'],
    ['avatar', 'VARCHAR(255) NULL'],
    ['phone', 'VARCHAR(50) NULL'],
];

foreach ($userColumns as $col) {
    addColumn($conn, 'users', $col[0], $col[1]);
}

// ============================================
// FIX CURRENCIES TABLE
// ============================================
echo "<h2>💱 Fixing 'currencies' table...</h2>";

$currencyColumns = [
    ['symbol', 'VARCHAR(5) NULL'],
    ['is_active', 'TINYINT(1) DEFAULT 1'],
];

foreach ($currencyColumns as $col) {
    addColumn($conn, 'currencies', $col[0], $col[1]);
}

// ============================================
// FIX RANKS TABLE
// ============================================
echo "<h2>⭐ Fixing 'ranks' table...</h2>";

$rankColumns = [
    ['department', "ENUM('deck', 'engine', 'catering', 'other') DEFAULT 'deck'"],
    ['level', 'INT DEFAULT 0'],
    ['is_officer', 'TINYINT(1) DEFAULT 0'],
    ['is_active', 'TINYINT(1) DEFAULT 1'],
];

foreach ($rankColumns as $col) {
    addColumn($conn, 'ranks', $col[0], $col[1]);
}

// ============================================
// ENSURE DEFAULT DATA EXISTS
// ============================================
echo "<h2>📊 Checking Default Data...</h2>";

// Check if currencies have data
$result = $conn->query("SELECT COUNT(*) as cnt FROM currencies");
if ($result && $result->fetch_assoc()['cnt'] == 0) {
    $conn->query("INSERT INTO currencies (code, name, symbol) VALUES 
        ('USD', 'US Dollar', '\$'),
        ('IDR', 'Indonesian Rupiah', 'Rp'),
        ('SGD', 'Singapore Dollar', 'S\$'),
        ('EUR', 'Euro', '€')");
    echo "<div class='success'>✅ Added default currencies</div>";
}

// Check if vessel_types have data  
$result = $conn->query("SELECT COUNT(*) as cnt FROM vessel_types");
if ($result && $result->fetch_assoc()['cnt'] == 0) {
    $conn->query("INSERT INTO vessel_types (name) VALUES 
        ('Container Ship'), ('Bulk Carrier'), ('Oil Tanker'), ('LNG Carrier'),
        ('Cargo Ship'), ('Cruise Ship'), ('Offshore Vessel'), ('Passenger Ship')");
    echo "<div class='success'>✅ Added default vessel types</div>";
}

// Check if ranks have data
$result = $conn->query("SELECT COUNT(*) as cnt FROM ranks");
if ($result && $result->fetch_assoc()['cnt'] == 0) {
    $conn->query("INSERT INTO ranks (name, department, level, is_officer) VALUES 
        ('Captain', 'deck', 1, 1),
        ('Chief Officer', 'deck', 2, 1),
        ('2nd Officer', 'deck', 3, 1),
        ('3rd Officer', 'deck', 4, 1),
        ('Bosun', 'deck', 5, 0),
        ('Able Seaman (AB)', 'deck', 6, 0),
        ('Chief Engineer', 'engine', 1, 1),
        ('2nd Engineer', 'engine', 2, 1),
        ('3rd Engineer', 'engine', 3, 1),
        ('4th Engineer', 'engine', 4, 1),
        ('Chief Cook', 'catering', 1, 0),
        ('Steward', 'catering', 2, 0)");
    echo "<div class='success'>✅ Added default ranks</div>";
}

// Check if flag_states have data
$result = $conn->query("SELECT COUNT(*) as cnt FROM flag_states");
if ($result && $result->fetch_assoc()['cnt'] == 0) {
    $conn->query("INSERT INTO flag_states (code, name, emoji) VALUES 
        ('ID', 'Indonesia', '🇮🇩'),
        ('SG', 'Singapore', '🇸🇬'),
        ('PA', 'Panama', '🇵🇦'),
        ('LR', 'Liberia', '🇱🇷'),
        ('MH', 'Marshall Islands', '🇲🇭'),
        ('HK', 'Hong Kong', '🇭🇰')");
    echo "<div class='success'>✅ Added default flag states</div>";
}

echo "<br><div class='success'><h3>🎉 All Fixes Applied!</h3>
<p>Database has been updated with all required columns and default data.</p>
<a href='/erp/' style='color: #0A2463; font-weight: bold;'>← Back to ERP Dashboard</a>
<br><br>
<a href='/erp/clients' style='color: #0A2463;'>→ Test Clients Page</a> | 
<a href='/erp/vessels' style='color: #0A2463;'>→ Test Vessels Page</a> | 
<a href='/erp/contracts' style='color: #0A2463;'>→ Test Contracts Page</a>
</div>";

echo "</body></html>";
$conn->close();
?>
