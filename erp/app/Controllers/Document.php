<?php
/**
 * PT Indo Ocean - ERP System
 * Document Controller - Handles contract document uploads
 */

namespace App\Controllers;

require_once APPPATH . 'Models/ContractModel.php';

use App\Models\BaseModel;

class ContractDocumentModel extends BaseModel
{
    protected $table = 'contract_documents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'contract_id', 'document_type', 'language', 'file_name', 
        'file_path', 'file_size', 'is_signed', 'signed_at', 
        'signature_type', 'generated_by'
    ];
    
    public function getByContract($contractId)
    {
        $sql = "SELECT * FROM contract_documents WHERE contract_id = ? ORDER BY created_at DESC";
        return $this->query($sql, [$contractId], 'i');
    }
}

class Document extends BaseController
{
    private $docModel;
    private $uploadPath;
    
    public function __construct()
    {
        parent::__construct();
        $this->docModel = new ContractDocumentModel($this->db);
        $this->uploadPath = FCPATH . 'uploads/contracts/';
        
        // Create upload directory if not exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    /**
     * Upload document for contract
     */
    public function upload($contractId)
    {
        if (!$this->isPost()) {
            $this->redirect('contracts/' . $contractId);
        }
        
        if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Please select a file to upload');
            $this->redirect('contracts/' . $contractId);
        }
        
        $file = $_FILES['document'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate extension
        if (!in_array($ext, ALLOWED_DOCUMENT_TYPES)) {
            $this->setFlash('error', 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_DOCUMENT_TYPES));
            $this->redirect('contracts/' . $contractId);
        }
        
        // Validate size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $this->setFlash('error', 'File too large. Max size: ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB');
            $this->redirect('contracts/' . $contractId);
        }
        
        // Generate unique filename
        $newFilename = 'contract_' . $contractId . '_' . time() . '.' . $ext;
        $filePath = $this->uploadPath . $newFilename;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $this->docModel->insert([
                'contract_id' => $contractId,
                'document_type' => $this->input('document_type', 'contract'),
                'language' => $this->input('language', 'id'),
                'file_name' => $file['name'],
                'file_path' => 'uploads/contracts/' . $newFilename,
                'file_size' => $file['size'],
                'generated_by' => $this->getCurrentUser()['id'] ?? null
            ]);
            
            $this->setFlash('success', 'Document uploaded successfully');
        } else {
            $this->setFlash('error', 'Failed to upload file');
        }
        
        $this->redirect('contracts/' . $contractId);
    }
    
    /**
     * Download document
     */
    public function download($docId)
    {
        $doc = $this->docModel->find($docId);
        if (!$doc) {
            $this->setFlash('error', 'Document not found');
            $this->redirect('contracts');
        }
        
        $filePath = FCPATH . $doc['file_path'];
        if (!file_exists($filePath)) {
            $this->setFlash('error', 'File not found on server');
            $this->redirect('contracts/' . $doc['contract_id']);
        }
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $doc['file_name'] . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
    
    /**
     * Mark document as signed
     */
    public function markSigned($docId)
    {
        if (!$this->isPost()) {
            $this->redirect('contracts');
        }
        
        $doc = $this->docModel->find($docId);
        if (!$doc) {
            $this->setFlash('error', 'Document not found');
            $this->redirect('contracts');
        }
        
        $this->docModel->update($docId, [
            'is_signed' => 1,
            'signed_at' => date('Y-m-d H:i:s'),
            'signature_type' => $this->input('signature_type', 'manual')
        ]);
        
        $this->setFlash('success', 'Document marked as signed');
        $this->redirect('contracts/' . $doc['contract_id']);
    }
    
    /**
     * Delete document
     */
    public function delete($docId)
    {
        $doc = $this->docModel->find($docId);
        if (!$doc) {
            $this->setFlash('error', 'Document not found');
            $this->redirect('contracts');
        }
        
        // Delete file
        $filePath = FCPATH . $doc['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $this->docModel->delete($docId);
        
        $this->setFlash('success', 'Document deleted');
        $this->redirect('contracts/' . $doc['contract_id']);
    }
}
