<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Interview Session — PT Indo Ocean Crew Services</title>
    <link rel="icon" type="image/jpeg" href="<?= asset('images/logo.jpg') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.08);
            --accent: #6366f1;
            --accent-glow: rgba(99,102,241,0.3);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-primary: #f9fafb;
            --text-secondary: rgba(255,255,255,0.5);
            --glass: rgba(255,255,255,0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            pointer-events: none;
        }
        .bg-animation .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: orbFloat 15s ease-in-out infinite;
        }
        .bg-animation .orb-1 {
            width: 400px; height: 400px;
            background: var(--accent);
            top: -100px; right: -100px;
            animation-delay: 0s;
        }
        .bg-animation .orb-2 {
            width: 300px; height: 300px;
            background: #8b5cf6;
            bottom: -50px; left: -50px;
            animation-delay: -5s;
        }
        .bg-animation .orb-3 {
            width: 200px; height: 200px;
            background: #06b6d4;
            top: 50%; left: 50%;
            animation-delay: -10s;
        }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Grid Pattern */
        .bg-grid {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }

        /* Main Container */
        .interview-app {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 1.5rem;
        }
        .top-bar .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .top-bar .brand .logo-circle {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .top-bar .brand span {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .top-bar .vacancy-name {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-align: center;
        }

        /* Timer Section */
        .timer-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .timer-ring {
            width: 50px; height: 50px;
            position: relative;
        }
        .timer-ring svg {
            width: 100%; height: 100%;
            transform: rotate(-90deg);
        }
        .timer-ring .bg { fill: none; stroke: rgba(255,255,255,0.08); stroke-width: 4; }
        .timer-ring .progress {
            fill: none;
            stroke: var(--accent);
            stroke-width: 4;
            stroke-linecap: round;
            transition: stroke-dashoffset 1s linear, stroke 0.3s;
        }
        .timer-ring .time-text {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.65rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }
        .timer-ring.warning .progress { stroke: var(--danger); }
        .timer-ring.warning .time-text { color: var(--danger); animation: blinkText 1s infinite; }
        @keyframes blinkText { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

        /* Progress Section */
        .progress-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 14px;
            margin-bottom: 1.5rem;
        }
        .progress-dots {
            display: flex;
            gap: 0.5rem;
            flex: 1;
            flex-wrap: wrap;
        }
        .progress-dot {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            transition: all 0.3s;
        }
        .progress-dot.answered {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }
        .progress-dot.current {
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 0 20px var(--accent-glow);
            animation: glowPulse 2s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%,100%{ box-shadow: 0 0 10px var(--accent-glow); }
            50%{ box-shadow: 0 0 25px var(--accent-glow); }
        }
        .progress-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        /* AI Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* AI Message Bubble */
        .ai-bubble {
            display: flex;
            gap: 1rem;
            max-width: 85%;
        }
        .ai-avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 4px 15px var(--accent-glow);
        }
        .ai-content {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 0 18px 18px 18px;
            padding: 1.25rem 1.5rem;
            position: relative;
        }
        .ai-content .ai-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .ai-content .ai-label .pulse-dot {
            width: 6px; height: 6px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulseDot 2s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%,100%{opacity:1;transform:scale(1);}
            50%{opacity:0.5;transform:scale(1.5);}
        }
        .ai-content .question {
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text-primary);
            font-weight: 400;
        }
        .ai-content .q-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(99,102,241,0.15);
            color: var(--accent);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Typing Animation */
        .typing-indicator {
            display: flex;
            gap: 0.3rem;
            padding: 0.5rem 0;
        }
        .typing-indicator span {
            width: 8px; height: 8px;
            background: var(--text-secondary);
            border-radius: 50%;
            animation: typingBounce 1.4s ease-in-out infinite;
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%,60%,100%{transform:translateY(0);opacity:0.4;}
            30%{transform:translateY(-10px);opacity:1;}
        }

        /* Answer Section */
        .answer-section {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: auto;
        }
        .answer-section .section-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .answer-section .section-label i { color: var(--accent); }

        /* Textarea */
        .answer-textarea {
            width: 100%;
            min-height: 150px;
            background: rgba(255,255,255,0.03);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            line-height: 1.7;
            resize: vertical;
            transition: all 0.3s;
        }
        .answer-textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
            background: rgba(255,255,255,0.05);
        }
        .answer-textarea::placeholder { color: rgba(255,255,255,0.2); }

        /* Word Count & Meta */
        .answer-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
        }
        .word-counter {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .word-counter .count-ring {
            width: 36px; height: 36px;
            position: relative;
        }
        .word-counter .count-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .word-counter .count-ring .ring-bg { fill: none; stroke: rgba(255,255,255,0.08); stroke-width: 3; }
        .word-counter .count-ring .ring-fill {
            fill: none; stroke: var(--accent); stroke-width: 3;
            stroke-linecap: round; transition: all 0.3s;
        }
        .word-counter .count-ring .ring-fill.complete { stroke: var(--success); }
        .word-counter .count-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        .word-counter .count-text strong { color: var(--text-primary); }

        /* Options (Multiple Choice) */
        .options-grid {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .option-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.03);
            border: 2px solid var(--border);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .option-card:hover {
            border-color: rgba(99,102,241,0.3);
            background: rgba(99,102,241,0.05);
        }
        .option-card.selected {
            border-color: var(--accent);
            background: rgba(99,102,241,0.1);
            box-shadow: 0 0 20px var(--accent-glow);
        }
        .option-card input[type="radio"] { display: none; }
        .option-indicator {
            width: 24px; height: 24px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .option-card.selected .option-indicator {
            border-color: var(--accent);
            background: var(--accent);
        }
        .option-card.selected .option-indicator::after {
            content: ''; width: 8px; height: 8px;
            background: white; border-radius: 50%;
        }
        .option-label {
            font-size: 0.95rem;
            color: var(--text-primary);
            line-height: 1.5;
        }
        .option-key {
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.06);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-secondary);
            flex-shrink: 0;
        }

        /* Submit Button */
        .submit-area {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.25rem;
        }
        .btn-submit-ai {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: white;
            border: none;
            border-radius: 14px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-submit-ai::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 200%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .btn-submit-ai:hover::before { left: 100%; }
        .btn-submit-ai:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--accent-glow);
        }
        .btn-submit-ai:active { transform: translateY(0); }
        .btn-submit-ai .arrow { font-size: 1rem; transition: transform 0.2s; }
        .btn-submit-ai:hover .arrow { transform: translateX(4px); }

        /* Webcam Preview (cosmetic) */
        .webcam-preview {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            width: 160px;
            height: 120px;
            background: var(--bg-secondary);
            border-radius: 14px;
            border: 2px solid var(--border);
            overflow: hidden;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .webcam-preview video {
            width: 100%; height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }
        .webcam-preview .cam-off {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.7rem;
        }
        .webcam-preview .cam-off i { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .webcam-toggle {
            position: absolute;
            top: 6px; right: 6px;
            width: 24px; height: 24px;
            background: rgba(0,0,0,0.5);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 0.65rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Status Bar */
        .status-bar {
            display: flex;
            justify-content: center;
            padding: 0.5rem;
            margin-top: 1rem;
        }
        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 20px;
            font-size: 0.7rem;
            color: var(--success);
        }
        .status-chip .rec-dot {
            width: 6px; height: 6px;
            background: var(--success);
            border-radius: 50%;
            animation: pulseDot 2s ease-in-out infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .interview-app { padding: 1rem; }
            .top-bar { flex-direction: column; gap: 0.75rem; text-align: center; }
            .ai-bubble { max-width: 100%; }
            .webcam-preview { display: none; }
            .ai-content .question { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="bg-grid"></div>

    <div class="interview-app">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="brand">
                <div class="logo-circle"><i class="fas fa-robot"></i></div>
                <span>AI Interview</span>
            </div>
            <div class="vacancy-name">
                <?= htmlspecialchars($session['vacancy_title']) ?>
            </div>
            <div class="timer-section">
                <?php $timeLimit = $currentQuestion['time_limit_seconds'] ?? 180; ?>
                <div class="timer-ring" id="timerRing">
                    <svg viewBox="0 0 60 60">
                        <circle class="bg" cx="30" cy="30" r="26"/>
                        <circle class="progress" cx="30" cy="30" r="26" id="timerCircle"
                            stroke-dasharray="<?= 2 * M_PI * 26 ?>"
                            stroke-dashoffset="0"/>
                    </svg>
                    <div class="time-text" id="timerText">
                        <?= sprintf('%d:%02d', floor($timeLimit / 60), $timeLimit % 60) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Dots -->
        <div class="progress-section">
            <div class="progress-dots">
                <?php foreach ($questions as $idx => $q): ?>
                    <div class="progress-dot <?= in_array($q['id'], $answeredIds) ? 'answered' : '' ?> <?= $idx === $currentIndex ? 'current' : '' ?>">
                        <?php if (in_array($q['id'], $answeredIds)): ?>
                            <i class="fas fa-check" style="font-size:0.65rem;"></i>
                        <?php else: ?>
                            <?= $idx + 1 ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="progress-label">
                <?= count($answeredIds) ?>/<?= count($questions) ?> answered
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <!-- AI Question Bubble -->
            <div class="ai-bubble">
                <div class="ai-avatar">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="ai-content">
                    <div class="ai-label">
                        <span class="pulse-dot"></span>
                        AI Interviewer
                    </div>
                    <div class="q-badge">
                        <i class="fas fa-hashtag"></i>
                        Question <?= $currentIndex + 1 ?> of <?= count($questions) ?>
                        <?php if ($currentQuestion['question_type'] === 'multiple_choice'): ?>
                            &nbsp;·&nbsp;<i class="fas fa-list"></i> Multiple Choice
                        <?php else: ?>
                            &nbsp;·&nbsp;<i class="fas fa-pen"></i> Essay
                        <?php endif; ?>
                    </div>
                    <div class="question" id="questionText">
                        <?= nl2br(htmlspecialchars($currentQuestion['question_text'])) ?>
                    </div>
                </div>
            </div>

            <!-- Answer Section -->
            <div class="answer-section">
                <div class="section-label">
                    <i class="fas fa-microphone-alt"></i>
                    Your Response
                </div>

                <form action="<?= url('/applicant/interview/submit') ?>" method="POST" id="answerForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                    <input type="hidden" name="question_id" value="<?= $currentQuestion['id'] ?>">
                    <input type="hidden" name="time_taken" id="timeTaken" value="0">

                    <?php if ($currentQuestion['question_type'] === 'multiple_choice'): ?>
                        <?php $options = json_decode($currentQuestion['options'] ?? '[]', true) ?: []; ?>
                        <div class="options-grid">
                            <?php $keys = ['A','B','C','D','E','F']; ?>
                            <?php foreach ($options as $idx => $option): ?>
                                <label class="option-card" onclick="selectOption(this)">
                                    <input type="radio" name="selected_option" value="<?= htmlspecialchars($option) ?>" required>
                                    <div class="option-key"><?= $keys[$idx] ?? ($idx+1) ?></div>
                                    <div class="option-indicator"></div>
                                    <span class="option-label"><?= htmlspecialchars($option) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <textarea class="answer-textarea" name="answer_text" id="answerText" 
                            placeholder="Type your answer here... Be thorough and specific in your response." 
                            required><?= old('answer_text') ?></textarea>
                        
                        <div class="answer-meta">
                            <div class="word-counter">
                                <?php $minWords = $currentQuestion['min_word_count'] ?? 50; ?>
                                <div class="count-ring">
                                    <svg viewBox="0 0 40 40">
                                        <circle class="ring-bg" cx="20" cy="20" r="16"/>
                                        <circle class="ring-fill" id="wordRing" cx="20" cy="20" r="16"
                                            stroke-dasharray="<?= 2 * M_PI * 16 ?>"
                                            stroke-dashoffset="<?= 2 * M_PI * 16 ?>"/>
                                    </svg>
                                </div>
                                <div class="count-text">
                                    <strong id="wordCount">0</strong> / <?= $minWords ?> words
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="submit-area">
                        <button type="submit" class="btn-submit-ai" id="submitBtn">
                            <?php if ($currentIndex + 1 >= count($questions)): ?>
                                <i class="fas fa-flag-checkered"></i> Complete Interview
                            <?php else: ?>
                                <span>Next Question</span>
                                <i class="fas fa-arrow-right arrow"></i>
                            <?php endif; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="status-bar">
            <div class="status-chip">
                <span class="rec-dot"></span>
                Session Active · AI Monitoring
            </div>
        </div>
    </div>

    <!-- Webcam Preview -->
    <div class="webcam-preview" id="webcamPreview">
        <div class="cam-off" id="camOff">
            <i class="fas fa-video-slash"></i>
            <div>Camera</div>
        </div>
        <video id="webcamVideo" autoplay muted playsinline style="display:none;"></video>
        <button class="webcam-toggle" id="camToggle" title="Toggle camera">
            <i class="fas fa-video"></i>
        </button>
    </div>

    <script>
        // ============ Timer ============
        const timeLimit = <?= $currentQuestion['time_limit_seconds'] ?? 180 ?>;
        let remaining = timeLimit;
        let timeSpent = 0;
        const circumference = 2 * Math.PI * 26;
        const timerCircle = document.getElementById('timerCircle');
        const timerText = document.getElementById('timerText');
        const timerRing = document.getElementById('timerRing');
        const timeTakenInput = document.getElementById('timeTaken');

        const timerInterval = setInterval(function() {
            timeSpent++;
            remaining--;
            timeTakenInput.value = timeSpent;

            // Update circular progress
            const progress = (timeLimit - remaining) / timeLimit;
            timerCircle.style.strokeDashoffset = circumference * progress;

            // Update text
            const mins = Math.floor(remaining / 60);
            const secs = remaining % 60;
            timerText.textContent = mins + ':' + String(secs).padStart(2, '0');

            // Warning state
            if (remaining <= 30) {
                timerRing.classList.add('warning');
                timerCircle.style.stroke = '#ef4444';
            }

            // Auto submit
            if (remaining <= 0) {
                clearInterval(timerInterval);
                document.getElementById('answerForm').submit();
            }
        }, 1000);

        // ============ Word Count ============
        const answerText = document.getElementById('answerText');
        const wordCountEl = document.getElementById('wordCount');
        const wordRing = document.getElementById('wordRing');
        const minWords = <?= $currentQuestion['min_word_count'] ?? 50 ?>;
        const wordCircumference = 2 * Math.PI * 16;

        if (answerText) {
            answerText.addEventListener('input', function() {
                const words = this.value.trim().split(/\s+/).filter(w => w.length > 0);
                const count = words.length;
                wordCountEl.textContent = count;

                // Update ring
                const progress = Math.min(1, count / minWords);
                wordRing.style.strokeDashoffset = wordCircumference * (1 - progress);
                
                if (count >= minWords) {
                    wordRing.classList.add('complete');
                } else {
                    wordRing.classList.remove('complete');
                }
            });
        }

        // ============ Option Selection ============
        function selectOption(el) {
            document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            el.querySelector('input').checked = true;
        }

        // Keyboard shortcuts for options
        document.addEventListener('keydown', function(e) {
            const keyMap = {'a':0,'b':1,'c':2,'d':3,'e':4,'f':5};
            const idx = keyMap[e.key.toLowerCase()];
            if (idx !== undefined) {
                const cards = document.querySelectorAll('.option-card');
                if (cards[idx]) selectOption(cards[idx]);
            }
        });

        // ============ Prevent Leave ============
        window.onbeforeunload = function() {
            return "Are you sure you want to leave? Your progress may be lost.";
        };
        document.getElementById('answerForm').onsubmit = function() {
            window.onbeforeunload = null;
        };

        // ============ Webcam (optional cosmetic) ============
        let camActive = false;
        const camToggle = document.getElementById('camToggle');
        const webcamVideo = document.getElementById('webcamVideo');
        const camOff = document.getElementById('camOff');

        camToggle.addEventListener('click', async function() {
            if (camActive) {
                const stream = webcamVideo.srcObject;
                if (stream) stream.getTracks().forEach(t => t.stop());
                webcamVideo.style.display = 'none';
                camOff.style.display = '';
                camActive = false;
                camToggle.innerHTML = '<i class="fas fa-video"></i>';
            } else {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    webcamVideo.srcObject = stream;
                    webcamVideo.style.display = '';
                    camOff.style.display = 'none';
                    camActive = true;
                    camToggle.innerHTML = '<i class="fas fa-video-slash"></i>';
                } catch(e) {
                    // Camera not available, no problem
                }
            }
        });

        // ============ Typing Animation Effect ============
        const questionEl = document.getElementById('questionText');
        const originalText = questionEl.innerHTML;
        questionEl.innerHTML = '';
        
        let charIndex = 0;
        const typeSpeed = 15;
        function typeQuestion() {
            if (charIndex < originalText.length) {
                // Handle HTML tags
                if (originalText[charIndex] === '<') {
                    const closeIdx = originalText.indexOf('>', charIndex);
                    questionEl.innerHTML += originalText.substring(charIndex, closeIdx + 1);
                    charIndex = closeIdx + 1;
                } else {
                    questionEl.innerHTML += originalText[charIndex];
                    charIndex++;
                }
                setTimeout(typeQuestion, typeSpeed);
            }
        }
        setTimeout(typeQuestion, 500);
    </script>
</body>
</html>
