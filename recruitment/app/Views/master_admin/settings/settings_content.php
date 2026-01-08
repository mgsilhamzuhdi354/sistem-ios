<!-- Master Admin Settings Content -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class="fas fa-cog me-2"></i>System Settings</h1>
        <p class="text-muted mb-0">Configure system behavior and automation</p>
    </div>
</div>

<form action="<?= url('/master-admin/settings/update') ?>" method="POST">
    <?= csrf_field() ?>
    
    <div class="row g-4">
        <?php foreach ($groupedSettings as $category => $catSettings): ?>
        <div class="col-lg-6">
            <div class="settings-card">
                <div class="settings-header">
                    <i class="fas <?= $category === 'Email Settings' ? 'fa-envelope' : ($category === 'Automation' ? 'fa-robot' : ($category === 'Interview Settings' ? 'fa-comments' : 'fa-sliders-h')) ?>"></i>
                    <h5><?= $category ?></h5>
                </div>
                <div class="settings-body">
                    <?php foreach ($catSettings as $setting): ?>
                    <div class="setting-item">
                        <label><?= ucwords(str_replace('_', ' ', $setting['setting_key'])) ?></label>
                        <?php 
                        $value = $setting['setting_value'];
                        $key = $setting['setting_key'];
                        
                        // Boolean settings
                        if (in_array(strtolower($value), ['true', 'false', '1', '0'])): ?>
                        <div class="toggle-switch">
                            <input type="hidden" name="settings[<?= $key ?>]" value="false">
                            <input type="checkbox" name="settings[<?= $key ?>]" id="<?= $key ?>" 
                                   value="true" <?= in_array(strtolower($value), ['true', '1']) ? 'checked' : '' ?>>
                            <label for="<?= $key ?>" class="toggle-label"></label>
                        </div>
                        <?php 
                        // Numeric settings
                        elseif (is_numeric($value)): ?>
                        <input type="number" name="settings[<?= $key ?>]" value="<?= $value ?>" class="form-control setting-input">
                        <?php 
                        // Text settings
                        else: ?>
                        <input type="text" name="settings[<?= $key ?>]" value="<?= htmlspecialchars($value) ?>" class="form-control setting-input">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($groupedSettings)): ?>
    <div class="empty-state">
        <i class="fas fa-cog"></i>
        <h5>No Settings Found</h5>
        <p>System settings will appear here once configured.</p>
    </div>
    <?php endif; ?>
    
    <div class="settings-footer">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i>Save Settings
        </button>
    </div>
</form>

<style>
.settings-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}
.settings-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.settings-header i {
    font-size: 1.25rem;
    opacity: 0.8;
}
.settings-header h5 {
    margin: 0;
    font-weight: 500;
}
.settings-body {
    padding: 1.5rem;
}
.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
}
.setting-item:last-child {
    border-bottom: none;
}
.setting-item label {
    font-weight: 500;
    color: #374151;
}
.setting-input {
    max-width: 200px;
    text-align: right;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
}
.toggle-switch input[type="checkbox"] {
    display: none;
}
.toggle-label {
    width: 50px;
    height: 26px;
    background: #e5e7eb;
    border-radius: 13px;
    cursor: pointer;
    position: relative;
    transition: background 0.3s;
}
.toggle-label::after {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: left 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.toggle-switch input:checked + .toggle-label {
    background: linear-gradient(90deg, #22c55e, #16a34a);
}
.toggle-switch input:checked + .toggle-label::after {
    left: 26px;
}

.settings-footer {
    margin-top: 2rem;
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 16px;
    color: #9ca3af;
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>
