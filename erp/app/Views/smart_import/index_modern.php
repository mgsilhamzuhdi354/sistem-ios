<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
<div style="margin-left:256px;min-height:100vh;background:#f8fafc;">
  <!-- Header -->
  <div style="padding:24px 32px 0;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <div>
        <h1 style="font-size:24px;font-weight:700;color:#1e293b;margin:0;">Smart Import</h1>
        <p style="color:#64748b;margin:4px 0 0;font-size:14px;">Import data kru dari file Excel secara otomatis</p>
      </div>
    </div>
  </div>

  <div style="padding:24px 32px;">
    <?php if (!empty($flash)): ?>
      <div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;background:<?= $flash['type']==='error' ? '#fef2f2' : '#f0fdf4' ?>;color:<?= $flash['type']==='error' ? '#dc2626' : '#16a34a' ?>;font-size:14px;">
        <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Upload Card -->
    <div style="background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);border:1px solid #e2e8f0;max-width:800px;margin:0 auto;">
      <div style="padding:32px;">
        <form method="POST" action="<?= BASE_URL ?>SmartImport/preview" enctype="multipart/form-data" id="uploadForm">

          <!-- Drag & Drop Area -->
          <div id="dropZone" style="border:2px dashed #cbd5e1;border-radius:12px;padding:48px 32px;text-align:center;cursor:pointer;transition:all 0.3s ease;background:#fafbfc;" onclick="document.getElementById('fileInput').click()">
            <div style="margin-bottom:16px;">
              <span class="material-icons" style="font-size:56px;color:#94a3b8;">cloud_upload</span>
            </div>
            <p style="font-size:16px;font-weight:600;color:#1e293b;margin:0 0 8px;">Drag & drop file Excel di sini</p>
            <p style="font-size:13px;color:#94a3b8;margin:0 0 16px;">atau klik untuk memilih file</p>
            <span style="display:inline-block;padding:8px 20px;background:#2563eb;color:#fff;border-radius:8px;font-size:13px;font-weight:600;">Pilih File</span>
            <p style="font-size:11px;color:#94a3b8;margin:12px 0 0;">Format: .xlsx, .xls, .csv — Max 10MB</p>
          </div>

          <input type="file" id="fileInput" name="excel_file" accept=".xlsx,.xls,.csv" style="display:none;" onchange="handleFileSelect(this)">

          <!-- File Info (hidden until selected) -->
          <div id="fileInfo" style="display:none;margin-top:16px;padding:16px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <span class="material-icons" style="color:#0284c7;font-size:28px;">description</span>
              <div style="flex:1;">
                <div id="fileName" style="font-weight:600;color:#1e293b;font-size:14px;"></div>
                <div id="fileSize" style="font-size:12px;color:#64748b;"></div>
              </div>
              <button type="button" onclick="clearFile()" style="background:none;border:none;cursor:pointer;color:#94a3b8;">
                <span class="material-icons" style="font-size:20px;">close</span>
              </button>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" id="submitBtn" disabled style="margin-top:20px;width:100%;padding:14px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;opacity:0.5;transition:all 0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
            <span class="material-icons" style="font-size:18px;vertical-align:middle;margin-right:6px;">auto_fix_high</span>
            Analisa & Preview Data
          </button>
        </form>
      </div>

      <!-- Info Section -->
      <div style="padding:24px 32px;border-top:1px solid #f1f5f9;background:#fafbfc;border-radius:0 0 16px 16px;">
        <h3 style="font-size:14px;font-weight:600;color:#1e293b;margin:0 0 12px;">
          <span class="material-icons" style="font-size:16px;vertical-align:middle;color:#2563eb;">info</span>
          Kolom Excel yang Didukung
        </h3>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;font-size:12px;color:#64748b;">
          <div>✅ NAME (First + Last)</div>
          <div>✅ CERTIFICATE / Rank</div>
          <div>✅ COMPANY (Client)</div>
          <div>✅ VESSEL, IMO, FLAG</div>
          <div>✅ JOINT DATE</div>
          <div>✅ FINISH CONTRACT</div>
          <div>✅ DATE OF BIRTH</div>
          <div>✅ ADDRESS, PHONE</div>
          <div>✅ EMERGENCY CONTACT</div>
          <div>✅ PASSPORT (No + Exp)</div>
          <div>✅ SEAMAN BOOK (No + Exp)</div>
          <div>✅ MCU Expiry</div>
          <div>✅ BANK ACCOUNT</div>
          <div>✅ EMAIL</div>
          <div>✅ CURRENCY</div>
          <div>✅ SALARY PAYROLL</div>
          <div>✅ SALARY INVOICES</div>
          <div>✅ STATUS, PIC, NOTE</div>
        </div>
      </div>
    </div>

    <!-- Flow Diagram -->
    <div style="max-width:800px;margin:24px auto 0;">
      <div style="background:#fff;border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.06);border:1px solid #e2e8f0;">
        <h3 style="font-size:13px;font-weight:600;color:#1e293b;margin:0 0 12px;">Import Flow</h3>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:12px;">
          <span style="padding:6px 10px;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-weight:600;">1. Upload</span>
          <span class="material-icons" style="font-size:16px;color:#cbd5e1;">arrow_forward</span>
          <span style="padding:6px 10px;background:#e0e7ff;color:#4338ca;border-radius:6px;font-weight:600;">2. Auto-Detect Mapping</span>
          <span class="material-icons" style="font-size:16px;color:#cbd5e1;">arrow_forward</span>
          <span style="padding:6px 10px;background:#fef3c7;color:#92400e;border-radius:6px;font-weight:600;">3. Validate</span>
          <span class="material-icons" style="font-size:16px;color:#cbd5e1;">arrow_forward</span>
          <span style="padding:6px 10px;background:#dcfce7;color:#166534;border-radius:6px;font-weight:600;">4. Import</span>
        </div>
      </div>
    <!-- Danger Zone -->
    <div style="max-width:800px;margin:24px auto 0;">
      <div style="background:linear-gradient(135deg,#fef2f2,#fff1f2);border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.06);border:1px solid #fecaca;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <div>
            <h3 style="font-size:14px;font-weight:600;color:#991b1b;margin:0 0 4px;">
              <span class="material-icons" style="font-size:16px;vertical-align:middle;color:#dc2626;">warning</span>
              Danger Zone
            </h3>
            <p style="font-size:12px;color:#b91c1c;margin:0;">Hapus semua data import (crew, kontrak, vessel, client, dokumen) untuk memulai ulang</p>
          </div>
          <button type="button" onclick="openPurgeModal()" style="padding:10px 20px;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;transition:all 0.2s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
            <span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px;">delete_forever</span>
            Hapus Semua Data
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Purge Confirmation Modal -->
<div id="purgeModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:16px;max-width:480px;width:90%;box-shadow:0 25px 50px rgba(0,0,0,0.25);animation:modalIn 0.25s ease;">
    <!-- Header -->
    <div style="padding:24px 24px 16px;text-align:center;">
      <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#fef2f2,#fee2e2);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
        <span class="material-icons" style="font-size:28px;color:#dc2626;">delete_forever</span>
      </div>
      <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 4px;">Hapus Semua Data?</h3>
      <p style="font-size:13px;color:#64748b;margin:0;">Tindakan ini tidak dapat dibatalkan!</p>
    </div>

    <!-- Stats -->
    <div id="purgeStatsContainer" style="padding:0 24px;">
      <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px;">
        <div id="purgeStatsLoading" style="text-align:center;color:#94a3b8;font-size:13px;">
          <span class="material-icons" style="font-size:20px;animation:spin 1s linear infinite;">refresh</span>
          Menghitung data...
        </div>
        <div id="purgeStatsData" style="display:none;">
          <div style="font-size:12px;font-weight:600;color:#991b1b;margin-bottom:8px;">Data yang akan dihapus:</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:13px;color:#b91c1c;">
            <div>👤 Crews: <strong id="statCrews">0</strong></div>
            <div>📄 Kontrak: <strong id="statContracts">0</strong></div>
            <div>💰 Salary: <strong id="statSalaries">0</strong></div>
            <div>📋 Dokumen: <strong id="statDocs">0</strong></div>
            <div>🚢 Vessels: <strong id="statVessels">0</strong></div>
            <div>🏢 Clients: <strong id="statClients">0</strong></div>
          </div>
          <div style="margin-top:8px;padding-top:8px;border-top:1px solid #fecaca;font-size:14px;font-weight:700;color:#991b1b;text-align:center;">
            Total: <span id="statTotal">0</span> records
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Input -->
    <div style="padding:16px 24px;">
      <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:6px;">
        Ketik <span style="color:#dc2626;font-weight:700;">HAPUS</span> untuk konfirmasi:
      </label>
      <input type="text" id="purgeConfirmInput" autocomplete="off" placeholder="Ketik HAPUS di sini..."
        style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color 0.2s;"
        oninput="checkPurgeConfirm()" onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e2e8f0'">
    </div>

    <!-- Actions -->
    <div style="padding:0 24px 24px;display:flex;gap:12px;">
      <button type="button" onclick="closePurgeModal()" style="flex:1;padding:12px;background:#f1f5f9;color:#475569;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        Batal
      </button>
      <form method="POST" action="<?= BASE_URL ?>SmartImport/purgeAll" style="flex:1;margin:0;">
        <input type="hidden" name="confirm_text" id="purgeConfirmHidden" value="">
        <button type="submit" id="purgeSubmitBtn" disabled
          style="width:100%;padding:12px;background:#dc2626;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:not-allowed;opacity:0.4;transition:all 0.2s;">
          <span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px;">delete_forever</span>
          Hapus Semua
        </button>
      </form>
    </div>
  </div>
