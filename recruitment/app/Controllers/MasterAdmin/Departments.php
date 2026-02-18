<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Departments Management Controller
 */
class Departments extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        // Only Master Admin (role 11)
        if (!isLoggedIn() || !isMasterAdmin()) {
            flash('error', 'Access denied');
            redirect(url('/admin/dashboard'));
        }
    }

    public function index()
    {
        $departments = $this->db->query("SELECT * FROM departments ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->view('master_admin/departments/index', [
            'departments' => $departments,
            'pageTitle' => 'Manage Departments'
        ]);
    }

    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect(url('/master-admin/departments'));
        }

        validate_csrf();

        $name = trim($this->input('name'));

        if (empty($name)) {
            flash('error', 'Department name is required');
            $this->redirect(url('/master-admin/departments'));
        }

        // Check if exists
        $stmt = $this->db->prepare("SELECT id FROM departments WHERE name = ?");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            flash('error', 'Department already exists');
        } else {
            $stmt = $this->db->prepare("INSERT INTO departments (name, is_active, created_at) VALUES (?, 1, NOW())");
            $stmt->bind_param('s', $name);
            if ($stmt->execute()) {
                flash('success', 'Department added successfully');
            } else {
                flash('error', 'Failed to add department');
            }
        }

        $this->redirect(url('/master-admin/departments'));
    }

    public function update($id)
    {
        if (!$this->isPost()) {
            $this->redirect(url('/master-admin/departments'));
        }

        validate_csrf();

        $name = trim($this->input('name'));
        $isActive = $this->input('is_active') ? 1 : 0;

        if (empty($name)) {
            flash('error', 'Department name is required');
            $this->redirect(url('/master-admin/departments'));
        }

        $stmt = $this->db->prepare("UPDATE departments SET name = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('sii', $name, $isActive, $id);

        if ($stmt->execute()) {
            flash('success', 'Department updated');
        } else {
            flash('error', 'Failed to update department');
        }

        $this->redirect(url('/master-admin/departments'));
    }

    public function delete($id)
    {
        if (!$this->isPost()) {
            $this->redirect(url('/master-admin/departments'));
        }

        validate_csrf();

        $stmt = $this->db->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            flash('success', 'Department deleted');
        } else {
            flash('error', 'Failed to delete department');
        }

        $this->redirect(url('/master-admin/departments'));
    }
}
