<style>
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .review-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 250px;
        height: 250px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .review-header .meta { position: relative; z-index: 1; }
    .review-header .meta h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .review-header .meta p {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }
    .review-header .tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .review-header .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.7rem;
        background: rgba(255,255,255,0.15);
        border-radius: 20px;
        font-size: 0.7rem;
    }

    .score-circle {
        position: relative;
        z-index: 1;
        width: 130px; height: 130px;
        text-align: center;
    }
    .score-circle svg { width: 100%; height: 100%; }
    .score-circle .bg {
        fill: none;
        stroke: rgba(255,255,255,0.15);
        stroke-width: 8;
    }
    .score-circle .progress-ring {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: center;
    }
    .score-circle .value {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
    }
    .score-circle .num {
        font-size: 2rem;
        font-weight: 800;
        display: block;
    }
    .score-circle .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0.8;
    }

    /* Back Link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #6366f1;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1.5rem;
    }
    .back-link:hover { text-decoration: underline; }

    /* Answer Cards */
    .answer-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        border-left: 4px solid transparent;
        transition: border-color 0.2s;
    }
    .answer-card.high-score { border-left-color: #10b981; }
    .answer-card.mid-score { border-left-color: #f59e0b; }
    .answer-card.low-score { border-left-color: #ef4444; }

    .answer-card .q-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .answer-card .q-num {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        background: rgba(99,102,241,0.08);
        color: #6366f1;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .answer-card .q-score {
        font-size: 1.2rem;
        font-weight: 700;
    }
    .answer-card .q-text {
        font-size: 0.95rem;
        color: #1f2937;
        line-height: 1.6;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .answer-card .a-text {
        font-size: 0.9rem;
        color: #374151;
        line-height: 1.7;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    .answer-card .feedback-section {
        padding: 1rem;
        background: rgba(99,102,241,0.04);
        border-radius: 10px;
        border: 1px solid rgba(99,102,241,0.1);
    }
    .answer-card .feedback-section h5 {
        font-size: 0.75rem;
        color: #6366f1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .answer-card .feedback-section p {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.6;
    }
    .keyword-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-top: 0.75rem;
    }
    .keyword-tag {
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .keyword-tag.matched { background: #d1fae5; color: #065f46; }
    .keyword-tag.missed { background: #fee2e2; color: #991b1b; }

    /* Override Form */
    .override-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .override-section h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .override-section h3 i { color: #6366f1; }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .form-group {
        margin-bottom: 0.75rem;
    }
    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.85rem;
        font-family: inherit;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .btn-save {
        padding: 0.625rem 1.5rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-save:hover { box-shadow: 0 4px 15px rgba(99,102,241,0.3); }

    .btn-reset {
        padding: 0.625rem 1.5rem;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        margin-left: 0.5rem;
    }
    .btn-reset:hover { background: #dc2626; }

    @media (max-width: 768px) {
        .review-header { flex-direction: column; gap: 1.5rem; text-align: center; }
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<a href="<?= url('/crewing/interviews') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Interviews
</a>

<!-- Review Header with Score -->
<?php 
$score = $session['admin_override_score'] ?? $session['total_score'] ?? 0;
$circumference = 2 * M_PI * 50;
$offset = $circumference - ($score / 100) * $circumference;
$strokeColor = $score >= 80 ? '#10b981' : ($score >= 50 ? '#f59e0b' : '#ef4444');
?>
<div class="review-header">
    <div class="meta">
        <h2><i class="fas fa-user"></i> <?= htmlspecialchars($session['full_name']) ?></h2>
        <p><?= htmlspecialchars($session['vacancy_title']) ?> — <?= htmlspecialchars($session['question_bank_name']) ?></p>
        <div class="tags">
            <span class="tag"><i class="fas fa-calendar"></i> <?= $session['completed_at'] ? date('d M Y H:i', strtotime($session['completed_at'])) : 'Not completed' ?></span>
            <?php if (!empty($session['ai_recommendation'])): ?>
            <span class="tag"><i class="fas fa-robot"></i> AI: <?= ucfirst($session['ai_recommendation']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="score-circle">
        <svg viewBox="0 0 120 120">
            <circle class="bg" cx="60" cy="60" r="50"/>
            <circle class="progress-ring" cx="60" cy="60" r="50" 
                stroke="<?= $strokeColor ?>" 
                stroke-dasharray="<?= $circumference ?>" 
                stroke-dashoffset="<?= $offset ?>"/>
        </svg>
        <div class="value">
            <span class="num"><?= $score ?></span>
            <span class="label">AI Score</span>
        </div>
    </div>
</div>

<!-- Answer Cards -->
<?php foreach ($answers as $i => $a): ?>
    <?php 
    $ansScore = $a['ai_score'] ?? 0;
    $maxScore = $a['max_score'] ?? 10;
    $pct = $maxScore > 0 ? ($ansScore / $maxScore) * 100 : 0;
    $scoreClass = $pct >= 80 ? 'high-score' : ($pct >= 50 ? 'mid-score' : 'low-score');
    ?>
    <div class="answer-card <?= $scoreClass ?>">
        <div class="q-header">
            <span class="q-num"><i class="fas fa-hashtag"></i> Question <?= $i + 1 ?></span>
            <span class="q-score" style="color:<?= $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444') ?>">
                <?= $ansScore ?>/<?= $maxScore ?>
            </span>
        </div>
        <div class="q-text"><?= nl2br(htmlspecialchars($a['question_text'])) ?></div>
        <div class="a-text"><?= nl2br(htmlspecialchars($a['answer_text'] ?? '—')) ?></div>
        
        <?php if (!empty($a['ai_feedback'])): ?>
            <div class="feedback-section">
                <h5><i class="fas fa-robot"></i> AI Feedback</h5>
                <p><?= nl2br(htmlspecialchars($a['ai_feedback'])) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($a['expected_keywords'])): ?>
            <?php $expected = array_map('trim', explode(',', $a['expected_keywords'])); ?>
            <?php $answerLower = strtolower($a['answer_text'] ?? ''); ?>
            <div class="keyword-tags">
                <?php foreach ($expected as $kw): ?>
                    <?php $found = stripos($answerLower, strtolower($kw)) !== false; ?>
                    <span class="keyword-tag <?= $found ? 'matched' : 'missed' ?>">
                        <i class="fas fa-<?= $found ? 'check' : 'times' ?>"></i> <?= htmlspecialchars($kw) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<!-- Override / Manual Score Section -->
<div class="override-section">
    <h3><i class="fas fa-edit"></i> Manual Score & Notes</h3>
    <form action="<?= url('/crewing/interviews/score/' . $session['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group">
                <label>Override Score (0-100)</label>
                <input type="number" name="override_score" value="<?= $session['admin_override_score'] ?? $session['total_score'] ?? '' ?>" min="0" max="100">
            </div>
            <div class="form-group">
                <label>Recommendation</label>
                <select name="recommendation">
                    <option value="">— Select —</option>
                    <option value="proceed" <?= ($session['ai_recommendation'] ?? '') === 'proceed' ? 'selected' : '' ?>>Proceed</option>
                    <option value="review" <?= ($session['ai_recommendation'] ?? '') === 'review' ? 'selected' : '' ?>>Needs Review</option>
                    <option value="reject" <?= ($session['ai_recommendation'] ?? '') === 'reject' ? 'selected' : '' ?>>Reject</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="admin_notes" rows="3" placeholder="Add your notes here..."><?= htmlspecialchars($session['admin_notes'] ?? '') ?></textarea>
        </div>
        <div style="display:flex; align-items:center;">
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save</button>
        </div>
    </form>
    
    <form action="<?= url('/crewing/interviews/reset/' . $session['id']) ?>" method="POST" style="margin-top: 1rem;" onsubmit="return confirm('Reset this interview? The applicant will need to retake it.');">
        <?= csrf_field() ?>
        <button type="submit" class="btn-reset"><i class="fas fa-redo"></i> Reset for Retry</button>
    </form>
</div>
