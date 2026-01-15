<?= $this->extend('layouts/master_admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Migration & Status</h5>
                        <p class="text-sm text-muted mb-0">Kelola struktur database dan cek koneksi</p>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Database Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white">Database</h6>
                                    <h4 class="text-white mb-0"><?= esc($dbInfo['database']) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body">
                                    <h6 class="text-white">MySQL Version</h6>
                                    <h4 class="text-white mb-0"><?= esc($dbInfo['version']) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body">
                                    <h6 class="text-white">Total Tabel</h6>
                                    <h4 class="text-white mb-0"><?= count($tables) ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary me-2" id="btnCheckConnection">
                            <i class="fas fa-plug me-2"></i>Cek Koneksi
                        </button>
                        <button type="button" class="btn btn-warning me-2" id="btnRunMigration">
                            <i class="fas fa-sync-alt me-2"></i>Jalankan Migration
                        </button>
                        <button type="button" class="btn btn-info" id="btnRefresh">
                            <i class="fas fa-redo me-2"></i>Refresh
                        </button>
                    </div>
                    
                    <!-- Result Area -->
                    <div id="resultArea" class="mb-4" style="display: none;">
                        <div class="alert" id="resultAlert">
                            <h6 id="resultTitle" class="alert-heading mb-2"></h6>
                            <div id="resultContent"></div>
                        </div>
                    </div>
                    
                    <!-- Tables List -->
                    <h6 class="mb-3"><i class="fas fa-table me-2"></i>Daftar Tabel (<?= count($tables) ?>)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nama Tabel</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tables as $i => $table): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <code><?= esc($table) ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info btnViewStructure" 
                                                data-table="<?= esc($table) ?>">
                                            <i class="fas fa-eye"></i> Struktur
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Structure -->
<div class="modal fade" id="modalStructure" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Struktur Tabel: <span id="modalTableName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="structureContent">Loading...</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultArea = document.getElementById('resultArea');
    const resultAlert = document.getElementById('resultAlert');
    const resultTitle = document.getElementById('resultTitle');
    const resultContent = document.getElementById('resultContent');
    
    function showResult(status, title, content) {
        resultArea.style.display = 'block';
        resultAlert.className = 'alert alert-' + (status === 'success' ? 'success' : status === 'error' ? 'danger' : 'warning');
        resultTitle.textContent = title;
        resultContent.innerHTML = content;
    }
    
    // Check Connection
    document.getElementById('btnCheckConnection').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking...';
        
        fetch('<?= base_url('master-admin/db-migration/check-connection') ?>')
            .then(res => res.json())
            .then(data => {
                let content = '<p class="mb-2">' + data.message + '</p>';
                if (data.details) {
                    content += '<ul class="mb-0">';
                    content += '<li>Database: <code>' + data.details.database + '</code></li>';
                    content += '<li>Version: ' + data.details.version + '</li>';
                    content += '<li>Tables: ' + data.details.tables_count + '</li>';
                    content += '</ul>';
                }
                showResult(data.status, data.status === 'success' ? '✅ Koneksi Berhasil' : '❌ Koneksi Gagal', content);
            })
            .catch(err => {
                showResult('error', '❌ Error', err.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-plug me-2"></i>Cek Koneksi';
            });
    });
    
    // Run Migration
    document.getElementById('btnRunMigration').addEventListener('click', function() {
        if (!confirm('Jalankan database migration? Ini akan mengupdate struktur database.')) return;
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Running...';
        
        fetch('<?= base_url('master-admin/db-migration/run-migrations') ?>')
            .then(res => res.json())
            .then(data => {
                let content = '<p class="mb-2">' + data.message + '</p>';
                
                if (data.results && data.results.length > 0) {
                    content += '<strong>Hasil:</strong><ul>';
                    data.results.forEach(r => {
                        content += '<li>' + r.migration + ': ';
                        if (r.details) {
                            Object.keys(r.details).forEach(key => {
                                if (Array.isArray(r.details[key]) && r.details[key].length > 0) {
                                    content += '<br><small>' + key + ': ' + r.details[key].join(', ') + '</small>';
                                }
                            });
                        }
                        content += '</li>';
                    });
                    content += '</ul>';
                }
                
                if (data.errors && data.errors.length > 0) {
                    content += '<strong class="text-danger">Errors:</strong><ul>';
                    data.errors.forEach(e => {
                        content += '<li class="text-danger">' + e.migration + ': ' + e.message + '</li>';
                    });
                    content += '</ul>';
                }
                
                showResult(data.status, data.status === 'success' ? '✅ Migration Selesai' : '⚠️ Migration Partial', content);
            })
            .catch(err => {
                showResult('error', '❌ Error', err.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Jalankan Migration';
            });
    });
    
    // Refresh
    document.getElementById('btnRefresh').addEventListener('click', function() {
        location.reload();
    });
    
    // View Structure
    document.querySelectorAll('.btnViewStructure').forEach(btn => {
        btn.addEventListener('click', function() {
            const table = this.dataset.table;
            document.getElementById('modalTableName').textContent = table;
            document.getElementById('structureContent').innerHTML = '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';
            
            new bootstrap.Modal(document.getElementById('modalStructure')).show();
            
            // You can add AJAX call here to get table structure
            // For now just show placeholder
            setTimeout(() => {
                document.getElementById('structureContent').innerHTML = 
                    '<p class="text-muted">Untuk melihat detail struktur, buka phpMyAdmin dan pilih tabel <code>' + table + '</code></p>' +
                    '<a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-primary btn-sm">Buka phpMyAdmin</a>';
            }, 500);
        });
    });
});
</script>
<?= $this->endSection() ?>
