<?php
/**
 * PT Indo Ocean - Database Cleanup Script
 * Run via browser: https://indooceancrewservice.com/erp/cleanup_data.php
 * DELETE THIS FILE AFTER USE!
 */

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🧹 Database Cleanup</h1><pre>";

// Load the database config (returns array with 'default' and 'recruitment')
$dbConfig = require __DIR__ . '/app/Config/Database.php';

$erpCfg = $dbConfig['default'];
$recCfg = $dbConfig['recruitment'];

// Connect to ERP database
$erpDb = new mysqli($erpCfg['hostname'], $erpCfg['username'], $erpCfg['password'], $erpCfg['database'], $erpCfg['port']);
if ($erpDb->connect_error) {
    die("❌ ERP DB connection failed: " . $erpDb->connect_error);
}
echo "✅ Connected to ERP DB: {$erpCfg['database']} @ {$erpCfg['hostname']}\n\n";

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
    $result = @$erpDb->query("TRUNCATE TABLE `$table`");
    if ($result) {
        echo "  ✅ Truncated: $table\n";
    } else {
        $result2 = @$erpDb->query("DELETE FROM `$table`");
        echo ($result2 ? "  ✅ Deleted from: $table\n" : "  ⚠️ Skipped: $table ({$erpDb->error})\n");
    }
}

$erpDb->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n✅ ERP cleanup done!\n\n";

// Verify
foreach (['crews', 'contracts', 'payroll_items', 'admin_checklists'] as $t) {
    $r = @$erpDb->query("SELECT COUNT(*) as cnt FROM `$t`");
    echo "  $t: " . ($r ? $r->fetch_assoc()['cnt'] : 'N/A') . " records\n";
}
$erpDb->close();

// Connect to Recruitment database
echo "\n━━━━━━━━━━━━━━━━━━\n\n";
$recDb = @new mysqli($recCfg['hostname'], $recCfg['username'], $recCfg['password'], $recCfg['database'], $recCfg['port']);
if ($recDb->connect_error) {
    echo "⚠️ Recruitment DB not accessible: " . $recDb->connect_error . "\n";
} else {
    echo "✅ Connected to Recruitment DB: {$recCfg['database']}\n\n";
    
    $recDb->query("SET FOREIGN_KEY_CHECKS = 0");
    
    @$recDb->query("DELETE FROM applicant_profiles WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM documents WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    
    $recTables = [
        'application_assignments', 'application_status_history',
        'pipeline_requests', 'status_change_requests', 'job_claim_requests',
        'medical_checkups', 'interview_answers', 'interview_sessions',
        'archived_applications', 'applicant_documents', 'email_logs',
        'applications'
    ];
    
    foreach ($recTables as $table) {
        $result = @$recDb->query("TRUNCATE TABLE `$table`");
        if ($result) {
            echo "  ✅ Truncated: $table\n";
        } else {
            $result2 = @$recDb->query("DELETE FROM `$table`");
            echo ($result2 ? "  ✅ Deleted from: $table\n" : "  ⚠️ Skipped: $table ({$recDb->error})\n");
        }
    }
    
    $delUsers = @$recDb->query("DELETE FROM users WHERE role_id = 3");
    echo "  ✅ Deleted " . $recDb->affected_rows . " applicant users\n";
    
    $recDb->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "\n✅ Recruitment cleanup done!\n\n";
    
    $r = @$recDb->query("SELECT COUNT(*) as cnt FROM applications");
    echo "  applications: " . ($r ? $r->fetch_assoc()['cnt'] : 'N/A') . " records\n";
    $r = @$recDb->query("SELECT COUNT(*) as cnt FROM users WHERE role_id = 3");
    echo "  applicant users: " . ($r ? $r->fetch_assoc()['cnt'] : 'N/A') . " records\n";
    
    $recDb->close();
}

echo "\n</pre>";
echo "<h2 style='color:green'>🎉 Cleanup Complete!</h2>";
echo "<p><b>⚠️ HAPUS FILE INI SETELAH SELESAI: <code>rm /var/www/html/erp/cleanup_data.php</code></b></p>";
echo "<p><a href='/erp/contracts'>→ Contracts</a> | <a href='/erp/crews'>→ Crews</a> | <a href='/erp/payroll'>→ Payroll</a> | <a href='/recruitment/public/crewing/pipeline'>→ Pipeline</a></p>";
