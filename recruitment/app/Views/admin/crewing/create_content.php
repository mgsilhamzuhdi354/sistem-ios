<div class="page-header">
    <a href="<?= url('/admin/crewing') ?>" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <h1>Add Crewing Staff</h1>
</div>

<?php if (!empty($_SESSION['errors'])): ?>
<div class="alert alert-danger">
    <ul>
        <?php foreach ($_SESSION['errors'] as $error): ?>
        <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php unset($_SESSION['errors']); endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/crewing/store') ?>">
            <?= csrf_field() ?>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-control" required
                           value="<?= old('full_name') ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" class="form-control"
                           value="<?= old('employee_id') ?>" placeholder="e.g. CRW001">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" required
                           value="<?= old('email') ?>">
                </div>
                
                <div class="form-group col-md-6">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?= old('phone') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" required
                           minlength="6" placeholder="Minimum 6 characters">
                </div>
                
                <div class="form-group col-md-6">
                    <label>Max Applications</label>
                    <input type="number" name="max_applications" class="form-control"
                           value="<?= old('max_applications', 50) ?>" min="1" max="200">
                    <small class="text-muted">Maximum number of active applications this staff can handle</small>
                </div>
            </div>
            
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" class="form-control"
                       value="<?= old('specialization') ?>" placeholder="e.g. Deck Officers, Engine Crew">
            </div>
            
            <div class="form-group">
                <label>Departments</label>
                <div class="checkbox-group">
                    <?php foreach ($departments as $dept): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="department_ids[]" value="<?= $dept['id'] ?>">
                        <?= htmlspecialchars($dept['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted">Leave empty for all departments</small>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_pic" value="1" <?= old('is_pic') ? 'checked' : '' ?>>
                    <strong>Person In Charge (PIC)</strong>
                    <small>PIC can view team workload and manage other crewing staff assignments</small>
                </label>
            </div>
            
            <div class="form-actions">
                <a href="<?= url('/admin/crewing') ?>" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Crewing Staff
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 0;
}

.col-md-6 {
    flex: 1;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
}

.required {
    color: #e74c3c;
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

.checkbox-label small {
    display: block;
    color: #666;
    margin-left: 26px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.text-muted {
    color: #666;
    font-size: 12px;
}
</style>
