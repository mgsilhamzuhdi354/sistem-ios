<?php
/**
 * PT Indo Ocean - ERP System
 * PDF Generator - Simple PDF using HTML to PDF conversion
 * Uses TCPDF-like approach with pure PHP
 */

namespace App\Libraries;

class PDFGenerator
{
    private $html = '';
    private $title = '';
    private $orientation = 'P'; // P = Portrait, L = Landscape
    private $pageSize = 'A4';
    
    /**
     * Initialize PDF
     */
    public function create($title = 'Document', $orientation = 'P')
    {
        $this->title = $title;
        $this->orientation = $orientation;
        $this->html = '';
        return $this;
    }
    
    /**
     * Add HTML content
     */
    public function addContent($html)
    {
        $this->html .= $html;
        return $this;
    }
    
    
    /**
     * Generate Contract PDF
     */
    public function generateContract($contract, $deductions = [])
    {
        $this->create('Contract - ' . $contract['contract_no']);
        
        $totalDeductions = array_sum(array_column($deductions, 'amount'));
        $netSalary = ($contract['total_monthly'] ?? 0) - $totalDeductions;
        
        $content = $this->getBaseStyles();
        $content .= "
        <div class='header'>
            <img src='" . BASE_URL . "assets/img/logo.png' style='height:60px;' alt='Logo'>
            <h1>PT INDO OCEAN</h1>
            <p>Crew Contract Management</p>
        </div>
        
        <h2 style='text-align:center; margin:30px 0;'>EMPLOYMENT CONTRACT</h2>
        <p style='text-align:center;'><strong>Contract No:</strong> {$contract['contract_no']}</p>
        
        <table class='info-table'>
            <tr>
                <td width='30%'><strong>Crew Name</strong></td>
                <td width='70%'>{$contract['crew_name']}</td>
            </tr>
            <tr>
                <td><strong>Position/Rank</strong></td>
                <td>" . ($contract['rank_name'] ?? '-') . "</td>
            </tr>
            <tr>
                <td><strong>Vessel</strong></td>
                <td>" . ($contract['vessel_name'] ?? '-') . "</td>
            </tr>
            <tr>
                <td><strong>Client</strong></td>
                <td>" . ($contract['client_name'] ?? '-') . "</td>
            </tr>
        </table>
        
        <h3>Contract Period</h3>
        <table class='info-table'>
            <tr>
                <td width='30%'><strong>Sign On Date</strong></td>
                <td width='70%'>" . date('d F Y', strtotime($contract['sign_on_date'])) . "</td>
            </tr>
            <tr>
                <td><strong>Sign Off Date</strong></td>
                <td>" . date('d F Y', strtotime($contract['sign_off_date'])) . "</td>
            </tr>
            <tr>
                <td><strong>Duration</strong></td>
                <td>" . ($contract['duration_months'] ?? '-') . " months</td>
            </tr>
        </table>
        
        <h3>Salary Structure</h3>
        <table class='salary-table'>
            <tr>
                <td>Basic Salary</td>
                <td class='amount'>$ " . number_format($contract['basic_salary'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td>Overtime Allowance</td>
                <td class='amount'>$ " . number_format($contract['overtime_allowance'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td>Leave Pay</td>
                <td class='amount'>$ " . number_format($contract['leave_pay'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td class='amount'>$ " . number_format($contract['bonus'] ?? 0, 2) . "</td>
            </tr>
            <tr class='total'>
                <td><strong>Total Monthly</strong></td>
                <td class='amount'><strong>$ " . number_format($contract['total_monthly'] ?? 0, 2) . "</strong></td>
            </tr>
        </table>
        
        <h3>Tax Information</h3>
        <table class='info-table'>
            <tr>
                <td width='30%'><strong>Tax Type</strong></td>
                <td width='70%'>" . ($contract['tax_type'] ?? 'PPh 21') . "</td>
            </tr>
            <tr>
                <td><strong>Tax Rate</strong></td>
                <td>" . ($contract['tax_rate'] ?? 5) . "%</td>
            </tr>
        </table>
        
        <div class='signature-section'>
            <div class='signature-box'>
                <p>Employee</p>
                <div class='signature-line'></div>
                <p>{$contract['crew_name']}</p>
            </div>
            <div class='signature-box'>
                <p>Company Representative</p>
                <div class='signature-line'></div>
                <p>PT Indo Ocean</p>
            </div>
        </div>
        
        <div class='footer'>
            <p>Generated on " . date('d M Y H:i') . " | PT Indo Ocean ERP System</p>
        </div>
        ";
        
        $this->html = $content;
        return $this;
    }
    
