<?php $isEdit = !empty($editMode); $e = $entry ?? []; ?>
<!-- Manual Entry Form - Crewing (Complete Data) -->
<div class="welcome-banner" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, <?= $isEdit ? '#f59e0b' : '#10b981' ?> 100%); color: white;">
    <div class="welcome-content">
        <div class="welcome-icon" style="color: white;"><i class="fas fa-<?= $isEdit ? 'edit' : 'user-plus' ?>"></i></div>
        <div class="welcome-text">
            <h1 style="color: white; margin: 0 0 0.5rem 0; font-size: 1.75rem;"><?= $isEdit ? (getCurrentLanguage() === 'en' ? 'Edit Applicant Data' : 'Edit Data Pelamar') : (getCurrentLanguage() === 'en' ? 'Manual Applicant Entry' : 'Input Data Pelamar Manual') ?></h1>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.95rem;"><?= $isEdit ? (getCurrentLanguage() === 'en' ? 'Update applicant data: ' : 'Perbarui data pelamar: ') . htmlspecialchars($e['full_name'] ?? '') : (getCurrentLanguage() === 'en' ? 'Enter complete data for walk-in or offline applicants' : 'Masukkan data lengkap pelamar walk-in atau offline') ?></p>
        </div>
    </div>
    <div class="welcome-date" style="color: rgba(255,255,255,0.95);">
        <?php if ($isEdit): ?>
        <a href="<?= url('/crewing/manual-entries/detail/' . $e['id']) ?>" style="color:white;text-decoration:none; padding: 0.5rem 1rem; background: rgba(255,255,255,0.15); border-radius: 8px; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 500;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"><i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back to Detail' : 'Kembali ke Detail' ?></a>
        <?php else: ?>
        <i class="fas fa-hand-point-right"></i> <span><?= getCurrentLanguage() === 'en' ? 'Walk-in / Offline Candidate' : 'Kandidat Walk-in / Offline' ?></span>
        <?php endif; ?>
    </div>
</div>

<?php if (flash('error')): ?>
    <div class="alert-box danger"><i class="fas fa-exclamation-circle"></i> <?= flash('error') ?></div>
<?php endif; ?>
<?php if (flash('success')): ?>
    <div class="alert-box success"><i class="fas fa-check-circle"></i> <?= flash('success') ?></div>
<?php endif; ?>

<!-- Progress Steps -->
<div class="form-steps">
    <div class="step active" data-step="1"><span class="step-num">1</span><span class="step-label"><?= getCurrentLanguage() === 'en' ? 'Position' : 'Posisi' ?></span></div>
    <div class="step-line"></div>
    <div class="step" data-step="2"><span class="step-num">2</span><span class="step-label"><?= getCurrentLanguage() === 'en' ? 'Personal' : 'Pribadi' ?></span></div>
    <div class="step-line"></div>
    <div class="step" data-step="3"><span class="step-num">3</span><span class="step-label"><?= getCurrentLanguage() === 'en' ? 'Documents' : 'Dokumen' ?></span></div>
    <div class="step-line"></div>
    <div class="step" data-step="4"><span class="step-num">4</span><span class="step-label"><?= getCurrentLanguage() === 'en' ? 'Physical & Emergency' : 'Fisik & Darurat' ?></span></div>
    <div class="step-line"></div>
    <div class="step" data-step="5"><span class="step-num">5</span><span class="step-label"><?= getCurrentLanguage() === 'en' ? 'Experience' : 'Pengalaman' ?></span></div>
</div>

