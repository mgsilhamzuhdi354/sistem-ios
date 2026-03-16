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
                            <div class="input-with-add">
                                <select name="department_id" id="departmentSelect" class="input-c" required>
                                    <option value=""><?= getCurrentLanguage() === 'en' ? 'Select Department' : 'Pilih Departemen' ?></option>
                                    <?php foreach ($departments ?? [] as $dept): ?>
                                        <option value="<?= $dept['id'] ?>" <?= ($vacancy['department_id'] ?? old('department_id')) == $dept['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn-quick-add" onclick="openAddModal('department')" title="<?= getCurrentLanguage() === 'en' ? 'Add New Department' : 'Tambah Departemen Baru' ?>">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group-c">
                            <label><?= getCurrentLanguage() === 'en' ? 'Vessel Type' : 'Jenis Kapal' ?></label>
                            <div class="input-with-add">
                                <select name="vessel_type_id" id="vesselTypeSelect" class="input-c">
                                    <option value=""><?= getCurrentLanguage() === 'en' ? 'Select Vessel Type' : 'Pilih Jenis Kapal' ?></option>
                                    <?php foreach ($vesselTypes ?? [] as $vt): ?>
                                        <option value="<?= $vt['id'] ?>" <?= ($vacancy['vessel_type_id'] ?? old('vessel_type_id')) == $vt['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($vt['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn-quick-add" onclick="openAddModal('vessel')" title="<?= getCurrentLanguage() === 'en' ? 'Add New Vessel Type' : 'Tambah Jenis Kapal Baru' ?>">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
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
                <div class="form-card-header"><i class="fas fa-dollar-sign"></i> Salary</div>
                <div class="form-card-body">
                    <div class="form-group-c">
                        <label><?= getCurrentLanguage() === 'en' ? 'Currency' : 'Mata Uang' ?></label>
                        <select name="salary_currency" id="salaryCurrency" class="input-c">
                            <?php $currentCurrency = $vacancy['salary_currency'] ?? old('salary_currency') ?? 'USD'; ?>
                            <option value="USD" <?= $currentCurrency === 'USD' ? 'selected' : '' ?>>🇺🇸 USD - US Dollar</option>
                            <option value="SGD" <?= $currentCurrency === 'SGD' ? 'selected' : '' ?>>🇸🇬 SGD - Singapore Dollar</option>
                            <option value="MYR" <?= $currentCurrency === 'MYR' ? 'selected' : '' ?>>🇲🇾 MYR - Malaysian Ringgit</option>
                            <option value="IDR" <?= $currentCurrency === 'IDR' ? 'selected' : '' ?>>🇮🇩 IDR - Indonesian Rupiah</option>
                        </select>
                    </div>
                    <div class="form-row-c">
                        <div class="form-group-c">
                            <label id="salaryMinLabel"><?= getCurrentLanguage() === 'en' ? 'Min Salary' : 'Gaji Min' ?> (<span class="currency-symbol"><?= $currentCurrency ?></span>)</label>
                            <input type="number" name="salary_min" class="input-c" value="<?= $vacancy['salary_min'] ?? old('salary_min') ?>" placeholder="0">
                        </div>
                        <div class="form-group-c">
                            <label id="salaryMaxLabel"><?= getCurrentLanguage() === 'en' ? 'Max Salary' : 'Gaji Max' ?> (<span class="currency-symbol"><?= $currentCurrency ?></span>)</label>
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

/* Quick Add Button */
.input-with-add {
    display: flex;
    gap: 8px;
    align-items: stretch;
}
.input-with-add .input-c {
    flex: 1;
}
.btn-quick-add {
    width: 42px;
    min-width: 42px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    background: white;
    color: #6366f1;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-quick-add:hover {
    background: #6366f1;
    color: white;
    border-color: #6366f1;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99,102,241,0.3);
}

/* Quick Add Modal */
.quick-add-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}
.quick-add-overlay.active { display: flex; }
.quick-add-modal {
    background: white;
    border-radius: 16px;
    width: 420px;
    max-width: 90vw;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: modalSlideIn 0.3s ease;
}
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.quick-add-modal-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.quick-add-modal-header h3 { margin: 0; font-size: 1rem; font-weight: 700; }
.quick-add-modal-close {
    background: none; border: none; color: white; font-size: 1.5rem;
    cursor: pointer; opacity: 0.8; transition: opacity 0.2s;
    line-height: 1;
}
.quick-add-modal-close:hover { opacity: 1; }
.quick-add-modal-body { padding: 1.5rem; }
.quick-add-modal-body .form-group-c { margin-bottom: 1rem; }
.quick-add-modal-body .form-group-c:last-child { margin-bottom: 0; }
.quick-add-actions {
    display: flex; gap: 0.75rem; justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}
.btn-qa-cancel {
    padding: 0.6rem 1.25rem; border: 2px solid #e5e7eb; border-radius: 10px;
    background: white; color: #6b7280; font-weight: 600; cursor: pointer;
    font-family: inherit; font-size: 0.9rem; transition: all 0.2s;
}
.btn-qa-cancel:hover { background: #f3f4f6; }
.btn-qa-save {
    padding: 0.6rem 1.5rem; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;
    font-weight: 700; cursor: pointer; font-family: inherit; font-size: 0.9rem;
    transition: all 0.3s; display: flex; align-items: center; gap: 6px;
}
.btn-qa-save:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(99,102,241,0.3); }
.btn-qa-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
.qa-success-msg {
    display: none;
    padding: 0.75rem;
    background: #d1fae5;
    color: #059669;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
    margin-top: 0.75rem;
}

/* Existing Items List */
.qa-section-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #94a3b8;
    margin-bottom: 0.5rem;
}
.qa-existing-list {
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #f8fafc;
}
.qa-existing-list::-webkit-scrollbar { width: 6px; }
.qa-existing-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.qa-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 0.85rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}
.qa-item:last-child { border-bottom: none; }
.qa-item:hover { background: #f0f4ff; }
.qa-item-name {
    font-size: 0.9rem;
    color: #334155;
    font-weight: 500;
    flex: 1;
}
.qa-item-delete {
    width: 30px; height: 30px;
    border: none; border-radius: 8px;
    background: transparent;
    color: #cbd5e1;
    cursor: pointer;
    font-size: 0.8rem;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}
.qa-item-delete:hover {
    background: #fef2f2;
    color: #ef4444;
}
.qa-empty {
    padding: 1.5rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.85rem;
}
.qa-loading {
    padding: 1.5rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.85rem;
}
.qa-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 1rem 0;
}
.qa-add-row {
    display: flex;
    gap: 8px;
    align-items: stretch;
}
.qa-add-row .input-c { flex: 1; }
.qa-add-row .btn-qa-save {
    padding: 0.6rem 1rem;
    white-space: nowrap;
}
.qa-delete-confirm {
    display: none;
    padding: 0.75rem;
    background: #fef2f2;
    color: #dc2626;
    border-radius: 10px;
    font-size: 0.85rem;
    text-align: center;
    margin-top: 0.5rem;
}
.qa-delete-confirm button {
    margin: 0 4px;
    padding: 4px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.8rem;
}
.qa-delete-confirm .btn-confirm-yes {
    background: #ef4444; color: white;
}
.qa-delete-confirm .btn-confirm-no {
    background: #e5e7eb; color: #374151;
}

