<?php
/**
 * ERP Sync Library
 * Handles synchronization between Recruitment and ERP databases
 */
class ErpSync {
    private $erpDb;
    private $recruitDb;
    
    public function __construct($recruitDb) {
        $this->recruitDb = $recruitDb;
        $this->erpDb = $this->getErpConnection();
    }
    
    /**
     * Get connection to ERP database
     */
    private function getErpConnection() {
        // Load ERP database config from Config/Database.php
        $dbConfig = require APPPATH . 'Config/Database.php';
        $erp = $dbConfig['erp'] ?? $dbConfig['default'];
        
        $conn = new mysqli(
            $erp['hostname'] ?? 'localhost',
            $erp['username'] ?? 'root',
            $erp['password'] ?? '',
            $erp['database'] ?? 'erp_db',
            $erp['port'] ?? 3306
        );
        if ($conn->connect_error) {
            throw new Exception('ERP database connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset($erp['charset'] ?? 'utf8mb4');
        return $conn;
    }
    
    /**
     * Create crew record in ERP system
     * 
     * @param array $data Crew data
     * @return int Crew ID in ERP
     */
    public function createCrew($data) {
        // Validate required fields
        $this->validateCrewData($data);
        
        // Prepare data for insertion
        $fullName = $data['full_name'];
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $employeeId = $data['employee_id'] ?? 'IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $candidateId = $data['candidate_id'] ?? null;
        $rankId = $data['rank_id'] ?? null;
        $source = 'recruitment';
        $status = $data['status'] ?? 'pending_approval';
        $joinDate = $data['join_date'] ?? null;
        $notes = $data['notes'] ?? '';
        
        // Map gender to ERP enum: 'male' or 'female'
        $rawGender = strtolower(trim($data['gender'] ?? ''));
        if (in_array($rawGender, ['male', 'laki-laki', 'l', 'm'])) {
            $gender = 'male';
        } elseif (in_array($rawGender, ['female', 'perempuan', 'p', 'f', 'w', 'wanita'])) {
            $gender = 'female';
        } else {
            $gender = 'male'; // Default fallback
        }
        
        // Personal details
        $birthDate = $data['birth_date'] ?? null;
        $birthPlace = $data['birth_place'] ?? '';
        $nationality = $data['nationality'] ?? 'Indonesian';
        $religion = $data['religion'] ?? '';
        $maritalStatus = $data['marital_status'] ?? null;
        $address = $data['address'] ?? '';
        $city = $data['city'] ?? '';
        $province = $data['province'] ?? '';
        $postalCode = $data['postal_code'] ?? '';
        
        // Emergency contact
        $emergencyName = $data['emergency_name'] ?? '';
        $emergencyRelation = $data['emergency_relation'] ?? '';
        $emergencyPhone = $data['emergency_phone'] ?? '';
        
        // Sea experience
        $totalSeaTime = intval($data['total_sea_time_months'] ?? 0);
        
        // Insert into crews table
        $stmt = $this->erpDb->prepare("
            INSERT INTO crews (
                employee_id, full_name, email, phone, 
                gender, birth_date, birth_place, nationality, religion, marital_status,
                address, city, province, postal_code,
                emergency_name, emergency_relation, emergency_phone,
                total_sea_time_months,
                current_rank_id, status, source, candidate_id, 
                notes, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->bind_param(
            'sssssssssssssssssisssis',
            $employeeId, $fullName, $email, $phone,
            $gender, $birthDate, $birthPlace, $nationality, $religion, $maritalStatus,
            $address, $city, $province, $postalCode,
            $emergencyName, $emergencyRelation, $emergencyPhone,
            $totalSeaTime,
            $rankId, $status, $source, $candidateId,
            $notes
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create crew in ERP: ' . $stmt->error);
        }
        
        $crewId = $this->erpDb->insert_id;
        $stmt->close();
        
        return $crewId;
    }
    
    /**
     * Validate crew data before sending to ERP
     * 
     * @param array $data
     * @throws Exception if validation fails
     */
    public function validateCrewData($data) {
        $required = ['full_name'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Required field '$field' is missing");
            }
        }
        
        // Validate email format if provided
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        return true;
    }
    
    /**
     * Check if crew already exists in ERP by candidate_id
     * 
     * @param int $candidateId
     * @return int|false Crew ID if exists, false otherwise
     */
    public function getCrewByCandidateId($candidateId) {
        $stmt = $this->erpDb->prepare("SELECT id FROM crews WHERE candidate_id = ? AND source = 'recruitment'");
        $stmt->bind_param('i', $candidateId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['id'];
        }
        
        return false;
    }
    
     /**
     * Update existing crew record
     * 
     * @param int $crewId
     * @param array $data
     * @return bool
     */
    public function updateCrew($crewId, $data) {
        $updates = [];
        $types = '';
        $values = [];
        
        $allowedFields = [
            'full_name', 'email', 'phone', 'current_rank_id', 'status', 'notes',
            'gender', 'birth_date', 'birth_place', 'nationality', 'religion', 'marital_status',
            'address', 'city', 'province', 'postal_code',
            'emergency_name', 'emergency_phone', 'emergency_relation',
            'whatsapp', 'photo', 'years_experience', 'total_sea_time_months'
        ];
        
        $intFields = ['current_rank_id', 'years_experience', 'total_sea_time_months'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $types .= in_array($field, $intFields) ? 'i' : 's';
                $values[] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return true; // Nothing to update
        }
        
        $values[] = $crewId;
        $types .= 'i';
        
        $sql = "UPDATE crews SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->erpDb->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    /**
     * Sync documents from recruitment to ERP crew_documents
     * 
     * @param int $crewId ERP crew ID
     * @param array $documents Array of document records from recruitment DB
     * @param int $uploadedBy User ID who uploaded
     * @return int Number of documents synced
     */
    public function syncDocuments($crewId, $documents, $uploadedBy = null) {
        $synced = 0;
        
        foreach ($documents as $doc) {
            // Map document type
            $docTypeCode = $this->mapDocumentType($doc['type_name'] ?? '', $doc['document_type_id'] ?? 0);
            $docName = $doc['type_name'] ?? 'Document';
            
            // Check if this document already exists for this crew
            $checkStmt = $this->erpDb->prepare(
                "SELECT id FROM crew_documents WHERE crew_id = ? AND document_type = ? AND document_number = ?"
            );
            $docNumber = $doc['document_number'] ?? '';
            $checkStmt->bind_param('iss', $crewId, $docTypeCode, $docNumber);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();
            
            if ($existing) {
                continue; // Skip duplicate
            }
            
            // Copy file to ERP uploads directory
            $erpFilePath = null;
            $erpFileName = null;
            $fileSize = null;
            $mimeType = null;
            
            if (!empty($doc['file_path'])) {
                $copyResult = $this->copyDocumentFile($doc['file_path'], $crewId, $doc['file_name'] ?? '');
                if ($copyResult) {
                    $erpFilePath = $copyResult['path'];
                    $erpFileName = $copyResult['name'];
                    $fileSize = $copyResult['size'];
                    $mimeType = $copyResult['mime'];
                }
            }
            
            // Determine document status based on expiry
            $status = 'pending';
            if (!empty($doc['expiry_date'])) {
                $expiry = strtotime($doc['expiry_date']);
                $now = time();
                $daysUntilExpiry = ($expiry - $now) / 86400;
                if ($daysUntilExpiry < 0) {
                    $status = 'expired';
                } elseif ($daysUntilExpiry < 90) {
                    $status = 'expiring_soon';
                } else {
                    $status = 'valid';
                }
            }
            
            // Insert into crew_documents
            $stmt = $this->erpDb->prepare("
                INSERT INTO crew_documents (
                    crew_id, document_type, document_name, document_number,
                    file_path, file_name, file_size, mime_type,
                    issue_date, expiry_date, issuing_authority,
                    status, uploaded_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $issueDate = $doc['issue_date'] ?? null;
            $expiryDate = $doc['expiry_date'] ?? null;
            $issuedBy = $doc['issued_by'] ?? null;
            
            // Set uploaded_by to NULL to avoid FK constraint error
            // Recruitment user IDs don't exist in ERP users table
            $uploadedByErp = null;
            
            $stmt->bind_param(
                'issssssissssi',
                $crewId, $docTypeCode, $docName, $docNumber,
                $erpFilePath, $erpFileName, $fileSize, $mimeType,
                $issueDate, $expiryDate, $issuedBy,
                $status, $uploadedByErp
            );
            
            if ($stmt->execute()) {
                $synced++;
            }
            $stmt->close();
        }
        
        return $synced;
    }
    
    /**
     * Copy document file from recruitment uploads to ERP uploads
     * 
     * @param string $sourcePath Relative path in recruitment
     * @param int $crewId ERP crew ID
     * @param string $fileName Original filename
     * @return array|false File info or false on failure
     */
    public function copyDocumentFile($sourcePath, $crewId, $fileName = '') {
        // Build absolute source path
        $recruitmentBase = dirname(dirname(dirname(__FILE__))); // recruitment root
        $absSource = $recruitmentBase . '/public/' . ltrim($sourcePath, '/');
        
        if (!file_exists($absSource)) {
            // Try alternative path
            $absSource = $recruitmentBase . '/' . ltrim($sourcePath, '/');
        }
        
        if (!file_exists($absSource)) {
            return false;
        }
        
        // Build ERP destination directory
        $erpBase = dirname(dirname(dirname(dirname(__FILE__)))); // PT_indoocean root
        $erpUploadsDir = $erpBase . '/erp/uploads/crew_documents/' . $crewId;
        
        if (!is_dir($erpUploadsDir)) {
            mkdir($erpUploadsDir, 0755, true);
        }
        
        // Generate unique filename
        $ext = pathinfo($absSource, PATHINFO_EXTENSION);
        $newFileName = $fileName ?: ('doc_' . time() . '_' . rand(1000, 9999) . '.' . $ext);
        $destPath = $erpUploadsDir . '/' . $newFileName;
        
        if (copy($absSource, $destPath)) {
            return [
                'path' => 'uploads/crew_documents/' . $crewId . '/' . $newFileName,
                'name' => $newFileName,
                'size' => filesize($destPath),
                'mime' => mime_content_type($destPath) ?: 'application/octet-stream'
            ];
        }
        
        return false;
    }
    
    /**
     * Copy profile photo from recruitment to ERP
     * 
     * @param string $photoPath Photo path from recruitment
     * @param int $crewId ERP crew ID
     * @return string|null ERP photo path or null
     */
    public function syncPhoto($photoPath, $crewId) {
        if (empty($photoPath)) return null;
        
        $recruitmentBase = dirname(dirname(dirname(__FILE__)));
        
        // Try multiple possible locations
        $possiblePaths = [
            $recruitmentBase . '/public/assets/uploads/photos/' . basename($photoPath),
            $recruitmentBase . '/public/' . ltrim($photoPath, '/'),
            $recruitmentBase . '/' . ltrim($photoPath, '/'),
        ];
        
        $absSource = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $absSource = $path;
                break;
            }
        }
        
        if (!$absSource) return null;
        
        $erpBase = dirname(dirname(dirname(dirname(__FILE__))));
        $erpPhotoDir = $erpBase . '/erp/uploads/crew_photos';
        
        if (!is_dir($erpPhotoDir)) {
            mkdir($erpPhotoDir, 0755, true);
        }
        
        $ext = pathinfo($absSource, PATHINFO_EXTENSION);
        $newName = 'crew_' . $crewId . '_' . time() . '.' . $ext;
        $destPath = $erpPhotoDir . '/' . $newName;
        
        if (copy($absSource, $destPath)) {
            return 'uploads/crew_photos/' . $newName;
        }
        
        return null;
    }
    
    /**
     * Map recruitment document type to ERP document type code
     * 
     * @param string $typeName Document type name from recruitment
     * @param int $typeId Document type ID from recruitment
     * @return string ERP document type code
     */
    private function mapDocumentType($typeName, $typeId = 0) {
        $typeMap = [
            'CV / Resume' => 'OTHER',
            'CV' => 'OTHER',
            'Photo' => 'OTHER',
            'Foto' => 'OTHER',
            'KTP' => 'KTP',
            'Passport' => 'PASSPORT',
            'Paspor' => 'PASSPORT',
            'Seaman Book' => 'SEAMAN_BOOK',
            'Buku Pelaut' => 'SEAMAN_BOOK',
            'COC' => 'COC',
            'Certificate of Competency' => 'COC',
            'COE' => 'COE',
            'BST' => 'BST',
            'Basic Safety Training' => 'BST',
            'Medical Certificate' => 'MEDICAL',
            'Sertifikat Kesehatan' => 'MEDICAL',
            'AFF' => 'AFF',
            'PST' => 'PST',
            'MFA' => 'MFA',
            'GMDSS' => 'GMDSS',
            'SSO' => 'SSO',
            'SID' => 'SID',
            'Kartu Keluarga' => 'KK',
            'KK' => 'KK',
            'ECDIS' => 'ECDIS',
            'RADAR' => 'RADAR',
            'ARPA' => 'ARPA',
        ];
        
        // Try exact match
        if (isset($typeMap[$typeName])) {
            return $typeMap[$typeName];
        }
        
        // Try partial match
        $lowerName = strtolower($typeName);
        foreach ($typeMap as $key => $code) {
            if (stripos($lowerName, strtolower($key)) !== false) {
                return $code;
            }
        }
        
        return 'OTHER';
    }
    
    /**
     * Get ranks from ERP for dropdown
     * 
     * @return array
     */
    public function getRanks() {
        // Check if ranks table exists
        $tableCheck = $this->erpDb->query("SHOW TABLES LIKE 'ranks'");
        if (!$tableCheck || $tableCheck->num_rows === 0) {
            // Create ranks table with default data
            $this->createDefaultRanksTable();
        }
        
        // Try with is_active column first
        $result = @$this->erpDb->query("SELECT id, name, department as category FROM ranks WHERE is_active = 1 ORDER BY department, name");
        
        if (!$result) {
            // Fallback: try without is_active column
            $result = @$this->erpDb->query("SELECT id, name, department as category FROM ranks ORDER BY department, name");
        }
        
        if (!$result) {
            // Last fallback: try minimal query
            $result = @$this->erpDb->query("SELECT id, name, '' as category FROM ranks ORDER BY name");
        }
        
        if (!$result) {
            throw new Exception('Cannot read ranks table: ' . $this->erpDb->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Create default ranks table if it doesn't exist
     */
    private function createDefaultRanksTable() {
        $this->erpDb->query("
            CREATE TABLE IF NOT EXISTS `ranks` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `department` VARCHAR(50) DEFAULT 'Deck',
                `is_active` TINYINT(1) DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Insert default maritime ranks
        $ranks = [
            ['Master', 'Deck'],
            ['Chief Officer', 'Deck'],
            ['2nd Officer', 'Deck'],
            ['3rd Officer', 'Deck'],
            ['Deck Cadet', 'Deck'],
            ['Bosun', 'Deck'],
            ['AB Seaman', 'Deck'],
            ['OS Seaman', 'Deck'],
            ['Chief Engineer', 'Engine'],
            ['2nd Engineer', 'Engine'],
            ['3rd Engineer', 'Engine'],
            ['4th Engineer', 'Engine'],
            ['Engine Cadet', 'Engine'],
            ['Fitter', 'Engine'],
            ['Oiler', 'Engine'],
            ['Wiper', 'Engine'],
            ['Electrician', 'Engine'],
            ['Chief Cook', 'Catering'],
            ['Cook', 'Catering'],
            ['Messman', 'Catering'],
            ['Steward', 'Catering'],
            ['Pumpman', 'Deck'],
        ];
        
        $stmt = $this->erpDb->prepare("INSERT IGNORE INTO ranks (name, department) VALUES (?, ?)");
        foreach ($ranks as $rank) {
            $stmt->bind_param('ss', $rank[0], $rank[1]);
            $stmt->execute();
        }
        $stmt->close();
    }
    
    /**
     * Close connections
     */
    public function __destruct() {
        if ($this->erpDb) {
            $this->erpDb->close();
        }
    }
}

