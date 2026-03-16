<!-- Crewing Dashboard Modern -->
<div class="welcome-banner">
    <div class="welcome-content">
        <div class="welcome-icon">
            <i class="fas fa-anchor"></i>
        </div>
        <div class="welcome-text">
            <h1><?= t('dashboard.welcome', 'Welcome back') ?>, <?= $_SESSION['user_name'] ?? 'Crewing' ?>! 👋</h1>
            <p><?= getCurrentLanguage() === 'en' ? 'Here\'s your work overview for today' : 'Berikut ringkasan pekerjaan Anda hari ini' ?></p>
        </div>
    </div>
    <div class="welcome-date">
        <i class="fas fa-calendar-alt"></i>
        <span><?= date('l, d F Y') ?></span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-row-crewing">
    <div class="stat-card-crewing blue">
        <div class="stat-icon-wrap">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['total_assigned'] ?? 0 ?></h2>
            <span><?= getCurrentLanguage() === 'en' ? 'Active Assignments' : 'Tugas Aktif' ?></span>
        </div>
    </div>
    
    <div class="stat-card-crewing orange">
        <div class="stat-icon-wrap">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['pending_review'] ?? 0 ?></h2>
            <span><?= t('dashboard.pending_review') ?></span>
        </div>
    </div>
    
    <div class="stat-card-crewing purple">
        <div class="stat-icon-wrap">
            <i class="fas fa-comments"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['in_interview'] ?? 0 ?></h2>
            <span><?= getCurrentLanguage() === 'en' ? 'In Interview' : 'Wawancara' ?></span>
        </div>
    </div>
    
    <div class="stat-card-crewing teal">
        <div class="stat-icon-wrap">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['documents_pending'] ?? 0 ?></h2>
            <span><?= getCurrentLanguage() === 'en' ? 'Docs Pending' : 'Dok. Menunggu' ?></span>
        </div>
    </div>
    
    <div class="stat-card-crewing green">
        <div class="stat-icon-wrap">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['completed_month'] ?? 0 ?></h2>
            <span><?= getCurrentLanguage() === 'en' ? 'Completed' : 'Selesai' ?></span>
        </div>
    </div>
    
    <div class="stat-card-crewing red">
        <div class="stat-icon-wrap">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="stat-content">
            <h2><?= $stats['new_today'] ?? 0 ?></h2>
            <span><?= getCurrentLanguage() === 'en' ? 'New Today' : 'Baru Hari Ini' ?></span>
        </div>
    </div>
</div>

<!-- Quick Actions Bar -->
<div class="quick-actions-bar">
    <a href="<?= url('/crewing/applications?status=1') ?>" class="quick-action-item">
        <i class="fas fa-eye"></i>
        <span><?= getCurrentLanguage() === 'en' ? 'Review New' : 'Review Baru' ?></span>
    </a>
    <a href="<?= url('/crewing/pipeline') ?>" class="quick-action-item">
        <i class="fas fa-stream"></i>
        <span><?= t('nav.pipeline') ?></span>
    </a>
    <a href="<?= url('/crewing/team') ?>" class="quick-action-item">
        <i class="fas fa-users"></i>
        <span><?= getCurrentLanguage() === 'en' ? 'Team' : 'Tim' ?></span>
    </a>
    <a href="<?= url('/crewing/applications') ?>" class="quick-action-item">
        <i class="fas fa-list"></i>
        <span><?= getCurrentLanguage() === 'en' ? 'All Applications' : 'Semua Lamaran' ?></span>
    </a>
</div>

