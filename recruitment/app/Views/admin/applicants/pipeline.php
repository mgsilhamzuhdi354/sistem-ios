<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipeline - Admin | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
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
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link active"><i class="fas fa-stream"></i><span>Pipeline</span></a></li>
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

        <div class="admin-content pipeline-page">
            <div class="page-header">
                <h1><i class="fas fa-stream"></i> Recruitment Pipeline</h1>
                <a href="<?= url('/admin/applicants') ?>" class="btn btn-outline">
                    <i class="fas fa-list"></i> List View
                </a>
            </div>

            <div class="pipeline-container">
                <div class="pipeline-board">
                    <?php foreach ($statuses ?? [] as $status): ?>
                        <div class="pipeline-column">
                            <div class="column-header" style="background: <?= $status['color'] ?? '#0A2463' ?>">
                                <span class="column-title"><?= htmlspecialchars($status['name']) ?></span>
                                <span class="column-count"><?= count($pipeline[$status['id']] ?? []) ?></span>
                            </div>
                            <div class="column-body" data-status-id="<?= $status['id'] ?>">
                                <?php if (!empty($pipeline[$status['id']])): ?>
                                    <?php foreach ($pipeline[$status['id']] as $app): ?>
                                        <div class="pipeline-card" data-id="<?= $app['id'] ?>" draggable="true">
                                            <div class="card-header">
                                                <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                                            </div>
                                            <div class="card-position"><?= htmlspecialchars($app['vacancy_title']) ?></div>
                                            <div class="card-footer">
                                                <span class="card-date">
                                                    <?php 
                                                    $days = $app['days_in_status'] ?? 0;
                                                    echo $days <= 0 ? 'Today' : $days . ' days ago';
                                                    ?>
                                                </span>
                                                <a href="<?= url('/admin/applicants/' . $app['id']) ?>" class="card-action" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-column">
                                        <i class="fas fa-inbox"></i>
                                        <span>No applicants</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
    .pipeline-page {
        padding: 20px;
        height: calc(100vh - 80px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .pipeline-page .page-header {
        flex-shrink: 0;
        margin-bottom: 20px;
    }
    
    .pipeline-container {
        flex: 1;
        overflow: hidden;
        height: calc(100vh - 160px); /* Fixed height for container */
    }
    
    .pipeline-board {
        display: flex;
        gap: 15px;
        height: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 10px;
    }
    
    .pipeline-column {
        min-width: 280px;
        width: 280px;
        flex-shrink: 0;
        background: #fff;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        height: 100%; /* Full height of container */
        max-height: calc(100vh - 180px); /* Maximum height with some margin */
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    
    .column-header {
        padding: 12px 15px;
        border-radius: 10px 10px 0 0;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    
    .column-title {
        font-size: 13px;
        font-weight: 600;
    }
    
    .column-count {
        background: rgba(255,255,255,0.25);
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .column-body {
        flex: 1;
        overflow-y: scroll; /* Always show scrollbar */
        overflow-x: hidden;
        padding: 10px;
        min-height: 100px;
        max-height: calc(100vh - 250px); /* Explicit max height */
    }
    
    .column-body::-webkit-scrollbar {
        width: 8px; /* Wider scrollbar */
    }
    
    .column-body::-webkit-scrollbar-track {
        background: #e9ecef;
        border-radius: 4px;
    }
    
    .column-body::-webkit-scrollbar-thumb {
        background: #adb5bd; /* Darker thumb */
        border-radius: 4px;
    }
    
    .column-body::-webkit-scrollbar-thumb:hover {
        background: #6c757d; /* Even darker on hover */
    }
    
    .column-body.drag-over {
        background: #e3f2fd;
    }
    
    .pipeline-card {
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: grab;
        transition: all 0.2s;
    }
    
    .pipeline-card:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transform: translateY(-1px);
        border-color: #0A2463;
    }
    
    .pipeline-card.dragging {
        opacity: 0.5;
        transform: rotate(2deg);
    }
    
    .card-header {
        margin-bottom: 6px;
    }
    
    .card-header strong {
        font-size: 14px;
        color: #1a1a2e;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .card-position {
        font-size: 12px;
        color: #666;
        margin-bottom: 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 8px;
        border-top: 1px solid #eee;
    }
    
    .card-date {
        font-size: 11px;
        color: #999;
        background: #fff;
        padding: 3px 8px;
        border-radius: 10px;
    }
    
    .card-action {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0A2463;
        color: white;
        border-radius: 50%;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .card-action:hover {
        background: #D4AF37;
        transform: scale(1.1);
    }
    
    .empty-column {
        text-align: center;
        padding: 40px 15px;
        color: #bbb;
    }
    
    .empty-column i {
        font-size: 32px;
        margin-bottom: 10px;
        display: block;
    }
    
    .empty-column span {
        font-size: 12px;
    }
    
    /* Notification toast */
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        background: #1a1a2e;
        color: white;
        border-radius: 8px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    }
    
    .toast.success { background: #28a745; }
    .toast.error { background: #dc3545; }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.pipeline-card');
        const columns = document.querySelectorAll('.column-body');
        let draggedCard = null;

        // Drag events for cards
        cards.forEach(card => {
            card.addEventListener('dragstart', function(e) {
                draggedCard = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.dataset.id);
            });

            card.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                draggedCard = null;
                columns.forEach(col => col.classList.remove('drag-over'));
            });
        });

        // Drop events for columns
        columns.forEach(column => {
            column.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('drag-over');
            });

            column.addEventListener('dragleave', function(e) {
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            });

            column.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (draggedCard) {
                    const cardId = draggedCard.dataset.id;
                    const newStatusId = this.dataset.statusId;
                    const oldColumn = draggedCard.parentElement;

                    // Move card visually
                    const emptyMsg = this.querySelector('.empty-column');
                    if (emptyMsg) emptyMsg.remove();
                    this.appendChild(draggedCard);

                    // Check if old column is now empty
                    if (oldColumn.querySelectorAll('.pipeline-card').length === 0) {
                        oldColumn.innerHTML = '<div class="empty-column"><i class="fas fa-inbox"></i><span>No applicants</span></div>';
                    }

                    // Update counts
                    updateColumnCounts();

                    // Send to server
                    updateStatus(cardId, newStatusId);
                }
            });
        });

        function updateColumnCounts() {
            document.querySelectorAll('.pipeline-column').forEach(col => {
                const count = col.querySelectorAll('.pipeline-card').length;
                col.querySelector('.column-count').textContent = count;
            });
        }

        function updateStatus(appId, statusId) {
            const formData = new FormData();
            formData.append('status_id', statusId);
            formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('<?= url('/admin/applicants/status/') ?>' + appId, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Status updated successfully', 'success');
                } else {
                    showToast(data.message || 'Failed to update status', 'error');
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Connection error', 'error');
            });
        }

        function showToast(message, type) {
            const existingToast = document.querySelector('.toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check' : 'exclamation') + '-circle"></i> ' + message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
    </script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