</div>

<style>
@keyframes modalIn { from { transform:scale(0.9);opacity:0; } to { transform:scale(1);opacity:1; } }
@keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
</style>

<script>
const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='#2563eb'; dz.style.background='#eff6ff'; });
dz.addEventListener('dragleave', () => { dz.style.borderColor='#cbd5e1'; dz.style.background='#fafbfc'; });
dz.addEventListener('drop', e => {
  e.preventDefault();
  dz.style.borderColor='#cbd5e1'; dz.style.background='#fafbfc';
  const f = e.dataTransfer.files[0];
  if (f) { document.getElementById('fileInput').files = e.dataTransfer.files; handleFileSelect(document.getElementById('fileInput')); }
});

function handleFileSelect(input) {
  const f = input.files[0];
  if (!f) return;
  document.getElementById('fileName').textContent = f.name;
  document.getElementById('fileSize').textContent = (f.size / 1024).toFixed(1) + ' KB';
  document.getElementById('fileInfo').style.display = 'block';
  document.getElementById('dropZone').style.display = 'none';
  const btn = document.getElementById('submitBtn');
  btn.disabled = false;
  btn.style.opacity = '1';
}

function clearFile() {
  document.getElementById('fileInput').value = '';
  document.getElementById('fileInfo').style.display = 'none';
  document.getElementById('dropZone').style.display = 'block';
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.style.opacity = '0.5';
}

