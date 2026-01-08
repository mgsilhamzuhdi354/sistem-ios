<?php
/**
 * Document Upload Form
 */
$currentPage = 'documents';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="<?= BASE_URL ?>documents/<?= $crew['id'] ?>" class="btn-icon" style="width: 40px; height: 40px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1><i class="fas fa-upload"></i> Upload Document</h1>
            <p>Upload dokumen untuk: <strong><?= htmlspecialchars($crew['full_name']) ?></strong></p>
        </div>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <form method="POST" action="<?= BASE_URL ?>documents/store" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
        <input type="hidden" name="crew_id" value="<?= $crew['id'] ?>">
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label">Jenis Dokumen <span style="color: var(--danger);">*</span></label>
            <select name="document_type" class="form-control" required id="docType" onchange="updateDocName()">
                <option value="">Pilih jenis dokumen</option>
                <?php 
                $categories = ['identity' => 'Dokumen Identitas', 'license' => 'Lisensi & Sertifikat', 'training' => 'Pelatihan', 'medical' => 'Medis', 'other' => 'Lainnya'];
                $currentCat = '';
                foreach ($documentTypes as $type): 
                    if ($type['category'] !== $currentCat):
                        if ($currentCat !== '') echo '</optgroup>';
                        $currentCat = $type['category'];
                        echo '<optgroup label="' . ($categories[$currentCat] ?? $currentCat) . '">';
                    endif;
                ?>
                    <option value="<?= $type['code'] ?>" data-name="<?= htmlspecialchars($type['name_id'] ?? $type['name']) ?>"><?= htmlspecialchars($type['name_id'] ?? $type['name']) ?></option>
                <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label">Nama Dokumen <span style="color: var(--danger);">*</span></label>
            <input type="text" name="document_name" id="docName" class="form-control" required placeholder="Nama dokumen">
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label">Nomor Dokumen</label>
            <input type="text" name="document_number" class="form-control" placeholder="Nomor seri/registrasi dokumen">
        </div>
        
        <div class="grid-2" style="gap: 16px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Tanggal Terbit</label>
                <input type="date" name="issue_date" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Tanggal Kadaluarsa</label>
                <input type="date" name="expiry_date" class="form-control">
                <small style="color: var(--text-muted);">Kosongkan jika tidak ada expiry</small>
            </div>
        </div>
        
        <div class="grid-2" style="gap: 16px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Diterbitkan Oleh</label>
                <input type="text" name="issuing_authority" class="form-control" placeholder="Lembaga penerbit">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Tempat Terbit</label>
                <input type="text" name="issuing_place" class="form-control" placeholder="Kota/negara">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label">File Dokumen <span style="color: var(--danger);">*</span></label>
            <div class="upload-area" id="uploadArea" style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.2s;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
                <p style="color: var(--text-muted); margin-bottom: 8px;">Drag & drop file atau <span style="color: var(--accent-gold);">klik untuk pilih</span></p>
                <p style="font-size: 12px; color: var(--text-muted);">PDF, JPG, PNG, DOC (Max 10MB)</p>
                <input type="file" name="document" id="fileInput" style="display: none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
            </div>
            <div id="fileInfo" style="display: none; margin-top: 12px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">
                <i class="fas fa-file" style="color: var(--success);"></i>
                <span id="fileName" style="margin-left: 8px;"></span>
                <button type="button" onclick="clearFile()" style="float: right; background: none; border: none; color: var(--danger); cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 24px;">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="<?= BASE_URL ?>documents/<?= $crew['id'] ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-upload"></i> Upload Document
            </button>
        </div>
    </form>
</div>

<script>
// Drag & Drop
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');

uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--accent-gold)';
    uploadArea.style.background = 'rgba(212, 175, 55, 0.05)';
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.style.borderColor = 'var(--border-color)';
    uploadArea.style.background = 'transparent';
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--border-color)';
    uploadArea.style.background = 'transparent';
    
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        showFileInfo(e.dataTransfer.files[0]);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length) {
        showFileInfo(e.target.files[0]);
    }
});

function showFileInfo(file) {
    fileName.textContent = file.name + ' (' + formatBytes(file.size) + ')';
    fileInfo.style.display = 'block';
    uploadArea.style.display = 'none';
}

function clearFile() {
    fileInput.value = '';
    fileInfo.style.display = 'none';
    uploadArea.style.display = 'block';
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function updateDocName() {
    const select = document.getElementById('docType');
    const nameInput = document.getElementById('docName');
    const option = select.options[select.selectedIndex];
    
    if (option && option.dataset.name) {
        nameInput.value = option.dataset.name;
    }
}
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
