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

        <!-- Smart Scan Button -->
        <div class="form-card" style="border: 2px dashed #8b5cf6; background: linear-gradient(135deg, #f5f3ff, #ede9fe); cursor: pointer;" onclick="openSmartScan()">
            <div class="form-card-body" style="display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 1.25rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-magic" style="color: white; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h4 style="margin: 0; color: #6d28d9; font-size: 1rem; font-weight: 700;">⚡ Smart Scan — Isi Otomatis</h4>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.82rem; color: #7c3aed;">Tempel/paste data pelamar (dari WhatsApp, CV, email, dll) — sistem akan mengisi semua field yang cocok secara otomatis!</p>
                </div>
                <i class="fas fa-chevron-right" style="color: #8b5cf6; font-size: 1.2rem;"></i>
            </div>
        </div>

        <div class="step-nav"><button type="button" class="btn-next" onclick="goStep(2)"><?= getCurrentLanguage() === 'en' ? 'Next' : 'Selanjutnya' ?> <i class="fas fa-arrow-right"></i></button></div>
    </div>

<!-- Smart Scan Modal -->
<div id="smartScanModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); justify-content:center; align-items:center;">
    <div style="background:white; border-radius:16px; max-width:640px; width:95%; max-height:90vh; overflow:auto; box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: smartScanSlide 0.3s ease;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); padding: 1.25rem 1.5rem; border-radius: 16px 16px 0 0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-magic" style="color:white; font-size:1.1rem;"></i>
                </div>
                <div>
                    <h3 style="margin:0; color:white; font-size:1.1rem; font-weight:700;">Smart Scan — Auto-Fill</h3>
                    <p style="margin:0; color:rgba(255,255,255,0.8); font-size:0.8rem;">Paste data pelamar dan sistem akan mengisi form otomatis</p>
                </div>
                <button type="button" onclick="closeSmartScan()" style="margin-left:auto; background:rgba(255,255,255,0.2); border:none; color:white; width:32px; height:32px; border-radius:8px; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">&times;</button>
            </div>
        </div>

        <!-- Body -->
        <div style="padding: 1.5rem;">
            <!-- Hint -->
            <div style="background:#f5f3ff; border:1px solid #ddd6fe; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1rem;">
                <p style="margin:0 0 0.5rem 0; font-size:0.8rem; font-weight:600; color:#6d28d9;"><i class="fas fa-lightbulb" style="margin-right:4px;"></i> Contoh data yang bisa di-paste:</p>
                <p style="margin:0; font-size:0.75rem; color:#7c3aed; line-height:1.6;">
                    Nama: Ahmad Fauzi<br>
                    Email: ahmad@email.com<br>
                    HP: 081234567890<br>
                    NIK: 3201234567890001<br>
                    L/P: Laki-laki<br>
                    TTL: Surabaya, 15-03-1990<br>
                    Alamat: Jl. Merdeka No.10, dll...
                </p>
            </div>

            <!-- Textarea -->
            <textarea id="smartScanInput" rows="10" placeholder="Paste / tempel data pelamar di sini...&#10;&#10;Bisa dari WhatsApp, email, CV, atau format apapun.&#10;Sistem akan mendeteksi otomatis: nama, email, HP, NIK, alamat, passport, dll." style="width:100%; padding:0.75rem 1rem; border:2px solid #e2e8f0; border-radius:10px; font-family:monospace; font-size:0.85rem; resize:vertical; outline:none; transition:border-color 0.2s; line-height:1.6;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'"></textarea>

            <!-- Result preview area -->
            <div id="smartScanResult" style="display:none; margin-top:0.75rem;"></div>
        </div>

        <!-- Footer -->
        <div style="padding: 1rem 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; border-radius: 0 0 16px 16px; display:flex; gap:0.75rem;">
            <button type="button" onclick="closeSmartScan()" style="flex:1; padding:0.65rem; border:1px solid #e2e8f0; border-radius:10px; background:white; color:#64748b; font-weight:600; font-size:0.85rem; cursor:pointer;">Batal</button>
            <button type="button" onclick="runSmartScan()" style="flex:1; padding:0.65rem; border:none; border-radius:10px; background:linear-gradient(135deg, #8b5cf6, #6d28d9); color:white; font-weight:700; font-size:0.85rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 4px 12px rgba(109,40,217,0.3);">
                <i class="fas fa-magic"></i> Proses & Isi Otomatis
            </button>
        </div>
    </div>
