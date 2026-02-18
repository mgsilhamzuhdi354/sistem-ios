<div class="page-header">
    <h1><i class="fas fa-building"></i> Manage Departments</h1>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="fas fa-plus"></i> Add New Department
    </button>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($departments)): ?>
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <h3>No Departments Found</h3>
                <p>Start by adding a new department.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $dept): ?>
                            <tr>
                                <td><strong>
                                        <?= htmlspecialchars($dept['name']) ?>
                                    </strong></td>
                                <td>
                                    <?php if ($dept['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($dept['created_at'])) ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn edit"
                                            onclick="openEditModal(<?= $dept['id'] ?>, '<?= htmlspecialchars(addslashes($dept['name'])) ?>', <?= $dept['is_active'] ?>)"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= url('/master-admin/departments/delete/' . $dept['id']) ?>"
                                            method="POST" class="delete-form" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="action-btn delete" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this department?')">
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
            <h3>Add New Department</h3>
            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <form action="<?= url('/master-admin/departments/store') ?>" method="POST">
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Department Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Deck Department">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Department</h3>
            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <form id="editForm" action="" method="POST">
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Department Name <span class="required">*</span></label>
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
                <button type="submit" class="btn btn-primary">Update Department</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Simple Modal Styles since we don't have bootstrap js */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 0;
        border: 1px solid #888;
        width: 500px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        animation: slideDown 0.3s;
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #0A2463;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function openEditModal(id, name, isActive) {
        document.getElementById('editForm').action = "<?= url('/master-admin/departments/update/') ?>" + id;
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