<div class="dashboard-grid-modern">
    <!-- Pipeline Summary -->
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon blue"><i class="fas fa-stream"></i></div>
                <h3><?= getCurrentLanguage() === 'en' ? 'My Pipeline' : 'Pipeline Saya' ?></h3>
            </div>
            <a href="<?= url('/crewing/pipeline') ?>" class="view-all-link"><?= getCurrentLanguage() === 'en' ? 'View All' : 'Lihat Semua' ?> <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-modern">
            <div class="pipeline-visual">
                <?php foreach ($pipeline as $status): ?>
                <div class="pipeline-status-item">
                    <div class="status-color-bar" style="background: <?= $status['color'] ?>"></div>
                    <div class="status-details">
                        <span class="status-name"><?= $status['name'] ?></span>
                        <span class="status-count"><?= $status['count'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Assignments -->
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon purple"><i class="fas fa-file-alt"></i></div>
                <h3><?= getCurrentLanguage() === 'en' ? 'Recent Assignments' : 'Tugas Terbaru' ?></h3>
            </div>
            <a href="<?= url('/crewing/applications') ?>" class="view-all-link"><?= getCurrentLanguage() === 'en' ? 'View All' : 'Lihat Semua' ?> <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-modern">
            <?php if (empty($recentApplications)): ?>
            <div class="empty-state-modern">
                <i class="fas fa-inbox"></i>
                <p><?= getCurrentLanguage() === 'en' ? 'No applications assigned yet' : 'Belum ada lamaran yang ditugaskan' ?></p>
            </div>
            <?php else: ?>
            <div class="assignments-list">
                <?php foreach ($recentApplications as $app): ?>
                <a href="<?= url('/crewing/applications/' . $app['id']) ?>" class="assignment-item">
                    <div class="assignment-avatar">
                        <?= strtoupper(substr($app['full_name'], 0, 2)) ?>
                    </div>
                    <div class="assignment-info">
                        <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                        <span><?= htmlspecialchars($app['vacancy_title']) ?></span>
                        <small><i class="fas fa-calendar"></i> <?= date('M d', strtotime($app['assigned_at'])) ?></small>
                    </div>
                    <span class="assignment-status" style="background: <?= $app['status_color'] ?>">
                        <?= $app['status_name'] ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pending Interviews -->
    <div class="dashboard-card-modern">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon orange"><i class="fas fa-robot"></i></div>
                <h3><?= getCurrentLanguage() === 'en' ? 'Pending Interviews' : 'Wawancara Menunggu' ?></h3>
            </div>
        </div>
        <div class="card-body-modern">
            <?php if (empty($pendingInterviews)): ?>
            <div class="empty-state-modern success">
                <i class="fas fa-check-circle"></i>
                <p><?= getCurrentLanguage() === 'en' ? 'No pending interviews' : 'Tidak ada wawancara menunggu' ?></p>
            </div>
            <?php else: ?>
            <div class="interviews-list">
                <?php foreach ($pendingInterviews as $interview): ?>
                <div class="interview-item-modern <?= $interview['days_left'] <= 2 ? 'urgent' : '' ?>">
                    <div class="interview-main">
                        <strong><?= htmlspecialchars($interview['full_name']) ?></strong>
                        <span><?= htmlspecialchars($interview['vacancy_title']) ?></span>
                    </div>
                    <div class="interview-badges">
                        <span class="days-left <?= $interview['days_left'] <= 2 ? 'danger' : 'warning' ?>">
                            <i class="fas fa-hourglass-half"></i> <?= $interview['days_left'] ?> <?= getCurrentLanguage() === 'en' ? 'days' : 'hari' ?>
                        </span>
                        <span class="interview-status"><?= ucfirst($interview['status']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($teamWorkload)): ?>
    <!-- Team Workload -->
    <div class="dashboard-card-modern team">
        <div class="card-header-modern">
            <div class="header-left">
                <div class="header-icon green"><i class="fas fa-users-cog"></i></div>
                <h3><?= t('nav.team_workload') ?></h3>
            </div>
            <a href="<?= url('/crewing/team') ?>" class="view-all-link"><?= getCurrentLanguage() === 'en' ? 'Manage' : 'Kelola' ?> <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body-modern">
            <div class="team-workload-list">
                <?php foreach ($teamWorkload as $crew): ?>
                <div class="team-member-row">
                    <div class="member-avatar-mini">
                        <?= strtoupper(substr($crew['full_name'], 0, 2)) ?>
                    </div>
                    <div class="member-details">
                        <strong><?= htmlspecialchars($crew['full_name']) ?></strong>
                        <?php 
                        $percentage = $crew['max_applications'] > 0 
                            ? min(100, ($crew['active_assignments'] / $crew['max_applications']) * 100) 
                            : 0;
                        $barClass = $percentage >= 80 ? 'danger' : ($percentage >= 50 ? 'warning' : 'success');
                        ?>
                        <div class="mini-progress">
                            <div class="mini-progress-fill <?= $barClass ?>" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </div>
                    <span class="workload-count"><?= $crew['active_assignments'] ?>/<?= $crew['max_applications'] ?: 50 ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<style>
/* Font Override */
body,*:not(i):not(.fas):not(.far):not(.fab):not(.fa){font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif!important}

/* PREMIUM BLUE BACKGROUND WITH MOTIFS */
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

/* Welcome Banner */
.welcome-banner {
    background: linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    box-shadow: 0 10px 40px rgba(15,23,42,0.3);
    position: relative;
    overflow: hidden;
}
.welcome-banner::before {
    content:'';position:absolute;top:0;right:0;width:350px;height:100%;
    background:linear-gradient(135deg,transparent 30%,rgba(14,165,233,0.12) 100%);
}
.welcome-banner::after {
    content:'';position:absolute;bottom:-30px;right:50px;width:140px;height:140px;
    border:2px solid rgba(255,255,255,0.05);border-radius:50%;
}
.welcome-content {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    position: relative;
    z-index: 1;
}
.welcome-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
}
.welcome-text h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
}
.welcome-text p {
    margin: 0.3rem 0 0;
    opacity: 0.7;
    font-size: 0.92rem;
}
.welcome-date {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.08);
    padding: 0.6rem 1.2rem;
    border-radius: 24px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 1;
}

