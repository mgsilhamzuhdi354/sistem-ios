<?php
/**
 * PT Indo Ocean - ERP System
 * Financial & Accounting Module - Models
 * 
 * International Standards: IFRS/PSAK compliant double-entry bookkeeping
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

// ═══════════════════════════════════════════════════════════════
// CHART OF ACCOUNTS (Bagan Akun)
// ═══════════════════════════════════════════════════════════════

class FinanceChartOfAccountsModel extends BaseModel
{
    protected $table = 'finance_chart_of_accounts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'code', 'name', 'name_en', 'type', 'parent_id',
        'is_system', 'is_active', 'sort_order'
    ];

    public function getAll()
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order, code"
        );
    }

    public function getGroupedByType()
    {
        $accounts = $this->getAll();
        $grouped = [];
        foreach ($accounts as $acc) {
            $grouped[$acc['type']][] = $acc;
        }
        return $grouped;
    }

    public function getByType($type)
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE type = ? AND is_active = 1 ORDER BY code",
            [$type], 's'
        );
    }

    public function findByCode($code)
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE code = ? LIMIT 1",
            [$code], 's'
        );
        return $result[0] ?? null;
    }

    public function getRevenueAccounts()
    {
        return $this->getByType('revenue');
    }

    public function getExpenseAccounts()
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE type IN ('cogs','expense') AND is_active = 1 AND code != '5-0000' AND code != '6-0000' ORDER BY code"
        );
    }

    public function getCashBankAccounts()
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE type = 'asset' AND code LIKE '1-1%' AND code NOT LIKE '1-14%' AND code NOT LIKE '1-15%' AND code NOT LIKE '1-16%' AND is_active = 1 ORDER BY code"
        );
    }

    /**
     * Get account balance from journal lines
     */
    public function getBalance($accountId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                    COALESCE(SUM(jl.debit), 0) as total_debit,
                    COALESCE(SUM(jl.credit), 0) as total_credit
                FROM finance_journal_lines jl
                JOIN finance_journal_entries je ON jl.journal_entry_id = je.id
                WHERE jl.account_id = ? AND je.is_posted = 1";
        $params = [$accountId];
        $types = 'i';

        if ($startDate) {
            $sql .= " AND je.entry_date >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate) {
            $sql .= " AND je.entry_date <= ?";
            $params[] = $endDate;
            $types .= 's';
        }

        $result = $this->query($sql, $params, $types);
        return $result[0] ?? ['total_debit' => 0, 'total_credit' => 0];
    }

    /**
     * Get all account balances and transaction counts in bulk
     */
    public function getAllBalances()
    {
        return $this->query(
            "SELECT jl.account_id,
                    COALESCE(SUM(jl.debit), 0) as total_debit,
                    COALESCE(SUM(jl.credit), 0) as total_credit,
                    COUNT(DISTINCT jl.journal_entry_id) as tx_count
             FROM finance_journal_lines jl
             JOIN finance_journal_entries je ON jl.journal_entry_id = je.id AND je.is_posted = 1
             GROUP BY jl.account_id"
        );
    }

    /**
     * Get accounts grouped by type with balances
     */
    public function getGroupedWithBalances()
    {
        $accounts = $this->getAll();

        // Bulk load balances
        $balRows = $this->getAllBalances();
        $balances = [];
        foreach ($balRows as $b) {
            $balances[$b['account_id']] = $b;
        }

        // Also count invoices and bills per account
        $invCounts = $this->query(
            "SELECT revenue_account_id as account_id, COUNT(*) as cnt, COALESCE(SUM(total), 0) as total
             FROM finance_invoices GROUP BY revenue_account_id"
        );
        $invMap = [];
        foreach ($invCounts as $ic) { $invMap[$ic['account_id']] = $ic; }

        $billCounts = $this->query(
            "SELECT expense_account_id as account_id, COUNT(*) as cnt, COALESCE(SUM(total), 0) as total
             FROM finance_bills GROUP BY expense_account_id"
        );
        $billMap = [];
        foreach ($billCounts as $bc) { $billMap[$bc['account_id']] = $bc; }

        $grouped = [];
        $totals = ['asset' => 0, 'liability' => 0, 'equity' => 0, 'revenue' => 0, 'cogs' => 0, 'expense' => 0];

        foreach ($accounts as &$acc) {
            $id = $acc['id'];
            $bal = $balances[$id] ?? ['total_debit' => 0, 'total_credit' => 0, 'tx_count' => 0];

            $debit = floatval($bal['total_debit']);
            $credit = floatval($bal['total_credit']);
            $acc['total_debit'] = $debit;
            $acc['total_credit'] = $credit;
            $acc['tx_count'] = intval($bal['tx_count']);

            // Calculate balance based on account type normal balance
            $type = $acc['type'];
            if (in_array($type, ['asset', 'cogs', 'expense'])) {
                $acc['balance'] = $debit - $credit; // Debit-normal
            } else {
                $acc['balance'] = $credit - $debit; // Credit-normal
            }

            $acc['invoice_count'] = intval($invMap[$id]['cnt'] ?? 0);
            $acc['invoice_total'] = floatval($invMap[$id]['total'] ?? 0);
            $acc['bill_count'] = intval($billMap[$id]['cnt'] ?? 0);
            $acc['bill_total'] = floatval($billMap[$id]['total'] ?? 0);

            if (isset($totals[$type])) {
                $totals[$type] += $acc['balance'];
            }

            $grouped[$type][] = $acc;
        }

        return ['grouped' => $grouped, 'totals' => $totals];
    }
}

// ═══════════════════════════════════════════════════════════════
// COST CENTERS (Pusat Biaya)
// ═══════════════════════════════════════════════════════════════

