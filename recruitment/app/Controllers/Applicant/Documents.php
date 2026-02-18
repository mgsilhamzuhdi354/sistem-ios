<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Applicant Documents Controller
 */
class Documents extends BaseController {
    
    private $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get document types
        $types = $this->db->query("SELECT * FROM document_types ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        
        // Get user documents
        $stmt = $this->db->prepare("
            SELECT d.*, dt.name as type_name, dt.is_required,
                   u.full_name as verified_by_name
            FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            LEFT JOIN users u ON d.verified_by = u.id
            WHERE d.user_id = ?
            ORDER BY dt.sort_order
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Organize by type
        $documentsByType = [];
        foreach ($documents as $doc) {
            $documentsByType[$doc['document_type_id']] = $doc;
        }
        
        $this->view('applicant/documents/index', [
            'types' => $types,
            'documents' => $documents,
            'documentsByType' => $documentsByType,
            'pageTitle' => 'My Documents'
        ]);
    }
    
    public function upload() {
        if (!$this->isPost()) {
            $this->redirect(url('/applicant/documents'));
        }
        
        validate_csrf();
        
        $userId = $_SESSION['user_id'];
        $documentTypeId = $this->input('document_type_id');
        $documentNumber = $this->input('document_number');
        $issueDate = $this->input('issue_date');
        $expiryDate = $this->input('expiry_date');
        $issuedBy = $this->input('issued_by');
        
        $file = $this->file('document_file');
        
        // Validate file
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Please select a file to upload');
            $this->redirect(url('/applicant/documents'));
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            flash('error', 'File size must be less than 5MB');
            $this->redirect(url('/applicant/documents'));
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            flash('error', 'Invalid file type. Allowed: ' . implode(', ', $this->allowedTypes));
            $this->redirect(url('/applicant/documents'));
        }
        
        // Generate unique filename
        $fileName = uniqid('doc_') . '_' . time() . '.' . $extension;
        $uploadDir = FCPATH . 'uploads/documents/' . $userId . '/';
        
        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Delete old document of same type
            $oldStmt = $this->db->prepare("
                SELECT file_path FROM documents 
                WHERE user_id = ? AND document_type_id = ?
            ");
            $oldStmt->bind_param('ii', $userId, $documentTypeId);
            $oldStmt->execute();
            $oldDoc = $oldStmt->get_result()->fetch_assoc();
            
            if ($oldDoc && file_exists($oldDoc['file_path'])) {
                unlink($oldDoc['file_path']);
            }
            
            // Delete old record
            $delStmt = $this->db->prepare("DELETE FROM documents WHERE user_id = ? AND document_type_id = ?");
            $delStmt->bind_param('ii', $userId, $documentTypeId);
            $delStmt->execute();
            
            // Insert new document
            $relPath = 'uploads/documents/' . $userId . '/' . $fileName;
            $fileSize = $file['size'];
            $fileType = $file['type'];
            
            $stmt = $this->db->prepare("
                INSERT INTO documents (user_id, document_type_id, file_name, original_name, file_path, 
                                       file_size, file_type, document_number, issue_date, expiry_date, 
                                       issued_by, verification_status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->bind_param('iisssisssss', 
                $userId, $documentTypeId, $fileName, $file['name'], $relPath,
                $fileSize, $fileType, $documentNumber, $issueDate, $expiryDate, $issuedBy
            );
            
            if ($stmt->execute()) {
                // Update profile completion
                $this->updateProfileCompletion($userId);
                
                flash('success', 'Document uploaded successfully');
            } else {
                flash('error', 'Failed to save document');
            }
        } else {
            flash('error', 'Failed to upload file');
        }
        
        $this->redirect(url('/applicant/documents'));
    }
    
    public function delete($id) {
        $userId = $_SESSION['user_id'];
        
        // Get document
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $document = $stmt->get_result()->fetch_assoc();
        
        if (!$document) {
            flash('error', 'Document not found');
            $this->redirect(url('/applicant/documents'));
        }
        
        // Delete file
        $fullPath = FCPATH . $document['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        // Delete record
        $delStmt = $this->db->prepare("DELETE FROM documents WHERE id = ?");
        $delStmt->bind_param('i', $id);
        $delStmt->execute();
        
        // Update profile completion
        $this->updateProfileCompletion($userId);
        
        flash('success', 'Document deleted');
        $this->redirect(url('/applicant/documents'));
    }
    
    private function updateProfileCompletion($userId) {
        // Count required documents
        $requiredCount = $this->db->query("SELECT COUNT(*) as c FROM document_types WHERE is_required = 1")->fetch_assoc()['c'];
        
        // Count uploaded required documents
        $uploadedStmt = $this->db->prepare("
            SELECT COUNT(*) as c FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            WHERE d.user_id = ? AND dt.is_required = 1
        ");
        $uploadedStmt->bind_param('i', $userId);
        $uploadedStmt->execute();
        $uploadedCount = $uploadedStmt->get_result()->fetch_assoc()['c'];
        
        $completion = $requiredCount > 0 ? round(($uploadedCount / $requiredCount) * 100) : 0;
        
        $updateStmt = $this->db->prepare("UPDATE applicant_profiles SET profile_completion = ? WHERE user_id = ?");
        $updateStmt->bind_param('ii', $completion, $userId);
        $updateStmt->execute();
    }
}
