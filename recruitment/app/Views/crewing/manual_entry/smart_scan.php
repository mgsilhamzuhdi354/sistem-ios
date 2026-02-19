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
            <p style="margin: 0.3rem 0 0 0; font-size: 0.8rem; color: #7c3aed; line-height: 1.4;">Paste data dari WhatsApp, CV, atau dokumen apapun → <b>Preview & edit</b> sebelum mengisi → deteksi 25+ field otomatis!</p>
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
                    <p style="margin:0; color:rgba(255,255,255,0.75); font-size:0.78rem;">Deteksi otomatis • Preview • Edit • Apply</p>
                </div>
                <button type="button" onclick="closeSmartScan()" style="background:rgba(255,255,255,0.15); border:none; color:white; width:34px; height:34px; border-radius:10px; cursor:pointer; font-size:1.2rem; display:flex; align-items:center; justify-content:center;">&times;</button>
            </div>
        </div>

        <div style="flex:1; overflow-y:auto;">
            
            <!-- Phase 1: Input -->
            <div id="scanPhase1" style="padding:1.5rem;">
                <!-- Action Buttons -->
                <div style="display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap;">
                    <button type="button" onclick="pasteFromClipboard()" style="flex:1; min-width:140px; padding:0.6rem; border:1px solid #ddd6fe; border-radius:10px; background:#f5f3ff; color:#6d28d9; font-size:0.8rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <i class="fas fa-clipboard"></i> Paste dari Clipboard
                    </button>
                    <label style="flex:1; min-width:140px; padding:0.6rem; border:1px solid #bfdbfe; border-radius:10px; background:#eff6ff; color:#1d4ed8; font-size:0.8rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <i class="fas fa-file-upload"></i> Upload File Teks
                        <input type="file" accept=".txt,.csv" style="display:none;" onchange="loadTextFile(this)">
                    </label>
                </div>

                <!-- Textarea -->
                <div id="scanDropZone" style="border:2px dashed #cbd5e1; border-radius:14px; transition:all 0.3s; background:#fafafa;">
                    <textarea id="smartScanInput" rows="8" placeholder="📋 Paste atau ketik data pelamar di sini...

