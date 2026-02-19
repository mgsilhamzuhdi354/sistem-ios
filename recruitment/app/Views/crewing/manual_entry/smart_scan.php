<!-- PDF.js & Mammoth.js CDN (verified working versions) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
<script>
// Configure PDF.js worker
if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
}
</script>

<!-- ===================== SMART SCAN PRO ===================== -->
<!-- Button -->
<div class="form-card" style="border: 2px dashed #8b5cf6; background: linear-gradient(135deg, #f5f3ff, #ede9fe); cursor: pointer; transition: all 0.3s;" onclick="openSmartScan()" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(139,92,246,0.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
    <div class="form-card-body" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem;">
        <div style="width: 52px; height: 52px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position:relative;">
            <i class="fas fa-robot" style="color: white; font-size: 1.3rem;"></i>
            <span style="position:absolute; top:-4px; right:-4px; width:14px; height:14px; background:#10b981; border:2px solid white; border-radius:50%;"></span>
        </div>
        <div style="flex:1;">
            <h4 style="margin: 0; color: #6d28d9; font-size: 1.05rem; font-weight: 700;">⚡ Smart Scan Pro — Auto-Fill Canggih</h4>
            <p style="margin: 0.3rem 0 0 0; font-size: 0.8rem; color: #7c3aed; line-height: 1.4;">Upload <b>PDF, Word (.docx), Teks</b> atau paste data → langsung otomatis terisi di semua form!</p>
        </div>
        <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
            <div style="background:linear-gradient(135deg,#8b5cf6,#6d28d9); color:white; font-size:0.65rem; font-weight:700; padding:3px 8px; border-radius:6px; text-transform:uppercase; letter-spacing:0.5px;">PRO</div>
            <i class="fas fa-chevron-right" style="color: #8b5cf6; font-size: 1rem;"></i>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="smartScanModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.65); backdrop-filter:blur(6px); justify-content:center; align-items:center;">
    <div style="background:white; border-radius:20px; max-width:780px; width:95%; max-height:92vh; overflow:hidden; box-shadow:0 32px 80px rgba(0,0,0,0.35); display:flex; flex-direction:column;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #7c3aed, #4f46e5); padding: 1.25rem 1.5rem; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; background:rgba(255,255,255,0.15); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-robot" style="color:white; font-size:1.2rem;"></i>
                </div>
                <div style="flex:1;">
                    <h3 style="margin:0; color:white; font-size:1.1rem; font-weight:700;">Smart Scan Pro</h3>
                    <p style="margin:0; color:rgba(255,255,255,0.75); font-size:0.78rem;">Upload PDF / Word / Teks → Langsung Isi Otomatis</p>
                </div>
                <button type="button" onclick="closeSmartScan()" style="background:rgba(255,255,255,0.15); border:none; color:white; width:34px; height:34px; border-radius:10px; cursor:pointer; font-size:1.2rem; display:flex; align-items:center; justify-content:center;">&times;</button>
            </div>
        </div>

        <div style="flex:1; overflow-y:auto; padding:1.5rem;">
            
            <!-- File Upload Area -->
            <div style="margin-bottom:1rem;">
                <p style="margin:0 0 0.5rem; font-size:0.82rem; font-weight:700; color:#1e293b;"><i class="fas fa-file-upload" style="color:#7c3aed; margin-right:4px;"></i> Upload Dokumen</p>
                <div id="scanDropZone" style="border:2px dashed #c4b5fd; border-radius:14px; background:linear-gradient(135deg,#f5f3ff,#ede9fe); padding:1.5rem; text-align:center; cursor:pointer; transition:all 0.3s;" onclick="document.getElementById('scanFileInput').click()">
                    <div style="width:56px; height:56px; background:linear-gradient(135deg,#8b5cf6,#6d28d9); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 0.75rem;">
                        <i class="fas fa-cloud-upload-alt" style="color:white; font-size:1.5rem;"></i>
                    </div>
                    <p style="margin:0; font-weight:700; color:#6d28d9; font-size:0.9rem;">Klik atau seret file ke sini</p>
                    <p style="margin:0.3rem 0 0; font-size:0.75rem; color:#8b5cf6;">PDF, Word (.docx), Teks (.txt/.csv) — Maks 10MB</p>
                    <input type="file" id="scanFileInput" accept=".pdf,.docx,.txt,.csv" style="display:none;" onchange="handleFileUpload(this)">
                </div>
                <!-- File status -->
                <div id="fileStatus" style="display:none; margin-top:0.5rem;"></div>
            </div>

            <!-- Divider -->
            <div style="display:flex; align-items:center; gap:0.75rem; margin:1.25rem 0;">
                <div style="flex:1; height:1px; background:#e2e8f0;"></div>
                <span style="font-size:0.75rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">atau paste manual</span>
                <div style="flex:1; height:1px; background:#e2e8f0;"></div>
            </div>

            <!-- Quick Actions -->
            <div style="display:flex; gap:0.5rem; margin-bottom:0.75rem;">
                <button type="button" onclick="pasteFromClipboard()" style="flex:1; padding:0.5rem; border:1px solid #ddd6fe; border-radius:10px; background:#f5f3ff; color:#6d28d9; font-size:0.78rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;">
                    <i class="fas fa-clipboard"></i> Paste Clipboard
                </button>
                <button type="button" onclick="clearScanInput()" style="padding:0.5rem 0.75rem; border:1px solid #e2e8f0; border-radius:10px; background:white; color:#94a3b8; font-size:0.78rem; cursor:pointer;">
                    <i class="fas fa-eraser"></i>
                </button>
            </div>

            <!-- Textarea -->
            <textarea id="smartScanInput" rows="6" placeholder="Paste data pelamar di sini...&#10;&#10;Contoh:&#10;Nama: Ahmad Fauzi&#10;Email: ahmad@gmail.com&#10;HP: 081234567890&#10;NIK: 3201234567890001&#10;TTL: Surabaya, 15-03-1990&#10;Alamat: Jl. Merdeka No.10" style="width:100%; padding:0.75rem 1rem; border:2px solid #e2e8f0; border-radius:12px; font-family:'Inter',sans-serif; font-size:0.85rem; resize:vertical; outline:none; transition:border-color 0.2s; line-height:1.6;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'" oninput="liveDetect()"></textarea>

            <!-- Live Counter -->
            <div id="liveCounter" style="display:none; margin-top:0.5rem; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #bbf7d0; border-radius:10px; padding:0.5rem 0.75rem; align-items:center; gap:6px;">
                <i class="fas fa-bolt" style="color:#22c55e;"></i>
                <span id="liveCountText" style="font-size:0.8rem; font-weight:600; color:#15803d;">0 field</span>
                <span style="margin-left:auto; font-size:0.65rem; background:#dcfce7; color:#16a34a; padding:2px 6px; border-radius:5px; font-weight:600;">LIVE</span>
            </div>
        </div>

        <!-- Footer -->
        <div style="padding:1rem 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; flex-shrink:0; display:flex; gap:0.75rem;">
            <button type="button" onclick="closeSmartScan()" style="flex:1; padding:0.7rem; border:1px solid #e2e8f0; border-radius:10px; background:white; color:#64748b; font-weight:600; font-size:0.85rem; cursor:pointer;">Batal</button>
            <button type="button" onclick="quickApply()" id="btnQuickApply" style="flex:2; padding:0.7rem; border:none; border-radius:10px; background:linear-gradient(135deg, #22c55e, #16a34a); color:white; font-weight:700; font-size:0.9rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 15px rgba(22,163,74,0.35); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                <i class="fas fa-magic"></i> Scan & Isi Langsung ke Form
            </button>
        </div>
    </div>
</div>

<!-- Processing Overlay -->
<div id="scanProcessing" style="display:none; position:fixed; inset:0; z-index:10000; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); justify-content:center; align-items:center;">
    <div style="background:white; border-radius:20px; padding:2.5rem; text-align:center; max-width:400px; width:90%; box-shadow:0 25px 60px rgba(0,0,0,0.3);">
        <div style="width:64px; height:64px; border:4px solid #e2e8f0; border-top-color:#7c3aed; border-radius:50%; animation:scanSpin 0.8s linear infinite; margin:0 auto 1.25rem;"></div>
        <h4 id="processingTitle" style="margin:0 0 0.5rem; color:#1e293b; font-size:1rem;">Memproses dokumen...</h4>
        <p id="processingText" style="margin:0; color:#94a3b8; font-size:0.82rem;">Mengekstrak teks dari file</p>
    </div>
</div>

<!-- Success Overlay -->
<div id="scanSuccess" style="display:none; position:fixed; inset:0; z-index:10000; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); justify-content:center; align-items:center;">
    <div style="background:white; border-radius:20px; padding:2rem; text-align:center; max-width:420px; width:90%; box-shadow:0 25px 60px rgba(0,0,0,0.3); animation:successPop 0.4s ease;">
        <div style="width:64px; height:64px; background:linear-gradient(135deg,#22c55e,#16a34a); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
            <i class="fas fa-check" style="color:white; font-size:1.5rem;"></i>
        </div>
        <h4 style="margin:0 0 0.5rem; color:#1e293b; font-size:1.1rem;">Smart Scan Berhasil!</h4>
        <p id="successDetail" style="margin:0 0 1rem; color:#64748b; font-size:0.85rem;"></p>
        <div id="successFields" style="display:flex; flex-wrap:wrap; gap:5px; justify-content:center; margin-bottom:1.25rem;"></div>
        <button type="button" onclick="document.getElementById('scanSuccess').style.display='none'" style="padding:0.6rem 2rem; border:none; border-radius:10px; background:linear-gradient(135deg,#22c55e,#16a34a); color:white; font-weight:700; font-size:0.85rem; cursor:pointer;">
            <i class="fas fa-check"></i> Lanjutkan
        </button>
    </div>
</div>

<style>
@keyframes scanSpin { to { transform: rotate(360deg); } }
@keyframes successPop { from { opacity:0; transform:scale(0.8); } to { opacity:1; transform:scale(1); } }
</style>

<script>
// ====== FIELD DEFINITIONS ======
const FIELD_DEFS = [
    { key:'full_name', label:'Nama Lengkap', step:2, type:'text',
      patterns: [/(?:nama\s*(?:lengkap)?|name|full\s*name)\s*[:=\-]\s*(.+)/i] },
    { key:'email', label:'Email', step:2, type:'text',
      patterns: [/(?:email|e-mail)\s*[:=\-]?\s*([\w.+-]+@[\w-]+\.[\w.]+)/i, /([\w.+-]+@[\w-]+\.[\w.]+)/i] },
    { key:'phone', label:'No. Telepon', step:2, type:'text',
      patterns: [/(?:hp|phone|telp|telepon|no\.?\s*hp|whatsapp|wa|mobile|handphone|no\.?\s*telp)\s*[:=\-]?\s*([\+]?[0-9\s\-\(\)]{9,15})/i, /(?:^|\s)((?:\+62|62|08)\d[\d\s\-]{7,13})/m],
      transform: v => v.replace(/[\s\-\(\)]/g, '') },
    { key:'ktp_number', label:'NIK / KTP', step:2, type:'text',
      patterns: [/(?:nik|ktp|no\.?\s*ktp|no\.?\s*nik|nomor\s*induk)\s*[:=\-]?\s*(\d{16})/i, /(?:^|\s)(\d{16})(?:\s|$)/m] },
    { key:'gender', label:'Jenis Kelamin', step:2, type:'select',
      patterns: [/(?:jenis\s*kelamin|gender|j\.?k\.?|l\/?p|kelamin)\s*[:=\-]?\s*(laki[\s\-]*laki|perempuan|pria|wanita|male|female|lk|pr)/i],
      transform: v => /laki|pria|male|^lk$/i.test(v) ? 'Male' : 'Female',
      display: v => v === 'Male' ? 'Laki-laki' : 'Perempuan' },
    { key:'date_of_birth', label:'Tanggal Lahir', step:2, type:'date',
      patterns: [/(?:ttl|tanggal\s*lahir|tgl\s*lahir|dob|born|lahir)\s*[:=\-]?\s*(?:[a-zA-Z\s]+,?\s*)?(\d{1,2}[\s\-\/\.]\d{1,2}[\s\-\/\.]\d{2,4})/i],
      transform: v => smartParseDate(v) },
    { key:'place_of_birth', label:'Tempat Lahir', step:2, type:'text',
      patterns: [/(?:ttl|tempat\s*lahir|tempat,?\s*tanggal\s*lahir)\s*[:=\-]?\s*([a-zA-Z\s]+?)(?:\s*,|\s+\d)/i,
                 /(?:tempat\s*lahir|birthplace)\s*[:=\-]?\s*(.+)/i] },
    { key:'nationality', label:'Kewarganegaraan', step:2, type:'text',
      patterns: [/(?:kewarganegaraan|nationality|warga\s*negara)\s*[:=\-]?\s*(.+)/i] },
    { key:'blood_type', label:'Gol. Darah', step:2, type:'select',
      patterns: [/(?:gol(?:ongan)?\s*darah|blood\s*(?:type)?|gd)\s*[:=\-]?\s*(AB|A|B|O)/i],
      transform: v => v.toUpperCase() },
    { key:'address', label:'Alamat', step:2, type:'text',
      patterns: [/(?:alamat|address|domisili)\s*[:=\-]\s*(.+)/i] },
    { key:'city', label:'Kota', step:2, type:'text',
      patterns: [/(?:kota|city|kabupaten)\s*[:=\-]\s*(.+)/i] },
    { key:'postal_code', label:'Kode Pos', step:2, type:'text',
      patterns: [/(?:kode\s*pos|postal\s*code|zip)\s*[:=\-]?\s*(\d{5})/i] },
    { key:'seaman_book_no', label:'No. Buku Pelaut', step:3, type:'text',
      patterns: [/(?:buku\s*pelaut|seaman\s*book|seamanbook|no\.?\s*buku\s*pelaut|BST)\s*[:=\-]?\s*([A-Z0-9\-\/]+)/i] },
    { key:'passport_no', label:'No. Paspor', step:3, type:'text',
      patterns: [/(?:passport|paspor|no\.?\s*paspor|no\.?\s*passport)\s*[:=\-]?\s*([A-Z0-9\-]+)/i] },
    { key:'height_cm', label:'Tinggi Badan', step:4, type:'text',
      patterns: [/(?:tinggi\s*(?:badan)?|height|tb)\s*[:=\-]?\s*(\d{2,3})\s*(?:cm)?/i],
      display: v => v + ' cm' },
    { key:'weight_kg', label:'Berat Badan', step:4, type:'text',
      patterns: [/(?:berat\s*(?:badan)?|weight|bb)\s*[:=\-]?\s*(\d{2,3})\s*(?:kg)?/i],
      display: v => v + ' kg' },
    { key:'shoe_size', label:'Ukuran Sepatu', step:4, type:'text',
      patterns: [/(?:ukuran\s*sepatu|shoe\s*size|sepatu)\s*[:=\-]?\s*(\d{2})/i] },
    { key:'overall_size', label:'Ukuran Overall', step:4, type:'text',
      patterns: [/(?:ukuran\s*overall|overall\s*size|coverall)\s*[:=\-]?\s*([XSML0-9]+)/i] },
    { key:'emergency_name', label:'Kontak Darurat', step:4, type:'text',
      patterns: [/(?:kontak\s*darurat|emergency\s*(?:contact)?(?:\s*name)?)\s*[:=\-]?\s*(.+)/i] },
    { key:'emergency_phone', label:'Telp. Darurat', step:4, type:'text',
      patterns: [/(?:telp?\s*darurat|emergency\s*phone|no\.?\s*darurat)\s*[:=\-]?\s*([\+]?[0-9\s\-]{9,15})/i],
      transform: v => v.replace(/[\s\-]/g, '') },
    { key:'total_sea_service_months', label:'Pengalaman Laut', step:5, type:'text',
      patterns: [/(?:pengalaman\s*(?:laut|berlayar)?|sea\s*service|total\s*sea)\s*[:=\-]?\s*(\d+)\s*(?:bulan|months|bln)?/i],
      display: v => v + ' bulan' },
    { key:'last_rank', label:'Rank Terakhir', step:5, type:'text',
      patterns: [/(?:rank|jabatan|posisi\s*terakhir|last\s*rank|pangkat)\s*[:=\-]?\s*(.+)/i] },
    { key:'last_vessel_name', label:'Kapal Terakhir', step:5, type:'text',
      patterns: [/(?:kapal\s*terakhir|vessel\s*(?:name)?|nama\s*kapal|ship\s*name|last\s*vessel)\s*[:=\-]?\s*(.+)/i] },
    { key:'last_vessel_type', label:'Jenis Kapal', step:5, type:'text',
      patterns: [/(?:jenis\s*kapal|vessel\s*type|ship\s*type|type\s*kapal|tipe\s*kapal)\s*[:=\-]?\s*(.+)/i] },
    { key:'expected_salary', label:'Gaji Diharapkan', step:1, type:'text',
      patterns: [/(?:gaji|salary|expected\s*salary|gaji\s*(?:yang\s*)?diharapkan)\s*[:=\-]?\s*[Rr]?[Pp]?\.?\s*([\d.,]+)/i],
      transform: v => v.replace(/[.,]/g, '') },
];

// ====== MODAL CONTROL ======
function openSmartScan() {
    document.getElementById('smartScanModal').style.display = 'flex';
    document.getElementById('smartScanInput').value = '';
    document.getElementById('liveCounter').style.display = 'none';
    document.getElementById('fileStatus').style.display = 'none';
    document.getElementById('scanFileInput').value = '';
    setTimeout(() => document.getElementById('smartScanInput').focus(), 200);
}
function closeSmartScan() { document.getElementById('smartScanModal').style.display = 'none'; }
function clearScanInput() { document.getElementById('smartScanInput').value = ''; document.getElementById('liveCounter').style.display = 'none'; }
document.getElementById('smartScanModal').addEventListener('click', e => { if (e.target.id === 'smartScanModal') closeSmartScan(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeSmartScan(); document.getElementById('scanSuccess').style.display='none'; } });

// ====== CLIPBOARD ======
async function pasteFromClipboard() {
    try {
        const text = await navigator.clipboard.readText();
        if (text) { document.getElementById('smartScanInput').value = text; liveDetect(); }
    } catch(e) { alert('Paste manual dengan Ctrl+V.'); }
}

// ====== FILE HANDLING ======
async function handleFileUpload(input) {
    const file = input.files[0];
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) { alert('File terlalu besar! Maksimal 10MB.'); input.value = ''; return; }

    showProcessing('Membaca ' + file.name + '...', 'Mengekstrak teks dari dokumen ' + ext.toUpperCase());

    try {
        let text = '';

        if (ext === 'pdf') {
            // Check if PDF.js loaded
            if (typeof pdfjsLib === 'undefined') {
                throw new Error('Library PDF.js belum termuat. Refresh halaman dan coba lagi.');
            }
            text = await extractPDF(file);
        } else if (ext === 'docx') {
            // Check if Mammoth loaded
            if (typeof mammoth === 'undefined') {
                throw new Error('Library Mammoth.js belum termuat. Refresh halaman dan coba lagi.');
            }
            text = await extractWord(file);
        } else if (ext === 'doc') {
            hideProcessing();
            showFileStatus('error', 'Format .doc tidak didukung. Silakan konversi ke .docx terlebih dahulu, atau copy-paste isi dokumen secara manual.');
            input.value = '';
            return;
        } else {
            // Plain text files (.txt, .csv)
            text = await readFileAsText(file);
        }

        hideProcessing();

        if (!text || text.trim().length < 3) {
            showFileStatus('error', 'File tidak mengandung teks yang bisa diekstrak. PDF mungkin berupa gambar/scan — coba copy-paste manual.');
            input.value = '';
            return;
        }

        // Show extracted text in textarea
        document.getElementById('smartScanInput').value = text.trim();
        showFileStatus('success', '✓ ' + file.name + ' — ' + text.trim().length + ' karakter berhasil diekstrak');
        liveDetect();

        // Auto-apply immediately after short delay
        setTimeout(() => quickApply(), 600);

    } catch(err) {
        hideProcessing();
        console.error('Smart Scan file error:', err);
        showFileStatus('error', 'Gagal: ' + (err.message || 'Error tidak diketahui'));
    }

    input.value = '';
}

