<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question Bank - Admin | PT Indo Ocean Crew</title>
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
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link active"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
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
                <a href="<?= url('/admin/interviews') ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </header>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-question-circle"></i> <?= htmlspecialchars($bank['name']) ?></h1>
                <span class="badge badge-info"><?= count($questions) ?> Questions</span>
            </div>

            <!-- Add Question Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-plus"></i> Add New Question</h3>
                </div>
                <div class="card-body">
                    <form action="<?= url('/admin/interviews/questions/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add_question">
                        <input type="hidden" name="question_bank_id" value="<?= $bank['id'] ?>">
                        
                        <div class="form-row">
                            <div class="form-group col-8">
                                <label>Question Text (English) <span class="required">*</span></label>
                                <textarea name="question_text" class="form-control" rows="3" required placeholder="Enter the question..."></textarea>
                            </div>
                            <div class="form-group col-4">
                                <label>Question Type</label>
                                <select name="question_type" class="form-control" id="questionType" onchange="toggleQuestionOptions()">
                                    <option value="text">Text Answer</option>
                                    <option value="multiple_choice">Multiple Choice</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Question Text (Indonesian)</label>
                            <textarea name="question_text_id" class="form-control" rows="2" placeholder="Terjemahan bahasa Indonesia..."></textarea>
                        </div>
                        
                        <!-- Multiple Choice Options -->
                        <div id="multipleChoiceOptions" style="display: none;">
                            <div class="form-group">
                                <label>Answer Options</label>
                                <textarea name="options" class="form-control" rows="4" placeholder="Enter each option on a new line:&#10;Option A&#10;Option B&#10;Option C&#10;Option D"></textarea>
                                <small class="form-text">One option per line</small>
                            </div>
                            <div class="form-group">
                                <label>Correct Answer</label>
                                <input type="text" name="correct_answer" class="form-control" placeholder="Enter the exact correct option text">
                            </div>
                        </div>
                        
                        <!-- Text Answer Options -->
                        <div id="textAnswerOptions">
                            <div class="form-group">
                                <label>Expected Keywords</label>
                                <input type="text" name="expected_keywords" class="form-control" placeholder="safety, navigation, protocol, communication">
                                <small class="form-text">Comma-separated keywords for AI scoring</small>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <label>Min Word Count</label>
                                    <input type="number" name="min_word_count" class="form-control" value="50">
                                </div>
                                <div class="form-group col-4">
                                    <label>Time Limit (seconds)</label>
                                    <input type="number" name="time_limit_seconds" class="form-control" value="180">
                                </div>
                                <div class="form-group col-4">
                                    <label>Max Score</label>
                                    <input type="number" name="max_score" class="form-control" value="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Questions List -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Questions in this Bank</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($questions)): ?>
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>No Questions Yet</h3>
                            <p>Add your first question using the form above.</p>
                        </div>
                    <?php else: ?>
                        <div class="questions-list">
                            <?php foreach ($questions as $index => $q): ?>
                                <div class="question-item">
                                    <div class="question-number"><?= $index + 1 ?></div>
                                    <div class="question-content">
                                        <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
                                        <div class="question-meta">
                                            <span class="badge badge-<?= $q['question_type'] === 'multiple_choice' ? 'warning' : 'info' ?>">
                                                <?= ucfirst(str_replace('_', ' ', $q['question_type'])) ?>
                                            </span>
                                            <span><i class="fas fa-clock"></i> <?= $q['time_limit_seconds'] ?>s</span>
                                            <span><i class="fas fa-star"></i> <?= $q['max_score'] ?> pts</span>
                                        </div>
                                    </div>
                                    <div class="question-actions">
                                        <form action="<?= url('/admin/interviews/questions/delete/' . $q['id']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Delete this question?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
    .form-row { display: flex; gap: 20px; margin-bottom: 1rem; }
    .form-group { margin-bottom: 1.5rem; flex: 1; }
    .col-4 { flex: 0 0 30%; }
    .col-8 { flex: 0 0 65%; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1a2e; }
    .form-group .required { color: #dc3545; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
    .form-control:focus { outline: none; border-color: #0A2463; box-shadow: 0 0 0 3px rgba(10,36,99,0.1); }
    .form-text { font-size: 12px; color: #666; margin-top: 5px; }
    .form-actions { display: flex; gap: 15px; justify-content: flex-end; margin-top: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    
    .questions-list { display: flex; flex-direction: column; gap: 15px; }
    .question-item { display: flex; gap: 15px; padding: 20px; background: #f8f9fa; border-radius: 10px; border: 1px solid #e0e0e0; align-items: flex-start; }
    .question-number { width: 40px; height: 40px; background: #0A2463; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    .question-content { flex: 1; }
    .question-text { font-size: 15px; color: #1a1a2e; margin-bottom: 10px; line-height: 1.6; }
    .question-meta { display: flex; gap: 15px; align-items: center; }
    .question-meta span { font-size: 12px; color: #666; display: flex; align-items: center; gap: 5px; }
    .question-actions { flex-shrink: 0; }
    
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-info { background: #0A246320; color: #0A2463; }
    .badge-warning { background: #f39c1220; color: #f39c12; }
    </style>
    
    <script>
    function toggleQuestionOptions() {
        const type = document.getElementById('questionType').value;
        document.getElementById('multipleChoiceOptions').style.display = type === 'multiple_choice' ? 'block' : 'none';
        document.getElementById('textAnswerOptions').style.display = type === 'text' ? 'block' : 'none';
    }
    </script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
