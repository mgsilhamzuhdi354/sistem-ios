<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - <?= htmlspecialchars($applicant['full_name']) ?> | <?= isMasterAdmin() ? 'Master Admin' : 'Admin' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <?php if (isMasterAdmin()): ?>
    <style>
        .admin-sidebar { background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%); }
        .nav-link.active { background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%); }
        .nav-link:hover { background: rgba(220, 38, 38, 0.2); }
    </style>
    <?php endif; ?>
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <?php if (isMasterAdmin()): ?>
            <a href="<?= url('/master-admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Master Admin</span></a>
            <?php else: ?>
            <a href="<?= url('/admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Recruitment</span></a>
            <?php endif; ?>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <?php if (isMasterAdmin()): ?>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/dashboard') ?>" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/users') ?>" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/vacancies') ?>" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Job Vacancies</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/pipeline') ?>" class="nav-link">
                        <i class="fas fa-stream"></i>
                        <span>Pipeline</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/requests') ?>" class="nav-link">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/interviews') ?>" class="nav-link">
                        <i class="fas fa-robot"></i>
                        <span>AI Interview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/documents') ?>" class="nav-link active">
                        <i class="fas fa-file-alt"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/admin/medical') ?>" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/reports') ?>" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= url('/master-admin/settings') ?>" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <?php else: ?>
                <li><a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i class="fas fa-stream"></i><span>Pipeline</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
                <li><a href="<?= url('/admin/documents') ?>" class="nav-link active"><i class="fas fa-file-alt"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/admin/medical') ?>" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div class="header-actions">
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                        <span><?= $_SESSION['user_name'] ?? 'Admin' ?></span>
                    </button>
                </div>
            </div>
        </header>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="page-header">
                <div>
                    <a href="<?= url('/admin/documents') ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to All Applicants</a>
                    <h1><i class="fas fa-folder-open"></i> Documents: <?= htmlspecialchars($applicant['full_name']) ?></h1>
                </div>
            </div>

            <!-- Applicant Info Card -->
            <div class="card mb-20">
                <div class="card-body">
                    <div class="applicant-profile">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="" class="profile-avatar">
                        <div class="profile-info">
                            <h3><?= htmlspecialchars($applicant['full_name']) ?></h3>
                            <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($applicant['email']) ?></p>
                            <p><i class="fas fa-phone"></i> <?= htmlspecialchars($applicant['phone'] ?? '-') ?></p>
                        </div>
                        <div class="profile-stats">
                            <div class="stat">
                                <span class="num pending"><?= $stats['pending'] ?></span>
                                <span class="label">Pending</span>
                            </div>
                            <div class="stat">
                                <span class="num verified"><?= $stats['verified'] ?></span>
                                <span class="label">Verified</span>
                            </div>
                            <div class="stat">
                                <span class="num rejected"><?= $stats['rejected'] ?></span>
                                <span class="label">Rejected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if ($stats['pending'] > 0): ?>
            <div class="card mb-20 action-card">
                <div class="card-body">
                    <div class="quick-actions">
                        <span class="action-label"><i class="fas fa-bolt"></i> Quick Actions:</span>
                        <form action="<?= url('/admin/documents/bulk-verify/' . $applicant['id']) ?>" method="POST" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Verify all pending documents?')">
                                <i class="fas fa-check-double"></i> Verify All Pending (<?= $stats['pending'] ?>)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Documents List -->
            <div class="documents-container">
                <?php 
                // Group documents by type
                $groupedDocs = [];
                foreach ($documents as $doc) {
                    $category = $doc['category'] ?? 'Other';
                    if (!isset($groupedDocs[$category])) {
                        $groupedDocs[$category] = [];
                    }
                    $groupedDocs[$category][] = $doc;
                }
                ?>
                
                <?php foreach ($groupedDocs as $category => $docs): ?>
                <div class="doc-category">
                    <h3 class="category-title">
                        <i class="fas fa-folder"></i> <?= htmlspecialchars($category) ?>
                        <span class="badge"><?= count($docs) ?></span>
                    </h3>
                    
                    <div class="doc-list">
                        <?php foreach ($docs as $doc): ?>
                        <div class="doc-item status-<?= $doc['verification_status'] ?>">
                            <div class="doc-icon">
                                <?php 
                                $ext = strtolower(pathinfo($doc['original_name'], PATHINFO_EXTENSION));
                                $icon = 'fa-file';
                                if (in_array($ext, ['pdf'])) $icon = 'fa-file-pdf';
                                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fa-file-image';
                                elseif (in_array($ext, ['doc', 'docx'])) $icon = 'fa-file-word';
                                ?>
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            
                            <div class="doc-details">
                                <h4><?= htmlspecialchars($doc['type_name']) ?></h4>
                                <p class="filename"><?= htmlspecialchars($doc['original_name']) ?></p>
                                <div class="doc-meta">
                                    <?php if (!empty($doc['document_number'])): ?>
                                    <span><i class="fas fa-hashtag"></i> <?= htmlspecialchars($doc['document_number']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($doc['expiry_date'])): ?>
                                    <span><i class="fas fa-calendar"></i> Expires: <?= date('d M Y', strtotime($doc['expiry_date'])) ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-clock"></i> Uploaded: <?= date('d M Y', strtotime($doc['created_at'])) ?></span>
                                </div>
                            </div>
                            
                            <div class="doc-status">
                                <?php if ($doc['verification_status'] === 'pending'): ?>
                                    <span class="status-badge pending"><i class="fas fa-clock"></i> Pending</span>
                                <?php elseif ($doc['verification_status'] === 'verified'): ?>
                                    <span class="status-badge verified"><i class="fas fa-check"></i> Verified</span>
                                <?php else: ?>
                                    <span class="status-badge rejected"><i class="fas fa-times"></i> Rejected</span>
                                    <?php if (!empty($doc['rejection_reason'])): ?>
                                    <p class="rejection-reason"><?= htmlspecialchars($doc['rejection_reason']) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="doc-actions">
                                <a href="<?= url('/uploads/documents/' . $applicant['id'] . '/' . $doc['file_name']) ?>" target="_blank" class="btn btn-sm btn-outline" title="View Document">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if ($doc['verification_status'] === 'pending'): ?>
                                <form action="<?= url('/admin/documents/verify/' . $doc['id']) ?>" method="POST" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="verify">
                                    <input type="hidden" name="applicant_id" value="<?= $applicant['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                
                                <button type="button" class="btn btn-sm btn-danger" title="Reject" onclick="showRejectModal(<?= $doc['id'] ?>, '<?= htmlspecialchars($doc['type_name']) ?>')">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($documents)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Documents</h3>
                    <p>This applicant hasn't uploaded any documents yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-times-circle"></i> Reject Document</h3>
                <button type="button" class="modal-close" onclick="hideRejectModal()">&times;</button>
            </div>
            <form action="" method="POST" id="rejectForm">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="applicant_id" value="<?= $applicant['id'] ?>">
                <div class="modal-body">
                    <p>Rejecting: <strong id="docTypeName"></strong></p>
                    <div class="form-group">
                        <label>Reason for Rejection *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Explain why this document is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="hideRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Document</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    .back-link { color: #0A2463; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; }
    .mb-20 { margin-bottom: 20px; }
    
    .applicant-profile {
        display: flex;
        align-items: center;
        gap: 25px;
    }
    
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e0e0e0;
    }
    
    .profile-info h3 {
        font-size: 20px;
        color: #1a1a2e;
        margin: 0 0 10px 0;
    }
    
    .profile-info p {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #666;
    }
    
    .profile-info p i {
        width: 20px;
        color: #0A2463;
    }
    
    .profile-stats {
        margin-left: auto;
        display: flex;
        gap: 30px;
    }
    
    .profile-stats .stat {
        text-align: center;
    }
    
    .profile-stats .num {
        display: block;
        font-size: 28px;
        font-weight: 700;
    }
    
    .profile-stats .num.pending { color: #ffc107; }
    .profile-stats .num.verified { color: #28a745; }
    .profile-stats .num.rejected { color: #dc3545; }
    
    .profile-stats .label {
        font-size: 12px;
        color: #6c757d;
    }
    
    .action-card .card-body {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    
    .quick-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .action-label {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    
    .doc-category {
        margin-bottom: 30px;
    }
    
    .category-title {
        font-size: 16px;
        color: #0A2463;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .category-title .badge {
        background: #0A2463;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
    }
    
    .doc-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .doc-item {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s;
    }
    
    .doc-item:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .doc-item.status-pending {
        border-left: 4px solid #ffc107;
    }
    
    .doc-item.status-verified {
        border-left: 4px solid #28a745;
        background: #f8fff9;
    }
    
    .doc-item.status-rejected {
        border-left: 4px solid #dc3545;
        background: #fff8f8;
    }
    
    .doc-icon {
        width: 50px;
        height: 50px;
        background: #f0f0f0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #dc3545;
    }
    
    .doc-item.status-verified .doc-icon {
        background: #e8f5e9;
        color: #28a745;
    }
    
    .doc-details {
        flex: 1;
    }
    
    .doc-details h4 {
        font-size: 15px;
        color: #1a1a2e;
        margin: 0 0 5px 0;
    }
    
    .doc-details .filename {
        font-size: 13px;
        color: #666;
        margin: 0 0 8px 0;
    }
    
    .doc-meta {
        display: flex;
        gap: 20px;
        font-size: 12px;
        color: #888;
    }
    
    .doc-meta span i {
        margin-right: 5px;
    }
    
    .doc-status {
        min-width: 100px;
        text-align: center;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.verified {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .rejection-reason {
        font-size: 11px;
        color: #dc3545;
        margin-top: 5px;
    }
    
    .doc-actions {
        display: flex;
        gap: 8px;
    }
    
    .btn-sm {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    .modal-overlay.show {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #dc3545;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    @media (max-width: 768px) {
        .applicant-profile { flex-direction: column; text-align: center; }
        .profile-stats { margin-left: 0; }
        .doc-item { flex-wrap: wrap; }
        .doc-actions { width: 100%; justify-content: flex-end; margin-top: 10px; }
    }
    </style>

    <script>
    function showRejectModal(docId, typeName) {
        document.getElementById('rejectForm').action = '<?= url('/admin/documents/verify/') ?>' + docId;
        document.getElementById('docTypeName').textContent = typeName;
        document.getElementById('rejectModal').classList.add('show');
    }
    
    function hideRejectModal() {
        document.getElementById('rejectModal').classList.remove('show');
    }
    
    // Close modal on outside click
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) hideRejectModal();
    });
    </script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
