<style>
/* ===== Report Page Styles ===== */
.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.report-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e3a5f;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.report-header h1 i {
    color: #dc2626;
    font-size: 1.5rem;
}
.report-header p {
    color: #64748b;
    margin: 0.25rem 0 0;
    font-size: 0.9rem;
}
.report-actions {
    display: flex;
    gap: 0.75rem;
}
.btn-print {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
}
.btn-print.primary {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    box-shadow: 0 4px 12px rgba(220,38,38,0.3);
}
.btn-print.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220,38,38,0.4);
}
.btn-print.secondary {
    background: white;
    color: #1e3a5f;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.btn-print.secondary:hover {
    background: #f8fafc;
    transform: translateY(-2px);
}

/* Stats Cards */
.report-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}
.report-stat-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s;
    border-left: 4px solid transparent;
}
.report-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
.report-stat-card.total { border-left-color: #3b82f6; }
.report-stat-card.sent { border-left-color: #10b981; }
.report-stat-card.not-sent { border-left-color: #f59e0b; }
.report-stat-card.rejected { border-left-color: #ef4444; }
.report-stat-card.approved { border-left-color: #8b5cf6; }
.report-stat-card.pending { border-left-color: #6366f1; }

.stat-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.report-stat-card.total .stat-icon-box { background: #eff6ff; color: #3b82f6; }
.report-stat-card.sent .stat-icon-box { background: #ecfdf5; color: #10b981; }
.report-stat-card.not-sent .stat-icon-box { background: #fffbeb; color: #f59e0b; }
.report-stat-card.rejected .stat-icon-box { background: #fef2f2; color: #ef4444; }
.report-stat-card.approved .stat-icon-box { background: #f5f3ff; color: #8b5cf6; }
.report-stat-card.pending .stat-icon-box { background: #eef2ff; color: #6366f1; }

.stat-info .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}
.stat-info .stat-label {
    font-size: 0.78rem;
    color: #94a3b8;
    margin-top: 4px;
    font-weight: 500;
}

/* Filter Bar */
.report-filters {
    background: white;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    margin-bottom: 1.5rem;
}
.filter-form {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    flex-wrap: wrap;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.filter-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.filter-group input,
.filter-group select {
    padding: 0.55rem 0.85rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #334155;
    transition: border-color 0.3s;
    background: white;
    min-width: 140px;
}
.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
}
.filter-btn {
    padding: 0.55rem 1.25rem;
    background: linear-gradient(135deg, #1e3a5f, #0d1f33);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.3s;
}
.filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(30,58,95,0.3);
}
.quick-filters {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
}
.quick-filter {
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    border: 1.5px solid #e2e8f0;
    background: white;
    color: #64748b;
    transition: all 0.2s;
    text-decoration: none;
}
.quick-filter:hover,
.quick-filter.active {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}

/* Data Table */
.report-table-wrapper {
    background: white;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    overflow: hidden;
}
.report-table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}
.report-table-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.report-table-header .result-count {
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 400;
}
.report-table {
    width: 100%;
    border-collapse: collapse;
}
.report-table thead th {
    background: #f8fafc;
    padding: 0.8rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.report-table tbody td {
    padding: 0.85rem 1rem;
    font-size: 0.85rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.report-table tbody tr:hover {
    background: #fafbfd;
}
.report-table tbody tr:last-child td {
    border-bottom: none;
}

/* Applicant Cell */
.applicant-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.applicant-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e3a5f, #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
    flex-shrink: 0;
}
.applicant-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.applicant-name {
    font-weight: 600;
    color: #1e293b;
}
.applicant-email {
    font-size: 0.75rem;
    color: #94a3b8;
}

/* ERP Status Badge */
.erp-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.7rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
}
.erp-badge.sent {
    background: #ecfdf5;
    color: #059669;
}
.erp-badge.not-sent {
    background: #fffbeb;
    color: #d97706;
}
.erp-badge.rejected {
    background: #fef2f2;
    color: #dc2626;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
}

/* Empty State */
.report-empty {
    text-align: center;
    padding: 3rem 2rem;
    color: #94a3b8;
}
.report-empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.4;
}
.report-empty h3 {
    font-size: 1.1rem;
    color: #64748b;
    margin-bottom: 0.5rem;
}
.report-empty p {
    font-size: 0.85rem;
}

/* Pagination */
.report-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.4rem;
    padding: 1.25rem;
    border-top: 1px solid #f1f5f9;
}
.report-pagination a,
.report-pagination span {
    padding: 0.45rem 0.85rem;
    border-radius: 8px;
    font-size: 0.82rem;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}
.report-pagination a {
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.report-pagination a:hover {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}
.report-pagination span.current {
    background: #dc2626;
    color: white;
    border: 1px solid #dc2626;
}
.report-pagination span.ellipsis {
    border: none;
    color: #94a3b8;
}

/* Print Styles */
@media print {
    body * { visibility: hidden; }
    .print-area, .print-area * { visibility: visible; }
    .print-area { 
        position: absolute; 
        left: 0; 
        top: 0; 
        width: 100%; 
        padding: 20px;
    }
    .admin-sidebar, .admin-header, .report-filters, .report-actions,
    .report-pagination, .no-print { display: none !important; }
    .admin-main { margin-left: 0 !important; }
    .report-table-wrapper { box-shadow: none; border: 1px solid #ddd; }
    .report-stat-card { box-shadow: none; border: 1px solid #ddd; }
}

@media (max-width: 768px) {
    .report-stats { grid-template-columns: repeat(2, 1fr); }
    .filter-form { flex-direction: column; }
    .report-header { flex-direction: column; align-items: flex-start; }
    .report-table { font-size: 0.78rem; }
}
</style>

<div class="print-area">

<!-- Page Header -->
<div class="report-header">
    <div>
        <h1><i class="fas fa-file-alt"></i> Laporan Harian Rekrutmen</h1>
        <p>Arsip data pelamar — status pengiriman ke ERP</p>
    </div>
    <div class="report-actions no-print">
        <a href="<?= url('/master-admin/recruitment-report/export-pdf?date_from=' . $dateFrom . '&date_to=' . $dateTo . '&status=' . $currentStatus) ?>" 
           target="_blank" class="btn-print primary">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <button onclick="window.print()" class="btn-print secondary">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="report-stats">
    <div class="report-stat-card total">
        <div class="stat-icon-box"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($stats['total']) ?></div>
            <div class="stat-label">Total Pelamar</div>
        </div>
    </div>
    <div class="report-stat-card sent">
        <div class="stat-icon-box"><i class="fas fa-paper-plane"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($stats['sent_to_erp']) ?></div>
            <div class="stat-label">Terkirim ke ERP</div>
        </div>
    </div>
    <div class="report-stat-card not-sent">
        <div class="stat-icon-box"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($stats['not_sent']) ?></div>
            <div class="stat-label">Belum Dikirim</div>
        </div>
    </div>
    <div class="report-stat-card rejected">
        <div class="stat-icon-box"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($stats['rejected']) ?></div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
    <div class="report-stat-card approved">
        <div class="stat-icon-box"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($stats['approved']) ?></div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>
    <div class="report-stat-card pending">
        <div class="stat-icon-box"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($stats['pending']) ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="report-filters no-print">
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <label>Dari Tanggal</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
        </div>
        <div class="filter-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
        </div>
        <div class="filter-group">
            <label>Status ERP</label>
            <select name="status">
                <option value="all" <?= $currentStatus === 'all' ? 'selected' : '' ?>>Semua Status</option>
                <option value="sent" <?= $currentStatus === 'sent' ? 'selected' : '' ?>>Terkirim ke ERP</option>
                <option value="not_sent" <?= $currentStatus === 'not_sent' ? 'selected' : '' ?>>Belum Dikirim</option>
                <option value="rejected" <?= $currentStatus === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Cari Pelamar</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nama, email, telepon...">
        </div>
        <button type="submit" class="filter-btn">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
    
    <!-- Quick Date Filters -->
    <div class="quick-filters">
        <?php
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');
        $yearStart = date('Y-01-01');
        
        $isToday = ($dateFrom === $today && $dateTo === $today);
        $isWeek = ($dateFrom === $weekStart && $dateTo === $today);
        $isMonth = ($dateFrom === $monthStart && $dateTo === $today);
        ?>
        <a href="<?= url('/master-admin/recruitment-report?date_from=' . $today . '&date_to=' . $today) ?>" 
           class="quick-filter <?= $isToday ? 'active' : '' ?>">
            <i class="fas fa-calendar-day"></i> Hari Ini
        </a>
        <a href="<?= url('/master-admin/recruitment-report?date_from=' . $weekStart . '&date_to=' . $today) ?>" 
           class="quick-filter <?= $isWeek ? 'active' : '' ?>">
            <i class="fas fa-calendar-week"></i> Minggu Ini
        </a>
        <a href="<?= url('/master-admin/recruitment-report?date_from=' . $monthStart . '&date_to=' . $today) ?>" 
           class="quick-filter <?= $isMonth ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Bulan Ini
        </a>
        <a href="<?= url('/master-admin/recruitment-report?date_from=' . $yearStart . '&date_to=' . $today) ?>" 
           class="quick-filter <?= ($dateFrom === $yearStart && $dateTo === $today) ? 'active' : '' ?>">
            <i class="fas fa-calendar"></i> Tahun Ini
        </a>
    </div>
</div>

<!-- Print Header (visible only in print) -->
<div style="display:none;" id="printHeader">
    <div style="text-align:center; margin-bottom:20px; border-bottom: 2px solid #1e3a5f; padding-bottom: 15px;">
        <h2 style="margin:0; color:#1e3a5f;">PT INDO OCEAN CREW SERVICES</h2>
        <p style="margin:5px 0 0; color:#666;">Laporan Harian Rekrutmen — <?= date('d F Y', strtotime($dateFrom)) ?><?= $dateFrom !== $dateTo ? ' s/d ' . date('d F Y', strtotime($dateTo)) : '' ?></p>
    </div>
</div>

<!-- Data Table -->
<div class="report-table-wrapper">
    <div class="report-table-header">
        <h3>
            <i class="fas fa-list"></i> Data Pelamar
            <span class="result-count">(<?= number_format($totalCount) ?> data)</span>
        </h3>
    </div>
    
    <?php if (empty($applications)): ?>
    <div class="report-empty">
        <i class="fas fa-inbox"></i>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ditemukan data pelamar untuk filter yang dipilih.</p>
    </div>
    <?php else: ?>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Pelamar</th>
                <th>Posisi</th>
                <th>Departemen</th>
                <th>Status Lamaran</th>
                <th>Status ERP</th>
                <th>Skor</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $startNum = ($currentPage - 1) * $perPage + 1;
            foreach ($applications as $i => $app): 
                $initials = strtoupper(substr($app['full_name'], 0, 2));
            ?>
            <tr>
                <td style="color:#94a3b8; font-weight:500;"><?= $startNum + $i ?></td>
                <td>
                    <div class="applicant-info">
                        <div class="applicant-avatar">
                            <?php if (!empty($app['avatar'])): ?>
                                <img src="<?= asset('uploads/avatars/' . $app['avatar']) ?>" alt="">
                            <?php else: ?>
                                <?= $initials ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="applicant-name"><?= htmlspecialchars($app['full_name']) ?></div>
                            <div class="applicant-email"><?= htmlspecialchars($app['email']) ?></div>
                            <?php if (!empty($app['phone'])): ?>
                                <div class="applicant-email"><i class="fas fa-phone" style="font-size:0.6rem;"></i> <?= htmlspecialchars($app['phone']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td><?= htmlspecialchars($app['vacancy_title'] ?? '-') ?></td>
                <td><?= htmlspecialchars($app['department_name'] ?? '-') ?></td>
                <td>
                    <span class="status-badge" style="background: <?= $app['status_color'] ?? '#e2e8f0' ?>20; color: <?= $app['status_color'] ?? '#64748b' ?>;">
                        <?= htmlspecialchars($app['status_name'] ?? 'Unknown') ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($app['sent_to_erp_at'])): ?>
                        <span class="erp-badge sent">
                            <i class="fas fa-check-circle"></i> Terkirim
                        </span>
                        <div style="font-size:0.68rem; color:#94a3b8; margin-top:3px;">
                            <?= date('d/m/Y H:i', strtotime($app['sent_to_erp_at'])) ?>
                            <?php if (!empty($app['erp_crew_id'])): ?>
                                <br>ERP ID: #<?= $app['erp_crew_id'] ?>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($app['status_id'] == 7): ?>
                        <span class="erp-badge rejected">
                            <i class="fas fa-times-circle"></i> Ditolak
                        </span>
                    <?php else: ?>
                        <span class="erp-badge not-sent">
                            <i class="fas fa-clock"></i> Belum Dikirim
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($app['overall_score'])): ?>
                        <span style="font-weight:600; color: <?= $app['overall_score'] >= 70 ? '#10b981' : ($app['overall_score'] >= 50 ? '#f59e0b' : '#ef4444') ?>;">
                            <?= $app['overall_score'] ?>
                        </span>
                    <?php else: ?>
                        <span style="color:#cbd5e1;">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-size:0.82rem;"><?= date('d M Y', strtotime($app['created_at'])) ?></div>
                    <div style="font-size:0.7rem; color:#94a3b8;"><?= date('H:i', strtotime($app['created_at'])) ?></div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="report-pagination no-print">
        <?php
        $queryParams = $_GET;
        
        if ($currentPage > 1): 
            $queryParams['page'] = $currentPage - 1;
        ?>
            <a href="?<?= http_build_query($queryParams) ?>"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        
        <?php
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);
        
        if ($start > 1): ?>
            <a href="?<?= http_build_query(array_merge($queryParams, ['page' => 1])) ?>">1</a>
            <?php if ($start > 2): ?><span class="ellipsis">...</span><?php endif; ?>
        <?php endif; ?>
        
        <?php for ($p = $start; $p <= $end; $p++): ?>
            <?php if ($p == $currentPage): ?>
                <span class="current"><?= $p ?></span>
            <?php else: ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $p])) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span class="ellipsis">...</span><?php endif; ?>
            <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $totalPages])) ?>"><?= $totalPages ?></a>
        <?php endif; ?>
        
        <?php if ($currentPage < $totalPages):
            $queryParams['page'] = $currentPage + 1;
        ?>
            <a href="?<?= http_build_query($queryParams) ?>"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>

</div><!-- /print-area -->

<script>
// Show print header when printing
window.addEventListener('beforeprint', function() {
    document.getElementById('printHeader').style.display = 'block';
});
window.addEventListener('afterprint', function() {
    document.getElementById('printHeader').style.display = 'none';
});
</script>
