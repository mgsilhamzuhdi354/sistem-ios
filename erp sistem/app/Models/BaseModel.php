<?php
/**
 * PT Indo Ocean - ERP System
 * Base Model
 */

namespace App\Models;

class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function findAll($conditions = [], $orderBy = null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $types = '';
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    // Handle IN clause
                    $placeholders = implode(',', array_fill(0, count($value), '?'));
                    $whereClauses[] = "$field IN ($placeholders)";
                    foreach ($value as $v) {
                        $params[] = $v;
                        $types .= is_int($v) ? 'i' : 's';
                    }
                } else {
                    $whereClauses[] = "$field = ?";
                    $params[] = $value;
                    $types .= is_int($value) ? 'i' : 's';
                }
            }
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function insert($data)
    {
        $fields = array_intersect_key($data, array_flip($this->allowedFields));
        $columns = implode(', ', array_keys($fields));
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        $types = '';
        $values = [];
        foreach ($fields as $value) {
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            $values[] = $value;
        }
        
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Alias for insert
     */
    public function create($data)
    {
        return $this->insert($data);
    }
    
    public function update($id, $data)
    {
        $fields = array_intersect_key($data, array_flip($this->allowedFields));
        $setClauses = [];
        $types = '';
        $values = [];
        
        foreach ($fields as $field => $value) {
            $setClauses[] = "$field = ?";
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            $values[] = $value;
        }
        
        $types .= 'i';
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = ?";
        
        // Ensure connection is alive before executing
        if (!$this->db->ping()) {
            // Connection lost, try to reconnect
            $dbConfig = require APPPATH . 'Config/Database.php';
            $this->db = new \mysqli(
                $dbConfig['default']['hostname'],
                $dbConfig['default']['username'],
                $dbConfig['default']['password'],
                $dbConfig['default']['database'],
                $dbConfig['default']['port']
            );
            $this->db->set_charset($dbConfig['default']['charset']);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
    
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $types = '';
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $field => $value) {
                $whereClauses[] = "$field = ?";
                $params[] = $value;
                $types .= is_int($value) ? 'i' : 's';
            }
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    public function query($sql, $params = [], $types = '')
    {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function execute($sql, $params = [], $types = '')
    {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }
}