<form method="POST" action="<?= $isEdit ? url('/crewing/manual-entries/update/' . $e['id']) : url('/crewing/manual-entry/submit') ?>" id="manualEntryForm" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <!-- STEP 1: Posisi Pekerjaan -->
    <div class="form-step-content active" id="step-1">
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon blue"><i class="fas fa-briefcase"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Job Position' : 'Posisi Pekerjaan' ?></h3></div>
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Select Vacancy' : 'Pilih Lowongan' ?> <span class="req">*</span></label>
                        <select name="vacancy_id" class="form-input" required>
                            <option value=""><?= getCurrentLanguage() === 'en' ? '-- Select vacancy --' : '-- Pilih lowongan --' ?></option>
                            <?php foreach ($vacancies as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= (isset($vacancy) && $vacancy['id'] == $v['id']) || ($isEdit && ($e['vacancy_id'] ?? '') == $v['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['title']) ?> - <?= htmlspecialchars($v['department_name'] ?? 'Umum') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Expected Salary (IDR)' : 'Gaji yang Diharapkan (IDR)' ?></label>
                        <input type="number" name="expected_salary" class="form-input" placeholder="e.g. 5000000" value="<?= htmlspecialchars($e['expected_salary'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Available Date' : 'Tanggal Tersedia' ?></label>
                        <input type="date" name="available_date" class="form-input" value="<?= htmlspecialchars($e['available_date'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Cover Letter / Notes' : 'Cover Letter / Catatan' ?></label>
                        <textarea name="cover_letter" class="form-input" rows="2" placeholder="<?= getCurrentLanguage() === 'en' ? 'Brief cover letter...' : 'Surat pengantar singkat...' ?>"><?= htmlspecialchars($e['cover_letter'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="step-nav"><button type="button" class="btn-next" onclick="goStep(2)"><?= getCurrentLanguage() === 'en' ? 'Next' : 'Selanjutnya' ?> <i class="fas fa-arrow-right"></i></button></div>
    </div>

    <!-- STEP 2: Informasi Pribadi -->
    <div class="form-step-content" id="step-2">
        <!-- Photo Upload Card (Prominent) -->
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon purple"><i class="fas fa-camera"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Seafarer Photo' : 'Foto Pelaut' ?></h3></div>
            <div class="form-card-body">
                <div class="photo-upload-section">
                    <div class="photo-preview-container">
                        <div class="photo-preview <?= ($isEdit && !empty($e['avatar'])) ? 'has-image' : '' ?>" id="photoPreview">
                            <?php if ($isEdit && !empty($e['avatar'])): ?>
                                <img src="<?= url('/' . $e['avatar']) ?>" alt="Current photo">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                                <span><?= getCurrentLanguage() === 'en' ? 'No photo yet' : 'Belum ada foto' ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="photo-upload-controls">
                        <label for="photoUpload" class="photo-upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span><?= ($isEdit && !empty($e['avatar'])) ? (getCurrentLanguage() === 'en' ? 'Change Seafarer Photo' : 'Ganti Foto Pelaut') : (getCurrentLanguage() === 'en' ? 'Select Seafarer Photo' : 'Pilih Foto Pelaut') ?></span>
                        </label>
                        <input type="file" id="photoUpload" name="doc_file[7]" accept="image/jpeg,image/png,image/jpg" style="display:none" onchange="previewPhoto(this)">
                        <p class="upload-hint" style="margin:0.75rem 0 0 0"><i class="fas fa-info-circle"></i> <?= getCurrentLanguage() === 'en' ? 'Format: JPG, PNG. Max 5MB. Passport or close-up photo.' : 'Format: JPG, PNG. Maks 5MB. Foto passport atau close-up wajah.' ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon purple"><i class="fas fa-user"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Personal Information' : 'Informasi Pribadi' ?></h3></div>
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Full Name' : 'Nama Lengkap' ?> <span class="req">*</span></label>
                        <input type="text" name="full_name" class="form-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'Name as per ID' : 'Nama sesuai KTP' ?>" required value="<?= htmlspecialchars($e['full_name'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="email@contoh.com" required value="<?= htmlspecialchars($e['email'] ?? '') ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Phone Number' : 'Nomor Telepon' ?> <span class="req">*</span></label>
                        <input type="tel" name="phone" class="form-input" placeholder="+62 xxx xxxx xxxx" required value="<?= htmlspecialchars($e['phone'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Gender' : 'Jenis Kelamin' ?></label>
                        <select name="gender" class="form-input"><option value=""><?= getCurrentLanguage() === 'en' ? 'Select...' : 'Pilih...' ?></option><option value="Male" <?= ($e['gender'] ?? '') === 'Male' ? 'selected' : '' ?>><?= getCurrentLanguage() === 'en' ? 'Male' : 'Laki-laki' ?></option><option value="Female" <?= ($e['gender'] ?? '') === 'Female' ? 'selected' : '' ?>><?= getCurrentLanguage() === 'en' ? 'Female' : 'Perempuan' ?></option></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Date of Birth' : 'Tanggal Lahir' ?></label>
                        <input type="date" name="date_of_birth" class="form-input" value="<?= htmlspecialchars($e['date_of_birth'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Place of Birth' : 'Tempat Lahir' ?></label>
                        <input type="text" name="place_of_birth" class="form-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'City of birth' : 'Kota kelahiran' ?>" value="<?= htmlspecialchars($e['place_of_birth'] ?? '') ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Nationality' : 'Kewarganegaraan' ?></label>
                        <input type="text" name="nationality" class="form-input" placeholder="Indonesia" value="<?= htmlspecialchars($e['nationality'] ?? 'Indonesia') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Blood Type' : 'Golongan Darah' ?></label>
                        <?php $bt = $e['blood_type'] ?? ''; ?>
                        <select name="blood_type" class="form-input"><option value=""><?= getCurrentLanguage() === 'en' ? 'Select...' : 'Pilih...' ?></option><option value="A" <?= $bt==='A'?'selected':'' ?>>A</option><option value="B" <?= $bt==='B'?'selected':'' ?>>B</option><option value="AB" <?= $bt==='AB'?'selected':'' ?>>AB</option><option value="O" <?= $bt==='O'?'selected':'' ?>>O</option></select></div>
                </div>
            </div>
        </div>
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon teal"><i class="fas fa-map-marker-alt"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Address' : 'Alamat' ?></h3></div>
            <div class="form-card-body">
                <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Full Address' : 'Alamat Lengkap' ?></label>
                    <textarea name="address" class="form-input" rows="2" placeholder="Jl. ..."><?= htmlspecialchars($e['address'] ?? '') ?></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'City' : 'Kota' ?></label>
                        <input type="text" name="city" class="form-input" placeholder="Jakarta, Surabaya..." value="<?= htmlspecialchars($e['city'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Country' : 'Negara' ?></label>
                        <input type="text" name="country" class="form-input" placeholder="Indonesia" value="<?= htmlspecialchars($e['country'] ?? 'Indonesia') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Postal Code' : 'Kode Pos' ?></label>
                        <input type="text" name="postal_code" class="form-input" placeholder="12345" value="<?= htmlspecialchars($e['postal_code'] ?? '') ?>"></div>
                </div>
            </div>
        </div>
        <div class="step-nav"><button type="button" class="btn-back" onclick="goStep(1)"><i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back' : 'Kembali' ?></button><button type="button" class="btn-next" onclick="goStep(3)"><?= getCurrentLanguage() === 'en' ? 'Next' : 'Selanjutnya' ?> <i class="fas fa-arrow-right"></i></button></div>
    </div>

    <!-- STEP 3: Dokumen -->
    <div class="form-step-content" id="step-3">
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon orange"><i class="fas fa-id-card"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Seafarer Documents' : 'Dokumen Pelaut' ?></h3></div>
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Seaman Book No.' : 'No. Buku Pelaut' ?></label>
                        <input type="text" name="seaman_book_no" class="form-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'Seaman book number' : 'Nomor buku pelaut' ?>" value="<?= htmlspecialchars($e['seaman_book_no'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Seaman Book Expiry' : 'Masa Berlaku Buku Pelaut' ?></label>
                        <input type="date" name="seaman_book_expiry" class="form-input" value="<?= htmlspecialchars($e['seaman_book_expiry'] ?? '') ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Passport No.' : 'No. Paspor' ?></label>
                        <input type="text" name="passport_no" class="form-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'Passport number' : 'Nomor paspor' ?>" value="<?= htmlspecialchars($e['passport_no'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Passport Expiry' : 'Masa Berlaku Paspor' ?></label>
                        <input type="date" name="passport_expiry" class="form-input" value="<?= htmlspecialchars($e['passport_expiry'] ?? '') ?>"></div>
                </div>
            </div>
        </div>

        <!-- Document Upload Section -->
        <div class="form-card" style="margin-top:1rem">
            <div class="form-card-header"><div class="form-card-icon teal"><i class="fas fa-cloud-upload-alt"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Upload Documents' : 'Upload Dokumen' ?></h3></div>
            <div class="form-card-body">
                <p class="upload-hint"><i class="fas fa-info-circle"></i> <?= getCurrentLanguage() === 'en' ? 'Format: PDF, JPG, PNG, DOC. Max 5MB per file.' : 'Format: PDF, JPG, PNG, DOC. Maks 5MB per file.' ?></p>
                <?php
                // Note: Type 7 (Foto) is handled separately in Step 2 photo upload section
                $docTypes = [
                    1 => ['name' => 'CV / Resume', 'icon' => 'fa-file-alt'],
                    2 => ['name' => 'Passport', 'icon' => 'fa-passport'],
                    3 => ['name' => getCurrentLanguage() === 'en' ? 'Seaman Book' : 'Buku Pelaut (Seaman Book)', 'icon' => 'fa-id-card'],
                    4 => ['name' => 'COC Certificate', 'icon' => 'fa-certificate'],
                    5 => ['name' => 'COP / STCW Certificates', 'icon' => 'fa-award'],
                    6 => ['name' => 'Medical Certificate', 'icon' => 'fa-heartbeat'],
                    8 => ['name' => getCurrentLanguage() === 'en' ? 'Other Certificates' : 'Sertifikat Lainnya', 'icon' => 'fa-folder-open']
                ];
                ?>
                <div class="upload-grid">
                <?php foreach ($docTypes as $typeId => $dt): ?>
                    <div class="upload-item">
                        <div class="upload-item-header">
                            <i class="fas <?= $dt['icon'] ?>"></i>
                            <span><?= $dt['name'] ?></span>
                        </div>
                        <input type="file" name="doc_file[<?= $typeId ?>]" class="form-input upload-file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <div class="form-row" style="margin-top:0.4rem">
                            <div class="form-group" style="flex:1"><input type="text" name="doc_number[<?= $typeId ?>]" class="form-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'Doc. Number' : 'No. Dokumen' ?>" style="font-size:0.8rem"></div>
                            <div class="form-group" style="flex:1"><input type="date" name="doc_expiry[<?= $typeId ?>]" class="form-input" title="<?= getCurrentLanguage() === 'en' ? 'Expiry Date' : 'Tanggal Kedaluwarsa' ?>" style="font-size:0.8rem"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="step-nav"><button type="button" class="btn-back" onclick="goStep(2)"><i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back' : 'Kembali' ?></button><button type="button" class="btn-next" onclick="goStep(4)"><?= getCurrentLanguage() === 'en' ? 'Next' : 'Selanjutnya' ?> <i class="fas fa-arrow-right"></i></button></div>
    </div>

    <!-- STEP 4: Fisik & Darurat -->
    <div class="form-step-content" id="step-4">
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon green"><i class="fas fa-heartbeat"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Physical Data' : 'Data Fisik' ?></h3></div>
            <div class="form-card-body">
                <div class="form-row four-cols">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Height (cm)' : 'Tinggi (cm)' ?></label>
                        <input type="number" name="height_cm" class="form-input" placeholder="170" value="<?= htmlspecialchars($e['height_cm'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Weight (kg)' : 'Berat (kg)' ?></label>
                        <input type="number" name="weight_kg" class="form-input" placeholder="70" value="<?= htmlspecialchars($e['weight_kg'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Shoe Size' : 'Ukuran Sepatu' ?></label>
                        <input type="text" name="shoe_size" class="form-input" placeholder="42" value="<?= htmlspecialchars($e['shoe_size'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Overall Size' : 'Ukuran Overall' ?></label>
                        <input type="text" name="overall_size" class="form-input" placeholder="L" value="<?= htmlspecialchars($e['overall_size'] ?? '') ?>"></div>
                </div>
            </div>
        </div>
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon red"><i class="fas fa-phone-alt"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Emergency Contact' : 'Kontak Darurat' ?></h3></div>
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Emergency Contact Name' : 'Nama Kontak Darurat' ?></label>
                        <input type="text" name="emergency_name" class="form-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'Full name' : 'Nama lengkap' ?>" value="<?= htmlspecialchars($e['emergency_name'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Emergency Phone' : 'No. Telepon Darurat' ?></label>
                        <input type="text" name="emergency_phone" class="form-input" placeholder="+62..." value="<?= htmlspecialchars($e['emergency_phone'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Relationship' : 'Hubungan' ?></label>
                        <?php $er = $e['emergency_relation'] ?? ''; ?>
                        <select name="emergency_relation" class="form-input"><option value=""><?= getCurrentLanguage() === 'en' ? 'Select...' : 'Pilih...' ?></option><option value="Suami/Istri" <?= $er==='Suami/Istri'?'selected':'' ?>><?= getCurrentLanguage() === 'en' ? 'Spouse' : 'Suami/Istri' ?></option><option value="Orang Tua" <?= $er==='Orang Tua'?'selected':'' ?>><?= getCurrentLanguage() === 'en' ? 'Parent' : 'Orang Tua' ?></option><option value="Saudara" <?= $er==='Saudara'?'selected':'' ?>><?= getCurrentLanguage() === 'en' ? 'Sibling' : 'Saudara' ?></option><option value="Anak" <?= $er==='Anak'?'selected':'' ?>><?= getCurrentLanguage() === 'en' ? 'Child' : 'Anak' ?></option><option value="Lainnya" <?= $er==='Lainnya'?'selected':'' ?>><?= getCurrentLanguage() === 'en' ? 'Other' : 'Lainnya' ?></option></select></div>
                </div>
            </div>
        </div>
        <div class="step-nav"><button type="button" class="btn-back" onclick="goStep(3)"><i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back' : 'Kembali' ?></button><button type="button" class="btn-next" onclick="goStep(5)"><?= getCurrentLanguage() === 'en' ? 'Next' : 'Selanjutnya' ?> <i class="fas fa-arrow-right"></i></button></div>
    </div>

    <!-- STEP 5: Pengalaman & Submit -->
    <div class="form-step-content" id="step-5">
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon purple"><i class="fas fa-ship"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Sea Service Experience' : 'Pengalaman Berlayar' ?></h3></div>
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Total Sea Service (months)' : 'Total Pengalaman Laut (bulan)' ?></label>
                        <input type="number" name="total_sea_service_months" class="form-input" placeholder="24" value="<?= htmlspecialchars($e['total_sea_service_months'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Last Rank/Position' : 'Jabatan/Rank Terakhir' ?></label>
                        <input type="text" name="last_rank" class="form-input" placeholder="AB, OS, Bosun, dll" value="<?= htmlspecialchars($e['last_rank'] ?? '') ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Last Vessel Name' : 'Nama Kapal Terakhir' ?></label>
                        <input type="text" name="last_vessel_name" class="form-input" placeholder="MV ..." value="<?= htmlspecialchars($e['last_vessel_name'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Vessel Type' : 'Jenis Kapal' ?></label>
                        <input type="text" name="last_vessel_type" class="form-input" placeholder="Tanker, Cargo, Tug..." value="<?= htmlspecialchars($e['last_vessel_type'] ?? '') ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Last Sign Off Date' : 'Tanggal Sign Off Terakhir' ?></label>
                        <input type="date" name="last_sign_off" class="form-input" value="<?= htmlspecialchars($e['last_sign_off'] ?? '') ?>"></div>
                    <div class="form-group"></div>
                </div>
            </div>
        </div>
        <div class="form-card">
            <div class="form-card-header"><div class="form-card-icon orange"><i class="fas fa-sticky-note"></i></div><h3><?= getCurrentLanguage() === 'en' ? 'Internal Notes' : 'Catatan Internal' ?></h3></div>
            <div class="form-card-body">
                <div class="form-group"><label class="form-label"><?= getCurrentLanguage() === 'en' ? 'Manual Entry Reason / Notes' : 'Alasan Input Manual / Catatan' ?></label>
                    <textarea name="notes" class="form-input" rows="3" placeholder="<?= getCurrentLanguage() === 'en' ? 'Walk-in, referral, important notes...' : 'Walk-in, referensi, catatan penting...' ?>"></textarea>
                    <small class="form-hint"><i class="fas fa-lock"></i> <?= getCurrentLanguage() === 'en' ? 'Internal only, not visible to candidate' : 'Hanya untuk internal, tidak terlihat oleh kandidat' ?></small></div>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-lightbulb"></i>
            <div><strong>Info:</strong> <?= getCurrentLanguage() === 'en' ? 'Candidate will automatically be <strong>assigned to you</strong> and get a system account. Fields marked with <span class="req">*</span> are required, the rest are optional.' : 'Kandidat akan otomatis <strong>ditugaskan kepada Anda</strong> dan mendapat akun sistem. Field bertanda <span class="req">*</span> wajib diisi, sisanya opsional.' ?></div>
        </div>

        <div class="step-nav">
            <button type="button" class="btn-back" onclick="goStep(4)"><i class="fas fa-arrow-left"></i> <?= getCurrentLanguage() === 'en' ? 'Back' : 'Kembali' ?></button>
            <button type="submit" class="btn-submit"><i class="fas fa-check-circle"></i> <?= $isEdit ? (getCurrentLanguage() === 'en' ? 'Update Applicant Data' : 'Update Data Pelamar') : (getCurrentLanguage() === 'en' ? 'Save Applicant Data' : 'Simpan Data Pelamar') ?></button>
        </div>
    </div>
</form>

<style>
/* Steps Indicator */
.form-steps { display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 1.5rem; padding: 1.25rem; background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.step { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 25px; transition: all 0.3s; cursor: pointer; }
.step.active { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
.step.completed { background: #dcfce7; color: #16a34a; }
.step-num { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; background: rgba(0,0,0,0.1); }
.step.active .step-num { background: rgba(255,255,255,0.3); }
.step.completed .step-num { background: #16a34a; color: white; }
.step-label { font-size: 0.8rem; font-weight: 600; }
.step-line { width: 30px; height: 2px; background: #e5e7eb; }

/* Form Step Content */
.form-step-content { display: none; animation: fadeSlide 0.4s ease; }
.form-step-content.active { display: block; }
@keyframes fadeSlide { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }

/* Cards */
.form-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.25rem; }
.form-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 0.75rem; }
.form-card-header h3 { margin: 0; font-size: 1rem; color: #1f2937; }
.form-card-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; min-width: 40px; }
.form-card-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.form-card-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.form-card-icon.teal { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.form-card-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.form-card-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.form-card-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
.form-card-body { padding: 1.5rem; }

/* Form Fields */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem; }
.form-row.four-cols { grid-template-columns: repeat(4, 1fr); }
.form-group { margin-bottom: 0.75rem; }
.form-label { display: block; font-weight: 600; font-size: 0.82rem; color: #374151; margin-bottom: 0.4rem; }
.req { color: #ef4444; }
.form-input { width: 100%; border: 2px solid #e5e7eb; border-radius: 10px; padding: 0.65rem 0.9rem; font-size: 0.9rem; font-family: inherit; transition: all 0.3s; background: #f9fafb; box-sizing: border-box; }
.form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); background: white; outline: none; }
.form-hint { color: #9ca3af; font-size: 0.78rem; margin-top: 0.3rem; display: block; }

/* Step Navigation */
.step-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; gap: 1rem; }
.btn-back { background: #f3f4f6; color: #374151; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; font-family: inherit; }
.btn-back:hover { background: #e5e7eb; }
.btn-next { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 0.75rem 2rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; margin-left: auto; font-family: inherit; }
.btn-next:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59,130,246,0.3); }
.btn-submit { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 0.85rem 2.5rem; border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.3s; font-family: inherit; box-shadow: 0 5px 20px rgba(16,185,129,0.3); }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16,185,129,0.4); }

/* Alert Box */
.alert-box { border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.9rem; }
.alert-box.danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.alert-box.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

/* Info Box */
.info-box { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 12px; padding: 1rem; display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem; color: #1e40af; font-size: 0.88rem; }
.info-box i { font-size: 1.1rem; margin-top: 0.1rem; color: #3b82f6; }

/* Upload */
.upload-hint { color: #6b7280; font-size: 0.82rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem; }
.upload-hint i { color: #3b82f6; }
.upload-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.upload-item { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; padding: 0.75rem 1rem; transition: all 0.2s; }
.upload-item:hover { border-color: #93c5fd; background: #eff6ff; }
.upload-item-header { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; font-size: 0.82rem; color: #1e3a5f; margin-bottom: 0.5rem; }
.upload-item-header i { color: #3b82f6; width: 18px; text-align: center; }
.upload-file-input { font-size: 0.8rem !important; padding: 0.4rem 0.6rem !important; }

/* Photo Upload Section */
.photo-upload-section { display: flex; gap: 1.5rem; align-items: center; }
.photo-preview-container { flex-shrink: 0; }
.photo-preview { width: 160px; height: 160px; border-radius: 12px; border: 3px dashed #d1d5db; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; background: #f9fafb; color: #9ca3af; overflow: hidden; position: relative; }
.photo-preview i { font-size: 3.5rem; color: #d1d5db; }
.photo-preview span { font-size: 0.8rem; font-weight: 500; }
.photo-preview img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 2; }
.photo-preview.has-image { border-color: #10b981; border-style: solid; }
.photo-upload-controls { flex: 1; }
.photo-upload-btn { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; }
.photo-upload-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(139,92,246,0.3); }
.photo-upload-btn i { font-size: 1.1rem; }

@media (max-width: 768px) {
    .form-row, .form-row.four-cols { grid-template-columns: 1fr; }
    .form-steps { flex-wrap: wrap; gap: 0.25rem; }
    .step-line { width: 15px; }
    .step-label { display: none; }
    .upload-grid { grid-template-columns: 1fr; }
    .photo-upload-section { flex-direction: column; text-align: center; }
    .photo-preview { margin: 0 auto; }
}
</style>

<script>
let currentStep = 1;
function goStep(n) {
    document.querySelectorAll('.form-step-content').forEach(el => el.classList.remove('active'));
    document.getElementById('step-' + n).classList.add('active');
    document.querySelectorAll('.step').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.remove('active', 'completed');
        if (s === n) el.classList.add('active');
        else if (s < n) el.classList.add('completed');
    });
    currentStep = n;
    // Scroll to form content area instead of top
    const formStepEl = document.getElementById('step-' + n);
    if (formStepEl) {
        formStepEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
document.querySelectorAll('.step').forEach(el => el.addEventListener('click', () => goStep(parseInt(el.dataset.step))));

// Handle form submit - show all steps temporarily so browser can validate required fields
document.getElementById('manualEntryForm').addEventListener('submit', function(e) {
    // Temporarily show all step contents for validation
    const steps = document.querySelectorAll('.form-step-content');
    steps.forEach(el => el.style.display = 'block');

    // Check validity - if invalid, find which step has the error and navigate there
    if (!this.checkValidity()) {
        e.preventDefault();
        // Find the first invalid field
        const firstInvalid = this.querySelector(':invalid');
        if (firstInvalid) {
            // Find which step it belongs to
            const stepEl = firstInvalid.closest('.form-step-content');
            if (stepEl) {
                const stepNum = parseInt(stepEl.id.replace('step-', ''));
                // Restore display
                steps.forEach(el => el.style.display = '');
                goStep(stepNum);
                firstInvalid.focus();
                firstInvalid.reportValidity();
            }
        }
        return;
    }

    // Restore display but keep submitting  
    steps.forEach(el => el.style.display = '');
    
    const btn = this.querySelector('.btn-submit');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;
});

const phoneInput = document.querySelector('input[name="phone"]');
if (phoneInput) phoneInput.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.startsWith('62')) v = '+' + v;
    else if (v.startsWith('0')) v = '+62' + v.substring(1);
    e.target.value = v;
});

// Photo preview function
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB.');
            input.value = '';
            return;
        }
        
        // Validate file type
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            alert('Format file tidak didukung! Gunakan JPG atau PNG.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            // Remove existing image if any
            const existingImg = preview.querySelector('img');
            if (existingImg) existingImg.remove();
            
            // Create and add new image
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
            preview.classList.add('has-image');
            
            // Hide placeholder text and icon
            const icon = preview.querySelector('i');
            const span = preview.querySelector('span');
            if (icon) icon.style.display = 'none';
            if (span) span.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// ===== AUTO-SAVE FORM DATA TO LOCALSTORAGE =====
<?php if (!$isEdit): ?>
try {
    const STORAGE_KEY = 'manual_entry_draft_<?= $_SESSION['user_id'] ?? 'guest' ?>';

    // Load saved data on page load (only for new entries, not edits)
    window.addEventListener('DOMContentLoaded', function() {
        const savedData = localStorage.getItem(STORAGE_KEY);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                // Restore form values
                for (const [key, value] of Object.entries(data)) {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input && value) {
                        if (input.type === 'checkbox') {
                            input.checked = value === 'true';
                        } else if (input.type === 'radio') {
                            if (input.value === value) input.checked = true;
                        } else {
                            input.value = value;
                        }
                    }
                }
                console.log('Form data restored from localStorage');
            } catch (e) {
                console.error('Error restoring form data:', e);
            }
        }
    });

    // Save form data on input change
    const formElement = document.getElementById('manualEntryForm');
    if (formElement) {
        formElement.addEventListener('input', function(e) {
            // Debounce the save operation
            clearTimeout(window.autoSaveTimeout);
            window.autoSaveTimeout = setTimeout(function() {
                try {
                    const formData = new FormData(formElement);
                    const dataObj = {};
                    
                    for (const [key, value] of formData.entries()) {
                        // Skip file inputs and CSRF token
                        if (key !== 'csrf_token' && !key.startsWith('doc_file')) {
                            dataObj[key] = value;
                        }
                    }
                    
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(dataObj));
                    console.log('Form data auto-saved');
                } catch (e) {
                    console.error('Error saving form data:', e);
                }
            }, 500); // Save after 500ms of inactivity
        });

        // Clear saved data on successful submit
        formElement.addEventListener('submit', function() {
            // Clear on next tick to ensure form submission completes
            setTimeout(function() {
                try {
                    localStorage.removeItem(STORAGE_KEY);
                    console.log('Draft data cleared after submit');
                } catch (e) {
                    console.error('Error clearing draft:', e);
                }
            }, 100);
        });
    }
} catch (e) {
    console.error('Error initializing auto-save:', e);
}
<?php endif; ?>

</script>
