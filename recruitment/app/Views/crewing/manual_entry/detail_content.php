<!-- Detail Pelamar -->
<div class="welcome-banner" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #8b5cf6 100%);">
    <div class="welcome-content">
        <div class="profile-photo-large <?= !empty($entry['avatar']) ? 'clickable' : '' ?>" <?php if (!empty($entry['avatar'])): ?> onclick="openDocViewer('<?= url('/' . $entry['avatar']) ?>', '<?= htmlspecialchars($entry['full_name']) ?>', 'image')" title="Klik untuk memperbesar foto"<?php endif; ?>>
            <?php if (!empty($entry['avatar'])): ?>
                <img src="<?= url('/' . $entry['avatar']) ?>" alt="<?= htmlspecialchars($entry['full_name']) ?>">
                <div class="photo-zoom-hint"><i class="fas fa-search-plus"></i></div>
            <?php else: ?>
                <span><?= strtoupper(substr($entry['full_name'], 0, 2)) ?></span>
            <?php endif; ?>
        </div>
        <div class="welcome-text">
            <h1><?= htmlspecialchars($entry['full_name']) ?></h1>
            <p><?= htmlspecialchars($entry['vacancy_title']) ?> — <?= htmlspecialchars($entry['department_name'] ?? '') ?></p>
        </div>
    </div>
    <div class="header-actions">
        <a href="<?= url('/crewing/manual-entries') ?>" class="btn-header-action"><i class="fas fa-arrow-left"></i> Kembali</a>
        <a href="<?= url('/crewing/manual-entries/edit/' . $entry['id']) ?>" class="btn-header-action"><i class="fas fa-edit"></i> Edit</a>
    </div>
</div>

<!-- Status & ERP Badges -->
<div class="detail-status-bar">
    <div class="status-info">
        <span class="status-badge-lg" style="background-color: <?= $entry['status_color'] ?>">
            <?= htmlspecialchars($entry['status_name_id'] ?? $entry['status_name']) ?>
        </span>
        <span class="source-badge-lg <?= $entry['entry_source'] ?>">
            <i class="fas fa-<?= $entry['entry_source'] === 'manual' ? 'hand-point-right' : 'globe' ?>"></i>
            <?= $entry['entry_source'] === 'manual' ? 'Manual Entry' : 'Online' ?>
        </span>
        <?php if (!empty($entry['is_synced_to_erp'])): ?>
        <span class="erp-badge synced"><i class="fas fa-check-circle"></i> Synced ke ERP (<?= $entry['erp_employee_id'] ?>)</span>
        <?php endif; ?>
    </div>
    <div class="status-actions">
        <?php 
        $isApproved = in_array(strtolower($entry['status_name']), ['approved', 'hired']);
        $isSynced = !empty($entry['is_synced_to_erp']);
        ?>
        <?php if ($isApproved && !$isSynced): ?>
        <form method="POST" action="<?= url('/crewing/manual-entries/push-erp/' . $entry['id']) ?>" style="display:inline" onsubmit="return confirm('Push data ke ERP System?\nEmployee ID akan di-generate otomatis.')">
            <?= csrf_field() ?>
            <button type="submit" class="btn-push-erp"><i class="fas fa-upload"></i> Push ke ERP</button>
        </form>
        <?php endif; ?>
        <button onclick="confirmDelete(<?= $entry['id'] ?>, '<?= htmlspecialchars(addslashes($entry['full_name'])) ?>')" class="btn-delete-entry"><i class="fas fa-trash"></i> Hapus</button>
    </div>
</div>

