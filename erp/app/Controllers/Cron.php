<?php
/**
 * PT Indo Ocean - ERP System
 * Cron Controller - For automated tasks
 */

namespace App\Controllers;

require_once APPPATH . 'Models/PayrollModel.php';
require_once APPPATH . 'Libraries/EmailService.php';

use App\Models\PayrollPeriodModel;
use App\Models\PayrollItemModel;

class Cron extends BaseController
{
    /**
     * Auto-generate payroll for current month
     * Called via cron job or Windows Task Scheduler
     * 
     * URL: /cron/auto-payroll?key=YOUR_SECRET_KEY
     * 
     * Setup Windows Task Scheduler:
     * - Action: Start a program
     * - Program: curl or powershell
     * - Arguments: "http://localhost/PT_indoocean/erp%20sistem/cron/auto-payroll?key=YOUR_KEY"
     * - Trigger: Monthly on your payday (e.g., 25th at 00:00)
     */
    public function autoPayroll()
    {
        // Security: Check cron key
        $key = $_GET['key'] ?? '';
        $validKey = 'indoocean_cron_2024'; // Change this to a secure key
        
        if ($key !== $validKey) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid cron key']);
            return;
        }
        
        $month = (int)($_GET['month'] ?? date('n'));
        $year = (int)($_GET['year'] ?? date('Y'));
        
        try {
            $periodModel = new PayrollPeriodModel($this->db);
            $itemModel = new PayrollItemModel($this->db);
            
            // Get or create period for current month
            $period = $periodModel->getOrCreate($month, $year);
            
            // Generate payroll items
            $itemModel->generateForPeriod($period['id']);
            
            // Update period totals
            $periodModel->updateTotals($period['id']);
            
            // Get updated period data
            $period = $periodModel->find($period['id']);
            
            // Send email notification if configured
            $this->sendPayrollNotification($period, $month, $year);
            
            // Log success
            $this->logCronRun('auto_payroll', 'success', [
                'period_id' => $period['id'],
                'month' => $month,
                'year' => $year,
                'total_crew' => $period['total_crew'],
                'total_net' => $period['total_net']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Payroll generated successfully',
                'data' => [
                    'period' => $this->getMonthName($month) . ' ' . $year,
                    'total_crew' => $period['total_crew'],
                    'total_gross' => '$' . number_format($period['total_gross'], 2),
                    'total_net' => '$' . number_format($period['total_net'], 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            // Log error
            $this->logCronRun('auto_payroll', 'error', [
                'error' => $e->getMessage()
            ]);
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check contract expirations and send alerts
     * Called daily via cron
     */
    public function contractAlerts()
    {
        $key = $_GET['key'] ?? '';
        $validKey = 'indoocean_cron_2024';
        
        if ($key !== $validKey) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid cron key']);
            return;
        }
        
        require_once APPPATH . 'Models/ContractModel.php';
        $contractModel = new \App\Models\ContractModel($this->db);
        
        // Get contracts expiring in 7 days
        $expiring7 = $contractModel->getExpiring(7);
        // Get contracts expiring in 30 days
        $expiring30 = $contractModel->getExpiring(30);
        
        // Send notification email
        if (!empty($expiring7) || !empty($expiring30)) {
            $this->sendExpirationAlerts($expiring7, $expiring30);
        }
        
        echo json_encode([
            'success' => true,
            'expiring_7_days' => count($expiring7),
            'expiring_30_days' => count($expiring30)
        ]);
    }
    
    /**
     * Send payroll notification email
     */
    private function sendPayrollNotification($period, $month, $year)
    {
        try {
            if (class_exists('\\App\\Libraries\\EmailService')) {
                $emailService = new \App\Libraries\EmailService();
                
                $monthName = $this->getMonthName($month);
                $subject = "Payroll {$monthName} {$year} - Generated";
                
                $body = "
                <h2>Payroll {$monthName} {$year} telah di-generate</h2>
                <table border='1' cellpadding='10' style='border-collapse: collapse;'>
                    <tr><td><strong>Total Crew:</strong></td><td>{$period['total_crew']}</td></tr>
                    <tr><td><strong>Gross Salary:</strong></td><td>\$" . number_format($period['total_gross'], 2) . "</td></tr>
                    <tr><td><strong>Total Tax:</strong></td><td>\$" . number_format($period['total_tax'], 2) . "</td></tr>
                    <tr><td><strong>Net Payable:</strong></td><td>\$" . number_format($period['total_net'], 2) . "</td></tr>
                </table>
                <p>Silakan login ke sistem ERP untuk melihat detail lengkap.</p>
                ";
                
                // Send to admin email
                $adminEmail = 'admin@indoocean.co.id'; // Change to actual admin email
                $emailService->send($adminEmail, $subject, $body);
            }
        } catch (\Exception $e) {
            // Log but don't fail
            error_log('Payroll notification email failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Send contract expiration alerts
     */
    private function sendExpirationAlerts($expiring7, $expiring30)
    {
        try {
            if (class_exists('\\App\\Libraries\\EmailService')) {
                $emailService = new \App\Libraries\EmailService();
                
                $subject = "Contract Expiration Alert - " . date('d M Y');
                
                $body = "<h2>Contract Expiration Alert</h2>";
                
                if (!empty($expiring7)) {
                    $body .= "<h3 style='color: red;'>⚠️ Expiring in 7 days (" . count($expiring7) . " contracts)</h3>";
                    $body .= "<ul>";
                    foreach ($expiring7 as $c) {
                        $body .= "<li>{$c['crew_name']} - {$c['vessel_name']} (Exp: {$c['sign_off_date']})</li>";
                    }
                    $body .= "</ul>";
                }
                
                if (!empty($expiring30)) {
                    $body .= "<h3 style='color: orange;'>⏰ Expiring in 30 days (" . count($expiring30) . " contracts)</h3>";
                    $body .= "<ul>";
                    foreach ($expiring30 as $c) {
                        $body .= "<li>{$c['crew_name']} - {$c['vessel_name']} (Exp: {$c['sign_off_date']})</li>";
                    }
                    $body .= "</ul>";
                }
                
                $adminEmail = 'admin@indoocean.co.id';
                $emailService->send($adminEmail, $subject, $body);
            }
        } catch (\Exception $e) {
            error_log('Contract alert email failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Log cron run
     */
    private function logCronRun($task, $status, $data = [])
    {
        $logFile = APPPATH . '../logs/cron_' . date('Y-m') . '.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'task' => $task,
            'status' => $status,
            'data' => $data
        ];
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
    }
    
    /**
     * Get month name
     */
    private function getMonthName($month)
    {
        $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];
        return $months[$month] ?? '';
    }
}
