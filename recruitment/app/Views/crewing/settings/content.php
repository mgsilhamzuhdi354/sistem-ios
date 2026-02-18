<style>
.settings-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #e2e8f0;
}
.settings-tabs .tab {
    padding: 0.75rem 1.5rem;
    border: none;
    background: none;
    font-size: 0.9rem;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    font-family: inherit;
}
.settings-tabs .tab:hover { color: #6366f1; }
.settings-tabs .tab.active {
    color: #6366f1;
    border-bottom-color: #6366f1;
}

.tab-content { display: none; }
.tab-content.active { display: block; }

.settings-header { margin-bottom: 2rem; }
.settings-header h2 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; }
.settings-header p { color: #64748b; margin: 0.25rem 0 0; }

.settings-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.settings-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.settings-card-header .card-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.settings-card-header .card-icon.server { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; }
.settings-card-header .card-icon.profile { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.settings-card-header .card-icon.backup { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.settings-card-header h3 { margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b; }

.settings-card-body { padding: 1.5rem; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.form-group { margin-bottom: 1.25rem; }
.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.4rem;
}
.form-group input, .form-group select {
    width: 100%;
    padding: 0.65rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: inherit;
    transition: all 0.2s;
    box-sizing: border-box;
    background: #fafbfc;
}
.form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    background: white;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }

.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.btn-warning:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); }