@media (max-width: 992px) {
    .form-grid-crewing { grid-template-columns: 1fr; }
}
</style>

<!-- Manage Modal (Add & Delete) -->
<div id="quickAddOverlay" class="quick-add-overlay" onclick="if(event.target===this) closeAddModal()">
    <div class="quick-add-modal">
        <div class="quick-add-modal-header">
            <h3 id="quickAddTitle"></h3>
            <button class="quick-add-modal-close" onclick="closeAddModal()">&times;</button>
        </div>
        <div class="quick-add-modal-body">
            <!-- Existing Items List -->
            <div class="qa-section-label"><?= getCurrentLanguage() === 'en' ? 'Existing Items' : 'Data Yang Ada' ?></div>
            <div id="qaExistingList" class="qa-existing-list">
                <div class="qa-loading"><i class="fas fa-spinner fa-spin"></i> <?= getCurrentLanguage() === 'en' ? 'Loading...' : 'Memuat...' ?></div>
            </div>

            <div class="qa-divider"></div>

            <!-- Add New -->
            <div class="qa-section-label"><?= getCurrentLanguage() === 'en' ? 'Add New' : 'Tambah Baru' ?></div>
            <div class="qa-add-row">
                <input type="text" id="quickAddName" class="input-c" placeholder="" required>
                <button type="button" class="btn-qa-save" id="quickAddSaveBtn" onclick="saveQuickAdd()">
                    <i class="fas fa-plus"></i> <?= getCurrentLanguage() === 'en' ? 'Add' : 'Tambah' ?>
                </button>
            </div>
            <div id="quickAddSuccessMsg" class="qa-success-msg">
                <i class="fas fa-check-circle"></i> <span></span>
            </div>
        </div>
        <div class="quick-add-actions">
            <button type="button" class="btn-qa-cancel" onclick="closeAddModal()"><?= getCurrentLanguage() === 'en' ? 'Close' : 'Tutup' ?></button>
        </div>
    </div>
