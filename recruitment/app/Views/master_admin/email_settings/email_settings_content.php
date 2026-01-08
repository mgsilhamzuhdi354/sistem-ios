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

.template-item { padding: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.75rem; }
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
.log-status { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 0.5rem; }
.log-status.sent { background: #16a34a; }
.log-status.failed { background: #dc2626; }
.log-status.pending { background: #d97706; }
.log-email { font-weight: 500; }
.log-subject { color: #64748b; }
.log-time { color: #94a3b8; font-size: 0.75rem; }

.btn-primary { background: #1e3a5f; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.875rem; }
.btn-primary:hover { background: #2c5282; }
.btn-outline { background: transparent; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.875rem; }
.btn-outline:hover { background: #f8fafc; }

.send-test-form { display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; }
.send-test-form input { flex: 1; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; }
.send-test-form select { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; }
</style>

<div class="page-header">
    <h1><i class="fas fa-envelope"></i> Email Settings</h1>
    <p>Manage email templates and notifications</p>
</div>

<!-- Stats Cards -->
<div class="email-stats">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
        <div class="stat-label">Total Emails</div>
    </div>
    <div class="stat-card sent">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= number_format($stats['sent'] ?? 0) ?></div>
        <div class="stat-label">Sent</div>
    </div>
    <div class="stat-card failed">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-value"><?= number_format($stats['failed'] ?? 0) ?></div>
        <div class="stat-label">Failed</div>
    </div>
    <div class="stat-card today">
        <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-value"><?= number_format($stats['today'] ?? 0) ?></div>
        <div class="stat-label">Today</div>
    </div>
</div>

<div class="email-grid">
    <!-- Email Templates -->
    <div class="email-card">
        <div class="email-card-header">
            <span><i class="fas fa-file-alt me-2"></i>Email Templates</span>
        </div>
        <div class="email-card-body">
            <?php if (empty($templates)): ?>
            <p class="text-muted">No templates found. Run the SQL migration first.</p>
            <?php else: ?>
            <?php foreach ($templates as $template): ?>
            <div class="template-item">
                <div class="template-name"><?= htmlspecialchars($template['name']) ?></div>
                <div class="template-subject"><?= htmlspecialchars($template['subject']) ?></div>
                <div class="template-badges">
                    <span class="badge <?= $template['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $template['is_active'] ? 'Active' : 'Inactive' ?>
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
            <span><i class="fas fa-history me-2"></i>Recent Emails</span>
            <a href="<?= url('/master-admin/email-settings/logs') ?>" class="btn-outline">View All</a>
        </div>
        <div class="email-card-body">
            <?php if (empty($logs)): ?>
            <p class="text-muted text-center">No emails sent yet.</p>
            <?php else: ?>
            <?php foreach ($logs as $log): ?>
            <div class="log-item">
                <span class="log-status <?= $log['status'] ?>"></span>
                <span class="log-email"><?= htmlspecialchars($log['recipient_email']) ?></span>
                <br>
                <span class="log-subject"><?= htmlspecialchars(substr($log['subject'], 0, 40)) ?>...</span>
                <span class="log-time"><?= date('d M H:i', strtotime($log['created_at'])) ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Simulation Mode Notice -->
<div style="margin-top: 2rem; padding: 1rem; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
    <strong><i class="fas fa-info-circle"></i> Simulation Mode Active</strong>
    <p style="margin: 0.5rem 0 0 0; color: #92400e;">
        Emails are being logged but not actually sent. Configure SMTP settings to enable real email sending.
    </p>
</div>

<script>
function sendTestEmail() {
    const templateId = document.getElementById('testTemplate').value;
    const email = document.getElementById('testEmail').value;
    
    if (!email) {
        alert('Please enter an email address');
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
    });
}
</script>
