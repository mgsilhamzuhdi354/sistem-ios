<?php
/**
 * PT Indo Ocean - ERP System
 * Crew Document Controller - Document Management
 */

namespace App\Controllers;

require_once APPPATH . 'Models/CrewModel.php';
require_once APPPATH . 'Models/UserModel.php';

use App\Models\CrewModel;
use App\Models\CrewDocumentModel;
use App\Models\DocumentTypeModel;
use App\Models\ActivityLogModel;

class CrewDocument extends BaseController
{
    private $docModel;
    private $crewModel;
    private $activityModel;

    public function __construct()
    {
        parent::__construct();
        $this->docModel = new CrewDocumentModel($this->db);
        $this->crewModel = new CrewModel($this->db);
        $this->activityModel = new ActivityLogModel($this->db);
    }

    /**
     * List documents for a crew
     */
    public function index($crewId = null)
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'view');

        if ($crewId) {
            // Show documents for specific crew
            $crew = $this->crewModel->find($crewId);
            if (!$crew) {
                $this->setFlash('error', 'Crew tidak ditemukan');
                $this->redirect('crews');
                return;
            }

            $documents = $this->docModel->getByCrew($crewId);

            $data = [
                'title' => 'Documents: ' . $crew['full_name'],
                'crew' => $crew,
                'documents' => $documents,
                'flash' => $this->getFlash()
            ];

            return $this->view('documents/crew_documents', $data);
        }

        // Show all documents overview
        $this->docModel->updateExpiryStatuses();

        $data = [
            'title' => 'Document Management',
            'expiring' => $this->docModel->getExpiring(90),
            'expired' => $this->docModel->getExpired(),
            'statusCounts' => $this->docModel->getStatusCounts(),
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'documents/index_modern' : 'documents/index';

        return $this->view($view, $data);
    }

    /**
     * Upload document form
     */
    public function upload($crewId)
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'create');

        $crew = $this->crewModel->find($crewId);
        if (!$crew) {
            $this->setFlash('error', 'Crew tidak ditemukan');
            $this->redirect('crews');
            return;
        }

        $docTypeModel = new DocumentTypeModel($this->db);

        $data = [
            'title' => 'Upload Document',
            'crew' => $crew,
            'documentTypes' => $docTypeModel->getActive(),
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ];

        return $this->view('documents/upload', $data);
    }

    /**
     * Create/Upload document form (alias for upload)
     */
    public function create($crewId = null)
    {
        // If no crew ID provided, redirect to crew list
        if (!$crewId) {
            $this->setFlash('error', 'Pilih crew terlebih dahulu');
            $this->redirect('crews');
            return;
        }

        // Alias to upload method
        return $this->upload($crewId);
    }

    /**
     * Store uploaded document
     */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'create');

        if (!$this->isPost()) {
            $this->redirect('documents');
            return;
        }

        $crewId = (int) $this->input('crew_id');
        $crew = $this->crewModel->find($crewId);

        if (!$crew) {
            $this->setFlash('error', 'Crew tidak ditemukan');
            $this->redirect('crews');
            return;
        }

        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Invalid security token');
            $this->redirect('documents/upload/' . $crewId);
            return;
        }

        // Handle file upload
        if (empty($_FILES['document']['name'])) {
            $this->setFlash('error', 'Pilih file untuk diupload');
            $this->redirect('documents/upload/' . $crewId);
            return;
        }

        $fileInfo = $this->uploadDocument($_FILES['document'], $crewId);

        if (!$fileInfo) {
            $this->setFlash('error', 'Gagal upload file. Pastikan format dan ukuran file sesuai.');
            $this->redirect('documents/upload/' . $crewId);
            return;
        }

        $expiryDate = $this->input('expiry_date') ?: null;
        $status = 'valid';

        if ($expiryDate) {
            $daysUntilExpiry = (strtotime($expiryDate) - time()) / 86400;
            if ($daysUntilExpiry < 0) {
                $status = 'expired';
            } elseif ($daysUntilExpiry < 90) {
                $status = 'expiring_soon';
            }
        }

        $data = [
            'crew_id' => $crewId,
            'document_type' => $this->input('document_type'),
            'document_name' => trim($this->input('document_name')),
            'document_number' => trim($this->input('document_number')),
            'file_path' => $fileInfo['path'],
            'file_name' => $fileInfo['name'],
            'file_size' => $fileInfo['size'],
            'mime_type' => $fileInfo['type'],
            'issue_date' => $this->input('issue_date') ?: null,
            'expiry_date' => $expiryDate,
            'issuing_authority' => trim($this->input('issuing_authority')),
            'issuing_place' => trim($this->input('issuing_place')),
            'status' => $status,
            'notes' => trim($this->input('notes')),
            'uploaded_by' => $this->getCurrentUser()['id']
        ];

        $docId = $this->docModel->insert($data);

        if ($docId) {
            $this->activityModel->log(
                $this->getCurrentUser()['id'],
                'upload',
                'document',
                $docId,
                "Uploaded document for {$crew['full_name']}: {$data['document_name']}"
            );

            $this->setFlash('success', 'Document berhasil diupload');
            $this->redirect('documents/' . $crewId);
        } else {
            $this->setFlash('error', 'Gagal menyimpan document');
            $this->redirect('documents/upload/' . $crewId);
        }
    }

    /**
     * Preview/download document
     */
    public function preview($id)
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'view');

        $doc = $this->docModel->find($id);

        if (!$doc || !file_exists($doc['file_path'])) {
            $this->setFlash('error', 'Document tidak ditemukan');
            $this->redirect('documents');
            return;
        }

        $mimeType = $doc['mime_type'] ?: mime_content_type($doc['file_path']);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $doc['file_name'] . '"');
        header('Content-Length: ' . filesize($doc['file_path']));

        readfile($doc['file_path']);
        exit;
    }

    /**
     * Download document
     */
    public function download($id)
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'view');

        $doc = $this->docModel->find($id);

        if (!$doc || !file_exists($doc['file_path'])) {
            $this->setFlash('error', 'Document tidak ditemukan');
            $this->redirect('documents');
            return;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $doc['file_name'] . '"');
        header('Content-Length: ' . filesize($doc['file_path']));

        readfile($doc['file_path']);
        exit;
    }

    /**
     * Delete document
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'delete');

        $doc = $this->docModel->find($id);

        if (!$doc) {
            $this->setFlash('error', 'Document tidak ditemukan');
            $this->redirect('documents');
            return;
        }

        $crewId = $doc['crew_id'];

        // Delete file
        if (file_exists($doc['file_path'])) {
            unlink($doc['file_path']);
        }

        $this->docModel->delete($id);

        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'delete',
            'document',
            $id,
            "Deleted document: {$doc['document_name']}"
        );

        $this->setFlash('success', 'Document berhasil dihapus');
        $this->redirect('documents/' . $crewId);
    }

    /**
     * Verify document
     */
    public function verify($id)
    {
        $this->requireAuth();
        $this->requirePermission('documents', 'edit');

        $doc = $this->docModel->find($id);

        if (!$doc) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Document not found']);
            }
            $this->redirect('documents');
            return;
        }

        $this->docModel->verify($id, $this->getCurrentUser()['id']);

        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'verify',
            'document',
            $id,
            "Verified document: {$doc['document_name']}"
        );

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Document verified']);
        }

        $this->setFlash('success', 'Document berhasil diverifikasi');
        $this->redirect('documents/' . $doc['crew_id']);
    }

    /**
     * Upload document file
     */
    private function uploadDocument($file, $crewId)
    {
        $uploadDir = 'uploads/documents/' . $crewId . '/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doc_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'path' => $filepath,
                'name' => $file['name'],
                'size' => $file['size'],
                'type' => $file['type']
            ];
        }

        return null;
    }

    /**
     * API: Get all crews with document counts (AJAX)
     */
    public function apiCrewList()
    {
        $this->requireAuth();
        
        $sql = "SELECT c.id, c.full_name, c.employee_id, 
                    COUNT(cd.id) AS doc_count
                FROM crews c
                LEFT JOIN crew_documents cd ON c.id = cd.crew_id
                GROUP BY c.id, c.full_name, c.employee_id
                HAVING doc_count > 0
                ORDER BY c.full_name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $crews = [];
        while ($row = $result->fetch_assoc()) {
            $crews[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'crews' => $crews]);
        exit;
    }

    /**
     * API: Get all documents for a specific crew (AJAX)
     */
    public function apiCrewDocs($crewId)
    {
        $this->requireAuth();
        
        $docs = $this->docModel->getByCrew((int)$crewId);
        $crew = $this->crewModel->find((int)$crewId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'crew' => $crew ? ['id' => $crew['id'], 'full_name' => $crew['full_name'], 'employee_id' => $crew['employee_id'] ?? ''] : null,
            'documents' => $docs ?: []
        ]);
        exit;
    }
}

