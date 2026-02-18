<!-- Modern Horizontal Glassmorphic Pipeline -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

<style>
    /* Inter Font Override - exclude Font Awesome icons */
    body, *:not(i):not(.fas):not(.far):not(.fab):not(.fa) { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important; }
    
    /* Modern Pipeline Container - Custom background without affecting layout */
    .modern-pipeline-wrapper {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3b82f6 100%);
        min-height: calc(100vh - 120px);
        padding: 2rem;
        margin: -2rem;
        border-radius: 0;
    }
    
    /* Gradient Header Banner */
    .modern-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #3b82f6 100%);
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    .modern-header::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='20' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%23ffffff' fill-opacity='0.08' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.5;
    }
    .modern-header-content { position: relative; z-index: 2; }
    .modern-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    .modern-header p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.1rem;
        margin: 0 0 1.5rem 0;
        font-weight: 400;
    }
    
    /* View Toggle Tabs */
    .view-toggle-container {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 16px;
        padding: 6px;
        gap: 6px;
    }
    .view-toggle-tab {
        padding: 12px 28px;
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .view-toggle-tab:hover { 
        color: white; 
        background: rgba(255, 255, 255, 0.1);
    }
    .view-toggle-tab.active {
        background: white;
        color: #2563eb;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Pending Requests Alert */
    .pending-alert {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #f59e0b;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }
    .pending-alert h5 {
        margin: 0 0 0.75rem 0;
        color: #92400e;
        font-weight: 700;
        font-size: 1rem;
    }
    .pending-item {
        background: white;
        padding: 0.875rem 1.125rem;
        border-radius: 12px;
        margin-top: 0.625rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    
    /* Horizontal Scroll Container */
    .pipeline-scroll-container {
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 1.5rem;
        margin: 0 -2rem;
        padding-left: 2rem;
        padding-right: 2rem;
        cursor: grab;
    }
    .pipeline-scroll-container:active { cursor: grabbing; }
    .pipeline-scroll-container::-webkit-scrollbar { height: 8px; }
    .pipeline-scroll-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .pipeline-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }
    .pipeline-scroll-container::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    /* Pipeline Columns Container */
    .pipeline-columns {
        display: flex;
        gap: 1.5rem;
        min-width: min-content;
    }
    
    /* Status Column */
    .status-column {
        width: 320px;
        flex-shrink: 0;
    }
    .status-column-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding: 0 0.5rem;
    }
    .status-title-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 0 12px currentColor;
    }
    .status-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: white;
        margin: 0;
        letter-spacing: -0.01em;
    }
    .status-count {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 700;
    }
    
    /* Glassmorphic Column Card */
    .glass-column-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 20px;
        padding: 1.25rem;
        min-height: 500px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        transition: all 0.3s ease;
    }
    .glass-column-card:hover {
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.15);
    }
    
    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.5rem;
        text-align: center;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.4;
    }
    .empty-state p {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    /* Application Card */
    .app-card-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        padding: 1.125rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        position: relative;
    }
    
    /* Candidate Avatar/Photo */
    .candidate-header-modern {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .candidate-avatar-modern {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 3px solid white;
        box-shadow: 0 3px 12px rgba(99, 102, 241, 0.4);
        position: relative;
        overflow: hidden;
    }
    .candidate-avatar-modern img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        object-position: center;
        background: white;
    }
    .candidate-info-modern {
        flex: 1;
        min-width: 0;
    }
    .app-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        border-color: rgba(59, 130, 246, 0.4);
    }
    .app-card-modern h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.01em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .app-card-modern .vacancy {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-weight: 500;
    }
    .app-card-modern .email {
        font-size: 0.8125rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Badges */
    .badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .badge-mcu {
        background: #dcfce7;
        color: #166534;
    }
    .badge-erp {
        background: #dbeafe;
        color: #1e40af;
    }
    
    /* Action Buttons */
    .card-actions-modern {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .btn-modern {
        flex: 1;
        padding: 0.625rem 1rem;
        border: none;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        position: relative;
        z-index: 10;
    }
    .btn-modern:hover {
        transform: translateY(-1px);
    }
    .btn-view {
        background: white;
        color: #475569;
        border: 2px solid #e2e8f0;
    }
    .btn-view:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background: #eff6ff;
    }
    .btn-claim {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }
    .btn-claim:hover {
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }
    .btn-claim.pending {
        background: #fbbf24;
        color: #78350f;
        cursor: not-allowed;
        box-shadow: none;
    }
    .btn-claim.pending:hover {
        transform: none;
    }
    .btn-status {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }
    .btn-status:hover {
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }
    .btn-erp {
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        color: white;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.25);
    }
    .btn-erp:hover {
        box-shadow: 0 6px 16px rgba(20, 184, 166, 0.4);
    }
    .assigned-badge-modern {
        background: #dcfce7;
        color: #16a34a;
        padding: 6px 12px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 0.625rem;
        font-size: 0.8125rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    
    /* Modal Styles - Reuse from original */
    .p-modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.65);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }
    .p-modal.show { display: flex; animation: fadeIn 0.3s ease; }
    .p-modal-box {
        background: white;
        border-radius: 20px;
        max-width: 480px;
        width: 95%;
        overflow: hidden;
        animation: scaleIn 0.4s ease;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    .p-modal-header {
        padding: 20px 25px;
        color: white;
    }
    .p-modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }
    .p-modal-body {
        padding: 25px;
        overflow-y: auto;
        flex: 1;
    }
    .p-modal-footer {
        padding: 15px 25px;
        background: #f8f9fa;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid #f3f4f6;
    }
    .btn-modal-cancel {
        background: #e5e7eb;
        border: none;
        padding: 10px 22px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 500;
    }
    .btn-modal-submit {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-modal-submit:disabled {
        opacity: 0.7;
        cursor: wait;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    
    /* Detail Modal Styles */
    #detailModal .p-modal-box { max-width: 600px; }
    .detail-loading { text-align: center; padding: 40px; color: #9ca3af; }
    .detail-loading i { font-size: 2rem; margin-bottom: 10px; display: block; }
    .detail-profile { display: flex; align-items: center; gap: 18px; margin-bottom: 20px; }
    .detail-avatar {
        width: 70px; height: 70px; border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; font-weight: 700; flex-shrink: 0;
    }
    .detail-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .detail-name { font-size: 1.2rem; font-weight: 700; color: #1e3a5f; margin: 0 0 4px; }
    .detail-vacancy { color: #6b7280; font-size: 0.88rem; }
    .detail-status-badge {
        display: inline-block; padding: 4px 12px; border-radius: 20px;
        color: white; font-size: 0.78rem; font-weight: 600; margin-top: 6px;
    }
    .detail-section { margin-bottom: 16px; }
    .detail-section-title {
        font-size: 0.82rem; font-weight: 700; color: #3b82f6;
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 8px; padding-bottom: 6px;
        border-bottom: 2px solid #eff6ff;
    }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .detail-item label { display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 2px; }
    .detail-item span { font-size: 0.88rem; color: #1f2937; font-weight: 500; }
    .detail-docs { display: flex; flex-wrap: wrap; gap: 6px; }
    .detail-doc-badge {
        background: #eff6ff; color: #1d4ed8; padding: 4px 10px;
        border-radius: 6px; font-size: 0.78rem; font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .pipeline-columns { flex-direction: column; }
        .status-column { width: 100%; }
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Modern Pipeline Wrapper with Gradient Background -->
<div class="modern-pipeline-wrapper">

<!-- Header Banner -->
<div class="modern-header">
    <div class="modern-header-content">
        <h1><i class="fas fa-columns" style="margin-right: 0.75rem;"></i>Pipeline Rekrutmen</h1>
        <p>Ambil dan kelola lamaran pelamar dengan efisien. Pantau status kandidat secara real-time.</p>
        
        <div class="view-toggle-container">
            <a href="<?= url('/crewing/pipeline?view=available') ?>" 
               class="view-toggle-tab <?= $currentView == 'available' ? 'active' : '' ?>">
                <i class="fas fa-inbox"></i>
                Tersedia
            </a>
            <a href="<?= url('/crewing/pipeline?view=my') ?>" 
               class="view-toggle-tab <?= $currentView == 'my' ? 'active' : '' ?>">
                <i class="fas fa-user-check"></i>
                Milik Saya
            </a>
            <button class="view-toggle-tab <?= $currentView == 'archived' ? 'active' : '' ?>" 
                    id="archivedTab" 
                    onclick="loadArchivedView()" 
                    style="border: none; background: transparent;">
                <i class="fas fa-archive"></i>
                Arsip
            </button>
        </div>
    </div>
</div>

<!-- NEW: Candidate Selected You Alert -->
<?php
$newCandidateAlerts = $newCandidateAlerts ?? [];
if (!empty($newCandidateAlerts)):
?>
<div class="new-candidate-alert" id="newCandidateAlert" style="
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #10b981;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    animation: slideDown 0.5s ease;
">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <h5 style="margin: 0; color: #065f46; font-weight: 700; font-size: 1rem;">
            <i class="fas fa-bell" style="margin-right: 0.5rem; animation: bellRing 1s ease infinite;"></i>
            Kandidat Baru Memilih Anda! (<?= count($newCandidateAlerts) ?>)
        </h5>
        <button onclick="dismissAllAlerts()" style="
            background: rgba(255,255,255,0.7);
            border: 1px solid #6ee7b7;
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            color: #065f46;
            transition: all 0.2s;
        " onmouseover="this.style.background='white'" onmouseout="this.style.background='rgba(255,255,255,0.7)'">
            <i class="fas fa-check-double" style="margin-right: 4px;"></i>Tandai Semua Dibaca
        </button>
    </div>
    <small style="color: #065f46; opacity: 0.8;">Pelamar berikut telah memilih Anda sebagai perekrut mereka</small>
    
    <?php foreach ($newCandidateAlerts as $alert): 
        $alertInitials = strtoupper(substr($alert['applicant_name'], 0, 1));
        $nameParts = explode(' ', $alert['applicant_name']);
        if (isset($nameParts[1])) $alertInitials .= strtoupper(substr($nameParts[1], 0, 1));
    ?>
        <div class="candidate-alert-item" id="alertItem-<?= $alert['id'] ?>" style="
            background: white;
            padding: 0.875rem 1.125rem;
            border-radius: 12px;
            margin-top: 0.625rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        ">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="
                    width: 42px; height: 42px; border-radius: 50%;
                    background: linear-gradient(135deg, #10b981, #059669);
                    color: white; display: flex; align-items: center; justify-content: center;
                    font-size: 0.9rem; font-weight: 700; flex-shrink: 0;
                    overflow: hidden; position: relative;
                ">
                    <?php if (!empty($alert['applicant_avatar'])): ?>
                        <img src="<?= url('/') ?><?= htmlspecialchars($alert['applicant_avatar']) ?>" 
                             style="width:100%;height:100%;object-fit:cover;border-radius:50%;" 
                             alt="<?= htmlspecialchars($alert['applicant_name']) ?>">
                    <?php else: ?>
                        <?= $alertInitials ?>
                    <?php endif; ?>
                </div>
                <div>
                    <strong style="color: #1e3a5f; font-size: 0.9rem;"><?= htmlspecialchars($alert['applicant_name']) ?></strong>
                    <small style="display: block; color: #6b7280;">
                        <i class="fas fa-briefcase" style="margin-right: 3px;"></i>
                        <?= htmlspecialchars($alert['vacancy_title'] ?? 'Manual Entry') ?>
                    </small>
                    <small style="display: block; color: #9ca3af; font-size: 0.75rem;">
                        <i class="fas fa-clock" style="margin-right: 3px;"></i>
                        <?= date('d M Y, H:i', strtotime($alert['created_at'])) ?>
                    </small>
                </div>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <a href="<?= url('/crewing/pipeline?view=my') ?>" style="
                    background: linear-gradient(135deg, #10b981, #059669);
                    color: white; padding: 6px 12px; border-radius: 8px;
                    font-size: 0.78rem; font-weight: 600; text-decoration: none;
                    display: inline-flex; align-items: center; gap: 4px;
                    transition: all 0.2s;
                ">
                    <i class="fas fa-eye"></i>Lihat
                </a>
                <button onclick="dismissAlert(<?= $alert['id'] ?>)" style="
                    background: #f3f4f6; border: 1px solid #e5e7eb;
                    padding: 6px 10px; border-radius: 8px; cursor: pointer;
                    font-size: 0.78rem; color: #6b7280; transition: all 0.2s;
                " onmouseover="this.style.background='#fee2e2';this.style.color='#991b1b'" 
                   onmouseout="this.style.background='#f3f4f6';this.style.color='#6b7280'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bellRing {
    0%, 100% { transform: rotate(0); }
    10%, 30% { transform: rotate(12deg); }
    20%, 40% { transform: rotate(-12deg); }
    50% { transform: rotate(0); }
}
</style>

<script>
function dismissAlert(applicationId) {
    const item = document.getElementById('alertItem-' + applicationId);
    if (item) {
        item.style.opacity = '0';
        item.style.transform = 'translateX(50px)';
        item.style.maxHeight = item.scrollHeight + 'px';
        
        setTimeout(() => {
            item.style.maxHeight = '0';
            item.style.padding = '0';
            item.style.margin = '0';
            item.style.overflow = 'hidden';
        }, 200);
        
        setTimeout(() => {
            item.remove();
            // Check if no more alerts
            const remaining = document.querySelectorAll('.candidate-alert-item');
            if (remaining.length === 0) {
                const alertBox = document.getElementById('newCandidateAlert');
                if (alertBox) {
                    alertBox.style.opacity = '0';
                    alertBox.style.transform = 'translateY(-20px)';
                    setTimeout(() => alertBox.remove(), 300);
                }
            }
        }, 500);
    }
    
    // Send AJAX request
    fetch('<?= url('/crewing/pipeline/dismiss-alert') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'application_id=' + applicationId + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    });
}

function dismissAllAlerts() {
    const alertBox = document.getElementById('newCandidateAlert');
    if (alertBox) {
        alertBox.style.opacity = '0';
        alertBox.style.transform = 'translateY(-20px)';
        setTimeout(() => alertBox.remove(), 300);
    }
    
    // Send AJAX request to dismiss all
    fetch('<?= url('/crewing/pipeline/dismiss-alert') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'application_id=0&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    });
}
</script>

<?php endif; ?>

<!-- Pending Requests Alert -->
<?php if (!empty($myPendingRequests)): ?>
<div class="pending-alert">
    <h5><i class="fas fa-hourglass-half" style="margin-right: 0.5rem;"></i>Request Pending (<?= count($myPendingRequests) ?>)</h5>
    <small style="color: #92400e;">Menunggu approval dari Master Admin</small>
    <?php foreach ($myPendingRequests as $req): ?>
        <div class="pending-item">
            <div>
                <strong><?= htmlspecialchars($req['applicant_name']) ?></strong>
                <small style="display: block; color: #6b7280;"><?= htmlspecialchars($req['vacancy_title'] ?? 'N/A') ?></small>
            </div>
            <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                <i class="fas fa-clock" style="margin-right: 4px;"></i>Pending
            </span>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Horizontal Scrolling Pipeline -->
<div class="pipeline-scroll-container" id="pipelineScroll">
    <div class="pipeline-columns">
        <?php 
        $statusColors = [
            '1' => '#3b82f6',  // Blue
            '2' => '#f59e0b',  // Amber
            '3' => '#a855f7',  // Purple
            '4' => '#f97316',  // Orange
            '5' => '#14b8a6',  // Teal
            '6' => '#22c55e',  // Green
            '7' => '#ef4444',  // Red
            '8' => '#64748b',  // Gray
        ];
        
        foreach ($statuses as $status): 
            $statusId = $status['id'];
            $count = isset($pipeline[$statusId]) ? count($pipeline[$statusId]) : 0;
            $color = $statusColors[$statusId] ?? '#6b7280';
        ?>
        <div class="status-column">
            <div class="status-column-header">
                <div class="status-title-wrapper">
                    <div class="status-indicator" style="background: <?= $color ?>;"></div>
                    <h3 class="status-title"><?= htmlspecialchars($status['name']) ?></h3>
                </div>
                <span class="status-count"><?= $count ?></span>
            </div>
            
            <div class="glass-column-card">
                <?php if ($count == 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No applications</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pipeline[$statusId] as $app): 
                        // Generate initials from name
                        $nameParts = explode(' ', $app['applicant_name']);
                        $initials = strtoupper(substr($nameParts[0], 0, 1));
                        if (isset($nameParts[1])) {
                            $initials .= strtoupper(substr($nameParts[1], 0, 1));
                        }
                    ?>
                        <div class="app-card-modern" data-app-id="<?= $app['id'] ?>">
                            <!-- Candidate Header with Avatar -->
                            <div class="candidate-header-modern" onclick="showDetail(<?= $app['id'] ?>)" style="cursor: pointer;">
                                <div class="candidate-avatar-modern">
                                    <?php if (!empty($app['applicant_avatar'])): ?>
                                        <img src="<?= url('/') ?><?= htmlspecialchars($app['applicant_avatar']) ?>" alt="<?= htmlspecialchars($app['applicant_name']) ?>">
                                    <?php else: ?>
                                        <?= $initials ?>
                                    <?php endif; ?>
                                </div>
                                <div class="candidate-info-modern">
                                    <h4><?= htmlspecialchars($app['applicant_name']) ?></h4>
                                    <div class="vacancy">
                                        <i class="fas fa-briefcase"></i>
                                        <?= htmlspecialchars($app['vacancy_title'] ?? 'N/A') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="email" onclick="showDetail(<?= $app['id'] ?>)" style="cursor: pointer;">
                                <i class="fas fa-envelope"></i>
                                <?= htmlspecialchars($app['applicant_email'] ?? '') ?>
                            </div>
                            
                            <?php if (!empty($app['medical_email_sent_at'])): ?>
                                <div class="badge-modern badge-mcu">
                                    <i class="fas fa-heartbeat"></i>
                                    MCU Email • <?= date('d/m/Y', strtotime($app['medical_email_sent_at'])) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($app['sent_to_erp_at'])): ?>
                                <div class="badge-modern badge-erp">
                                    <i class="fas fa-check-circle"></i>
                                    Sent to ERP • ID: <?= $app['erp_crew_id'] ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($app['email_sent_count']) && $app['email_sent_count'] > 0): ?>
                                <div class="badge-modern badge-email-sent">
                                    <i class="fas fa-envelope-open-text"></i>
                                    Email Terkirim (<?= $app['email_sent_count'] ?>) • <?= date('d/m/Y', strtotime($app['last_email_sent_at'])) ?>
                                </div>
                            <?php else: ?>
                                <div class="badge-modern badge-email-pending">
                                    <i class="fas fa-envelope"></i>
                                    Belum Diemail
                                </div>
                            <?php endif; ?>

                            <div class="card-actions-modern">
                                <?php if ($currentView == 'available'): ?>
                                    <?php if (!empty($app['my_pending_request'])): ?>
                                        <button class="btn-modern btn-claim pending" disabled>
                                            <i class="fas fa-hourglass-half"></i> Pending...
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-modern btn-view" onclick="event.stopPropagation(); showDetail(<?= $app['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-modern btn-claim" onclick="event.stopPropagation(); openClaimModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($app['vacancy_title'] ?? '', ENT_QUOTES) ?>')">
                                            <i class="fas fa-hand-paper"></i> Request
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div style="width: 100%;">
                                        <div class="assigned-badge-modern">
                                            <i class="fas fa-check-circle"></i>
                                            Ditangani Anda
                                        </div>
                                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                            <button class="btn-modern btn-view" onclick="event.stopPropagation(); showDetail(<?= $app['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-modern btn-status" onclick="event.stopPropagation(); openStatusModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>', <?= $app['status_id'] ?>)">
                                                <i class="fas fa-exchange-alt"></i> Status
                                            </button>
                                            <?php if ($statusId == '6' && empty($app['sent_to_erp_at'])): ?>
                                            <button class="btn-modern btn-erp" onclick="event.stopPropagation(); openErpModal(<?= $app['id'] ?>, '<?= htmlspecialchars($app['applicant_name'], ENT_QUOTES) ?>')">
                                                <i class="fas fa-paper-plane"></i> ERP
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn-modern" style="width:100%;background:#fff5f5;color:#991b1b;border:2px solid #fca5a5;box-shadow:none;" 
                                            onmouseover="this.style.background='#fee2e2';this.style.borderColor='#ef4444'" 
                                            onmouseout="this.style.background='#fff5f5';this.style.borderColor='#fca5a5'"
                                            onclick="event.stopPropagation(); archiveApplication(<?= $app['id'] ?>)">
                                            <i class="fas fa-archive"></i> Pindahkan ke Arsip
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</div><!-- /.modern-pipeline-wrapper -->

<!-- Drag-to-scroll functionality -->
<script>
const scrollContainer = document.getElementById('pipelineScroll');
let isDown = false;
let startX;
let scrollLeft;

scrollContainer.addEventListener('mousedown', (e) => {
    isDown = true;
    scrollContainer.style.cursor = 'grabbing';
    startX = e.pageX - scrollContainer.offsetLeft;
    scrollLeft = scrollContainer.scrollLeft;
});

scrollContainer.addEventListener('mouseleave', () => {
    isDown = false;
    scrollContainer.style.cursor = 'grab';
});

scrollContainer.addEventListener('mouseup', () => {
    isDown = false;
    scrollContainer.style.cursor = 'grab';
});

scrollContainer.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - scrollContainer.offsetLeft;
    const walk = (x - startX) * 2;
    scrollContainer.scrollLeft = scrollLeft - walk;
});
</script>

<!-- All modals and JavaScript functions -->
<?php include __DIR__ . '/_modals_and_js.php'; ?>
