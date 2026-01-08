<div class="page-header">
    <div class="header-left">
        <a href="<?= url('/crewing/applications') ?>" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h1>Application Detail</h1>
    </div>
    <div class="header-actions">
        <span class="status-badge large" style="background-color: <?= $application['status_color'] ?>">
            <?= $application['status_name'] ?>
        </span>
    </div>
</div>

<div class="detail-grid">
    <!-- Applicant Info -->
    <div class="detail-card applicant-card">
        <div class="card-header">
            <h2><i class="fas fa-user"></i> Applicant Information</h2>
        </div>
        <div class="card-body">
            <div class="applicant-profile">
                <img src="<?= $application['avatar'] ? asset('uploads/avatars/' . $application['avatar']) : asset('images/avatar-default.png') ?>" 
                     alt="Avatar" class="avatar-large">
                <div class="profile-info">
                    <h3><?= htmlspecialchars($application['full_name']) ?></h3>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($application['email']) ?></p>
                    <?php if ($application['phone']): ?>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($application['phone']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Date of Birth</label>
                    <span><?= $application['date_of_birth'] ? date('M d, Y', strtotime($application['date_of_birth'])) : '-' ?></span>
                </div>
                <div class="info-item">
                    <label>Gender</label>
                    <span><?= ucfirst($application['gender'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <label>Nationality</label>
                    <span><?= htmlspecialchars($application['nationality'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <label>Seaman Book</label>
                    <span><?= htmlspecialchars($application['seaman_book_no'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <label>Passport</label>
                    <span><?= htmlspecialchars($application['passport_no'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <label>Total Sea Service</label>
                    <span><?= $application['total_sea_service_months'] ?? 0 ?> months</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Vacancy Info -->
    <div class="detail-card vacancy-card">
        <div class="card-header">
            <h2><i class="fas fa-briefcase"></i> Applied Position</h2>
        </div>
        <div class="card-body">
            <h3><?= htmlspecialchars($application['vacancy_title']) ?></h3>
            <p class="department"><?= htmlspecialchars($application['department_name'] ?? '') ?></p>
            
            <div class="vacancy-meta">
                <div class="meta-item">
                    <label>Salary Range</label>
                    <span>
                        <?php if ($application['salary_min'] && $application['salary_max']): ?>
                        $<?= number_format($application['salary_min']) ?> - $<?= number_format($application['salary_max']) ?>
                        <?php else: ?>
                        Negotiable
                        <?php endif; ?>
                    </span>
                </div>
                <div class="meta-item">
                    <label>Submitted</label>
                    <span><?= date('M d, Y H:i', strtotime($application['submitted_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Assignment Info -->
    <div class="detail-card assignment-card">
        <div class="card-header">
            <h2><i class="fas fa-user-tag"></i> Current Assignment</h2>
            <button type="button" class="btn btn-sm btn-primary" id="reassignBtn">
                <i class="fas fa-exchange-alt"></i> Reassign
            </button>
        </div>
        <div class="card-body">
            <?php if ($application['assigned_to_name']): ?>
            <div class="assignment-info">
                <div class="assigned-to">
                    <label>Assigned To</label>
                    <strong><?= htmlspecialchars($application['assigned_to_name']) ?></strong>
                </div>
                <div class="assigned-by">
                    <label>Assigned By</label>
                    <span><?= htmlspecialchars($application['assigned_by_name'] ?? 'System') ?></span>
                </div>
                <div class="assigned-at">
                    <label>Assigned At</label>
                    <span><?= date('M d, Y H:i', strtotime($application['assigned_at'])) ?></span>
                </div>
                <?php if ($application['assignment_notes']): ?>
                <div class="assignment-notes">
                    <label>Notes</label>
                    <p><?= nl2br(htmlspecialchars($application['assignment_notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="no-assignment">
                <p>This application is not assigned to anyone yet.</p>
                <button type="button" class="btn btn-primary" id="assignBtn">
                    <i class="fas fa-user-plus"></i> Assign Now
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="detail-card actions-card">
        <div class="card-header">
            <h2><i class="fas fa-cogs"></i> Actions</h2>
        </div>
        <div class="card-body">
            <!-- Update Status -->
            <form method="POST" action="<?= url('/crewing/applications/status/' . $application['id']) ?>" class="status-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Update Status</label>
                    <select name="status_id" class="form-control" required>
                        <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status['id'] ?>" <?= $application['status_id'] == $status['id'] ? 'selected' : '' ?>>
                            <?= $status['name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="low" <?= ($application['priority'] ?? 'normal') === 'low' ? 'selected' : '' ?>>⚪ Low</option>
                        <option value="normal" <?= ($application['priority'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>🟢 Normal</option>
                        <option value="high" <?= ($application['priority'] ?? 'normal') === 'high' ? 'selected' : '' ?>>🟠 High</option>
                        <option value="urgent" <?= ($application['priority'] ?? 'normal') === 'urgent' ? 'selected' : '' ?>>🔴 Urgent</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Add notes about this status change..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Update Status
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Documents Section -->
<div class="detail-section">
    <div class="section-header">
        <h2><i class="fas fa-file-alt"></i> Documents</h2>
    </div>
    <div class="documents-grid">
        <?php if (empty($documents)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p>No documents uploaded yet</p>
        </div>
        <?php else: ?>
        <?php foreach ($documents as $doc): ?>
        <div class="document-card <?= $doc['verification_status'] ?>">
            <div class="doc-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="doc-info">
                <h4><?= htmlspecialchars($doc['type_name']) ?></h4>
                <p><?= htmlspecialchars($doc['original_name']) ?></p>
                <span class="doc-status <?= $doc['verification_status'] ?>">
                    <?= ucfirst($doc['verification_status']) ?>
                </span>
            </div>
            <div class="doc-actions">
                <a href="<?= asset('uploads/documents/' . $doc['file_name']) ?>" target="_blank" class="btn btn-sm btn-outline">
                    <i class="fas fa-eye"></i> View
                </a>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Interviews Section -->
<div class="detail-section">
    <div class="section-header">
        <h2><i class="fas fa-robot"></i> Interview Sessions</h2>
    </div>
    <?php if (empty($interviews)): ?>
    <div class="empty-state">
        <i class="fas fa-comments"></i>
        <p>No interview sessions yet</p>
    </div>
    <?php else: ?>
    <div class="interview-cards">
        <?php foreach ($interviews as $interview): ?>
        <div class="interview-card <?= $interview['status'] ?>">
            <div class="interview-header">
                <span class="interview-status <?= $interview['status'] ?>"><?= ucfirst($interview['status']) ?></span>
                <span class="interview-bank"><?= htmlspecialchars($interview['question_bank_name']) ?></span>
            </div>
            <div class="interview-body">
                <div class="interview-meta">
                    <span><i class="fas fa-calendar"></i> Created: <?= date('M d, Y', strtotime($interview['created_at'])) ?></span>
                    <?php if ($interview['expires_at']): ?>
                    <span><i class="fas fa-clock"></i> Expires: <?= date('M d, Y', strtotime($interview['expires_at'])) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($interview['total_score']): ?>
                <div class="interview-score">
                    <strong>Score: <?= $interview['total_score'] ?></strong>
                    <span class="recommendation <?= $interview['ai_recommendation'] ?>">
                        AI: <?= ucfirst($interview['ai_recommendation'] ?? 'N/A') ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($interview['status'] === 'completed'): ?>
            <div class="interview-footer">
                <a href="<?= url('/admin/interviews/review/' . $interview['id']) ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Review Answers
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Assignment History -->
<div class="detail-section">
    <div class="section-header">
        <h2><i class="fas fa-history"></i> Assignment History</h2>
    </div>
    <?php if (empty($assignmentHistory)): ?>
    <div class="empty-state">
        <i class="fas fa-user-clock"></i>
        <p>No assignment history</p>
    </div>
    <?php else: ?>
    <div class="history-timeline">
        <?php foreach ($assignmentHistory as $history): ?>
        <div class="timeline-item <?= $history['status'] ?>">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <div class="timeline-header">
                    <strong><?= htmlspecialchars($history['assigned_to_name']) ?></strong>
                    <span class="status-tag <?= $history['status'] ?>"><?= ucfirst($history['status']) ?></span>
                </div>
                <p>Assigned by <?= htmlspecialchars($history['assigned_by_name']) ?></p>
                <?php if ($history['notes']): ?>
                <p class="notes"><?= htmlspecialchars($history['notes']) ?></p>
                <?php endif; ?>
                <small><?= date('M d, Y H:i', strtotime($history['assigned_at'])) ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Status History -->
<div class="detail-section">
    <div class="section-header">
        <h2><i class="fas fa-exchange-alt"></i> Status History</h2>
    </div>
    <?php if (empty($statusHistory)): ?>
    <div class="empty-state">
        <i class="fas fa-stream"></i>
        <p>No status changes yet</p>
    </div>
    <?php else: ?>
    <div class="history-timeline">
        <?php foreach ($statusHistory as $history): ?>
        <div class="timeline-item">
            <div class="timeline-marker" style="background-color: <?= $history['to_color'] ?>"></div>
            <div class="timeline-content">
                <div class="timeline-header">
                    <?php if ($history['from_status']): ?>
                    <span><?= $history['from_status'] ?></span>
                    <i class="fas fa-arrow-right"></i>
                    <?php endif; ?>
                    <strong style="color: <?= $history['to_color'] ?>"><?= $history['to_status'] ?></strong>
                </div>
                <?php if ($history['notes']): ?>
                <p class="notes"><?= htmlspecialchars($history['notes']) ?></p>
                <?php endif; ?>
                <small>
                    By <?= htmlspecialchars($history['changed_by_name'] ?? 'System') ?> 
                    • <?= date('M d, Y H:i', strtotime($history['created_at'])) ?>
                </small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Assign/Reassign Modal -->
<div class="modal" id="assignModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Assign Application</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST" action="<?= url('/crewing/applications/assign/' . $application['id']) ?>">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label>Assign to Crewing Staff</label>
                    <select name="assign_to" required class="form-control">
                        <option value="">Select Crewing Staff...</option>
                        <?php foreach ($crewingStaff as $crew): ?>
                        <option value="<?= $crew['id'] ?>">
                            <?= htmlspecialchars($crew['full_name']) ?>
                            (<?= $crew['active_assignments'] ?> active)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="notes" rows="3" class="form-control" 
                              placeholder="Add notes for the assigned crewing..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assignModal');
    
    // Open modal
    ['assignBtn', 'reassignBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', () => modal.classList.add('show'));
        }
    });
    
    // Close modal
    modal.querySelector('.modal-close').addEventListener('click', () => modal.classList.remove('show'));
    modal.querySelector('.modal-cancel').addEventListener('click', () => modal.classList.remove('show'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('show');
    });
});
</script>