</div>

<style>
@keyframes smartScanSlide {
    from { opacity:0; transform:translateY(20px); }
    to { opacity:1; transform:translateY(0); }
}
</style>

<script>
function openSmartScan() {
    document.getElementById('smartScanModal').style.display = 'flex';
    document.getElementById('smartScanInput').value = '';
    document.getElementById('smartScanResult').style.display = 'none';
    setTimeout(() => document.getElementById('smartScanInput').focus(), 200);
}
function closeSmartScan() {
    document.getElementById('smartScanModal').style.display = 'none';
}
document.getElementById('smartScanModal').addEventListener('click', function(e) {
    if (e.target === this) closeSmartScan();
});

function runSmartScan() {
    const raw = document.getElementById('smartScanInput').value.trim();
    if (!raw) { alert('Silakan tempel/paste data terlebih dahulu!'); return; }

    const found = {};
    const text = raw;

    // === EMAIL ===
    const emailRe = /[\w.+-]+@[\w-]+\.[\w.]+/i;
    const emailMatch = text.match(emailRe);
    if (emailMatch) { found['Email'] = emailMatch[0]; setField('email', emailMatch[0]); }

    // === PHONE ===
    const phonePatterns = [
        /(?:hp|phone|telp|telepon|no\.?\s*hp|whatsapp|wa|mobile|contact)\s*[:=\-]?\s*([\+]?[0-9\s\-\(\)]{9,15})/i,
        /(?:^|\s)((?:\+62|62|08)\d[\d\s\-]{7,13})/m
    ];
    for (const re of phonePatterns) {
        const m = text.match(re);
        if (m) { const ph = m[1].replace(/[\s\-\(\)]/g, ''); found['Telepon'] = ph; setField('phone', ph); break; }
    }

    // === KTP / NIK ===
    const ktpRe = /(?:nik|ktp|no\.?\s*ktp|no\.?\s*nik)\s*[:=\-]?\s*(\d{16})/i;
    const ktpMatch = text.match(ktpRe);
    if (ktpMatch) { found['NIK/KTP'] = ktpMatch[1]; setField('ktp_number', ktpMatch[1]); }
    else {
        // standalone 16-digit number
        const standalone16 = text.match(/(?:^|\s)(\d{16})(?:\s|$)/m);
        if (standalone16) { found['NIK/KTP'] = standalone16[1]; setField('ktp_number', standalone16[1]); }
    }

    // === NAME ===
    const namePatterns = [
        /(?:nama\s*(?:lengkap)?|name|full\s*name)\s*[:=\-]\s*(.+)/i
    ];
    for (const re of namePatterns) {
        const m = text.match(re);
        if (m) { const n = m[1].trim().replace(/[,;].*/, '').trim(); found['Nama'] = n; setField('full_name', n); break; }
    }

    // === GENDER ===
    const genderRe = /(?:jenis\s*kelamin|gender|j\.?k\.?|l\/?p)\s*[:=\-]?\s*(laki[\s\-]*laki|perempuan|pria|wanita|male|female|lk|pr)/i;
    const genderMatch = text.match(genderRe);
    if (genderMatch) {
        const g = genderMatch[1].toLowerCase();
        const isMale = /laki|pria|male|^lk$/i.test(g);
        found['Gender'] = isMale ? 'Male' : 'Female';
        setSelect('gender', isMale ? 'Male' : 'Female');
    }

    // === DATE OF BIRTH ===
    const dobPatterns = [
        /(?:ttl|tanggal\s*lahir|tgl\s*lahir|dob|born|lahir)\s*[:=\-]?\s*(?:([a-zA-Z\s]+),?\s*)?(\d{1,2}[\s\-\/\.]\d{1,2}[\s\-\/\.]\d{2,4})/i,
        /(?:ttl|tanggal\s*lahir|tgl\s*lahir|dob|born|lahir)\s*[:=\-]?\s*(?:([a-zA-Z\s]+),?\s*)?(\d{1,2}\s+(?:jan(?:uari)?|feb(?:ruari)?|mar(?:et)?|apr(?:il)?|mei|jun(?:i)?|jul(?:i)?|agu(?:stus)?|sep(?:tember)?|okt(?:ober)?|nov(?:ember)?|des(?:ember)?)\s+\d{2,4})/i
    ];
    for (const re of dobPatterns) {
        const m = text.match(re);
        if (m) {
            if (m[1]) { found['Tempat Lahir'] = m[1].trim(); setField('place_of_birth', m[1].trim()); }
            if (m[2]) {
                const parsed = parseDate(m[2]);
                if (parsed) { found['Tanggal Lahir'] = parsed; setField('date_of_birth', parsed); }
            }
            break;
        }
    }

    // === ADDRESS ===
    const addrRe = /(?:alamat|address)\s*[:=\-]\s*(.+)/i;
    const addrMatch = text.match(addrRe);
    if (addrMatch) { found['Alamat'] = addrMatch[1].trim(); setField('address', addrMatch[1].trim()); }

    // === CITY ===
    const cityRe = /(?:kota|city)\s*[:=\-]\s*(.+)/i;
    const cityMatch = text.match(cityRe);
    if (cityMatch) { found['Kota'] = cityMatch[1].trim(); setField('city', cityMatch[1].trim()); }

    // === NATIONALITY ===
    const natRe = /(?:kewarganegaraan|nationality|wni|wna)\s*[:=\-]\s*(.+)/i;
    const natMatch = text.match(natRe);
    if (natMatch) { found['Kewarganegaraan'] = natMatch[1].trim(); setField('nationality', natMatch[1].trim()); }

    // === BLOOD TYPE ===
    const bloodRe = /(?:gol(?:ongan)?\s*darah|blood\s*(?:type)?)\s*[:=\-]?\s*(A|B|AB|O)/i;
    const bloodMatch = text.match(bloodRe);
    if (bloodMatch) { found['Gol. Darah'] = bloodMatch[1].toUpperCase(); setSelect('blood_type', bloodMatch[1].toUpperCase()); }

    // === SEAMAN BOOK ===
    const seamanRe = /(?:buku\s*pelaut|seaman\s*book|seamanbook|no\.?\s*buku\s*pelaut)\s*[:=\-]?\s*([A-Z0-9\-\/\s]+)/i;
    const seamanMatch = text.match(seamanRe);
    if (seamanMatch) { found['No. Buku Pelaut'] = seamanMatch[1].trim(); setField('seaman_book_no', seamanMatch[1].trim()); }

    // === PASSPORT ===
    const passRe = /(?:passport|paspor|no\.?\s*paspor|no\.?\s*passport)\s*[:=\-]?\s*([A-Z0-9\-]+)/i;
    const passMatch = text.match(passRe);
    if (passMatch) { found['No. Paspor'] = passMatch[1].trim(); setField('passport_no', passMatch[1].trim()); }

    // === HEIGHT ===
    const hRe = /(?:tinggi\s*(?:badan)?|height)\s*[:=\-]?\s*(\d{2,3})\s*(?:cm)?/i;
    const hMatch = text.match(hRe);
    if (hMatch) { found['Tinggi'] = hMatch[1] + ' cm'; setField('height_cm', hMatch[1]); }

    // === WEIGHT ===
    const wRe = /(?:berat\s*(?:badan)?|weight)\s*[:=\-]?\s*(\d{2,3})\s*(?:kg)?/i;
    const wMatch = text.match(wRe);
    if (wMatch) { found['Berat'] = wMatch[1] + ' kg'; setField('weight_kg', wMatch[1]); }

    // === RANK ===
    const rankRe = /(?:rank|jabatan|posisi\s*terakhir|last\s*rank)\s*[:=\-]?\s*(.+)/i;
    const rankMatch = text.match(rankRe);
    if (rankMatch) { found['Rank'] = rankMatch[1].trim(); setField('last_rank', rankMatch[1].trim()); }

    // === VESSEL NAME ===
    const vesselRe = /(?:kapal\s*terakhir|vessel\s*(?:name)?|nama\s*kapal|ship\s*name|last\s*vessel)\s*[:=\-]?\s*(.+)/i;
    const vesselMatch = text.match(vesselRe);
    if (vesselMatch) { found['Kapal'] = vesselMatch[1].trim(); setField('last_vessel_name', vesselMatch[1].trim()); }

    // === VESSEL TYPE ===
    const vtRe = /(?:jenis\s*kapal|vessel\s*type|ship\s*type|type\s*kapal)\s*[:=\-]?\s*(.+)/i;
    const vtMatch = text.match(vtRe);
    if (vtMatch) { found['Jenis Kapal'] = vtMatch[1].trim(); setField('last_vessel_type', vtMatch[1].trim()); }

    // === SEA SERVICE ===
    const ssRe = /(?:pengalaman\s*laut|sea\s*service|total\s*sea|pengalaman\s*berlayar)\s*[:=\-]?\s*(\d+)\s*(?:bulan|months|bln)?/i;
    const ssMatch = text.match(ssRe);
    if (ssMatch) { found['Pengalaman Laut'] = ssMatch[1] + ' bulan'; setField('total_sea_service_months', ssMatch[1]); }

    // === SHOE SIZE ===
    const shoeRe = /(?:ukuran\s*sepatu|shoe\s*size)\s*[:=\-]?\s*(\d{2})/i;
    const shoeMatch = text.match(shoeRe);
    if (shoeMatch) { found['Ukuran Sepatu'] = shoeMatch[1]; setField('shoe_size', shoeMatch[1]); }

    // === OVERALL SIZE ===
    const overallRe = /(?:ukuran\s*overall|overall\s*size|coverall)\s*[:=\-]?\s*([XSML0-9]+)/i;
    const overallMatch = text.match(overallRe);
    if (overallMatch) { found['Ukuran Overall'] = overallMatch[1]; setField('overall_size', overallMatch[1]); }

    // === EMERGENCY ===
    const emergNameRe = /(?:kontak\s*darurat|emergency\s*(?:contact)?(?:\s*name)?)\s*[:=\-]?\s*(.+)/i;
    const emergNameMatch = text.match(emergNameRe);
    if (emergNameMatch) { found['Kontak Darurat'] = emergNameMatch[1].trim(); setField('emergency_name', emergNameMatch[1].trim()); }

    // === POSTAL CODE ===
    const postalRe = /(?:kode\s*pos|postal\s*code|zip)\s*[:=\-]?\s*(\d{5})/i;
    const postalMatch = text.match(postalRe);
    if (postalMatch) { found['Kode Pos'] = postalMatch[1]; setField('postal_code', postalMatch[1]); }

    // === SHOW RESULTS ===
    const keys = Object.keys(found);
    if (keys.length === 0) {
        document.getElementById('smartScanResult').innerHTML = '<div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:0.75rem; text-align:center; font-size:0.85rem; color:#dc2626;"><i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>Tidak ada data yang terdeteksi. Pastikan format data memiliki label seperti "Nama:", "Email:", "HP:", dll.</div>';
        document.getElementById('smartScanResult').style.display = 'block';
        return;
    }

    let html = '<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:0.75rem 1rem;">';
    html += '<p style="margin:0 0 0.5rem 0; font-size:0.82rem; font-weight:700; color:#16a34a;"><i class="fas fa-check-circle" style="margin-right:4px;"></i> ' + keys.length + ' field berhasil terdeteksi & diisi:</p>';
    html += '<div style="display:flex; flex-wrap:wrap; gap:6px;">';
    for (const k of keys) {
        html += '<span style="background:#dcfce7; color:#15803d; font-size:0.72rem; padding:3px 8px; border-radius:6px; font-weight:600;">' + k + '</span>';
    }
    html += '</div></div>';
    document.getElementById('smartScanResult').innerHTML = html;
    document.getElementById('smartScanResult').style.display = 'block';

    // Close after 1.5s
    setTimeout(() => {
        closeSmartScan();
        alert('✅ Smart Scan selesai!\n\n' + keys.length + ' field berhasil diisi otomatis:\n• ' + keys.join('\n• ') + '\n\nSilakan cek dan lengkapi data di setiap step.');
    }, 800);
}

