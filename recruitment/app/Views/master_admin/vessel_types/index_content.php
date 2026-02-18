<div class="page-header">
    <h1><i class="fas fa-ship"></i> Manage Vessel Types</h1>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="fas fa-plus"></i> Add New Vessel Type
    </button>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($vesselTypes)): ?>
            <div class="empty-state">
                <i class="fas fa-ship"></i>
                <h3>No Vessel Types Found</h3>
                <p>Start by adding a new vessel type.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Vessel Type Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vesselTypes as $vt): ?>
                            <tr>
                                <td><strong>
                                        <?= htmlspecialchars($vt['name']) ?>
                                    </strong></td>
                                <td>
                                    <?php if ($vt['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn edit"
                                            onclick="openEditModal(<?= $vt['id'] ?>, '<?= htmlspecialchars(addslashes($vt['name'])) ?>', <?= $vt['is_active'] ?>)"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= url('/master-admin/vessel-types/delete/' . $vt['id']) ?>"
                                            method="POST" class="delete-form" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="action-btn delete" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this vessel type?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Vessel Type</h3>
            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <form action="<?= url('/master-admin/vessel-types/store') ?>" method="POST">
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Vessel Type Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Bulk Carrier">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Vessel Type</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Vessel Type</h3>
            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <form id="editForm" action="" method="POST">
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Vessel Type Name <span class="required">*</span></label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1">
                        Active
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Vessel Type</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function openEditModal(id, name, isActive) {
        document.getElementById('editForm').action = "<?= url('/master-admin/vessel-types/update/') ?>" + id;
        document.getElementById('editName').value = name;
        document.getElementById('editIsActive').checked = isActive == 1;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Close modal if clicked outside
    window.onclick = function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>