// ====== READ TEXT FILE ======
function readFileAsText(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = e => resolve(e.target.result);
        reader.onerror = () => reject(new Error('Gagal membaca file'));
        reader.readAsText(file);
    });
}

// ====== PDF EXTRACTION ======
async function extractPDF(file) {
    try {
        const arrayBuffer = await file.arrayBuffer();
        const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
        const pdf = await loadingTask.promise;
        
        let fullText = '';
        const totalPages = pdf.numPages;
        
        for (let i = 1; i <= totalPages; i++) {
            const page = await pdf.getPage(i);
            const content = await page.getTextContent();
            // Join items with proper spacing
            let lastY = null;
            let pageText = '';
            content.items.forEach(item => {
                if (lastY !== null && Math.abs(item.transform[5] - lastY) > 5) {
                    pageText += '\n'; // New line when Y position changes
                } else if (pageText.length > 0) {
                    pageText += ' ';
                }
                pageText += item.str;
                lastY = item.transform[5];
            });
            fullText += pageText + '\n';
        }
        
        return fullText;
    } catch(err) {
        console.error('PDF extraction error:', err);
        throw new Error('Gagal membaca PDF: ' + (err.message || 'Format tidak didukung'));
    }
}

// ====== WORD (.docx) EXTRACTION ======
async function extractWord(file) {
    try {
        const arrayBuffer = await file.arrayBuffer();
        const result = await mammoth.extractRawText({ arrayBuffer: arrayBuffer });
        if (result.messages && result.messages.length > 0) {
            console.warn('Mammoth warnings:', result.messages);
        }
        return result.value;
    } catch(err) {
        console.error('Word extraction error:', err);
        throw new Error('Gagal membaca Word: ' + (err.message || 'Format tidak didukung'));
    }
}