class FinanceCostCenterModel extends BaseModel
{
    protected $table = 'finance_cost_centers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'code', 'name', 'name_en', 'description', 'is_active'
    ];

    public function getAll()
    {
        return $this->query(
            "SELECT * FROM {$this->table} ORDER BY code"
        );
    }

    public function getActive()
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY code"
        );
    }

    public function findByCode($code)
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE code = ? LIMIT 1",
            [$code], 's'
        );
        return $result[0] ?? null;
    }

    /**
     * Get cost center expense totals for a period
     */
    public function getExpenseSummary($startDate, $endDate)
    {
        return $this->query(
            "SELECT cc.id, cc.code, cc.name,
                    COALESCE(SUM(jl.debit), 0) as total_expense
             FROM {$this->table} cc
             LEFT JOIN finance_journal_lines jl ON jl.cost_center_id = cc.id
             LEFT JOIN finance_journal_entries je ON jl.journal_entry_id = je.id
                AND je.entry_date BETWEEN ? AND ? AND je.is_posted = 1
             LEFT JOIN finance_chart_of_accounts coa ON jl.account_id = coa.id
                AND coa.type IN ('cogs','expense')
             WHERE cc.is_active = 1
             GROUP BY cc.id, cc.code, cc.name
             ORDER BY cc.code",
            [$startDate, $endDate], 'ss'
        );
    }

    /**
     * Get detailed stats per cost center with invoices, bills, and estimated operational costs
     */
    public function getDetailedStats()
    {
        $centers = $this->getAll();
        $result = [];

        foreach ($centers as $cc) {
            $id = $cc['id'];
            $code = strtoupper($cc['code'] ?? '');

            // Invoice count & revenue for this cost center
            $invData = $this->query(
                "SELECT COUNT(*) as cnt, COALESCE(SUM(total), 0) as revenue, COALESCE(SUM(amount_paid), 0) as collected
                 FROM finance_invoices WHERE cost_center_id = ?",
                [$id], 'i'
            );

            // Bill count & expense for this cost center  
            $billData = $this->query(
                "SELECT COUNT(*) as cnt, COALESCE(SUM(total), 0) as expense, COALESCE(SUM(amount_paid), 0) as paid_out
                 FROM finance_bills WHERE cost_center_id = ?",
                [$id], 'i'
            );

            // Journal entries count for this cost center
            $jrnlData = $this->query(
                "SELECT COUNT(DISTINCT jl.journal_entry_id) as cnt, 
                        COALESCE(SUM(jl.debit), 0) as total_debit,
                        COALESCE(SUM(jl.credit), 0) as total_credit
                 FROM finance_journal_lines jl
                 WHERE jl.cost_center_id = ?",
                [$id], 'i'
            );

            // Estimate operational cost from contracts based on CC code
            $opCost = 0;
            $crewCount = 0;
            // Map CC codes to potential contract data
            if (in_array($code, ['CC-CREW', 'CC-SHIP'])) {
                $crewData = $this->query(
                    "SELECT COUNT(*) as crew_count, 
                            COALESCE(SUM(cs.total_monthly), 0) as monthly_cost
                     FROM contracts c
                     JOIN contract_salaries cs ON c.id = cs.contract_id
                     WHERE c.status IN ('active','onboard')"
                );
                if ($code === 'CC-CREW') {
                    $crewCount = intval($crewData[0]['crew_count'] ?? 0);
                    $opCost = floatval($crewData[0]['monthly_cost'] ?? 0);
                }
                if ($code === 'CC-SHIP') {
                    // Ship chandler — would track vessel supplies separately
                    $vesselData = $this->query(
                        "SELECT COUNT(DISTINCT c.vessel_id) as cnt 
                         FROM contracts c WHERE c.status IN ('active','onboard')"
                    );
                    $crewCount = intval($vesselData[0]['cnt'] ?? 0);
                }
            }

            $cc['invoice_count'] = intval($invData[0]['cnt'] ?? 0);
            $cc['invoice_revenue'] = floatval($invData[0]['revenue'] ?? 0);
            $cc['invoice_collected'] = floatval($invData[0]['collected'] ?? 0);
            $cc['bill_count'] = intval($billData[0]['cnt'] ?? 0);
            $cc['bill_expense'] = floatval($billData[0]['expense'] ?? 0);
            $cc['bill_paid'] = floatval($billData[0]['paid_out'] ?? 0);
            $cc['journal_count'] = intval($jrnlData[0]['cnt'] ?? 0);
            $cc['journal_debit'] = floatval($jrnlData[0]['total_debit'] ?? 0);
            $cc['journal_credit'] = floatval($jrnlData[0]['total_credit'] ?? 0);
            $cc['operational_cost'] = $opCost;
            $cc['crew_count'] = $crewCount;
            $cc['total_activity'] = $cc['invoice_count'] + $cc['bill_count'] + $cc['journal_count'];

            $result[] = $cc;
        }

        // Sort by total activity descending (most active first)
        usort($result, function($a, $b) { return $b['total_activity'] - $a['total_activity']; });

        return $result;
    }

    /**
     * Get summary totals across all cost centers
     */
    public function getOverallTotals()
    {
        $inv = $this->query(
            "SELECT COUNT(*) as cnt, COALESCE(SUM(total), 0) as total FROM finance_invoices"
        );
        $bill = $this->query(
            "SELECT COUNT(*) as cnt, COALESCE(SUM(total), 0) as total FROM finance_bills"
        );
        $jrnl = $this->query(
            "SELECT COUNT(*) as cnt FROM finance_journal_entries"
        );

        return [
            'total_invoices' => intval($inv[0]['cnt'] ?? 0),
            'total_invoice_amount' => floatval($inv[0]['total'] ?? 0),
            'total_bills' => intval($bill[0]['cnt'] ?? 0),
            'total_bill_amount' => floatval($bill[0]['total'] ?? 0),
            'total_journals' => intval($jrnl[0]['cnt'] ?? 0),
        ];
    }
}

// ═══════════════════════════════════════════════════════════════
// INVOICES - Accounts Receivable (AR)
// ═══════════════════════════════════════════════════════════════

