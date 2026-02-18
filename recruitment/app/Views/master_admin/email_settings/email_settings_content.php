<style>
.email-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 1rem; }
.stat-card .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; }
.stat-card .stat-label { color: #64748b; font-size: 0.875rem; }
.stat-card.total .stat-icon { background: #e0f2fe; color: #0284c7; }
.stat-card.sent .stat-icon { background: #dcfce7; color: #16a34a; }
.stat-card.failed .stat-icon { background: #fee2e2; color: #dc2626; }
.stat-card.today .stat-icon { background: #fef3c7; color: #d97706; }

.email-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.email-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.email-card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
.email-card-body { padding: 1.5rem; max-height: 400px; overflow-y: auto; }

.template-item { padding: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.75rem; cursor: pointer; transition: all 0.2s; }
.template-item:hover { border-color: #3b82f6; background: #f8fafc; }
.template-name { font-weight: 600; color: #1e293b; }
.template-subject { font-size: 0.875rem; color: #64748b; margin-top: 0.25rem; }
.template-badges { margin-top: 0.5rem; display: flex; gap: 0.5rem; }
.badge { padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; }
.badge-active { background: #dcfce7; color: #16a34a; }
.badge-inactive { background: #f3f4f6; color: #6b7280; }
.badge-auto { background: #dbeafe; color: #2563eb; }

.log-item { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
.log-item:last-child { border-bottom: none; }
.log-status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 0.5rem; }
.log-status-dot.sent { background: #16a34a; }
.log-status-dot.failed { background: #dc2626; }
.log-status-dot.pending { background: #d97706; }
.log-email { font-weight: 500; }
.log-subject { color: #64748b; }
.log-time { color: #94a3b8; font-size: 0.75rem; float: right; }

.btn-primary { background: #1e3a5f; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.875rem; }
.btn-primary:hover { background: #2c5282; }
.btn-outline { background: transparent; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.875rem; color: #374151; text-decoration: none; }
.btn-outline:hover { background: #f8fafc; }

/* SMTP Settings Form */
.smtp-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem; }
.smtp-card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem; }
.smtp-card-header h3 { margin: 0; font-size: 1.1rem; font-weight: 600; color: #1e293b; }
.smtp-card-header .smtp-badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.smtp-badge.active { background: #dcfce7; color: #16a34a; }
.smtp-badge.inactive { background: #fef3c7; color: #b45309; }
.smtp-card-body { padding: 1.5rem; }

.smtp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.4rem; }
.form-group label i { width: 18px; color: #6366f1; }
.form-group input, .form-group select { width: 100%; padding: 0.6rem 0.85rem; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; transition: border-color 0.2s; box-sizing: border-box; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.form-group .hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem; }

.smtp-section-title { font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0; }

.btn-save-smtp { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; border: none; padding: 0.75rem 2rem; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-save-smtp:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }

.send-test-form { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; }
.send-test-form input { flex: 1; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; }
.send-test-form select { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; }

.flash-success { padding: 1rem; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }

@keyframes fadeInDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.animate-in { animation: fadeInDown 0.4s ease; }
</style>

<div class="page-header">
    <h1><i class="fas fa-envelope"></i> Email Settings</h1>
    <p>Pengaturan SMTP dan template email</p>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="flash-success animate-in">
    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- Stats Cards -->
<div class="email-stats">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
        <div class="stat-label">Total Email</div>
    </div>
    <div class="stat-card sent">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= number_format($stats['sent'] ?? 0) ?></div>
        <div class="stat-label">Terkirim</div>
    </div>
    <div class="stat-card failed">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-value"><?= number_format($stats['failed'] ?? 0) ?></div>
        <div class="stat-label">Gagal</div>
    </div>
    <div class="stat-card today">
        <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-value"><?= number_format($stats['today'] ?? 0) ?></div>
        <div class="stat-label">Hari Ini</div>
    </div>
</div>

<!-- ====== SMTP SETTINGS ====== -->
<?php
$isSmtpActive = !empty($smtpSettings['smtp_host']) && !empty($smtpSettings['smtp_username']) && !empty($smtpSettings['smtp_password']);
?>
<div class="smtp-card animate-in">
    <div class="smtp-card-header">
        <i class="fas fa-server" style="color:#6366f1;font-size:1.2rem;"></i>
        <h3>Pengaturan SMTP</h3>
        <?php if ($isSmtpActive): ?>
            <span class="smtp-badge active"><i class="fas fa-check-circle me-1"></i>SMTP Aktif</span>
        <?php else: ?>
            <span class="smtp-badge inactive"><i class="fas fa-exclamation-triangle me-1"></i>Mode Simulasi</span>
        <?php endif; ?>
    </div>
    <div class="smtp-card-body">
        <form method="POST" action="<?= url('/master-admin/email-settings/save-settings') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="smtp-grid">
                <!-- LEFT: Server Settings -->
                <div>
                    <div class="smtp-section-title"><i class="fas fa-globe me-1"></i> Server SMTP</div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-server me-1"></i> SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= htmlspecialchars($smtpSettings['smtp_host']) ?>" placeholder="mail.indoceancrew.co.id">
                        <div class="hint">Untuk DomainNesia: mail.domainanda.com</div>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div class="form-group">
                            <label><i class="fas fa-hashtag me-1"></i> Port</label>
                            <input type="number" name="smtp_port" value="<?= htmlspecialchars($smtpSettings['smtp_port']) ?>" placeholder="465">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock me-1"></i> Enkripsi</label>
                            <select name="smtp_encryption">
                                <option value="ssl" <?= ($smtpSettings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                                <option value="tls" <?= ($smtpSettings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user me-1"></i> Username</label>
                        <input type="text" name="smtp_username" value="<?= htmlspecialchars($smtpSettings['smtp_username']) ?>" placeholder="crewing@indoceancrew.co.id">
                        <div class="hint">Email account dari DomainNesia Mailspace</div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-key me-1"></i> Password</label>
                        <input type="password" name="smtp_password" placeholder="<?= !empty($smtpSettings['smtp_password']) ? '••••••• (sudah tersimpan)' : 'Masukkan password email' ?>">
                        <div class="hint">Kosongkan jika tidak ingin mengubah password</div>
                    </div>
                </div>
                
                <!-- RIGHT: Sender Settings -->
                <div>
                    <div class="smtp-section-title"><i class="fas fa-id-card me-1"></i> Pengirim</div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope me-1"></i> Email Pengirim</label>
                        <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($smtpSettings['smtp_from_email']) ?>" placeholder="crewing@indoceancrew.co.id">
                        <div class="hint">Biasanya sama dengan SMTP Username</div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-building me-1"></i> Nama Pengirim</label>
                        <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($smtpSettings['smtp_from_name']) ?>" placeholder="PT Indo Ocean Crew Services">
                    </div>
                    
                    <!-- DomainNesia Quick Guide -->
                    <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:1rem;margin-top:1rem;">
                        <div style="font-weight:600;color:#0369a1;font-size:0.85rem;margin-bottom:0.5rem;">
                            <i class="fas fa-info-circle me-1"></i> Panduan DomainNesia Mailspace
                        </div>
                        <div style="font-size:0.8rem;color:#475569;line-height:1.6;">
                            <strong>Host:</strong> mail.indoceancrew.co.id<br>
                            <strong>Port:</strong> 465 (SSL) atau 587 (TLS)<br>
                            <strong>Username:</strong> alamat email lengkap<br>
                            <strong>Password:</strong> password email account<br>
                            <strong>Enkripsi:</strong> SSL (recommended)
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="display:flex;justify-content:flex-end;padding-top:1rem;border-top:1px solid #e2e8f0;margin-top:1rem;">
                <button type="submit" class="btn-save-smtp">
                    <i class="fas fa-save me-1"></i> Simpan Pengaturan SMTP
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Templates & Logs Grid -->
<div class="email-grid">
    <!-- Email Templates -->
    <div class="email-card">
        <div class="email-card-header">
            <span><i class="fas fa-file-alt me-2"></i>Email Templates</span>
        </div>
        <div class="email-card-body">
            <?php if (empty($templates)): ?>
            <p style="color:#94a3b8;text-align:center;">Belum ada template. Jalankan SQL migration terlebih dahulu.</p>
            <?php else: ?>
            <?php foreach ($templates as $template): ?>
            <div class="template-item" onclick="window.location='<?= url('/master-admin/email-settings/edit-template/' . $template['id']) ?>'">
                <div class="template-name"><?= htmlspecialchars($template['name']) ?></div>
                <div class="template-subject"><?= htmlspecialchars($template['subject']) ?></div>
                <div class="template-badges">
                    <span class="badge <?= $template['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $template['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                    <?php if ($template['is_auto_send']): ?>
                    <span class="badge badge-auto">Auto-send</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Send Test Email -->
            <div class="send-test-form">
                <select id="testTemplate">
                    <?php foreach ($templates as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="email" id="testEmail" placeholder="test@email.com">
                <button class="btn-primary" onclick="sendTestEmail()">
                    <i class="fas fa-paper-plane"></i> Test
                </button>
            </div>
        </div>
    </div>
    
    <!-- Recent Logs -->
    <div class="email-card">
        <div class="email-card-header">
            <span><i class="fas fa-history me-2"></i>Email Terakhir</span>
            <a href="<?= url('/master-admin/email-settings/logs') ?>" class="btn-outline">Lihat Semua</a>
        </div>
        <div class="email-card-body">
            <?php if (empty($logs)): ?>
            <p style="color:#94a3b8;text-align:center;">Belum ada email terkirim.</p>
            <?php else: ?>
            <?php foreach ($logs as $log): ?>
            <div class="log-item">
                <span class="log-status-dot <?= $log['status'] ?>"></span>
                <span class="log-email"><?= htmlspecialchars($log['to_email'] ?? '') ?></span>
                <span class="log-time"><?= date('d M H:i', strtotime($log['created_at'])) ?></span>
                <br>
                <span class="log-subject"><?= htmlspecialchars(substr($log['subject'], 0, 50)) ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function sendTestEmail() {
    const templateId = document.getElementById('testTemplate').value;
    const email = document.getElementById('testEmail').value;
    
    if (!email) {
        alert('Masukkan alamat email tujuan');
        return;
    }
    
    fetch('<?= url('/master-admin/email-settings/send-test') ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `csrf_token=<?= csrf_token() ?>&template_id=${templateId}&email=${encodeURIComponent(email)}`
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) location.reload();
    })
    .catch(e => alert('Error: ' + e.message));
}
</script>
