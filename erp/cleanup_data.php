<?php
define('BASEPATH', __DIR__ . '/');
define('APPPATH', BASEPATH . 'app/');
if (file_exists(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
    if (class_exists('Dotenv\\Dotenv')) { $dotenv = Dotenv\Dotenv::createImmutable(BASEPATH); $dotenv->safeLoad(); }
}
$dbConfig = require APPPATH . 'Config/Database.php';
$cfg = $dbConfig['default'];
$recCfg = $dbConfig['recruitment'];

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🧹 Database Cleanup</h1><pre>";

// Disable mysqli exceptions so we can try multiple hosts
mysqli_report(MYSQLI_REPORT_OFF);

$hostsToTry = [$cfg['hostname'], '127.0.0.1', 'localhost', 'mysql', 'indoocean_mysql'];
$erpDb = null;
foreach ($hostsToTry as $host) {
    echo "Trying: $host ... ";
    try {
        $conn = new mysqli($host, $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
        if (!$conn->connect_error) { $erpDb = $conn; echo "✅\n\n"; break; }
        echo "❌ {$conn->connect_error}\n";
    } catch (Exception $e) { echo "❌ {$e->getMessage()}\n"; }
}
if (!$erpDb) die("\n❌ Cannot connect to any DB host.");

$erpDb->query("SET FOREIGN_KEY_CHECKS = 0");
foreach (['payroll_items','payroll_periods','contract_deductions','contract_salaries','contract_taxes','contract_approvals','contract_logs','contract_documents','contracts','crew_skills','crew_experiences','crew_documents','crews','admin_checklists','finance_invoice_items','finance_invoices','finance_payments','finance_journal_entries','finance_journal_items','activity_logs','notifications'] as $t) {
    $r = @$erpDb->query("TRUNCATE TABLE `$t`"); echo ($r ? "  ✅ $t\n" : "  ⚠️ $t\n");
}
$erpDb->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n--- Verify ---\n";
foreach (['crews','contracts','payroll_items','admin_checklists'] as $t) {
    $r = @$erpDb->query("SELECT COUNT(*) as c FROM `$t`"); echo "  $t: ".($r?$r->fetch_assoc()['c']:'?')."\n";
}

// Find working host
$wh = $cfg['hostname'];
foreach ($hostsToTry as $h) {
    try { $t = new mysqli($h,$recCfg['username'],$recCfg['password'],$recCfg['database'],$recCfg['port']); if(!$t->connect_error){$wh=$h;$t->close();break;} } catch(Exception $e){}
}
$erpDb->close();

echo "\n━━━━━━━━━━━━━━━━━━\n\n";
try {
    $recDb = new mysqli($wh,$recCfg['username'],$recCfg['password'],$recCfg['database'],$recCfg['port']);
} catch(Exception $e) { die("⚠️ Recruitment: ".$e->getMessage()); }
if($recDb->connect_error) die("⚠️ Recruitment: ".$recDb->connect_error);
echo "✅ Recruitment: {$recCfg['database']}\n\n";
$recDb->query("SET FOREIGN_KEY_CHECKS = 0");
@$recDb->query("DELETE FROM applicant_profiles WHERE user_id IN (SELECT id FROM users WHERE role_id=3)");
@$recDb->query("DELETE FROM documents WHERE user_id IN (SELECT id FROM users WHERE role_id=3)");
@$recDb->query("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE role_id=3)");
foreach(['application_assignments','application_status_history','pipeline_requests','status_change_requests','job_claim_requests','medical_checkups','interview_answers','interview_sessions','archived_applications','applicant_documents','email_logs','applications'] as $t) {
    $r=@$recDb->query("TRUNCATE TABLE `$t`"); echo ($r?"  ✅ $t\n":"  ⚠️ $t\n");
}
@$recDb->query("DELETE FROM users WHERE role_id=3");
echo "  ✅ Deleted ".$recDb->affected_rows." applicants\n";
$recDb->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n--- Verify ---\n";
$r=@$recDb->query("SELECT COUNT(*) as c FROM applications"); echo "  applications: ".($r?$r->fetch_assoc()['c']:'?')."\n";
$recDb->close();
echo "\n</pre><h2 style='color:green'>🎉 Done!</h2><p><b>HAPUS: rm /var/www/html/erp/cleanup_data.php</b></p>";
echo "<p><a href='/erp/contracts'>Contracts</a> | <a href='/erp/crews'>Crews</a> | <a href='/erp/payroll'>Payroll</a> | <a href='/recruitment/public/crewing/pipeline'>Pipeline</a></p>";