class FinanceInvoiceModel extends BaseModel
{
    protected $table = 'finance_invoices';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_no', 'client_id', 'vessel_id',
        'invoice_date', 'due_date',
        'subtotal', 'discount_percent', 'discount_amount',
        'tax_percent', 'tax_amount', 'total', 'amount_paid',
        'currency_code', 'exchange_rate',
        'status', 'cost_center_id', 'revenue_account_id',
        'terms', 'notes', 'internal_notes',
        'sent_at', 'paid_at', 'cancelled_at',
        'created_by', 'updated_by'
    ];

    /**
     * Get all invoices with client names
     */
    public function getAllWithClient($filters = [])
    {
        $sql = "SELECT fi.*, c.name as client_name, v.name as vessel_name
                FROM {$this->table} fi
                LEFT JOIN clients c ON fi.client_id = c.id
                LEFT JOIN vessels v ON fi.vessel_id = v.id
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            $sql .= " AND fi.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }
        if (!empty($filters['client_id'])) {
            $sql .= " AND fi.client_id = ?";
            $params[] = (int)$filters['client_id'];
            $types .= 'i';
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND fi.invoice_date >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND fi.invoice_date <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }

        $sql .= " ORDER BY fi.created_at DESC";

        return $this->query($sql, $params, $types);
    }

    /**
     * Get single invoice with all relations
     */
    public function getDetail($id)
    {
        $sql = "SELECT fi.*, c.name as client_name, c.address as client_address,
                       c.phone as client_phone, c.email as client_email,
                       v.name as vessel_name,
                       cc.name as cost_center_name, cc.code as cost_center_code,
                       coa.name as revenue_account_name, coa.code as revenue_account_code
                FROM {$this->table} fi
                LEFT JOIN clients c ON fi.client_id = c.id
                LEFT JOIN vessels v ON fi.vessel_id = v.id
                LEFT JOIN finance_cost_centers cc ON fi.cost_center_id = cc.id
                LEFT JOIN finance_chart_of_accounts coa ON fi.revenue_account_id = coa.id
                WHERE fi.id = ?";
        $result = $this->query($sql, [$id], 'i');
        return $result[0] ?? null;
    }

    /**
     * Generate next invoice number (format: INV-{YYYY}{MM}-{NNN})
     */
    public function generateInvoiceNo()
    {
        $prefix = 'INV-' . date('Ym') . '-';
        $result = $this->query(
            "SELECT invoice_no FROM {$this->table} WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1",
            [$prefix . '%'], 's'
        );

        if (!empty($result)) {
            $lastNo = intval(substr($result[0]['invoice_no'], -3)) + 1;
        } else {
            $lastNo = 1;
        }

        return $prefix . str_pad($lastNo, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate and update totals from items
     */
    public function recalculateTotals($invoiceId)
    {
        $invoice = $this->find($invoiceId);
        if (!$invoice) return false;

        $items = $this->query(
            "SELECT SUM(amount) as subtotal FROM finance_invoice_items WHERE invoice_id = ?",
            [$invoiceId], 'i'
        );

        $subtotal = floatval($items[0]['subtotal'] ?? 0);
        $discountAmount = $subtotal * floatval($invoice['discount_percent']) / 100;
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = $afterDiscount * floatval($invoice['tax_percent']) / 100;
        $total = $afterDiscount + $taxAmount;

        return $this->update($invoiceId, [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2)
        ]);
    }

    /**
     * Update payment amount and status
     */
    public function updatePaymentStatus($invoiceId)
    {
        $payments = $this->query(
            "SELECT COALESCE(SUM(amount), 0) as total_paid FROM finance_payments WHERE payment_type = 'receivable' AND reference_id = ?",
            [$invoiceId], 'i'
        );

        $totalPaid = floatval($payments[0]['total_paid'] ?? 0);
        $invoice = $this->find($invoiceId);
        $total = floatval($invoice['total'] ?? 0);

        $status = $invoice['status'];
        if ($totalPaid >= $total && $total > 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        }

        return $this->update($invoiceId, [
            'amount_paid' => round($totalPaid, 2),
            'status' => $status,
            'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : $invoice['paid_at']
        ]);
    }

    /**
     * Get overdue invoices
     */
    public function getOverdue()
    {
        return $this->query(
            "SELECT fi.*, c.name as client_name,
                    DATEDIFF(CURDATE(), fi.due_date) as days_overdue
             FROM {$this->table} fi
             LEFT JOIN clients c ON fi.client_id = c.id
             WHERE fi.status IN ('unpaid','partial') AND fi.due_date < CURDATE()
             ORDER BY fi.due_date ASC"
        );
    }

    /**
     * Get aging summary
     */
    public function getAgingSummary()
    {
        return $this->query(
            "SELECT 
                SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 0 AND 30 THEN total - amount_paid ELSE 0 END) as aging_0_30,
                SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 31 AND 60 THEN total - amount_paid ELSE 0 END) as aging_31_60,
                SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 61 AND 90 THEN total - amount_paid ELSE 0 END) as aging_61_90,
                SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) > 90 THEN total - amount_paid ELSE 0 END) as aging_90_plus,
                SUM(CASE WHEN due_date >= CURDATE() THEN total - amount_paid ELSE 0 END) as not_yet_due
             FROM {$this->table}
             WHERE status IN ('unpaid','partial','overdue')"
        );
    }

    /**
     * Get total revenue by date range
     */
    public function getTotalRevenue($startDate, $endDate)
    {
        $result = $this->query(
            "SELECT COALESCE(SUM(total), 0) as total_revenue FROM {$this->table}
             WHERE status IN ('paid','partial') AND invoice_date BETWEEN ? AND ?",
            [$startDate, $endDate], 'ss'
        );
        return floatval($result[0]['total_revenue'] ?? 0);
    }

    /**
     * Monthly revenue for chart
     */
    public function getMonthlyRevenue($year)
    {
        return $this->query(
            "SELECT MONTH(invoice_date) as month, 
                    COALESCE(SUM(total), 0) as revenue,
                    COUNT(*) as invoice_count
             FROM {$this->table}
             WHERE YEAR(invoice_date) = ? AND status IN ('paid','partial','unpaid')
             GROUP BY MONTH(invoice_date)
             ORDER BY month",
            [$year], 'i'
        );
    }
}

class FinanceInvoiceItemModel extends BaseModel
{
    protected $table = 'finance_invoice_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_id', 'description', 'quantity', 'unit',
        'unit_price', 'discount_percent', 'amount', 'account_id', 'sort_order'
    ];

    public function getByInvoice($invoiceId)
    {
        return $this->query(
            "SELECT fii.*, coa.code as account_code, coa.name as account_name
             FROM {$this->table} fii
             LEFT JOIN finance_chart_of_accounts coa ON fii.account_id = coa.id
             WHERE fii.invoice_id = ? ORDER BY fii.sort_order, fii.id",
            [$invoiceId], 'i'
        );
    }

    public function deleteByInvoice($invoiceId)
    {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE invoice_id = ?",
            [$invoiceId], 'i'
        );
    }
}

// ═══════════════════════════════════════════════════════════════
// BILLS - Accounts Payable (AP)
// ═══════════════════════════════════════════════════════════════