function setField(name, value) {
    const el = document.querySelector('[name="' + name + '"]');
    if (el) { el.value = value; el.style.borderColor = '#8b5cf6'; setTimeout(() => el.style.borderColor = '', 3000); }
}
function setSelect(name, value) {
    const el = document.querySelector('[name="' + name + '"]');
    if (el) {
        for (let i = 0; i < el.options.length; i++) {
            if (el.options[i].value === value) { el.selectedIndex = i; el.style.borderColor = '#8b5cf6'; setTimeout(() => el.style.borderColor = '', 3000); break; }
        }
    }
}

function parseDate(str) {
    const months = {jan:1,januari:1,feb:2,februari:2,mar:3,maret:3,apr:4,april:4,mei:5,may:5,jun:6,juni:6,jul:7,juli:7,agu:8,agustus:8,aug:8,sep:9,september:9,okt:10,oktober:10,oct:10,nov:11,november:11,des:12,desember:12,dec:12};
    // Try dd-mm-yyyy or dd/mm/yyyy
    let m = str.match(/(\d{1,2})[\s\-\/\.](\d{1,2})[\s\-\/\.](\d{2,4})/);
    if (m) {
        let d = parseInt(m[1]), mo = parseInt(m[2]), y = parseInt(m[3]);
        if (y < 100) y += 2000; if (y < 1950) y = y - 2000 + 1900 + 100;
        return y + '-' + String(mo).padStart(2,'0') + '-' + String(d).padStart(2,'0');
    }
    // Try dd month yyyy
    m = str.match(/(\d{1,2})\s+([a-zA-Z]+)\s+(\d{2,4})/);
    if (m) {
        const mo = months[m[2].toLowerCase().substring(0,3)];
        if (mo) {
            let y = parseInt(m[3]); if (y < 100) y += 1900;
            return y + '-' + String(mo).padStart(2,'0') + '-' + String(parseInt(m[1])).padStart(2,'0');
        }
    }
    return null;
}
</script>

    <!-- STEP 2: Informasi Pribadi -->
    <div class="form-step-content" id="step-2">
        <!-- KTP Number Lookup Card -->
        <div class="form-card" style="border: 2px solid #3b82f6; background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="form-card-header" style="border-bottom-color: #bfdbfe;">
                <div class="form-card-icon blue"><i class="fas fa-id-card"></i></div>
                <h3><?= getCurrentLanguage() === 'en' ? 'KTP Number (NIK) - Quick Lookup' : 'Nomor KTP (NIK) - Pencarian Cepat' ?></h3>
            </div>
            <div class="form-card-body">
                <p style="font-size: 0.85rem; color: #1e40af; margin: 0 0 0.75rem 0;">
                    <i class="fas fa-magic" style="margin-right: 0.3rem;"></i>
                    <?= getCurrentLanguage() === 'en' ? 'Enter KTP number to auto-fill all fields from existing data' : 'Masukkan nomor KTP untuk mengisi otomatis dari data yang sudah ada' ?>
                </p>
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label"><?= getCurrentLanguage() === 'en' ? 'KTP Number (NIK)' : 'Nomor KTP (NIK)' ?></label>
                        <input type="text" name="ktp_number" id="ktpNumberInput" class="form-input" 
                               placeholder="<?= getCurrentLanguage() === 'en' ? '16-digit NIK number' : 'Masukkan 16 digit NIK' ?>" 
                               maxlength="16" pattern="[0-9]*" inputmode="numeric"
                               value="<?= htmlspecialchars($e['ktp_number'] ?? '') ?>"
                               style="font-size: 1.1rem; letter-spacing: 1px; font-weight: 600;">
                    </div>
                    <div class="form-group" style="flex: 1; display: flex; align-items: flex-end;">
                        <button type="button" id="btnSearchKtp" onclick="searchByKtp()" class="btn-search-ktp" style="width: 100%; margin-bottom: 0.75rem;">
                            <i class="fas fa-search"></i> <?= getCurrentLanguage() === 'en' ? 'Search Data' : 'Cari Data' ?>
                        </button>
                    </div>
                </div>
                <!-- Search Result Notification -->
                <div id="ktpSearchResult" style="display: none;"></div>
            </div>
        </div>

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