</div>

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

<script>
// Currency selector - update salary labels dynamically
document.getElementById('salaryCurrency').addEventListener('change', function() {
    var symbols = document.querySelectorAll('.currency-symbol');
    symbols.forEach(function(el) {
        el.textContent = this.value;
    }.bind(this));
});
</script>

<script>
// Manage Department / Vessel Type (Add & Delete)
var quickAddType = null;
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function openAddModal(type) {
    quickAddType = type;
    var overlay = document.getElementById('quickAddOverlay');
    var title = document.getElementById('quickAddTitle');
    var input = document.getElementById('quickAddName');
    var successMsg = document.getElementById('quickAddSuccessMsg');

    successMsg.style.display = 'none';
    input.value = '';

    if (type === 'department') {
        title.textContent = '<?= getCurrentLanguage() === "en" ? "Manage Departments" : "Kelola Departemen" ?>';
        input.placeholder = '<?= getCurrentLanguage() === "en" ? "e.g. Deck, Engine, Galley" : "contoh: Deck, Engine, Galley" ?>';
    } else {
        title.textContent = '<?= getCurrentLanguage() === "en" ? "Manage Vessel Types" : "Kelola Jenis Kapal" ?>';
        input.placeholder = '<?= getCurrentLanguage() === "en" ? "e.g. Bulk Carrier, Tanker" : "contoh: Bulk Carrier, Tanker" ?>';
    }

    overlay.classList.add('active');
    loadExistingItems();
    setTimeout(function() { input.focus(); }, 300);
}

function closeAddModal() {
    document.getElementById('quickAddOverlay').classList.remove('active');
    quickAddType = null;
}