Contoh format:
━━━━━━━━━━━━━━━━━━
Nama: Ahmad Fauzi
Email: ahmad@gmail.com
HP: 081234567890
NIK: 3201234567890001
TTL: Surabaya, 15-03-1990
Jenis Kelamin: Laki-laki
Alamat: Jl. Merdeka No.10
Gol. Darah: O
Buku Pelaut: BST-123456
Paspor: A1234567
Tinggi: 175 | Berat: 70
Rank: AB | Kapal: MV Indo Star
━━━━━━━━━━━━━━━━━━" style="width:100%; padding:1rem; border:none; background:transparent; font-family:'Inter',sans-serif; font-size:0.85rem; resize:vertical; outline:none; line-height:1.7; min-height:180px;" oninput="liveDetect()"></textarea>
                </div>

                <!-- Live Detection Counter -->
                <div id="liveCounter" style="display:none; margin-top:0.75rem; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #bbf7d0; border-radius:12px; padding:0.6rem 1rem; align-items:center; gap:8px;">
                    <div style="width:28px; height:28px; background:#22c55e; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-check" style="color:white; font-size:0.7rem;"></i>
                    </div>
                    <span id="liveCountText" style="font-size:0.82rem; font-weight:600; color:#15803d;">0 field</span>
                    <span style="margin-left:auto; font-size:0.68rem; color:#16a34a; background:#dcfce7; padding:2px 8px; border-radius:6px; font-weight:600;">⚡ Live</span>
                </div>

                <!-- Scan Button -->
                <button type="button" onclick="startScan()" style="width:100%; margin-top:1rem; padding:0.85rem; border:none; border-radius:12px; background:linear-gradient(135deg, #7c3aed, #4f46e5); color:white; font-weight:700; font-size:0.95rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 6px 20px rgba(79,70,229,0.35); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                    <i class="fas fa-search-plus"></i> Analisis & Preview Hasil
                </button>
            </div>

            <!-- Phase 2: Preview & Edit -->
            <div id="scanPhase2" style="display:none; padding:1.5rem;">
                <button type="button" onclick="backToInput()" style="background:none; border:none; color:#6d28d9; font-size:0.82rem; cursor:pointer; display:flex; align-items:center; gap:4px; margin-bottom:1rem; font-weight:600;">
                    <i class="fas fa-arrow-left"></i> Kembali edit data
                </button>

                <!-- Step Summary -->
                <div id="stepSummary" style="display:flex; gap:8px; margin-bottom:1rem; flex-wrap:wrap;"></div>

                <!-- Scanning Animation -->
                <div id="scanAnimation" style="text-align:center; padding:2rem;">
                    <div style="width:60px; height:60px; border:4px solid #e2e8f0; border-top-color:#7c3aed; border-radius:50%; animation:scanSpin 0.8s linear infinite; margin:0 auto 1rem;"></div>
                    <p style="color:#6d28d9; font-weight:600;">Menganalisis data...</p>
                    <p style="color:#94a3b8; font-size:0.78rem;">Mendeteksi pola dan mengekstrak informasi</p>
                </div>

                <!-- Detected Fields -->
                <div id="detectedFieldsContainer" style="display:none;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
                        <h4 style="margin:0; font-size:0.9rem; color:#1e293b; font-weight:700;">
                            <i class="fas fa-list-ul" style="color:#7c3aed; margin-right:4px;"></i>
                            Hasil Deteksi — <span id="detectedCount">0</span> Field
                        </h4>
                        <div style="display:flex; gap:6px;">
                            <button type="button" onclick="toggleAll(true)" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; font-size:0.7rem; padding:3px 8px; border-radius:6px; cursor:pointer; font-weight:600;">✓ Semua</button>
                            <button type="button" onclick="toggleAll(false)" style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; font-size:0.7rem; padding:3px 8px; border-radius:6px; cursor:pointer; font-weight:600;">✕ Hapus</button>
                        </div>
                    </div>
                    <div id="detectedFieldsList" style="display:flex; flex-direction:column; gap:6px; max-height:350px; overflow-y:auto; padding-right:4px;"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div id="scanFooter" style="display:none; padding:1rem 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; flex-shrink:0;">
            <div style="display:flex; gap:0.75rem;">
                <button type="button" onclick="closeSmartScan()" style="flex:1; padding:0.7rem; border:1px solid #e2e8f0; border-radius:10px; background:white; color:#64748b; font-weight:600; font-size:0.85rem; cursor:pointer;">Batal</button>
                <button type="button" onclick="applyFields()" style="flex:2; padding:0.7rem; border:none; border-radius:10px; background:linear-gradient(135deg, #22c55e, #16a34a); color:white; font-weight:700; font-size:0.9rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 15px rgba(22,163,74,0.35);">
                    <i class="fas fa-check-double"></i> Terapkan ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scanSpin { to { transform: rotate(360deg); } }
@keyframes fieldSlideIn { from { opacity:0; transform:translateX(-10px); } to { opacity:1; transform:translateX(0); } }
</style>