// === Purge Modal ===
function openPurgeModal() {
  const modal = document.getElementById('purgeModal');
  modal.style.display = 'flex';
  document.getElementById('purgeConfirmInput').value = '';
  document.getElementById('purgeConfirmHidden').value = '';
  document.getElementById('purgeSubmitBtn').disabled = true;
  document.getElementById('purgeSubmitBtn').style.opacity = '0.4';
  document.getElementById('purgeSubmitBtn').style.cursor = 'not-allowed';
  document.getElementById('purgeStatsLoading').style.display = 'block';
  document.getElementById('purgeStatsData').style.display = 'none';

  // Fetch stats
  fetch('<?= BASE_URL ?>SmartImport/purgeStats')
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const s = data.stats;
        document.getElementById('statCrews').textContent = s.crews || 0;
        document.getElementById('statContracts').textContent = s.contracts || 0;
        document.getElementById('statSalaries').textContent = s.contract_salaries || 0;
        document.getElementById('statDocs').textContent = s.crew_documents || 0;
        document.getElementById('statVessels').textContent = s.vessels || 0;
        document.getElementById('statClients').textContent = s.clients || 0;
        const total = (s.crews||0) + (s.contracts||0) + (s.contract_salaries||0) + (s.crew_documents||0) + (s.vessels||0) + (s.clients||0);
        document.getElementById('statTotal').textContent = total.toLocaleString();
        document.getElementById('purgeStatsLoading').style.display = 'none';
        document.getElementById('purgeStatsData').style.display = 'block';
      }
    }).catch(() => {
      document.getElementById('purgeStatsLoading').innerHTML = '<span style="color:#dc2626;">Gagal memuat data</span>';
    });
}

function closePurgeModal() {
  document.getElementById('purgeModal').style.display = 'none';
}

function checkPurgeConfirm() {
  const val = document.getElementById('purgeConfirmInput').value.trim();
  const btn = document.getElementById('purgeSubmitBtn');
  const hidden = document.getElementById('purgeConfirmHidden');
  if (val === 'HAPUS') {
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';
    hidden.value = 'HAPUS';
  } else {
    btn.disabled = true;
    btn.style.opacity = '0.4';
    btn.style.cursor = 'not-allowed';
    hidden.value = '';
  }
}

// Close modal on overlay click
document.getElementById('purgeModal')?.addEventListener('click', function(e) {
  if (e.target === this) closePurgeModal();
});
</script>
