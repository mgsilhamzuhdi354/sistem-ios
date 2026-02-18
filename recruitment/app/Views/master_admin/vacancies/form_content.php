<div class="page-header">
    <div>
        <a href="<?= url('/admin/vacancies') ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to
            Vacancies</a>
        <h1>
            <?= isset($vacancy) ? 'Edit Vacancy' : 'Create New Vacancy' ?>
        </h1>
    </div>
</div>

<form action="<?= url('/admin/vacancies/' . (isset($vacancy) ? 'update/' . $vacancy['id'] : 'store')) ?>" method="POST"
    class="vacancy-form" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Basic Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Job Title <span class="required">*</span></label>
                        <input type="text" name="title" class="form-control"
                            value="<?= htmlspecialchars($vacancy['title'] ?? old('title')) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Department <span class="required">*</span></label>
                            <select name="department_id" class="form-control" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments ?? [] as $dept): ?>
                                    <option value="<?= $dept['id'] ?>" <?= ($vacancy['department_id'] ?? old('department_id')) == $dept['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Vessel Type</label>
                            <select name="vessel_type_id" class="form-control">
                                <option value="">Select Vessel Type</option>
                                <?php foreach ($vesselTypes ?? [] as $vt): ?>
                                    <option value="<?= $vt['id'] ?>" <?= ($vacancy['vessel_type_id'] ?? old('vessel_type_id')) == $vt['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($vt['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-ship" style="color:#0369a1;margin-right:5px;"></i>Ship Photo</label>
                        <div style="border:2px dashed #ddd;border-radius:12px;padding:20px;text-align:center;transition:all 0.3s;" id="shipDropZone">
                            <?php if (isset($vacancy['ship_photo']) && $vacancy['ship_photo']): ?>
                            <div id="photoPreviewContainer">
                                <img src="<?= asset($vacancy['ship_photo']) ?>" alt="Ship Photo" id="shipPhotoPreview" style="max-width:100%;max-height:200px;border-radius:10px;margin-bottom:10px;">
                                <p class="text-muted" style="margin:5px 0;">Current ship photo</p>
                            </div>
                            <?php else: ?>
                            <div id="photoPreviewContainer" style="display:none;">
                                <img src="" alt="Preview" id="shipPhotoPreview" style="max-width:100%;max-height:200px;border-radius:10px;margin-bottom:10px;">
                                <p class="text-muted" style="margin:5px 0;">Photo preview</p>
                            </div>
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#94a3b8;margin-bottom:8px;"></i>
                                <p style="color:#64748b;margin:0;">Click to upload or drag & drop</p>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="ship_photo" id="shipPhotoInput" class="form-control" accept="image/jpeg,image/jpg,image/png" style="margin-top:10px;">
                            <small class="form-text text-muted">Upload ship photo (JPG, PNG, max 5MB)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description <span class="required">*</span></label>
                        <textarea name="description" class="form-control" rows="6"
                            required><?= htmlspecialchars($vacancy['description'] ?? old('description')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Requirements</label>
                        <textarea name="requirements" class="form-control" rows="4"
                            placeholder="Enter each requirement on a new line"><?= htmlspecialchars($vacancy['requirements'] ?? old('requirements')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Required Certificates</label>
                        <textarea name="certificates_required" class="form-control" rows="3"
                            placeholder="Enter required certificates"><?= htmlspecialchars($vacancy['certificates_required'] ?? old('certificates_required')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Compensation</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Min Salary ($)</label>
                            <input type="number" name="salary_min" class="form-control"
                                value="<?= $vacancy['salary_min'] ?? old('salary_min') ?>">
                        </div>
                        <div class="form-group">
                            <label>Max Salary ($)</label>
                            <input type="number" name="salary_max" class="form-control"
                                value="<?= $vacancy['salary_max'] ?? old('salary_max') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Contract Duration (months)</label>
                        <input type="number" name="contract_duration_months" class="form-control"
                            value="<?= $vacancy['contract_duration_months'] ?? old('contract_duration_months') ?? 6 ?>">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" <?= ($vacancy['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>
                                >Draft</option>
                            <option value="published" <?= ($vacancy['status'] ?? '') === 'published' ? 'selected' : '' ?>
                                >Published</option>
                            <option value="closed" <?= ($vacancy['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Positions Available</label>
                        <input type="number" name="positions_available" class="form-control"
                            value="<?= $vacancy['positions_available'] ?? old('positions_available') ?? 1 ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label>Application Deadline</label>
                        <input type="date" name="deadline" class="form-control"
                            value="<?= $vacancy['deadline'] ?? old('deadline') ?>">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= !empty($vacancy['is_featured']) ? 'checked' : '' ?>>
                            Featured Vacancy
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i>
                    <?= isset($vacancy) ? 'Update Vacancy' : 'Create Vacancy' ?>
                </button>
                <a href="<?= url('/admin/vacancies') ?>" class="btn btn-outline btn-block">Cancel</a>
            </div>
        </div>
    </div>
</form>

<style>
    .back-link {
        color: #0A2463;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 25px;
    }

    .form-main .card,
    .form-sidebar .card {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
    }

    .form-group .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #0A2463;
        outline: none;
        box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.1);
    }

    textarea.form-control {
        resize: vertical;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .checkbox-label input {
        width: 18px;
        height: 18px;
    }

    .form-actions {
        margin-top: 20px;
    }

    .btn-block {
        width: 100%;
        margin-bottom: 10px;
    }

    @media (max-width: 992px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    #shipDropZone:hover {
        border-color: #0369a1;
        background: #f0f9ff;
    }
</style>

<script>
document.getElementById('shipPhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('shipPhotoPreview').src = ev.target.result;
            document.getElementById('photoPreviewContainer').style.display = 'block';
            const placeholder = document.getElementById('uploadPlaceholder');
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>