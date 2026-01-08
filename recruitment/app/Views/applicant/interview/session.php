<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Session - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0A2463;
            --primary-light: #1E5AA8;
            --secondary: #D4AF37;
            --success: #28A745;
            --warning: #FFC107;
            --danger: #DC3545;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; min-height: 100vh; }
        .interview-container { max-width: 900px; margin: 0 auto; padding: 30px 20px; }
        .interview-header { background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; }
        .header-info h1 { font-size: 20px; color: #1a1a2e; margin-bottom: 5px; }
        .header-info p { color: #6c757d; font-size: 14px; }
        .timer-box { text-align: center; }
        .timer-label { font-size: 12px; color: #6c757d; margin-bottom: 5px; }
        .timer { font-size: 32px; font-weight: 700; color: var(--primary); }
        .timer.warning { color: var(--danger); animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .progress-bar { background: #e9ecef; height: 8px; border-radius: 4px; margin-bottom: 25px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--primary-light)); transition: width 0.3s ease; }
        .question-card { background: white; border-radius: 12px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .question-number { background: var(--primary); color: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; display: inline-block; margin-bottom: 20px; }
        .question-text { font-size: 20px; color: #1a1a2e; line-height: 1.6; margin-bottom: 30px; }
        .answer-area textarea { width: 100%; min-height: 200px; padding: 20px; border: 2px solid #e0e0e0; border-radius: 12px; font-family: inherit; font-size: 16px; line-height: 1.6; resize: vertical; transition: all 0.3s ease; }
        .answer-area textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(10, 36, 99, 0.1); }
        .word-count { text-align: right; font-size: 13px; color: #6c757d; margin-top: 10px; }
        .options-list { display: flex; flex-direction: column; gap: 15px; }
        .option-item { display: flex; align-items: center; gap: 15px; padding: 18px 20px; border: 2px solid #e0e0e0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
        .option-item:hover { border-color: var(--primary-light); background: #f8faff; }
        .option-item.selected { border-color: var(--primary); background: #f0f4ff; }
        .option-item input { display: none; }
        .option-radio { width: 24px; height: 24px; border: 2px solid #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .option-item.selected .option-radio { border-color: var(--primary); background: var(--primary); }
        .option-item.selected .option-radio::after { content: ''; width: 8px; height: 8px; background: white; border-radius: 50%; }
        .option-text { font-size: 15px; color: #333; }
        .actions { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 25px; border-top: 1px solid #f0f0f0; }
        .btn { display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; border: none; border-radius: 8px; font-family: inherit; font-size: 15px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(10, 36, 99, 0.3); }
        .btn-outline { background: transparent; border: 2px solid #ddd; color: #666; }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .questions-nav { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px; }
        .nav-dot { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 500; color: #666; }
        .nav-dot.answered { background: var(--success); border-color: var(--success); color: white; }
        .nav-dot.current { background: var(--primary); border-color: var(--primary); color: white; }
    </style>
</head>
<body>
    <div class="interview-container">
        <div class="interview-header">
            <div class="header-info">
                <h1><?= htmlspecialchars($session['vacancy_title']) ?></h1>
                <p><?= htmlspecialchars($session['question_bank_name'] ?? 'AI Interview') ?></p>
            </div>
            <div class="timer-box">
                <div class="timer-label">Time Remaining</div>
                <div class="timer" id="timer"><?= sprintf('%02d:%02d', floor(($currentQuestion['time_limit_seconds'] ?? 180) / 60), ($currentQuestion['time_limit_seconds'] ?? 180) % 60) ?></div>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= (($currentIndex) / count($questions)) * 100 ?>%"></div>
        </div>

        <!-- Question Navigation -->
        <div class="questions-nav">
            <?php foreach ($questions as $idx => $q): ?>
                <div class="nav-dot <?= in_array($q['id'], $answeredIds) ? 'answered' : '' ?> <?= $idx === $currentIndex ? 'current' : '' ?>">
                    <?= $idx + 1 ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Question Card -->
        <div class="question-card">
            <span class="question-number">Question <?= $currentIndex + 1 ?> of <?= count($questions) ?></span>
            
            <div class="question-text">
                <?= nl2br(htmlspecialchars($currentQuestion['question_text'])) ?>
            </div>

            <form action="<?= url('/applicant/interview/submit') ?>" method="POST" id="answerForm">
                <?= csrf_field() ?>
                <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                <input type="hidden" name="question_id" value="<?= $currentQuestion['id'] ?>">
                <input type="hidden" name="time_taken" id="timeTaken" value="0">

                <?php if ($currentQuestion['question_type'] === 'multiple_choice'): ?>
                    <?php $options = json_decode($currentQuestion['options'] ?? '[]', true) ?: []; ?>
                    <div class="options-list">
                        <?php foreach ($options as $idx => $option): ?>
                            <label class="option-item" onclick="selectOption(this)">
                                <input type="radio" name="selected_option" value="<?= htmlspecialchars($option) ?>" required>
                                <div class="option-radio"></div>
                                <span class="option-text"><?= htmlspecialchars($option) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="answer-area">
                        <textarea name="answer_text" id="answerText" placeholder="Type your answer here..." required><?= old('answer_text') ?></textarea>
                        <div class="word-count">
                            <span id="wordCount">0</span> words (minimum: <?= $currentQuestion['min_word_count'] ?? 50 ?>)
                        </div>
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <span></span>
                    <button type="submit" class="btn btn-primary">
                        <?php if ($currentIndex + 1 >= count($questions)): ?>
                            <i class="fas fa-check"></i> Finish Interview
                        <?php else: ?>
                            Next Question <i class="fas fa-arrow-right"></i>
                        <?php endif; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Timer
        let timeLimit = <?= $currentQuestion['time_limit_seconds'] ?? 180 ?>;
        let timeSpent = 0;
        const timerEl = document.getElementById('timer');
        const timeTakenInput = document.getElementById('timeTaken');

        const timerInterval = setInterval(function() {
            timeSpent++;
            timeLimit--;
            timeTakenInput.value = timeSpent;

            const mins = Math.floor(timeLimit / 60);
            const secs = timeLimit % 60;
            timerEl.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

            if (timeLimit <= 30) {
                timerEl.classList.add('warning');
            }

            if (timeLimit <= 0) {
                clearInterval(timerInterval);
                document.getElementById('answerForm').submit();
            }
        }, 1000);

        // Word count
        const answerText = document.getElementById('answerText');
        const wordCountEl = document.getElementById('wordCount');

        if (answerText) {
            answerText.addEventListener('input', function() {
                const words = this.value.trim().split(/\s+/).filter(w => w.length > 0);
                wordCountEl.textContent = words.length;
            });
        }

        // Select option
        function selectOption(element) {
            document.querySelectorAll('.option-item').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            element.querySelector('input').checked = true;
        }

        // Prevent leaving page
        window.onbeforeunload = function() {
            return "Are you sure you want to leave? Your progress may be lost.";
        };

        document.getElementById('answerForm').onsubmit = function() {
            window.onbeforeunload = null;
        };
    </script>
</body>
</html>