.flash-msg {
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex; align-items: center; gap: 0.5rem;
    font-weight: 500;
}
.flash-msg.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.flash-msg.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.info-box {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.info-box p { margin: 0; font-size: 0.9rem; color: #0369a1; line-height: 1.6; }

/* Photo Upload Styles */
.photo-upload-container {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 2rem;
    align-items: start;
}

.photo-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    color: #94a3b8;
    font-size: 3.5rem;
}

.photo-upload-controls {
    flex: 1;
}

.upload-dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.upload-dropzone:hover {
    border-color: #6366f1;
    background: #f0f4ff;
}

.upload-dropzone.dragover {
    border-color: #6366f1;
    background: #eef2ff;
}

.upload-dropzone i {
    font-size: 2.5rem;
    color: #6366f1;
    margin-bottom: 1rem;
    display: block;
}

.upload-dropzone p {
    margin: 0.5rem 0;
    color: #64748b;
    font-size: 0.95rem;
}

.upload-dropzone strong {
    color: #1e293b;
}

.file-types {
    display: block;
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.btn-delete-photo {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 0.65rem 1.5rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-delete-photo:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

@media (max-width: 768px) {
    .photo-upload-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .photo-preview {
        margin: 0 auto;
    }
}
</style>

<div class="settings-header">
    <h2><i class="fas fa-cog me-2" style="color:#6366f1;"></i>Settings</h2>
    <p>Manage your profile, SMTP configuration, and database backup</p>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="flash-msg success">
    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="flash-msg error">
    <i class="fas fa-times-circle"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<div class="settings-tabs">
    <button class="tab <?= $activeTab === 'profile' ? 'active' : '' ?>" onclick="switchTab('profile')">
        <i class="fas fa-user-circle me-1"></i> <?= t('settings.my_profile') ?>
    </button>
    <button class="tab <?= $activeTab === 'display' ? 'active' : '' ?>" onclick="switchTab('display')">
        <i class="fas fa-desktop me-1"></i> <?= t('settings.display') ?>
    </button>
    <button class="tab <?= $activeTab === 'smtp' ? 'active' : '' ?>" onclick="switchTab('smtp')">
        <i class="fas fa-envelope me-1"></i> <?= t('settings.smtp_settings') ?>
    </button>
    <button class="tab <?= $activeTab === 'backup' ? 'active' : '' ?>" onclick="switchTab('backup')">
        <i class="fas fa-database me-1"></i> <?= t('settings.database_backup') ?>
    </button>
</div>

<!-- Profile Tab -->
<div id="tab-profile" class="tab-content <?= $activeTab === 'profile' ? 'active' : '' ?>">
    <form method="POST" action="<?= url('/crewing/settings/update-profile') ?>" enctype="multipart/form-data">
        <!-- Profile Photo Upload Card -->
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="card-icon profile"><i class="fas fa-camera"></i></div>
                <h3><?= t('settings.profile_photo') ?></h3>
            </div>
            <div class="settings-card-body">
                <div class="photo-upload-container">
                    <div class="photo-preview" style="<?= !empty($user['photo']) ? 'cursor:pointer;' : '' ?>" 
                         <?= !empty($user['photo']) ? 'onclick="viewPhotoFull()"' : '' ?>>
                        <?php if (!empty($user['photo'])): ?>
                            <img src="<?= url('/' . 'uploads/recruiters/' . $user['photo']) ?>" alt="Profile Photo" id="photoPreview">
                        <?php else: ?>
                            <div class="photo-placeholder" id="photoPreview">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="photo-upload-controls">
                        <div class="upload-dropzone" id="uploadDropzone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p><strong><?= t('btn.upload', 'Click to upload') ?></strong></p>
                            <span class="file-types">JPG, PNG or GIF (MAX. 2MB)</span>
                            <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/gif" style="display:none;">
                        </div>
                        <?php if (!empty($user['photo'])): ?>
                        <a href="<?= url('/crewing/settings/delete-photo') ?>" 
                           class="btn-delete-photo" 
                           onclick="return confirm('Are you sure you want to delete your photo?')"
                           style="margin-top:12px; text-decoration:none; text-align:center;">
                            <i class="fas fa-trash"></i> <?= t('btn.delete') ?> Photo
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="card-icon profile"><i class="fas fa-user"></i></div>
                <h3><?= t('settings.personal_info') ?></h3>
            </div>
            <div class="settings-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> <?= t('form.full_name') ?></label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> <?= t('form.email') ?></label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> <?= t('form.phone') ?></label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-id-badge"></i> Employee ID</label>
                        <input type="text" name="employee_id" value="<?= htmlspecialchars($user['employee_id'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-briefcase"></i> Specialization</label>
                        <input type="text" name="specialization" value="<?= htmlspecialchars($user['specialization'] ?? '') ?>" placeholder="e.g. Deck Officer Recruitment">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tasks"></i> Max Applications</label>
                        <input type="number" name="max_applications" value="<?= htmlspecialchars($user['max_applications'] ?? 50) ?>" min="1">
                    </div>
                </div>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save me-1"></i> <?= t('settings.update_profile') ?>
            </button>
        </div>
    </form>
</div>

<!-- Display Tab -->
<div id="tab-display" class="tab-content <?= $activeTab === 'display' ? 'active' : '' ?>">
    <!-- Language Selector -->
    <div class="settings-card" style="margin-bottom:1.5rem;">
        <div class="settings-card-header">
            <div class="card-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);"><i class="fas fa-language"></i></div>
            <h3><?= t('settings.language', 'Bahasa / Language') ?></h3>
        </div>
        <div class="settings-card-body">
            <div class="info-box" style="margin-bottom:1.5rem;">
                <p>
                    <i class="fas fa-info-circle me-1"></i>
                    <?= getCurrentLanguage() === 'en' 
                        ? 'Choose your preferred language. The page will reload automatically after selection.' 
                        : 'Pilih bahasa yang Anda inginkan. Halaman akan dimuat ulang otomatis setelah pemilihan.' ?>
                </p>
            </div>
            
            <div class="form-group">
                <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <span><i class="fas fa-globe"></i> <?= t('settings.language_select', 'Pilih Bahasa') ?></span>
                    <span id="langValue" style="color:#3b82f6;font-weight:700;font-size:1rem;">
                        <?= getCurrentLanguage() === 'en' ? '🇺🇸 English' : '🇮🇩 Bahasa Indonesia' ?>
                    </span>
                </label>
                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                    <button type="button" onclick="saveLanguage('id')" 
                            class="lang-btn <?= getCurrentLanguage() === 'id' ? 'active' : '' ?>" 
                            style="flex:1;min-width:200px;padding:1rem 1.5rem;border-radius:12px;border:2px solid <?= getCurrentLanguage() === 'id' ? '#3b82f6' : '#e2e8f0' ?>;background:<?= getCurrentLanguage() === 'id' ? 'linear-gradient(135deg,#eff6ff,#dbeafe)' : '#fff' ?>;cursor:pointer;display:flex;align-items:center;gap:0.75rem;transition:all 0.3s ease;">
                        <span style="font-size:2rem;">🇮🇩</span>
                        <div style="text-align:left;">
                            <div style="font-weight:700;color:#1e293b;font-size:1rem;">Bahasa Indonesia</div>
                            <div style="color:#64748b;font-size:0.85rem;">Tampilan dalam Bahasa Indonesia</div>
                        </div>
                        <?php if (getCurrentLanguage() === 'id'): ?>
                        <i class="fas fa-check-circle" style="margin-left:auto;color:#3b82f6;font-size:1.2rem;"></i>
                        <?php endif; ?>
                    </button>
                    <button type="button" onclick="saveLanguage('en')" 
                            class="lang-btn <?= getCurrentLanguage() === 'en' ? 'active' : '' ?>"
                            style="flex:1;min-width:200px;padding:1rem 1.5rem;border-radius:12px;border:2px solid <?= getCurrentLanguage() === 'en' ? '#3b82f6' : '#e2e8f0' ?>;background:<?= getCurrentLanguage() === 'en' ? 'linear-gradient(135deg,#eff6ff,#dbeafe)' : '#fff' ?>;cursor:pointer;display:flex;align-items:center;gap:0.75rem;transition:all 0.3s ease;">
                        <span style="font-size:2rem;">🇺🇸</span>
                        <div style="text-align:left;">
                            <div style="font-weight:700;color:#1e293b;font-size:1rem;">English</div>
                            <div style="color:#64748b;font-size:0.85rem;">Display in English</div>
                        </div>
                        <?php if (getCurrentLanguage() === 'en'): ?>
                        <i class="fas fa-check-circle" style="margin-left:auto;color:#3b82f6;font-size:1.2rem;"></i>
                        <?php endif; ?>
                    </button>
                </div>
            </div>
            
            <div id="langMessage" style="display:none;margin-top:1rem;padding:0.75rem 1rem;border-radius:10px;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;font-size:0.9rem;">
                <i class="fas fa-check-circle"></i> <span id="langMessageText">Language saved</span>
            </div>
        </div>
    </div>
    
    <!-- UI Scale -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="card-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);"><i class="fas fa-search-plus"></i></div>
            <h3><?= t('settings.ui_scale') ?></h3>
        </div>
        <div class="settings-card-body">
            <div class="info-box" style="margin-bottom:1.5rem;">
                <p>
                    <i class="fas fa-info-circle me-1"></i>
                    <?= t('settings.ui_scale_desc') ?>
                </p>
            </div>
            
            <div class="form-group">
                <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <span><i class="fas fa-text-height"></i> <?= t('settings.display_size') ?></span>
                    <span id="scaleValue" style="color:#6366f1;font-weight:700;font-size:1.1rem;"><?= intval(($user['ui_scale'] ?? 1.00) * 100) ?>%</span>
                </label>
                <input type="range" id="uiScaleSlider" min="0.75" max="1.20" step="0.05" 
                       value="<?= $user['ui_scale'] ?? 1.00 ?>"
                       style="width:100%;height:8px;border-radius:10px;background:linear-gradient(90deg,#e0e7ff,#6366f1);cursor:pointer;outline:none;-webkit-appearance:none;">
                <div style="display:flex;justify-content:space-between;margin-top:0.5rem;color:#94a3b8;font-size:0.85rem;">
                    <span>75%</span>
                    <span>90%</span>
                    <span style="color:#6366f1;font-weight:600;">100%</span>
                    <span>110%</span>
                    <span>120%</span>
                </div>
            </div>
            
            <!-- Preview Card -->
            <div style="margin-top:2rem;padding:1.5rem;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-radius:12px;border:2px solid #e2e8f0;">
                <h4 style="margin:0 0 0.5rem;color:#1e293b;font-size:1rem;"><?= t('settings.preview') ?></h4>
                <p id="previewText" style="margin:0;color:#64748b;line-height:1.6;transition:all 0.2s ease;">
                    <?= t('settings.preview_text') ?>
                </p>
            </div>
            
            <div id="scaleMessage" style="display:none;margin-top:1rem;padding:0.75rem 1rem;border-radius:10px;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;font-size:0.9rem;">
                <i class="fas fa-check-circle"></i> <span id="scaleMessageText">UI scale saved</span>
            </div>
        </div>
    </div>
</div>

<!-- SMTP Tab -->
<div id="tab-smtp" class="tab-content <?= $activeTab === 'smtp' ? 'active' : '' ?>">
    <div class="info-box" style="margin-bottom:1.5rem;">
        <p>
            <i class="fas fa-info-circle me-1"></i>
            <strong>Pengaturan SMTP Pribadi:</strong> Setiap akun memiliki konfigurasi email masing-masing. 
            Perubahan di sini hanya berlaku untuk akun Anda, tidak mempengaruhi akun lain.
        </p>
    </div>
    <form method="POST" action="<?= url('/crewing/settings/save-smtp') ?>">
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="card-icon server"><i class="fas fa-server"></i></div>
                <h3>SMTP Configuration (<?= htmlspecialchars($user['full_name'] ?? 'Personal') ?>)</h3>
            </div>
            <div class="settings-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-globe"></i> SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= htmlspecialchars($smtpSettings['smtp_host']) ?>" placeholder="mail.indooceancrew.co.id">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Port</label>
                        <input type="number" name="smtp_port" value="<?= htmlspecialchars($smtpSettings['smtp_port']) ?>" placeholder="465">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username (Email)</label>
                        <input type="text" name="smtp_username" value="<?= htmlspecialchars($smtpSettings['smtp_username']) ?>" placeholder="nama@indooceancrew.co.id">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Password</label>
                        <input type="password" name="smtp_password" placeholder="<?= !empty($smtpSettings['smtp_password']) ? '••••••••• (tersimpan)' : 'Masukkan password' ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-shield-alt"></i> Encryption</label>
                        <select name="smtp_encryption">
                            <option value="ssl" <?= ($smtpSettings['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                            <option value="tls" <?= ($smtpSettings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> From Email</label>
                        <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($smtpSettings['smtp_from_email']) ?>" placeholder="nama@indooceancrew.co.id">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> From Name</label>
                        <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($smtpSettings['smtp_from_name']) ?>">
                    </div>
                </div>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save me-1"></i> <?= t('settings.save_smtp') ?>
            </button>
        </div>
    </form>
</div>

<!-- Photo Viewer Modal -->
<div id="photoViewerModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.9);z-index:99999;align-items:center;justify-content:center;" onclick="closePhotoViewer()">
    <div style="max-width:90%;max-height:90%;position:relative;">
        <button onclick="closePhotoViewer()" style="position:absolute;top:-40px;right:0;background:white;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:20px;color:#333;">
            ×
        </button>
        <img id="photoViewerImage" src="" style="max-width:100%;max-height:90vh;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.5);">
    </div>
</div>

<!-- Backup Tab -->
<div id="tab-backup" class="tab-content <?= $activeTab === 'backup' ? 'active' : '' ?>">
    <!-- Download Backup -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="card-icon backup"><i class="fas fa-download"></i></div>
            <h3>Download Database Backup</h3>
        </div>
        <div class="settings-card-body">
            <div class="info-box">
                <p>
                    <i class="fas fa-info-circle me-1"></i>
                    Download a complete backup of the recruitment database as an SQL file. 
                    This backup includes all tables, data, and structure.
                </p>
            </div>
            <form method="POST" action="<?= url('/crewing/settings/backup-database') ?>">
                <button type="submit" class="btn-warning">
                    <i class="fas fa-download me-1"></i> Download Database Backup
                </button>
            </form>
        </div>
    </div>
    
    <!-- Import/Restore Backup -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="card-icon backup" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="fas fa-upload"></i></div>
            <h3>Import/Restore Database</h3>
        </div>
        <div class="settings-card-body">
            <div class="info-box" style="background:linear-gradient(135deg,#fef3c7,#fde68a);border-color:#fbbf24;">
                <p style="color:#92400e;">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>WARNING:</strong> Importing a database file will REPLACE all existing data. 
                    Make sure to download a backup first! Only upload SQL files created by this system.
                </p>
            </div>
            <form method="POST" action="<?= url('/crewing/settings/import-database') ?>" enctype="multipart/form-data" id="importForm">
                <div class="form-group">
                    <label><i class="fas fa-file-import"></i> Select SQL Backup File</label>
                    <input type="file" name="sql_file" accept=".sql" required 
                           style="padding:0.75rem;border:2px dashed #cbd5e1;border-radius:10px;">
                    <small style="display:block;margin-top:0.5rem;color:#64748b;">
                        <i class="fas fa-info-circle"></i> Only .sql files are accepted (created from database backup)
                    </small>
                </div>
                <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#10b981,#059669);" 
                        onclick="return confirm('⚠️ WARNING! This will REPLACE ALL existing data with the uploaded backup. Continue?')">
                    <i class="fas fa-upload me-1"></i> Import Database
                </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete All Data -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="card-icon backup" style="background:linear-gradient(135deg,#ef4444,#dc2626);"><i class="fas fa-trash-alt"></i></div>
            <h3>Delete All Data (DANGER)</h3>
        </div>
        <div class="settings-card-body">
            <div class="info-box" style="background:linear-gradient(135deg,#fee2e2,#fecaca);border-color:#f87171;">
                <p style="color:#991b1b;word-wrap:break-word;white-space:normal;overflow-wrap:break-word;">
                    <i class="fas fa-skull-crossbones me-1"></i>
                    <strong>EXTREME DANGER:</strong> This will permanently DELETE application data (applications, vacancies, etc.) but will PRESERVE user accounts, roles, and system settings.
                    This action CANNOT be undone. Make sure you have downloaded a backup first!
                </p>
            </div>
            <button type="button" class="btn-primary" style="background:linear-gradient(135deg,#ef4444,#dc2626);" 
                    onclick="showDeleteConfirmation()">
                <i class="fas fa-trash-alt me-1"></i> Delete All Data
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.85);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:16px;padding:2rem;max-width:500px;width:90%;">
        <div style="text-align:center;color:#dc2626;margin-bottom:1.5rem;">
            <i class="fas fa-exclamation-triangle" style="font-size:4rem;"></i>
        </div>
        <h3 style="text-align:center;color:#991b1b;margin-bottom:1rem;">⚠️ CONFIRM DATA DELETION ⚠️</h3>
        <p style="color:#64748b;text-align:center;margin-bottom:1.5rem;">
            This will <strong>PERMANENTLY DELETE ALL DATA</strong> from the database!<br>
            Type <code style="background:#fee2e2;padding:4px 8px;border-radius:4px;color:#991b1b;">DELETE ALL DATA</code> to confirm:
        </p>
        <form method="POST" action="<?= url('/crewing/settings/delete-all-data') ?>" id="deleteForm">
            <input type="text" name="confirmation" id="confirmationInput" 
                   style="width:100%;padding:0.75rem;border:2px solid #f87171;border-radius:10px;font-size:1rem;text-align:center;"
                   placeholder="Type: DELETE ALL DATA"
                   autocomplete="off">
            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="button" onclick="closeDeleteConfirmation()" 
                        style="flex:1;padding:0.75rem;border:2px solid #e2e8f0;border-radius:10px;background:white;cursor:pointer;font-weight:600;">
                    Cancel
                </button>
                <button type="submit" id="confirmDeleteBtn" disabled
                        style="flex:1;padding:0.75rem;border:none;border-radius:10px;background:#dc2626;color:white;cursor:not-allowed;font-weight:600;">
                    Delete Everything
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    // Update URL
    window.history.pushState({}, '', '<?= url('/crewing/settings') ?>?tab=' + tab);
    
    // Update tabs
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelector('.tab[onclick*="' + tab + '"]').classList.add('active');
    
    // Update content
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
}

// Photo Upload Functionality
const photoInput = document.getElementById('photoInput');
const uploadDropzone = document.getElementById('uploadDropzone');
const photoPreview = document.getElementById('photoPreview');

if (photoInput && uploadDropzone) {
    // Click to upload
    uploadDropzone.addEventListener('click', () => {
        photoInput.click();
    });
    
    // File selected
    photoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            validateAndPreviewFile(file);
        }
    });
    
    // Drag and drop
    uploadDropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadDropzone.classList.add('dragover');
    });
    
    uploadDropzone.addEventListener('dragleave', () => {
        uploadDropzone.classList.remove('dragover');
    });
    
    uploadDropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadDropzone.classList.remove('dragover');
        
        const file = e.dataTransfer.files[0];
        if (file) {
            // Set file to input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            photoInput.files = dataTransfer.files;
            
            validateAndPreviewFile(file);
        }
    });
}