<script>
// ====== SMART SCAN PRO ENGINE ======
let detectedFields = [];
const FIELD_DEFS = [
    { key:'full_name', label:'Nama Lengkap', step:2, icon:'fa-user', type:'text',
      patterns: [/(?:nama\s*(?:lengkap)?|name|full\s*name)\s*[:=\-]\s*(.+)/i] },
    { key:'email', label:'Email', step:2, icon:'fa-envelope', type:'text',
      patterns: [/(?:email|e-mail)\s*[:=\-]?\s*([\w.+-]+@[\w-]+\.[\w.]+)/i, /([\w.+-]+@[\w-]+\.[\w.]+)/i] },
    { key:'phone', label:'No. Telepon', step:2, icon:'fa-phone', type:'text',
      patterns: [/(?:hp|phone|telp|telepon|no\.?\s*hp|whatsapp|wa|mobile|handphone)\s*[:=\-]?\s*([\+]?[0-9\s\-\(\)]{9,15})/i, /(?:^|\s)((?:\+62|62|08)\d[\d\s\-]{7,13})/m],
      transform: v => v.replace(/[\s\-\(\)]/g, '') },
    { key:'ktp_number', label:'NIK / KTP', step:2, icon:'fa-id-card', type:'text',
      patterns: [/(?:nik|ktp|no\.?\s*ktp|no\.?\s*nik|nomor\s*induk)\s*[:=\-]?\s*(\d{16})/i, /(?:^|\s)(\d{16})(?:\s|$)/m] },
    { key:'gender', label:'Jenis Kelamin', step:2, icon:'fa-venus-mars', type:'select',
      patterns: [/(?:jenis\s*kelamin|gender|j\.?k\.?|l\/?p|kelamin)\s*[:=\-]?\s*(laki[\s\-]*laki|perempuan|pria|wanita|male|female|lk|pr)/i],
      transform: v => /laki|pria|male|^lk$/i.test(v) ? 'Male' : 'Female',
      display: v => v === 'Male' ? 'Laki-laki' : 'Perempuan' },
    { key:'date_of_birth', label:'Tanggal Lahir', step:2, icon:'fa-calendar', type:'date',
      patterns: [/(?:ttl|tanggal\s*lahir|tgl\s*lahir|dob|born|lahir)\s*[:=\-]?\s*(?:[a-zA-Z\s]+,?\s*)?(\d{1,2}[\s\-\/\.]\d{1,2}[\s\-\/\.]\d{2,4})/i],
      transform: v => smartParseDate(v) },
    { key:'place_of_birth', label:'Tempat Lahir', step:2, icon:'fa-map-pin', type:'text',
      patterns: [/(?:ttl|tempat\s*lahir|tempat,?\s*tanggal\s*lahir)\s*[:=\-]?\s*([a-zA-Z\s]+?)(?:\s*,|\s+\d)/i,
                 /(?:tempat\s*lahir|birthplace)\s*[:=\-]?\s*(.+)/i] },
    { key:'nationality', label:'Kewarganegaraan', step:2, icon:'fa-flag', type:'text',
      patterns: [/(?:kewarganegaraan|nationality|warga\s*negara)\s*[:=\-]?\s*(.+)/i] },
    { key:'blood_type', label:'Gol. Darah', step:2, icon:'fa-tint', type:'select',
      patterns: [/(?:gol(?:ongan)?\s*darah|blood\s*(?:type)?|gd)\s*[:=\-]?\s*(AB|A|B|O)/i],
      transform: v => v.toUpperCase() },
    { key:'address', label:'Alamat', step:2, icon:'fa-home', type:'text',
      patterns: [/(?:alamat|address|domisili)\s*[:=\-]\s*(.+)/i] },
    { key:'city', label:'Kota', step:2, icon:'fa-city', type:'text',
      patterns: [/(?:kota|city|kabupaten)\s*[:=\-]\s*(.+)/i] },
    { key:'postal_code', label:'Kode Pos', step:2, icon:'fa-mail-bulk', type:'text',
      patterns: [/(?:kode\s*pos|postal\s*code|zip)\s*[:=\-]?\s*(\d{5})/i] },
    { key:'seaman_book_no', label:'No. Buku Pelaut', step:3, icon:'fa-id-badge', type:'text',
      patterns: [/(?:buku\s*pelaut|seaman\s*book|seamanbook|no\.?\s*buku\s*pelaut|BST)\s*[:=\-]?\s*([A-Z0-9\-\/]+)/i] },
    { key:'passport_no', label:'No. Paspor', step:3, icon:'fa-passport', type:'text',
      patterns: [/(?:passport|paspor|no\.?\s*paspor|no\.?\s*passport)\s*[:=\-]?\s*([A-Z0-9\-]+)/i] },
    { key:'height_cm', label:'Tinggi Badan', step:4, icon:'fa-ruler-vertical', type:'text',
      patterns: [/(?:tinggi\s*(?:badan)?|height|tb)\s*[:=\-]?\s*(\d{2,3})\s*(?:cm)?/i],
      display: v => v + ' cm' },
    { key:'weight_kg', label:'Berat Badan', step:4, icon:'fa-weight', type:'text',
      patterns: [/(?:berat\s*(?:badan)?|weight|bb)\s*[:=\-]?\s*(\d{2,3})\s*(?:kg)?/i],
      display: v => v + ' kg' },
    { key:'shoe_size', label:'Ukuran Sepatu', step:4, icon:'fa-shoe-prints', type:'text',
      patterns: [/(?:ukuran\s*sepatu|shoe\s*size|sepatu)\s*[:=\-]?\s*(\d{2})/i] },
    { key:'overall_size', label:'Ukuran Overall', step:4, icon:'fa-tshirt', type:'text',
      patterns: [/(?:ukuran\s*overall|overall\s*size|coverall)\s*[:=\-]?\s*([XSML0-9]+)/i] },
    { key:'emergency_name', label:'Kontak Darurat', step:4, icon:'fa-phone-alt', type:'text',
      patterns: [/(?:kontak\s*darurat|emergency\s*(?:contact)?(?:\s*name)?)\s*[:=\-]?\s*(.+)/i] },
    { key:'total_sea_service_months', label:'Pengalaman Laut', step:5, icon:'fa-anchor', type:'text',
      patterns: [/(?:pengalaman\s*(?:laut|berlayar)?|sea\s*service|total\s*sea)\s*[:=\-]?\s*(\d+)\s*(?:bulan|months|bln)?/i],
      display: v => v + ' bulan' },
    { key:'last_rank', label:'Rank Terakhir', step:5, icon:'fa-medal', type:'text',
      patterns: [/(?:rank|jabatan|posisi\s*terakhir|last\s*rank|pangkat)\s*[:=\-]?\s*(.+)/i] },
    { key:'last_vessel_name', label:'Kapal Terakhir', step:5, icon:'fa-ship', type:'text',
      patterns: [/(?:kapal\s*terakhir|vessel\s*(?:name)?|nama\s*kapal|ship\s*name|last\s*vessel)\s*[:=\-]?\s*(.+)/i] },
    { key:'last_vessel_type', label:'Jenis Kapal', step:5, icon:'fa-water', type:'text',
      patterns: [/(?:jenis\s*kapal|vessel\s*type|ship\s*type|type\s*kapal|tipe\s*kapal)\s*[:=\-]?\s*(.+)/i] },
    { key:'expected_salary', label:'Gaji Diharapkan', step:1, icon:'fa-money-bill', type:'text',
      patterns: [/(?:gaji|salary|expected\s*salary|gaji\s*(?:yang\s*)?diharapkan)\s*[:=\-]?\s*[Rr]?[Pp]?\.?\s*([\d.,]+)/i],
      transform: v => v.replace(/[.,]/g, '') },
];

