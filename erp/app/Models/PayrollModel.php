<?php
/**
 * PT Indo Ocean - ERP System
 * Payroll Model
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class PayrollPeriodModel extends BaseModel
{
    protected $table = 'payroll_periods';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'period_month', 'period_year', 'start_date', 'end_date', 'status',
        'total_crew', 'total_gross', 'total_deductions', 'total_tax', 'total_net',
        'processed_by', 'processed_at', 'locked_by', 'locked_at'
    ];
    
    /**
     * Get or create period
     */
    public function getOrCreate($month, $year)
    {
        $result = $this->query(
            "SELECT * FROM payroll_periods WHERE period_month = ? AND period_year = ?",
            [$month, $year], 'ii'
        );
        
        if (!empty($result)) {
            return $result[0];
        }
        
        // Create new period
        $startDate = date('Y-m-01', strtotime("$year-$month-01"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));
        
        $id = $this->insert([
            'period_month' => $month,
            'period_year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'draft'
        ]);
        
        return $this->find($id);
    }
    
    /**
     * Get all periods
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM payroll_periods ORDER BY period_year DESC, period_month DESC");
    }
    
    /**
     * Update period totals
     */
    public function updateTotals($periodId)
    {
        $sql = "UPDATE payroll_periods p SET
                    total_crew = (SELECT COUNT(*) FROM payroll_items WHERE payroll_period_id = ?),
                    total_gross = (SELECT COALESCE(SUM(gross_salary), 0) FROM payroll_items WHERE payroll_period_id = ?),
                    total_deductions = (SELECT COALESCE(SUM(total_deductions), 0) FROM payroll_items WHERE payroll_period_id = ?),
                    total_tax = (SELECT COALESCE(SUM(tax_amount), 0) FROM payroll_items WHERE payroll_period_id = ?),
                    total_net = (SELECT COALESCE(SUM(net_salary), 0) FROM payroll_items WHERE payroll_period_id = ?)
                WHERE id = ?";
        
        return $this->execute($sql, [$periodId, $periodId, $periodId, $periodId, $periodId, $periodId], 'iiiiii');
    }
}

