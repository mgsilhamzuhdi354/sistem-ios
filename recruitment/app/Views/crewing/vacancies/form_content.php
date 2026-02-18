<!-- Buat/Edit Lowongan - Crewing -->
<div class="welcome-banner" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3b82f6 100%);">
    <div class="welcome-content">
        <div class="welcome-icon"><i class="fas fa-<?= isset($vacancy) ? 'edit' : 'plus-circle' ?>"></i></div>
        <div class="welcome-text">
            <h1><?= isset($vacancy) ? (getCurrentLanguage() === 'en' ? 'Edit Vacancy' : 'Edit Lowongan') : (getCurrentLanguage() === 'en' ? 'Create New Vacancy' : 'Buat Lowongan Baru') ?></h1>
            <p><?= isset($vacancy) ? (getCurrentLanguage() === 'en' ? 'Update vacancy information' : 'Update informasi lowongan kerja') : (getCurrentLanguage() === 'en' ? 'Create a new job vacancy to share with candidates' : 'Buat lowongan kerja baru untuk dibagikan ke kandidat') ?></p>
        </div>
    </div>
</div>

<div style="margin-bottom: 1rem;">
    <a href="<?= url('/crewing/vacancies') ?>" class="back-link-crewing">
        <i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back to Vacancy List' : 'Kembali ke Daftar Lowongan' ?>
    </a>
</div>

