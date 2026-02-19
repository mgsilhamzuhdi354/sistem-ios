<style>
    .ai-hero {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .ai-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .ai-hero-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    .ai-hero h1 {
        color: white;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .ai-hero p {
        color: rgba(255,255,255,0.8);
        font-size: 0.95rem;
        max-width: 500px;
    }
    .hero-icon {
        font-size: 4rem;
        color: rgba(255,255,255,0.15);
    }

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card-modern {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: transform 0.2s;
    }
    .stat-card-modern:hover { transform: translateY(-3px); }
    .stat-card-modern h3 { font-size: 1.8rem; color: #1a1a2e; margin: 0; }
    .stat-card-modern span { font-size: 0.8rem; color: #64748b; }
    .icon-wrap {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .icon-wrap.blue { background: rgba(99,102,241,0.1); color: #6366f1; }
    .icon-wrap.yellow { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .icon-wrap.green { background: rgba(16,185,129,0.1); color: #10b981; }
    .icon-wrap.purple { background: rgba(168,85,247,0.1); color: #a855f7; }

    /* Toolbar */
    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .filter-group {
        display: flex;
        gap: 0.5rem;
    }
    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-btn:hover, .filter-btn.active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }
    .btn-assign {
        padding: 0.625rem 1.25rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    .btn-assign:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(99,102,241,0.3);
    }

    /* Sessions Table */
    .sessions-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .sessions-table table {
        width: 100%;
        border-collapse: collapse;
    }
    .sessions-table th {
        text-align: left;
        padding: 1rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .sessions-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .sessions-table tr:last-child td { border-bottom: none; }
    .sessions-table tr:hover td { background: rgba(99,102,241,0.02); }
    
    .applicant-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .applicant-cell .avatar {
        width: 38px; height: 38px;
        border-radius: 10px;
        object-fit: cover;
        background: #e2e8f0;
    }
    .applicant-cell .name {
        font-weight: 600;
        font-size: 0.85rem;
        color: #1a1a2e;
    }
    .applicant-cell .email {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .status-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-badge-modern.pending { background: #fef3c7; color: #92400e; }
    .status-badge-modern.in_progress { background: #dbeafe; color: #1e40af; }
    .status-badge-modern.completed { background: #d1fae5; color: #065f46; }
    .status-badge-modern.expired { background: #fee2e2; color: #991b1b; }
    .status-badge-modern .dot {
        width: 6px; height: 6px; border-radius: 50%; background: currentColor;
    }

    .progress-bar-mini {
        width: 80px; height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-bar-mini .fill {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
    }
    .progress-text {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }

    .score-cell .score-val {
        font-size: 1.2rem;
        font-weight: 700;
    }
    .score-cell .score-val.high { color: #10b981; }
    .score-cell .score-val.mid { color: #f59e0b; }
    .score-cell .score-val.low { color: #ef4444; }

    .action-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        background: rgba(99,102,241,0.08);
        color: #6366f1;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .action-link:hover {
        background: #6366f1;
        color: white;
    }

    .empty-table {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }
    .empty-table i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        color: #cbd5e1;
    }
    .empty-table p {
        font-size: 0.9rem;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        width: 480px;
        max-width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .modal-box h3 {
        font-size: 1.2rem;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .modal-box h3 i { color: #6366f1; }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .form-group select, .form-group input {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.85rem;
        font-family: inherit;
    }
    .form-group select:focus, .form-group input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .modal-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }
    .btn-modal-cancel {
        padding: 0.5rem 1rem;
        background: #f1f5f9;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        cursor: pointer;
        color: #64748b;
    }
    .btn-modal-submit {
        padding: 0.5rem 1.25rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-submit:hover { box-shadow: 0 4px 15px rgba(99,102,241,0.3); }

    @media (max-width: 768px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .toolbar { flex-direction: column; }
        .sessions-table { overflow-x: auto; }
    }
</style>

<!-- Hero Section -->
<div class="ai-hero">
    <div class="ai-hero-content">
        <div>
            <h1><i class="fas fa-robot"></i> AI Interview Center</h1>
            <p>Manage AI-powered interview sessions for your applicants. Assign question banks, track progress, and review AI-scored responses.</p>
        </div>
        <div class="hero-icon">
            <i class="fas fa-brain"></i>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card-modern">
        <div class="icon-wrap blue"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <h3><?= intval($stats['total'] ?? 0) ?></h3>
            <span>Total Sessions</span>
        </div>
    </div>
    <div class="stat-card-modern">
        <div class="icon-wrap yellow"><i class="fas fa-spinner"></i></div>
        <div>
            <h3><?= intval($stats['in_progress'] ?? 0) ?></h3>
            <span>In Progress</span>
        </div>
    </div>
    <div class="stat-card-modern">
        <div class="icon-wrap green"><i class="fas fa-check-circle"></i></div>
        <div>
            <h3><?= intval($stats['completed'] ?? 0) ?></h3>
            <span>Completed</span>
        </div>
    </div>
    <div class="stat-card-modern">
        <div class="icon-wrap purple"><i class="fas fa-chart-line"></i></div>
        <div>
            <h3><?= round($stats['avg_score'] ?? 0) ?></h3>
            <span>Avg Score</span>
        </div>
    </div>
</div>

<!-- Toolbar -->
<div class="toolbar">
    <div class="filter-group">
        <a href="<?= url('/crewing/interviews') ?>" class="filter-btn <?= empty($filter_status) ? 'active' : '' ?>">All</a>
        <a href="<?= url('/crewing/interviews?status=pending') ?>" class="filter-btn <?= ($filter_status ?? '') === 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="<?= url('/crewing/interviews?status=in_progress') ?>" class="filter-btn <?= ($filter_status ?? '') === 'in_progress' ? 'active' : '' ?>">In Progress</a>
        <a href="<?= url('/crewing/interviews?status=completed') ?>" class="filter-btn <?= ($filter_status ?? '') === 'completed' ? 'active' : '' ?>">Completed</a>
    </div>
    <button class="btn-assign" onclick="document.getElementById('assignModal').classList.add('active')">
        <i class="fas fa-plus"></i> Assign Interview
    </button>
    <a href="<?= url('/crewing/interviews/questions') ?>" class="btn-assign" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);text-decoration:none;">
        <i class="fas fa-database"></i> Kelola Pertanyaan
    </a>

</div>

<!-- Sessions Table -->
<div class="sessions-table">
    <?php if (empty($sessions)): ?>
        <div class="empty-table">
            <i class="fas fa-robot"></i>
            <p>No interview sessions yet. Use "Assign Interview" to get started.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Vacancy</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Score</th>
                    <th>Date</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $s): ?>
                    <tr>
                        <td>
                            <div class="applicant-cell">
                                <img src="<?= $s['avatar'] ? asset('uploads/avatars/' . $s['avatar']) : asset('images/avatar-default.svg') ?>" alt="Avatar" class="avatar">
                                <div>
                                    <div class="name"><?= htmlspecialchars($s['full_name']) ?></div>
                                    <div class="email"><?= htmlspecialchars($s['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.85rem; color:#374151;"><?= htmlspecialchars($s['vacancy_title']) ?></td>
                        <td>
                            <span class="status-badge-modern <?= $s['status'] ?>">
                                <span class="dot"></span>
                                <?= ucfirst(str_replace('_', ' ', $s['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php $pct = $s['total_questions'] > 0 ? round(($s['answered_questions'] / $s['total_questions']) * 100) : 0; ?>
                            <div class="progress-bar-mini">
                                <div class="fill" style="width: <?= $pct ?>%"></div>
                            </div>
                            <div class="progress-text"><?= $s['answered_questions'] ?>/<?= $s['total_questions'] ?></div>
                        </td>
                        <td>
                            <div class="score-cell">
                                <?php if ($s['status'] === 'completed' && $s['total_score'] !== null): ?>
                                    <?php $sc = $s['admin_override_score'] ?? $s['total_score']; ?>
                                    <span class="score-val <?= $sc >= 80 ? 'high' : ($sc >= 50 ? 'mid' : 'low') ?>"><?= $sc ?></span>
                                <?php else: ?>
                                    <span style="color:#94a3b8">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="font-size: 0.8rem; color: #64748b;">
                            <?= date('d M Y', strtotime($s['created_at'])) ?>
                        </td>
                        <td style="text-align:right;">
                            <?php if ($s['status'] === 'completed'): ?>
                                <a href="<?= url('/crewing/interviews/review/' . $s['id']) ?>" class="action-link">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:#94a3b8;">Waiting</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Assign Interview Modal -->
<div class="modal-overlay" id="assignModal">
    <div class="modal-box">
        <h3><i class="fas fa-plus-circle"></i> Assign AI Interview</h3>
        <form action="<?= url('/crewing/interviews/assign') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Pilih Pelamar</label>
                <select name="application_id" required>
                    <option value="">— Pilih Pelamar —</option>
                    <?php foreach ($assignableApplicants as $app): ?>
                        <option value="<?= $app['application_id'] ?>">
                            <?= htmlspecialchars($app['full_name']) ?> — <?= htmlspecialchars($app['vacancy_title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Pertanyaan Soal</label>
                <select name="question_bank_id" required>
                    <option value="">— Pilih Pertanyaan Soal —</option>
                    <?php foreach ($questionBanks as $qb): ?>
                        <option value="<?= $qb['id'] ?>"><?= htmlspecialchars($qb['name']) ?> (<?= $qb['question_count'] ?> questions)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kadaluarsa Dalam (hari)</label>
                <input type="number" name="expiry_days" value="7" min="1" max="30">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="document.getElementById('assignModal').classList.remove('active')">Batal</button>
                <button type="submit" class="btn-modal-submit"><i class="fas fa-paper-plane"></i> Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
// Close modal on outside click
document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
</script>
