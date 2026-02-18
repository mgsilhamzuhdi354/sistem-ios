<!-- Semua Pelamar Saya - Crewing -->
<div class="welcome-banner" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #8b5cf6 100%);">
    <div class="welcome-content">
        <div class="welcome-icon"><i class="fas fa-users"></i></div>
        <div class="welcome-text">
            <h1>Semua Pelamar Saya</h1>
            <p>Kandidat manual, online, dan yang ditugaskan kepada Anda</p>
        </div>
    </div>
    <a href="<?= url('/crewing/manual-entry') ?>" class="btn-header-action">
        <i class="fas fa-plus-circle"></i> Tambah Manual
    </a>
</div>

<!-- Stats Row -->
<div class="stats-row-crewing">
    <div class="stat-card-crewing blue">
        <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
        <div class="stat-content"><h2><?= $stats['total'] ?></h2><span>Total Pelamar</span></div>
    </div>
    <div class="stat-card-crewing green">
        <div class="stat-icon-wrap"><i class="fas fa-hand-point-right"></i></div>
        <div class="stat-content"><h2><?= $stats['manual'] ?></h2><span>Input Manual</span></div>
    </div>
    <div class="stat-card-crewing purple">
        <div class="stat-icon-wrap"><i class="fas fa-globe"></i></div>
        <div class="stat-content"><h2><?= $stats['online'] ?></h2><span>Daftar Online</span></div>
    </div>
    <div class="stat-card-crewing teal">
        <div class="stat-icon-wrap"><i class="fas fa-check-circle"></i></div>
        <div class="stat-content"><h2><?= $stats['approved'] ?></h2><span>Approved</span></div>
    </div>
    <div class="stat-card-crewing orange">
        <div class="stat-icon-wrap"><i class="fas fa-clock"></i></div>
        <div class="stat-content"><h2><?= $stats['in_progress'] ?></h2><span>In Progress</span></div>
    </div>
    <div class="stat-card-crewing gold">
        <div class="stat-icon-wrap"><i class="fas fa-cloud-upload-alt"></i></div>
        <div class="stat-content"><h2><?= $stats['synced'] ?? 0 ?></h2><span>Synced ERP</span></div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <button class="filter-tab active" data-filter="all">
        <i class="fas fa-th-list"></i> Semua <span class="tab-count"><?= $stats['total'] ?></span>
    </button>
    <button class="filter-tab" data-filter="manual">
        <i class="fas fa-hand-point-right"></i> Manual <span class="tab-count"><?= $stats['manual'] ?></span>
    </button>
    <button class="filter-tab" data-filter="online">
        <i class="fas fa-globe"></i> Online <span class="tab-count"><?= $stats['online'] ?></span>
    </button>
</div>

<!-- Search -->
<div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="searchInput" placeholder="Cari nama, email, posisi..." oninput="filterEntries()">
</div>

<!-- Entries -->
<?php if (empty($entries)): ?>
    <div class="empty-card">
        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
        <h3>Belum Ada Pelamar</h3>
        <p>Anda belum memiliki pelamar yang ditugaskan atau diinput manual.</p>
        <a href="<?= url('/crewing/manual-entry') ?>" class="btn-add-first"><i class="fas fa-plus"></i> Tambah Pelamar Pertama</a>
    </div>