<form action="<?= url(isset($vacancy) ? '/crewing/vacancies/update/' . $vacancy['id'] : '/crewing/vacancies/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="form-grid-crewing">
        <!-- Main Form -->
        <div class="form-main-col">
            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-info-circle"></i> <?= getCurrentLanguage() === 'en' ? 'Basic Information' : 'Informasi Dasar' ?></div>
                <div class="form-card-body">
                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Position Title' : 'Judul Posisi' ?> <span class="req">*</span></label>
                        <input type="text" name="title" class="input-c" placeholder="e.g. Chief Engineer, AB Seaman" value="<?= htmlspecialchars($vacancy['title'] ?? old('title')) ?>" required>
                    </div>

                    <div class="form-row-c">
                        <div class="form-group-c">
                            <label><?= getCurrentLanguage() === 'en' ? 'Department' : 'Departemen' ?> <span class="req">*</span></label>
                            <select name="department_id" class="input-c" required>
                                <option value=""><?= getCurrentLanguage() === 'en' ? 'Select Department' : 'Pilih Departemen' ?></option>
                                <?php foreach ($departments ?? [] as $dept): ?>
                                    <option value="<?= $dept['id'] ?>" <?= ($vacancy['department_id'] ?? old('department_id')) == $dept['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group-c">
                            <label><?= getCurrentLanguage() === 'en' ? 'Vessel Type' : 'Jenis Kapal' ?></label>
                            <select name="vessel_type_id" class="input-c">
                                <option value=""><?= getCurrentLanguage() === 'en' ? 'Select Vessel Type' : 'Pilih Jenis Kapal' ?></option>
                                <?php foreach ($vesselTypes ?? [] as $vt): ?>
                                    <option value="<?= $vt['id'] ?>" <?= ($vacancy['vessel_type_id'] ?? old('vessel_type_id')) == $vt['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($vt['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Ship Photo' : 'Foto Kapal' ?></label>
                        <div class="photo-upload-area" id="photoUploadArea">
                            <div id="photoPreviewContainer">
                                <?php if (isset($vacancy['ship_photo']) && $vacancy['ship_photo']): ?>
                                <img src="<?= asset($vacancy['ship_photo']) ?>" alt="Foto Kapal" id="shipPhotoPreview" style="max-width:100%;max-height:200px;border-radius:12px;margin-bottom:10px;">
                                <p id="existingPhotoLabel" style="font-size:0.8rem;color:#6b7280;margin-bottom:10px;"><?= getCurrentLanguage() === 'en' ? 'Current photo (upload new to replace)' : 'Foto saat ini (upload baru untuk mengganti)' ?></p>
                                <?php else: ?>
                                <img src="" alt="Preview" id="shipPhotoPreview" style="max-width:100%;max-height:200px;border-radius:12px;margin-bottom:10px;display:none;">
                                <?php endif; ?>
                            </div>
                            <input type="file" name="ship_photo" id="shipPhotoInput" class="input-c" accept="image/jpeg,image/png,image/webp">
                            <small style="color:#94a3b8;"><?= getCurrentLanguage() === 'en' ? 'JPG, PNG, or WebP. Max 5MB.' : 'JPG, PNG, atau WebP. Maks 5MB.' ?></small>
                        </div>
                    </div>

                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Job Description' : 'Deskripsi Pekerjaan' ?> <span class="req">*</span></label>
                        <textarea name="description" class="input-c" rows="5" placeholder="<?= getCurrentLanguage() === 'en' ? 'Describe duties and responsibilities...' : 'Jelaskan tugas dan tanggung jawab...' ?>" required><?= htmlspecialchars($vacancy['description'] ?? old('description')) ?></textarea>
                    </div>

                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Requirements' : 'Persyaratan' ?></label>
                        <textarea name="requirements" class="input-c" rows="4" placeholder="<?= getCurrentLanguage() === 'en' ? 'Enter each requirement on a new line...' : 'Masukkan setiap persyaratan di baris baru...' ?>"><?= htmlspecialchars($vacancy['requirements'] ?? old('requirements')) ?></textarea>
                    </div>

                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Required Certificates' : 'Sertifikat Dibutuhkan' ?></label>
                        <textarea name="required_certificates" class="input-c" rows="3" placeholder="<?= getCurrentLanguage() === 'en' ? 'Enter each certificate on a new line...' : 'Masukkan setiap sertifikat di baris baru...' ?>"><?php 
                            $certs = $vacancy['required_certificates'] ?? old('required_certificates');
                            if ($certs && is_string($certs) && substr($certs, 0, 1) === '[') {
                                $certsArray = json_decode($certs, true);
                                echo htmlspecialchars(implode("\n", $certsArray));
                            } else {
                                echo htmlspecialchars($certs);
                            }
                        ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="form-side-col">
            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-dollar-sign"></i> <?= getCurrentLanguage() === 'en' ? 'Compensation' : 'Kompensasi' ?></div>
                <div class="form-card-body">
                    <div class="form-row-c">
                        <div class="form-group-c">
                            <label><?= getCurrentLanguage() === 'en' ? 'Min Salary ($)' : 'Gaji Min ($)' ?></label>
                            <input type="number" name="salary_min" class="input-c" value="<?= $vacancy['salary_min'] ?? old('salary_min') ?>" placeholder="0">
                        </div>
                        <div class="form-group-c">
                            <label><?= getCurrentLanguage() === 'en' ? 'Max Salary ($)' : 'Gaji Max ($)' ?></label>
                            <input type="number" name="salary_max" class="input-c" value="<?= $vacancy['salary_max'] ?? old('salary_max') ?>" placeholder="0">
                        </div>
                    </div>
                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Contract Duration (months)' : 'Durasi Kontrak (bulan)' ?></label>
                        <input type="number" name="contract_duration_months" class="input-c" value="<?= $vacancy['contract_duration_months'] ?? old('contract_duration_months') ?? 6 ?>">
                    </div>
                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Joining Date' : 'Tanggal Bergabung' ?></label>
                        <input type="date" name="joining_date" class="input-c" value="<?= $vacancy['joining_date'] ?? old('joining_date') ?>">
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-cog"></i> <?= getCurrentLanguage() === 'en' ? 'Settings' : 'Pengaturan' ?></div>
                <div class="form-card-body">
                    <div class="form-group-c">
                        <label>Status</label>
                        <select name="status" class="input-c">
                            <option value="published" <?= ($vacancy['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($vacancy['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Application Deadline' : 'Deadline Lamaran' ?></label>
                        <input type="date" name="application_deadline" class="input-c" value="<?= $vacancy['application_deadline'] ?? old('application_deadline') ?>" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group-c" style="margin-top:0.5rem;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_featured" value="1" style="width:18px;height:18px;" <?= !empty($vacancy['is_featured']) ? 'checked' : '' ?>>
                            Featured Vacancy
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-create-vacancy">
                <i class="fas fa-save"></i> <?= isset($vacancy) ? (getCurrentLanguage() === 'en' ? 'Update Vacancy' : 'Update Lowongan') : (getCurrentLanguage() === 'en' ? 'Save Vacancy' : 'Simpan Lowongan') ?>
            </button>
            <a href="<?= url('/crewing/vacancies') ?>" class="btn-cancel-vacancy"><?= getCurrentLanguage() === 'en' ? 'Cancel' : 'Batal' ?></a>
        </div>
    </div>
</form>

<style>
.back-link-crewing {
    color: #6366f1;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}
.back-link-crewing:hover { color: #4f46e5; }

.form-grid-crewing {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.5rem;
}

.form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.form-card-header {
    padding: 1rem 1.5rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-card-header i { color: #6366f1; }
.form-card-body { padding: 1.5rem; }

.form-group-c { margin-bottom: 1.25rem; }
.form-group-c label {
    display: block;
    font-size: 0.88rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.4rem;
}
.form-group-c .req { color: #ef4444; }

.input-c {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: inherit;
    transition: all 0.2s;
    outline: none;
    box-sizing: border-box;
}
.input-c:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}
textarea.input-c { resize: vertical; }

.form-row-c { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.btn-create-vacancy {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    box-shadow: 0 4px 15px rgba(99,102,241,0.3);
    transition: all 0.3s;
}
.btn-create-vacancy:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99,102,241,0.4);
}

.btn-cancel-vacancy {
    display: block;
    text-align: center;
    padding: 12px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    margin-top: 0.75rem;
}
.btn-cancel-vacancy:hover { color: #374151; }

.photo-upload-area {
    padding: 1rem;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    text-align: center;
}

@media (max-width: 992px) {
    .form-grid-crewing { grid-template-columns: 1fr; }
}
</style>

<script>
document.getElementById('shipPhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) { 
            alert('<?= getCurrentLanguage() === 'en' ? 'File too large. Max 5MB.' : 'File terlalu besar. Maks 5MB.' ?>'); 
            this.value = ''; 
            return; 
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('shipPhotoPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            
            // Hide existing photo label if present
            const existingLabel = document.getElementById('existingPhotoLabel');
            if (existingLabel) {
                existingLabel.textContent = '<?= getCurrentLanguage() === 'en' ? 'New photo (will replace old photo)' : 'Foto baru (akan mengganti foto lama)' ?>';
                existingLabel.style.color = '#059669';
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
