<style>
    /* ===== Variables ===== */
    :root {
        --primary: #6366f1;
        --primary-light: rgba(99,102,241,0.08);
        --primary-dark: #4f46e5;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --text: #1a1a2e;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --bg: #f8fafc;
        --radius: 16px;
    }

    /* ===== Hero Banner ===== */
    .qb-hero {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .qb-hero::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -5%;
        width: 280px;
        height: 280px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .qb-hero-text { position: relative; z-index: 1; }
    .qb-hero h1 {
        color: white;
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0 0 0.4rem 0;
    }
    .qb-hero p {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
        margin: 0;
    }
    .qb-hero .hero-actions {
        display: flex;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }
    .qb-hero-icon { font-size: 3.5rem; color: rgba(255,255,255,0.12); position: relative; z-index: 1; }

    .btn-hero {
        padding: 0.6rem 1.2rem;
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        backdrop-filter: blur(4px);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-hero:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-1px);
    }

    /* ===== Layout Grid ===== */
    .qb-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 900px) {
        .qb-grid { grid-template-columns: 1fr; }
    }

    /* ===== Sidebar Panel ===== */
    .qb-sidebar {
        background: white;
        border-radius: var(--radius);
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .qb-sidebar-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .qb-sidebar-header h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .qb-bank-list {
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 500px;
        overflow-y: auto;
    }
    .qb-bank-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        text-decoration: none;
        color: var(--text);
        transition: all 0.15s;
    }
    .qb-bank-item:hover { background: var(--bg); }
    .qb-bank-item.active {
        background: var(--primary-light);
        border-left: 3px solid var(--primary);
    }
    .qb-bank-item .bank-name {
        font-size: 0.85rem;
        font-weight: 600;
    }
    .qb-bank-item .bank-count {
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 20px;
        font-weight: 700;
    }
    .qb-bank-empty {
        padding: 2rem;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    /* ===== Main Content ===== */
    .qb-main {
        background: white;
        border-radius: var(--radius);
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .qb-main-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .qb-main-header h3 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .qb-main-header h3 i { color: var(--primary); font-size: 0.9rem; }
    .qb-main-header .bank-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-add-q {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .btn-add-q:hover { box-shadow: 0 4px 12px rgba(99,102,241,0.3); }

    .btn-delete-bank {
        padding: 0.5rem 0.75rem;
        background: rgba(239,68,68,0.08);
        color: var(--danger);
        border: none;
        border-radius: 10px;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .btn-delete-bank:hover { background: rgba(239,68,68,0.15); }

    /* ===== Question Cards ===== */
    .qb-questions {
        padding: 1rem 1.5rem;
    }
    .q-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
        position: relative;
    }
    .q-card:hover {
        border-color: rgba(99,102,241,0.3);
        box-shadow: 0 2px 8px rgba(99,102,241,0.06);
    }
    .q-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    .q-card-header .q-num {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--primary);
        padding: 0.2rem 0.6rem;
        background: var(--primary-light);
        border-radius: 8px;
    }
    .q-card-header .q-type {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        background: #dbeafe;
        color: #1e40af;
    }
    .q-card-header .q-type.essay { background: #f0fdf4; color: #166534; }
    .q-card-header .q-type.multiple_choice { background: #fef3c7; color: #92400e; }

    .q-text {
        font-size: 0.9rem;
        color: var(--text);
        line-height: 1.6;
        margin-bottom: 0.75rem;
    }
    .q-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .q-meta-item {
        font-size: 0.7rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .q-meta-item i { font-size: 0.65rem; }

    .q-card-actions {
        display: flex;
        gap: 0.35rem;
    }
    .q-btn {
        padding: 0.3rem 0.6rem;
        border: none;
        border-radius: 6px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .q-btn-edit { background: var(--primary-light); color: var(--primary); }
    .q-btn-edit:hover { background: var(--primary); color: white; }
    .q-btn-del { background: rgba(239,68,68,0.08); color: var(--danger); }
    .q-btn-del:hover { background: var(--danger); color: white; }

    .qb-empty-qs {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }
    .qb-empty-qs i {
        font-size: 2.5rem;
        color: #cbd5e1;
        display: block;
        margin-bottom: 0.75rem;
    }

    .qb-select-prompt {
        padding: 4rem 2rem;
        text-align: center;
        color: #94a3b8;
    }
    .qb-select-prompt i {
        font-size: 3rem;
        color: #cbd5e1;
        display: block;
        margin-bottom: 1rem;
    }
    .qb-select-prompt p { font-size: 0.9rem; }

    /* ===== Modal ===== */
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
        width: 560px;
        max-width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalSlideUp 0.3s ease;
    }
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-box h3 {
        font-size: 1.15rem;
        color: var(--text);
        margin: 0 0 1.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .modal-box h3 i { color: var(--primary); }

    .form-group {
        margin-bottom: 1rem;
    }
    .form-group label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.35rem;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.85rem;
        font-family: inherit;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .form-group .hint {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0.75rem;
    }

    .modal-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    .btn-cancel {
        padding: 0.5rem 1rem;
        background: #f1f5f9;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        cursor: pointer;
        color: var(--text-muted);
    }
    .btn-cancel:hover { background: #e2e8f0; }
    .btn-submit {
        padding: 0.5rem 1.25rem;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .btn-submit:hover { box-shadow: 0 4px 15px rgba(99,102,241,0.3); }

    .back-link-small {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--primary);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1rem;
    }
    .back-link-small:hover { text-decoration: underline; }

    /* Options toggle */
    .options-group { display: none; }
    .options-group.visible { display: block; }
</style>

<!-- Back Link -->
<a href="<?= url('/crewing/interviews') ?>" class="back-link-small">
    <i class="fas fa-arrow-left"></i> Back to AI Interviews
</a>

<!-- Hero -->
<div class="qb-hero">
    <div class="qb-hero-text">
        <h1><i class="fas fa-database"></i> Question Banks</h1>
        <p>Create and manage interview question banks. Add questions with different types, scoring, and time limits.</p>
    </div>
    <div style="display:flex;align-items:center;gap:1.5rem;">
        <button class="btn-hero" onclick="openModal('createBankModal')">
            <i class="fas fa-plus"></i> New Question Bank
        </button>
        <div class="qb-hero-icon"><i class="fas fa-layer-group"></i></div>
    </div>
</div>

<!-- Grid Layout -->
<div class="qb-grid">
    <!-- Sidebar: Bank List -->
    <div class="qb-sidebar">
        <div class="qb-sidebar-header">
            <h3><i class="fas fa-folder"></i> Banks (<?= count($banks) ?>)</h3>
        </div>
        <?php if (empty($banks)): ?>
            <div class="qb-bank-empty">
                <i class="fas fa-inbox"></i>
                <p>No question banks yet.<br>Create one to get started.</p>
            </div>
        <?php else: ?>
            <ul class="qb-bank-list">
                <?php foreach ($banks as $b): ?>
                    <a href="<?= url('/crewing/interviews/questions?bank_id=' . $b['id']) ?>" 
                       class="qb-bank-item <?= ($selectedBankId ?? '') == $b['id'] ? 'active' : '' ?>">
                        <div>
                            <div class="bank-name"><?= htmlspecialchars($b['name']) ?></div>
                            <?php if (!empty($b['description'])): ?>
                                <div style="font-size:0.7rem;color:#94a3b8;margin-top:0.15rem;"><?= htmlspecialchars(mb_strimwidth($b['description'], 0, 50, '...')) ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="bank-count"><?= $b['question_count'] ?></span>
                    </a>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Main: Questions List -->
    <div class="qb-main">
        <?php if ($selectedBank): ?>
            <div class="qb-main-header">
                <h3><i class="fas fa-list-ol"></i> <?= htmlspecialchars($selectedBank['name']) ?></h3>
                <div class="bank-actions">
                    <button class="btn-add-q" onclick="openModal('addQuestionModal')">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                    <form action="<?= url('/crewing/interviews/deleteBank/' . $selectedBank['id']) ?>" method="POST" style="display:inline;"
                          onsubmit="return confirm('Delete this bank and all its questions?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-delete-bank" title="Delete Bank">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="qb-questions">
                <?php if (empty($questions)): ?>
                    <div class="qb-empty-qs">
                        <i class="fas fa-comments"></i>
                        <p>No questions in this bank yet.</p>
                        <button class="btn-add-q" style="margin-top:0.75rem;" onclick="openModal('addQuestionModal')">
                            <i class="fas fa-plus"></i> Add First Question
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($questions as $i => $q): ?>
                        <div class="q-card">
                            <div class="q-card-header">
                                <div style="display:flex;gap:0.5rem;align-items:center;">
                                    <span class="q-num"><i class="fas fa-hashtag"></i> <?= $i + 1 ?></span>
                                    <span class="q-type <?= $q['question_type'] ?>"><?= ucfirst(str_replace('_', ' ', $q['question_type'])) ?></span>
                                </div>
                                <div class="q-card-actions">
                                    <button class="q-btn q-btn-edit" onclick="editQuestion(<?= htmlspecialchars(json_encode($q)) ?>)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form action="<?= url('/crewing/interviews/deleteQuestion/' . $q['id']) ?>" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this question?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="q-btn q-btn-del"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <div class="q-text"><?= nl2br(htmlspecialchars($q['question_text'])) ?></div>
                            <div class="q-meta">
                                <span class="q-meta-item"><i class="fas fa-clock"></i> <?= ($q['time_limit_seconds'] ?? 180) ?>s</span>
                                <span class="q-meta-item"><i class="fas fa-star"></i> Max <?= $q['max_score'] ?? 10 ?> pts</span>
                                <?php if (!empty($q['expected_keywords'])): ?>
                                    <span class="q-meta-item"><i class="fas fa-key"></i> <?= htmlspecialchars(mb_strimwidth($q['expected_keywords'], 0, 40, '...')) ?></span>
                                <?php endif; ?>
                                <?php if ($q['question_type'] === 'essay'): ?>
                                    <span class="q-meta-item"><i class="fas fa-align-left"></i> Min <?= $q['min_word_count'] ?? 50 ?> words</span>
                                <?php endif; ?>
                                <?php if ($q['question_type'] === 'multiple_choice' && !empty($q['options'])): ?>
                                    <?php $opts = json_decode($q['options'], true); ?>
                                    <span class="q-meta-item"><i class="fas fa-list"></i> <?= count($opts) ?> options</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="qb-select-prompt">
                <i class="fas fa-hand-pointer"></i>
                <p>Select a question bank from the sidebar<br>or create a new one to get started.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== Create Bank Modal ===== -->
<div class="modal-overlay" id="createBankModal">
    <div class="modal-box">
        <h3><i class="fas fa-folder-plus"></i> Create Question Bank</h3>
        <form action="<?= url('/crewing/interviews/storeQuestion') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_bank">
            <div class="form-group">
                <label>Bank Name *</label>
                <input type="text" name="bank_name" required placeholder="e.g. Deck Officer Assessment">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Brief description of this question bank..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('createBankModal')">Cancel</button>
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Create</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Add Question Modal ===== -->
<?php if ($selectedBank): ?>
<div class="modal-overlay" id="addQuestionModal">
    <div class="modal-box">
        <h3><i class="fas fa-plus-circle"></i> Add Question</h3>
        <form action="<?= url('/crewing/interviews/storeQuestion') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add_question">
            <input type="hidden" name="question_bank_id" value="<?= $selectedBank['id'] ?>">
            
            <div class="form-group">
                <label>Question Type *</label>
                <select name="question_type" id="addQType" onchange="toggleOptions('add')">
                    <option value="essay">Essay (Free Text)</option>
                    <option value="multiple_choice">Multiple Choice</option>
                </select>
            </div>
            <div class="form-group">
                <label>Question Text *</label>
                <textarea name="question_text" rows="3" required placeholder="Enter your question here..."></textarea>
            </div>
            <div class="form-group options-group" id="addOptionsGroup">
                <label>Options (one per line)</label>
                <textarea name="options" rows="4" placeholder="Option A&#10;Option B&#10;Option C&#10;Option D"></textarea>
            </div>
            <div class="form-group options-group" id="addCorrectGroup">
                <label>Correct Answer</label>
                <input type="text" name="correct_answer" placeholder="e.g. Option A">
            </div>
            <div class="form-group">
                <label>Expected Keywords</label>
                <input type="text" name="expected_keywords" placeholder="keyword1, keyword2, keyword3">
                <div class="hint">Comma-separated keywords for AI scoring (essay questions)</div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label>Time Limit (sec)</label>
                    <input type="number" name="time_limit_seconds" value="180" min="30" max="600">
                </div>
                <div class="form-group">
                    <label>Max Score</label>
                    <input type="number" name="max_score" value="10" min="1" max="100">
                </div>
                <div class="form-group">
                    <label>Min Words</label>
                    <input type="number" name="min_word_count" value="50" min="0" max="500">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addQuestionModal')">Cancel</button>
                <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Add Question</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Edit Question Modal ===== -->
<div class="modal-overlay" id="editQuestionModal">
    <div class="modal-box">
        <h3><i class="fas fa-edit"></i> Edit Question</h3>
        <form action="<?= url('/crewing/interviews/storeQuestion') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="edit_question">
            <input type="hidden" name="question_id" id="editQId">
            <input type="hidden" name="question_bank_id" value="<?= $selectedBank['id'] ?>">
            
            <div class="form-group">
                <label>Question Type *</label>
                <select name="question_type" id="editQType" onchange="toggleOptions('edit')">
                    <option value="essay">Essay (Free Text)</option>
                    <option value="multiple_choice">Multiple Choice</option>
                </select>
            </div>
            <div class="form-group">
                <label>Question Text *</label>
                <textarea name="question_text" id="editQText" rows="3" required></textarea>
            </div>
            <div class="form-group options-group" id="editOptionsGroup">
                <label>Options (one per line)</label>
                <textarea name="options" id="editQOptions" rows="4"></textarea>
            </div>
            <div class="form-group options-group" id="editCorrectGroup">
                <label>Correct Answer</label>
                <input type="text" name="correct_answer" id="editQCorrect">
            </div>
            <div class="form-group">
                <label>Expected Keywords</label>
                <input type="text" name="expected_keywords" id="editQKeywords">
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label>Time Limit (sec)</label>
                    <input type="number" name="time_limit_seconds" id="editQTime" min="30" max="600">
                </div>
                <div class="form-group">
                    <label>Max Score</label>
                    <input type="number" name="max_score" id="editQScore" min="1" max="100">
                </div>
                <div class="form-group">
                    <label>Min Words</label>
                    <input type="number" name="min_word_count" id="editQWords" min="0" max="500">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('editQuestionModal')">Cancel</button>
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Close modals on outside click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', e => {
        if (e.target === modal) modal.classList.remove('active');
    });
});

function toggleOptions(prefix) {
    const type = document.getElementById(prefix + 'QType').value;
    const show = type === 'multiple_choice';
    document.getElementById(prefix + 'OptionsGroup').classList.toggle('visible', show);
    document.getElementById(prefix + 'CorrectGroup').classList.toggle('visible', show);
}

function editQuestion(q) {
    document.getElementById('editQId').value = q.id;
    document.getElementById('editQType').value = q.question_type;
    document.getElementById('editQText').value = q.question_text;
    document.getElementById('editQKeywords').value = q.expected_keywords || '';
    document.getElementById('editQTime').value = q.time_limit_seconds || 180;
    document.getElementById('editQScore').value = q.max_score || 10;
    document.getElementById('editQWords').value = q.min_word_count || 50;
    
    // Handle options for multiple choice
    if (q.options) {
        try {
            const opts = JSON.parse(q.options);
            document.getElementById('editQOptions').value = opts.join('\n');
        } catch(e) {
            document.getElementById('editQOptions').value = q.options;
        }
    } else {
        document.getElementById('editQOptions').value = '';
    }
    document.getElementById('editQCorrect').value = q.correct_answer || '';
    
    toggleOptions('edit');
    openModal('editQuestionModal');
}
</script>