function validateAndPreviewFile(file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload a JPG, PNG, or GIF image');
        photoInput.value = '';
        return;
    }
    
    // Validate file size (2MB max)
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
    if (file.size > maxSize) {
        alert('File size must be less than 2MB');
        photoInput.value = '';
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = (e) => {
        if (photoPreview.tagName === 'IMG') {
            photoPreview.src = e.target.result;
        } else {
            // Replace placeholder with image
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Profile Photo';
            img.id = 'photoPreview';
            photoPreview.parentNode.replaceChild(img, photoPreview);
        }
    };
    reader.readAsDataURL(file);
}

// Photo Viewer Functions
function viewPhotoFull() {
    const photoSrc = document.getElementById('photoPreview').src;
    if (photoSrc) {
        document.getElementById('photoViewerImage').src = photoSrc;
        const modal = document.getElementById('photoViewerModal');
        modal.style.display = 'flex';
    }
}

function closePhotoViewer() {
    const modal = document.getElementById('photoViewerModal');
    modal.style.display = 'none';
}

// Prevent clicks on image from closing modal
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('photoViewerImage')) {
        document.getElementById('photoViewerImage').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Delete confirmation input validation
    const confirmInput = document.getElementById('confirmationInput');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmInput && confirmBtn) {
        confirmInput.addEventListener('input', function() {
            if (this.value === 'DELETE ALL DATA') {
                confirmBtn.disabled = false;
                confirmBtn.style.cursor = 'pointer';
                confirmBtn.style.background = '#dc2626';
            } else {
                confirmBtn.disabled = true;
                confirmBtn.style.cursor = 'not-allowed';
                confirmBtn.style.background = '#9ca3af';
            }
        });
    }
});