const STEP_NAMES = {1:'Posisi', 2:'Pribadi', 3:'Dokumen', 4:'Fisik', 5:'Pengalaman'};
const STEP_COLORS = {1:'#3b82f6', 2:'#8b5cf6', 3:'#f59e0b', 4:'#10b981', 5:'#6366f1'};

function openSmartScan() {
    document.getElementById('smartScanModal').style.display = 'flex';
    document.getElementById('scanPhase1').style.display = 'block';
    document.getElementById('scanPhase2').style.display = 'none';
    document.getElementById('scanFooter').style.display = 'none';
    document.getElementById('smartScanInput').value = '';
    document.getElementById('liveCounter').style.display = 'none';
    detectedFields = [];
    setTimeout(() => document.getElementById('smartScanInput').focus(), 200);
}
function closeSmartScan() { document.getElementById('smartScanModal').style.display = 'none'; }
document.getElementById('smartScanModal').addEventListener('click', e => { if (e.target.id === 'smartScanModal') closeSmartScan(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSmartScan(); });

async function pasteFromClipboard() {
    try {
        const text = await navigator.clipboard.readText();
        if (text) { document.getElementById('smartScanInput').value = text; liveDetect(); }
    } catch(e) { alert('Tidak bisa akses clipboard. Paste manual (Ctrl+V).'); }
}

function loadTextFile(input) {
    const file = input.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('smartScanInput').value = e.target.result; liveDetect(); };
    reader.readAsText(file); input.value = '';
}

