<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Vessel Types Management Controller
 */
class VesselTypes extends BaseController
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
        $vesselTypes = $this->db->query("SELECT * FROM vessel_types ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->view('master_admin/vessel_types/index', [
            'vesselTypes' => $vesselTypes,
            'pageTitle' => 'Manage Vessel Types'
        ]);
    }

    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect(url('/master-admin/vessel-types'));
        }

        validate_csrf();

        $name = trim($this->input('name'));

        if (empty($name)) {
            flash('error', 'Vessel Type name is required');
            $this->redirect(url('/master-admin/vessel-types'));
        }

        // Check if exists
        $stmt = $this->db->prepare("SELECT id FROM vessel_types WHERE name = ?");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            flash('error', 'Vessel Type already exists');
        } else {
            $stmt = $this->db->prepare("INSERT INTO vessel_types (name, is_active) VALUES (?, 1)");
            $stmt->bind_param('s', $name);
            if ($stmt->execute()) {
                flash('success', 'Vessel Type added successfully');
            } else {
                flash('error', 'Failed to add vessel type');
            }
        }

        $this->redirect(url('/master-admin/vessel-types'));
    }

    public function update($id)
    {
        if (!$this->isPost()) {
            $this->redirect(url('/master-admin/vessel-types'));
        }

        validate_csrf();

        $name = trim($this->input('name'));
        $isActive = $this->input('is_active') ? 1 : 0;

        if (empty($name)) {
            flash('error', 'Vessel Type name is required');
            $this->redirect(url('/master-admin/vessel-types'));
        }

        $stmt = $this->db->prepare("UPDATE vessel_types SET name = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param('sii', $name, $isActive, $id);

        if ($stmt->execute()) {
            flash('success', 'Vessel Type updated');
        } else {
            flash('error', 'Failed to update vessel type');
        }

        $this->redirect(url('/master-admin/vessel-types'));
    }

    public function delete($id)
    {
        if (!$this->isPost()) {
            $this->redirect(url('/master-admin/vessel-types'));
        }

        validate_csrf();

        $stmt = $this->db->prepare("DELETE FROM vessel_types WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            flash('success', 'Vessel Type deleted');
        } else {
            flash('error', 'Failed to delete vessel type');
        }

        $this->redirect(url('/master-admin/vessel-types'));
    }
}
