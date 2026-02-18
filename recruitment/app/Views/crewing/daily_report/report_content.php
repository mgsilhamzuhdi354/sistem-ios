<!-- Crewing Daily Report Content -->
<style>
    /* ===== Report Header ===== */
    .report-header {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: white;
        padding: 25px 30px;
        border-radius: 16px;
        margin-bottom: 25px;
    }
    .report-header h2 { margin: 0 0 8px; font-weight: 600; }
    .report-header p { margin: 0; opacity: 0.9; font-size: 0.9rem; }

    /* ===== Date Picker & Filter Bar ===== */
    .date-picker-bar {
        background: white;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .date-presets {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .date-preset-btn {
        padding: 8px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        color: #374151;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .date-preset-btn:hover { border-color: #14b8a6; color: #14b8a6; }
    .date-preset-btn.active { 
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: white;
        border-color: transparent;
    }

    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr auto auto;
        gap: 12px;
        align-items: end;
    }
    .filter-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .filter-input, .filter-select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-family: inherit;
        font-size: 0.9rem;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #14b8a6;
    }

    .btn-filter, .btn-export {
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-filter {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(59,130,246,0.4); }
    .btn-export {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }
    .btn-export:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(5,150,105,0.4); }

    /* ===== Statistics Cards ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border-left: 4px solid #14b8a6;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 12px;
    }
    .stat-label {
        font-size: 0.82rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e3a5f;
    }

    /* ===== Report Table ===== */
    .report-table-container {
        background: white;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow-x: auto;
    }
    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .report-table thead tr {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    }
    .report-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 2px solid #e2e8f0;
    }
    .report-table td {
        padding: 0;
        vertical-align: top;
    }
    .report-table td:first-child {
        padding: 18px 16px;
        vertical-align: middle;
        font-weight: 600;
        color: #94a3b8;
        font-size: 0.85rem;
        width: 50px;
    }
    .report-table tbody tr {
        background: #ffffff;
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    .report-table tbody tr:hover {
        background: #f8fffe;
        box-shadow: 0 2px 12px rgba(13,148,136,0.08);
    }

    /* ===== Applicant Card ===== */
    .applicant-card {
        padding: 16px 16px 14px;
    }

    /* --- Name Header --- */
    .applicant-card__header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }
    .applicant-card__avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: #ffffff;
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .applicant-card__name {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }
    .applicant-card__name small {
        display: block;
        font-size: 0.75rem;
        font-weight: 400;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* --- Badge Row --- */
    .applicant-card__badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }

    .dr-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        border: 1px solid;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        white-space: nowrap;
    }
    .dr-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    }
    .dr-badge i {
        font-size: 0.72rem;
        opacity: 0.85;
    }
    .dr-badge__label {
        font-weight: 500;
        opacity: 0.7;
        margin-right: 2px;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Badge Variants */
    .dr-badge--rank {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-color: #93c5fd;
        color: #1d4ed8;
    }
    .dr-badge--vessel {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-color: #fcd34d;
        color: #b45309;
    }
    .dr-badge--department {
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
        border-color: #c4b5fd;
        color: #7c3aed;
    }
    .dr-badge--status {
        color: #ffffff;
        border-color: transparent;
        padding: 5px 14px;
    }

    /* --- Vacancy Info --- */
    .applicant-card__vacancy {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 0.82rem;
        color: #115e59;
        font-weight: 500;
        border: 1px solid #99f6e4;
    }
    .applicant-card__vacancy i {
        color: #0d9488;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    /* --- Meta Row (Handler + Contact + Date) --- */
    .applicant-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }
    .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        color: #64748b;
    }
    .meta-item i {
        width: 14px;
        text-align: center;
        font-size: 0.75rem;
    }
    .meta-item strong {
        color: #334155;
        font-weight: 600;
    }
    .meta-item a {
        color: #0d9488;
        text-decoration: none;
    }
    .meta-item a:hover {
        text-decoration: underline;
    }
    .meta-separator {
        width: 1px;
        height: 14px;
        background: #e2e8f0;
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    .empty-state i {
        font-size: 3.5rem;
        margin-bottom: 15px;
        display: block;
        opacity: 0.3;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        justify-content: flex-end;
    }

    /* ===== Pagination ===== */
    .pagination-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }
    .pagination-bar a {
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .pagination-bar a.active {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: white;
        box-shadow: 0 4px 12px rgba(13,148,136,0.3);
    }
    .pagination-bar a:not(.active) {
        background: #f3f4f6;
        color: #374151;
    }
    .pagination-bar a:not(.active):hover {
        background: #e5e7eb;
    }

    /* ===== Responsive ===== */
    @media (max-width: 992px) {
        .filter-row {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .applicant-card__meta {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
        .meta-separator { display: none; }
    }
</style>

<div class="report-header">
    <h2><i class="fas fa-chart-line me-2"></i><?= t('report.title') ?></h2>
    <p><?= getCurrentLanguage() === 'en' ? 'Daily progress overview of applicants you are handling' : 'Ringkasan progres harian pelamar yang Anda tangani' ?></p>
</div>

<!-- Date Picker & Filters -->
<div class="date-picker-bar">
    <div class="date-presets">
        <button class="date-preset-btn" onclick="setDateRange('today')">
            <i class="fas fa-calendar-day"></i> <?= getCurrentLanguage() === 'en' ? 'Today' : 'Hari Ini' ?>
        </button>
        <button class="date-preset-btn" onclick="setDateRange('yesterday')">
            <i class="fas fa-calendar-minus"></i> <?= getCurrentLanguage() === 'en' ? 'Yesterday' : 'Kemarin' ?>
        </button>
        <button class="date-preset-btn" onclick="setDateRange('week')">
            <i class="fas fa-calendar-week"></i> <?= getCurrentLanguage() === 'en' ? 'This Week' : 'Minggu Ini' ?>
        </button>
        <button class="date-preset-btn" onclick="setDateRange('month')">
            <i class="fas fa-calendar-alt"></i> <?= getCurrentLanguage() === 'en' ? 'This Month' : 'Bulan Ini' ?>
        </button>
    </div>
    
    <form method="GET" action="<?= url('/crewing/daily-report') ?>" id="filterForm">
        <div class="filter-row">
            <div class="filter-group">
                <label><i class="fas fa-calendar me-1"></i><?= t('report.from_date') ?></label>
                <input type="date" name="date_from" class="filter-input" value="<?= $dateFrom ?>" required>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar me-1"></i><?= t('report.to_date') ?></label>
                <input type="date" name="date_to" class="filter-input" value="<?= $dateTo ?>" required>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-filter me-1"></i><?= t('vacancy.status', 'Status') ?></label>
                <select name="status" class="filter-select">
                    <option value="all" <?= $currentStatus == 'all' ? 'selected' : '' ?>><?= getCurrentLanguage() === 'en' ? 'All Status' : 'Semua Status' ?></option>
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $currentStatus == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-search me-1"></i><?= t('btn.search') ?></label>
                <input type="text" name="search" class="filter-input" placeholder="<?= getCurrentLanguage() === 'en' ? 'Name, email, phone...' : 'Nama, email, telepon...' ?>" value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> <?= t('btn.filter') ?>
            </button>
            <button type="button" class="btn-filter" onclick="resetFilters()" style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                <i class="fas fa-redo"></i> <?= t('btn.reset') ?>
            </button>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #3b82f6;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-label"><?= t('dashboard.total_applicants') ?></div>
        <div class="stat-value"><?= $stats['total'] ?></div>
    </div>

    <div class="stat-card" style="border-left-color: #f59e0b;">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #f59e0b;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-label"><?= t('status.pending', 'Pending') ?></div>
        <div class="stat-value"><?= $stats['pending'] ?></div>
    </div>

    <div class="stat-card" style="border-left-color: #10b981;">
        <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #10b981;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-label"><?= t('status.approved', 'Approved') ?></div>
        <div class="stat-value"><?= $stats['approved'] ?></div>
    </div>

    <div class="stat-card" style="border-left-color: #8b5cf6;">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #8b5cf6;">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="stat-label"><?= getCurrentLanguage() === 'en' ? 'Sent to ERP' : 'Terkirim ke ERP' ?></div>
        <div class="stat-value"><?= $stats['sent_to_erp'] ?></div>
    </div>
</div>

<!-- Export Buttons -->
<div class="export-buttons">
    <!-- PDF Export Dropdown -->
    <div style="position: relative; display: inline-block;">
        <button class="btn-export" id="pdfExportBtn" onclick="togglePdfDropdown()" 
                style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <i class="fas fa-file-pdf"></i> <?= t('btn.export_pdf') ?> <i class="fas fa-caret-down ms-1"></i>
        </button>
        <div id="pdfDropdown" style="display:none; position:absolute; top:100%; right:0; margin-top:8px; background:white; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.15); min-width:240px; z-index:1000; overflow:hidden;">
            <a href="<?= url('/crewing/daily-report/export-pdf-combined') ?>?date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $currentStatus ?>" 
               target="_blank"
               style="display:flex; align-items:center; gap:12px; padding:12px 16px; text-decoration:none; color:#1f2937; transition:background 0.2s; border-bottom:1px solid #f3f4f6;"
               onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
                <i class="fas fa-file-pdf" style="color:#ef4444; font-size:1.1rem; width:20px; text-align:center;"></i>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:0.9rem; color:#0f172a;">Combined PDF</div>
                    <div style="font-size:0.75rem; color:#9ca3af;">All dates in one file</div>
                </div>
            </a>
            <a href="<?= url('/crewing/daily-report/export-pdf-daily') ?>?date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $currentStatus ?>" 
               style="display:flex; align-items:center; gap:12px; padding:12px 16px; text-decoration:none; color:#1f2937; transition:background 0.2s;"
               onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
                <i class="fas fa-layer-group" style="color:#f97316; font-size:1.1rem; width:20px; text-align:center;"></i>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:0.9rem; color:#0f172a;">Daily PDFs (ZIP)</div>
                    <div style="font-size:0.75rem; color:#9ca3af;">Separate file per day</div>
                </div>
            </a>
        </div>
    </div>
    
    <a href="<?= url('/crewing/daily-report/export-excel') ?>?date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $currentStatus ?>" 
       class="btn-export">
        <i class="fas fa-file-excel"></i> <?= t('btn.export_excel') ?>
    </a>
</div>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('pdfDropdown');
    const btn = document.getElementById('pdfExportBtn');
    if (dropdown && !dropdown.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

function togglePdfDropdown() {
    const dropdown = document.getElementById('pdfDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}
</script>


<!-- Report Table -->
<div class="report-table-container">
    <?php if (empty($applications)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4 style="color: #64748b; margin-bottom: 8px;"><?= getCurrentLanguage() === 'en' ? 'No Data Found' : 'Data Tidak Ditemukan' ?></h4>
            <p style="color: #94a3b8;"><?= getCurrentLanguage() === 'en' ? 'No applicants handled in the selected date range' : 'Tidak ada pelamar yang ditangani dalam rentang tanggal yang dipilih' ?></p>
        </div>
    <?php else: ?>
        <!-- Summary -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
            <span style="font-size: 0.85rem; color: #64748b;">
                <?= getCurrentLanguage() === 'en' ? 'Showing' : 'Menampilkan' ?> <strong style="color: #0f172a;"><?= count($applications) ?></strong> <?= getCurrentLanguage() === 'en' ? 'of' : 'dari' ?> 
                <strong style="color: #0f172a;"><?= $totalCount ?></strong> <?= getCurrentLanguage() === 'en' ? 'applicants' : 'pelamar' ?>
                <?php if ($dateFrom === $dateTo): ?>
                    on <strong style="color: #0d9488;"><?= date('d M Y', strtotime($dateFrom)) ?></strong>
                <?php else: ?>
                    from <strong style="color: #0d9488;"><?= date('d M Y', strtotime($dateFrom)) ?></strong>
                    to <strong style="color: #0d9488;"><?= date('d M Y', strtotime($dateTo)) ?></strong>
                <?php endif; ?>
            </span>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th><?= getCurrentLanguage() === 'en' ? 'Applicant Details' : 'Detail Pelamar' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = ($currentPage - 1) * $perPage + 1;
                foreach ($applications as $app): 
                    // Get initials for avatar
                    $nameParts = explode(' ', $app['applicant_name'] ?? '?');
                    $initials = strtoupper(substr($nameParts[0], 0, 1));
                    if (count($nameParts) > 1) {
                        $initials .= strtoupper(substr(end($nameParts), 0, 1));
                    }
                    
                    // Format date in international format
                    $createdDate = date('d M Y', strtotime($app['created_at']));
                    $createdTime = date('H:i', strtotime($app['created_at']));
                    
                    // Status color
                    $statusColor = $app['status_color'] ?? '#6c757d';
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="applicant-card">
                                <!-- Header: Avatar + Name -->
                                <div class="applicant-card__header">
                                    <div class="applicant-card__avatar">
                                        <?php if (!empty($app['avatar'])): ?>
                                            <img src="<?= url('/' . $app['avatar']) ?>" alt="<?= htmlspecialchars($app['applicant_name']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?= $initials ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="applicant-card__name">
                                        <?= htmlspecialchars($app['applicant_name'] ?? '-') ?>
                                        <small>ID: #<?= $app['id'] ?> &middot; <?= getCurrentLanguage() === 'en' ? 'Applied' : 'Melamar' ?> <?= $createdDate ?></small>
                                    </div>
                                </div>

                                <!-- Badges Row -->
                                <div class="applicant-card__badges">
                                    <!-- Rank Badge -->
                                    <span class="dr-badge dr-badge--rank">
                                        <i class="fas fa-anchor"></i>
                                        <span class="dr-badge__label">Rank</span>
                                        <?= htmlspecialchars($app['last_rank'] ?? '-') ?>
                                    </span>

                                    <!-- Vessel Type Badge -->
                                    <span class="dr-badge dr-badge--vessel">
                                        <i class="fas fa-ship"></i>
                                        <span class="dr-badge__label">Vessel</span>
                                        <?= htmlspecialchars($app['vessel_type'] ?? '-') ?>
                                    </span>

                                    <!-- Department Badge -->
                                    <span class="dr-badge dr-badge--department">
                                        <i class="fas fa-sitemap"></i>
                                        <span class="dr-badge__label">Dept</span>
                                        <?= htmlspecialchars($app['department_name'] ?? '-') ?>
                                    </span>

                                    <!-- Status Badge -->
                                    <span class="dr-badge dr-badge--status" style="background: <?= $statusColor ?>;">
                                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                        <?= htmlspecialchars($app['status_name'] ?? '-') ?>
                                    </span>
                                </div>

                                <!-- Vacancy Info -->
                                <?php if (!empty($app['vacancy_title'])): ?>
                                    <div class="applicant-card__vacancy">
                                        <i class="fas fa-briefcase"></i>
                                        <span>Vacancy: <strong><?= htmlspecialchars($app['vacancy_title']) ?></strong></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Meta Row: Handler, Contact, Date -->
                                <div class="applicant-card__meta">
                                    <!-- Handler -->
                                    <div class="meta-item">
                                        <i class="fas fa-user-tie" style="color: #0d9488;"></i>
                                        <span>Handler: <strong><?= htmlspecialchars($app['handler_name'] ?? '-') ?></strong></span>
                                    </div>

                                    <div class="meta-separator"></div>

                                    <!-- Email -->
                                    <?php if (!empty($app['email'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-envelope" style="color: #6366f1;"></i>
                                            <a href="mailto:<?= htmlspecialchars($app['email']) ?>"><?= htmlspecialchars($app['email']) ?></a>
                                        </div>
                                        <div class="meta-separator"></div>
                                    <?php endif; ?>

                                    <!-- Phone -->
                                    <?php if (!empty($app['phone'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-phone" style="color: #10b981;"></i>
                                            <a href="tel:<?= htmlspecialchars($app['phone']) ?>"><?= htmlspecialchars($app['phone']) ?></a>
                                        </div>
                                        <div class="meta-separator"></div>
                                    <?php endif; ?>

                                    <!-- ERP Status -->
                                    <?php if (!empty($app['sent_to_erp_at'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-cloud-upload-alt" style="color: #8b5cf6;"></i>
                                            <span>Sent to ERP: <strong><?= date('d M Y, H:i', strtotime($app['sent_to_erp_at'])) ?></strong></span>
                                        </div>
                                        <div class="meta-separator"></div>
                                    <?php endif; ?>

                                    <!-- Created Date -->
                                    <div class="meta-item">
                                        <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                        <span><?= $createdDate ?> at <?= $createdTime ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-bar">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $currentStatus ?>&search=<?= urlencode($search ?? '') ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $currentStatus ?>&search=<?= urlencode($search ?? '') ?>" 
                       class="<?= $i == $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $currentStatus ?>&search=<?= urlencode($search ?? '') ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function setDateRange(preset) {
    const today = new Date();
    let dateFrom, dateTo;
    
    // Highlight active preset
    document.querySelectorAll('.date-preset-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.date-preset-btn').classList.add('active');
    
    switch(preset) {
        case 'today':
            dateFrom = dateTo = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            dateFrom = dateTo = formatDate(yesterday);
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            dateFrom = formatDate(weekStart);
            dateTo = formatDate(today);
            break;
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            dateFrom = formatDate(monthStart);
            dateTo = formatDate(today);
            break;
    }
    
    document.querySelector('input[name="date_from"]').value = dateFrom;
    document.querySelector('input[name="date_to"]').value = dateTo;
    document.getElementById('filterForm').submit();
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function resetFilters() {
    window.location.href = '<?= url('/crewing/daily-report') ?>';
}
</script>