class PayrollItemModel extends BaseModel
{
    protected $table = 'payroll_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'payroll_period_id', 'contract_id', 'crew_name', 'rank_name', 'vessel_name',
        'basic_salary', 'overtime', 'leave_pay', 'bonus', 'other_allowance', 'gross_salary',
        'insurance', 'medical', 'advance', 'other_deductions', 'admin_bank_fee', 'reimbursement', 'loans', 'total_deductions',
        'tax_type', 'tax_rate', 'tax_amount', 'net_salary', 'currency_code',
        'original_currency', 'original_basic', 'original_overtime', 'original_leave_pay', 'exchange_rate',
        'payment_method', 'bank_name', 'bank_account', 'bank_holder',
        'status', 'payment_date', 'payment_reference', 'notes',
        'email_sent_at', 'email_status', 'email_failure_reason'
    ];
    
    /**
     * Get items by period
     */
    public function getByPeriod($periodId)
    {
        $sql = "SELECT pi.*, c.contract_no
                FROM payroll_items pi
                LEFT JOIN contracts c ON pi.contract_id = c.id
                WHERE pi.payroll_period_id = ?
                ORDER BY pi.vessel_name, pi.rank_name";
        
        return $this->query($sql, [$periodId], 'i');
    }
    
    /**
     * Generate payroll for period
     */
    public function generateForPeriod($periodId)
    {
        // Get active contracts with currency info
        $sql = "SELECT c.id, c.crew_name, c.crew_id,
                    r.name AS rank_name,
                    v.name AS vessel_name,
                    cs.basic_salary, cs.overtime_allowance, cs.leave_pay, cs.bonus, cs.other_allowance,
                    cs.total_monthly, cs.exchange_rate AS contract_exchange_rate,
                    cur.code AS currency_code,
                    ct.tax_type, ct.tax_rate,
                    cr.bank_name AS crew_bank_name, cr.bank_account AS crew_bank_account, cr.bank_holder AS crew_bank_holder
                FROM contracts c
                LEFT JOIN ranks r ON c.rank_id = r.id
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                LEFT JOIN contract_taxes ct ON c.id = ct.contract_id
                LEFT JOIN crews cr ON c.crew_id = cr.id
                WHERE c.status IN ('active', 'onboard')";
        
        $contracts = $this->query($sql);
        
        foreach ($contracts as $contract) {
            // Check if already exists
            $existing = $this->query(
                "SELECT id, status FROM payroll_items WHERE payroll_period_id = ? AND contract_id = ?",
                [$periodId, $contract['id']], 'ii'
            );
            
            $existingId = null;
            if (!empty($existing)) {
                // Skip if already paid
                if ($existing[0]['status'] === 'paid') continue;
                $existingId = $existing[0]['id'];
            }
            
            // Detect currency
            $originalCurrency = $contract['currency_code'] ?? null;
            $totalMonthly = floatval($contract['total_monthly'] ?? 0);
            
            if (!$originalCurrency || ($originalCurrency === 'USD' && $totalMonthly > 1000000)) {
                $originalCurrency = 'IDR';
            }
            
            // Exchange rate = Kurs (1 unit original currency = X IDR)
            $exchangeRate = floatval($contract['contract_exchange_rate'] ?? 0);
            if ($exchangeRate <= 0) {
                // Default IDR rates: 1 USD = 15900 IDR, 1 MYR = 3500 IDR, etc.
                $defaultRates = ['IDR' => 1, 'USD' => 15900, 'MYR' => 3500, 'SGD' => 11800, 'EUR' => 17000];
                $exchangeRate = $defaultRates[$originalCurrency] ?? 1;
            }
            
            // Salary values in ORIGINAL currency (no USD conversion)
            $basicSalary = floatval($contract['basic_salary'] ?? 0);
            $overtime = floatval($contract['overtime_allowance'] ?? 0);
            $leavePay = floatval($contract['leave_pay'] ?? 0);
            $bonus = floatval($contract['bonus'] ?? 0);
            $otherAllowance = floatval($contract['other_allowance'] ?? 0);
            $grossSalary = $totalMonthly > 0 ? $totalMonthly : ($basicSalary + $overtime + $leavePay + $bonus + $otherAllowance);
            
            // Calculate deductions (try/catch in case table doesn't exist)
            $insurance = 0;
            $medical = 0;
            $advance = 0;
            $otherDed = 0;
            
            try {
                $deductions = $this->query(
                    "SELECT deduction_type, SUM(amount) as total 
                     FROM contract_deductions 
                     WHERE contract_id = ? AND is_active = 1 AND is_recurring = 1
                     GROUP BY deduction_type",
                    [$contract['id']], 'i'
                );
                
                foreach ($deductions as $ded) {
                    switch ($ded['deduction_type']) {
                        case 'insurance': $insurance = floatval($ded['total']); break;
                        case 'medical': $medical = floatval($ded['total']); break;
                        case 'advance': $advance = floatval($ded['total']); break;
                        default: $otherDed += floatval($ded['total']);
                    }
                }
            } catch (\Exception $e) {
                // contract_deductions table may not exist in production
            }
            
            $totalDeductions = $insurance + $medical + $advance + $otherDed;
            
            // Calculate tax
            $taxRate = floatval($contract['tax_rate'] ?? 2.5);
            $taxBase = $grossSalary - $totalDeductions;
            $taxAmount = $taxBase > 0 ? $taxBase * ($taxRate / 100) : 0;
            
            $netSalary = $grossSalary - $totalDeductions - $taxAmount;
            
            $payrollData = [
                'payroll_period_id' => $periodId,
                'contract_id' => $contract['id'],
                'crew_name' => $contract['crew_name'],
                'rank_name' => $contract['rank_name'] ?? '',
                'vessel_name' => $contract['vessel_name'] ?? '',
                'basic_salary' => round($basicSalary, 2),
                'overtime' => round($overtime, 2),
                'leave_pay' => round($leavePay, 2),
                'bonus' => round($bonus, 2),
                'other_allowance' => round($otherAllowance, 2),
                'gross_salary' => round($grossSalary, 2),
                'insurance' => round($insurance, 2),
                'medical' => round($medical, 2),
                'advance' => round($advance, 2),
                'other_deductions' => round($otherDed, 2),
                'admin_bank_fee' => 0,
                'reimbursement' => 0,
                'loans' => 0,
                'total_deductions' => round($totalDeductions, 2),
                'tax_type' => $contract['tax_type'] ?? 'pph21',
                'tax_rate' => $taxRate,
                'tax_amount' => round($taxAmount, 2),
                'net_salary' => round($netSalary, 2),
                'currency_code' => $originalCurrency,
                'original_currency' => $originalCurrency,
                'original_basic' => $basicSalary,
                'original_overtime' => $overtime,
                'original_leave_pay' => $leavePay,
                'exchange_rate' => $exchangeRate,
                'payment_method' => 'bank_transfer',
                'bank_name' => $contract['crew_bank_name'] ?? null,
                'bank_account' => $contract['crew_bank_account'] ?? null,
                'bank_holder' => $contract['crew_bank_holder'] ?? $contract['crew_name'],
                'status' => 'pending'
            ];
            
            // Insert new or update existing (BaseModel filters out non-existent columns)
            if ($existingId) {
                $this->update($existingId, $payrollData);
            } else {
                $this->insert($payrollData);
            }
        }
        
        // Update period totals
        $periodModel = new PayrollPeriodModel($this->db);
        $periodModel->updateTotals($periodId);
        
        return true;
    }
    
    /**
     * Get exchange rate to USD for a currency
     */
    public function getExchangeRate($currencyCode)
    {
        if ($currencyCode === 'USD') {
            return 1.0;
        }
        
        // Get latest rate from database
        $result = $this->query(
            "SELECT rate_to_usd FROM exchange_rates 
             WHERE currency_code = ? 
             ORDER BY effective_date DESC LIMIT 1",
            [$currencyCode], 's'
        );
        
        if (!empty($result)) {
            return (float)$result[0]['rate_to_usd'];
        }
        
        // Default rates if not in database
        $defaultRates = [
            'IDR' => 0.000063,  // 1 IDR = 0.000063 USD (approx 1 USD = 15900 IDR)
            'SGD' => 0.74,      // 1 SGD = 0.74 USD
            'EUR' => 1.05,      // 1 EUR = 1.05 USD
            'MYR' => 0.21,      // 1 MYR = 0.21 USD (approx 1 USD = 4.76 MYR)
        ];
        
        return $defaultRates[$currencyCode] ?? 1.0;
    }
    
    /**
     * Get summary by vessel
     */
    public function getSummaryByVessel($periodId)
    {
        $sql = "SELECT vessel_name,
                    COUNT(*) as crew_count,
                    SUM(gross_salary) as total_gross,
                    SUM(total_deductions) as total_deductions,
                    SUM(tax_amount) as total_tax,
                    SUM(net_salary) as total_net
                FROM payroll_items
                WHERE payroll_period_id = ?
                GROUP BY vessel_name
                ORDER BY vessel_name";
        
        return $this->query($sql, [$periodId], 'i');
    }
}