/* Stats Row */
.stats-row-crewing {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card-crewing {
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}
.stat-card-crewing:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.stat-card-crewing::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    border-radius: 4px 0 0 4px;
}
.stat-card-crewing.blue::before { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
.stat-card-crewing.orange::before { background: linear-gradient(180deg, #f59e0b, #d97706); }
.stat-card-crewing.purple::before { background: linear-gradient(180deg, #8b5cf6, #7c3aed); }
.stat-card-crewing.teal::before { background: linear-gradient(180deg, #14b8a6, #0d9488); }
.stat-card-crewing.green::before { background: linear-gradient(180deg, #22c55e, #16a34a); }
.stat-card-crewing.red::before { background: linear-gradient(180deg, #ef4444, #dc2626); }

.stat-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    flex-shrink: 0;
}
.stat-card-crewing.blue .stat-icon-wrap { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-card-crewing.orange .stat-icon-wrap { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-card-crewing.purple .stat-icon-wrap { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.stat-card-crewing.teal .stat-icon-wrap { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.stat-card-crewing.green .stat-icon-wrap { background: linear-gradient(135deg, #22c55e, #16a34a); }
.stat-card-crewing.red .stat-icon-wrap { background: linear-gradient(135deg, #ef4444, #dc2626); }

.stat-content h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    color: #1f2937;
    letter-spacing: -0.02em;
}
.stat-content span {
    font-size: 0.7rem;
    color: #8b95a5;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
}

/* Quick Actions Bar */
.quick-actions-bar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.quick-action-item {
    flex: 1;
    background: rgba(255,255,255,0.95);
    border-radius: 14px;
    padding: 1rem 1.2rem;
    text-align: center;
    text-decoration: none;
    color: #374151;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid rgba(255,255,255,0.2);
}
.quick-action-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(99,102,241,0.15);
    border-color: #c7d2fe;
    color: #4f46e5;
}
.quick-action-item i {
    font-size: 1.25rem;
    color: #6366f1;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    border-radius: 12px;
}
.quick-action-item:hover i {
    background: #6366f1;
    color: #fff;
}
.quick-action-item span {
    font-size: 0.78rem;
    font-weight: 600;
}

/* Dashboard Grid */
.dashboard-grid-modern {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}
.dashboard-card-modern {
    background: rgba(255,255,255,0.95);
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}
.dashboard-card-modern:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}
.dashboard-card-modern.team {
    grid-column: 1 / -1;
}
.card-header-modern {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(248,250,252,0.5);
}
.header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.header-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.95rem;
}
.header-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.header-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.header-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.header-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.card-header-modern h3 {
    margin: 0;
    font-size: 1rem;
    color: #1f2937;
    font-weight: 700;
}
.view-all-link {
    font-size: 0.8rem;
    color: #6366f1;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
    transition: all 0.2s;
}
.view-all-link:hover {
    background: #eef2ff;
    color: #4f46e5;
}
.card-body-modern {
    padding: 1.5rem;
}

/* Pipeline Visual */
.pipeline-visual {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.pipeline-status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    background: #f8f9fb;
    border-radius: 12px;
    border: 1px solid #eef0f4;
    transition: all 0.2s;
}
.pipeline-status-item:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
    transform: translateX(4px);
}
.status-color-bar {
    width: 4px;
    height: 32px;
    border-radius: 4px;
}
.status-details {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.status-name {
    font-size: 0.88rem;
    color: #374151;
    font-weight: 500;
}
.status-count {
    font-weight: 800;
    color: #1f2937;
    font-size: 1.1rem;
    background: #f1f5f9;
    padding: 2px 10px;
    border-radius: 8px;
}

/* Assignments List */
.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.assignment-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.25s;
    border: 1px solid transparent;
}
.assignment-item:hover {
    background: #f8f9fb;
    border-color: #eef0f4;
    transform: translateX(4px);
}
.assignment-avatar {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    font-weight: 700;
    flex-shrink: 0;
}
.assignment-info {
    flex: 1;
    min-width: 0;
}
.assignment-info strong {
    display: block;
    color: #1f2937;
    font-size: 0.9rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.assignment-info span {
    display: block;
    color: #8b95a5;
    font-size: 0.78rem;
    font-weight: 500;
}
.assignment-info small {
    color: #c4cad4;
    font-size: 0.72rem;
}
.assignment-status {
    padding: 4px 12px;
    border-radius: 20px;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Interviews List */
.interviews-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.interview-item-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.85rem 1rem;
    border-radius: 12px;
    background: #f8f9fb;
    border: 1px solid #eef0f4;
    transition: all 0.2s;
}
.interview-item-modern:hover {
    transform: translateX(4px);
}
.interview-item-modern.urgent {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 4px solid #ef4444;
}
.interview-main strong {
    display: block;
    font-size: 0.88rem;
    color: #1f2937;
    font-weight: 700;
}
.interview-main span {
    font-size: 0.78rem;
    color: #8b95a5;
}
.interview-badges {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}
.days-left {
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.72rem;
    font-weight: 600;
}
.days-left.warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.days-left.danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.interview-status {
    padding: 4px 10px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 8px;
    font-size: 0.72rem;
    font-weight: 600;
}

/* Team Workload */
.team-workload-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.team-member-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 0.85rem;
    border-radius: 12px;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.team-member-row:hover {
    background: #f8f9fb;
    border-color: #eef0f4;
}
.member-avatar-mini {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    flex-shrink: 0;
}
.member-details {
    flex: 1;
}
.member-details strong {
    display: block;
    font-size: 0.85rem;
    color: #1f2937;
    margin-bottom: 0.3rem;
    font-weight: 700;
}
.mini-progress {
    height: 6px;
    background: #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
}
.mini-progress-fill {
    height: 100%;
    border-radius: 6px;
    transition: width 0.6s ease;
}
.mini-progress-fill.success { background: linear-gradient(90deg, #22c55e, #16a34a); }
.mini-progress-fill.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
.mini-progress-fill.danger { background: linear-gradient(90deg, #ef4444, #dc2626); }
.workload-count {
    font-size: 0.82rem;
    color: #6b7280;
    font-weight: 700;
    flex-shrink: 0;
}

/* Empty State */
.empty-state-modern {
    text-align: center;
    padding: 2.5rem;
    color: #c4cad4;
}
.empty-state-modern i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.4;
}
.empty-state-modern p {
    font-weight: 500;
    font-size: 0.88rem;
}
.empty-state-modern.success i {
    color: #22c55e;
    opacity: 1;
}

@media (max-width: 1200px) {
    .stats-row-crewing {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .stats-row-crewing {
        grid-template-columns: repeat(2, 1fr);
    }
    .dashboard-grid-modern {
        grid-template-columns: 1fr;
    }
    .quick-actions-bar {
        flex-wrap: wrap;
    }
    .quick-action-item {
        flex: 0 0 calc(50% - 0.375rem);
    }
    .welcome-banner {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
}
</style>
