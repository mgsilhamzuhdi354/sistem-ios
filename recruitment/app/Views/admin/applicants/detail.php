<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Detail - Admin | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Recruitment</span></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link active"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i class="fas fa-stream"></i><span>Pipeline</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
                <li><a href="<?= url('/admin/documents') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/admin/medical') ?>" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
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
                    <a href="<?= url('/admin/applicants') ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Applicants</a>
                    <h1><?= htmlspecialchars($application['full_name'] ?? 'Applicant') ?></h1>
                </div>
                <div class="header-actions-right">
                    <span class="badge large" style="background: <?= $application['status_color'] ?? '#6c757d' ?>20; color: <?= $application['status_color'] ?? '#6c757d' ?>">
                        <?= htmlspecialchars($application['status_name'] ?? '-') ?>
                    </span>
                </div>
            </div>

            <div class="detail-grid">
                <!-- Main Content -->
                <div class="detail-main">
                    <!-- Application Info -->
                    <div class="card">
                        <div class="card-header"><h3><i class="fas fa-file-alt"></i> Application Details</h3></div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Position Applied</label>
                                    <strong><?= htmlspecialchars($application['vacancy_title'] ?? '-') ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Department</label>
                                    <strong><?= htmlspecialchars($application['department_name'] ?? '-') ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Applied On</label>
                                    <strong><?= date('d M Y, H:i', strtotime($application['submitted_at'] ?? $application['created_at'])) ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Expected Salary</label>
                                    <strong><?= $application['expected_salary'] ? '$' . number_format($application['expected_salary']) : '-' ?></strong>
                                </div>
                            </div>
                            
                            <?php if (!empty($application['cover_letter'])): ?>
                            <div class="section">
                                <h4>Cover Letter</h4>
                                <p><?= nl2br(htmlspecialchars($application['cover_letter'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div class="card">
                        <div class="card-header"><h3><i class="fas fa-user"></i> Personal Information</h3></div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Full Name</label>
                                    <strong><?= htmlspecialchars($application['full_name'] ?? '-') ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Email</label>
                                    <strong><?= htmlspecialchars($application['email'] ?? '-') ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Phone</label>
                                    <strong><?= htmlspecialchars($application['phone'] ?? '-') ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Date of Birth</label>
                                    <strong><?= !empty($profile['date_of_birth']) ? date('d M Y', strtotime($profile['date_of_birth'])) : '-' ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Nationality</label>
                                    <strong><?= htmlspecialchars($profile['nationality'] ?? '-') ?></strong>
                                </div>
                                <div class="info-item">
                                    <label>Experience</label>
                                    <strong><?= ($profile['experience_years'] ?? 0) ?> years</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="card">
                        <div class="card-header"><h3><i class="fas fa-folder"></i> Documents</h3></div>
                        <div class="card-body">
                            <?php if (empty($documents)): ?>
                                <p class="text-muted">No documents uploaded yet.</p>
                            <?php else: ?>
                                <div class="documents-list">
                                    <?php foreach ($documents as $doc): ?>
                                        <div class="doc-item">
                                            <div class="doc-info">
                                                <i class="fas fa-file-pdf"></i>
                                                <div>
                                                    <strong><?= htmlspecialchars($doc['type_name'] ?? $doc['original_name']) ?></strong>
                                                    <span class="text-muted"><?= htmlspecialchars($doc['original_name']) ?></span>
                                                </div>
                                            </div>
                                            <div class="doc-actions">
                                                <span class="badge badge-<?= $doc['verification_status'] === 'verified' ? 'success' : ($doc['verification_status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                                    <?= ucfirst($doc['verification_status']) ?>
                                                </span>
                                                <a href="<?= url('/uploads/documents/' . $application['user_id'] . '/' . $doc['file_name']) ?>" target="_blank" class="btn btn-sm btn-outline">View</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="detail-sidebar">
                    <!-- Status Change -->
                    <div class="card">
                        <div class="card-header"><h3>Change Status</h3></div>
                        <div class="card-body">
                            <form action="<?= url('/admin/applicants/status/' . $application['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <select name="status_id" class="form-control">
                                        <?php foreach ($statuses ?? [] as $status): ?>
                                            <option value="<?= $status['id'] ?>" <?= $application['status_id'] == $status['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($status['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea name="notes" class="form-control" placeholder="Add notes (optional)" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header"><h3>Quick Actions</h3></div>
                        <div class="card-body">
                            <a href="<?= url('/admin/interviews/schedule/' . $application['id']) ?>" class="btn btn-outline btn-block mb-10">
                                <i class="fas fa-video"></i> Schedule Interview
                            </a>
                            <a href="<?= url('/admin/medical/schedule/' . $application['id']) ?>" class="btn btn-outline btn-block mb-10">
                                <i class="fas fa-heartbeat"></i> Schedule Medical
                            </a>
                            <a href="mailto:<?= $application['email'] ?>" class="btn btn-outline btn-block">
                                <i class="fas fa-envelope"></i> Send Email
                            </a>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card">
                        <div class="card-header"><h3>History</h3></div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($history ?? [] as $item): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker" style="background: <?= $item['to_color'] ?? '#0A2463' ?>"></div>
                                        <div class="timeline-content">
                                            <strong><?= htmlspecialchars($item['to_status'] ?? '') ?></strong>
                                            <?php if (!empty($item['notes'])): ?>
                                                <p><?= htmlspecialchars($item['notes']) ?></p>
                                            <?php endif; ?>
                                            <span class="time"><?= date('d M Y, H:i', strtotime($item['created_at'])) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .back-link { color: #0A2463; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; }
    .header-actions-right { display: flex; align-items: center; gap: 15px; }
    .badge.large { padding: 10px 20px; font-size: 14px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 350px; gap: 25px; }
    .detail-main .card { margin-bottom: 20px; }
    .detail-sidebar .card { margin-bottom: 20px; }
    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .info-item label { display: block; font-size: 12px; color: #6c757d; margin-bottom: 5px; }
    .info-item strong { font-size: 14px; color: #1a1a2e; }
    .section { margin-top: 25px; padding-top: 25px; border-top: 1px solid #f0f0f0; }
    .section h4 { font-size: 14px; color: #333; margin-bottom: 10px; }
    .section p { color: #666; line-height: 1.7; }
    .documents-list { display: flex; flex-direction: column; gap: 15px; }
    .doc-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 8px; }
    .doc-info { display: flex; align-items: center; gap: 15px; }
    .doc-info i { font-size: 24px; color: #dc3545; }
    .doc-info strong { display: block; font-size: 14px; }
    .doc-info span { font-size: 12px; }
    .doc-actions { display: flex; align-items: center; gap: 10px; }
    .timeline { position: relative; padding-left: 25px; }
    .timeline::before { content: ''; position: absolute; left: 6px; top: 0; bottom: 0; width: 2px; background: #e0e0e0; }
    .timeline-item { position: relative; padding-bottom: 20px; }
    .timeline-marker { width: 14px; height: 14px; border-radius: 50%; position: absolute; left: -25px; top: 3px; }
    .timeline-content strong { display: block; font-size: 13px; margin-bottom: 3px; }
    .timeline-content p { font-size: 12px; color: #666; margin-bottom: 3px; }
    .timeline-content .time { font-size: 11px; color: #999; }
    .mb-10 { margin-bottom: 10px; }
    .text-muted { color: #6c757d; }
    @media (max-width: 992px) { .detail-grid { grid-template-columns: 1fr; } }
    </style>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