    /**
     * Generate Payslip PDF
     */
    public function generatePayslip($payrollItem, $contract)
    {
        $this->create('Payslip - ' . $payrollItem['period_name']);
        
        $content = $this->getBaseStyles();
        $content .= "
        <div class='header'>
            <h1>PT INDO OCEAN</h1>
            <h2>PAYSLIP</h2>
            <p>Period: {$payrollItem['period_name']}</p>
        </div>
        
        <table class='info-table'>
            <tr>
                <td width='25%'><strong>Employee</strong></td>
                <td width='25%'>{$contract['crew_name']}</td>
                <td width='25%'><strong>Position</strong></td>
                <td width='25%'>" . ($contract['rank_name'] ?? '-') . "</td>
            </tr>
            <tr>
                <td><strong>Contract No</strong></td>
                <td>{$contract['contract_no']}</td>
                <td><strong>Vessel</strong></td>
                <td>" . ($contract['vessel_name'] ?? '-') . "</td>
            </tr>
        </table>
        
        <h3>Earnings</h3>
        <table class='salary-table'>
            <tr>
                <td>Basic Salary</td>
                <td class='amount'>$ " . number_format($payrollItem['basic_salary'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td>Overtime</td>
                <td class='amount'>$ " . number_format($payrollItem['overtime'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td>Allowances</td>
                <td class='amount'>$ " . number_format($payrollItem['allowances'] ?? 0, 2) . "</td>
            </tr>
            <tr class='subtotal'>
                <td><strong>Gross Salary</strong></td>
                <td class='amount'><strong>$ " . number_format($payrollItem['gross_salary'] ?? 0, 2) . "</strong></td>
            </tr>
        </table>
        
        <h3>Deductions</h3>
        <table class='salary-table'>
            <tr>
                <td>Tax (PPh 21)</td>
                <td class='amount deduction'>- $ " . number_format($payrollItem['tax_amount'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td>Other Deductions</td>
                <td class='amount deduction'>- $ " . number_format($payrollItem['deductions'] ?? 0, 2) . "</td>
            </tr>
            <tr class='subtotal'>
                <td><strong>Total Deductions</strong></td>
                <td class='amount deduction'><strong>- $ " . number_format(($payrollItem['tax_amount'] ?? 0) + ($payrollItem['deductions'] ?? 0), 2) . "</strong></td>
            </tr>
        </table>
        
        <table class='salary-table total-box'>
            <tr class='net-pay'>
                <td><strong>NET PAY</strong></td>
                <td class='amount'><strong>$ " . number_format($payrollItem['net_salary'] ?? 0, 2) . "</strong></td>
            </tr>
        </table>
        
        <div class='footer'>
            <p>This is a computer-generated document. No signature required.</p>
            <p>Generated on " . date('d M Y H:i') . " | PT Indo Ocean ERP</p>
        </div>
        ";
        
        $this->html = $content;
        return $this;
    }
    
