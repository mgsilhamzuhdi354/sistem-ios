<?php
/**
 * PT Indo Ocean - ERP System
 * Rank Model
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class RankModel extends BaseModel
{
    protected $table = 'ranks';
    protected $allowedFields = ['name', 'department', 'level', 'is_officer', 'is_active', 'code', 'description'];
    
    public function getForDropdown()
    {
        return $this->query("SELECT id, name, department FROM ranks WHERE is_active = 1 ORDER BY department, level");
    }
    
    public function getByDepartment($department)
    {
        return $this->findAll(['department' => $department, 'is_active' => 1], 'level ASC');
    }

    /**
     * Get all ranks with filters
     */
    public function getList($filters = [], $page = 1, $perPage = 20)
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['search'])) {
            $where[] = '(name LIKE ? OR department LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }
        
        if (!empty($filters['department'])) {
            $where[] = 'department = ?';
            $params[] = $filters['department'];
            $types .= 's';
        }
        
        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY department, level LIMIT {$perPage} OFFSET {$offset}";
        
        return $this->query($sql, $params, $types);
    }
    
    /**
     * Count ranks
     */
    public function countList($filters = [])
    {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        if (!empty($filters['search'])) {
            $where[] = '(name LIKE ? OR department LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $types .= 'ss';
        }
        
        if (!empty($filters['department'])) {
            $where[] = 'department = ?';
            $params[] = $filters['department'];
            $types .= 's';
        }
        
        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";
        
        $result = $this->query($sql, $params, $types);
        return $result[0]['total'] ?? 0;
    }
}
