<?php $this->extend('layouts/crewing_pic'); ?>

<?php $this->section('content'); ?>

<div class="smtp-personal-container">
    <div class="smtp-header">
        <div class="header-left">
            <a href="<?= url('/crewing/email') ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Email
            </a>
            <h1><i class="fas fa-envelope-circle-check"></i> Personal SMTP Configuration</h1>
            <p class="subtitle">Configure your own email server settings for independent email sending</p>
        </div>
        <div class="header-right">
            <?php if ($hasConfig): ?>
                <span class="status-badge active">
                    <i class="fas fa-check-circle"></i> Personal SMTP Active
                </span>
            <?php else: ?>
                <span class="status-badge inactive">
                    <i class="fas fa-info-circle"></i> Using Global SMTP
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <div class="smtp-grid">
        <!-- Configuration Form -->
        <div class="smtp-card">
            <div class="card-header">
                <h2><i class="fas fa-cog"></i> SMTP Server Configuration</h2>
                <p>Enter your email server details below</p>
            </div>
            <form id="smtpForm" class="smtp-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_host">
                            <i class="fas fa-server"></i> SMTP Host *
                        </label>
                        <input type="text" id="smtp_host" name="smtp_host" 
                               value="<?= $existingConfig['smtp_host'] ?? '' ?>" 
                               placeholder="smtp.gmail.com" required>
                        <small>SMTP server hostname (e.g., smtp.gmail.com, smtp.office365.com)</small>
                    </div>
                    <div class="form-group">
                        <label for="smtp_port">
                            <i class="fas fa-plug"></i> Port *
                        </label>
                        <input type="number" id="smtp_port" name="smtp_port" 
                               value="<?= $existingConfig['smtp_port'] ?? '465' ?>" 
                               placeholder="465" min="1" max="65535" required>
                        <small>Usually 465 (SSL) or 587 (TLS)</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_username">
                            <i class="fas fa-user"></i> Username *
                        </label>
                        <input type="text" id="smtp_username" name="smtp_username" 
                               value="<?= $existingConfig['smtp_username'] ?? '' ?>" 
                               placeholder="your-email@example.com" required>
                        <small>Your email account username</small>
                    </div>
                    <div class="form-group">
                        <label for="smtp_password">
                            <i class="fas fa-lock"></i> Password *
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="smtp_password" name="smtp_password" 
                                   value="<?= $existingConfig['smtp_password_decrypted'] ?? '' ?>" 
                                   placeholder="••••••••" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small>Use App Password for Gmail/Outlook</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="smtp_encryption">
                        <i class="fas fa-shield-alt"></i> Encryption *
                    </label>
                    <select id="smtp_encryption" name="smtp_encryption" required>
                        <option value="ssl" <?= ($existingConfig['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="tls" <?= ($existingConfig['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    </select>
                    <small>Security protocol (SSL recommended for port 465)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_from_email">
                            <i class="fas fa-envelope"></i> From Email *
                        </label>
                        <input type="email" id="smtp_from_email" name="smtp_from_email" 
                               value="<?= $existingConfig['smtp_from_email'] ?? '' ?>" 
                               placeholder="your-name@company.com" required>
                        <small>Email address shown as sender</small>
                    </div>
                    <div class="form-group">
                        <label for="smtp_from_name">
                            <i class="fas fa-id-card"></i> From Name *
                        </label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" 
                               value="<?= $existingConfig['smtp_from_name'] ?? '' ?>" 
                               placeholder="Your Name" required>
                        <small>Name displayed as sender</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="testConnection()">
                        <i class="fas fa-vial"></i> Test Connection
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Configuration
                    </button>
                    <?php if ($hasConfig): ?>
                        <button type="button" class="btn btn-danger" onclick="deleteConfig()">
                            <i class="fas fa-trash"></i> Delete Config
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="smtp-sidebar">
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Quick Guide</h3>
                </div>
                <div class="info-content">
                    <div class="info-item">
                        <strong>Gmail:</strong>
                        <p>Host: <code>smtp.gmail.com</code></p>
                        <p>Port: <code>465</code> (SSL)</p>
                        <p>Use App Password (not your regular password)</p>
                    </div>
                    <div class="info-item">
                        <strong>Domainesia / cPanel Hosting:</strong>
                        <p>Host: <code>mail.yourdomain.com</code></p>
                        <p>Port: <code>465</code> (SSL) ⭐ Recommended</p>
                        <p>Username: Full email address</p>
                        <p class="warning-text">⚠️ Check spam folder if not received</p>
                    </div>
                    <div class="info-item">
                        <strong>Outlook/Office365:</strong>
                        <p>Host: <code>smtp.office365.com</code></p>
                        <p>Port: <code>587</code> (TLS)</p>
                    </div>
                    <div class="info-item">
                        <strong>Yahoo:</strong>
                        <p>Host: <code>smtp.mail.yahoo.com</code></p>
                        <p>Port: <code>465</code> (SSL)</p>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-question-circle"></i>
                    <h3>Why Personal SMTP?</h3>
                </div>
                <div class="info-content">
                    <ul>
                        <li>✓ Send emails from your own account</li>
                        <li>✓ Better deliverability</li>
                        <li>✓ Independent from global settings</li>
                        <li>✓ Personalized sender identity</li>
                    </ul>
                </div>
            </div>

            <?php if (!empty($globalSettings['smtp_host'])): ?>
            <div class="info-card global-settings">
                <div class="info-header">
                    <i class="fas fa-globe"></i>
                    <h3>Global SMTP (Fallback)</h3>
                </div>
                <div class="info-content">
                    <p><strong>Host:</strong> <?= $globalSettings['smtp_host'] ?? 'Not configured' ?></p>
                    <p><strong>From:</strong> <?= $globalSettings['smtp_from_email'] ?? 'N/A' ?></p>
                    <small>Used when personal SMTP is not configured</small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.smtp-personal-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.smtp-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.header-left h1 {
    font-size: 1.75rem;
    color: #1f2937;
    margin: 0.5rem 0 0.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-left h1 i {
    color: #14b8a6;
}

.subtitle {
    color: #6b7280;
    margin: 0;
    font-size: 0.95rem;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    transition: color 0.2s;
}

.back-btn:hover {
    color: #14b8a6;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-badge.active {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.status-badge.inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.alert {
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.smtp-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.smtp-card, .smtp-sidebar {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.smtp-card {
    padding: 2rem;
}

.card-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h2 {
    font-size: 1.25rem;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-header h2 i {
    color: #14b8a6;
}

.card-header p {
    color: #6b7280;
    margin: 0;
    font-size: 0.9rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-group label i {
    color: #14b8a6;
    font-size: 0.85rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
}

.form-group small {
    display: block;
    color: #6b7280;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.2s;
}

.toggle-password:hover {
    color: #14b8a6;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #fee2e2;
    color: #991b1b;
    margin-left: auto;
}

.btn-danger:hover {
    background: #fecaca;
}

.smtp-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-card {
    padding: 1.5rem;
}

.info-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.info-header i {
    font-size: 1.25rem;
    color: #14b8a6;
}

.info-header h3 {
    font-size: 1rem;
    color: #1f2937;
    margin: 0;
}

.info-content {
    font-size: 0.875rem;
}

.info-item {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-item strong {
    color: #1f2937;
    display: block;
    margin-bottom: 0.25rem;
}

.info-item p {
    color: #6b7280;
    margin: 0.1rem 0;
}

.info-item code {
    background: #f3f4f6;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    color: #14b8a6;
    font-size: 0.85rem;
}

.info-item .warning-text {
    color: #f59e0b;
    font-weight: 600;
    margin-top: 0.25rem;
}

.info-content ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-content ul li {
    padding: 0.5rem 0;
    color: #6b7280;
}

.global-settings {
    background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
    border: 1px solid #99f6e4;
}

@media (max-width: 1024px) {
    .smtp-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function togglePassword() {
    const input = document.getElementById('smtp_password');
    const icon = document.querySelector('.toggle-password i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function testConnection() {
    const form = document.getElementById('smtpForm');
    const formData = new FormData(form);
    
    // Disable button
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    
    fetch('<?= url('/crewing/settings/smtp-personal/test') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(err => {
        showToast('error', 'Error: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-vial"></i> Test Connection';
    });
}

function deleteConfig() {
    if (!confirm('Are you sure you want to delete your personal SMTP configuration? You will use the global SMTP settings.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= url('/crewing/settings/smtp-personal/delete') ?>';
    document.body.appendChild(form);
    form.submit();
}

document.getElementById('smtpForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    fetch('<?= url('/crewing/settings/smtp-personal/save') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Configuration';
        }
    })
    .catch(err => {
        showToast('error', 'Error: ' + err.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Configuration';
    });
});

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    const style = document.createElement('style');
    style.textContent = `
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .toast-success {
            background: #10b981;
            color: white;
        }
        .toast-error {
            background: #ef4444;
            color: white;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<?php $this->endSection(); ?>
