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
    protected $allowedFields = [
        'name', 'short_name', 'country', 'address', 'city', 'postal_code',
        'email', 'phone', 'website', 'contact_person', 'contact_email',
        'contact_phone', 'notes', 'is_active'
    ];
    
    /**
     * Get client with stats
     */
    public function getWithStats($id)
    {
        $client = $this->find($id);
        if (!$client) return null;
        
        // Get vessel count
        $result = $this->query(
            "SELECT COUNT(*) as count FROM vessels WHERE client_id = ? AND is_active = 1",
            [$id], 'i'
        );
        $client['vessel_count'] = $result[0]['count'] ?? 0;
        
        // Get active crew count
        $result = $this->query(
            "SELECT COUNT(*) as count FROM contracts WHERE client_id = ? AND status IN ('active', 'onboard')",
            [$id], 'i'
        );
        $client['active_crew_count'] = $result[0]['count'] ?? 0;
        
        // Get monthly cost
        $result = $this->query(
            "SELECT SUM(cs.total_monthly) as total 
             FROM contracts c
             LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
             WHERE c.client_id = ? AND c.status IN ('active', 'onboard')",
            [$id], 'i'
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
                    (SELECT COUNT(*) FROM vessels v WHERE v.client_id = c.id AND v.is_active = 1) AS vessel_count,
                    (SELECT COUNT(*) FROM contracts ct WHERE ct.client_id = c.id AND ct.status IN ('active', 'onboard')) AS active_crew_count
                FROM clients c
                WHERE c.is_active = 1
                ORDER BY c.name";
        
        $clients = $this->query($sql);
        
        // Default exchange rates
        $defaultRates = [
            'USD' => 1.0,
            'IDR' => 0.000063,
            'SGD' => 0.74,
            'EUR' => 1.05,
        ];
        
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
                $currency = $cost['currency_code'] ?? 'USD';
                $amount = $cost['total_monthly'] ?? 0;
                $contractRate = $cost['contract_rate'] ?? 0;
                
                // Sum by currency
                if (!isset($byCurrency[$currency])) {
                    $byCurrency[$currency] = 0;
                    $symbols[$currency] = $cost['currency_symbol'] ?? $currency;
                }
                $byCurrency[$currency] += $amount;
                
                // Convert to USD
                if ($currency === 'USD') {
                    $totalUsd += $amount;
                } elseif ($contractRate > 0) {
                    $totalUsd += $amount / $contractRate;
                } else {
                    $totalUsd += $amount * ($defaultRates[$currency] ?? 0.000063);
                }
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
                    (SELECT COUNT(*) FROM contracts ct 
                     WHERE ct.vessel_id = v.id AND ct.status IN ('active', 'onboard')) AS crew_count
                FROM vessels v
                LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
                LEFT JOIN flag_states fs ON v.flag_state_id = fs.id
                WHERE v.client_id = ? AND v.is_active = 1
                ORDER BY v.name";
        
        return $this->query($sql, [$clientId], 'i');
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
        $defaultRates = [
            'USD' => 1.0,
            'IDR' => 0.000063,
            'SGD' => 0.74,
            'EUR' => 1.05,
        ];
        
        foreach ($contracts as &$c) {
            $amount = $c['total_monthly'] ?? 0;
            $clientRateRaw = $c['client_rate'] ?? 0;
            
            // Auto-detect currency: override if amounts look like IDR (> 1,000,000)
            $currency = $c['currency_code'] ?? null;
            $symbol = $c['currency_symbol'] ?? null;
            
            // If currency is NULL or USD but amount is very large, it's likely IDR
            if (!$currency || ($currency === 'USD' && ($amount > 1000000 || $clientRateRaw > 1000000))) {
                $currency = 'IDR';
                $symbol = 'Rp';
            }
            
            // Update the contract data with corrected currency
            $c['currency_code'] = $currency;
            $c['currency_symbol'] = $symbol ?? ($currency === 'IDR' ? 'Rp' : '$');
            
            $contractRate = $c['contract_rate'] ?? 0;
            $clientRate = $clientRateRaw;
            
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
            
            // Calculate USD salary
            if ($currency === 'USD') {
                $c['salary_usd'] = $amount;
            } elseif ($contractRate > 0) {
                $c['salary_usd'] = $amount / $contractRate;
            } else {
                $c['salary_usd'] = $amount * ($defaultRates[$currency] ?? 0.000063);
            }
            
            // Calculate monthly profit (in original currency)
            $c['profit'] = $clientRate > 0 ? ($clientRate - $amount) : 0;
            
            // Calculate monthly profit in USD
            if ($clientRate > 0) {
                if ($currency === 'USD') {
                    $c['profit_usd'] = $c['profit'];
                    $c['client_rate_usd'] = $clientRate;
                } elseif ($contractRate > 0) {
                    $c['profit_usd'] = $c['profit'] / $contractRate;
                    $c['client_rate_usd'] = $clientRate / $contractRate;
                } else {
                    $c['profit_usd'] = $c['profit'] * ($defaultRates[$currency] ?? 0.000063);
                    $c['client_rate_usd'] = $clientRate * ($defaultRates[$currency] ?? 0.000063);
                }
            } else {
                $c['profit_usd'] = 0;
                $c['client_rate_usd'] = 0;
            }
            
            // Calculate total accumulated profit
            $c['total_profit'] = $c['profit'] * $c['months_active'];
            $c['total_profit_usd'] = $c['profit_usd'] * $c['months_active'];
            $c['total_salary'] = $amount * $c['months_active'];
            $c['total_client_rate'] = $clientRate * $c['months_active'];
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
        
        $defaultRates = [
            'USD' => 1.0,
            'IDR' => 0.000063,
            'SGD' => 0.74,
            'EUR' => 1.05,
        ];
        
        $byCurrency = [];
        $symbols = [];
        $totalUsd = 0;
        
        foreach ($costs as $cost) {
            $currency = $cost['currency_code'] ?? 'USD';
            $amount = $cost['total_monthly'] ?? 0;
            $contractRate = $cost['contract_rate'] ?? 0;
            
            if (!isset($byCurrency[$currency])) {
                $byCurrency[$currency] = 0;
                $symbols[$currency] = $cost['currency_symbol'] ?? $currency;
            }
            $byCurrency[$currency] += $amount;
            
            if ($currency === 'USD') {
                $totalUsd += $amount;
            } elseif ($contractRate > 0) {
                $totalUsd += $amount / $contractRate;
            } else {
                $totalUsd += $amount * ($defaultRates[$currency] ?? 0.000063);
            }
        }
        
        return [
            'by_currency' => $byCurrency,
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
}
