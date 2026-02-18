<!-- Admin Email Settings -->
<style>
    .es-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; padding: 25px 30px; border-radius: 16px; margin-bottom: 24px;
    }
    .es-header h2 { margin: 0 0 4px; font-weight: 700; font-size: 1.4rem; }
    .es-header small { opacity: 0.8; }

    .es-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; }
    .es-stat {
        background: white; border-radius: 14px; padding: 20px; text-align: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .es-stat .num { font-size: 2rem; font-weight: 800; }
    .es-stat .lbl { font-size: 0.82rem; color: #6b7280; margin-top: 4px; }
    .es-stat.sent .num { color: #16a34a; }
    .es-stat.pending .num { color: #d97706; }
    .es-stat.failed .num { color: #dc2626; }

    .es-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
    .es-tab {
        padding: 10px 22px; background: white; border: 2px solid #e5e7eb; border-radius: 10px;
        cursor: pointer; font-weight: 600; color: #6b7280; transition: all 0.3s; font-size: 0.88rem; border: 2px solid transparent;
    }
    .es-tab.active { background: #1e3a5f; color: white; }
    .es-tab:hover:not(.active) { border-color: #1e3a5f; color: #1e3a5f; background: white; }

    .es-panel { display: none; }
    .es-panel.active { display: block; }

    .es-card {
        background: white; border-radius: 16px; padding: 28px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin-bottom: 20px;
    }
    .es-card h3 { margin: 0 0 20px; color: #1e3a5f; font-size: 1.15rem; display: flex; align-items: center; gap: 10px; }
    .es-card h3 i { color: #2563eb; }

    .es-form-row { margin-bottom: 16px; }
    .es-form-row label { display: block; font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 6px; }
    .es-form-row input, .es-form-row select {
        width: 100%; padding: 11px 14px; border: 2px solid #e5e7eb; border-radius: 10px;
        font-family: inherit; font-size: 0.9rem; transition: border-color 0.3s;
    }
    .es-form-row input:focus { outline: none; border-color: #2563eb; }
    .es-form-row .hint { font-size: 0.78rem; color: #9ca3af; margin-top: 4px; }
    .es-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .btn-save {
        padding: 12px 28px; background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white;
        border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.9rem;
        display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.4); }
    .btn-test {
        padding: 12px 22px; background: white; border: 2px solid #e5e7eb; border-radius: 10px;
        font-weight: 600; cursor: pointer; font-size: 0.9rem; color: #374151; transition: all 0.3s;
    }
    .btn-test:hover { border-color: #22c55e; color: #16a34a; }
    .btn-test:disabled { opacity: 0.6; cursor: wait; }

    /* Template list */
    .tpl-list { display: flex; flex-direction: column; gap: 10px; }
    .tpl-row {
        display: flex; align-items: center; justify-content: space-between; padding: 14px 18px;
        background: #fafbfc; border-radius: 12px; border: 1px solid #f3f4f6;
    }
    .tpl-row h4 { margin: 0 0 2px; font-size: 0.95rem; color: #1e3a5f; }
    .tpl-row small { color: #9ca3af; font-size: 0.8rem; }
    .tpl-toggle {
        width: 48px; height: 26px; border-radius: 13px; border: none; cursor: pointer;
        position: relative; transition: all 0.3s;
    }
    .tpl-toggle.on { background: #22c55e; }
    .tpl-toggle.off { background: #d1d5db; }
    .tpl-toggle::after {
        content: ''; width: 20px; height: 20px; border-radius: 50%; background: white;
        position: absolute; top: 3px; transition: all 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .tpl-toggle.on::after { left: 25px; }
    .tpl-toggle.off::after { left: 3px; }

    /* Logs */
    .es-logs-table { width: 100%; border-collapse: collapse; }
    .es-logs-table th {
        text-align: left; padding: 12px 16px; font-size: 0.78rem; text-transform: uppercase;
        letter-spacing: 0.5px; color: #9ca3af; background: #fafbfc; font-weight: 700;
    }
    .es-logs-table td { padding: 12px 16px; font-size: 0.85rem; border-top: 1px solid #f3f4f6; color: #374151; }
    .es-logs-table tr:hover { background: #fafbfc; }
    .log-st { padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .log-st.sent { background: #dcfce7; color: #166534; }
    .log-st.pending { background: #fef3c7; color: #92400e; }
    .log-st.failed { background: #fee2e2; color: #991b1b; }

    @media (max-width: 768px) {
        .es-stats { grid-template-columns: 1fr; }
        .es-form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="es-header">
    <h2><i class="fas fa-envelope-open-text me-2"></i>Email Settings</h2>
    <small>Konfigurasi SMTP, kelola template, lihat riwayat email</small>
</div>

<!-- Stats -->
<div class="es-stats">
    <div class="es-stat sent">
        <div class="num"><?= $totalSent ?></div>
        <div class="lbl"><i class="fas fa-check-circle me-1"></i>Terkirim</div>
    </div>
    <div class="es-stat pending">
        <div class="num"><?= $totalPending ?></div>
        <div class="lbl"><i class="fas fa-clock me-1"></i>Pending</div>
    </div>
    <div class="es-stat failed">
        <div class="num"><?= $totalFailed ?></div>
        <div class="lbl"><i class="fas fa-times-circle me-1"></i>Gagal</div>
    </div>
</div>

<!-- Tabs -->
<div class="es-tabs">
    <button class="es-tab active" onclick="esTab('smtp')"><i class="fas fa-server me-1"></i>SMTP Config</button>
    <button class="es-tab" onclick="esTab('templates')"><i class="fas fa-file-alt me-1"></i>Templates (<?= count($templates) ?>)</button>
    <button class="es-tab" onclick="esTab('logs')"><i class="fas fa-history me-1"></i>Logs (<?= count($logs) ?>)</button>
</div>

<!-- SMTP Panel -->
<div class="es-panel active" id="panel-smtp">
    <form action="<?= url('/admin/email-settings/save') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="es-card">
            <h3><i class="fas fa-server"></i>SMTP Configuration</h3>
            <div class="es-form-grid">
                <div class="es-form-row">
                    <label>SMTP Host</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host']) ?>" placeholder="smtp.gmail.com">
                    <div class="hint">Contoh: smtp.gmail.com, smtp.office365.com</div>
                </div>
                <div class="es-form-row">
                    <label>SMTP Port</label>
                    <input type="number" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port']) ?>" placeholder="587">
                    <div class="hint">587 (TLS) atau 465 (SSL)</div>
                </div>
                <div class="es-form-row">
                    <label>SMTP Username</label>
                    <input type="text" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username']) ?>" placeholder="email@gmail.com">
                </div>
                <div class="es-form-row">
                    <label>SMTP Password</label>
                    <input type="password" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password']) ?>" placeholder="App Password">
                    <div class="hint">Untuk Gmail gunakan App Password</div>
                </div>
            </div>
        </div>

        <div class="es-card">
            <h3><i class="fas fa-id-card"></i>Sender Identity</h3>
            <div class="es-form-grid">
                <div class="es-form-row">
                    <label>From Email</label>
                    <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email']) ?>" placeholder="recruitment@company.com">
                </div>
                <div class="es-form-row">
                    <label>From Name</label>
                    <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name']) ?>" placeholder="PT Indo Ocean Crew Services">
                </div>
            </div>
            <div class="es-form-row">
                <label>Auto Send Email</label>
                <select name="email_auto_send">
                    <option value="false" <?= $settings['email_auto_send'] !== 'true' ? 'selected' : '' ?>>Tidak (Simulation Mode)</option>
                    <option value="true" <?= $settings['email_auto_send'] === 'true' ? 'selected' : '' ?>>Ya (Kirim secara otomatis)</option>
                </select>
                <div class="hint">Jika "Tidak", email hanya disimpan di log tanpa benar-benar terkirim</div>
            </div>
        </div>

        <div style="display:flex;gap:12px;align-items:center;">
            <button type="submit" class="btn-save"><i class="fas fa-save"></i>Simpan Settings</button>
            <button type="button" class="btn-test" onclick="testSmtp()"><i class="fas fa-vial me-1"></i>Test Kirim Email</button>
        </div>
    </form>
</div>

<!-- Templates Panel -->
<div class="es-panel" id="panel-templates">
    <div class="es-card">
        <h3><i class="fas fa-file-alt"></i>Email Templates</h3>
        <div class="tpl-list">
            <?php foreach ($templates as $tpl): ?>
                <div class="tpl-row">
                    <div>
                        <h4><?= htmlspecialchars($tpl['name']) ?></h4>
                        <small><strong>Slug:</strong> <?= htmlspecialchars($tpl['slug']) ?> | <strong>Subject:</strong> <?= htmlspecialchars($tpl['subject']) ?></small>
                    </div>
                    <button class="tpl-toggle <?= $tpl['is_active'] ? 'on' : 'off' ?>"
                            onclick="toggleTpl(<?= $tpl['id'] ?>, this)"
                            title="<?= $tpl['is_active'] ? 'Aktif' : 'Nonaktif' ?>"></button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Logs Panel -->
<div class="es-panel" id="panel-logs">
    <div class="es-card" style="padding:0;">
        <h3 style="padding:20px 24px;border-bottom:1px solid #f3f4f6;"><i class="fas fa-history"></i>Riwayat Email</h3>
        <?php if (empty($logs)): ?>
            <div style="text-align:center;padding:50px;color:#9ca3af;">
                <i class="fas fa-inbox" style="font-size:3rem;opacity:0.3;display:block;margin-bottom:12px;"></i>
                <p>Belum ada email yang dikirim</p>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="es-logs-table">
                    <thead>
                        <tr>
                            <th>Penerima</th>
                            <th>Subject</th>
                            <th>Template</th>
                            <th>Status</th>
                            <th>Error</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($log['recipient_name'] ?? '-') ?></strong>
                                    <br><small style="color:#9ca3af;"><?= htmlspecialchars($log['recipient_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($log['subject']) ?></td>
                                <td>
                                    <?php if ($log['template_name']): ?>
                                        <span style="background:#eef2ff;color:#4f46e5;padding:3px 10px;border-radius:6px;font-size:0.75rem;font-weight:600;"><?= htmlspecialchars($log['template_name']) ?></span>
                                    <?php else: ?>
                                        <span style="color:#9ca3af;">Custom</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="log-st <?= $log['status'] ?>"><?= ucfirst($log['status']) ?></span></td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#ef4444;font-size:0.8rem;">
                                    <?= htmlspecialchars($log['error_message'] ?? '') ?>
                                </td>
                                <td style="white-space:nowrap;"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function esTab(name) {
    document.querySelectorAll('.es-tab').forEach(function(t,i) { t.classList.toggle('active', i === ['smtp','templates','logs'].indexOf(name)); });
    document.querySelectorAll('.es-panel').forEach(function(p) { p.classList.remove('active'); });
    document.getElementById('panel-' + name).classList.add('active');
}

function testSmtp() {
    var testEmail = prompt('Masukkan email tujuan test:', '');
    if (!testEmail) return;

    var btn = event.target.closest('.btn-test');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Testing...';

    fetch('<?= url('/admin/email-settings/test') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'csrf_token=<?= csrf_token() ?>&test_email=' + encodeURIComponent(testEmail)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        alert((data.success ? '✅ ' : '❌ ') + data.message);
    })
    .catch(function(e) { alert('❌ Error: ' + e.message); })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-vial me-1"></i>Test Kirim Email';
    });
}

function toggleTpl(id, btn) {
    var isOn = btn.classList.contains('on');
    var newState = isOn ? 0 : 1;

    fetch('<?= url('/admin/email-settings/toggle-template') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'csrf_token=<?= csrf_token() ?>&template_id=' + id + '&is_active=' + newState
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            btn.classList.toggle('on');
            btn.classList.toggle('off');
        }
    });
}
</script>