<!-- Detail Grid -->
<div class="detail-grid">
    <!-- Personal Information -->
    <div class="detail-card">
        <div class="detail-card-header"><i class="fas fa-user"></i> Informasi Pribadi</div>
        <div class="detail-card-body">
            <div class="detail-row"><span class="detail-label">Nama Lengkap</span><span class="detail-value"><?= htmlspecialchars($entry['full_name']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value"><a href="mailto:<?= $entry['email'] ?>"><?= htmlspecialchars($entry['email']) ?></a></span></div>
            <div class="detail-row"><span class="detail-label">Telepon</span><span class="detail-value"><a href="tel:<?= $entry['phone'] ?>"><?= htmlspecialchars($entry['phone']) ?></a></span></div>
            <?php if ($entry['gender']): ?>
            <div class="detail-row"><span class="detail-label">Jenis Kelamin</span><span class="detail-value"><?= $entry['gender'] === 'Male' || $entry['gender'] === 'male' ? 'Laki-laki' : 'Perempuan' ?></span></div>
            <?php endif; ?>
            <?php if ($entry['date_of_birth']): ?>
            <div class="detail-row"><span class="detail-label">Tanggal Lahir</span><span class="detail-value"><?= date('d M Y', strtotime($entry['date_of_birth'])) ?></span></div>
            <?php endif; ?>
            <?php if ($entry['place_of_birth']): ?>
            <div class="detail-row"><span class="detail-label">Tempat Lahir</span><span class="detail-value"><?= htmlspecialchars($entry['place_of_birth']) ?></span></div>
            <?php endif; ?>
            <?php if ($entry['nationality']): ?>
            <div class="detail-row"><span class="detail-label">Kewarganegaraan</span><span class="detail-value"><?= htmlspecialchars($entry['nationality']) ?></span></div>
            <?php endif; ?>
            <?php if ($entry['blood_type']): ?>
            <div class="detail-row"><span class="detail-label">Golongan Darah</span><span class="detail-value"><?= $entry['blood_type'] ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Address -->
    <div class="detail-card">
        <div class="detail-card-header"><i class="fas fa-map-marker-alt"></i> Alamat</div>
        <div class="detail-card-body">
            <div class="detail-row"><span class="detail-label">Alamat</span><span class="detail-value"><?= htmlspecialchars($entry['address'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Kota</span><span class="detail-value"><?= htmlspecialchars($entry['city'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Negara</span><span class="detail-value"><?= htmlspecialchars($entry['country'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Kode Pos</span><span class="detail-value"><?= htmlspecialchars($entry['postal_code'] ?: '-') ?></span></div>
        </div>
    </div>

    <!-- Physical -->
    <div class="detail-card">
        <div class="detail-card-header"><i class="fas fa-ruler-vertical"></i> Fisik</div>
        <div class="detail-card-body">
            <div class="detail-row"><span class="detail-label">Tinggi</span><span class="detail-value"><?= $entry['height_cm'] ? $entry['height_cm'] . ' cm' : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">Berat</span><span class="detail-value"><?= $entry['weight_kg'] ? $entry['weight_kg'] . ' kg' : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">Ukuran Sepatu</span><span class="detail-value"><?= htmlspecialchars($entry['shoe_size'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Ukuran Overall</span><span class="detail-value"><?= htmlspecialchars($entry['overall_size'] ?: '-') ?></span></div>
        </div>
    </div>

    <!-- Emergency -->
    <div class="detail-card">
        <div class="detail-card-header"><i class="fas fa-phone-alt"></i> Kontak Darurat</div>
        <div class="detail-card-body">
            <div class="detail-row"><span class="detail-label">Nama</span><span class="detail-value"><?= htmlspecialchars($entry['emergency_name'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Telepon</span><span class="detail-value"><?= htmlspecialchars($entry['emergency_phone'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Hubungan</span><span class="detail-value"><?= htmlspecialchars($entry['emergency_relation'] ?: '-') ?></span></div>
        </div>
    </div>

    <!-- Maritime / Sea Experience -->
    <div class="detail-card">
        <div class="detail-card-header"><i class="fas fa-anchor"></i> Pengalaman Laut</div>
        <div class="detail-card-body">
            <div class="detail-row"><span class="detail-label">No. Buku Pelaut</span><span class="detail-value"><?= htmlspecialchars($entry['seaman_book_no'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Exp. Buku Pelaut</span><span class="detail-value"><?= $entry['seaman_book_expiry'] ? date('d M Y', strtotime($entry['seaman_book_expiry'])) : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">No. Passport</span><span class="detail-value"><?= htmlspecialchars($entry['passport_no'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Exp. Passport</span><span class="detail-value"><?= $entry['passport_expiry'] ? date('d M Y', strtotime($entry['passport_expiry'])) : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">Total Pengalaman</span><span class="detail-value"><?= $entry['total_sea_service_months'] ? $entry['total_sea_service_months'] . ' bulan' : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">Pangkat Terakhir</span><span class="detail-value"><?= htmlspecialchars($entry['last_rank'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Kapal Terakhir</span><span class="detail-value"><?= htmlspecialchars($entry['last_vessel_name'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Jenis Kapal</span><span class="detail-value"><?= htmlspecialchars($entry['last_vessel_type'] ?: '-') ?></span></div>
            <div class="detail-row"><span class="detail-label">Sign Off Terakhir</span><span class="detail-value"><?= $entry['last_sign_off'] ? date('d M Y', strtotime($entry['last_sign_off'])) : '-' ?></span></div>
        </div>
    </div>

    <!-- Application Info -->
    <div class="detail-card">
        <div class="detail-card-header"><i class="fas fa-file-alt"></i> Info Lamaran</div>
        <div class="detail-card-body">
            <div class="detail-row"><span class="detail-label">Posisi</span><span class="detail-value"><?= htmlspecialchars($entry['vacancy_title']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Gaji Diharapkan</span><span class="detail-value"><?= $entry['expected_salary'] ? 'Rp ' . number_format($entry['expected_salary'], 0, ',', '.') : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">Tanggal Tersedia</span><span class="detail-value"><?= $entry['available_date'] ? date('d M Y', strtotime($entry['available_date'])) : '-' ?></span></div>
            <div class="detail-row"><span class="detail-label">Tanggal Daftar</span><span class="detail-value"><?= date('d M Y H:i', strtotime($entry['created_at'])) ?></span></div>
            <?php if ($entry['cover_letter']): ?>
            <div class="detail-row full"><span class="detail-label">Cover Letter</span><span class="detail-value"><?= nl2br(htmlspecialchars($entry['cover_letter'])) ?></span></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Documents Section -->
<div class="detail-card full-width">
    <div class="detail-card-header"><i class="fas fa-folder-open"></i> Dokumen (<?= count($documents) ?>)</div>
    <div class="detail-card-body">
        <?php if (empty($documents)): ?>
        <div class="no-docs"><i class="fas fa-file-excel"></i> Belum ada dokumen yang diupload</div>
        <?php else: ?>
        <div class="doc-grid">
            <?php foreach ($documents as $doc): ?>
            <?php
            $ext = strtolower(pathinfo($doc['file_path'], PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $icon = $isImage ? 'fa-image' : 'fa-file-pdf';
            $docType = $isImage ? 'image' : 'pdf';
            $docUrl = url('/' . $doc['file_path']);
            $docTitle = htmlspecialchars($doc['type_name_id'] ?? $doc['type_name']);
            ?>
            <div class="doc-item clickable-doc" onclick="openDocViewer('<?= $docUrl ?>', '<?= $docTitle ?>', '<?= $docType ?>')">
                <div class="doc-icon <?= $isImage ? 'img-icon' : 'pdf-icon' ?>">
                    <i class="fas <?= $icon ?>"></i>
                </div>
                <div class="doc-info">
                    <strong><?= $docTitle ?></strong>
                    <span class="doc-meta"><?= htmlspecialchars($doc['original_name']) ?></span>
                    <?php if ($doc['document_number']): ?>
                    <span class="doc-meta">No: <?= htmlspecialchars($doc['document_number']) ?></span>
                    <?php endif; ?>
                    <?php if ($doc['expiry_date']): ?>
                    <span class="doc-meta">Exp: <?= date('d M Y', strtotime($doc['expiry_date'])) ?></span>
                    <?php endif; ?>
                </div>
                <div class="doc-actions">
                    <button class="doc-view-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                    <a href="<?= $docUrl ?>" download onclick="event.stopPropagation()" class="doc-download" title="Download"><i class="fas fa-download"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <div class="modal-header"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</div>
        <p>Apakah Anda yakin ingin menghapus data pelamar <strong id="deleteTargetName"></strong>?</p>
        <p style="color:#ef4444;font-size:0.85rem">⚠️ Semua data (profil, dokumen, lamaran) akan dihapus permanen.</p>
        <form id="deleteForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-actions">
                <button type="button" onclick="closeDeleteModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-confirm-delete"><i class="fas fa-trash"></i> Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>

<!-- Document/Photo Viewer Modal -->
<div id="docViewerModal" class="doc-viewer-overlay">
    <div class="doc-viewer-backdrop" onclick="closeDocViewer()"></div>
    <div class="doc-viewer-container">
        <div class="doc-viewer-header">
            <div class="doc-viewer-title">
                <i class="fas fa-file" id="docViewerIcon"></i>
                <span id="docViewerTitle">Document</span>
            </div>
            <div class="doc-viewer-controls">
                <a href="#" id="docViewerDownload" download class="doc-viewer-btn" title="Download">
                    <i class="fas fa-download"></i>
                </a>
                <a href="#" id="docViewerNewTab" target="_blank" class="doc-viewer-btn" title="Buka di tab baru">
                    <i class="fas fa-external-link-alt"></i>
                </a>
                <button onclick="closeDocViewer()" class="doc-viewer-btn close-btn" title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="doc-viewer-body" id="docViewerBody">
            <!-- Content injected by JS -->
        </div>
    </div>
</div>

<style>
/* Profile Photo */
.profile-photo-large { width: 120px; height: 120px; min-width: 120px; border-radius: 16px; overflow: hidden; border: 4px solid rgba(255,255,255,0.4); box-shadow: 0 8px 25px rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05)); position: relative; }
.profile-photo-large.clickable { cursor: pointer; }
.profile-photo-large.clickable:hover { border-color: rgba(255,255,255,0.8); }
.profile-photo-large.clickable:hover .photo-zoom-hint { opacity: 1; }
.profile-photo-large img { width: 100%; height: 100%; object-fit: cover; object-position: center top; }
.profile-photo-large span { color: white; font-size: 2.5rem; font-weight: 700; }
.photo-zoom-hint { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); color: white; text-align: center; padding: 0.5rem 0 0.3rem; font-size: 0.8rem; opacity: 0; transition: opacity 0.3s; }

/* Header */
.header-actions { display: flex; gap: 0.5rem; }
.btn-header-action { background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; border: 1px solid rgba(255,255,255,0.3); }
.btn-header-action:hover { background: rgba(255,255,255,0.3); color: white; transform: translateY(-2px); }

/* Status bar */
.detail-status-bar { display: flex; justify-content: space-between; align-items: center; background: white; padding: 1rem 1.5rem; border-radius: 14px; margin-bottom: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); flex-wrap: wrap; gap: 0.75rem; }
.status-info { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
.status-badge-lg { padding: 0.4rem 1rem; border-radius: 20px; color: white; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; }
.source-badge-lg { padding: 0.35rem 0.9rem; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
.source-badge-lg.manual { background: #dcfce7; color: #16a34a; }
.source-badge-lg.online { background: #dbeafe; color: #2563eb; }
.erp-badge { padding: 0.35rem 0.9rem; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
.erp-badge.synced { background: #fef3c7; color: #92400e; }

/* Actions */
.status-actions { display: flex; gap: 0.5rem; }
.btn-push-erp { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; padding: 0.5rem 1.15rem; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; font-family: inherit; }
.btn-push-erp:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(245,158,11,0.4); }
.btn-delete-entry { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; padding: 0.5rem 1.15rem; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; font-family: inherit; }
.btn-delete-entry:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(239,68,68,0.4); }

/* Detail grid */
.detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem; }
.detail-card { background: white; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden; }
.detail-card.full-width { grid-column: 1 / -1; }
.detail-card-header { padding: 0.85rem 1.25rem; background: linear-gradient(135deg, #f8fafc, #eef2f7); font-weight: 700; font-size: 0.9rem; color: #1e3a5f; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem; }
.detail-card-header i { color: #3b82f6; }
.detail-card-body { padding: 1rem 1.25rem; }

/* Detail rows */
.detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; }
.detail-row:last-child { border-bottom: none; }
.detail-row.full { flex-direction: column; gap: 0.3rem; }
.detail-label { font-size: 0.82rem; color: #6b7280; font-weight: 500; min-width: 140px; }
.detail-value { font-size: 0.88rem; color: #1f2937; font-weight: 600; text-align: right; }
.detail-row.full .detail-value { text-align: left; font-weight: 400; line-height: 1.5; }
.detail-value a { color: #3b82f6; text-decoration: none; }
.detail-value a:hover { text-decoration: underline; }

/* Empty docs */
.no-docs { text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.9rem; }
.no-docs i { display: block; font-size: 2rem; margin-bottom: 0.5rem; }

/* Document grid */
.doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 0.75rem; }
.doc-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e5e7eb; transition: all 0.3s; }
.doc-item.clickable-doc { cursor: pointer; }
.doc-item.clickable-doc:hover { border-color: #3b82f6; background: #eff6ff; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(59,130,246,0.12); }
.doc-icon { width: 40px; height: 40px; border-radius: 10px; color: white; display: flex; align-items: center; justify-content: center; font-size: 1rem; min-width: 40px; }
.doc-icon.img-icon { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.doc-icon.pdf-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
.doc-info { flex: 1; min-width: 0; }
.doc-info strong { display: block; font-size: 0.82rem; color: #1f2937; }
.doc-meta { display: block; font-size: 0.72rem; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.doc-actions { display: flex; gap: 0.35rem; }
.doc-view-btn { width: 34px; height: 34px; border-radius: 8px; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.8rem; }
.doc-view-btn:hover { background: #3b82f6; color: white; transform: scale(1.1); }
.doc-download { width: 34px; height: 34px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; font-size: 0.8rem; }
.doc-download:hover { background: #16a34a; color: white; transform: scale(1.1); }

/* ===== Document Viewer Modal ===== */
.doc-viewer-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999; display: none; align-items: center; justify-content: center; }
.doc-viewer-overlay.active { display: flex; }
.doc-viewer-backdrop { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0); transition: background 0.4s ease; }
.doc-viewer-overlay.active .doc-viewer-backdrop { background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

.doc-viewer-container { position: relative; z-index: 2; width: 90%; max-width: 900px; max-height: 90vh; background: white; border-radius: 20px; box-shadow: 0 25px 80px rgba(0,0,0,0.4); overflow: hidden; display: flex; flex-direction: column;
    opacity: 0; transform: scale(0.85) translateY(30px); transition: opacity 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.doc-viewer-overlay.active .doc-viewer-container { opacity: 1; transform: scale(1) translateY(0); }
.doc-viewer-overlay.closing .doc-viewer-container { opacity: 0; transform: scale(0.9) translateY(20px); transition: opacity 0.25s ease, transform 0.25s ease; }
.doc-viewer-overlay.closing .doc-viewer-backdrop { background: rgba(0,0,0,0); transition: background 0.3s ease; }

.doc-viewer-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background: linear-gradient(135deg, #1e3a5f, #2d5a87); color: white; }
.doc-viewer-title { display: flex; align-items: center; gap: 0.6rem; font-weight: 700; font-size: 1rem; }
.doc-viewer-title i { font-size: 1.1rem; opacity: 0.8; }
.doc-viewer-controls { display: flex; gap: 0.4rem; }
.doc-viewer-btn { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); cursor: pointer; transition: all 0.3s; text-decoration: none; font-size: 0.85rem; }
.doc-viewer-btn:hover { background: rgba(255,255,255,0.3); color: white; transform: scale(1.05); }
.doc-viewer-btn.close-btn:hover { background: rgba(239,68,68,0.8); }

.doc-viewer-body { flex: 1; overflow: auto; display: flex; align-items: center; justify-content: center; background: #f1f5f9; min-height: 400px; max-height: calc(90vh - 70px); padding: 1.5rem; }
.doc-viewer-body img { max-width: 100%; max-height: 100%; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); animation: imgFadeIn 0.5s ease 0.2s both; }
.doc-viewer-body iframe { width: 100%; height: 100%; min-height: 500px; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); animation: imgFadeIn 0.5s ease 0.2s both; }
.doc-viewer-body .viewer-loading { display: flex; flex-direction: column; align-items: center; gap: 1rem; color: #6b7280; }
.doc-viewer-body .viewer-loading i { font-size: 2.5rem; color: #3b82f6; animation: pulse 1.5s ease infinite; }

@keyframes imgFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
@keyframes pulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.1); } }

/* Delete Modal */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; }
.modal-box { background: white; border-radius: 16px; padding: 2rem; max-width: 440px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { font-size: 1.1rem; font-weight: 700; color: #ef4444; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.modal-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; justify-content: flex-end; }
.btn-cancel { padding: 0.5rem 1.25rem; border: 2px solid #d1d5db; background: white; color: #374151; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-confirm-delete { padding: 0.5rem 1.25rem; background: #ef4444; color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-confirm-delete:hover { background: #dc2626; }

@media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) {
    .detail-status-bar { flex-direction: column; align-items: flex-start; }
    .doc-viewer-container { width: 95%; max-width: none; border-radius: 16px; }
    .doc-viewer-body { min-height: 300px; padding: 1rem; }
}
</style>

<script>
// Delete modal
function confirmDelete(id, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = '<?= url('/crewing/manual-entries/delete/') ?>' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// Document/Photo Viewer
function openDocViewer(fileUrl, title, type) {
    const modal = document.getElementById('docViewerModal');
    const body = document.getElementById('docViewerBody');
    const titleEl = document.getElementById('docViewerTitle');
    const iconEl = document.getElementById('docViewerIcon');
    const downloadLink = document.getElementById('docViewerDownload');
    const newTabLink = document.getElementById('docViewerNewTab');

    titleEl.textContent = title;
    downloadLink.href = fileUrl;
    newTabLink.href = fileUrl;

    // Show loading
    body.innerHTML = '<div class="viewer-loading"><i class="fas fa-spinner fa-spin"></i><span>Memuat dokumen...</span></div>';

    if (type === 'image') {
        iconEl.className = 'fas fa-image';
        const img = new Image();
        img.onload = function() {
            body.innerHTML = '';
            body.appendChild(img);
        };
        img.onerror = function() {
            body.innerHTML = '<div class="viewer-loading"><i class="fas fa-exclamation-triangle" style="color:#ef4444"></i><span>Gagal memuat gambar</span></div>';
        };
        img.src = fileUrl;
        img.alt = title;
        img.style.cssText = 'max-width:100%;max-height:100%;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.15);cursor:zoom-in;';
        img.onclick = function() { window.open(fileUrl, '_blank'); };
    } else {
        iconEl.className = 'fas fa-file-pdf';
        body.innerHTML = '<iframe src="' + fileUrl + '" style="width:100%;height:100%;min-height:500px;border:none;border-radius:12px;"></iframe>';
    }

    // Animate in
    modal.classList.remove('closing');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDocViewer() {
    const modal = document.getElementById('docViewerModal');
    modal.classList.add('closing');
    setTimeout(() => {
        modal.classList.remove('active', 'closing');
        document.getElementById('docViewerBody').innerHTML = '';
        document.body.style.overflow = '';
    }, 300);
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('docViewerModal');
        if (modal.classList.contains('active')) closeDocViewer();
        if (document.getElementById('deleteModal').style.display === 'flex') closeDeleteModal();
    }
});
</script>
