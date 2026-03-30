<?php
/**
 * PT Indo Ocean - Database Cleanup Script
 * Tries multiple DB hosts to find the working one
 * DELETE THIS FILE AFTER USE!
 */

define('BASEPATH', __DIR__ . '/');
define('APPPATH', BASEPATH . 'app/');

if (file_exists(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
    if (class_exists('Dotenv\\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(BASEPATH);
        $dotenv->safeLoad();
    }
}

$dbConfig = require APPPATH . 'Config/Database.php';
$cfg = $dbConfig['default'];
$recCfg = $dbConfig['recruitment'];

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🧹 Database Cleanup</h1><pre>";

// Try multiple hosts since Docker networking varies
$hostsToTry = [$cfg['hostname'], '127.0.0.1', 'localhost', 'mysql', 'indoocean_mysql'];
$erpDb = null;

foreach ($hostsToTry as $host) {
    echo "Trying host: $host ... ";
    $erpDb = @new mysqli($host, $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
    if (!$erpDb->connect_error) {
        echo "✅ Connected!\n\n";
        break;
    }
    echo "❌ {$erpDb->connect_error}\n";
    $erpDb = null;
}

if (!$erpDb) {
    die("\n❌ Cannot connect to ERP database with any host. Check MySQL is running.");
}

$erpDb->query("SET FOREIGN_KEY_CHECKS = 0");

$erpTables = [
    'payroll_items', 'payroll_periods',
    'contract_deductions', 'contract_salaries', 'contract_taxes',
    'contract_approvals', 'contract_logs', 'contract_documents', 'contracts',
    'crew_skills', 'crew_experiences', 'crew_documents', 'crews',
    'admin_checklists',
    'finance_invoice_items', 'finance_invoices', 'finance_payments',
    'finance_journal_entries', 'finance_journal_items',
    'activity_logs', 'notifications'
];

foreach ($erpTables as $table) {
    $r = @$erpDb->query("TRUNCATE TABLE `$table`");
    if ($r) { echo "  ✅ $table\n"; }
    else { @$erpDb->query("DELETE FROM `$table`"); echo "  ⚠️ $table\n"; }
}

$erpDb->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n--- ERP Verify ---\n";
foreach (['crews','contracts','payroll_items','admin_checklists'] as $t) {
    $r = @$erpDb->query("SELECT COUNT(*) as c FROM `$t`");
    echo "  $t: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
}

// Find working host for recruitment
$workingHost = $erpDb->host_info;
$hostUsed = $cfg['hostname']; // will be overridden
foreach ($hostsToTry as $h) {
    $test = @new mysqli($h, $recCfg['username'], $recCfg['password'], $recCfg['database'], $recCfg['port']);
    if (!$test->connect_error) { $hostUsed = $h; $test->close(); break; }
    @$test->close();
}

$erpDb->close();

echo "\n━━━━━━━━━━━━━━━━━━\n\n";
$recDb = @new mysqli($hostUsed, $recCfg['username'], $recCfg['password'], $recCfg['database'], $recCfg['port']);
if ($recDb->connect_error) {
    echo "⚠️ Recruitment DB: " . $recDb->connect_error . "\n";
} else {
    echo "✅ Connected to Recruitment: {$recCfg['database']}\n\n";
    $recDb->query("SET FOREIGN_KEY_CHECKS = 0");
    @$recDb->query("DELETE FROM applicant_profiles WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM documents WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    
    foreach (['application_assignments','application_status_history','pipeline_requests','status_change_requests','job_claim_requests','medical_checkups','interview_answers','interview_sessions','archived_applications','applicant_documents','email_logs','applications'] as $t) {
        $r = @$recDb->query("TRUNCATE TABLE `$t`");
        if ($r) { echo "  ✅ $t\n"; }
        else { @$recDb->query("DELETE FROM `$t`"); echo "  ⚠️ $t\n"; }
    }
    
    @$recDb->query("DELETE FROM users WHERE role_id = 3");
    echo "  ✅ Deleted " . $recDb->affected_rows . " applicant users\n";
    $recDb->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n--- Recruitment Verify ---\n";
    $r = @$recDb->query("SELECT COUNT(*) as c FROM applications"); echo "  applications: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
    $r = @$recDb->query("SELECT COUNT(*) as c FROM users WHERE role_id=3"); echo "  applicants: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
    $recDb->close();
}

echo "\n</pre><h2 style='color:green'>🎉 Done!</h2>";
echo "<p><b>⚠️ HAPUS: <code>rm /var/www/html/erp/cleanup_data.php</code></b></p>";
echo "<p><a href='/erp/contracts'>Contracts</a> | <a href='/erp/crews'>Crews</a> | <a href='/erp/payroll'>Payroll</a> | <a href='/recruitment/public/crewing/pipeline'>Pipeline</a></p>";
