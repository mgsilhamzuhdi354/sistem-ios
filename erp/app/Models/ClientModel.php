<?php
/**
 * PT Indo Ocean - ERP System
 * Client Model
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class ClientModel extends BaseModel
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    private $_usdRate = null;

    /**
     * Get USD exchange rate to IDR from currencies table (cached)
     */
    private function getUsdRate()
    {
        if ($this->_usdRate === null) {
            $result = $this->query("SELECT exchange_rate_to_idr FROM currencies WHERE code = 'USD' LIMIT 1");
            $this->_usdRate = (float)($result[0]['exchange_rate_to_idr'] ?? 15800);
            if ($this->_usdRate <= 0) $this->_usdRate = 15800;
        }
        return $this->_usdRate;
    }

    /**
     * Convert any amount to USD using proper exchange rates
     * @param float $amount - the amount in original currency
     * @param string $currencyCode - currency code (USD, IDR, MYR, etc.)
     * @param float $rateToIdr - the exchange_rate from contract (IDR per 1 unit of currency)
     * @return float amount in USD
     */
    private function convertToUsd($amount, $currencyCode, $rateToIdr = 0)
    {
        if ($amount == 0) return 0;
        $currencyCode = strtoupper($currencyCode ?: 'USD');

        // USD needs no conversion
        if ($currencyCode === 'USD') return (float)$amount;

        $usdRate = $this->getUsdRate(); // e.g. 15800

        // For IDR: always divide by USD rate, ignore contract rate
        // (contract rate for IDR is 1.0 which is meaningless for USD conversion)
        if ($currencyCode === 'IDR') {
            return $amount / $usdRate;
        }

        // For other currencies (MYR, SGD, EUR, etc.):
        // Convert to IDR first using contract rate, then to USD
        // amount_usd = amount * rateToIdr / usdRate
        if ($rateToIdr > 0) {
            return ($amount * $rateToIdr) / $usdRate;
        }

        // Fallback: load rate from currencies table
        $result = $this->query(
            "SELECT exchange_rate_to_idr FROM currencies WHERE code = ? LIMIT 1",
            [$currencyCode], 's'
        );
        $fallbackRate = (float)($result[0]['exchange_rate_to_idr'] ?? 0);
        if ($fallbackRate > 0) {
            return ($amount * $fallbackRate) / $usdRate;
        }

        // Last resort: hardcoded defaults
        $defaults = ['SGD' => 11800, 'EUR' => 17200, 'MYR' => 3500];
        $rate = $defaults[$currencyCode] ?? 1;
        return ($amount * $rate) / $usdRate;
    }

    /**
     * Auto-detect actual currency when DB data may be wrong
     */
    private function detectCurrency($currencyCode, $amount, $symbol = null)
    {
        // If currency is NULL or USD but amount > 1M, it's likely IDR
        if ((!$currencyCode || $currencyCode === 'USD') && $amount > 1000000) {
            return ['code' => 'IDR', 'symbol' => 'Rp'];
        }
        if (!$currencyCode) {
            return ['code' => 'USD', 'symbol' => '$'];
        }
        return ['code' => $currencyCode, 'symbol' => $symbol ?? $currencyCode];
    }
    protected $allowedFields = [
        'name',
        'short_name',
        'country',
        'address',
        'city',
        'postal_code',
        'email',
        'phone',
        'website',
        'contact_person',
        'contact_email',
        'contact_phone',
        'notes',
        'is_active'
    ];

    /**
     * Get client with stats
     */
    public function getWithStats($id)
    {
        $client = $this->find($id);
        if (!$client)
            return null;

        // Get vessel count (vessels assigned to client OR with active contracts)
        $result = $this->query(
            "SELECT COUNT(DISTINCT v.id) as count FROM vessels v
             LEFT JOIN contracts ct ON ct.vessel_id = v.id AND ct.client_id = ? AND ct.status IN ('active', 'onboard')
             WHERE v.is_active = 1 AND (v.client_id = ? OR ct.id IS NOT NULL)",
            [$id, $id],
            'ii'
        );
        $client['vessel_count'] = $result[0]['count'] ?? 0;

        // Get active crew count
        $result = $this->query(
            "SELECT COUNT(*) as count FROM contracts WHERE client_id = ? AND status IN ('active', 'onboard')",
            [$id],
            'i'
        );
        $client['active_crew_count'] = $result[0]['count'] ?? 0;

        // Get monthly cost
        $result = $this->query(
            "SELECT SUM(cs.total_monthly) as total 
             FROM contracts c
             LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
             WHERE c.client_id = ? AND c.status IN ('active', 'onboard')",
            [$id],
            'i'
        );
        $client['monthly_cost'] = $result[0]['total'] ?? 0;

        return $client;
    }

    /**
     * Get all clients with stats (including USD converted monthly cost)
     */
    public function getAllWithStats()
    {
        // First get basic client data
        $sql = "SELECT c.*,
                    (SELECT COUNT(DISTINCT v.id) FROM vessels v
                     LEFT JOIN contracts ct3 ON ct3.vessel_id = v.id AND ct3.client_id = c.id AND ct3.status IN ('active', 'onboard')
                     WHERE v.is_active = 1 AND (v.client_id = c.id OR ct3.id IS NOT NULL)) AS vessel_count,
                    (SELECT COUNT(*) FROM contracts ct WHERE ct.client_id = c.id AND ct.status IN ('active', 'onboard')) AS active_crew_count
                FROM clients c
                WHERE c.is_active = 1
                ORDER BY c.name";

        $clients = $this->query($sql);

        // For each client, calculate monthly cost with currency conversion
        foreach ($clients as &$client) {
            $costSql = "SELECT cs.total_monthly, cs.exchange_rate AS contract_rate,
                            cur.code AS currency_code, cur.symbol AS currency_symbol
                        FROM contracts ct
                        LEFT JOIN contract_salaries cs ON ct.id = cs.contract_id
                        LEFT JOIN currencies cur ON cs.currency_id = cur.id
                        WHERE ct.client_id = ? AND ct.status IN ('active', 'onboard')";

            $costs = $this->query($costSql, [$client['id']], 'i');

            $totalUsd = 0;
            $byCurrency = [];
            $symbols = [];

            foreach ($costs as $cost) {
                $amount = $cost['total_monthly'] ?? 0;
                $contractRate = $cost['contract_rate'] ?? 0;
                $detected = $this->detectCurrency($cost['currency_code'], $amount, $cost['currency_symbol']);
                $currency = $detected['code'];
                $symbol = $detected['symbol'];

                if (!isset($byCurrency[$currency])) {
                    $byCurrency[$currency] = 0;
                    $symbols[$currency] = $symbol;
                }
                $byCurrency[$currency] += $amount;
                $totalUsd += $this->convertToUsd($amount, $currency, $contractRate);
            }

            $client['monthly_cost'] = round($totalUsd, 2);
            $client['monthly_cost_by_currency'] = $byCurrency;
            $client['currency_symbols'] = $symbols;
        }

        return $clients;
    }

    /**
     * Get client vessels
     */
    public function getVessels($clientId)
    {
        $sql = "SELECT v.*, 
                    vt.name AS vessel_type_name,
                    fs.emoji AS flag_emoji,
                    fs.name AS flag_state_name,
                    (SELECT COUNT(*) FROM contracts ct 
                     WHERE ct.vessel_id = v.id AND ct.client_id = ? AND ct.status IN ('active', 'onboard')) AS crew_count,
                    (SELECT MAX(ct2.sign_off_date) FROM contracts ct2 
                     WHERE ct2.vessel_id = v.id AND ct2.client_id = ? AND ct2.status IN ('active', 'onboard')) AS latest_sign_off
                FROM vessels v
                LEFT JOIN contracts c ON c.vessel_id = v.id AND c.client_id = ? AND c.status IN ('active', 'onboard')
                LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
                LEFT JOIN flag_states fs ON v.flag_state_id = fs.id
                WHERE v.is_active = 1 AND (v.client_id = ? OR c.id IS NOT NULL)
                GROUP BY v.id
                ORDER BY v.name";

        return $this->query($sql, [$clientId, $clientId, $clientId, $clientId], 'iiii');
    }

    /**
     * Get client contracts
     */
    public function getContracts($clientId)
    {
        $sql = "SELECT c.*, 
                    v.name AS vessel_name,
                    r.name AS rank_name,
                    DATEDIFF(c.sign_off_date, CURDATE()) AS days_remaining
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN ranks r ON c.rank_id = r.id
                WHERE c.client_id = ? AND c.status IN ('active', 'onboard')
                ORDER BY v.name, r.level";

        return $this->query($sql, [$clientId], 'i');
    }

    /**
     * Get client contracts with salary details (for client detail page)
     */
    public function getContractsWithSalary($clientId)
    {
        $sql = "SELECT c.*, 
                    v.name AS vessel_name,
                    r.name AS rank_name,
                    cs.total_monthly, cs.exchange_rate AS contract_rate, cs.client_rate,
                    cur.code AS currency_code, cur.symbol AS currency_symbol,
                    DATEDIFF(c.sign_off_date, CURDATE()) AS days_remaining
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN ranks r ON c.rank_id = r.id
                LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                WHERE c.client_id = ?
                ORDER BY 
                    CASE c.status 
                        WHEN 'active' THEN 1 
                        WHEN 'onboard' THEN 2 
                        WHEN 'pending' THEN 3 
                        WHEN 'draft' THEN 4 
                        ELSE 5 
                    END,
                    v.name, r.level";

        $contracts = $this->query($sql, [$clientId], 'i');

        // Add USD conversion for each contract
        foreach ($contracts as &$c) {
            $amount = $c['total_monthly'] ?? 0;
            $clientRateRaw = $c['client_rate'] ?? 0;
            $contractRate = $c['contract_rate'] ?? 0;

            // Auto-detect currency
            $maxAmt = max($amount, $clientRateRaw);
            $detected = $this->detectCurrency($c['currency_code'], $maxAmt, $c['currency_symbol']);
            $currency = $detected['code'];
            $c['currency_code'] = $currency;
            $c['currency_symbol'] = $detected['symbol'];

            // Calculate months active (from sign_on_date to now or sign_off_date)
            $signOn = $c['sign_on_date'] ?? null;
            $signOff = $c['sign_off_date'] ?? null;
            $c['months_active'] = 0;
            if ($signOn) {
                $start = new \DateTime($signOn);
                $end = $signOff && strtotime($signOff) < time() ? new \DateTime($signOff) : new \DateTime();
                $diff = $start->diff($end);
                $c['months_active'] = max(1, ($diff->y * 12) + $diff->m + ($diff->d > 0 ? 1 : 0));
            }

            // Calculate USD salary using proper conversion
            $c['salary_usd'] = round($this->convertToUsd($amount, $currency, $contractRate), 2);

            // Calculate monthly profit (in original currency)
            $c['profit'] = $clientRateRaw > 0 ? ($clientRateRaw - $amount) : 0;

            // Calculate monthly profit and client rate in USD
            if ($clientRateRaw > 0) {
                $c['client_rate_usd'] = round($this->convertToUsd($clientRateRaw, $currency, $contractRate), 2);
                $c['profit_usd'] = round($c['client_rate_usd'] - $c['salary_usd'], 2);
            } else {
                $c['profit_usd'] = 0;
                $c['client_rate_usd'] = 0;
            }

            // Calculate total accumulated profit
            $c['total_profit'] = $c['profit'] * $c['months_active'];
            $c['total_profit_usd'] = $c['profit_usd'] * $c['months_active'];
            $c['total_salary'] = $amount * $c['months_active'];
            $c['total_client_rate'] = $clientRateRaw * $c['months_active'];
        }

        return $contracts;
    }

    /**
     * Get monthly cost breakdown by currency (for client detail page)
     */
    public function getMonthlyCostBreakdown($clientId)
    {
        $sql = "SELECT cs.total_monthly, cs.exchange_rate AS contract_rate,
                    cur.code AS currency_code, cur.symbol AS currency_symbol
                FROM contracts c
                LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                WHERE c.client_id = ? AND c.status IN ('active', 'onboard')";

        $costs = $this->query($sql, [$clientId], 'i');

        $byCurrency = [];
        $byCurrencyUsd = [];
        $symbols = [];
        $totalUsd = 0;

        foreach ($costs as $cost) {
            $amount = $cost['total_monthly'] ?? 0;
            $contractRate = $cost['contract_rate'] ?? 0;
            $detected = $this->detectCurrency($cost['currency_code'], $amount, $cost['currency_symbol']);
            $currency = $detected['code'];
            $symbol = $detected['symbol'];

            if (!isset($byCurrency[$currency])) {
                $byCurrency[$currency] = 0;
                $byCurrencyUsd[$currency] = 0;
                $symbols[$currency] = $symbol;
            }
            $byCurrency[$currency] += $amount;

            // Convert to USD using proper conversion
            $amountUsd = $this->convertToUsd($amount, $currency, $contractRate);
            $totalUsd += $amountUsd;
            $byCurrencyUsd[$currency] += $amountUsd;
        }

        return [
            'by_currency' => $byCurrency,
            'by_currency_usd' => $byCurrencyUsd,
            'symbols' => $symbols,
            'total_usd' => round($totalUsd, 2),
        ];
    }

    /**
     * Get for dropdown
     */
    public function getForDropdown()
    {
        return $this->query("SELECT id, name, short_name FROM clients WHERE is_active = 1 ORDER BY name");
    }


    /**
     * Get dashboard stats
     */
    public function getDashboardStats()
    {
        $result = $this->query("SELECT COUNT(*) as count FROM clients WHERE is_active = 1");
        return $result[0]['count'] ?? 0;
    }

    /**
     * Get contract growth percentage for a client
     * Calculates percentage of contracts that were created/activated in last 30 days
     * Returns 0 if no active contracts
     */
    public function getContractGrowthPercentage($clientId)
    {
        // Get contracts created or activated in last 30 days
        $recentSql = "SELECT COUNT(*) as recent_count 
                      FROM contracts 
                      WHERE client_id = ? 
                      AND status IN ('active', 'onboard')
                      AND (created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                           OR sign_on_date >= DATE_SUB(NOW(), INTERVAL 30 DAY))";

        // Get total active contracts  
        $totalSql = "SELECT COUNT(*) as total_count 
                     FROM contracts 
                     WHERE client_id = ? 
                     AND status IN ('active', 'onboard')";

        $recentResult = $this->query($recentSql, [$clientId], 'i');
        $totalResult = $this->query($totalSql, [$clientId], 'i');

        $recent = $recentResult[0]['recent_count'] ?? 0;
        $total = $totalResult[0]['total_count'] ?? 0;

        if ($total == 0)
            return 0;

        return round(($recent / $total) * 100, 0);
    }

    /**
     * Get profit data per vessel for a specific client
     * Returns: array of vessels with revenue_usd, cost_usd, profit_usd, margin_percent, is_profitable
     */
    public function getVesselsProfitByClient($clientId)
    {
        // Get all vessels for this client
        $vessels = $this->getVessels($clientId);

        $profitData = [];

        foreach ($vessels as $vessel) {
            $vesselId = $vessel['id'];

            // Get revenue (client_rate) and cost (total_monthly) grouped by currency
            $sql = "SELECT 
                        SUM(cs.client_rate) as total_revenue,
                        SUM(cs.total_monthly) as total_cost,
                        cur.code AS currency_code,
                        cs.exchange_rate AS contract_rate
                    FROM contracts c
                    JOIN contract_salaries cs ON c.id = cs.contract_id
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE c.vessel_id = ? AND c.client_id = ? AND c.status IN ('active', 'onboard')
                    GROUP BY cur.code, cs.exchange_rate";

            $results = $this->query($sql, [$vesselId, $clientId], 'ii');

            $totalRevenueUSD = 0;
            $totalCostUSD = 0;

            foreach ($results as $row) {
                $revenue = $row['total_revenue'] ?? 0;
                $cost = $row['total_cost'] ?? 0;
                $contractRate = $row['contract_rate'] ?? 0;
                $detected = $this->detectCurrency($row['currency_code'], max($revenue, $cost));
                $currency = $detected['code'];

                $totalRevenueUSD += $this->convertToUsd($revenue, $currency, $contractRate);
                $totalCostUSD += $this->convertToUsd($cost, $currency, $contractRate);
            }

            $profit = $totalRevenueUSD - $totalCostUSD;
            $margin = $totalRevenueUSD > 0 ? ($profit / $totalRevenueUSD) * 100 : 0;

            $profitData[] = [
                'id' => $vessel['id'],
                'name' => $vessel['name'],
                'vessel_type' => $vessel['vessel_type_name'] ?? '-',
                'image_url' => $vessel['image_url'] ?? '',
                'crew_count' => $vessel['crew_count'] ?? 0,
                'status' => $vessel['status'] ?? 'active',
                'imo_number' => $vessel['imo_number'] ?? 'N/A',
                'revenue_usd' => round($totalRevenueUSD, 2),
                'cost_usd' => round($totalCostUSD, 2),
                'profit_usd' => round($profit, 2),
                'margin_percent' => round($margin, 1),
                'is_profitable' => $profit > 0,
            ];
        }

        // Sort by profit (highest first)
        usort($profitData, function ($a, $b) {
            return $b['profit_usd'] <=> $a['profit_usd'];
        });

        return $profitData;
    }

    /**
     * Get revenue growth percentage (current month vs previous month)
     * Revenue = SUM(client_rate) from active contracts
     */
    public function getRevenueGrowth()
    {
        // Get contracts that were active this month vs last month
        $currentSql = "SELECT cs.client_rate, cs.exchange_rate AS contract_rate, cur.code AS currency_code
                        FROM contracts c
                        JOIN contract_salaries cs ON c.id = cs.contract_id
                        LEFT JOIN currencies cur ON cs.currency_id = cur.id
                        WHERE c.status IN ('active', 'onboard')
                        AND cs.client_rate > 0";

        $currentContracts = $this->query($currentSql);

        $currentRevenue = 0;
        foreach ($currentContracts as $row) {
            $rate = $row['client_rate'] ?? 0;
            $contractRate = $row['contract_rate'] ?? 0;
            $detected = $this->detectCurrency($row['currency_code'], $rate);
            $currentRevenue += $this->convertToUsd($rate, $detected['code'], $contractRate);
        }

        // Previous month: contracts that were active last month
        $prevSql = "SELECT cs.client_rate, cs.exchange_rate AS contract_rate, cur.code AS currency_code
                    FROM contracts c
                    JOIN contract_salaries cs ON c.id = cs.contract_id
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE c.status IN ('active', 'onboard', 'completed', 'terminated')
                    AND cs.client_rate > 0
                    AND c.sign_on_date <= LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                    AND (c.sign_off_date IS NULL OR c.sign_off_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'))";

        $prevContracts = $this->query($prevSql);

        $prevRevenue = 0;
        foreach ($prevContracts as $row) {
            $rate = $row['client_rate'] ?? 0;
            $contractRate = $row['contract_rate'] ?? 0;
            $detected = $this->detectCurrency($row['currency_code'], $rate);
            $prevRevenue += $this->convertToUsd($rate, $detected['code'], $contractRate);
        }

        if ($prevRevenue <= 0) return 0;
        $growth = round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1);
        return max(-999, min(999, $growth));
    }

    /**
     * Get margin growth percentage (current margin vs previous month margin)
     */
    public function getMarginGrowth()
    {
        // Current month active contracts
        $currentSql = "SELECT cs.client_rate, cs.total_monthly, cs.exchange_rate AS contract_rate, cur.code AS currency_code
                        FROM contracts c
                        JOIN contract_salaries cs ON c.id = cs.contract_id
                        LEFT JOIN currencies cur ON cs.currency_id = cur.id
                        WHERE c.status IN ('active', 'onboard')
                    AND cs.client_rate > 0";

        $contracts = $this->query($currentSql);

        $currentRevenue = 0;
        $currentCost = 0;
        foreach ($contracts as $row) {
            $rate = $row['client_rate'] ?? 0;
            $cost = $row['total_monthly'] ?? 0;
            $contractRate = $row['contract_rate'] ?? 0;
            $detected = $this->detectCurrency($row['currency_code'], max($rate, $cost));
            $currency = $detected['code'];

            $currentRevenue += $this->convertToUsd($rate, $currency, $contractRate);
            $currentCost += $this->convertToUsd($cost, $currency, $contractRate);
        }

        $currentMargin = $currentRevenue > 0 ? (($currentRevenue - $currentCost) / $currentRevenue) * 100 : 0;

        // Previous month contracts
        $prevSql = "SELECT cs.client_rate, cs.total_monthly, cs.exchange_rate AS contract_rate, cur.code AS currency_code
                    FROM contracts c
                    JOIN contract_salaries cs ON c.id = cs.contract_id
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE c.status IN ('active', 'onboard', 'completed', 'terminated')
                    AND cs.client_rate > 0
                    AND c.sign_on_date <= LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                    AND (c.sign_off_date IS NULL OR c.sign_off_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'))";

        $prevContracts = $this->query($prevSql);

        $prevRevenue = 0;
        $prevCost = 0;
        foreach ($prevContracts as $row) {
            $rate = $row['client_rate'] ?? 0;
            $cost = $row['total_monthly'] ?? 0;
            $contractRate = $row['contract_rate'] ?? 0;
            $detected = $this->detectCurrency($row['currency_code'], max($rate, $cost));
            $currency = $detected['code'];

            $prevRevenue += $this->convertToUsd($rate, $currency, $contractRate);
            $prevCost += $this->convertToUsd($cost, $currency, $contractRate);
        }

        $prevMargin = $prevRevenue > 0 ? (($prevRevenue - $prevCost) / $prevRevenue) * 100 : 0;

        $growth = round($currentMargin - $prevMargin, 1);
        return max(-999, min(999, $growth));
    }

    /**
     * Get active contract count across all clients
     */
    public function getActiveContractCount()
    {
        $result = $this->query("SELECT COUNT(*) as cnt FROM contracts WHERE status IN ('active', 'onboard')");
        return $result[0]['cnt'] ?? 0;
    }

    /**
     * Get active contract growth (this month new contracts vs total)
     */
    public function getActiveContractGrowth()
    {
        $totalResult = $this->query("SELECT COUNT(*) as cnt FROM contracts WHERE status IN ('active', 'onboard')");
        $total = $totalResult[0]['cnt'] ?? 0;

        if ($total === 0) return 0;

        $recentResult = $this->query(
            "SELECT COUNT(*) as cnt FROM contracts 
             WHERE status IN ('active', 'onboard') 
             AND (created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) OR sign_on_date >= DATE_SUB(NOW(), INTERVAL 30 DAY))"
        );
        $recent = $recentResult[0]['cnt'] ?? 0;

        return round(($recent / $total) * 100, 0);
    }

    /**
     * Get monthly revenue data for the last 6 months (for analytics chart)
     * Returns array of [month => revenue_usd]
     */
    public function getMonthlyRevenueTrend($months = 6)
    {
        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-{$i} months"));
            $monthEnd = date('Y-m-t', strtotime("-{$i} months"));
            $monthLabel = date('M', strtotime("-{$i} months"));

            // Contracts that were active during this month
            $sql = "SELECT cs.client_rate, cs.total_monthly, cs.exchange_rate AS contract_rate, cur.code AS currency_code
                    FROM contracts c
                    JOIN contract_salaries cs ON c.id = cs.contract_id
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE c.sign_on_date <= ?
                    AND (c.sign_off_date IS NULL OR c.sign_off_date >= ?)
                    AND c.status NOT IN ('draft', 'cancelled', 'rejected')
                    AND cs.client_rate > 0";

            $contracts = $this->query($sql, [$monthEnd, $monthStart], 'ss');

            $revenueUsd = 0;
            $profitUsd = 0;
            foreach ($contracts as $row) {
                $rate = $row['client_rate'] ?? 0;
                $cost = $row['total_monthly'] ?? 0;
                $contractRate = $row['contract_rate'] ?? 0;
                $detected = $this->detectCurrency($row['currency_code'], max($rate, $cost));
                $currency = $detected['code'];

                $revenueUsd += $this->convertToUsd($rate, $currency, $contractRate);
                $profitUsd += $this->convertToUsd($rate - $cost, $currency, $contractRate);
            }

            $trend[] = [
                'month' => $monthLabel,
                'revenue' => round($revenueUsd, 2),
                'profit' => round($profitUsd, 2),
            ];
        }

        return $trend;
    }
}