// ====== PROCESSING UI ======
function showProcessing(title, text) {
    document.getElementById('processingTitle').textContent = title;
    document.getElementById('processingText').textContent = text;
    document.getElementById('scanProcessing').style.display = 'flex';
}
function hideProcessing() { document.getElementById('scanProcessing').style.display = 'none'; }

function showFileStatus(type, msg) {
    const el = document.getElementById('fileStatus');
    const isErr = type === 'error';
    el.innerHTML = '<div style="padding:0.6rem 0.85rem; border-radius:8px; font-size:0.8rem; font-weight:600; ' +
        (isErr ? 'background:#fef2f2; color:#dc2626; border:1px solid #fecaca;' : 'background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;') +
        '"><i class="fas fa-' + (isErr ? 'exclamation-circle' : 'check-circle') + '" style="margin-right:4px;"></i>' + msg + '</div>';
    el.style.display = 'block';
}

// ====== LIVE DETECTION ======
let liveTimer = null;
function liveDetect() {
    clearTimeout(liveTimer);
    liveTimer = setTimeout(() => {
        const text = document.getElementById('smartScanInput').value.trim();
        if (!text) { document.getElementById('liveCounter').style.display = 'none'; return; }
        const count = detectAll(text).length;
        document.getElementById('liveCounter').style.display = 'flex';
        document.getElementById('liveCountText').textContent = count + ' field terdeteksi';
    }, 300);
}

