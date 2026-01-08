<!-- Crewing Pipeline with Request System -->
<div class="pipeline-header">
    <div class="header-left">
        <div class="header-icon">
            <i class="fas fa-stream"></i>
        </div>
        <div>
            <h1>Recruitment Pipeline</h1>
            <p>Drag cards to request status changes</p>
        </div>
    </div>
    <div class="header-actions">
        <div class="view-toggle">
            <a href="<?= url('/crewing/pipeline?view=my') ?>" class="view-btn <?= $currentView === 'my' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> My Pipeline
            </a>
            <a href="<?= url('/crewing/pipeline?view=team') ?>" class="view-btn <?= $currentView === 'team' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Team
            </a>
            <a href="<?= url('/crewing/pipeline?view=all') ?>" class="view-btn <?= $currentView === 'all' ? 'active' : '' ?>">
                <i class="fas fa-globe"></i> All
            </a>
        </div>
        
        <?php if ($currentView === 'team' && !empty($crewingStaff)): ?>
        <select id="crewingFilter" class="filter-select" onchange="filterByCrewing(this.value)">
            <option value="">All Team Members</option>
            <?php foreach ($crewingStaff as $crew): ?>
            <option value="<?= $crew['id'] ?>" <?= ($filterCrewingId ?? '') == $crew['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($crew['full_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
    </div>
</div>

<!-- Info Banner -->
<div class="info-banner">
    <i class="fas fa-info-circle"></i>
    <span>Status changes require Master Admin approval. Drag a card to request a move.</span>
</div>

<!-- Pipeline Kanban Board -->
<div class="pipeline-kanban-modern">
    <?php foreach ($statuses as $status): ?>
    <div class="kanban-column-modern" data-status-id="<?= $status['id'] ?>">
        <div class="column-header-modern" style="background: <?= $status['color'] ?>">
            <h3><?= $status['name'] ?></h3>
            <span class="column-count"><?= count($pipeline[$status['id']] ?? []) ?></span>
        </div>
        
        <div class="column-body-modern" data-status-id="<?= $status['id'] ?>" data-status-name="<?= $status['name'] ?>">
            <?php if (empty($pipeline[$status['id']])): ?>
            <div class="empty-column-modern">
                <i class="fas fa-inbox"></i>
                <span>No applications</span>
            </div>
            <?php else: ?>
            <?php foreach ($pipeline[$status['id']] as $app): ?>
            <div class="kanban-card-modern" 
                 data-app-id="<?= $app['id'] ?>"
                 data-app-name="<?= htmlspecialchars($app['full_name']) ?>"
                 data-current-status="<?= $status['name'] ?>"
                 draggable="true">
                
                <!-- Card Header with Avatar -->
                <div class="card-top">
                    <div class="applicant-avatar">
                        <?= strtoupper(substr($app['full_name'], 0, 2)) ?>
                    </div>
                    <div class="applicant-main">
                        <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                        <span class="vacancy-name"><?= htmlspecialchars($app['vacancy_title']) ?></span>
                    </div>
                    <?php if (isset($app['priority']) && $app['priority'] !== 'normal'): ?>
                    <span class="priority-dot <?= $app['priority'] ?>"></span>
                    <?php endif; ?>
                </div>
                
                <!-- Applicant Details -->
                <div class="card-details">
                    <?php if (!empty($app['email'])): ?>
                    <div class="detail-row">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($app['email']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($app['phone'])): ?>
                    <div class="detail-row">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($app['phone']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($app['department_name'])): ?>
                    <div class="detail-row">
                        <i class="fas fa-building"></i>
                        <span><?= htmlspecialchars($app['department_name']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Card Footer -->
                <div class="card-bottom">
                    <?php if (!empty($app['crewing_name'])): ?>
                    <span class="handler-tag">
                        <i class="fas fa-user-tag"></i> <?= htmlspecialchars($app['crewing_name']) ?>
                    </span>
                    <?php endif; ?>
                    <span class="days-badge <?= ($app['days_in_status'] ?? 0) > 5 ? 'warning' : '' ?>">
                        <?= $app['days_in_status'] ?? 0 ?> days
                    </span>
                </div>
                
                <!-- Hover Actions -->
                <div class="card-actions-overlay">
                    <a href="<?= url('/crewing/applications/' . $app['id']) ?>" class="action-btn view">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Request Status Change Modal -->
<div class="modal-overlay" id="requestModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-exchange-alt"></i> Request Status Change</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="requestForm" action="<?= url('/crewing/pipeline/request-status') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="application_id" id="reqAppId">
            <input type="hidden" name="to_status_id" id="reqToStatus">
            
            <div class="modal-body">
                <div class="request-summary">
                    <div class="summary-item">
                        <label>Applicant</label>
                        <span id="reqAppName">-</span>
                    </div>
                    <div class="summary-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="summary-item">
                        <label>From Status</label>
                        <span id="reqFromStatus" class="status-label">-</span>
                    </div>
                    <div class="summary-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="summary-item">
                        <label>To Status</label>
                        <span id="reqToStatusName" class="status-label success">-</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="reason"><i class="fas fa-comment-alt"></i> Reason for Change <span class="required">*</span></label>
                    <textarea name="reason" id="reason" rows="3" class="form-input" required 
                              placeholder="Please provide a reason for this status change request..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Pipeline Header */
.pipeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    border-radius: 16px;
    margin-bottom: 1rem;
    color: white;
}
.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.header-left h1 {
    margin: 0;
    font-size: 1.25rem;
}
.header-left p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.85rem;
}
.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}
.view-toggle {
    display: flex;
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    overflow: hidden;
}
.view-btn {
    padding: 0.5rem 1rem;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.view-btn:hover, .view-btn.active {
    background: rgba(255,255,255,0.2);
    color: white;
}
.filter-select {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
    color: white;
    font-size: 0.85rem;
}
.filter-select option {
    color: #333;
}

/* Info Banner */
.info-banner {
    background: #fef3c7;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #92400e;
    font-size: 0.9rem;
}
.info-banner i {
    color: #f59e0b;
}

/* Kanban Board */
.pipeline-kanban-modern {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    min-height: calc(100vh - 280px);
}
.kanban-column-modern {
    flex: 0 0 300px;
    background: #f8fafc;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 280px);
}
.column-header-modern {
    padding: 1rem 1.25rem;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}
.column-header-modern h3 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
}
.column-count {
    background: rgba(255,255,255,0.3);
    padding: 0.25rem 0.625rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}