let liveTimer = null;
function liveDetect() {
    clearTimeout(liveTimer);
    liveTimer = setTimeout(() => {
        const text = document.getElementById('smartScanInput').value.trim();
        if (!text) { document.getElementById('liveCounter').style.display = 'none'; return; }
        const count = detectAll(text).length;
        document.getElementById('liveCounter').style.display = 'flex';
        document.getElementById('liveCountText').textContent = count + ' field terdeteksi real-time';
    }, 300);
}

function detectAll(text) {
    const results = [];
    for (const def of FIELD_DEFS) {
        for (const re of def.patterns) {
            const m = text.match(re);
            if (m) {
                let val = (m[1] || m[0]).trim().replace(/[,;]$/, '').trim();
                if (def.transform) val = def.transform(val);
                if (val && val.length > 0) {
                    results.push({ ...def, value: val, displayValue: def.display ? def.display(val) : val, enabled: true });
                    break;
                }
            }
        }
    }
    return results;
}

function startScan() {
    const text = document.getElementById('smartScanInput').value.trim();
    if (!text) { alert('Silakan masukkan data terlebih dahulu!'); return; }
    detectedFields = detectAll(text);
    document.getElementById('scanPhase1').style.display = 'none';
    document.getElementById('scanPhase2').style.display = 'block';
    document.getElementById('scanAnimation').style.display = 'block';
    document.getElementById('detectedFieldsContainer').style.display = 'none';
    document.getElementById('scanFooter').style.display = 'none';
    setTimeout(() => {
        document.getElementById('scanAnimation').style.display = 'none';
        document.getElementById('detectedFieldsContainer').style.display = 'block';
        document.getElementById('scanFooter').style.display = 'block';
        renderResults();
    }, 1200);
}

function renderResults() {
    const list = document.getElementById('detectedFieldsList');
    const countEl = document.getElementById('detectedCount');
    if (detectedFields.length === 0) {
        list.innerHTML = '<div style="text-align:center; padding:2rem; color:#94a3b8;"><i class="fas fa-search" style="font-size:2rem; margin-bottom:0.5rem; display:block;"></i><p style="font-weight:600;">Tidak ada data terdeteksi</p><p style="font-size:0.8rem;">Pastikan format seperti "Nama: ...", "Email: ...", dll.</p></div>';
        countEl.textContent = '0';
        document.getElementById('stepSummary').innerHTML = '';
        return;
    }
    countEl.textContent = detectedFields.length;

    // Step summary
    const stepsFound = [...new Set(detectedFields.map(f => f.step))].sort();
    document.getElementById('stepSummary').innerHTML = stepsFound.map(s => {
        const count = detectedFields.filter(f => f.step === s).length;
        return '<div style="display:flex;align-items:center;gap:5px;background:'+STEP_COLORS[s]+'15;border:1px solid '+STEP_COLORS[s]+'40;border-radius:8px;padding:4px 10px;"><span style="width:18px;height:18px;background:'+STEP_COLORS[s]+';color:white;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;">'+s+'</span><span style="font-size:0.72rem;font-weight:600;color:'+STEP_COLORS[s]+';">'+STEP_NAMES[s]+'</span><span style="font-size:0.65rem;color:'+STEP_COLORS[s]+'80;">('+count+')</span></div>';
    }).join('');

    // Field rows
    list.innerHTML = detectedFields.map((f, i) => 
        '<div style="display:flex;align-items:center;gap:10px;padding:0.6rem 0.8rem;border:1px solid #e2e8f0;border-radius:10px;background:white;animation:fieldSlideIn 0.3s ease '+(i*0.05)+'s both;">' +
        '<input type="checkbox" id="chk-'+i+'" '+(f.enabled?'checked':'')+' onchange="detectedFields['+i+'].enabled=this.checked" style="accent-color:#7c3aed;width:16px;height:16px;cursor:pointer;flex-shrink:0;">' +
        '<div style="width:28px;height:28px;background:'+STEP_COLORS[f.step]+'15;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas '+f.icon+'" style="font-size:0.7rem;color:'+STEP_COLORS[f.step]+';"></i></div>' +
        '<div style="flex:1;min-width:0;">' +
        '<div style="display:flex;align-items:center;gap:6px;"><span style="font-size:0.78rem;font-weight:600;color:#334155;">'+f.label+'</span><span style="font-size:0.6rem;background:'+STEP_COLORS[f.step]+'20;color:'+STEP_COLORS[f.step]+';padding:1px 5px;border-radius:4px;font-weight:600;">Step '+f.step+'</span></div>' +
        '<input type="text" value="'+escHtml(f.displayValue)+'" onchange="detectedFields['+i+'].value=this.value;detectedFields['+i+'].displayValue=this.value" style="width:100%;border:none;background:transparent;font-size:0.82rem;color:#0f172a;font-weight:500;padding:2px 0;outline:none;border-bottom:1px dashed #e2e8f0;">' +
        '</div></div>'
    ).join('');
}