class FinanceBillModel extends BaseModel
{
    protected $table = 'finance_bills';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'bill_no', 'vendor_name', 'vendor_address', 'vendor_phone', 'vendor_email',
        'bill_date', 'due_date',
        'subtotal', 'tax_percent', 'tax_amount', 'total', 'amount_paid',
        'currency_code', 'exchange_rate',
        'status', 'cost_center_id', 'expense_account_id', 'category',
        'notes', 'receipt_file',
        'paid_at', 'cancelled_at',
        'created_by', 'updated_by'
    ];

    /**
     * Get all bills with optional filters
     */
    public function getAllFiltered($filters = [])
    {
        $sql = "SELECT fb.*, cc.name as cost_center_name, cc.code as cost_center_code
                FROM {$this->table} fb
                LEFT JOIN finance_cost_centers cc ON fb.cost_center_id = cc.id
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            $sql .= " AND fb.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }
        if (!empty($filters['category'])) {
            $sql .= " AND fb.category = ?";
            $params[] = $filters['category'];
            $types .= 's';
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND fb.bill_date >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND fb.bill_date <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }

        $sql .= " ORDER BY fb.created_at DESC";

        return $this->query($sql, $params, $types);
    }

    /**
     * Get bill detail with cost center
     */
    public function getDetail($id)
    {
        $sql = "SELECT fb.*, cc.name as cost_center_name, cc.code as cost_center_code,
                       coa.name as expense_account_name, coa.code as expense_account_code
                FROM {$this->table} fb
                LEFT JOIN finance_cost_centers cc ON fb.cost_center_id = cc.id
                LEFT JOIN finance_chart_of_accounts coa ON fb.expense_account_id = coa.id
                WHERE fb.id = ?";
        $result = $this->query($sql, [$id], 'i');
        return $result[0] ?? null;
    }

    /**
     * Generate bill number
     */
    public function generateBillNo()
    {
        $prefix = 'BILL-' . date('Ym') . '-';
        $result = $this->query(
            "SELECT bill_no FROM {$this->table} WHERE bill_no LIKE ? ORDER BY id DESC LIMIT 1",
            [$prefix . '%'], 's'
        );

        $lastNo = !empty($result) ? intval(substr($result[0]['bill_no'], -3)) + 1 : 1;
        return $prefix . str_pad($lastNo, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate bill totals
     */
    public function recalculateTotals($billId)
    {
        $bill = $this->find($billId);
        if (!$bill) return false;

        $items = $this->query(
            "SELECT SUM(amount) as subtotal FROM finance_bill_items WHERE bill_id = ?",
            [$billId], 'i'
        );

        $subtotal = floatval($items[0]['subtotal'] ?? 0);
        $taxAmount = $subtotal * floatval($bill['tax_percent']) / 100;
        $total = $subtotal + $taxAmount;

        return $this->update($billId, [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2)
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($billId)
    {
        $payments = $this->query(
            "SELECT COALESCE(SUM(amount), 0) as total_paid FROM finance_payments WHERE payment_type = 'payable' AND reference_id = ?",
            [$billId], 'i'
        );

        $totalPaid = floatval($payments[0]['total_paid'] ?? 0);
        $bill = $this->find($billId);
        $total = floatval($bill['total'] ?? 0);

        $status = $bill['status'];
        if ($totalPaid >= $total && $total > 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        }

        return $this->update($billId, [
            'amount_paid' => round($totalPaid, 2),
            'status' => $status,
            'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : $bill['paid_at']
        ]);
    }

    /**
     * Get total expenses by date
     */
    public function getTotalExpenses($startDate, $endDate)
    {
        $result = $this->query(
            "SELECT COALESCE(SUM(total), 0) as total_expense FROM {$this->table}
             WHERE status IN ('paid','partial','unpaid') AND bill_date BETWEEN ? AND ?",
            [$startDate, $endDate], 'ss'
        );
        return floatval($result[0]['total_expense'] ?? 0);
    }

    /**
     * Monthly expenses for chart
     */
    public function getMonthlyExpenses($year)
    {
        return $this->query(
            "SELECT MONTH(bill_date) as month,
                    COALESCE(SUM(total), 0) as expense,
                    COUNT(*) as bill_count
             FROM {$this->table}
             WHERE YEAR(bill_date) = ? AND status IN ('paid','partial','unpaid')
             GROUP BY MONTH(bill_date)
             ORDER BY month",
            [$year], 'i'
        );
    }

    /**
     * Expenses by category
     */
    public function getByCategory($startDate, $endDate)
    {
        return $this->query(
            "SELECT category, 
                    COUNT(*) as count,
                    COALESCE(SUM(total), 0) as total
             FROM {$this->table}
             WHERE bill_date BETWEEN ? AND ? AND status != 'cancelled'
             GROUP BY category
             ORDER BY total DESC",
            [$startDate, $endDate], 'ss'
        );
    }
}

class FinanceBillItemModel extends BaseModel
{
    protected $table = 'finance_bill_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'bill_id', 'description', 'quantity', 'unit',
        'unit_price', 'amount', 'account_id', 'sort_order'
    ];

    public function getByBill($billId)
    {
        return $this->query(
            "SELECT fbi.*, coa.code as account_code, coa.name as account_name
             FROM {$this->table} fbi
             LEFT JOIN finance_chart_of_accounts coa ON fbi.account_id = coa.id
             WHERE fbi.bill_id = ? ORDER BY fbi.sort_order, fbi.id",
            [$billId], 'i'
        );
    }

    public function deleteByBill($billId)
    {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE bill_id = ?",
            [$billId], 'i'
        );
    }
}

// ═══════════════════════════════════════════════════════════════
// JOURNAL ENTRIES - General Ledger
// ═══════════════════════════════════════════════════════════════

class FinanceJournalEntryModel extends BaseModel
{
    protected $table = 'finance_journal_entries';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'entry_no', 'entry_date', 'reference_no',
        'source_type', 'source_id',
        'description', 'total_debit', 'total_credit',
        'is_auto', 'is_posted', 'is_reversed', 'reversed_by',
        'created_by'
    ];

    /**
     * Get all journal entries with filters
     */
    public function getAllFiltered($filters = [])
    {
        $sql = "SELECT je.*,
                    (SELECT GROUP_CONCAT(DISTINCT coa.code ORDER BY coa.code SEPARATOR ', ') 
                     FROM finance_journal_lines jl 
                     JOIN finance_chart_of_accounts coa ON jl.account_id = coa.id 
                     WHERE jl.journal_entry_id = je.id) as accounts_used
                FROM {$this->table} je
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['date_from'])) {
            $sql .= " AND je.entry_date >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND je.entry_date <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }
        if (!empty($filters['source_type'])) {
            $sql .= " AND je.source_type = ?";
            $params[] = $filters['source_type'];
            $types .= 's';
        }
        if (isset($filters['is_auto'])) {
            $sql .= " AND je.is_auto = ?";
            $params[] = (int)$filters['is_auto'];
            $types .= 'i';
        }

        $sql .= " ORDER BY je.entry_date DESC, je.id DESC";

        return $this->query($sql, $params, $types);
    }

    /**
     * Generate entry number
     */
    public function generateEntryNo()
    {
        $prefix = 'JV-' . date('Ym') . '-';
        $result = $this->query(
            "SELECT entry_no FROM {$this->table} WHERE entry_no LIKE ? ORDER BY id DESC LIMIT 1",
            [$prefix . '%'], 's'
        );

        $lastNo = !empty($result) ? intval(substr($result[0]['entry_no'], -4)) + 1 : 1;
        return $prefix . str_pad($lastNo, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create auto-journal for invoice creation
     */
    public function createInvoiceJournal($invoice, $items)
    {
        $arAccount = $this->getAccountId('1-1400'); // Piutang Usaha
        $revenueAccountId = $invoice['revenue_account_id'] ?? $this->getAccountId('4-1000');

        $entryId = $this->insert([
            'entry_no' => $this->generateEntryNo(),
            'entry_date' => $invoice['invoice_date'],
            'reference_no' => $invoice['invoice_no'],
            'source_type' => 'invoice',
            'source_id' => $invoice['id'],
            'description' => "Invoice {$invoice['invoice_no']} — AR Recognition",
            'total_debit' => $invoice['total'],
            'total_credit' => $invoice['total'],
            'is_auto' => 1,
            'is_posted' => 1,
            'created_by' => $invoice['created_by'] ?? null
        ]);

        if (!$entryId) return false;

        $lineModel = new FinanceJournalLineModel($this->db);

        // Debit: Accounts Receivable
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $arAccount,
            'cost_center_id' => $invoice['cost_center_id'] ?? null,
            'debit' => $invoice['total'],
            'credit' => 0,
            'description' => "Piutang atas {$invoice['invoice_no']}"
        ]);

        // Credit: Revenue (subtotal before tax)
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $revenueAccountId,
            'cost_center_id' => $invoice['cost_center_id'] ?? null,
            'debit' => 0,
            'credit' => $invoice['subtotal'] - $invoice['discount_amount'],
            'description' => "Pendapatan atas {$invoice['invoice_no']}"
        ]);

        // Credit: Tax Payable (if any)
        if ($invoice['tax_amount'] > 0) {
            $taxAccount = $this->getAccountId('2-1100'); // Hutang Pajak
            $lineModel->insert([
                'journal_entry_id' => $entryId,
                'account_id' => $taxAccount,
                'cost_center_id' => null,
                'debit' => 0,
                'credit' => $invoice['tax_amount'],
                'description' => "PPN atas {$invoice['invoice_no']}"
            ]);
        }

        return $entryId;
    }

    /**
     * Create auto-journal for invoice payment
     */
    public function createInvoicePaymentJournal($payment, $invoice)
    {
        $cashAccount = $payment['bank_account_id'] ?? $this->getAccountId('1-1000');
        $arAccount = $this->getAccountId('1-1400');

        $entryId = $this->insert([
            'entry_no' => $this->generateEntryNo(),
            'entry_date' => $payment['payment_date'],
            'reference_no' => $payment['payment_no'] ?? $invoice['invoice_no'],
            'source_type' => 'invoice_payment',
            'source_id' => $payment['id'],
            'description' => "Payment for Invoice {$invoice['invoice_no']}",
            'total_debit' => $payment['amount'],
            'total_credit' => $payment['amount'],
            'is_auto' => 1,
            'is_posted' => 1,
            'created_by' => $payment['created_by'] ?? null
        ]);

        if (!$entryId) return false;

        $lineModel = new FinanceJournalLineModel($this->db);

        // Debit: Cash/Bank
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $cashAccount,
            'debit' => $payment['amount'],
            'credit' => 0,
            'description' => "Terima pembayaran {$invoice['invoice_no']}"
        ]);

        // Credit: Accounts Receivable
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $arAccount,
            'debit' => 0,
            'credit' => $payment['amount'],
            'description' => "Pelunasan piutang {$invoice['invoice_no']}"
        ]);

        return $entryId;
    }

    /**
     * Create auto-journal for bill receipt
     */
    public function createBillJournal($bill, $items)
    {
        $apAccount = $this->getAccountId('2-1000'); // Hutang Usaha
        $expenseAccountId = $bill['expense_account_id'] ?? $this->getAccountId('6-9000');

        $entryId = $this->insert([
            'entry_no' => $this->generateEntryNo(),
            'entry_date' => $bill['bill_date'],
            'reference_no' => $bill['bill_no'],
            'source_type' => 'bill',
            'source_id' => $bill['id'],
            'description' => "Bill {$bill['bill_no']} — {$bill['vendor_name']}",
            'total_debit' => $bill['total'],
            'total_credit' => $bill['total'],
            'is_auto' => 1,
            'is_posted' => 1,
            'created_by' => $bill['created_by'] ?? null
        ]);

        if (!$entryId) return false;

        $lineModel = new FinanceJournalLineModel($this->db);

        // Debit: Expense/COGS account
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $expenseAccountId,
            'cost_center_id' => $bill['cost_center_id'] ?? null,
            'debit' => $bill['total'],
            'credit' => 0,
            'description' => "Biaya dari {$bill['vendor_name']}"
        ]);

        // Credit: Accounts Payable
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $apAccount,
            'cost_center_id' => null,
            'debit' => 0,
            'credit' => $bill['total'],
            'description' => "Hutang ke {$bill['vendor_name']}"
        ]);

        return $entryId;
    }

    /**
     * Create auto-journal for bill payment
     */
    public function createBillPaymentJournal($payment, $bill)
    {
        $cashAccount = $payment['bank_account_id'] ?? $this->getAccountId('1-1000');
        $apAccount = $this->getAccountId('2-1000');

        $entryId = $this->insert([
            'entry_no' => $this->generateEntryNo(),
            'entry_date' => $payment['payment_date'],
            'reference_no' => $payment['payment_no'] ?? $bill['bill_no'],
            'source_type' => 'bill_payment',
            'source_id' => $payment['id'],
            'description' => "Payment for Bill {$bill['bill_no']} — {$bill['vendor_name']}",
            'total_debit' => $payment['amount'],
            'total_credit' => $payment['amount'],
            'is_auto' => 1,
            'is_posted' => 1,
            'created_by' => $payment['created_by'] ?? null
        ]);

        if (!$entryId) return false;

        $lineModel = new FinanceJournalLineModel($this->db);

        // Debit: Accounts Payable
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $apAccount,
            'debit' => $payment['amount'],
            'credit' => 0,
            'description' => "Pelunasan hutang {$bill['bill_no']}"
        ]);

        // Credit: Cash/Bank
        $lineModel->insert([
            'journal_entry_id' => $entryId,
            'account_id' => $cashAccount,
            'debit' => 0,
            'credit' => $payment['amount'],
            'description' => "Pembayaran ke {$bill['vendor_name']}"
        ]);

        return $entryId;
    }

    /**
     * Helper: get account ID by code
     */
    private function getAccountId($code)
    {
        $result = $this->query(
            "SELECT id FROM finance_chart_of_accounts WHERE code = ? AND is_active = 1 LIMIT 1",
            [$code], 's'
        );
        return $result[0]['id'] ?? null;
    }
}