/* KTP Search Button */
.btn-search-ktp { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; font-family: inherit; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
.btn-search-ktp:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59,130,246,0.3); }
.btn-search-ktp:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.btn-search-ktp.loading i { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* KTP Result Toast */
.ktp-result { padding: 0.75rem 1rem; border-radius: 10px; font-size: 0.88rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; animation: slideIn 0.3s ease; }
.ktp-result.success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.ktp-result.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.ktp-result.info { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
@keyframes slideIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

/* Auto-fill highlight */
.field-filled { animation: highlightFill 1.5s ease; }
@keyframes highlightFill { 0% { background-color: #dcfce7; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); } 100% { background-color: #f9fafb; box-shadow: none; } }

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

// ===== KTP AUTO-FILL FUNCTION =====
function searchByKtp() {
    const ktpInput = document.getElementById('ktpNumberInput');
    const btn = document.getElementById('btnSearchKtp');
    const resultDiv = document.getElementById('ktpSearchResult');
    const ktp = ktpInput.value.replace(/\D/g, '');
    
    if (ktp.length < 10) {
        resultDiv.innerHTML = '<div class="ktp-result info"><i class="fas fa-info-circle"></i> Masukkan minimal 10 digit nomor KTP</div>';
        resultDiv.style.display = 'block';
        return;
    }
    
    // Show loading state
    btn.disabled = true;
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-spinner"></i> Mencari...';
    resultDiv.style.display = 'none';
    
    fetch(`<?= url('/crewing/manual-entry/search-ktp') ?>?ktp=${encodeURIComponent(ktp)}`)
        .then(res => res.json())
        .then(data => {
            if (data.found && data.data) {
                resultDiv.innerHTML = '<div class="ktp-result success"><i class="fas fa-check-circle"></i> ' + data.message + ' - Data berhasil diisi otomatis!</div>';
                
                // Map API response fields to form field names
                const fieldMap = {
                    'full_name': 'full_name',
                    'email': 'email',
                    'phone': 'phone',
                    'date_of_birth': 'date_of_birth',
                    'gender': 'gender',
                    'place_of_birth': 'place_of_birth',
                    'nationality': 'nationality',
                    'blood_type': 'blood_type',
                    'address': 'address',
                    'city': 'city',
                    'country': 'country',
                    'postal_code': 'postal_code',
                    'seaman_book_no': 'seaman_book_no',
                    'seaman_book_expiry': 'seaman_book_expiry',
                    'passport_no': 'passport_no',
                    'passport_expiry': 'passport_expiry',
                    'height_cm': 'height_cm',
                    'weight_kg': 'weight_kg',
                    'shoe_size': 'shoe_size',
                    'overall_size': 'overall_size',
                    'emergency_name': 'emergency_name',
                    'emergency_phone': 'emergency_phone',
                    'emergency_relation': 'emergency_relation',
                    'total_sea_service_months': 'total_sea_service_months',
                    'last_rank': 'last_rank',
                    'last_vessel_name': 'last_vessel_name',
                    'last_vessel_type': 'last_vessel_type',
                    'last_sign_off': 'last_sign_off'
                };
                
                let filledCount = 0;
                for (const [apiKey, formName] of Object.entries(fieldMap)) {
                    const value = data.data[apiKey];
                    if (value !== null && value !== '' && value !== undefined) {
                        const input = document.querySelector(`[name="${formName}"]`);
                        if (input) {
                            input.value = value;
                            input.classList.add('field-filled');
                            filledCount++;
                            // Remove highlight class after animation
                            setTimeout(() => input.classList.remove('field-filled'), 1500);
                        }
                    }
                }
                
                resultDiv.innerHTML = `<div class="ktp-result success"><i class="fas fa-check-circle"></i> ${data.message} — <strong>${filledCount} kolom</strong> terisi otomatis!</div>`;
            } else {
                resultDiv.innerHTML = '<div class="ktp-result error"><i class="fas fa-times-circle"></i> ' + data.message + '</div>';
            }
            resultDiv.style.display = 'block';
        })
        .catch(err => {
            console.error('KTP search error:', err);
            resultDiv.innerHTML = '<div class="ktp-result error"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat mencari data</div>';
            resultDiv.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('loading');
            btn.innerHTML = '<i class="fas fa-search"></i> <?= getCurrentLanguage() === 'en' ? 'Search Data' : 'Cari Data' ?>';
        });
}

// Auto-search when 16 digits entered
document.getElementById('ktpNumberInput').addEventListener('input', function(e) {
    // Only allow digits
    this.value = this.value.replace(/\D/g, '');
    // Auto-search when 16 digits complete
    if (this.value.length === 16) {
        searchByKtp();
    }
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
