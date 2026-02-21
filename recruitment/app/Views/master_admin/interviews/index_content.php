<div class="page-header">
    <h1><i class="fas fa-robot"></i> AI Interviews</h1>
</div>

<!-- Tabs -->
<div class="tabs-container">
    <div class="tabs-nav">
        <button class="tab-btn active" onclick="showTab('sessions')">
            <i class="fas fa-video"></i> Interview Sessions
        </button>
        <button class="tab-btn" onclick="showTab('questions')">
            <i class="fas fa-question-circle"></i> Question Banks
        </button>
    </div>

    <!-- Sessions Tab -->
    <div class="tab-content active" id="sessions">
        <div class="card">
            <div class="card-header">
                <h3>Recent Interview Sessions</h3>
            </div>
            <div class="card-body">
                <?php if (empty($sessions)): ?>
                    <div class="empty-state">
                        <i class="fas fa-video"></i>
                        <h3>No Interviews Yet</h3>
                        <p>Interview sessions will appear here once applicants complete them.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr>
                                        <td>
                                            <div class="user-badge">
                                                <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                                                <div class="user-info">
                                                    <strong>
                                                        <?= htmlspecialchars($session['full_name']) ?>
                                                    </strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($session['vacancy_title'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $colors = ['pending' => '#ffc107', 'in_progress' => '#17a2b8', 'completed' => '#28a745', 'expired' => '#dc3545'];
                                            $color = $colors[$session['status']] ?? '#6c757d';
                                            ?>
                                            <span class="badge" style="background: <?= $color ?>20; color: <?= $color ?>">
                                                <?= ucfirst(str_replace('_', ' ', $session['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($session['total_score'] !== null): ?>
                                                <strong class="score">
                                                    <?= $session['total_score'] ?>
                                                </strong><span class="text-muted">/100</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($session['created_at'])) ?>
                                        </td>
                                        <td>
                                            <a href="<?= url('/master-admin/interviews/review/' . $session['id']) ?>"
                                                class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Question Banks Tab -->
    <div class="tab-content" id="questions">
        <div class="card">
            <div class="card-header">
                <h3>Question Banks</h3>
                <a href="<?= url('/master-admin/interviews/questions/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Question Bank
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($questionBanks)): ?>
                    <div class="empty-state">
                        <i class="fas fa-question-circle"></i>
                        <h3>No Question Banks</h3>
                        <p>Create question banks to use in AI interviews.</p>
                        <a href="<?= url('/master-admin/interviews/questions/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Question Bank
                        </a>
                    </div>
                <?php else: ?>
                    <div class="banks-grid">
                        <?php foreach ($questionBanks as $bank): ?>
                            <div class="bank-card">
                                <div class="bank-header">
                                    <h4>
                                        <?= htmlspecialchars($bank['name']) ?>
                                    </h4>
                                    <span class="question-count">
                                        <?= $bank['question_count'] ?? 0 ?> questions
                                    </span>
                                </div>
                                <p class="bank-desc">
                                    <?= htmlspecialchars($bank['description'] ?? 'No description') ?>
                                </p>
                                <div class="bank-meta">
                                    <span><i class="fas fa-clock"></i>
                                        <?= $bank['time_limit_minutes'] ?? 30 ?> min
                                    </span>
                                    <span class="badge badge-<?= !empty($bank['is_active']) ? 'success' : 'secondary' ?>">
                                        <?= !empty($bank['is_active']) ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                                <div class="bank-actions">
                                    <a href="<?= url('/master-admin/interviews/questions/' . $bank['id']) ?>"
                                        class="btn btn-outline btn-sm">
                                        <i class="fas fa-edit"></i> Manage
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .tabs-container {
        margin-top: 10px;
    }

    .tabs-nav {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .tab-btn {
        padding: 12px 24px;
        border: none;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-family: inherit;
        font-size: 14px;
        font-weight: 500;
        color: #666;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .tab-btn:hover {
        color: #0A2463;
    }

    .tab-btn.active {
        background: #0A2463;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .score {
        font-size: 18px;
        color: #0A2463;
    }

    .banks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .bank-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
    }

    .bank-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .bank-header h4 {
        font-size: 16px;
        color: #1a1a2e;
        margin: 0;
    }

    .question-count {
        font-size: 12px;
        background: #e0e0e0;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .bank-desc {
        font-size: 13px;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .bank-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .bank-meta span {
        font-size: 12px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .bank-actions {
        display: flex;
        gap: 10px;
    }

    .text-muted {
        color: #6c757d;
    }
</style>

<script>
    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        event.target.closest('.tab-btn').classList.add('active');
    }
</script>