class FinanceJournalLineModel extends BaseModel
{
    protected $table = 'finance_journal_lines';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'journal_entry_id', 'account_id', 'cost_center_id',
        'debit', 'credit', 'description'
    ];

    public function getByEntry($entryId)
    {
        return $this->query(
            "SELECT jl.*, coa.code as account_code, coa.name as account_name, coa.type as account_type,
                    cc.code as cost_center_code, cc.name as cost_center_name
             FROM {$this->table} jl
             LEFT JOIN finance_chart_of_accounts coa ON jl.account_id = coa.id
             LEFT JOIN finance_cost_centers cc ON jl.cost_center_id = cc.id
             WHERE jl.journal_entry_id = ? ORDER BY jl.id",
            [$entryId], 'i'
        );
    }

    public function deleteByEntry($entryId)
    {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE journal_entry_id = ?",
            [$entryId], 'i'
        );
    }
}

// ═══════════════════════════════════════════════════════════════
// PAYMENTS
// ═══════════════════════════════════════════════════════════════

class FinancePaymentModel extends BaseModel
{
    protected $table = 'finance_payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'payment_no', 'payment_type', 'reference_id',
        'payment_date', 'amount',
        'payment_method', 'bank_account_id',
        'reference_number', 'notes',
        'journal_entry_id', 'created_by'
    ];

    public function getByInvoice($invoiceId)
    {
        return $this->query(
            "SELECT fp.*, coa.name as bank_name, coa.code as bank_code
             FROM {$this->table} fp
             LEFT JOIN finance_chart_of_accounts coa ON fp.bank_account_id = coa.id
             WHERE fp.payment_type = 'receivable' AND fp.reference_id = ?
             ORDER BY fp.payment_date DESC",
            [$invoiceId], 'i'
        );
    }

    public function getByBill($billId)
    {
        return $this->query(
            "SELECT fp.*, coa.name as bank_name, coa.code as bank_code
             FROM {$this->table} fp
             LEFT JOIN finance_chart_of_accounts coa ON fp.bank_account_id = coa.id
             WHERE fp.payment_type = 'payable' AND fp.reference_id = ?
             ORDER BY fp.payment_date DESC",
            [$billId], 'i'
        );
    }

    public function generatePaymentNo($type)
    {
        $prefix = ($type === 'receivable' ? 'RCV-' : 'PAY-') . date('Ym') . '-';
        $result = $this->query(
            "SELECT payment_no FROM {$this->table} WHERE payment_no LIKE ? ORDER BY id DESC LIMIT 1",
            [$prefix . '%'], 's'
        );
        $lastNo = !empty($result) ? intval(substr($result[0]['payment_no'], -4)) + 1 : 1;
        return $prefix . str_pad($lastNo, 4, '0', STR_PAD_LEFT);
    }
}

