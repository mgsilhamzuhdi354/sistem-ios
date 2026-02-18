<?php
/**
 * Integration Status View
 */
$currentPage = 'monitoring';
ob_start();
?>
<div class="page-header">
    <h1>Integration Status</h1>
    <p>Monitor koneksi ke semua sistem terintegrasi</p>
</div>

<div class="grid-3">
    <?php foreach ($integrations as $key => $integration): ?>
        <div class="card">
            <div style="display: flex; align-items: start; gap: 12px; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; 
                        background: <?= $integration['status'] === 'connected' ? 'var(--success)' : ($integration['status'] === 'error' ? 'var(--danger)' : 'var(--warning)') ?>; 
                        display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-<?= $integration['status'] === 'connected' ? 'check' : ($integration['status'] === 'error' ? 'times' : 'exclamation') ?>"
                        style="color: white; font-size: 24px;"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 4px 0;">
                        <?= htmlspecialchars($integration['name']) ?>
                    </h3>
                    <span
                        class="badge badge-<?= $integration['status'] === 'connected' ? 'success' : ($integration['status'] === 'error' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($integration['status']) ?>
                    </span>
                </div>
            </div>

            <div style="padding: 12px; background: var(--card-bg); border-radius: 8px; margin-bottom: 12px;">
                <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 4px;">Status</div>
                <div>
                    <?= htmlspecialchars($integration['message']) ?>
                </div>
            </div>

            <div style="font-size: 12px; color: var(--text-muted);">
                <i class="fas fa-clock"></i> Last check:
                <?= $integration['last_check'] ?>
            </div>

            <?php if ($integration['status'] === 'error'): ?>
                <div style="margin-top: 12px;">
                    <button onclick="retryConnection('<?= $key ?>')" class="btn btn-sm btn-secondary" style="width: 100%;">
                        <i class="fas fa-sync"></i> Retry Connection
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 16px;">System Information</h3>

    <div class="grid-2">
        <div>
            <h4 style="color: var(--accent-gold); margin-bottom: 12px;">
                <i class="fas fa-server"></i> HRIS Absensi Integration
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                    <strong>Base URL:</strong>
                    <code>http://localhost/absensi/aplikasiabsensibygerry/public/api</code>
                </li>
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                    <strong>Protocol:</strong> REST API
                </li>
                <li style="padding: 8px 0;">
                    <strong>Endpoints:</strong> /employees, /attendance, /payroll
                </li>
            </ul>
        </div>

        <div>
            <h4 style="color: var(--accent-gold); margin-bottom: 12px;">
                <i class="fas fa-database"></i> Recruitment System
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                    <strong>Database:</strong>
                    <code>recruitment_db</code>
                </li>
                <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                    <strong>Protocol:</strong> Direct Database
                </li>
                <li style="padding: 8px 0;">
                    <strong>Tables:</strong> applications, candidates
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 16px;">Troubleshooting Guide</h3>

    <div
        style="padding: 16px; background: var(--card-bg); border-radius: 8px; border-left: 4px solid var(--accent-gold);">
        <h4 style="margin-top: 0;">HRIS Connection Issues</h4>
        <ol>
            <li>Pastikan HRIS Laravel app sudah running di
                <code>http://localhost/absensi/aplikasiabsensibygerry/public</code>
            </li>
            <li>Cek API endpoints sudah dibuat di <code>routes/api.php</code></li>
            <li>Test langsung: <code>curl http://localhost/absensi/aplikasiabsensibygerry/public/api/employees</code>
            </li>
            <li>Periksa CORS settings jika ada error cross-origin</li>
        </ol>
    </div>

    <div
        style="padding: 16px; background: var(--card-bg); border-radius: 8px; border-left: 4px solid var(--info); margin-top: 12px;">
        <h4 style="margin-top: 0;">Recruitment Connection Issues</h4>
        <ol>
            <li>Pastikan database <code>recruitment_db</code> exists</li>
            <li>Check credentials di <code>.env</code> file</li>
            <li>Verify table <code>applications</code> ada di database</li>
        </ol>
    </div>

    <div
        style="padding: 16px; background: var(--card-bg); border-radius: 8px; border-left: 4px solid var(--success); margin-top: 12px;">
        <h4 style="margin-top: 0;">Company Profile Tracking</h4>
        <ol>
            <li>Tambahkan tracking script di semua pages Company Profile</li>
            <li>Pastikan endpoint <code>/api/track-visitor</code> sudah dibuat</li>
            <li>Test dengan membuka Company Profile website</li>
        </ol>
    </div>
</div>

<script>
    function retryConnection(system) {
        alert('Retrying connection to ' + system + '...');
        window.location.reload();
    }
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>