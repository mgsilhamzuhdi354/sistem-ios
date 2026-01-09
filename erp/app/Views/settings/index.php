<?php
/**
 * Settings View - Full Translation Support
 */
$currentPage = 'settings';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-cog"></i> <span data-translate="settings_title">Pengaturan</span></h1>
        <p data-translate="settings_subtitle">Konfigurasi sistem dan preferensi</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= BASE_URL ?>settings/init" class="btn btn-secondary">
            <i class="fas fa-sync"></i> <span data-translate="reset_defaults">Reset Default</span>
        </a>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>settings/save">
    <div class="grid-2" style="gap: 24px;">

        <!-- General Settings -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-building" style="color: var(--accent-gold);"></i> <span data-translate="company_info">Informasi Perusahaan</span>
            </h3>
            <?php 
            $generalSettings = [];
            foreach ($settings['general'] ?? [] as $s) { $generalSettings[$s['setting_key']] = $s['setting_value']; }
            ?>
            <div class="form-group">
                <label class="form-label" data-translate="company_name">Nama Perusahaan</label>
                <input type="text" name="settings[company_name]" class="form-control" 
                       value="<?= htmlspecialchars($generalSettings['company_name'] ?? 'PT Indo Ocean') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="company_email">Email</label>
                <input type="email" name="settings[company_email]" class="form-control" 
                       value="<?= htmlspecialchars($generalSettings['company_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="company_phone">Telepon</label>
                <input type="text" name="settings[company_phone]" class="form-control" 
                       value="<?= htmlspecialchars($generalSettings['company_phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="company_address">Alamat</label>
                <textarea name="settings[company_address]" class="form-control" rows="2"><?= htmlspecialchars($generalSettings['company_address'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Currency & Tax Settings -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-dollar-sign" style="color: var(--success);"></i> <span data-translate="currency_tax">Mata Uang & Pajak</span>
            </h3>
            <?php 
            $currencySettings = [];
            foreach ($settings['currency'] ?? [] as $s) { $currencySettings[$s['setting_key']] = $s['setting_value']; }
            $taxSettings = [];
            foreach ($settings['tax'] ?? [] as $s) { $taxSettings[$s['setting_key']] = $s['setting_value']; }
            ?>
            <div class="form-group">
                <label class="form-label" data-translate="default_currency">Mata Uang Default</label>
                <select name="settings[default_currency]" class="form-control">
                    <option value="USD" <?= ($currencySettings['default_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD - US Dollar</option>
                    <option value="IDR" <?= ($currencySettings['default_currency'] ?? '') === 'IDR' ? 'selected' : '' ?>>IDR - Indonesian Rupiah</option>
                    <option value="SGD" <?= ($currencySettings['default_currency'] ?? '') === 'SGD' ? 'selected' : '' ?>>SGD - Singapore Dollar</option>
                    <option value="EUR" <?= ($currencySettings['default_currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="currency_position">Posisi Mata Uang</label>
                <select name="settings[currency_position]" class="form-control">
                    <option value="before" <?= ($currencySettings['currency_position'] ?? '') === 'before' ? 'selected' : '' ?> data-translate="before_amount">Sebelum jumlah ($100)</option>
                    <option value="after" <?= ($currencySettings['currency_position'] ?? '') === 'after' ? 'selected' : '' ?> data-translate="after_amount">Setelah jumlah (100$)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="default_tax_rate">Tarif Pajak Default (%)</label>
                <input type="number" name="settings[default_tax_rate]" class="form-control" step="0.1"
                       value="<?= htmlspecialchars($taxSettings['default_tax_rate'] ?? '5') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="tax_calculation">Dasar Perhitungan Pajak</label>
                <select name="settings[tax_calculation]" class="form-control">
                    <option value="gross" <?= ($taxSettings['tax_calculation'] ?? '') === 'gross' ? 'selected' : '' ?> data-translate="gross_salary">Gaji Kotor</option>
                    <option value="net" <?= ($taxSettings['tax_calculation'] ?? '') === 'net' ? 'selected' : '' ?> data-translate="net_salary">Gaji Bersih (setelah potongan)</option>
                </select>
            </div>
        </div>

        <!-- Contract Settings -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-file-contract" style="color: var(--info);"></i> <span data-translate="contract_settings">Pengaturan Kontrak</span>
            </h3>
            <?php 
            $contractSettings = [];
            foreach ($settings['contract'] ?? [] as $s) { $contractSettings[$s['setting_key']] = $s['setting_value']; }
            ?>
            <div class="form-group">
                <label class="form-label" data-translate="contract_prefix">Awalan Nomor Kontrak</label>
                <input type="text" name="settings[contract_prefix]" class="form-control" 
                       value="<?= htmlspecialchars($contractSettings['contract_prefix'] ?? 'CTR') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="default_duration">Durasi Default (bulan)</label>
                <input type="number" name="settings[default_duration]" class="form-control" 
                       value="<?= htmlspecialchars($contractSettings['default_duration'] ?? '6') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="expiry_alert_days">Hari Peringatan Berakhir</label>
                <input type="text" name="settings[expiry_alert_days]" class="form-control" 
                       value="<?= htmlspecialchars($contractSettings['expiry_alert_days'] ?? '30,14,7') ?>"
                       placeholder="30,14,7">
                <small style="color: var(--text-muted);" data-translate="expiry_alert_hint">Hari sebelum berakhir untuk menampilkan peringatan</small>
            </div>
        </div>

        <!-- Payroll Settings -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-money-bill-wave" style="color: var(--warning);"></i> <span data-translate="payroll_settings">Pengaturan Penggajian</span>
            </h3>
            <?php 
            $payrollSettings = [];
            foreach ($settings['payroll'] ?? [] as $s) { $payrollSettings[$s['setting_key']] = $s['setting_value']; }
            ?>
            <div class="form-group">
                <label class="form-label" data-translate="payroll_day">Hari Pemrosesan Gaji</label>
                <input type="number" name="settings[payroll_day]" class="form-control" min="1" max="31"
                       value="<?= htmlspecialchars($payrollSettings['payroll_day'] ?? '25') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="auto_generate">Otomatis Generate Payroll</label>
                <select name="settings[auto_generate_payroll]" class="form-control">
                    <option value="0" <?= ($payrollSettings['auto_generate_payroll'] ?? '0') === '0' ? 'selected' : '' ?> data-translate="disabled">Nonaktif</option>
                    <option value="1" <?= ($payrollSettings['auto_generate_payroll'] ?? '0') === '1' ? 'selected' : '' ?> data-translate="enabled">Aktif</option>
                </select>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="card" style="grid-column: span 2;">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-bell" style="color: var(--danger);"></i> <span data-translate="notification_settings">Pengaturan Notifikasi</span>
            </h3>
            <?php 
            $notifSettings = [];
            foreach ($settings['notification'] ?? [] as $s) { $notifSettings[$s['setting_key']] = $s['setting_value']; }
            ?>
            <div class="grid-3" style="gap: 20px;">
                <div class="form-group">
                    <label class="form-label" data-translate="email_notifications">Notifikasi Email</label>
                    <select name="settings[email_notifications]" class="form-control">
                        <option value="1" <?= ($notifSettings['email_notifications'] ?? '1') === '1' ? 'selected' : '' ?> data-translate="enabled">Aktif</option>
                        <option value="0" <?= ($notifSettings['email_notifications'] ?? '1') === '0' ? 'selected' : '' ?> data-translate="disabled">Nonaktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate="contract_expiry_alerts">Peringatan Kontrak Berakhir</label>
                    <select name="settings[contract_expiry_notify]" class="form-control">
                        <option value="1" <?= ($notifSettings['contract_expiry_notify'] ?? '1') === '1' ? 'selected' : '' ?> data-translate="enabled">Aktif</option>
                        <option value="0" <?= ($notifSettings['contract_expiry_notify'] ?? '1') === '0' ? 'selected' : '' ?> data-translate="disabled">Nonaktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate="payroll_complete_alerts">Peringatan Payroll Selesai</label>
                    <select name="settings[payroll_complete_notify]" class="form-control">
                        <option value="1" <?= ($notifSettings['payroll_complete_notify'] ?? '1') === '1' ? 'selected' : '' ?> data-translate="enabled">Aktif</option>
                        <option value="0" <?= ($notifSettings['payroll_complete_notify'] ?? '1') === '0' ? 'selected' : '' ?> data-translate="disabled">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>

            <!-- Appearance Settings -->
        <div class="card" style="grid-column: span 2;">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-palette" style="color: var(--accent-gold);"></i> <span data-translate="appearance">Tampilan</span>
            </h3>
            <div class="grid-2" style="gap: 24px;">
                <!-- Language Selection -->
                <div>
                    <label class="form-label" data-translate="language">Bahasa</label>
                    <select id="languageSelect" class="form-control" onchange="setLanguage(this.value); location.reload();">
                        <option value="id">🇮🇩 Bahasa Indonesia</option>
                        <option value="en">🇺🇸 English</option>
                    </select>
                    <small style="color: var(--text-muted); margin-top: 6px; display: block;" data-translate="lang_hint">Pilih bahasa tampilan</small>
                </div>
                
                <!-- Theme Color Selection -->
                <div>
                    <label class="form-label" data-translate="theme_color">Warna Tema</label>
                    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-top: 8px;">
                        <button type="button" onclick="setTheme('gold')" class="theme-btn" data-theme="gold" 
                                style="width: 100%; aspect-ratio: 1; border-radius: 12px; background: linear-gradient(135deg, #D4AF37, #E8C547); border: 3px solid transparent; cursor: pointer; transition: all 0.2s;"
                                data-translate-title="theme_gold" title="Emas (Default)"></button>
                        <button type="button" onclick="setTheme('blue')" class="theme-btn" data-theme="blue"
                                style="width: 100%; aspect-ratio: 1; border-radius: 12px; background: linear-gradient(135deg, #3B82F6, #60A5FA); border: 3px solid transparent; cursor: pointer; transition: all 0.2s;"
                                data-translate-title="theme_blue" title="Biru Laut"></button>
                        <button type="button" onclick="setTheme('green')" class="theme-btn" data-theme="green"
                                style="width: 100%; aspect-ratio: 1; border-radius: 12px; background: linear-gradient(135deg, #10B981, #34D399); border: 3px solid transparent; cursor: pointer; transition: all 0.2s;"
                                data-translate-title="theme_green" title="Hijau Zamrud"></button>
                        <button type="button" onclick="setTheme('purple')" class="theme-btn" data-theme="purple"
                                style="width: 100%; aspect-ratio: 1; border-radius: 12px; background: linear-gradient(135deg, #8B5CF6, #A78BFA); border: 3px solid transparent; cursor: pointer; transition: all 0.2s;"
                                data-translate-title="theme_purple" title="Ungu Royal"></button>
                        <button type="button" onclick="setTheme('red')" class="theme-btn" data-theme="red"
                                style="width: 100%; aspect-ratio: 1; border-radius: 12px; background: linear-gradient(135deg, #EF4444, #F87171); border: 3px solid transparent; cursor: pointer; transition: all 0.2s;"
                                data-translate-title="theme_red" title="Merah Rubi"></button>
                        <button type="button" onclick="setTheme('teal')" class="theme-btn" data-theme="teal"
                                style="width: 100%; aspect-ratio: 1; border-radius: 12px; background: linear-gradient(135deg, #14B8A6, #2DD4BF); border: 3px solid transparent; cursor: pointer; transition: all 0.2s;"
                                data-translate-title="theme_teal" title="Hijau Toska"></button>
                    </div>
                    <small style="color: var(--text-muted); margin-top: 8px; display: block;" data-translate="theme_hint">Klik untuk mengubah warna tema</small>
                </div>
            </div>
        </div>

    </div>

    <div style="margin-top: 24px; text-align: right;">
        <button type="submit" class="btn btn-primary" style="min-width: 200px;">
            <i class="fas fa-save"></i> <span data-translate="save_settings">Simpan Pengaturan</span>
        </button>
    </div>
</form>

<!-- Backup & Restore Section -->
<div class="card" style="margin-top: 32px; border: 2px solid var(--info); background: rgba(59, 130, 246, 0.05);">
    <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: var(--info);">
        <i class="fas fa-database"></i> <span data-translate="backup_restore">Backup & Restore</span>
    </h3>
    <p style="color: var(--text-secondary); margin-bottom: 20px;" data-translate="backup_hint">
        Backup data Anda sebelum melakukan reset atau perubahan besar. File backup dalam format JSON dapat diimport kembali kapan saja.
    </p>
    
    <div class="grid-2" style="gap: 24px;">
        <!-- Export Section -->
        <div style="padding: 20px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-download" style="color: var(--success);"></i> <span data-translate="export_data">Ekspor Data</span>
            </h4>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px;" data-translate="export_hint">
                Download backup semua data (kontrak, payroll, settings, dll) dalam format JSON.
            </p>
            <a href="<?= BASE_URL ?>settings/export" class="btn btn-primary">
                <i class="fas fa-file-download"></i> <span data-translate="download_backup">Download Backup</span>
            </a>
        </div>
        
        <!-- Import Section -->
        <div style="padding: 20px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
            <h4 style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-upload" style="color: var(--warning);"></i> <span data-translate="import_data">Impor Data</span>
            </h4>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 16px;" data-translate="import_hint">
                Restore data dari file backup JSON. Data yang ada akan ditimpa.
            </p>
            <form method="POST" action="<?= BASE_URL ?>settings/import" enctype="multipart/form-data" id="importForm">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <input type="file" name="import_file" id="importFile" accept=".json" 
                           style="flex: 1; padding: 8px; background: var(--bg-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
                    <button type="button" class="btn btn-secondary" onclick="confirmImport()">
                        <i class="fas fa-file-upload"></i> <span data-translate="import_btn">Impor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Danger Zone -->
<div class="card" style="margin-top: 32px; border: 2px solid var(--danger); background: rgba(239, 68, 68, 0.05);">
    <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: var(--danger);">
        <i class="fas fa-exclamation-triangle"></i> <span data-translate="danger_zone">Zona Berbahaya</span>
    </h3>
    <p style="color: var(--text-secondary); margin-bottom: 20px;" data-translate="danger_hint">
        Tindakan di bawah ini bersifat destruktif dan tidak dapat dibatalkan. Harap pastikan Anda yakin sebelum melanjutkan.
    </p>
    
    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary" onclick="showConfirmModal('payroll', t('delete_payroll_title'), t('confirm_delete_payroll'))">
            <i class="fas fa-money-bill-wave"></i> <span data-translate="reset_payroll">Reset Payroll</span>
        </button>
        
        <button type="button" class="btn btn-secondary" onclick="showConfirmModal('contracts', t('delete_contracts_title'), t('confirm_delete_contracts'))">
            <i class="fas fa-file-contract"></i> <span data-translate="delete_contracts">Hapus Kontrak</span>
        </button>
        
        <button type="button" class="btn btn-secondary" onclick="showConfirmModal('notifications', t('delete_notifications_title'), t('confirm_delete_notifications'))">
            <i class="fas fa-bell"></i> <span data-translate="delete_notifications">Hapus Notifikasi</span>
        </button>
        
        <button type="button" class="btn" style="background: var(--danger); color: white;" onclick="showConfirmModal('all', t('delete_all_title'), t('confirm_delete_all'))">
            <i class="fas fa-trash-alt"></i> <span data-translate="delete_all_data">Hapus Semua Data</span>
        </button>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: var(--bg-dark); border-radius: 16px; padding: 32px; max-width: 500px; width: 90%; border: 1px solid var(--border-color);">
        <h3 id="modalTitle" style="color: var(--danger); margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-triangle"></i> <span></span>
        </h3>
        <p id="modalMessage" style="color: var(--text-secondary); margin-bottom: 24px;"></p>
        
        <div style="background: rgba(239, 68, 68, 0.1); padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 13px;">
                <span data-translate="type_confirm">Ketik</span> <strong style="color: var(--danger);">HAPUS</strong> <span data-translate="to_confirm">untuk konfirmasi:</span>
            </label>
            <input type="text" id="confirmInput" class="form-control" placeholder="Ketik HAPUS" autocomplete="off" style="border-color: var(--danger);">
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">
                <i class="fas fa-times"></i> <span data-translate="cancel">Batal</span>
            </button>
            <button type="button" id="confirmBtn" class="btn" style="background: var(--danger); color: white;" onclick="executeDelete()" disabled>
                <i class="fas fa-trash-alt"></i> <span data-translate="delete_now">Hapus Sekarang</span>
            </button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="<?= BASE_URL ?>settings/delete-data" style="display: none;">
    <input type="hidden" name="delete_type" id="deleteType" value="">
    <input type="hidden" name="confirm_code" id="confirmCode" value="">
</form>

<script>
let currentDeleteType = '';

function showConfirmModal(type, title, message) {
    currentDeleteType = type;
    document.getElementById('modalTitle').querySelector('span').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('confirmInput').value = '';
    document.getElementById('confirmBtn').disabled = true;
    document.getElementById('confirmModal').style.display = 'flex';
    document.getElementById('confirmInput').focus();
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    currentDeleteType = '';
}

document.getElementById('confirmInput').addEventListener('input', function(e) {
    const isValid = e.target.value.toUpperCase() === 'HAPUS';
    document.getElementById('confirmBtn').disabled = !isValid;
});

function executeDelete() {
    if (document.getElementById('confirmInput').value.toUpperCase() !== 'HAPUS') {
        return;
    }
    
    document.getElementById('deleteType').value = currentDeleteType;
    document.getElementById('confirmCode').value = 'HAPUS';
    document.getElementById('deleteForm').submit();
}

// Close modal on outside click
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirmModal();
    }
});

// Import confirmation function
function confirmImport() {
    const fileInput = document.getElementById('importFile');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        alert(t('select_file_first'));
        return;
    }
    
    const file = fileInput.files[0];
    
    if (!file.name.toLowerCase().endsWith('.json')) {
        alert(t('file_must_be_json'));
        return;
    }
    
    const confirmed = confirm(
        t('import_warning') + '\n\n' +
        t('file') + ': ' + file.name + '\n' +
        t('size') + ': ' + (file.size / 1024).toFixed(2) + ' KB\n\n' +
        t('confirm_import')
    );
    
    if (confirmed) {
        document.getElementById('importForm').submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
