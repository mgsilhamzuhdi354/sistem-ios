<?php
/**
 * PT Indo Ocean - ERP System
 * Rank Management Controller
 */

namespace App\Controllers;

use App\Models\RankModel;

class Ranks extends BaseController
{
    private $rankModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->rankModel = new RankModel($this->db);
    }
    
    /**
     * List all ranks
     */
    public function index()
    {
        $page = (int)$this->input('page', 1);
        $filters = [
            'search' => $this->input('search'),
            'department' => $this->input('department'),
        ];
        
        $data = [
            'title' => 'Master Pangkat (Rank Management)',
            'ranks' => $this->rankModel->getList($filters, $page, 50), // Show more per page
            'total' => $this->rankModel->countList($filters),
            'page' => $page,
            'perPage' => 50,
            'filters' => $filters,
            'departments' => ['Deck', 'Engine', 'Galley', 'Hotel', 'Other'],
            'flash' => $this->getFlash()
        ];
        
        // Check UI mode
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $viewFile = $uiMode === 'modern' ? 'ranks/index_modern' : 'ranks/index';
        
        return $this->view($viewFile, $data);
    }
    
    /**
     * Create new rank form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Pangkat Baru',
            'currentPage' => 'ranks',
            'departments' => ['Deck', 'Engine', 'Galley', 'Hotel', 'Other'],
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'ranks/form_modern' : 'ranks/form';

        return $this->view($view, $data);
    }
    
    /**
     * Store new rank
     */
    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect('ranks');
        }
        
        $data = [
            'name' => $this->input('name'),
            'code' => $this->input('code'), // Optional short code
            'department' => $this->input('department'),
            'level' => (int)$this->input('level', 99), // Sort order
            'is_officer' => $this->input('is_officer') ? 1 : 0,
            'description' => $this->input('description'),
            'is_active' => 1
        ];
        
        try {
            $this->rankModel->insert($data);
            $this->setFlash('success', 'Pangkat berhasil ditambahkan');
            $this->redirect('ranks');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Gagal menambah pangkat: ' . $e->getMessage());
            $this->redirect('ranks/create');
        }
    }
    
    /**
     * Edit rank form
     */
    public function edit($id)
    {
        $rank = $this->rankModel->find($id);
        if (!$rank) {
            $this->setFlash('error', 'Pangkat tidak ditemukan');
            $this->redirect('ranks');
        }
        
        $data = [
            'title' => 'Edit Pangkat - ' . $rank['name'],
            'currentPage' => 'ranks',
            'rank' => $rank,
            'departments' => ['Deck', 'Engine', 'Galley', 'Hotel', 'Other'],
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'ranks/form_modern' : 'ranks/form';

        return $this->view($view, $data);
    }
    
    /**
     * Update rank
     */
    public function update($id)
    {
        if (!$this->isPost()) {
            $this->redirect('ranks');
        }
        
        $data = [
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'department' => $this->input('department'),
            'level' => (int)$this->input('level'),
            'is_officer' => $this->input('is_officer') ? 1 : 0,
            'description' => $this->input('description'),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];
        
        try {
            $this->rankModel->update($id, $data);
            $this->setFlash('success', 'Pangkat berhasil diperbarui');
            $this->redirect('ranks');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Gagal update pangkat: ' . $e->getMessage());
            $this->redirect("ranks/edit/$id");
        }
    }
    
    /**
     * Delete rank (Soft delete usually, but hard delete if unused)
     */
    public function delete($id)
    {
        // Check usage first
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM contracts WHERE rank_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $count = $result['count'] ?? 0;
        
        if ($count > 0) {
            $this->setFlash('error', "Tidak bisa menghapus pangkat ini karena sedang digunakan oleh $count kontrak. Silakan non-aktifkan saja.");
        } else {
            $this->rankModel->delete($id);
            $this->setFlash('success', 'Pangkat dihapus');
        }
        
        $this->redirect('ranks');
    }
}