// ====== CORE DETECTION ======
function detectAll(text) {
    const results = [];
    for (const def of FIELD_DEFS) {
        for (const re of def.patterns) {
            const m = text.match(re);
            if (m) {
                let val = (m[1] || m[0]).trim().replace(/[,;]$/, '').trim();
                if (def.transform) val = def.transform(val);
                if (val && val.length > 0) {
                    results.push({ ...def, value: val, displayValue: def.display ? def.display(val) : val });
                    break;
                }
            }
        }
    }
    return results;
}

// ====== QUICK APPLY (LANGSUNG ISI) ======
function quickApply() {
    const text = document.getElementById('smartScanInput').value.trim();
    if (!text) { alert('Masukkan data terlebih dahulu!'); return; }

    showProcessing('Menganalisis data...', 'Mendeteksi field dan mengisi form');

    setTimeout(() => {
        const fields = detectAll(text);
        hideProcessing();

        if (fields.length === 0) {
            alert('Tidak ada field terdeteksi.\n\nPastikan data memiliki label seperti:\nNama: ...\nEmail: ...\nHP: ...\nNIK: ...');
            return;
        }

        // Apply all fields directly
        let applied = 0;
        fields.forEach(f => {
            const el = document.querySelector('[name="' + f.key + '"]');
            if (!el) return;
            if (f.type === 'select') {
                for (let i = 0; i < el.options.length; i++) {
                    if (el.options[i].value === f.value) { el.selectedIndex = i; applied++; break; }
                }
            } else {
                el.value = f.value;
                applied++;
            }
            // Purple glow highlight
            el.style.transition = 'all 0.5s';
            el.style.boxShadow = '0 0 0 3px rgba(139,92,246,0.3)';
            el.style.borderColor = '#8b5cf6';
            el.style.background = '#faf5ff';
            setTimeout(() => { el.style.boxShadow = ''; el.style.borderColor = ''; el.style.background = ''; }, 4000);
        });

        closeSmartScan();

        // Show success overlay
        const steps = [...new Set(fields.map(f => f.step))].sort();
        document.getElementById('successDetail').textContent = applied + ' field berhasil diisi di ' + steps.length + ' step form';
        document.getElementById('successFields').innerHTML = fields.map(f => 
            '<span style="background:#dcfce7; color:#15803d; font-size:0.7rem; padding:3px 8px; border-radius:6px; font-weight:600;">' + f.label + '</span>'
        ).join('');
        document.getElementById('scanSuccess').style.display = 'flex';
    }, 800);
}

