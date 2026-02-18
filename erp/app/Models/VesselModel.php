<?php
/**
 * PT Indo Ocean - ERP System
 * Vessel Model
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class VesselModel extends BaseModel
{
    protected $table = 'vessels';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'imo_number',
        'vessel_type_id',
        'flag_state_id',
        'client_id',
        'gross_tonnage',
        'dwt',
        'year_built',
        'call_sign',
        'mmsi',
        'engine_type',
        'crew_capacity',
        'status',
        'notes',
        'image_url',  // Photo upload URL
        'is_active'
    ];

    /**
     * Get vessel with details
     */
    public function getWithDetails($id)
    {
        $sql = "SELECT v.*, 
                    vt.name AS vessel_type_name,
                    fs.name AS flag_state_name, fs.emoji AS flag_emoji,
                    c.name AS owner_name
                FROM vessels v
                LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
                LEFT JOIN flag_states fs ON v.flag_state_id = fs.id
                LEFT JOIN clients c ON v.client_id = c.id
                WHERE v.id = ?";

        $result = $this->query($sql, [$id], 'i');
        return $result[0] ?? null;
    }

    /**
     * Get all vessels with details
     */
    public function getAllWithDetails($onlyActive = true)
    {
        $where = $onlyActive ? 'WHERE v.is_active = 1' : '';

        $sql = "SELECT v.*, 
                    vt.name AS vessel_type_name,
                    vt.name AS type,
                    fs.name AS flag_state_name, 
                    fs.name AS flag_state,
                    fs.emoji AS flag_emoji,
                    c.name AS owner_name,
                    (SELECT COUNT(*) FROM contracts ct 
                     WHERE ct.vessel_id = v.id AND ct.status IN ('active', 'onboard')) AS active_crew_count
                FROM vessels v
                LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
                LEFT JOIN flag_states fs ON v.flag_state_id = fs.id
                LEFT JOIN clients c ON v.client_id = c.id
                $where
                ORDER BY v.name ASC";

        return $this->query($sql);
    }

    /**
     * Get vessel crew list
     */
    public function getCrewList($vesselId)
    {
        $sql = "SELECT c.*, r.name AS rank_name, r.department,
                    cs.total_monthly, cur.code AS currency_code,
                    DATEDIFF(c.sign_off_date, CURDATE()) AS days_remaining
                FROM contracts c
                LEFT JOIN ranks r ON c.rank_id = r.id
                LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                WHERE c.vessel_id = ? AND c.status IN ('active', 'onboard')
                ORDER BY r.department, r.level";

        return $this->query($sql, [$vesselId], 'i');
    }

    /**
     * Get vessel total monthly cost (with currency breakdown)
     * Returns: [
     *   'by_currency' => ['IDR' => 40000000, 'USD' => 2000],
     *   'total_usd' => 2527.00,
     *   'total_original' => 40000000 (largest currency amount for display)
     * ]
     */
    public function getTotalMonthlyCost($vesselId)
    {
        // Get all crew costs with their currencies and exchange rates
        $sql = "SELECT cs.total_monthly, cs.exchange_rate AS contract_rate,
                    cur.code AS currency_code, cur.symbol AS currency_symbol
                FROM contracts c
                LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                WHERE c.vessel_id = ? AND c.status IN ('active', 'onboard')";

        $results = $this->query($sql, [$vesselId], 'i');

        $byCurrency = [];
        $totalUSD = 0;
        $currencySymbols = [];

        // Default exchange rates to USD
        $defaultRates = [
            'USD' => 1.0,
            'IDR' => 0.000063,  // 1 IDR = 0.000063 USD (1 USD = ~15900 IDR)
            'SGD' => 0.74,
            'EUR' => 1.05,
        ];

        foreach ($results as $row) {
            $amount = $row['total_monthly'] ?? 0;
            $contractRate = $row['contract_rate'] ?? 0;

            // Auto-detect currency: if NULL or USD but amount > 1M, assume IDR
            $currency = $row['currency_code'] ?? null;
            if (!$currency || ($currency === 'USD' && $amount > 1000000)) {
                $currency = 'IDR';
            }

            // Sum by currency
            if (!isset($byCurrency[$currency])) {
                $byCurrency[$currency] = 0;
                $currencySymbols[$currency] = $row['currency_symbol'] ?? $currency;
            }
            $byCurrency[$currency] += $amount;

            // Convert to USD
            if ($currency === 'USD') {
                $totalUSD += $amount;
            } elseif ($contractRate > 0) {
                // Use contract's custom exchange rate (1 USD = X currency)
                $totalUSD += $amount / $contractRate;
            } else {
                // Use default rate
                $totalUSD += $amount * ($defaultRates[$currency] ?? 0.000063);
            }
        }

        return [
            'by_currency' => $byCurrency,
            'symbols' => $currencySymbols,
            'total_usd' => round($totalUSD, 2),
        ];
    }

    /**
     * Get for dropdown
     */
    public function getForDropdown()
    {
        return $this->query("SELECT id, name FROM vessels WHERE is_active = 1 ORDER BY name");
    }

    /**
     * Get dashboard stats
     */
    public function getDashboardStats()
    {
        $stats = [];

        // Total vessels
        $result = $this->query("SELECT COUNT(*) as count FROM vessels WHERE is_active = 1");
        $stats['total'] = $result[0]['count'] ?? 0;

        // Active vessels
        $result = $this->query("SELECT COUNT(*) as count FROM vessels WHERE is_active = 1 AND status = 'active'");
        $stats['active'] = $result[0]['count'] ?? 0;

        // In maintenance
        $result = $this->query("SELECT COUNT(*) as count FROM vessels WHERE is_active = 1 AND status = 'maintenance'");
        $stats['maintenance'] = $result[0]['count'] ?? 0;

        // Contracts by vessel (for pie chart)
        $stats['vessel_contracts'] = $this->getVesselContractsStats();

        return $stats;
    }

    /**
     * Get contracts count by vessel for chart (ALL contracts, real-time data)
     */
    public function getVesselContractsStats($limit = 5)
    {
        // Get all contracts for all vessels (not just active/onboard)
        $sql = "SELECT v.name, COUNT(c.id) as count
                FROM vessels v
                LEFT JOIN contracts c ON v.id = c.vessel_id
                WHERE v.is_active = 1
                GROUP BY v.id, v.name
                HAVING count > 0
                ORDER BY count DESC
                LIMIT ?";

        $topVessels = $this->query($sql, [$limit], 'i');

        return $topVessels;
    }

    /**
     * Get vessel profit (Revenue from Client Rate - Cost from Crew Salary)
     */
    public function getVesselProfit($vesselId)
    {
        // Get total client rate (revenue) for this vessel
        $revenueSql = "SELECT 
                        SUM(cs.client_rate) as total_revenue,
                        SUM(cs.total_monthly) as total_cost,
                        cur.code AS currency_code,
                        cs.exchange_rate AS contract_rate
                    FROM contracts c
                    JOIN contract_salaries cs ON c.id = cs.contract_id
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE c.vessel_id = ? AND c.status IN ('active', 'onboard')
                    GROUP BY cur.code, cs.exchange_rate";

        $results = $this->query($revenueSql, [$vesselId], 'i');

        $totalRevenueUSD = 0;
        $totalCostUSD = 0;

        // Default exchange rates to USD
        $defaultRates = [
            'USD' => 1.0,
            'IDR' => 0.000063,
            'SGD' => 0.74,
            'EUR' => 1.05,
        ];

        foreach ($results as $row) {
            $revenue = $row['total_revenue'] ?? 0;
            $cost = $row['total_cost'] ?? 0;
            $contractRate = $row['contract_rate'] ?? 0;
            $currency = $row['currency_code'] ?? 'IDR';

            // Auto-detect currency
            if (!$currency || ($currency === 'USD' && $revenue > 1000000)) {
                $currency = 'IDR';
            }

            // Convert to USD
            if ($currency === 'USD') {
                $totalRevenueUSD += $revenue;
                $totalCostUSD += $cost;
            } elseif ($contractRate > 0) {
                $totalRevenueUSD += $revenue / $contractRate;
                $totalCostUSD += $cost / $contractRate;
            } else {
                $rate = $defaultRates[$currency] ?? 0.000063;
                $totalRevenueUSD += $revenue * $rate;
                $totalCostUSD += $cost * $rate;
            }
        }

        $profit = $totalRevenueUSD - $totalCostUSD;
        $margin = $totalRevenueUSD > 0 ? ($profit / $totalRevenueUSD) * 100 : 0;

        return [
            'revenue_usd' => round($totalRevenueUSD, 2),
            'cost_usd' => round($totalCostUSD, 2),
            'profit_usd' => round($profit, 2),
            'margin_percent' => round($margin, 1),
            'is_profitable' => $profit > 0
        ];
    }

    /**
     * Get all vessels profit summary (for Profit per Vessel report)
     */
    public function getAllVesselsProfit()
    {
        $vessels = $this->getAllWithDetails();
        $profitData = [];

        foreach ($vessels as $vessel) {
            $profit = $this->getVesselProfit($vessel['id']);
            $cost = $this->getTotalMonthlyCost($vessel['id']);

            $profitData[] = [
                'id' => $vessel['id'],
                'name' => $vessel['name'],
                'vessel_type' => $vessel['vessel_type_name'] ?? '-',
                'client' => $vessel['owner_name'] ?? '-',
                'crew_count' => $vessel['active_crew_count'] ?? 0,
                'revenue_usd' => $profit['revenue_usd'],
                'cost_usd' => $profit['cost_usd'],
                'profit_usd' => $profit['profit_usd'],
                'margin_percent' => $profit['margin_percent'],
                'is_profitable' => $profit['is_profitable'],
            ];
        }

        // Sort by profit (highest first)
        usort($profitData, function ($a, $b) {
            return $b['profit_usd'] <=> $a['profit_usd'];
        });

        return $profitData;
    }
}


/**
 * Vessel Type Model
 */
class VesselTypeModel extends BaseModel
{
    protected $table = 'vessel_types';
    protected $allowedFields = ['name', 'description', 'is_active'];

    public function getForDropdown()
    {
        return $this->query("SELECT id, name FROM vessel_types WHERE is_active = 1 ORDER BY name");
    }
}

/**
 * Flag State Model
 */
class FlagStateModel extends BaseModel
{
    protected $table = 'flag_states';
    protected $allowedFields = ['code', 'name', 'emoji', 'is_active'];

    public function getForDropdown()
    {
        return $this->query("SELECT id, name, emoji FROM flag_states WHERE is_active = 1 ORDER BY name");
    }
}



/**
 * Currency Model
 */
class CurrencyModel extends BaseModel
{
    protected $table = 'currencies';
    protected $allowedFields = ['code', 'name', 'symbol', 'is_active'];

    public function getForDropdown()
    {
        return $this->query("SELECT id, code, name, symbol FROM currencies WHERE is_active = 1 ORDER BY code");
    }

    public function getByCode($code)
    {
        $result = $this->findAll(['code' => $code]);
        return $result[0] ?? null;
    }
}
