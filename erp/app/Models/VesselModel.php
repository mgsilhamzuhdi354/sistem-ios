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
        'name', 'imo_number', 'vessel_type_id', 'flag_state_id', 'client_id',
        'gross_tonnage', 'dwt', 'year_built', 'call_sign', 'mmsi',
        'engine_type', 'crew_capacity', 'status', 'notes', 'is_active'
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
                    fs.name AS flag_state_name, fs.emoji AS flag_emoji,
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
     * Get contracts count by vessel for chart
     */
    public function getVesselContractsStats($limit = 4)
    {
        $sql = "SELECT v.name, COUNT(c.id) as count
                FROM vessels v
                LEFT JOIN contracts c ON v.id = c.vessel_id AND c.status IN ('active', 'onboard')
                WHERE v.is_active = 1
                GROUP BY v.id, v.name
                HAVING count > 0
                ORDER BY count DESC
                LIMIT ?";
        
        $topVessels = $this->query($sql, [$limit - 1], 'i');
        
        // Get "Others" count
        if (count($topVessels) >= $limit - 1) {
            $topIds = array_column($topVessels, 'name');
            $placeholders = str_repeat('?,', count($topIds) - 1) . '?';
            
            if (!empty($topIds)) {
                $sqlOthers = "SELECT COUNT(c.id) as count
                              FROM contracts c
                              JOIN vessels v ON c.vessel_id = v.id
                              WHERE c.status IN ('active', 'onboard') 
                              AND v.name NOT IN ($placeholders)";
                $othersResult = $this->query($sqlOthers, $topIds, str_repeat('s', count($topIds)));
                $othersCount = $othersResult[0]['count'] ?? 0;
                
                if ($othersCount > 0) {
                    $topVessels[] = ['name' => 'Others', 'count' => $othersCount];
                }
            }
        }
        
        return $topVessels;
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
 * Rank Model
 */
class RankModel extends BaseModel
{
    protected $table = 'ranks';
    protected $allowedFields = ['name', 'department', 'level', 'is_officer', 'is_active'];
    
    public function getForDropdown()
    {
        return $this->query("SELECT id, name, department FROM ranks WHERE is_active = 1 ORDER BY department, level");
    }
    
    public function getByDepartment($department)
    {
        return $this->findAll(['department' => $department, 'is_active' => 1], 'level ASC');
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
