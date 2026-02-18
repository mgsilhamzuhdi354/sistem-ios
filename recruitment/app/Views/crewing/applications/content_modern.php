<!-- Modern Applications View - Glassmorphic Design -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

<style>
    /* Inter Font Override */
    body, *:not(i):not(.fas):not(.far):not(.fab):not(.fa) { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important; }
    
    /* Modern Wrapper */
    .modern-apps-wrapper {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #0d9488 100%);
        min-height: calc(100vh - 120px);
        padding: 2rem;
        margin: -2rem;
    }
    
    /* Header Banner */
    .modern-header {
        background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.05) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 24px;
        padding: 2.5rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .modern-header::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='20' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.5;
    }
    .modern-header-content { position: relative; z-index: 2; }
    .modern-header h1 {
        font-size: 2.25rem;
        font-weight: 800;
        color: white;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .modern-header p {
        color: rgba(255,255,255,0.8);
        font-size: 1.05rem;
        margin: 0 0 1.5rem 0;
        font-weight: 400;
    }
    
    /* View Toggle */
    .view-toggle-container {
        display: inline-flex;
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: 6px;
        gap: 6px;
    }
    .view-toggle-tab {
        padding: 10px 24px;
        border-radius: 12px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .view-toggle-tab:hover { color: white; background: rgba(255,255,255,0.1); }
    .view-toggle-tab.active {
        background: white;
        color: #0d9488;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    /* Stats Summary Row */
    .stats-summary {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .stat-mini {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        min-width: 140px;
        transition: all 0.3s ease;
    }
    .stat-mini:hover {
        background: rgba(255,255,255,0.15);
        transform: translateY(-2px);
    }
    .stat-mini-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }
    .stat-mini-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-mini-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-mini-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .stat-mini-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .stat-mini h3 { margin: 0; font-size: 1.5rem; font-weight: 800; color: white; }
    .stat-mini span { font-size: 0.75rem; color: rgba(255,255,255,0.7); text-transform: uppercase; font-weight: 500; letter-spacing: 0.5px; }
    
    /* Status Filter Tabs */
    .status-tabs-modern {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        flex-wrap: wrap;
    }
    .status-tabs-modern::-webkit-scrollbar { height: 4px; }
    .status-tabs-modern::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 4px; }
    .status-tab-modern {
        padding: 8px 18px;
        border-radius: 12px;
        color: rgba(255,255,255,0.75);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .status-tab-modern:hover {
        background: rgba(255,255,255,0.15);
        color: white;
    }
    .status-tab-modern.active {
        background: white;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: white;
    }
    .status-tab-modern .count {
        background: rgba(255,255,255,0.2);
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .status-tab-modern.active .count {
        background: #0d9488;
        color: white;
    }
    
    /* Glassmorphic Filter Bar */
    .filter-bar-modern {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .filter-form-modern {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .filter-form-modern .filter-input {
        flex: 1;
        min-width: 160px;
        padding: 0.65rem 1rem;
        border: 1.5px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        font-size: 0.88rem;
        font-family: inherit;
        background: rgba(255,255,255,0.08);
        color: white;
        transition: all 0.3s ease;
    }
    .filter-form-modern .filter-input::placeholder { color: rgba(255,255,255,0.5); }
    .filter-form-modern .filter-input:focus {
        outline: none;
        border-color: rgba(255,255,255,0.5);
        background: rgba(255,255,255,0.15);
        box-shadow: 0 0 0 3px rgba(255,255,255,0.1);
    }
    .filter-form-modern .filter-input option { background: #1e293b; color: white; }
    .filter-form-modern .search-wrapper {
        flex: 2;
        position: relative;
        min-width: 220px;
    }
    .filter-form-modern .search-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.5);
        font-size: 0.85rem;
    }
    .filter-form-modern .search-wrapper input {
        padding-left: 38px;
    }
    .btn-filter-modern {
        padding: 0.65rem 1.5rem;
        border: none;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-filter-apply {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: white;
        box-shadow: 0 4px 12px rgba(99,102,241,0.3);
    }
    .btn-filter-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(99,102,241,0.4); }
    .btn-filter-clear {
        background: rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.8);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .btn-filter-clear:hover { background: rgba(255,255,255,0.2); color: white; }
    
    /* Modern Table Container */
    .table-container-modern {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .table-modern-apps {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern-apps thead th {
        padding: 1rem 1.25rem;
        text-align: left;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        font-weight: 700;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-modern-apps tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern-apps tbody tr:last-child { border-bottom: none; }
    .table-modern-apps tbody tr:hover {
        background: linear-gradient(90deg, #f8fafc, #eef2ff, #f8fafc);
    }
    .table-modern-apps tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.88rem;
        color: #334155;
        vertical-align: middle;
    }
    
    /* Applicant Cell */
    .applicant-cell-modern {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-circle-modern {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        flex-shrink: 0;
        color: white;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .avatar-circle-modern img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .applicant-details-modern strong {
        display: block;
        font-size: 0.9rem;
        color: #1e293b;
        font-weight: 600;
    }
    .applicant-details-modern small {
        color: #94a3b8;
        font-size: 0.78rem;
    }
    
    /* Status Badge */
    .status-pill-modern {
        padding: 5px 14px;
        border-radius: 20px;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    
    /* Priority Badge */
    .priority-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .priority-badge-modern.urgent { background: #fee2e2; color: #dc2626; }
    .priority-badge-modern.high { background: #ffedd5; color: #ea580c; }
    .priority-badge-modern.normal { background: #dcfce7; color: #16a34a; }
    .priority-badge-modern.low { background: #f1f5f9; color: #64748b; }
    
    /* Assigned PIC */
    .pic-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pic-online-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pic-online-dot.online { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.5); }
    .pic-online-dot.offline { background: #9ca3af; }
    .pic-name {
        font-size: 0.85rem;
        color: #334155;
        font-weight: 500;
    }
    .unassigned-pill {
        padding: 4px 12px;
        background: #fef3c7;
        color: #92400e;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    /* Rank & Company Badge */
    .info-badge-modern {
        padding: 4px 10px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 500;
    }
    
    /* Date Cell */
    .date-cell-modern {
        display: flex;
        flex-direction: column;
    }
    .date-cell-modern .date { font-size: 0.85rem; color: #334155; font-weight: 500; }
    .date-cell-modern .time { font-size: 0.72rem; color: #94a3b8; }
    
    /* Action Buttons */
    .action-btns-modern {
        display: flex;
        gap: 6px;
    }
    .btn-action-modern {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-action-modern:hover { transform: translateY(-2px); }
    .btn-action-view {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 2px 8px rgba(59,130,246,0.3);
    }
    .btn-action-view:hover { box-shadow: 0 4px 12px rgba(59,130,246,0.5); }
    .btn-action-assign {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
        box-shadow: 0 2px 8px rgba(139,92,246,0.3);
    }
    .btn-action-assign:hover { box-shadow: 0 4px 12px rgba(139,92,246,0.5); }
    
    /* Empty State */
    .empty-state-modern {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-state-modern i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
        display: block;
    }
    .empty-state-modern h3 {
        color: #64748b;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }
    .empty-state-modern p {
        color: #94a3b8;
        font-size: 0.9rem;
        margin: 0;
    }
    
    /* Result Count */
    .result-count {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .result-count span {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }
    .result-count strong { color: #1e293b; }
    
    /* Modal Styles */
    .modal-overlay-modern {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(6px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .modal-overlay-modern.show { display: flex; animation: fadeInModal 0.3s ease; }
    .modal-box-modern {
        background: white;
        border-radius: 24px;
        max-width: 480px;
        width: 95%;
        overflow: hidden;
        animation: scaleInModal 0.35s ease;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    }
    .modal-header-modern {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 1.5rem 2rem;
        color: white;
    }
    .modal-header-modern h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-body-modern {
        padding: 2rem;
    }
    .modal-body-modern p {
        margin: 0 0 1.25rem;
        font-size: 0.9rem;
        color: #475569;
    }
    .modal-body-modern .form-group {
        margin-bottom: 1.25rem;
    }
    .modal-body-modern label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.4rem;
    }
    .modal-body-modern select,
    .modal-body-modern textarea {
        width: 100%;
        padding: 0.65rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.9rem;
        font-family: inherit;
        background: #fafbfc;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .modal-body-modern select:focus,
    .modal-body-modern textarea:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
        background: white;
    }
    .modal-footer-modern {
        padding: 1.25rem 2rem;
        background: #f8fafc;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid #f1f5f9;
    }
    .btn-modal-cancel-m {
        background: #e5e7eb;
        border: none;
        padding: 10px 24px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.9rem;
        font-family: inherit;
        transition: all 0.2s;
    }
    .btn-modal-cancel-m:hover { background: #d1d5db; }
    .btn-modal-submit-m {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: inherit;
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        transition: all 0.2s;
    }
    .btn-modal-submit-m:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(139,92,246,0.4);
    }
    
    @keyframes fadeInModal { from { opacity: 0; } to { opacity: 1; } }
    @keyframes scaleInModal { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .stats-summary { flex-wrap: wrap; }
        .stat-mini { min-width: 130px; }
    }
    @media (max-width: 768px) {
        .modern-apps-wrapper { padding: 1rem; margin: -1rem; }
        .modern-header { padding: 1.5rem 1.25rem; }
        .modern-header h1 { font-size: 1.5rem; }
        .filter-form-modern { flex-direction: column; }
        .filter-form-modern .filter-input,
        .filter-form-modern .search-wrapper { min-width: 100%; }
        .table-container-modern { border-radius: 16px; overflow-x: auto; }
        .table-modern-apps thead th { font-size: 0.65rem; padding: 0.75rem; }
        .table-modern-apps tbody td { padding: 0.75rem; font-size: 0.8rem; }
        .stats-summary { gap: 0.75rem; }
        .stat-mini { padding: 0.75rem 1rem; }
    }
</style>

<div class="modern-apps-wrapper">

<!-- Header Banner -->
<div class="modern-header">
    <div class="modern-header-content">
        <h1><i class="fas fa-file-alt" style="margin-right: 0.75rem;"></i>Aplikasi Rekrutmen</h1>
        <p>Kelola dan pantau semua lamaran kandidat yang telah di-assign kepada Anda</p>
        
        <div class="view-toggle-container">
            <a href="<?= url('/crewing/applications?view=my') ?>" 
               class="view-toggle-tab <?= ($filters['view'] ?? 'my') === 'my' ? 'active' : '' ?>">
                <i class="fas fa-user-check"></i>
                My Assignments
            </a>
            <a href="<?= url('/crewing/applications?view=all') ?>" 
               class="view-toggle-tab <?= ($filters['view'] ?? 'my') === 'all' ? 'active' : '' ?>">
                <i class="fas fa-globe"></i>
                All Applications
            </a>
        </div>
    </div>
</div>

<!-- Stats Summary -->
<?php 
$totalApps = count($applications ?? []);
$statusBreakdown = [];
foreach (($applications ?? []) as $app) {
    $sName = $app['status_name'] ?? 'Unknown';
    $statusBreakdown[$sName] = ($statusBreakdown[$sName] ?? 0) + 1;
}
?>
<div class="stats-summary">
    <div class="stat-mini">
        <div class="stat-mini-icon blue"><i class="fas fa-folder-open"></i></div>
        <div>
            <h3><?= $totalApps ?></h3>
            <span>Total</span>
        </div>
    </div>
    <?php 
    $iconMap = ['Pending' => 'clock', 'Review' => 'search', 'Interview' => 'comments', 'Approved' => 'check-circle'];
    $colorMap = ['Pending' => 'orange', 'Review' => 'purple', 'Interview' => 'purple', 'Approved' => 'green'];
    $shown = 0;
    foreach ($statusBreakdown as $sName => $sCount):
        if ($shown >= 3) break;
        $icon = 'folder';
        $color = 'purple';
        foreach ($iconMap as $key => $val) {
            if (stripos($sName, $key) !== false) { $icon = $val; break; }
        }
        foreach ($colorMap as $key => $val) {
            if (stripos($sName, $key) !== false) { $color = $val; break; }
        }
        $shown++;
    ?>
    <div class="stat-mini">
        <div class="stat-mini-icon <?= $color ?>"><i class="fas fa-<?= $icon ?>"></i></div>
        <div>
            <h3><?= $sCount ?></h3>
            <span><?= htmlspecialchars($sName) ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Status Filter Tabs -->
<div class="status-tabs-modern">
    <a href="<?= url('/crewing/applications?view=' . ($filters['view'] ?? 'my')) ?>" 
       class="status-tab-modern <?= empty($filters['status']) ? 'active' : '' ?>">
        <i class="fas fa-th-list"></i> Semua
    </a>
    <?php foreach ($statusCounts as $status): ?>
    <a href="<?= url('/crewing/applications?view=' . ($filters['view'] ?? 'my') . '&status=' . $status['id']) ?>" 
       class="status-tab-modern <?= ($filters['status'] ?? '') == $status['id'] ? 'active' : '' ?>">
        <span style="width:8px;height:8px;border-radius:50%;background:<?= $status['color'] ?>;display:inline-block;"></span>
        <?= $status['name'] ?>
        <span class="count"><?= $status['count'] ?></span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Filter Bar -->
<div class="filter-bar-modern">
    <form method="GET" action="<?= url('/crewing/applications') ?>" class="filter-form-modern">
        <input type="hidden" name="view" value="<?= $filters['view'] ?? 'my' ?>">
        
        <select name="department" class="filter-input">
            <option value="">🏢 Semua Department</option>
            <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['id'] ?>" <?= ($filters['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                <?= $dept['name'] ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <select name="priority" class="filter-input">
            <option value="">⚡ Semua Prioritas</option>
            <option value="urgent" <?= ($filters['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>🔴 Urgent</option>
            <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>🟠 High</option>
            <option value="normal" <?= ($filters['priority'] ?? '') === 'normal' ? 'selected' : '' ?>>🟢 Normal</option>
            <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>⚪ Low</option>
        </select>
        
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Cari applicant atau vacancy..." 
                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="filter-input">
        </div>
        
        <button type="submit" class="btn-filter-modern btn-filter-apply">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <a href="<?= url('/crewing/applications?view=' . ($filters['view'] ?? 'my')) ?>" class="btn-filter-modern btn-filter-clear">
            <i class="fas fa-times"></i> Reset
        </a>
    </form>
</div>

<!-- Applications Table -->
<div class="table-container-modern">
    <div class="result-count">
        <span>Menampilkan <strong><?= $totalApps ?></strong> aplikasi</span>
        <span><i class="fas fa-clock" style="margin-right:4px;"></i><?= date('d M Y, H:i') ?></span>
    </div>
    
    <?php if (empty($applications)): ?>
    <div class="empty-state-modern">
        <i class="fas fa-inbox"></i>
        <h3>Tidak ada aplikasi ditemukan</h3>
        <p>Coba ubah filter atau periksa tab status lainnya</p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
    <table class="table-modern-apps">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Vacancy</th>
                <th>Status</th>
                <th>Prioritas</th>
                <th>Crewing PIC</th>
                <th>Rank</th>
                <th>Company</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $avatarColors = ['#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981','#6366f1','#ef4444','#14b8a6'];
            foreach ($applications as $idx => $app): 
                $initials = strtoupper(substr($app['full_name'], 0, 2));
                $avatarColor = $avatarColors[$idx % count($avatarColors)];
            ?>
            <tr>
                <td>
                    <div class="applicant-cell-modern">
                        <div class="avatar-circle-modern" style="background: linear-gradient(135deg, <?= $avatarColor ?>, <?= $avatarColor ?>dd);">
                            <?php if (!empty($app['avatar'])): ?>
                                <img src="<?= url('/uploads/avatars/' . $app['avatar']) ?>" alt="<?= htmlspecialchars($app['full_name']) ?>">
                            <?php else: ?>
                                <?= $initials ?>
                            <?php endif; ?>
                        </div>
                        <div class="applicant-details-modern">
                            <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                            <small><?= htmlspecialchars($app['email']) ?></small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <span style="font-weight:500;color:#1e293b;"><?= htmlspecialchars($app['vacancy_title']) ?></span>
                        <?php if (!empty($app['department_name'])): ?>
                        <br><small style="color:#94a3b8;"><?= htmlspecialchars($app['department_name']) ?></small>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <span class="status-pill-modern" style="background-color: <?= $app['status_color'] ?>">
                        <?= $app['status_name'] ?>
                    </span>
                </td>
                <td>
                    <?php
                    $pClass = $app['priority'] ?? 'normal';
                    $pIcons = ['urgent' => '🔴', 'high' => '🟠', 'normal' => '🟢', 'low' => '⚪'];
                    ?>
                    <span class="priority-badge-modern <?= $pClass ?>">
                        <?= $pIcons[$pClass] ?? '🟢' ?> <?= ucfirst($pClass) ?>
                    </span>
                </td>
                <td>
                    <?php if ($app['assigned_to_name']): ?>
                    <div class="pic-cell">
                        <span class="pic-online-dot <?= !empty($app['crewing_online']) && $app['crewing_online'] ? 'online' : 'offline' ?>"></span>
                        <span class="pic-name"><?= htmlspecialchars($app['assigned_to_name']) ?></span>
                    </div>
                    <?php else: ?>
                    <span class="unassigned-pill"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>Unassigned</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="info-badge-modern"><?= htmlspecialchars($app['crewing_rank'] ?? '-') ?></span>
                </td>
                <td>
                    <span class="info-badge-modern"><?= htmlspecialchars($app['crewing_company'] ?? '-') ?></span>
                </td>
                <td>
                    <div class="date-cell-modern">
                        <span class="date"><?= date('d M Y', strtotime($app['submitted_at'])) ?></span>
                        <span class="time"><?= date('H:i', strtotime($app['submitted_at'])) ?></span>
                    </div>
                </td>
                <td>
                    <div class="action-btns-modern">
                        <a href="<?= url('/crewing/applications/' . $app['id']) ?>" class="btn-action-modern btn-action-view" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" class="btn-action-modern btn-action-assign assign-btn-modern" 
                                data-app-id="<?= $app['id'] ?>" 
                                data-app-name="<?= htmlspecialchars($app['full_name'], ENT_QUOTES) ?>"
                                title="Assign">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

</div><!-- /.modern-apps-wrapper -->

<!-- Assign Modal -->
<div class="modal-overlay-modern" id="assignModalModern">
    <div class="modal-box-modern">
        <div class="modal-header-modern">
            <h3><i class="fas fa-user-plus"></i> Assign Aplikasi</h3>
        </div>
        <form method="POST" action="" id="assignFormModern">
            <?= csrf_field() ?>
            <div class="modal-body-modern">
                <p>Assign aplikasi untuk: <strong id="assignNameModern"></strong></p>
                
                <div class="form-group">
                    <label><i class="fas fa-users" style="margin-right:6px;"></i>Assign ke Crewing Staff</label>
                    <select name="assign_to" required>
                        <option value="">Pilih Crewing Staff...</option>
                        <?php foreach ($crewingStaff as $crew): ?>
                        <option value="<?= $crew['id'] ?>">
                            <?= htmlspecialchars($crew['full_name']) ?>
                            (<?= $crew['active_assignments'] ?> aktif)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-sticky-note" style="margin-right:6px;"></i>Catatan (opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Tambahkan catatan untuk crewing..."></textarea>
                </div>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn-modal-cancel-m modal-close-modern">Batal</button>
                <button type="submit" class="btn-modal-submit-m">
                    <i class="fas fa-check" style="margin-right:6px;"></i>Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assignModalModern');
    const form = document.getElementById('assignFormModern');
    const nameEl = document.getElementById('assignNameModern');
    
    // Open modal
    document.querySelectorAll('.assign-btn-modern').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.dataset.appId;
            const name = this.dataset.appName;
            
            form.action = '<?= url('/crewing/applications/assign/') ?>' + appId;
            nameEl.textContent = name;
            modal.classList.add('show');
        });
    });
    
    // Close modal
    modal.querySelectorAll('.modal-close-modern').forEach(el => {
        el.addEventListener('click', () => modal.classList.remove('show'));
    });
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('show');
    });
    
    // ESC key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') modal.classList.remove('show');
    });
});
</script>