.column-body-modern {
    flex: 1;
    padding: 0.75rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.empty-column-modern {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
}
.empty-column-modern i {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.5rem;
}

/* Kanban Card */
.kanban-card-modern {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    cursor: grab;
    position: relative;
    transition: all 0.2s;
    overflow: hidden;
}
.kanban-card-modern:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}
.kanban-card-modern.dragging {
    opacity: 0.5;
    transform: rotate(3deg);
}
.card-top {
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
}
.applicant-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.85rem;
    font-weight: 600;
    flex-shrink: 0;
}
.applicant-main {
    flex: 1;
    min-width: 0;
}
.applicant-main strong {
    display: block;
    font-size: 0.9rem;
    color: #1f2937;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.vacancy-name {
    display: block;
    font-size: 0.8rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.priority-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.priority-dot.urgent { background: #ef4444; }
.priority-dot.high { background: #f59e0b; }
.priority-dot.low { background: #9ca3af; }

.card-details {
    padding: 0.75rem 1rem;
    background: #fafafa;
}
.detail-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}
.detail-row:last-child {
    margin-bottom: 0;
}
.detail-row i {
    width: 14px;
    color: #9ca3af;
}

.card-bottom {
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
}
.handler-tag {
    color: #6b7280;
}
.handler-tag i {
    margin-right: 0.25rem;
    color: #3b82f6;
}
.days-badge {
    background: #e5e7eb;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
    color: #374151;
}
.days-badge.warning {
    background: #fef3c7;
    color: #92400e;
}

/* Card Hover Actions */
.card-actions-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(30, 58, 95, 0.9);
    display: none;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}
.kanban-card-modern:hover .card-actions-overlay {
    display: flex;
}
.action-btn.view {
    background: white;
    color: #1e3a5f;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
}

/* Drag Over State */
.column-body-modern.drag-over {
    background: rgba(59, 130, 246, 0.1);
    border: 2px dashed #3b82f6;
    border-radius: 8px;
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
.modal-overlay.active {
    display: flex;
}
.modal-container {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #9ca3af;
    cursor: pointer;
}
.modal-body {
    padding: 1.5rem;
}
.request-summary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 1.25rem;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}
.summary-item {
    text-align: center;
}
.summary-item label {
    display: block;
    font-size: 0.7rem;
    color: #9ca3af;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}
.summary-item span {
    font-weight: 600;
    color: #1f2937;
}
.status-label {
    background: #e5e7eb;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
}
.status-label.success {
    background: #dcfce7;
    color: #166534;
}
.summary-arrow {
    color: #9ca3af;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #374151;
}
.required {
    color: #ef4444;
}
.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    font-family: inherit;
    resize: vertical;
}
.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}
.modal-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-radius: 0 0 16px 16px;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
.btn-cancel {
    padding: 0.625rem 1.25rem;
    background: #e5e7eb;
    border: none;
    border-radius: 8px;
    color: #374151;
    cursor: pointer;
    font-size: 0.9rem;
}
.btn-submit {
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.kanban-card-modern');
    const columns = document.querySelectorAll('.column-body-modern');
    
    let draggedCard = null;
    let originalColumn = null;
    
    cards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedCard = this;
            originalColumn = this.parentElement;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        
        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedCard = null;
            columns.forEach(col => col.classList.remove('drag-over'));
        });
    });
    
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });
        
        column.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedCard && originalColumn !== this) {
                const newStatusId = this.dataset.statusId;
                const newStatusName = this.dataset.statusName;
                const appId = draggedCard.dataset.appId;
                const appName = draggedCard.dataset.appName;
                const currentStatus = draggedCard.dataset.currentStatus;
                
                // Show request modal
                showRequestModal(appId, appName, currentStatus, newStatusId, newStatusName);
            }
        });
    });
});

function showRequestModal(appId, appName, fromStatus, toStatusId, toStatusName) {
    document.getElementById('reqAppId').value = appId;
    document.getElementById('reqToStatus').value = toStatusId;
    document.getElementById('reqAppName').textContent = appName;
    document.getElementById('reqFromStatus').textContent = fromStatus;
    document.getElementById('reqToStatusName').textContent = toStatusName;
    document.getElementById('reason').value = '';
    document.getElementById('requestModal').classList.add('active');
}

function closeModal() {
    document.getElementById('requestModal').classList.remove('active');
}

function filterByCrewing(crewingId) {
    const url = new URL(window.location.href);
    url.searchParams.set('view', 'team');
    if (crewingId) {
        url.searchParams.set('crewing', crewingId);
    } else {
        url.searchParams.delete('crewing');
    }
    window.location.href = url.toString();
}

// Close modal on outside click
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