// ═══════════════════════════════════════════════════════════════
// PROFIT & LOSS REPORT
// ═══════════════════════════════════════════════════════════════

class FinancePnLModel extends BaseModel
{
    protected $table = 'finance_journal_entries';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    /**
     * Generate P&L report data
     * Returns hierarchical structure: Revenue > COGS > Gross Profit > OpEx > Net Profit
     */
    public function generate($startDate, $endDate, $costCenterId = null)
    {
        $whereCC = '';
        $params = [$startDate, $endDate];
        $types = 'ss';

        if ($costCenterId) {
            $whereCC = ' AND jl.cost_center_id = ?';
            $params[] = (int)$costCenterId;
            $types .= 'i';
        }

        // Get all account balances for the period
        $sql = "SELECT coa.id, coa.code, coa.name, coa.name_en, coa.type,
                    COALESCE(SUM(jl.debit), 0) as total_debit,
                    COALESCE(SUM(jl.credit), 0) as total_credit
                FROM finance_chart_of_accounts coa
                LEFT JOIN finance_journal_lines jl ON jl.account_id = coa.id
                LEFT JOIN finance_journal_entries je ON jl.journal_entry_id = je.id
                    AND je.entry_date BETWEEN ? AND ? AND je.is_posted = 1
                    {$whereCC}
                WHERE coa.is_active = 1 
                    AND coa.type IN ('revenue','cogs','expense')
                    AND coa.code NOT LIKE '%-0000'
                GROUP BY coa.id, coa.code, coa.name, coa.name_en, coa.type
                HAVING total_debit > 0 OR total_credit > 0
                ORDER BY coa.type, coa.code";

        $accounts = $this->query($sql, $params, $types);

        $revenue = [];
        $cogs = [];
        $expenses = [];
        $totalRevenue = 0;
        $totalCogs = 0;
        $totalExpenses = 0;

        foreach ($accounts as $acc) {
            // Revenue: balance = credit - debit
            // COGS/Expense: balance = debit - credit
            if ($acc['type'] === 'revenue') {
                $balance = floatval($acc['total_credit']) - floatval($acc['total_debit']);
                $revenue[] = array_merge($acc, ['balance' => $balance]);
                $totalRevenue += $balance;
            } elseif ($acc['type'] === 'cogs') {
                $balance = floatval($acc['total_debit']) - floatval($acc['total_credit']);
                $cogs[] = array_merge($acc, ['balance' => $balance]);
                $totalCogs += $balance;
            } else { // expense
                $balance = floatval($acc['total_debit']) - floatval($acc['total_credit']);
                $expenses[] = array_merge($acc, ['balance' => $balance]);
                $totalExpenses += $balance;
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'revenue' => ['items' => $revenue, 'total' => $totalRevenue],
            'cogs' => ['items' => $cogs, 'total' => $totalCogs],
            'gross_profit' => $grossProfit,
            'expenses' => ['items' => $expenses, 'total' => $totalExpenses],
            'net_profit' => $netProfit,
            'margin' => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0
        ];
    }
}

// ═══════════════════════════════════════════════════════════════
// DASHBOARD (Aggregated finance stats)
// Includes: Contract Revenue, Crew Costs, Invoices, Bills, Payroll
// ═══════════════════════════════════════════════════════════════

class FinanceDashboardModel extends BaseModel
{
    protected $table = 'finance_invoices'; // default, unused
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    /**
     * Default exchange rates to IDR for multi-currency conversion
     */
    private $defaultRatesIDR = [
        'IDR' => 1,
        'USD' => 15800,
        'SGD' => 11700,
        'EUR' => 16600,
        'MYR' => 3500,
    ];

    /**
     * Convert amount to IDR using contract exchange_rate or default
     */
    private function toIDR($amount, $currencyCode, $contractRate = 0)
    {
        if (!$currencyCode || $currencyCode === 'IDR') return $amount;
        if ($contractRate > 0) return $amount * $contractRate;
        return $amount * ($this->defaultRatesIDR[$currencyCode] ?? 15800);
    }

