<!-- Crewing Pipeline - Clean Professional Design -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<style>
    /* PREMIUM BLUE BACKGROUND WITH PATTERNS */
    .admin-content.crewing-content{
        background:linear-gradient(135deg,#0c1929 0%,#132f4c 30%,#1a3a5c 50%,#0f4c75 70%,#1b6ca8 100%)!important;
        position:relative;
    }
    .admin-content.crewing-content::before{
        content:'';position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;
        background-image:
            radial-gradient(circle at 20% 30%,rgba(59,130,246,0.08) 0%,transparent 50%),
            radial-gradient(circle at 80% 70%,rgba(14,165,233,0.06) 0%,transparent 50%),
            radial-gradient(circle at 50% 50%,rgba(99,102,241,0.04) 0%,transparent 40%),
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50' cy='50' r='40' stroke='%23ffffff' stroke-width='0.5' fill='none' stroke-opacity='0.02'/%3E%3C/svg%3E");
    }
    .admin-content.crewing-content>*{position:relative;z-index:1}
    body,*:not(i):not(.fas):not(.far):not(.fab):not(.fa){font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif!important}

    .pipeline-header{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);color:#fff;padding:32px 36px;border-radius:20px;margin-bottom:24px;position:relative;overflow:hidden;box-shadow:0 10px 40px rgba(26,26,46,0.2)}
    .pipeline-header::before{content:'';position:absolute;top:0;right:0;width:300px;height:100%;background:linear-gradient(135deg,transparent 40%,rgba(14,165,233,0.15) 100%)}
    .pipeline-header::after{content:'';position:absolute;bottom:-20px;right:40px;width:120px;height:120px;border:2px solid rgba(255,255,255,0.06);border-radius:50%}
    .pipeline-header h2{margin:0 0 4px;font-weight:800;font-size:1.6rem;letter-spacing:-0.03em;position:relative;z-index:1}
    .pipeline-header small{opacity:0.7;position:relative;z-index:1;font-weight:400;font-size:0.88rem}

    .view-tabs{display:flex;gap:6px;margin-bottom:24px;background:#fff;padding:5px;border-radius:14px;width:fit-content;box-shadow:0 1px 4px rgba(0,0,0,0.06);border:1px solid #eef0f4}
    .view-tab{padding:10px 24px;background:transparent;border:none;border-radius:10px;cursor:pointer;font-weight:600;color:#8b95a5;transition:all 0.25s;text-decoration:none;font-size:0.86rem;display:inline-flex;align-items:center;gap:7px}
    .view-tab.active{background:#0f3460;color:#fff;box-shadow:0 2px 8px rgba(15,52,96,0.25)}
    .view-tab:hover:not(.active){background:#f5f7fa;color:#374151}

    .pending-requests-box{background:#fff;border-radius:16px;padding:18px 22px;margin-bottom:20px;border:1px solid #fde68a;box-shadow:0 2px 8px rgba(245,158,11,0.08)}
    .pending-requests-box h5{margin:0 0 10px;color:#92400e;font-weight:700;font-size:0.9rem}
    .pending-item{background:#fefce8;padding:10px 16px;border-radius:10px;margin-top:8px;display:flex;justify-content:space-between;align-items:center;border:1px solid #fef08a}
    .pending-item:hover{background:#fef9c3}

    .pipeline-board{display:flex;flex-direction:row!important;flex-wrap:nowrap!important;gap:16px;overflow-x:auto;overflow-y:hidden;padding:4px 2px 20px;scroll-behavior:smooth;width:100%;box-sizing:border-box;align-items:flex-start}
    .pipeline-board::-webkit-scrollbar{height:7px}
    .pipeline-board::-webkit-scrollbar-track{background:rgba(255,255,255,0.1);border-radius:10px}
    .pipeline-board::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.35);border-radius:10px}
    .pipeline-board::-webkit-scrollbar-thumb:hover{background:rgba(255,255,255,0.55)}

    .pipeline-column{min-width:270px;width:270px;max-width:270px;background:#fff;border-radius:16px;flex-shrink:0!important;flex-grow:0!important;border:1px solid #eef0f4;box-shadow:0 1px 4px rgba(0,0,0,0.04);transition:box-shadow 0.3s;display:flex;flex-direction:column}
    .pipeline-column:hover{box-shadow:0 4px 16px rgba(0,0,0,0.07)}
    .column-header{padding:12px 16px;color:#fff;display:flex;justify-content:space-between;align-items:center;font-weight:700;border-radius:16px 16px 0 0;font-size:0.78rem;letter-spacing:0.04em;text-transform:uppercase;flex-shrink:0}
    .column-header .count{background:rgba(255,255,255,0.25);padding:2px 10px;border-radius:20px;font-size:0.75rem;font-weight:700}
    .column-body{padding:10px;max-height:calc(100vh - 320px);min-height:120px;overflow-y:auto;flex:1}
    .column-body::-webkit-scrollbar{width:4px}
    .column-body::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:4px}
    .empty-column{text-align:center;padding:30px 12px;color:#c4cad4}
    .empty-column i{font-size:1.6rem;display:block;margin-bottom:8px;opacity:0.4}
    .empty-column small{font-weight:500;font-size:0.8rem}

    .app-card{background:#fff;border-radius:14px;padding:16px;margin-bottom:10px;border:1px solid #eef0f4;transition:all 0.3s;cursor:pointer;position:relative}
    .app-card:hover{border-color:#c7d2fe;box-shadow:0 6px 24px rgba(99,102,241,0.1);transform:translateY(-2px)}
    .card-profile{display:flex;align-items:center;gap:12px;margin-bottom:10px}
    .card-avatar{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:700;flex-shrink:0;overflow:hidden}
    .card-avatar img{width:100%;height:100%;object-fit:cover;border-radius:12px}
    .card-info h4{margin:0 0 2px;font-size:0.9rem;font-weight:700;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:175px}
    .card-info .vacancy{color:#8b95a5;font-size:0.76rem;margin:0;font-weight:500;display:flex;align-items:center;gap:4px}
    .card-info .vacancy i{color:#6366f1;font-size:0.68rem}
    .card-meta{font-size:0.74rem;color:#9ca3af;margin-bottom:8px;display:flex;align-items:center;gap:5px;padding:5px 8px;background:#f8f9fb;border-radius:6px}

    .badge-sm{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:6px;font-size:0.72rem;font-weight:600;margin-bottom:6px}
    .badge-mcu{background:#ecfdf5;color:#059669;border:1px solid #a7f3d0}
    .badge-erp{background:#eef2ff;color:#4f46e5;border:1px solid #c7d2fe}
    .badge-email-sent{background:#fffbeb;color:#b45309;border:1px solid #fde68a}
    .badge-email-none{background:#f9fafb;color:#9ca3af;border:1px solid #e5e7eb}

    .erp-checklist{background:#f8f9fb;border:1px solid #eef0f4;border-radius:12px;padding:12px 14px;margin-bottom:8px}
    .erp-checklist-header{display:flex;justify-content:space-between;align-items:center;font-size:0.76rem;font-weight:700;color:#4f46e5;margin-bottom:6px}
    .erp-progress-bar{background:#e5e7eb;border-radius:4px;height:4px;overflow:hidden;margin-bottom:6px}
    .erp-progress-fill{height:100%;border-radius:4px;transition:width 0.6s ease}
    .erp-check-item{display:flex;align-items:center;gap:4px;padding:2px 5px;border-radius:5px;font-size:0.67rem;font-weight:500}

    .card-actions{display:flex;gap:6px;flex-wrap:wrap}
    .btn-detail-sm{padding:8px 12px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;color:#6b7280;cursor:pointer;transition:all 0.2s;font-size:0.82rem}
    .btn-detail-sm:hover{border-color:#6366f1;color:#6366f1;background:#eef2ff}
    .btn-claim{flex:1;padding:8px 14px;border:none;border-radius:10px;background:#0f3460;color:#fff;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:5px;font-size:0.82rem}
    .btn-claim:hover{background:#1a4a7a;box-shadow:0 4px 12px rgba(15,52,96,0.25)}
    .btn-claim.pending{background:#fbbf24;color:#78350f;cursor:not-allowed}
    .btn-claim.pending:hover{background:#fbbf24;box-shadow:none}
    .btn-claim:disabled{opacity:0.6;cursor:wait}
    .btn-status-change{flex:1;padding:8px 14px;border:none;border-radius:10px;background:#6366f1;color:#fff;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:5px;font-size:0.82rem}
    .btn-status-change:hover{background:#4f46e5;box-shadow:0 4px 12px rgba(99,102,241,0.3)}
    .btn-erp{flex:1;padding:8px 14px;border:none;border-radius:10px;background:#7c3aed;color:#fff;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:5px;font-size:0.82rem}
    .btn-erp:hover{background:#6d28d9;box-shadow:0 4px 12px rgba(124,58,237,0.3)}
    .btn-archive-sm{padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;color:#ef4444;cursor:pointer;transition:all 0.2s;font-size:0.82rem}
    .btn-archive-sm:hover{border-color:#fca5a5;background:#fef2f2}
    .assigned-badge{background:#ecfdf5;color:#059669;padding:6px 12px;border-radius:8px;text-align:center;margin-bottom:8px;font-size:0.78rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:5px;border:1px solid #a7f3d0}

    .p-modal{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.5);display:none;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(6px)}
    .p-modal.show{display:flex;animation:pfadeIn 0.25s ease}
    .p-modal-box{background:#fff;border-radius:20px;max-width:480px;width:95%;overflow:hidden;animation:pscaleIn 0.3s ease;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,0.15)}
    .p-modal-header{padding:22px 26px;color:#fff}
    .p-modal-header h3{margin:0;font-size:1.1rem;font-weight:700}
    .p-modal-body{padding:24px 26px;overflow-y:auto;flex:1}
    .p-modal-footer{padding:16px 26px;background:#f9fafb;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid #f3f4f6}
    .btn-modal-cancel{background:#f3f4f6;border:1px solid #e5e7eb;padding:9px 20px;border-radius:10px;cursor:pointer;font-weight:600;color:#6b7280;transition:all 0.2s}
    .btn-modal-cancel:hover{background:#e5e7eb}
    .btn-modal-submit{background:#0f3460;color:#fff;border:none;padding:9px 20px;border-radius:10px;cursor:pointer;font-weight:600;transition:all 0.2s}
    .btn-modal-submit:hover{background:#1a4a7a}
    .btn-modal-submit:disabled{opacity:0.6;cursor:wait}
    @keyframes pfadeIn{from{opacity:0}to{opacity:1}}
    @keyframes pscaleIn{from{transform:scale(0.95);opacity:0}to{transform:scale(1);opacity:1}}

    #detailModal .p-modal-box{max-width:600px}
    .detail-loading{text-align:center;padding:40px;color:#9ca3af}
    .detail-loading i{font-size:2rem;margin-bottom:10px;display:block;color:#6366f1}
    .detail-profile{display:flex;align-items:center;gap:16px;margin-bottom:20px}
    .detail-avatar{width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;flex-shrink:0;overflow:hidden}
    .detail-avatar img{width:100%;height:100%;border-radius:16px;object-fit:cover}
    .detail-name{font-size:1.15rem;font-weight:800;color:#1f2937;margin:0 0 3px;letter-spacing:-0.02em}
    .detail-vacancy{color:#8b95a5;font-size:0.85rem}
    .detail-status-badge{display:inline-block;padding:4px 12px;border-radius:16px;color:#fff;font-size:0.75rem;font-weight:600;margin-top:5px}
    .detail-section{margin-bottom:18px}
    .detail-section-title{font-size:0.75rem;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f3f4f6}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
    .detail-item{background:#f9fafb;padding:8px 12px;border-radius:8px}
    .detail-item label{display:block;font-size:0.7rem;color:#9ca3af;margin-bottom:2px;text-transform:uppercase;letter-spacing:0.3px}
    .detail-item span{font-size:0.85rem;color:#1f2937;font-weight:600}
    .detail-docs{display:flex;flex-wrap:wrap;gap:5px}
    .detail-doc-badge{background:#eef2ff;color:#4f46e5;padding:4px 10px;border-radius:6px;font-size:0.75rem;font-weight:600;border:1px solid #e0e7ff}

    .new-alert-box{background:#fff;border-radius:16px;padding:18px 22px;margin-bottom:20px;border:1px solid #a7f3d0;box-shadow:0 2px 8px rgba(16,185,129,0.08);animation:slideDown 0.4s ease}
    @keyframes slideDown{from{opacity:0;transform:translateY(-15px)}to{opacity:1;transform:translateY(0)}}
    @keyframes bellRing{0%,100%{transform:rotate(0)}10%,30%{transform:rotate(10deg)}20%,40%{transform:rotate(-10deg)}50%{transform:rotate(0)}}
    /* Card Action Popup Modal */
    .card-action-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.6);display:none;align-items:center;justify-content:center;z-index:99999;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
    .card-action-overlay.show{display:flex;animation:cardActionFadeIn 0.3s ease}
    .card-action-popup{background:#fff;border-radius:24px;width:92%;max-width:400px;overflow:hidden;animation:cardActionPopIn 0.4s cubic-bezier(0.34,1.56,0.64,1);box-shadow:0 25px 60px rgba(0,0,0,0.3)}
    .card-action-popup-header{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:24px;display:flex;align-items:center;gap:16px;position:relative;overflow:hidden}
    .card-action-popup-header::before{content:'';position:absolute;top:-50%;right:-50%;width:200%;height:200%;background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 60%)}
    .card-action-popup-avatar{width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0;border:2px solid rgba(255,255,255,0.3);position:relative;overflow:hidden;z-index:1}
    .card-action-popup-avatar img{width:100%;height:100%;object-fit:cover;border-radius:12px}
    .card-action-popup-info{position:relative;z-index:1;flex:1;min-width:0}
    .card-action-popup-info h4{color:#fff;font-size:1.1rem;font-weight:700;margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .card-action-popup-info p{color:rgba(255,255,255,0.75);font-size:0.85rem;margin:0}
    .card-action-popup-body{padding:20px 24px 24px}
    .card-action-popup-body .action-label{font-size:0.75rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px}
    .card-action-btn{width:100%;padding:14px 18px;border:2px solid #e2e8f0;border-radius:14px;background:#fff;cursor:pointer;display:flex;align-items:center;gap:14px;margin-bottom:10px;transition:all 0.25s ease;position:relative;overflow:hidden}
    .card-action-btn::after{position:absolute;right:16px;font-family:'Font Awesome 5 Free';font-weight:900;content:'\f054';font-size:0.75rem;color:#cbd5e1;transition:all 0.25s ease}
    .card-action-btn:hover{transform:translateX(4px);box-shadow:0 4px 12px rgba(59,130,246,0.15)}
    .card-action-btn:hover::after{color:#3b82f6}
    .card-action-btn:active{transform:translateX(2px) scale(0.98)}
    .card-action-btn .action-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .card-action-btn .action-text h5{font-size:0.9rem;font-weight:600;color:#1e293b;margin:0 0 2px}
    .card-action-btn .action-text p{font-size:0.78rem;color:#94a3b8;margin:0}
    .card-action-btn.btn-action-view .action-icon{background:#eef2ff;color:#6366f1}
    .card-action-btn.btn-action-view:hover{border-color:#6366f1;background:#eef2ff}
    .card-action-btn.btn-action-status .action-icon{background:#f0fdf4;color:#16a34a}
    .card-action-btn.btn-action-status:hover{border-color:#16a34a;background:#f0fdf4}
    .card-action-btn.btn-action-archive .action-icon{background:#fff5f5;color:#ef4444}
    .card-action-btn.btn-action-archive:hover{border-color:#ef4444;background:#fff5f5}
    .card-action-close{width:100%;padding:14px;border:none;border-top:1px solid #f1f5f9;background:#f8fafc;color:#64748b;font-size:0.9rem;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:6px;border-radius:0 0 24px 24px}
    .card-action-close:hover{background:#f1f5f9;color:#334155}
    @keyframes cardActionFadeIn{from{opacity:0}to{opacity:1}}
    @keyframes cardActionPopIn{from{transform:scale(0.85) translateY(20px);opacity:0}to{transform:scale(1) translateY(0);opacity:1}}
    @keyframes cardActionPopOut{from{transform:scale(1);opacity:1}to{transform:scale(0.85) translateY(20px);opacity:0}}
    @keyframes cardActionFadeOut{from{opacity:1}to{opacity:0}}

    /* Pipeline selalu horizontal di semua ukuran layar - scroll kiri-kanan */
    @media(max-width:768px){.detail-grid{grid-template-columns:1fr}#detailModal .p-modal-box{max-width:95%}.view-tabs{width:100%;overflow-x:auto;flex-wrap:nowrap}}
</style>

<div class="pipeline-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start">
        <div>
            <h2><i class="fas fa-columns me-2"></i><?= t('pipeline.title') ?></h2>
            <small><?= getCurrentLanguage() === 'en' ? 'Click a card to view details, claim and manage applications' : 'Klik kartu untuk lihat detail, ambil dan kelola lamaran pelamar' ?></small>
        </div>
        <a href="<?= url('/crewing/pipeline/export-pdf') ?>" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:10px;color:#fff;text-decoration:none;font-weight:600;font-size:0.82rem;backdrop-filter:blur(4px);transition:all 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<!-- View Tabs -->
<div class="view-tabs">
    <a href="<?= url('/crewing/pipeline?view=available') ?>" class="view-tab <?= $currentView == 'available' ? 'active' : '' ?>">
        <i class="fas fa-inbox"></i><?= getCurrentLanguage() === 'en' ? 'Available' : 'Tersedia' ?>
    </a>
    <a href="<?= url('/crewing/pipeline?view=my') ?>" class="view-tab <?= $currentView == 'my' ? 'active' : '' ?>">
        <i class="fas fa-user-check"></i><?= getCurrentLanguage() === 'en' ? 'My Applications' : 'Milik Saya' ?>
    </a>
    <button class="view-tab" id="archivedTab" onclick="loadArchivedView()">
        <i class="fas fa-archive"></i><?= getCurrentLanguage() === 'en' ? 'Archive' : 'Arsip' ?>
    </button>
</div>

<!-- New Candidate Alerts - Centered Modal Popup -->
<?php $newCandidateAlerts = $newCandidateAlerts ?? []; if (!empty($newCandidateAlerts)): ?>
<div id="alertOverlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:99999;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.3s ease;">
    <div id="newCandidateAlert" style="background:#fff;border-radius:20px;padding:24px 28px;max-width:520px;width:90%;max-height:80vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:slideUp 0.4s ease;position:relative;">
        <button onclick="document.getElementById('alertOverlay').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.4rem;color:#9ca3af;cursor:pointer;padding:4px 8px;border-radius:8px;transition:all 0.2s" onmouseover="this.style.color='#ef4444';this.style.background='#fef2f2'" onmouseout="this.style.color='#9ca3af';this.style.background='none'">&times;</button>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;"><i class="fas fa-bell" style="color:#fff;font-size:1.2rem;animation:bellRing 1s ease infinite"></i></div>
            <div>
                <h4 style="margin:0;color:#065f46;font-weight:700;font-size:1.05rem;">Kandidat Baru Memilih Anda!</h4>
                <small style="color:#6b7280"><?= count($newCandidateAlerts) ?> kandidat baru</small>
            </div>
        </div>
        <?php foreach ($newCandidateAlerts as $alert): $ai = strtoupper(substr($alert['applicant_name'],0,1)); $np = explode(' ',$alert['applicant_name']); if(isset($np[1])) $ai .= strtoupper(substr($np[1],0,1)); ?>
        <div class="pending-item" id="alertItem-<?= $alert['id'] ?>" style="border-left:3px solid #10b981;display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#f9fafb;border-radius:10px;margin-bottom:8px;">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.82rem;overflow:hidden">
                    <?php if(!empty($alert['applicant_avatar'])): ?><img src="<?= url('/') ?><?= htmlspecialchars($alert['applicant_avatar']) ?>" style="width:100%;height:100%;object-fit:cover"><?php else: ?><?= $ai ?><?php endif; ?>
                </div>
                <div><strong style="font-size:0.88rem;color:#1e3a5f"><?= htmlspecialchars($alert['applicant_name']) ?></strong><small style="display:block;color:#6b7280"><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($alert['vacancy_title'] ?? 'Manual Entry') ?></small></div>
            </div>
            <div style="display:flex;gap:6px">
                <button onclick="showDetail(<?= $alert['id'] ?>)" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:5px 10px;border-radius:7px;font-size:0.75rem;font-weight:600;border:none;cursor:pointer"><i class="fas fa-eye"></i></button>
                <button onclick="dismissAlert(<?= $alert['id'] ?>)" style="background:#f3f4f6;border:1px solid #e5e7eb;padding:5px 8px;border-radius:7px;cursor:pointer;font-size:0.75rem;color:#6b7280"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <?php endforeach; ?>
        <div style="display:flex;gap:10px;margin-top:16px;justify-content:flex-end;">
            <button onclick="dismissAllAlerts()" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;font-weight:600;font-size:0.85rem;transition:all 0.2s"><i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca</button>
            <button onclick="document.getElementById('alertOverlay').style.display='none'" style="background:#f3f4f6;border:1px solid #e5e7eb;padding:8px 18px;border-radius:10px;cursor:pointer;font-weight:600;font-size:0.85rem;color:#374151">Tutup</button>
        </div>
    </div>
</div>
<style>
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes slideUp{from{opacity:0;transform:translateY(30px) scale(0.95)}to{opacity:1;transform:translateY(0) scale(1)}}
</style>
<?php endif; ?>

<!-- Pending Requests Alert - Centered Modal Popup -->
<?php if (!empty($myPendingRequests)): ?>
<div id="pendingOverlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:99998;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.3s ease;">
    <div style="background:#fff;border-radius:20px;padding:24px 28px;max-width:520px;width:90%;max-height:80vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:slideUp 0.4s ease;position:relative;">
        <button onclick="document.getElementById('pendingOverlay').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.4rem;color:#9ca3af;cursor:pointer;padding:4px 8px;border-radius:8px;transition:all 0.2s" onmouseover="this.style.color='#ef4444';this.style.background='#fef2f2'" onmouseout="this.style.color='#9ca3af';this.style.background='none'">&times;</button>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;"><i class="fas fa-hourglass-half" style="color:#fff;font-size:1.2rem"></i></div>
            <div>
                <h4 style="margin:0;color:#92400e;font-weight:700;font-size:1.05rem;"><?= getCurrentLanguage() === 'en' ? 'Your Pending Requests' : 'Request Pending Anda' ?></h4>
                <small style="color:#92400e"><?= getCurrentLanguage() === 'en' ? 'Awaiting Master Admin approval' : 'Menunggu approval dari Master Admin' ?></small>
            </div>
        </div>
        <?php foreach ($myPendingRequests as $req): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:10px;margin-bottom:8px;">
            <div><strong style="font-size:0.88rem;color:#1e3a5f"><?= htmlspecialchars($req['applicant_name']) ?></strong><small style="display:block;color:#6b7280"><?= htmlspecialchars($req['vacancy_title'] ?? 'N/A') ?></small></div>
            <span style="background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:6px;font-size:0.78rem;font-weight:600"><i class="fas fa-clock me-1"></i>Pending</span>
        </div>
        <?php endforeach; ?>
        <div style="display:flex;justify-content:flex-end;margin-top:16px;">
            <button onclick="document.getElementById('pendingOverlay').style.display='none'" style="background:#f3f4f6;border:1px solid #e5e7eb;padding:8px 18px;border-radius:10px;cursor:pointer;font-weight:600;font-size:0.85rem;color:#374151">Tutup</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Pipeline Board: using table layout for guaranteed horizontal columns -->
<div style="width:100%;overflow-x:auto;overflow-y:visible;padding-bottom:20px;">
<table class="pl-kanban" id="pipelineBoard" style="border-collapse:separate;border-spacing:16px 0;min-width:max-content;table-layout:fixed;"><tr style="vertical-align:top;">
<?php foreach ($statuses as $status): ?>
    <td class="pl-col" data-status-id="<?= $status['id'] ?>" style="width:270px;min-width:270px;max-width:270px;vertical-align:top;padding:0;background:#fff;border-radius:16px;border:1px solid #eef0f4;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div class="column-header" style="background:<?= $status['color'] ?? '#6c757d' ?>">
            <span><?= htmlspecialchars($status['name']) ?></span>
            <span class="count" data-count-for="<?= $status['id'] ?>"><?= count($pipeline[$status['id']] ?? []) ?></span>
        </div>
        <div class="column-body" id="columnBody-<?= $status['id'] ?>">
<?php if (empty($pipeline[$status['id']])): ?>
            <div class="empty-column"><i class="fas fa-inbox"></i><small><?= getCurrentLanguage() === 'en' ? 'No applications' : 'Belum ada lamaran' ?></small></div>
<?php else: foreach ($pipeline[$status['id']] as $app):
    $nameParts = explode(' ', $app['applicant_name']);
    $initials = strtoupper(substr($nameParts[0],0,1));
    if(isset($nameParts[1])) $initials .= strtoupper(substr($nameParts[1],0,1));
?>
            <div class="app-card" id="appCard-<?= $app['id'] ?>" data-app-id="<?= $app['id'] ?>">
                <div onclick="<?= $currentView == 'my' ? 'openCardActionPopup(' . $app['id'] . ', \'' . htmlspecialchars($app['applicant_name'], ENT_QUOTES) . '\', \'' . htmlspecialchars($app['vacancy_title'] ?? 'N/A', ENT_QUOTES) . '\', \'' . htmlspecialchars($app['applicant_avatar'] ?? '', ENT_QUOTES) . '\', ' . $app['status_id'] . ', ' . (empty($app['sent_to_erp_at']) && $app['status_id'] == 6 ? '1' : '0') . ')' : 'showDetail(' . $app['id'] . ')' ?>">
                    <div class="card-profile">
                        <div class="card-avatar"><?php if(!empty($app['applicant_avatar'])): ?><img src="<?= url('/') ?><?= htmlspecialchars($app['applicant_avatar']) ?>" alt=""><?php else: ?><?= $initials ?><?php endif; ?></div>
                        <div class="card-info"><h4><?= htmlspecialchars($app['applicant_name']) ?></h4><div class="vacancy"><i class="fas fa-briefcase"></i><?= htmlspecialchars($app['vacancy_title'] ?? 'N/A') ?></div></div>
                    </div>
                    <div class="card-meta"><i class="fas fa-envelope"></i><?= htmlspecialchars($app['applicant_email'] ?? '') ?></div>
<?php if (!empty($app['medical_email_sent_at'])): ?>
                    <div class="badge-sm badge-mcu"><i class="fas fa-heartbeat"></i>MCU Email Terkirim <span style="opacity:0.7"><?= date('d/m/Y', strtotime($app['medical_email_sent_at'])) ?></span></div>
<?php endif; ?>
<?php if (!empty($app['sent_to_erp_at'])):
    $crewId = $app['erp_crew_id'];
    $progress = ($erpProgress ?? [])[$crewId] ?? null;
    $passedCount = $progress ? intval($progress['passed_count']) : 0;
    $rejectedCount = $progress ? intval($progress['rejected_count']) : 0;
    $totalCount = $progress ? intval($progress['total_count']) : 6;
    $pct = $totalCount > 0 ? round(($passedCount/$totalCount)*100) : 0;
    $isRejected = $rejectedCount > 0;
    $checkItems = [['key'=>'document_check','label'=>'Doc Check'],['key'=>'owner_interview','label'=>'Interview'],['key'=>'pengantar_mcu','label'=>'MCU'],['key'=>'agreement_kontrak','label'=>'Kontrak'],['key'=>'admin_charge','label'=>'Admin'],['key'=>'ok_to_board','label'=>'OK Board']];
?>
                    <div class="erp-checklist">
                        <div class="erp-checklist-header"><span><i class="fas fa-tasks me-1"></i>Admin Checklist</span><span><?= $passedCount ?>/<?= $totalCount ?></span></div>
                        <div class="erp-progress-bar"><div class="erp-progress-fill" style="width:<?= $pct ?>%;background:<?= $isRejected ? '#ef4444' : ($pct==100 ? '#22c55e' : '#3b82f6') ?>"></div></div>
                        <div style="display:flex;flex-wrap:wrap;gap:3px">
<?php foreach($checkItems as $ci): $val=$progress?intval($progress[$ci['key']]??0):0; ?>
                            <div class="erp-check-item" style="background:<?= $val==1?'rgba(34,197,94,0.12)':($val==2?'rgba(239,68,68,0.12)':'rgba(148,163,184,0.08)') ?>;color:<?= $val==1?'#166534':($val==2?'#991b1b':'#64748b') ?>">
                                <span style="font-weight:800"><?= $val==1?'&#x2714;':($val==2?'&#x2718;':'&#x25CB;') ?></span><?= $ci['label'] ?>
                            </div>
<?php endforeach; ?>
                        </div>
<?php if($isRejected): ?><small style="color:#ef4444;font-weight:600;margin-top:4px;display:block"><i class="fas fa-times-circle"></i> Rejected</small>
<?php elseif($pct==100): ?><small style="color:#22c55e;font-weight:600;margin-top:4px;display:block"><i class="fas fa-check-circle"></i> Siap Deploy</small>
<?php else: ?><small style="color:#1e40af;margin-top:4px;display:block">ID: <?= $crewId ?> • Proses di ERP</small><?php endif; ?>
                    </div>
<?php elseif(!empty($app['email_sent_count']) && $app['email_sent_count'] > 0): ?>
                    <div class="badge-sm badge-email-sent"><i class="fas fa-envelope-open-text"></i>Email Terkirim (<?= $app['email_sent_count'] ?>) â€¢ <?= date('d/m/Y', strtotime($app['last_email_sent_at'])) ?></div>
<?php endif; ?>
                </div>
                <div class="card-actions">
<?php if ($currentView == 'available'): ?>
    <?php if (!empty($app['my_pending_request'])): ?>
                    <button class="btn-claim pending" disabled style="flex:1"><i class="fas fa-hourglass-half"></i>Request Pending...</button>
    <?php else: ?>
                    <button class="btn-detail-sm" onclick="event.stopPropagation();showDetail(<?= $app['id'] ?>)"><i class="fas fa-eye"></i></button>
                    <button class="btn-claim" id="claimBtn-<?= $app['id'] ?>" onclick="event.stopPropagation();openClaimModal(<?= $app['id'] ?>,'<?= htmlspecialchars($app['applicant_name'],ENT_QUOTES) ?>','<?= htmlspecialchars($app['vacancy_title']??'',ENT_QUOTES) ?>')"><i class="fas fa-hand-paper"></i><?= getCurrentLanguage()==='en'?'Request Claim':'Request Ambil' ?></button>
                    <button class="btn-archive-sm" onclick="event.stopPropagation();archiveApplication(<?= $app['id'] ?>)" title="Arsipkan"><i class="fas fa-archive"></i></button>
    <?php endif; ?>
<?php else: ?>
                    <div style="width:100%">
                        <div class="assigned-badge"><i class="fas fa-check-circle"></i><?= getCurrentLanguage()==='en'?'Handled by You':'Ditangani Anda' ?></div>
                    </div>
<?php endif; ?>
                </div>
           <?php endforeach; endif; ?>
        </div>
    </td>
<?php endforeach; ?>
</tr></table>
</div><!-- /pipeline scroll wrapper -->

<!-- Archive view container (filled by JS) -->
<div id="archiveContainer" style="display:none;width:100%;margin-top:8px;"></div>

<?php include __DIR__ . '/_modals_and_js.php'; ?>
<?php include __DIR__ . '/erp_modal.php'; ?>

<!-- Table layout handles horizontal columns natively, no JS needed -->



