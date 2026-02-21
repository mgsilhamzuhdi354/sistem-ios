<?php
/**
 * PT Indo Ocean - ERP System
 * Contract Model
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class ContractModel extends BaseModel
{
    protected $table = 'contracts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'contract_no', 'crew_id', 'crew_name', 'vessel_id', 'client_id', 'rank_id',
        'contract_type', 'status', 'sign_on_date', 'sign_off_date', 'duration_months',
        'actual_sign_off_date', 'embarkation_port', 'disembarkation_port',
        'is_renewal', 'previous_contract_id', 'notes', 'termination_reason',
        'created_by', 'updated_by'
    ];
    
    /**
     * Get contract with all related data
     */
    public function getWithDetails($id)
    {
        $sql = "SELECT c.*, 
                    v.name AS vessel_name, v.imo_number,
                    cl.name AS client_name,
                    r.name AS rank_name, r.department,
                    cs.basic_salary, cs.overtime_allowance, cs.leave_pay, cs.bonus, 
                    cs.other_allowance, cs.total_monthly, cs.exchange_rate, cs.client_rate,
                    cs.currency_id,
                    cur.code AS currency_code, cur.symbol AS currency_symbol,
                    ct.tax_type, ct.npwp_number, ct.tax_rate
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN clients cl ON c.client_id = cl.id
                LEFT JOIN ranks r ON c.rank_id = r.id
                LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                LEFT JOIN contract_taxes ct ON c.id = ct.contract_id
                WHERE c.id = ?";
        
        $result = $this->query($sql, [$id], 'i');
        return $result[0] ?? null;
    }
    
    /**
     * Get all contracts with pagination and filters
     */
    public function getList($filters = [], $page = 1, $perPage = 20)
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['status'])) {
            $where[] = 'c.status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }
        
        if (!empty($filters['vessel_id'])) {
            $where[] = 'c.vessel_id = ?';
            $params[] = $filters['vessel_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['client_id'])) {
            $where[] = 'c.client_id = ?';
            $params[] = $filters['client_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(c.contract_no LIKE ? OR c.crew_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }
        
        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT c.*, 
                    v.name AS vessel_name,
                    cl.name AS client_name,
                    r.name AS rank_name,
                    cr.source AS crew_source,
                    DATEDIFF(c.sign_off_date, CURDATE()) AS days_remaining
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN clients cl ON c.client_id = cl.id
                LEFT JOIN ranks r ON c.rank_id = r.id
                LEFT JOIN crews cr ON c.crew_id = cr.id
                WHERE $whereClause
                ORDER BY c.created_at DESC
                LIMIT $perPage OFFSET $offset";
        
        return $this->query($sql, $params, $types);
    }
    
    /**
     * Count contracts with filters
     */
    public function countList($filters = [])
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }
        
        if (!empty($filters['vessel_id'])) {
            $where[] = 'vessel_id = ?';
            $params[] = $filters['vessel_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['client_id'])) {
            $where[] = 'client_id = ?';
            $params[] = $filters['client_id'];
            $types .= 'i';
        }
        
        $whereClause = implode(' AND ', $where);
        $result = $this->query("SELECT COUNT(*) as count FROM contracts WHERE $whereClause", $params, $types);
        return $result[0]['count'] ?? 0;
    }
    
    /**
     * Get active contracts
     */
    public function getActive()
    {
        return $this->findAll(['status' => ['active', 'onboard']], 'sign_off_date ASC');
    }
    
    /**
     * Get expiring contracts (within days)
     */
    public function getExpiring($days = 60)
    {
        $sql = "SELECT c.*, 
                    v.name AS vessel_name,
                    cl.name AS client_name,
                    r.name AS rank_name,
                    DATEDIFF(c.sign_off_date, CURDATE()) AS days_remaining
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN clients cl ON c.client_id = cl.id
                LEFT JOIN ranks r ON c.rank_id = r.id
                WHERE c.status IN ('active', 'onboard')
                AND c.sign_off_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY c.sign_off_date ASC";
        
        return $this->query($sql, [$days], 'i');
    }
    
    /**
     * Generate next contract number
     */
    public function generateContractNumber()
    {
        $year = date('Y');
        $prefix = 'CTR';
        
        $sql = "SELECT contract_no FROM contracts 
                WHERE contract_no LIKE ? 
                ORDER BY id DESC LIMIT 1";
        
        $result = $this->query($sql, ["$prefix-$year-%"], 's');
        
        if (!empty($result)) {
            $lastNo = $result[0]['contract_no'];
            $parts = explode('-', $lastNo);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }
        
        return sprintf('%s-%s-%04d', $prefix, $year, $seq);
    }
    
    /**
     * Get dashboard stats
     */
    public function getDashboardStats()
    {
        $stats = [];
        
        // Active contracts
        $result = $this->query("SELECT COUNT(*) as count FROM contracts WHERE status IN ('active', 'onboard')");
        $stats['active'] = $result[0]['count'] ?? 0;
        
        // Total crew (unique crew in active contracts)
        $result = $this->query("SELECT COUNT(DISTINCT crew_id) as count FROM contracts WHERE status IN ('active', 'onboard')");
        $stats['total_crew'] = $result[0]['count'] ?? 0;
        
        // Expiring in 7 days
        $result = $this->query("SELECT COUNT(*) as count FROM contracts 
            WHERE status IN ('active', 'onboard') 
            AND sign_off_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
        $stats['expiring_7'] = $result[0]['count'] ?? 0;
        
        // Expiring in 30 days
        $result = $this->query("SELECT COUNT(*) as count FROM contracts 
            WHERE status IN ('active', 'onboard') 
            AND sign_off_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
        $stats['expiring_30'] = $result[0]['count'] ?? 0;
        
        // Expiring in 60 days
        $result = $this->query("SELECT COUNT(*) as count FROM contracts 
            WHERE status IN ('active', 'onboard') 
            AND sign_off_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)");
        $stats['expiring_60'] = $result[0]['count'] ?? 0;
        
        // Draft contracts
        $result = $this->query("SELECT COUNT(*) as count FROM contracts WHERE status = 'draft'");
        $stats['draft'] = $result[0]['count'] ?? 0;
        
        // Pending approval
        $result = $this->query("SELECT COUNT(*) as count FROM contracts WHERE status = 'pending_approval'");
        $stats['pending'] = $result[0]['count'] ?? 0;
        
        // Monthly contracts data (last 6 months)
        $stats['monthly'] = $this->getMonthlyContractsStats();
        
        return $stats;
    }
    
    /**
     * Get monthly contracts statistics for chart
     */
    public function getMonthlyContractsStats()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $monthName = date('M', strtotime("-$i months"));
            
            $result = $this->query(
                "SELECT COUNT(*) as count FROM contracts WHERE DATE_FORMAT(created_at, '%Y-%m') = ?",
                [$date],
                's'
            );
            
            $months[] = [
                'month' => $monthName,
                'count' => $result[0]['count'] ?? 0
            ];
        }
        return $months;
    }
    
    /**
     * Get contracts by vessel
     */
    public function getByVessel($vesselId)
    {
        $sql = "SELECT c.*, r.name AS rank_name,
                    DATEDIFF(c.sign_off_date, CURDATE()) AS days_remaining
                FROM contracts c
                LEFT JOIN ranks r ON c.rank_id = r.id
                WHERE c.vessel_id = ? AND c.status IN ('active', 'onboard')
                ORDER BY r.level ASC";
        
        return $this->query($sql, [$vesselId], 'i');
    }
    
    /**
     * Get contracts by client
     */
    public function getByClient($clientId)
    {
        $sql = "SELECT c.*, v.name AS vessel_name, r.name AS rank_name
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN ranks r ON c.rank_id = r.id
                WHERE c.client_id = ? AND c.status IN ('active', 'onboard')
                ORDER BY v.name, r.level";
        
        return $this->query($sql, [$clientId], 'i');
    }
    
    /**
     * Create contract with salary and tax
     */
    public function createWithDetails($contractData, $salaryData, $taxData)
    {
        $this->db->begin_transaction();
        
        try {
            // Insert contract
            $contractId = $this->insert($contractData);
            if (!$contractId) {
                throw new \Exception('Failed to create contract');
            }
            
            // Insert salary
            $salaryData['contract_id'] = $contractId;
            $salaryModel = new ContractSalaryModel($this->db);
            $salaryModel->insert($salaryData);
            
            // Insert tax
            $taxData['contract_id'] = $contractId;
            $taxModel = new ContractTaxModel($this->db);
            $taxModel->insert($taxData);
            
            $this->db->commit();
            return $contractId;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}

// Salary Model
class ContractSalaryModel extends BaseModel
{
    protected $table = 'contract_salaries';
    protected $allowedFields = [
        'contract_id', 'currency_id', 'exchange_rate', 'client_rate', 'basic_salary', 'overtime_allowance',
        'leave_pay', 'bonus', 'other_allowance', 'notes'
    ];
    
    public function getByContract($contractId)
    {
        $result = $this->findAll(['contract_id' => $contractId]);
        return $result[0] ?? null;
    }
}

// Tax Model
class ContractTaxModel extends BaseModel
{
    protected $table = 'contract_taxes';
    protected $allowedFields = [
        'contract_id', 'tax_type', 'npwp_number', 'tax_rate',
        'monthly_tax_amount', 'effective_from', 'notes'
    ];
    
    public function getByContract($contractId)
    {
        $result = $this->findAll(['contract_id' => $contractId]);
        return $result[0] ?? null;
    }
}

// Deduction Model
class ContractDeductionModel extends BaseModel
{
    protected $table = 'contract_deductions';
    protected $allowedFields = [
        'contract_id', 'deduction_type', 'description', 'amount',
        'currency_id', 'is_recurring', 'recurring_months', 'deducted_count',
        'start_date', 'end_date', 'is_active'
    ];
    
    public function getByContract($contractId)
    {
        return $this->findAll(['contract_id' => $contractId], 'created_at ASC');
    }
    
    public function getActiveByContract($contractId)
    {
        return $this->findAll(['contract_id' => $contractId, 'is_active' => 1]);
    }
}

// Approval Model
class ContractApprovalModel extends BaseModel
{
    protected $table = 'contract_approvals';
    protected $allowedFields = [
        'contract_id', 'approval_level', 'status', 'approver_id',
        'approver_name', 'approved_at', 'notes', 'rejection_reason'
    ];
    
    public function getByContract($contractId)
    {
        return $this->findAll(['contract_id' => $contractId], 'id ASC');
    }
    
    public function getPending($contractId)
    {
        $result = $this->findAll(['contract_id' => $contractId, 'status' => 'pending']);
        return $result[0] ?? null;
    }
}

// Log Model
class ContractLogModel extends BaseModel
{
    protected $table = 'contract_logs';
    protected $allowedFields = [
        'contract_id', 'action', 'field_changed', 'old_value',
        'new_value', 'user_id', 'user_name', 'ip_address'
    ];
    
    public function getByContract($contractId, $limit = 50)
    {
        $sql = "SELECT * FROM contract_logs WHERE contract_id = ? ORDER BY created_at DESC LIMIT $limit";
        return $this->query($sql, [$contractId], 'i');
    }
    
    public function log($contractId, $action, $changes = [], $userId = null, $userName = null)
    {
        $data = [
            'contract_id' => $contractId,
            'action' => $action,
            'user_id' => $userId,
            'user_name' => $userName,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ];
        
        if (!empty($changes)) {
            $data['field_changed'] = $changes['field'] ?? null;
            $data['old_value'] = $changes['old'] ?? null;
            $data['new_value'] = $changes['new'] ?? null;
        }
        
        return $this->insert($data);
    }
}
