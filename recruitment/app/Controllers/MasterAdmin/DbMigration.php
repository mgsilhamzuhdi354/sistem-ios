<?php

namespace App\Controllers\MasterAdmin;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Database Migration Controller
 * Manages database schema updates and connection testing
 */
class DbMigration extends BaseController
{
    protected $db;
    protected $forge;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->forge = \Config\Database::forge();
    }
    
    /**
     * Check database connection status
     */
    public function checkConnection()
    {
        $result = [
            'status' => 'error',
            'message' => '',
            'details' => []
        ];
        
        try {
            // Test connection
            $connected = $this->db->connect();
            
            if ($connected) {
                $result['status'] = 'success';
                $result['message'] = 'Koneksi database berhasil!';
                
                // Get database info
                $result['details'] = [
                    'database' => $this->db->getDatabase(),
                    'hostname' => $this->db->hostname,
                    'driver' => $this->db->DBDriver,
                    'version' => $this->db->getVersion(),
                    'platform' => $this->db->getPlatform(),
                    'tables_count' => count($this->db->listTables()),
                    'tables' => $this->db->listTables()
                ];
            }
        } catch (DatabaseException $e) {
            $result['status'] = 'error';
            $result['message'] = 'Koneksi gagal: ' . $e->getMessage();
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($result);
    }
    
    /**
     * Run all migrations
     */
    public function runMigrations()
    {
        $results = [];
        $errors = [];
        
        // Run each migration
        $migrations = [
            'createMissingTables' => 'Membuat tabel yang belum ada',
            'addMissingColumns' => 'Menambah kolom yang kurang',
            'addMissingIndexes' => 'Menambah index untuk performa',
            'updateColumnTypes' => 'Update tipe kolom',
        ];
        
        foreach ($migrations as $method => $description) {
            try {
                $result = $this->$method();
                $results[] = [
                    'migration' => $description,
                    'status' => 'success',
                    'details' => $result
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'migration' => $description,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $this->response->setJSON([
            'status' => empty($errors) ? 'success' : 'partial',
            'message' => empty($errors) ? 'Semua migration berhasil!' : 'Beberapa migration gagal',
            'results' => $results,
            'errors' => $errors
        ]);
    }
    
    /**
     * Create missing tables
     */
    protected function createMissingTables()
    {
        $created = [];
        $existingTables = $this->db->listTables();
        
        // Define required tables
        $requiredTables = [
            'roles', 'users', 'password_resets', 'applicant_profiles',
            'departments', 'vessel_types', 'job_vacancies', 
            'application_statuses', 'applications', 'application_status_history',
            'document_types', 'documents',
            'interview_question_banks', 'interview_questions', 
            'interview_sessions', 'interview_answers',
            'medical_checkups', 'notifications', 'audit_logs', 'settings'
        ];
        
        foreach ($requiredTables as $table) {
            if (!in_array($table, $existingTables)) {
                $created[] = $table;
            }
        }
        
        // Create roles table if missing
        if (!in_array('roles', $existingTables)) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'auto_increment' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 50],
                'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('name');
            $this->forge->createTable('roles', true);
            
            // Insert default roles
            $this->db->table('roles')->insertBatch([
                ['name' => 'admin', 'description' => 'System Administrator'],
                ['name' => 'hr_staff', 'description' => 'HR Staff - Limited Access'],
                ['name' => 'applicant', 'description' => 'Job Applicant'],
            ]);
        }
        
        // Create otp_codes table if missing (from migration files)
        if (!in_array('otp_codes', $existingTables)) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'auto_increment' => true],
                'user_id' => ['type' => 'INT'],
                'otp_code' => ['type' => 'VARCHAR', 'constraint' => 10],
                'expires_at' => ['type' => 'DATETIME'],
                'is_used' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('otp_codes', true);
            $created[] = 'otp_codes';
        }
        
        // Create login_logs table if missing
        if (!in_array('login_logs', $existingTables)) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'auto_increment' => true],
                'user_id' => ['type' => 'INT'],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
                'user_agent' => ['type' => 'TEXT', 'null' => true],
                'login_at' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
                'status' => ['type' => "ENUM('success','failed')", 'default' => 'success'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('login_logs', true);
            $created[] = 'login_logs';
        }
        
        // Create email_archives table if missing
        if (!in_array('email_archives', $existingTables)) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'auto_increment' => true],
                'user_id' => ['type' => 'INT', 'null' => true],
                'to_email' => ['type' => 'VARCHAR', 'constraint' => 255],
                'subject' => ['type' => 'VARCHAR', 'constraint' => 500],
                'body' => ['type' => 'TEXT'],
                'status' => ['type' => "ENUM('sent','failed','pending')", 'default' => 'pending'],
                'error_message' => ['type' => 'TEXT', 'null' => true],
                'sent_at' => ['type' => 'TIMESTAMP', 'null' => true],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('email_archives', true);
            $created[] = 'email_archives';
        }
        
        // Create crewing_ratings table if missing
        if (!in_array('crewing_ratings', $existingTables)) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'auto_increment' => true],
                'user_id' => ['type' => 'INT'],
                'application_id' => ['type' => 'INT', 'null' => true],
                'rating' => ['type' => 'DECIMAL', 'constraint' => '3,2'],
                'review' => ['type' => 'TEXT', 'null' => true],
                'rated_by' => ['type' => 'INT'],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->createTable('crewing_ratings', true);
            $created[] = 'crewing_ratings';
        }
        
        // Create crewing_policies table if missing
        if (!in_array('crewing_policies', $existingTables)) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'auto_increment' => true],
                'policy_key' => ['type' => 'VARCHAR', 'constraint' => 100],
                'policy_value' => ['type' => 'TEXT'],
                'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'updated_at' => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('policy_key');
            $this->forge->createTable('crewing_policies', true);
            $created[] = 'crewing_policies';
        }
        
        return ['created_tables' => $created];
    }
    
    /**
     * Add missing columns to existing tables
     */
    protected function addMissingColumns()
    {
        $added = [];
        
        // Check users table
        if ($this->db->tableExists('users')) {
            $fields = $this->db->getFieldNames('users');
            
            if (!in_array('two_factor_enabled', $fields)) {
                $this->forge->addColumn('users', [
                    'two_factor_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'is_active']
                ]);
                $added[] = 'users.two_factor_enabled';
            }
            
            if (!in_array('two_factor_secret', $fields)) {
                $this->forge->addColumn('users', [
                    'two_factor_secret' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'two_factor_enabled']
                ]);
                $added[] = 'users.two_factor_secret';
            }
        }
        
        // Check applicant_profiles table
        if ($this->db->tableExists('applicant_profiles')) {
            $fields = $this->db->getFieldNames('applicant_profiles');
            
            if (!in_array('marital_status', $fields)) {
                $this->forge->addColumn('applicant_profiles', [
                    'marital_status' => ['type' => "ENUM('single','married','divorced','widowed')", 'null' => true, 'after' => 'gender']
                ]);
                $added[] = 'applicant_profiles.marital_status';
            }
            
            if (!in_array('religion', $fields)) {
                $this->forge->addColumn('applicant_profiles', [
                    'religion' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'marital_status']
                ]);
                $added[] = 'applicant_profiles.religion';
            }
        }
        
        // Check applications table
        if ($this->db->tableExists('applications')) {
            $fields = $this->db->getFieldNames('applications');
            
            if (!in_array('priority', $fields)) {
                $this->forge->addColumn('applications', [
                    'priority' => ['type' => "ENUM('low','normal','high','urgent')", 'default' => 'normal', 'after' => 'status_id']
                ]);
                $added[] = 'applications.priority';
            }
        }
        
        // Check documents table  
        if ($this->db->tableExists('documents')) {
            $fields = $this->db->getFieldNames('documents');
            
            if (!in_array('is_primary', $fields)) {
                $this->forge->addColumn('documents', [
                    'is_primary' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'rejection_reason']
                ]);
                $added[] = 'documents.is_primary';
            }
        }
        
        return ['added_columns' => $added];
    }
    
    /**
     * Add missing indexes for performance
     */
    protected function addMissingIndexes()
    {
        $added = [];
        
        // Get existing indexes helper
        $getIndexes = function($table) {
            $result = $this->db->query("SHOW INDEX FROM {$table}")->getResultArray();
            return array_column($result, 'Key_name');
        };
        
        // Applications indexes
        if ($this->db->tableExists('applications')) {
            $indexes = $getIndexes('applications');
            
            if (!in_array('idx_applications_submitted', $indexes)) {
                $this->db->query("CREATE INDEX idx_applications_submitted ON applications(submitted_at)");
                $added[] = 'applications.idx_applications_submitted';
            }
        }
        
        // Documents indexes
        if ($this->db->tableExists('documents')) {
            $indexes = $getIndexes('documents');
            
            if (!in_array('idx_documents_expiry', $indexes)) {
                $this->db->query("CREATE INDEX idx_documents_expiry ON documents(expiry_date)");
                $added[] = 'documents.idx_documents_expiry';
            }
        }
        
        // Audit logs indexes
        if ($this->db->tableExists('audit_logs')) {
            $indexes = $getIndexes('audit_logs');
            
            if (!in_array('idx_audit_created', $indexes)) {
                $this->db->query("CREATE INDEX idx_audit_created ON audit_logs(created_at)");
                $added[] = 'audit_logs.idx_audit_created';
            }
        }
        
        return ['added_indexes' => $added];
    }
    
    /**
     * Update column types if needed
     */
    protected function updateColumnTypes()
    {
        $updated = [];
        
        // Example: Update settings value column to LONGTEXT for large JSON
        if ($this->db->tableExists('settings')) {
            $fields = $this->db->getFieldData('settings');
            foreach ($fields as $field) {
                if ($field->name === 'setting_value' && $field->type !== 'longtext') {
                    $this->forge->modifyColumn('settings', [
                        'setting_value' => ['type' => 'LONGTEXT', 'null' => true]
                    ]);
                    $updated[] = 'settings.setting_value -> LONGTEXT';
                }
            }
        }
        
        return ['updated_columns' => $updated];
    }
    
    /**
     * Display migration status page
     */
    public function index()
    {
        // Check if admin
        if (session()->get('role_id') != 1) {
            return redirect()->to('/master-admin')->with('error', 'Akses ditolak');
        }
        
        $existingTables = $this->db->listTables();
        
        $data = [
            'title' => 'Database Migration',
            'tables' => $existingTables,
            'dbInfo' => [
                'database' => $this->db->getDatabase(),
                'version' => $this->db->getVersion(),
                'platform' => $this->db->getPlatform(),
            ]
        ];
        
        return view('master_admin/db_migration', $data);
    }
}