    /**
     * Get dashboard stats for current month/year
     * Now includes contract revenue, crew costs, and payroll
     */
    public function getStats($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // ─── CONTRACT REVENUE (client_rate from active contracts) ───
        $contractRevenue = $this->getContractRevenue();
        $crewCost = $this->getCrewCost();

        // ─── INVOICE REVENUE (finance_invoices) ───
        $rev = $this->query(
            "SELECT COALESCE(SUM(amount_paid), 0) as val FROM finance_invoices WHERE status IN ('paid','partial') AND invoice_date BETWEEN ? AND ?",
            [$startDate, $endDate], 'ss'
        );
        $invoiceRevenue = floatval($rev[0]['val'] ?? 0);

        // ─── BILL EXPENSES (finance_bills) ───
        $exp = $this->query(
            "SELECT COALESCE(SUM(amount_paid), 0) as val FROM finance_bills WHERE status IN ('paid','partial') AND bill_date BETWEEN ? AND ?",
            [$startDate, $endDate], 'ss'
        );
        $billExpenses = floatval($exp[0]['val'] ?? 0);

        // ─── PAYROLL EXPENSES (payroll_items for this month) ───
        $payrollExp = $this->query(
            "SELECT COALESCE(SUM(pi.net_salary), 0) as val 
             FROM payroll_items pi
             JOIN payroll_periods pp ON pi.payroll_period_id = pp.id
             WHERE pp.period_month = ? AND pp.period_year = ?",
            [(int)$month, (int)$year], 'ii'
        );
        $payrollExpenses = floatval($payrollExp[0]['val'] ?? 0);

        // ─── ACTIVE CONTRACT COUNTS ───
        $activeContracts = $this->query(
            "SELECT COUNT(*) as count FROM contracts WHERE status IN ('active','onboard')"
        );
        $totalCrew = $this->query(
            "SELECT COUNT(DISTINCT crew_id) as count FROM contracts WHERE status IN ('active','onboard')"
        );

        // ─── TOTAL REVENUE & EXPENSES ───
        $totalRevenue = $contractRevenue + $invoiceRevenue;
        $totalExpenses = $crewCost + $billExpenses + $payrollExpenses;

        // ─── OUTSTANDING INVOICES ───
        $outstanding = $this->query(
            "SELECT COUNT(*) as count, COALESCE(SUM(total - amount_paid), 0) as amount FROM finance_invoices WHERE status IN ('unpaid','partial','overdue')"
        );

        // ─── OUTSTANDING BILLS ───
        $outstandingBills = $this->query(
            "SELECT COUNT(*) as count, COALESCE(SUM(total - amount_paid), 0) as amount FROM finance_bills WHERE status IN ('unpaid','partial','overdue')"
        );

        // ─── OVERDUE INVOICES ───
        $overdue = $this->query(
            "SELECT COUNT(*) as count, COALESCE(SUM(total - amount_paid), 0) as amount FROM finance_invoices WHERE status IN ('unpaid','partial') AND due_date < CURDATE()"
        );

        return [
            // Combined totals
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,

            // Breakdown
            'contract_revenue' => $contractRevenue,
            'invoice_revenue' => $invoiceRevenue,
            'crew_cost' => $crewCost,
            'bill_expenses' => $billExpenses,
            'payroll_expenses' => $payrollExpenses,
            'gross_margin' => $contractRevenue - $crewCost,

            // Operational
            'active_contracts' => intval($activeContracts[0]['count'] ?? 0),
            'total_crew' => intval($totalCrew[0]['count'] ?? 0),

            // Outstanding
            'outstanding_receivable' => floatval($outstanding[0]['amount'] ?? 0),
            'outstanding_receivable_count' => intval($outstanding[0]['count'] ?? 0),
            'outstanding_payable' => floatval($outstandingBills[0]['amount'] ?? 0),
            'outstanding_payable_count' => intval($outstandingBills[0]['count'] ?? 0),
            'overdue_count' => intval($overdue[0]['count'] ?? 0),
            'overdue_amount' => floatval($overdue[0]['amount'] ?? 0),
        ];
    }

    /**
     * Get monthly contract revenue from active contracts (client_rate)
     * Converts all currencies to IDR
     */
    public function getContractRevenue()
    {
        $rows = $this->query(
            "SELECT cs.client_rate, cs.exchange_rate AS contract_rate,
                    cur.code AS currency_code
             FROM contracts c
             JOIN contract_salaries cs ON c.id = cs.contract_id
             LEFT JOIN currencies cur ON cs.currency_id = cur.id
             WHERE c.status IN ('active','onboard')"
        );

        $total = 0;
        foreach ($rows as $row) {
            $rate = floatval($row['client_rate'] ?? 0);
            $currency = $row['currency_code'] ?? 'IDR';
            $contractRate = floatval($row['contract_rate'] ?? 0);

            // Auto-detect: if amount > 1M and marked USD, it's likely IDR
            if ((!$currency || $currency === 'USD') && $rate > 1000000) {
                $currency = 'IDR';
            }

            $total += $this->toIDR($rate, $currency, $contractRate);
        }
        return round($total, 0);
    }

    /**
     * Get monthly crew cost from active contracts (total_monthly)
     * Converts all currencies to IDR
     */
    public function getCrewCost()
    {
        $rows = $this->query(
            "SELECT cs.total_monthly, cs.exchange_rate AS contract_rate,
                    cur.code AS currency_code
             FROM contracts c
             JOIN contract_salaries cs ON c.id = cs.contract_id
             LEFT JOIN currencies cur ON cs.currency_id = cur.id
             WHERE c.status IN ('active','onboard')"
        );

        $total = 0;
        foreach ($rows as $row) {
            $cost = floatval($row['total_monthly'] ?? 0);
            $currency = $row['currency_code'] ?? 'IDR';
            $contractRate = floatval($row['contract_rate'] ?? 0);

            if ((!$currency || $currency === 'USD') && $cost > 1000000) {
                $currency = 'IDR';
            }

            $total += $this->toIDR($cost, $currency, $contractRate);
        }
        return round($total, 0);
    }

