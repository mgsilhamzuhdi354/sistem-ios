<?php
/**
 * PT Indo Ocean - ERP System
 * Crew Model - Data lengkap kru
 */

namespace App\Models;

require_once APPPATH . 'Models/BaseModel.php';

class CrewModel extends BaseModel
{
    protected $table = 'crews';
    protected $allowedFields = [
        'employee_id',
        'full_name',
        'nickname',
        'gender',
        'birth_date',
        'birth_place',
        'nationality',
        'religion',
        'marital_status',
        'email',
        'phone',
        'whatsapp',
        'address',
        'city',
        'province',
        'postal_code',
        'emergency_name',
        'emergency_relation',
        'emergency_phone',
        'bank_name',
        'bank_account',
        'bank_holder',
        'current_rank_id',
        'years_experience',
        'total_sea_time_months',
        'photo',
        'status',
        'notes',
        'created_by'
    ];

    /**
     * Get all crews with filters
     */
    public function getList($filters = [], $page = 1, $perPage = 20)
    {
        $where = ['1=1'];
        $params = [];
        $types = '';

        if (!empty($filters['search'])) {
            $where[] = '(c.full_name LIKE ? OR c.employee_id LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
            $types .= 'ssss';
        }

        if (!empty($filters['status'])) {
            $where[] = 'c.status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['rank_id'])) {
            $where[] = 'c.current_rank_id = ?';
            $params[] = $filters['rank_id'];
            $types .= 'i';
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT c.*, r.name as rank_name 
                FROM {$this->table} c
                LEFT JOIN ranks r ON c.current_rank_id = r.id
                WHERE {$whereClause} 
                ORDER BY c.full_name ASC 
                LIMIT {$perPage} OFFSET {$offset}";

        return $this->query($sql, $params, $types);
    }

    /**
     * Count crews
     */
    public function countCrews($filters = [])
    {
        $where = ['1=1'];
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";

        $result = $this->query($sql, $params, $types);
        return $result[0]['total'] ?? 0;
    }

    /**
     * Get crew with all details
     */
    public function getWithDetails($id)
    {
        $sql = "SELECT c.*, r.name as rank_name 
                FROM {$this->table} c
                LEFT JOIN ranks r ON c.current_rank_id = r.id
                WHERE c.id = ?";
        $result = $this->query($sql, [$id], 'i');
        return $result[0] ?? null;
    }

    /**
     * Generate employee ID
     */
    public function generateEmployeeId()
    {
        $year = date('Y');
        $sql = "SELECT MAX(CAST(SUBSTRING(employee_id, 6) AS UNSIGNED)) as max_num 
                FROM {$this->table} 
                WHERE employee_id LIKE ?";
        $result = $this->query($sql, ["IO{$year}%"], 's');
        $maxNum = ($result[0]['max_num'] ?? 0) + 1;
        return "IO{$year}" . str_pad($maxNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get crew's contract history
     */
    public function getContractHistory($crewId)
    {
        $sql = "SELECT c.*, v.name as vessel_name, cl.name as client_name
                FROM contracts c
                LEFT JOIN vessels v ON c.vessel_id = v.id
                LEFT JOIN clients cl ON c.client_id = cl.id
                WHERE c.crew_id = ?
                ORDER BY c.sign_on_date DESC";
        return $this->query($sql, [$crewId], 'i');
    }

    /**
     * Get available crews (not on contract)
     */
    public function getAvailable()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'available' ORDER BY full_name";
        return $this->query($sql);
    }

    /**
     * Update status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get crews for dropdown selection
     */
    public function getForDropdown()
    {
        $sql = "SELECT c.id, c.full_name as name, c.employee_id, r.name as `rank`
                FROM {$this->table} c
                LEFT JOIN ranks r ON c.current_rank_id = r.id
                WHERE c.status IN ('available', 'onboard')
                ORDER BY c.full_name ASC";
        return $this->query($sql);
    }
}



/**
 * Crew Experience Model
 */
class CrewExperienceModel extends BaseModel
{
    protected $table = 'crew_experiences';
    protected $allowedFields = [
        'crew_id',
        'vessel_name',
        'vessel_type',
        'vessel_flag',
        'gross_tonnage',
        'engine_type',
        'company_name',
        'rank_position',
        'start_date',
        'end_date',
        'reason_leaving',
        'reference_contact'
    ];

    public function getByCrew($crewId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE crew_id = ? ORDER BY start_date DESC";
        return $this->query($sql, [$crewId], 'i');
    }

    public function calculateTotalSeaTime($crewId)
    {
        $sql = "SELECT SUM(TIMESTAMPDIFF(MONTH, start_date, COALESCE(end_date, CURDATE()))) as total_months 
                FROM {$this->table} WHERE crew_id = ?";
        $result = $this->query($sql, [$crewId], 'i');
        return $result[0]['total_months'] ?? 0;
    }
}

/**
 * Crew Document Model
 */
class CrewDocumentModel extends BaseModel
{
    protected $table = 'crew_documents';
    protected $allowedFields = [
        'crew_id',
        'document_type',
        'document_name',
        'document_number',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'issuing_place',
        'status',
        'verified_by',
        'verified_at',
        'notes',
        'uploaded_by'
    ];

    /**
     * Get documents by crew
     */
    public function getByCrew($crewId)
    {
        $sql = "SELECT d.*, dt.name as type_name, dt.name_id as type_name_id, dt.category
                FROM {$this->table} d
                LEFT JOIN document_types dt ON d.document_type = dt.code
                WHERE d.crew_id = ?
                ORDER BY dt.sort_order, d.document_name";
        return $this->query($sql, [$crewId], 'i');
    }

    /**
     * Get expiring documents (within X days)
     */
    public function getExpiring($days = 90)
    {
        $sql = "SELECT d.*, c.full_name as crew_name, c.employee_id, dt.name as type_name
                FROM {$this->table} d
                JOIN crews c ON d.crew_id = c.id
                LEFT JOIN document_types dt ON d.document_type = dt.code
                WHERE d.expiry_date IS NOT NULL 
                AND d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND d.status != 'expired'
                ORDER BY d.expiry_date ASC";
        return $this->query($sql, [$days], 'i');
    }

    /**
     * Get expired documents
     */
    public function getExpired()
    {
        $sql = "SELECT d.*, c.full_name as crew_name, c.employee_id, dt.name as type_name
                FROM {$this->table} d
                JOIN crews c ON d.crew_id = c.id
                LEFT JOIN document_types dt ON d.document_type = dt.code
                WHERE d.expiry_date IS NOT NULL 
                AND d.expiry_date < CURDATE()
                ORDER BY d.expiry_date ASC";
        return $this->query($sql);
    }

    /**
     * Update document statuses based on expiry
     */
    public function updateExpiryStatuses()
    {
        // Mark as expired
        $this->execute("UPDATE {$this->table} SET status = 'expired' 
                       WHERE expiry_date < CURDATE() AND status != 'expired'");

        // Mark as expiring soon (within 90 days)
        $this->execute("UPDATE {$this->table} SET status = 'expiring_soon' 
                       WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) 
                       AND status = 'valid'");

        // Mark as valid if expiry is more than 90 days
        $this->execute("UPDATE {$this->table} SET status = 'valid' 
                       WHERE expiry_date > DATE_ADD(CURDATE(), INTERVAL 90 DAY) 
                       AND status = 'expiring_soon'");
    }

    /**
     * Verify document
     */
    public function verify($id, $userId)
    {
        return $this->update($id, [
            'verified_by' => $userId,
            'verified_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get document counts by status
     */
    public function getStatusCounts()
    {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        $results = $this->query($sql);
        $counts = ['valid' => 0, 'expiring_soon' => 0, 'expired' => 0, 'pending' => 0];
        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }
        return $counts;
    }
}

/**
 * Document Type Model
 */
class DocumentTypeModel extends BaseModel
{
    protected $table = 'document_types';
    protected $allowedFields = [
        'code',
        'name',
        'name_id',
        'description',
        'is_mandatory',
        'validity_years',
        'reminder_days',
        'category',
        'sort_order',
        'is_active'
    ];

    public function getActive()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order";
        return $this->query($sql);
    }

    public function getByCategory($category)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category = ? AND is_active = 1 ORDER BY sort_order";
        return $this->query($sql, [$category], 's');
    }
}