// ====== DATE PARSER ======
function smartParseDate(str) {
    const months = {jan:1,januari:1,feb:2,februari:2,mar:3,maret:3,apr:4,april:4,mei:5,may:5,jun:6,juni:6,jul:7,juli:7,agu:8,agustus:8,aug:8,sep:9,september:9,okt:10,oktober:10,oct:10,nov:11,november:11,des:12,desember:12,dec:12};
    let m = str.match(/(\d{1,2})[\s\-\/\.](\d{1,2})[\s\-\/\.](\d{2,4})/);
    if (m) { let d=parseInt(m[1]),mo=parseInt(m[2]),y=parseInt(m[3]); if(y<100)y+=1900; if(y<1950)y+=100; return y+'-'+String(mo).padStart(2,'0')+'-'+String(d).padStart(2,'0'); }
    m = str.match(/(\d{1,2})\s+([a-zA-Z]+)\s+(\d{2,4})/);
    if (m) { const mo=months[m[2].toLowerCase().substring(0,3)]; if(mo){let y=parseInt(m[3]);if(y<100)y+=1900; return y+'-'+String(mo).padStart(2,'0')+'-'+String(parseInt(m[1])).padStart(2,'0');} }
    return str;
}

// ====== DRAG & DROP ======
const dz = document.getElementById('scanDropZone');
if (dz) {
    ['dragenter','dragover'].forEach(evt => {
        dz.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); dz.style.borderColor='#7c3aed'; dz.style.background='#ede9fe'; });
    });
    dz.addEventListener('dragleave', e => { e.preventDefault(); dz.style.borderColor='#c4b5fd'; dz.style.background='linear-gradient(135deg,#f5f3ff,#ede9fe)'; });
    dz.addEventListener('drop', e => {
        e.preventDefault(); e.stopPropagation();
        dz.style.borderColor='#c4b5fd'; dz.style.background='linear-gradient(135deg,#f5f3ff,#ede9fe)';
        const file = e.dataTransfer.files[0];
        if (file) {
            // Use DataTransfer to set files on the real input
            const dt = new DataTransfer();
            dt.items.add(file);
            const input = document.getElementById('scanFileInput');
            input.files = dt.files;
            handleFileUpload(input);
        }
    });
}

// Log library status on load
console.log('[Smart Scan] PDF.js loaded:', typeof pdfjsLib !== 'undefined');
console.log('[Smart Scan] Mammoth.js loaded:', typeof mammoth !== 'undefined');
</script>