function loadExistingItems() {
    var listEl = document.getElementById('qaExistingList');
    listEl.innerHTML = '<div class="qa-loading"><i class="fas fa-spinner fa-spin"></i> <?= getCurrentLanguage() === "en" ? "Loading..." : "Memuat..." ?></div>';

    var endpoint = quickAddType === 'department'
        ? '<?= url("/crewing/vacancies/list-departments") ?>'
        : '<?= url("/crewing/vacancies/list-vessel-types") ?>';

    fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (!data.items || data.items.length === 0) {
            listEl.innerHTML = '<div class="qa-empty"><i class="fas fa-inbox"></i> <?= getCurrentLanguage() === "en" ? "No items yet" : "Belum ada data" ?></div>';
            return;
        }
        var html = '';
        data.items.forEach(function(item) {
            html += '<div class="qa-item" data-id="' + item.id + '">' +
                '<span class="qa-item-name">' + escapeHtml(item.name) + '</span>' +
                '<button type="button" class="qa-item-delete" onclick="deleteItem(' + item.id + ', \'' + escapeHtml(item.name).replace(/'/g, "\\'") + '\')" title="<?= getCurrentLanguage() === "en" ? "Delete" : "Hapus" ?>">' +
                '<i class="fas fa-trash-alt"></i></button>' +
                '</div>';
        });
        listEl.innerHTML = html;
    })
    .catch(function() {
        listEl.innerHTML = '<div class="qa-empty" style="color:#ef4444;"><i class="fas fa-exclamation-circle"></i> <?= getCurrentLanguage() === "en" ? "Failed to load" : "Gagal memuat" ?></div>';
    });
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function deleteItem(id, name) {
    if (!confirm('<?= getCurrentLanguage() === "en" ? "Are you sure you want to delete" : "Apakah Anda yakin ingin menghapus" ?> "' + name + '"?')) {
        return;
    }

    var endpoint = quickAddType === 'department'
        ? '<?= url("/crewing/vacancies/delete-department/") ?>' + id
        : '<?= url("/crewing/vacancies/delete-vessel-type/") ?>' + id;

    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'csrf_token=' + encodeURIComponent(csrfToken)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            // Remove from list with animation
            var itemEl = document.querySelector('.qa-item[data-id="' + id + '"]');
            if (itemEl) {
                itemEl.style.transition = 'all 0.3s';
                itemEl.style.opacity = '0';
                itemEl.style.transform = 'translateX(20px)';
                setTimeout(function() {
                    itemEl.remove();
                    // Check if list is now empty
                    var listEl = document.getElementById('qaExistingList');
                    if (!listEl.querySelector('.qa-item')) {
                        listEl.innerHTML = '<div class="qa-empty"><i class="fas fa-inbox"></i> <?= getCurrentLanguage() === "en" ? "No items yet" : "Belum ada data" ?></div>';
                    }
                }, 300);
            }

            // Remove from dropdown select
            var select = quickAddType === 'department'
                ? document.getElementById('departmentSelect')
                : document.getElementById('vesselTypeSelect');
            var optToRemove = select.querySelector('option[value="' + id + '"]');
            if (optToRemove) optToRemove.remove();

            // Show success
            var successMsg = document.getElementById('quickAddSuccessMsg');
            successMsg.querySelector('span').textContent = data.message;
            successMsg.style.display = 'block';
            setTimeout(function() { successMsg.style.display = 'none'; }, 2500);
        } else {
            alert(data.message || '<?= getCurrentLanguage() === "en" ? "Failed to delete" : "Gagal menghapus" ?>');
        }
    })
    .catch(function(err) {
        console.error(err);
        alert('<?= getCurrentLanguage() === "en" ? "An error occurred" : "Terjadi kesalahan" ?>');
    });
}

function saveQuickAdd() {
    var name = document.getElementById('quickAddName').value.trim();
    if (!name) {
        document.getElementById('quickAddName').focus();
        return;
    }

    var saveBtn = document.getElementById('quickAddSaveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    var endpoint = quickAddType === 'department'
        ? '<?= url("/crewing/vacancies/add-department") ?>'
        : '<?= url("/crewing/vacancies/add-vessel-type") ?>';

    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'name=' + encodeURIComponent(name) + '&csrf_token=' + encodeURIComponent(csrfToken)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            // Add to dropdown and select it
            var select = quickAddType === 'department'
                ? document.getElementById('departmentSelect')
                : document.getElementById('vesselTypeSelect');

            var option = document.createElement('option');
            option.value = data.id;
            option.textContent = data.name;
            option.selected = true;
            select.appendChild(option);

            // Show success
            var successMsg = document.getElementById('quickAddSuccessMsg');
            successMsg.querySelector('span').textContent = data.message;
            successMsg.style.display = 'block';

            // Clear input
            document.getElementById('quickAddName').value = '';

            // Reload list to show the new item
            loadExistingItems();

            setTimeout(function() { successMsg.style.display = 'none'; }, 2500);
        } else {
            alert(data.message || '<?= getCurrentLanguage() === "en" ? "Failed to save" : "Gagal menyimpan" ?>');
        }
    })
    .catch(function(err) {
        console.error(err);
        alert('<?= getCurrentLanguage() === "en" ? "An error occurred" : "Terjadi kesalahan" ?>');
    })
    .finally(function() {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-plus"></i> <?= getCurrentLanguage() === "en" ? "Add" : "Tambah" ?>';
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && quickAddType) closeAddModal();
});

// Submit on Enter key in the input
document.getElementById('quickAddName').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); saveQuickAdd(); }
});
</script>