    /**
     * Generate Report PDF
     */
    public function generateReport($title, $headers, $data, $summary = [])
    {
        $this->create($title, 'L'); // Landscape for reports
        
        $content = $this->getBaseStyles();
        $content .= "
        <div class='header'>
            <h1>PT INDO OCEAN</h1>
            <h2>{$title}</h2>
            <p>Generated: " . date('d M Y H:i') . "</p>
        </div>
        
        <table class='report-table'>
            <thead>
                <tr>";
        
        foreach ($headers as $header) {
            $content .= "<th>{$header}</th>";
        }
        
        $content .= "</tr></thead><tbody>";
        
        foreach ($data as $row) {
            $content .= "<tr>";
            foreach ($row as $cell) {
                $content .= "<td>" . htmlspecialchars($cell) . "</td>";
            }
            $content .= "</tr>";
        }
        
        $content .= "</tbody></table>";
        
        if (!empty($summary)) {
            $content .= "<div class='summary'><h3>Summary</h3><ul>";
            foreach ($summary as $key => $value) {
                $content .= "<li><strong>{$key}:</strong> {$value}</li>";
            }
            $content .= "</ul></div>";
        }
        
        $content .= "
        <div class='footer'>
            <p>PT Indo Ocean ERP System | " . date('Y') . "</p>
        </div>";
        
        $this->html = $content;
        return $this;
    }
    
    /**
     * Get base CSS styles
     */
    private function getBaseStyles()
    {
        return "
        <style>
            @page { margin: 20mm; }
            body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
            .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #0A2463; margin-bottom: 20px; }
            .header h1 { color: #0A2463; margin: 0; font-size: 24px; }
            .header h2 { color: #D4AF37; margin: 5px 0; font-size: 18px; }
            .header p { margin: 5px 0; color: #666; }
            
            h3 { color: #0A2463; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 20px; }
            
            .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
            .info-table td { padding: 8px; border: 1px solid #ddd; }
            .info-table tr:nth-child(even) { background: #f9f9f9; }
            
            .salary-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
            .salary-table td { padding: 10px; border-bottom: 1px solid #eee; }
            .salary-table .amount { text-align: right; font-family: monospace; }
            .salary-table .deduction { color: #dc3545; }
            .salary-table .subtotal { background: #f5f5f5; font-weight: bold; }
            .salary-table .total { background: #0A2463; color: white; }
            .salary-table .net-pay { background: #D4AF37; color: #0A2463; font-size: 16px; }
            
            .total-box { border: 2px solid #D4AF37; margin-top: 20px; }
            
            .report-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .report-table th { background: #0A2463; color: white; padding: 10px; text-align: left; }
            .report-table td { padding: 8px; border-bottom: 1px solid #ddd; }
            .report-table tr:nth-child(even) { background: #f9f9f9; }
            
            .signature-section { display: flex; justify-content: space-between; margin-top: 50px; page-break-inside: avoid; }
            .signature-box { width: 40%; text-align: center; }
            .signature-line { border-bottom: 1px solid #333; margin: 60px 0 10px 0; }
            
            .summary { background: #f5f5f5; padding: 15px; margin-top: 20px; border-radius: 5px; }
            .summary ul { margin: 10px 0; padding-left: 20px; }
            
            .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #666; }
        </style>
        ";
    }
    
    /**
     * Output PDF (downloads or displays)
     */
    public function output($filename = 'document.pdf', $destination = 'D')
    {
        // For now, output as HTML that can be printed to PDF
        // In production, integrate with TCPDF or DOMPDF
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$this->title}</title>
            <style>
                @media print {
                    body { -webkit-print-color-adjust: exact; }
                }
            </style>
        </head>
        <body>
            {$this->html}
            <script>
                window.onload = function() {
                    window.print();
                }
            </script>
        </body>
        </html>
        ";
        
        if ($destination === 'I') {
            // Inline - display in browser
            header('Content-Type: text/html; charset=UTF-8');
            echo $html;
        } elseif ($destination === 'D') {
            // Download
            header('Content-Type: text/html; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.html"');
            echo $html;
        } elseif ($destination === 'S') {
            // Return as string
            return $html;
        }
        
        exit;
    }
    
    /**
     * Get HTML content
     */
    public function getHtml()
    {
        return $this->html;
    }
}