// Delete confirmation modal functions
function showDeleteConfirmation() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.style.display = 'flex';
    document.getElementById('confirmationInput').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
}

function closeDeleteConfirmation() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.style.display = 'none';
    document.getElementById('confirmationInput').value = '';
}

// UI Scale Slider Functionality
const uiScaleSlider = document.getElementById('uiScaleSlider');
const scaleValue = document.getElementById('scaleValue');
const previewText = document.getElementById('previewText');
const scaleMessage = document.getElementById('scaleMessage');
const scaleMessageText = document.getElementById('scaleMessageText');

if (uiScaleSlider) {
    let saveTimeout;
    
    uiScaleSlider.addEventListener('input', function() {
        const scale = parseFloat(this.value);
        const percentage = Math.round(scale * 100);
        
        // Update displayed percentage
        scaleValue.textContent = percentage + '%';
        
        // Update preview text scale
        if (previewText) {
            previewText.style.transform = `scale(${scale})`;
            previewText.style.transformOrigin = 'top left';
        }
        
        // Clear previous timeout
        clearTimeout(saveTimeout);
        
        // Auto-save after 500ms of no changes
        saveTimeout = setTimeout(() => {
            saveUiScale(scale);
        }, 500);
    });
}

function saveUiScale(scale) {
    fetch('<?= url('/crewing/settings/save-ui-scale') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `scale=${scale}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Show success message
            scaleMessageText.textContent = 'Tersimpan! Refresh halaman untuk melihat perubahan di seluruh aplikasi.';
            scaleMessage.style.display = 'block';
            
            // Hide message after 3 seconds
            setTimeout(() => {
                scaleMessage.style.display = 'none';
            }, 3000);
        } else {
            scaleMessageText.textContent = 'Error: ' + (data.message || 'Failed to save');
            scaleMessage.style.background = '#fee2e2';
            scaleMessage.style.color = '#991b1b';
            scaleMessage.style.borderColor = '#fecaca';
            scaleMessage.style.display = 'block';
        }
    })
    .catch(err => {
        console.error('Error saving UI scale:', err);
    });
}

// Language Selector
function saveLanguage(lang) {
    const langMessage = document.getElementById('langMessage');
    const langMessageText = document.getElementById('langMessageText');
    
    fetch('<?= url('/crewing/settings/save-language') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `language=${lang}&csrf_token=<?= csrf_token() ?>`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            langMessageText.textContent = lang === 'id' ? '✓ Bahasa berhasil diubah! Memuat ulang...' : '✓ Language saved! Reloading...';
            langMessage.style.display = 'block';
            // Reload page after 1 second to apply language
            setTimeout(() => {
                window.location.href = '<?= url('/crewing/settings') ?>?tab=display';
            }, 800);
        } else {
            langMessageText.textContent = 'Error: ' + (data.message || 'Failed');
            langMessage.style.background = '#fee2e2';
            langMessage.style.color = '#991b1b';
            langMessage.style.borderColor = '#fecaca';
            langMessage.style.display = 'block';
        }
    })
    .catch(err => {
        console.error('Error saving language:', err);
    });
}
</script>