function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function backToInput() { document.getElementById('scanPhase1').style.display = 'block'; document.getElementById('scanPhase2').style.display = 'none'; document.getElementById('scanFooter').style.display = 'none'; }
function toggleAll(state) { detectedFields.forEach((f, i) => { f.enabled = state; const c = document.getElementById('chk-'+i); if (c) c.checked = state; }); }

function applyFields() {
    const toApply = detectedFields.filter(f => f.enabled);
    if (!toApply.length) { alert('Tidak ada field dipilih!'); return; }
    toApply.forEach(f => {
        const el = document.querySelector('[name="'+f.key+'"]');
        if (!el) return;
        if (f.type === 'select') {
            for (let i = 0; i < el.options.length; i++) { if (el.options[i].value === f.value) { el.selectedIndex = i; break; } }
        } else { el.value = f.value; }
        el.style.transition = 'all 0.5s';
        el.style.boxShadow = '0 0 0 3px rgba(139,92,246,0.3)';
        el.style.borderColor = '#8b5cf6';
        setTimeout(() => { el.style.boxShadow = ''; el.style.borderColor = ''; }, 3000);
    });
    closeSmartScan();
    const summary = toApply.map(f => '✓ ' + f.label + ': ' + f.displayValue).join('\n');
    const steps = [...new Set(toApply.map(f => f.step))].sort();
    alert('✅ Smart Scan Pro Berhasil!\n\n' + toApply.length + ' field diisi di ' + steps.length + ' step:\n' + summary + '\n\nSilakan cek setiap step untuk verifikasi.');
}

function smartParseDate(str) {
    const months = {jan:1,januari:1,feb:2,februari:2,mar:3,maret:3,apr:4,april:4,mei:5,may:5,jun:6,juni:6,jul:7,juli:7,agu:8,agustus:8,aug:8,sep:9,september:9,okt:10,oktober:10,oct:10,nov:11,november:11,des:12,desember:12,dec:12};
    let m = str.match(/(\d{1,2})[\s\-\/\.](\d{1,2})[\s\-\/\.](\d{2,4})/);
    if (m) { let d=parseInt(m[1]),mo=parseInt(m[2]),y=parseInt(m[3]); if(y<100)y+=1900; if(y<1950)y+=100; return y+'-'+String(mo).padStart(2,'0')+'-'+String(d).padStart(2,'0'); }
    m = str.match(/(\d{1,2})\s+([a-zA-Z]+)\s+(\d{2,4})/);
    if (m) { const mo=months[m[2].toLowerCase().substring(0,3)]; if(mo){let y=parseInt(m[3]);if(y<100)y+=1900; return y+'-'+String(mo).padStart(2,'0')+'-'+String(parseInt(m[1])).padStart(2,'0');} }
    return str;
}

// Drag & Drop
const dz = document.getElementById('scanDropZone');
if (dz) {
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='#7c3aed'; dz.style.background='#f5f3ff'; });
    dz.addEventListener('dragleave', () => { dz.style.borderColor='#cbd5e1'; dz.style.background='#fafafa'; });
    dz.addEventListener('drop', e => {
        e.preventDefault(); dz.style.borderColor='#cbd5e1'; dz.style.background='#fafafa';
        const f = e.dataTransfer.files[0];
        if (f) { const r=new FileReader(); r.onload=ev=>{document.getElementById('smartScanInput').value=ev.target.result;liveDetect();}; r.readAsText(f); }
    });
}
</script>