    /**
     * Get top clients by revenue (from active contracts)
     */
    public function getTopClientRevenue($limit = 5)
    {
        $rows = $this->query(
            "SELECT cl.id, cl.name, 
                    cs.client_rate, cs.total_monthly, cs.exchange_rate AS contract_rate,
                    cur.code AS currency_code
             FROM contracts c
             JOIN contract_salaries cs ON c.id = cs.contract_id
             JOIN clients cl ON c.client_id = cl.id
             LEFT JOIN currencies cur ON cs.currency_id = cur.id
             WHERE c.status IN ('active','onboard')"
        );

        $clients = [];
        foreach ($rows as $row) {
            $clientId = $row['id'];
            $currency = $row['currency_code'] ?? 'IDR';
            $contractRate = floatval($row['contract_rate'] ?? 0);
            $revenue = floatval($row['client_rate'] ?? 0);
            $cost = floatval($row['total_monthly'] ?? 0);

            if ((!$currency || $currency === 'USD') && $revenue > 1000000) {
                $currency = 'IDR';
            }

            $revIDR = $this->toIDR($revenue, $currency, $contractRate);
            $costIDR = $this->toIDR($cost, $currency, $contractRate);

            if (!isset($clients[$clientId])) {
                $clients[$clientId] = [
                    'id' => $clientId,
                    'name' => $row['name'],
                    'revenue' => 0,
                    'cost' => 0,
                    'crew_count' => 0,
                ];
            }
            $clients[$clientId]['revenue'] += $revIDR;
            $clients[$clientId]['cost'] += $costIDR;
            $clients[$clientId]['crew_count']++;
        }

        // Calculate profit and sort
        foreach ($clients as &$c) {
            $c['profit'] = $c['revenue'] - $c['cost'];
            $c['margin'] = $c['revenue'] > 0 ? round(($c['profit'] / $c['revenue']) * 100, 1) : 0;
        }
        unset($c);

        usort($clients, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        return array_slice($clients, 0, $limit);
    }

    /**
     * Get top vessels by revenue (from active contracts)
     */
    public function getTopVesselRevenue($limit = 5)
    {
        $rows = $this->query(
            "SELECT v.id, v.name AS vessel_name,
                    cl.name AS client_name,
                    cs.client_rate, cs.total_monthly, cs.exchange_rate AS contract_rate,
                    cur.code AS currency_code
             FROM contracts c
             JOIN contract_salaries cs ON c.id = cs.contract_id
             JOIN vessels v ON c.vessel_id = v.id
             JOIN clients cl ON c.client_id = cl.id
             LEFT JOIN currencies cur ON cs.currency_id = cur.id
             WHERE c.status IN ('active','onboard')"
        );

        $vessels = [];
        foreach ($rows as $row) {
            $vesselId = $row['id'];
            $currency = $row['currency_code'] ?? 'IDR';
            $contractRate = floatval($row['contract_rate'] ?? 0);
            $revenue = floatval($row['client_rate'] ?? 0);
            $cost = floatval($row['total_monthly'] ?? 0);

            if ((!$currency || $currency === 'USD') && $revenue > 1000000) {
                $currency = 'IDR';
            }

            $revIDR = $this->toIDR($revenue, $currency, $contractRate);
            $costIDR = $this->toIDR($cost, $currency, $contractRate);

            if (!isset($vessels[$vesselId])) {
                $vessels[$vesselId] = [
                    'id' => $vesselId,
                    'vessel_name' => $row['vessel_name'],
                    'client_name' => $row['client_name'],
                    'revenue' => 0,
                    'cost' => 0,
                    'crew_count' => 0,
                ];
            }
            $vessels[$vesselId]['revenue'] += $revIDR;
            $vessels[$vesselId]['cost'] += $costIDR;
            $vessels[$vesselId]['crew_count']++;
        }

        foreach ($vessels as &$v) {
            $v['profit'] = $v['revenue'] - $v['cost'];
            $v['margin'] = $v['revenue'] > 0 ? round(($v['profit'] / $v['revenue']) * 100, 1) : 0;
        }
        unset($v);

        usort($vessels, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        return array_slice($vessels, 0, $limit);
    }

    /**
     * Get recent transactions (invoices + bills + payments)
     */
    public function getRecentTransactions($limit = 10)
    {
        $sql = "(SELECT 'invoice' as type, invoice_no as ref_no, 
                        (SELECT name FROM clients WHERE id = client_id) as description,
                        total as amount, status, created_at as date, 'in' as direction
                 FROM finance_invoices ORDER BY created_at DESC LIMIT ?)
                UNION ALL
                (SELECT 'bill' as type, bill_no as ref_no,
                        vendor_name as description,
                        total as amount, status, created_at as date, 'out' as direction
                 FROM finance_bills ORDER BY created_at DESC LIMIT ?)
                ORDER BY date DESC LIMIT ?";

        return $this->query($sql, [$limit, $limit, $limit], 'iii');
    }

    /**
     * Get monthly trend (includes contract revenue estimate per month)
     */
    public function getMonthlyTrend($months = 12)
    {
        // Get current contract revenue as monthly estimate
        $contractRevMonthly = $this->getContractRevenue();
        $crewCostMonthly = $this->getCrewCost();

        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-{$i} months"));
            $endDate = date('Y-m-t', strtotime($date));
            $label = date('M Y', strtotime($date));
            $monthNum = (int)date('m', strtotime($date));
            $yearNum = (int)date('Y', strtotime($date));

            // Invoice revenue
            $rev = $this->query(
                "SELECT COALESCE(SUM(total), 0) as val FROM finance_invoices WHERE status IN ('paid','partial','unpaid') AND invoice_date BETWEEN ? AND ?",
                [$date, $endDate], 'ss'
            );
            $invoiceRev = floatval($rev[0]['val'] ?? 0);

            // Bill expenses
            $exp = $this->query(
                "SELECT COALESCE(SUM(total), 0) as val FROM finance_bills WHERE status IN ('paid','partial','unpaid') AND bill_date BETWEEN ? AND ?",
                [$date, $endDate], 'ss'
            );
            $billExp = floatval($exp[0]['val'] ?? 0);

            // Payroll expenses for that month
            $payroll = $this->query(
                "SELECT COALESCE(SUM(net_salary), 0) as val FROM payroll_items pi JOIN payroll_periods pp ON pi.payroll_period_id = pp.id WHERE pp.period_month = ? AND pp.period_year = ?",
                [$monthNum, $yearNum], 'ii'
            );
            $payrollExp = floatval($payroll[0]['val'] ?? 0);

            // For current and recent months, use actual contract data
            // For past months, estimate using current rates (approximation)
            $isCurrentOrRecent = ($i <= 2);
            $contractRev = $isCurrentOrRecent ? $contractRevMonthly : 0;
            $crewExp = $isCurrentOrRecent ? $crewCostMonthly : 0;

            $data[] = [
                'label' => $label,
                'revenue' => $invoiceRev + $contractRev,
                'expense' => $billExp + $payrollExp + $crewExp,
                'contract_revenue' => $contractRev,
                'invoice_revenue' => $invoiceRev,
                'crew_cost' => $crewExp,
                'bill_expense' => $billExp,
                'payroll_expense' => $payrollExp,
            ];
        }
        return $data;
    }
}
