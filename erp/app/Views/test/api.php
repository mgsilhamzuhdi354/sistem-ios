<?php
/**
 * API Integration Test Page
 */

require_once APPPATH . 'Libraries/HrisApi.php';

use App\Libraries\HrisApi;

$hrisApi = new HrisApi();

// Test results
$testResults = [];

// Test 1: Connection Test
$testResults['connection'] = [
    'name' => 'Connection Test',
    'result' => $hrisApi->testConnection(),
    'message' => ''
];

// Test 2: Get Employees
$employeeResult = $hrisApi->getEmployees();
$testResults['employees'] = [
    'name' => 'Get Employees',
    'result' => $employeeResult['success'],
    'message' => $employeeResult['success'] ? count($employeeResult['data']) . ' employees found' : ($employeeResult['error'] ?? 'Failed'),
    'data' => $employeeResult['success'] ? array_slice($employeeResult['data'], 0, 3) : null
];

// Test 3: Get Employee Summary
$summaryResult = $hrisApi->getEmployeeSummary();
$testResults['summary'] = [
    'name' => 'Get Employee Summary',
    'result' => $summaryResult['success'],
    'message' => $summaryResult['success'] ? 'Success' : ($summaryResult['error'] ?? 'Failed'),
    'data' => $summaryResult['data'] ?? null
];

// Test 4: Get Attendance Summary
$month = date('m');
$year = date('Y');
$attendanceResult = $hrisApi->getAttendanceSummary($month, $year);
$testResults['attendance'] = [
    'name' => 'Get Attendance Summary',
    'result' => $attendanceResult['success'],
    'message' => $attendanceResult['success'] ? 'Success for ' . date('F Y') : ($attendanceResult['error'] ?? 'Failed'),
    'data' => $attendanceResult['data'] ?? null
];

// Test 5: Get Payroll Summary
$payrollResult = $hrisApi->getPayrollSummary($month, $year);
$testResults['payroll'] = [
    'name' => 'Get Payroll Summary',
    'result' => $payrollResult['success'],
    'message' => $payrollResult['success'] ? 'Success' : ($payrollResult['error'] ?? 'Failed'),
    'data' => $payrollResult['data'] ?? null
];

$totalTests = count($testResults);
$passedTests = count(array_filter($testResults, fn($t) => $t['result']));
?>

<div class="page-header">
    <h1><i class="fas fa-vial" style="color: var(--info);"></i> API Integration Test</h1>
    <p>Testing connectivity to HRIS API</p>
</div>

<!-- Test Summary -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-flask"></i></div>
        <div class="stat-info">
            <h3>
                <?= $totalTests ?>
            </h3>
            <p>Total Tests</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3>
                <?= $passedTests ?>
            </h3>
            <p>Passed</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon <?= $passedTests === $totalTests ? 'green' : 'red' ?>">
            <i class="fas fa-<?= $passedTests === $totalTests ? 'check' : 'times' ?>"></i>
        </div>
        <div class="stat-info">
            <h3>
                <?= round(($passedTests / $totalTests) * 100) ?>%
            </h3>
            <p>Success Rate</p>
        </div>
    </div>
</div>

<!-- Test Results -->
<div class="card">
    <h3 style="margin-bottom: 20px;"><i class="fas fa-clipboard-list"></i> Test Results</h3>

    <?php foreach ($testResults as $key => $test): ?>
        <div style="padding: 16px; border-left: 4px solid <?= $test['result'] ? 'var(--success)' : 'var(--danger)' ?>; 
                background: rgba(255,255,255,0.03); margin-bottom: 16px; border-radius: 4px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <h4 style="margin: 0;">
                    <i class="fas fa-<?= $test['result'] ? 'check-circle' : 'times-circle' ?>"
                        style="color: <?= $test['result'] ? 'var(--success)' : 'var(--danger)' ?>;"></i>
                    <?= $test['name'] ?>
                </h4>
                <span class="badge <?= $test['result'] ? 'badge-success' : 'badge-danger' ?>">
                    <?= $test['result'] ? 'PASS' : 'FAIL' ?>
                </span>
            </div>
            <p style="color: var(--text-secondary); margin: 0;">
                <?= htmlspecialchars($test['message']) ?>
            </p>

            <?php if ($test['result'] && isset($test['data']) && $test['data']): ?>
                <details style="margin-top: 12px;">
                    <summary style="cursor: pointer; color: var(--accent-gold); font-size: 13px;">
                        <i class="fas fa-code"></i> View Response Data
                    </summary>
                    <pre
                        style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: 4px; overflow: auto; margin-top: 8px; font-size: 12px;">
        <?= htmlspecialchars(json_encode($test['data'], JSON_PRETTY_PRINT)) ?>
                    </pre>
                </details>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- API Endpoints Reference -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 16px;"><i class="fas fa-book"></i> API Endpoints Reference</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Method</th>
                <th>Endpoint</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" style="background: rgba(16, 185, 129, 0.1); font-weight: 600;">Employee Endpoints</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/employees</code></td>
                <td>Get all employees (with filters)</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/employees/{id}</code></td>
                <td>Get employee by ID</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/employees/summary</code></td>
                <td>Get employee statistics</td>
            </tr>

            <tr>
                <td colspan="3" style="background: rgba(59, 130, 246, 0.1); font-weight: 600;">Attendance Endpoints</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/attendance</code></td>
                <td>Get attendance records</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/attendance/summary</code></td>
                <td>Get attendance summary</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/attendance/employee/{id}</code></td>
                <td>Get attendance by employee</td>
            </tr>

            <tr>
                <td colspan="3" style="background: rgba(245, 158, 11, 0.1); font-weight: 600;">Payroll Endpoints</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/payroll</code></td>
                <td>Get payroll records</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/payroll/summary</code></td>
                <td>Get payroll summary</td>
            </tr>
            <tr>
                <td><code style="color: var(--success);">GET</code></td>
                <td><code>/api/payroll/employee/{id}</code></td>
                <td>Get payroll by employee</td>
            </tr>
        </tbody>
    </table>
</div>

<div
    style="margin-top: 20px; padding: 16px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; border-left: 4px solid var(--info);">
    <p style="margin: 0;">
        <i class="fas fa-info-circle"></i>
        <strong>Note:</strong> API Base URL is configured in <code>HrisApi.php</code> as
        <code>http://localhost/absensi/aplikasiabsensibygerry/api</code>
    </p>
</div>