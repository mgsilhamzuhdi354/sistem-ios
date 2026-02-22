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
            
            // Get exchange rate: use contract's custom rate if set, otherwise system default
            // Auto-detect currency: if NULL or USD but amount > 1M, assume IDR
            $originalCurrency = $contract['currency_code'] ?? null;
            $originalGross = $contract['total_monthly'] ?? 0;
            
            if (!$originalCurrency || ($originalCurrency === 'USD' && $originalGross > 1000000)) {
                $originalCurrency = 'IDR';
            }
            
            if (!empty($contract['contract_exchange_rate']) && $contract['contract_exchange_rate'] > 0) {
                // Contract has custom rate set by owner (1 USD = X currency)
                // To convert to USD: amount / rate
                $exchangeRate = 1 / $contract['contract_exchange_rate'];
            } else {
                // Use system default rate
                $exchangeRate = $this->getExchangeRate($originalCurrency);
            }
            
            // Calculate deductions in original currency
            $deductions = $this->query(
                "SELECT deduction_type, SUM(amount) as total 
                 FROM contract_deductions 
                 WHERE contract_id = ? AND is_active = 1 AND is_recurring = 1
                 GROUP BY deduction_type",
                [$contract['id']], 'i'
            );
            
            $insurance = 0;
            $medical = 0;
            $advance = 0;
            $other = 0;
            
            foreach ($deductions as $ded) {
                switch ($ded['deduction_type']) {
                    case 'insurance': $insurance = $ded['total']; break;
                    case 'medical': $medical = $ded['total']; break;
                    case 'advance': $advance = $ded['total']; break;
                    default: $other += $ded['total'];
                }
            }
            
            // Original values in original currency
            $originalGross = $contract['total_monthly'] ?? 0;
            $originalBasic = $contract['basic_salary'] ?? 0;
            $originalOvertime = $contract['overtime_allowance'] ?? 0;
            $originalLeavePay = $contract['leave_pay'] ?? 0;
            $originalBonus = $contract['bonus'] ?? 0;
            $originalOther = $contract['other_allowance'] ?? 0;
            
            // Convert all values to USD
            $basicSalaryUSD = $originalBasic * $exchangeRate;
            $overtimeUSD = $originalOvertime * $exchangeRate;
            $leavePayUSD = $originalLeavePay * $exchangeRate;
            $bonusUSD = $originalBonus * $exchangeRate;
            $otherAllowanceUSD = $originalOther * $exchangeRate;
            $grossSalaryUSD = $originalGross * $exchangeRate;
            
            // Convert deductions to USD
            $insuranceUSD = $insurance * $exchangeRate;
            $medicalUSD = $medical * $exchangeRate;
            $advanceUSD = $advance * $exchangeRate;
            $otherDeductionsUSD = $other * $exchangeRate;
            $totalDeductionsUSD = ($insurance + $medical + $advance + $other) * $exchangeRate;
            
            // Calculate tax in USD
            $taxRate = $contract['tax_rate'] ?? 5;
            $taxAmountUSD = ($grossSalaryUSD - $totalDeductionsUSD) * ($taxRate / 100);
            
            $netSalaryUSD = $grossSalaryUSD - $totalDeductionsUSD - $taxAmountUSD;
            
            $payrollData = [
                'payroll_period_id' => $periodId,
                'contract_id' => $contract['id'],
                'crew_name' => $contract['crew_name'],
                'rank_name' => $contract['rank_name'],
                'vessel_name' => $contract['vessel_name'],
                'basic_salary' => round($basicSalaryUSD, 2),
                'overtime' => round($overtimeUSD, 2),
                'leave_pay' => round($leavePayUSD, 2),
                'bonus' => round($bonusUSD, 2),
                'other_allowance' => round($otherAllowanceUSD, 2),
                'gross_salary' => round($grossSalaryUSD, 2),
                'insurance' => round($insuranceUSD, 2),
                'medical' => round($medicalUSD, 2),
                'advance' => round($advanceUSD, 2),
                'other_deductions' => round($otherDeductionsUSD, 2),
                'admin_bank_fee' => 0,
                'reimbursement' => 0,
                'loans' => 0,
                'total_deductions' => round($totalDeductionsUSD, 2),
                'tax_type' => $contract['tax_type'] ?? 'pph21',
                'tax_rate' => $taxRate,
                'tax_amount' => round($taxAmountUSD, 2),
                'net_salary' => round($netSalaryUSD, 2),
                'currency_code' => 'USD',
                'original_currency' => $originalCurrency,
                'original_basic' => $originalBasic,
                'original_overtime' => $originalOvertime,
                'original_leave_pay' => $originalLeavePay,
                'exchange_rate' => $exchangeRate > 0 ? (1 / $exchangeRate) : 0,
                'payment_method' => 'bank_transfer',
                'bank_name' => $contract['crew_bank_name'] ?? null,
                'bank_account' => $contract['crew_bank_account'] ?? null,
                'bank_holder' => $contract['crew_bank_holder'] ?? $contract['crew_name'],
                'status' => 'pending'
            ];
            
            // Insert new or update existing
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