<?php else: ?>
    <div class="entries-grid" id="entriesGrid">
        <?php foreach ($entries as $entry): ?>
        <div class="applicant-card" 
             data-source="<?= $entry['entry_source'] ?>" 
             data-name="<?= strtolower(htmlspecialchars($entry['candidate_name'])) ?>"
             data-email="<?= strtolower(htmlspecialchars($entry['email'])) ?>"
             data-vacancy="<?= strtolower(htmlspecialchars($entry['vacancy_title'])) ?>">
            
            <!-- Card Header -->
            <div class="acard-header">
                <div class="acard-avatar <?= $entry['entry_source'] === 'manual' ? 'manual' : 'online' ?> <?= !empty($entry['avatar']) ? 'has-photo' : '' ?>">
                    <?php if (!empty($entry['avatar'])): ?>
                        <img src="<?= url('/' . $entry['avatar']) ?>" alt="<?= htmlspecialchars($entry['candidate_name']) ?>" class="avatar-img">
                    <?php else: ?>
                        <?= strtoupper(substr($entry['candidate_name'], 0, 2)) ?>
                    <?php endif; ?>
                </div>
                <div class="acard-identity">
                    <h4><?= htmlspecialchars($entry['candidate_name']) ?></h4>
                    <span class="acard-vacancy"><i class="fas fa-briefcase"></i> <?= htmlspecialchars($entry['vacancy_title']) ?></span>
                    <?php if ($entry['department_name']): ?>
                        <span class="acard-dept"><?= htmlspecialchars($entry['department_name']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="acard-badges">
                    <span class="status-badge" style="background-color: <?= $entry['status_color'] ?>">
                        <?= htmlspecialchars($entry['status_name_id'] ?? $entry['status_name']) ?>
                    </span>
                    <span class="source-badge <?= $entry['entry_source'] ?>">
                        <i class="fas fa-<?= $entry['entry_source'] === 'manual' ? 'hand-point-right' : 'globe' ?>"></i>
                        <?= $entry['entry_source'] === 'manual' ? 'Manual' : 'Online' ?>
                    </span>
                    <?php if (!empty($entry['is_synced_to_erp'])): ?>
                    <span class="erp-synced-badge"><i class="fas fa-check-circle"></i> ERP ✓</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Body - Quick Info -->
            <div class="acard-body">
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($entry['email']) ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($entry['phone']) ?></span>
                    </div>
                    <?php if ($entry['gender']): ?>
                    <div class="info-item">
                        <i class="fas fa-<?= strtolower($entry['gender']) === 'male' ? 'mars' : 'venus' ?>"></i>
                        <span><?= $entry['gender'] === 'Male' || $entry['gender'] === 'male' ? 'Laki-laki' : 'Perempuan' ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($entry['date_of_birth']): ?>
                    <div class="info-item">
                        <i class="fas fa-birthday-cake"></i>
                        <span><?= date('d M Y', strtotime($entry['date_of_birth'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($entry['city']): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($entry['city']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($entry['nationality']): ?>
                    <div class="info-item">
                        <i class="fas fa-flag"></i>
                        <span><?= htmlspecialchars($entry['nationality']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Maritime Info -->
                <?php if ($entry['seaman_book_no'] || $entry['last_rank'] || $entry['total_sea_service_months']): ?>
                <div class="maritime-info">
                    <?php if ($entry['seaman_book_no']): ?>
                    <div class="sea-tag"><i class="fas fa-id-card"></i> BST: <?= htmlspecialchars($entry['seaman_book_no']) ?></div>
                    <?php endif; ?>
                    <?php if ($entry['passport_no']): ?>
                    <div class="sea-tag"><i class="fas fa-passport"></i> <?= htmlspecialchars($entry['passport_no']) ?></div>
                    <?php endif; ?>
                    <?php if ($entry['total_sea_service_months'] > 0): ?>
                    <div class="sea-tag"><i class="fas fa-anchor"></i> <?= $entry['total_sea_service_months'] ?> bulan laut</div>
                    <?php endif; ?>
                    <?php if ($entry['last_rank']): ?>
                    <div class="sea-tag"><i class="fas fa-user-tie"></i> <?= htmlspecialchars($entry['last_rank']) ?></div>
                    <?php endif; ?>
                    <?php if ($entry['last_vessel_name']): ?>
                    <div class="sea-tag"><i class="fas fa-ship"></i> <?= htmlspecialchars($entry['last_vessel_name']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Physical -->
                <?php if ($entry['height_cm'] || $entry['weight_kg'] || $entry['blood_type']): ?>
                <div class="physical-info">
                    <?php if ($entry['height_cm']): ?><span><i class="fas fa-ruler-vertical"></i> <?= $entry['height_cm'] ?> cm</span><?php endif; ?>
                    <?php if ($entry['weight_kg']): ?><span><i class="fas fa-weight"></i> <?= $entry['weight_kg'] ?> kg</span><?php endif; ?>
                    <?php if ($entry['blood_type']): ?><span><i class="fas fa-tint"></i> <?= $entry['blood_type'] ?></span><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Card Footer with Actions -->
            <div class="acard-footer">
                <span class="acard-date"><i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($entry['created_at'])) ?></span>
                <div class="acard-actions">
                    <a href="<?= url('/crewing/manual-entries/detail/' . $entry['id']) ?>" class="btn-action btn-detail" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= url('/crewing/manual-entries/edit/' . $entry['id']) ?>" class="btn-action btn-edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php 
                    $isApproved = in_array(strtolower($entry['status_name']), ['approved', 'hired']);
                    $isSynced = !empty($entry['is_synced_to_erp']);
                    ?>
                    <?php if ($isApproved && !$isSynced): ?>
                    <form method="POST" action="<?= url('/crewing/manual-entries/push-erp/' . $entry['id']) ?>" style="display:inline" onsubmit="return confirm('Push ke ERP?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-action btn-erp" title="Push ke ERP"><i class="fas fa-upload"></i></button>
                    </form>
                    <?php endif; ?>
                    <button onclick="confirmDelete(<?= $entry['id'] ?>, '<?= htmlspecialchars(addslashes($entry['candidate_name'])) ?>')" class="btn-action btn-del" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Delete Modal -->
<div id="deleteModal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <div class="modal-header"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</div>
        <p>Apakah Anda yakin ingin menghapus data pelamar <strong id="deleteTargetName"></strong>?</p>
        <p style="color:#ef4444;font-size:0.85rem">⚠️ Semua data akan dihapus permanen.</p>
        <form id="deleteForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-actions">
                <button type="button" onclick="closeDeleteModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-confirm-delete"><i class="fas fa-trash"></i> Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Header Action Button */
.btn-header-action { background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; border: 1px solid rgba(255,255,255,0.3); }
.btn-header-action:hover { background: rgba(255,255,255,0.3); color: white; transform: translateY(-2px); }

/* Stats - 6 columns */
.stats-row-crewing { grid-template-columns: repeat(6, 1fr) !important; }
.stat-card-crewing.gold { --card-color: #f59e0b; }
.stat-card-crewing.gold .stat-icon-wrap { background: linear-gradient(135deg, #f59e0b, #d97706); }

/* Filter Tabs */
.filter-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; background: white; padding: 0.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.filter-tab { border: none; background: transparent; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; color: #6b7280; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; font-family: inherit; }
.filter-tab:hover { background: #f3f4f6; color: #374151; }
.filter-tab.active { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
.tab-count { background: rgba(0,0,0,0.15); padding: 0.15rem 0.5rem; border-radius: 10px; font-size: 0.75rem; }
.filter-tab.active .tab-count { background: rgba(255,255,255,0.25); }

/* Search */
.search-box { position: relative; margin-bottom: 1.25rem; }
.search-box i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.search-box input { width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 0.9rem; background: white; transition: all 0.3s; box-sizing: border-box; font-family: inherit; }
.search-box input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); outline: none; }

/* Entries Grid */
.entries-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }

/* Applicant Card */
.applicant-card { background: white; border-radius: 16px; box-shadow: 0 3px 15px rgba(0,0,0,0.06); overflow: hidden; transition: all 0.3s; border: 1px solid #f3f4f6; }
.applicant-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.1); transform: translateY(-3px); border-color: #dbeafe; }

.acard-header { display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; }
.acard-avatar { width: 80px; height: 80px; min-width: 80px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem; position: relative; overflow: hidden; box-shadow: 0 3px 12px rgba(0,0,0,0.12); transition: transform 0.3s; }
.acard-avatar:hover { transform: scale(1.05); }
.acard-avatar.manual { background: linear-gradient(135deg, #10b981, #059669); }
.acard-avatar.online { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.acard-avatar.has-photo { border: 3px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
.acard-avatar .avatar-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; background: white; }
.acard-identity { flex: 1; min-width: 0; }
.acard-identity h4 { margin: 0; font-size: 0.95rem; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.acard-vacancy { font-size: 0.78rem; color: #6b7280; display: block; margin-top: 0.15rem; }
.acard-vacancy i { margin-right: 0.2rem; }
.acard-dept { font-size: 0.7rem; color: #9ca3af; display: block; }
.acard-badges { display: flex; flex-direction: column; gap: 0.3rem; align-items: flex-end; }
.status-badge { padding: 0.2rem 0.6rem; border-radius: 15px; color: white; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; white-space: nowrap; }
.source-badge { padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.65rem; font-weight: 600; }
.source-badge.manual { background: #dcfce7; color: #16a34a; }
.source-badge.online { background: #dbeafe; color: #2563eb; }
.erp-synced-badge { padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.6rem; font-weight: 600; background: #fef3c7; color: #92400e; }

.acard-body { padding: 0.75rem 1.25rem; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem; }
.info-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; color: #6b7280; }
.info-item i { width: 14px; text-align: center; color: #9ca3af; font-size: 0.7rem; }

.maritime-info { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.6rem; padding-top: 0.6rem; border-top: 1px dashed #e5e7eb; }
.sea-tag { background: #f0f9ff; color: #0369a1; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.7rem; font-weight: 500; display: flex; align-items: center; gap: 0.25rem; }
.sea-tag i { font-size: 0.6rem; }

.physical-info { display: flex; gap: 0.75rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e5e7eb; }
.physical-info span { font-size: 0.72rem; color: #6b7280; display: flex; align-items: center; gap: 0.25rem; }
.physical-info i { font-size: 0.65rem; color: #9ca3af; }

.acard-footer { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 1.25rem; background: #f8fafc; border-top: 1px solid #f3f4f6; }
.acard-date { font-size: 0.72rem; color: #9ca3af; }
.acard-date i { margin-right: 0.2rem; }
.acard-actions { display: flex; gap: 0.35rem; }
.btn-action { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; border: none; cursor: pointer; transition: all 0.3s; text-decoration: none; font-family: inherit; }
.btn-detail { background: #dbeafe; color: #2563eb; }
.btn-detail:hover { background: #3b82f6; color: white; transform: translateY(-2px); }
.btn-edit { background: #dcfce7; color: #16a34a; }
.btn-edit:hover { background: #16a34a; color: white; transform: translateY(-2px); }
.btn-erp { background: #fef3c7; color: #d97706; }
.btn-erp:hover { background: #f59e0b; color: white; transform: translateY(-2px); }
.btn-del { background: #fee2e2; color: #dc2626; }
.btn-del:hover { background: #ef4444; color: white; transform: translateY(-2px); }

/* Empty State */
.empty-card { background: white; border-radius: 16px; padding: 3rem; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.empty-icon { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; }
.empty-card h3 { color: #374151; margin: 0 0 0.5rem; }
.empty-card p { color: #9ca3af; margin: 0 0 1.5rem; }
.btn-add-first { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; }
.btn-add-first:hover { transform: translateY(-2px); color: white; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; }
.modal-box { background: white; border-radius: 16px; padding: 2rem; max-width: 440px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { font-size: 1.1rem; font-weight: 700; color: #ef4444; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.modal-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; justify-content: flex-end; }
.btn-cancel { padding: 0.5rem 1.25rem; border: 2px solid #d1d5db; background: white; color: #374151; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-confirm-delete { padding: 0.5rem 1.25rem; background: #ef4444; color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-confirm-delete:hover { background: #dc2626; }

@media (max-width: 1200px) {
    .entries-grid { grid-template-columns: 1fr; }
    .stats-row-crewing { grid-template-columns: repeat(3, 1fr) !important; }
}
@media (max-width: 768px) {
    .stats-row-crewing { grid-template-columns: repeat(2, 1fr) !important; }
    .info-grid { grid-template-columns: 1fr; }
    .filter-tabs { flex-wrap: wrap; }
}
</style>

<script>
// Filter by source
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('.applicant-card').forEach(card => {
            if (filter === 'all' || card.dataset.source === filter) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Search
function filterEntries() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.applicant-card').forEach(card => {
        const name = card.dataset.name || '';
        const email = card.dataset.email || '';
        const vacancy = card.dataset.vacancy || '';
        card.style.display = (name.includes(q) || email.includes(q) || vacancy.includes(q)) ? '' : 'none';
    });
}

// Delete modal
function confirmDelete(id, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = '<?= url('/crewing/manual-entries/delete/') ?>' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
