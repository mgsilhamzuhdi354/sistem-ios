<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Detail - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
</head>
<body class="applicant-body">
    <!-- Sidebar -->
    <aside class="applicant-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Indo Ocean</span>
            </a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/applicant/dashboard') ?>" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/jobs') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/applicant/applications') ?>" class="nav-link active"><i class="fas fa-file-alt"></i><span>My Applications</span></a></li>
                <li><a href="<?= url('/applicant/documents') ?>" class="nav-link"><i class="fas fa-folder"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/applicant/interview') ?>" class="nav-link"><i class="fas fa-video"></i><span>Interview</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <main class="applicant-main">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <a href="<?= url('/applicant/applications') ?>"><i class="fas fa-arrow-left"></i> Back to Applications</a>
        </nav>

        <div class="detail-layout">
            <!-- Main Info -->
            <div class="detail-main">
                <div class="card">
                    <div class="card-header">
                        <h2><?= htmlspecialchars($application['vacancy_title']) ?></h2>
                        <span class="status-badge large" style="background: <?= $application['status_color'] ?>20; color: <?= $application['status_color'] ?>">
                            <?= htmlspecialchars($application['status_name']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-building"></i>
                                <div>
                                    <span class="label">Department</span>
                                    <span class="value"><?= htmlspecialchars($application['department_name'] ?? '-') ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-ship"></i>
                                <div>
                                    <span class="label">Vessel Type</span>
                                    <span class="value"><?= htmlspecialchars($application['vessel_type'] ?? '-') ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-dollar-sign"></i>
                                <div>
                                    <span class="label">Salary Range</span>
                                    <span class="value">
                                        <?php if ($application['salary_min'] && $application['salary_max']): ?>
                                            $<?= number_format($application['salary_min']) ?> - $<?= number_format($application['salary_max']) ?>
                                        <?php else: ?>
                                            Negotiable
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <span class="label">Applied On</span>
                                    <span class="value"><?= date('d M Y, H:i', strtotime($application['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($application['cover_letter'])): ?>
                        <div class="section">
                            <h3>Cover Letter</h3>
                            <p><?= nl2br(htmlspecialchars($application['cover_letter'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Application Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($history as $item): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker" style="background: <?= $item['to_color'] ?? '#0A2463' ?>"></div>
                                    <div class="timeline-content">
                                        <strong><?= htmlspecialchars($item['to_status']) ?></strong>
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

            <!-- Sidebar -->
            <div class="detail-sidebar">
                <!-- Interview Card -->
                <?php if ($interview): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-video"></i> AI Interview</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> <?= ucfirst($interview['status']) ?></p>
                        <?php if ($interview['status'] === 'pending' || $interview['status'] === 'in_progress'): ?>
                            <a href="<?= url('/applicant/interview/start/' . $interview['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-play"></i> Start Interview
                            </a>
                        <?php elseif ($interview['status'] === 'completed'): ?>
                            <p><strong>Score:</strong> <?= $interview['total_score'] ?>/100</p>
                            <p><strong>Result:</strong> <?= ucfirst($interview['ai_recommendation']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Medical Card -->
                <?php if ($medical): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-heartbeat"></i> Medical Check-up</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Date:</strong> <?= date('d M Y', strtotime($medical['scheduled_date'])) ?></p>
                        <p><strong>Time:</strong> <?= $medical['scheduled_time'] ?></p>
                        <p><strong>Hospital:</strong> <?= htmlspecialchars($medical['hospital_name']) ?></p>
                        <?php if ($medical['result']): ?>
                            <p><strong>Result:</strong> 
                                <span class="badge badge-<?= $medical['result'] === 'fit' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($medical['result']) ?>
                                </span>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Help Card -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-question-circle"></i> Need Help?</h3>
                    </div>
                    <div class="card-body">
                        <p>Contact our recruitment team for any questions about your application.</p>
                        <a href="mailto:recruitment@indoceancrew.com" class="btn btn-outline btn-block">
                            <i class="fas fa-envelope"></i> Contact HR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
    .breadcrumb-nav { margin-bottom: 20px; }
    .breadcrumb-nav a { color: #0A2463; display: inline-flex; align-items: center; gap: 8px; }
    .detail-layout { display: grid; grid-template-columns: 1fr 350px; gap: 25px; }
    .detail-main { display: flex; flex-direction: column; gap: 25px; }
    .detail-sidebar { display: flex; flex-direction: column; gap: 20px; }
    .status-badge.large { padding: 8px 16px; font-size: 14px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
    .info-item { display: flex; gap: 15px; }
    .info-item i { width: 40px; height: 40px; background: #f0f4ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #0A2463; }
    .info-item .label { display: block; font-size: 12px; color: #6c757d; }
    .info-item .value { font-weight: 500; color: #1a1a2e; }
    .section { margin-top: 25px; padding-top: 25px; border-top: 1px solid #f0f0f0; }
    .section h3 { font-size: 16px; margin-bottom: 15px; color: #1a1a2e; }
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before { content: ''; position: absolute; left: 8px; top: 0; bottom: 0; width: 2px; background: #e0e0e0; }
    .timeline-item { position: relative; padding-bottom: 25px; }
    .timeline-marker { width: 18px; height: 18px; border-radius: 50%; position: absolute; left: -30px; top: 0; border: 3px solid white; box-shadow: 0 0 0 2px #e0e0e0; }
    .timeline-content strong { display: block; margin-bottom: 5px; }
    .timeline-content p { font-size: 14px; color: #666; margin-bottom: 5px; }
    .timeline-content .time { font-size: 12px; color: #999; }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    @media (max-width: 992px) {
        .detail-layout { grid-template-columns: 1fr; }
    }
    </style>
</body>
</html>
