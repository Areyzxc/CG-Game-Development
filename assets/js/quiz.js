/**
 * File: quiz.js
 * Purpose: Implements interactive quiz logic for CodeGaming, including state management, question display, answer handling, feedback, leaderboard, and notifications.
 * Features:
 *   - Manages quiz state, user/guest sessions, and difficulty selection.
 *   - Fetches questions and handles answer validation, scoring, and hearts.
 *   - Displays feedback modals, motivational messages, and end-of-quiz stats.
 *   - Integrates leaderboard with all-time and weekly tabs.
 *   - Provides instructions modal and notification system.
 *   - Supports guest nickname validation and session creation.
 * Usage:
 *   - Included on quiz pages requiring interactive quiz functionality.
 *   - Requires specific HTML structure for screens, modals, and buttons.
 *   - Relies on API endpoints: api/quiz-questions.php, api/quiz-attempt.php, api/quiz-leaderboard.php, api/check-guest-nickname.php, api/guest-session.php.
 * Included Files/Dependencies:
 *   - Bootstrap (modals)
 *   - HTML/CSS for quiz UI
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// assets/js/quiz.js - Interactive Quiz Logic

let quizState = {
  questions: [],
  current: 0,
  hearts: 7,
  difficulty: null,
  timer: null,
  timeLeft: 0,
  userId: null,
  guestSessionId: null,
  nickname: null,
  isExpert: false,
  isGuest: true,
  score: 0,
  finished: false,
  startTime: null,
  totalTime: 0
};

// Debounce helper
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

// Show notification helper
function showNotification(message, type = 'info') {
  console.log(`Notification [${type}]:`, message);
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `quiz-notification quiz-notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    background: ${type === 'success' ? '#2ecc71' : type === 'warning' ? '#f39c12' : type === 'error' ? '#e74c3c' : '#3498db'};
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 10000;
    font-family: 'Press Start 2P', monospace;
    font-size: 12px;
    max-width: 300px;
    animation: slideIn 0.3s ease-out;
  `;
  
  document.body.appendChild(notification);
  
  // Auto-remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

function initQuiz() {
  console.log('=== INIT QUIZ CALLED ===');
  console.log('Initializing quiz...');
  
  // Always show welcome screen and hide start/quiz screens on load
  const welcomeScreen = document.querySelector('.quiz-welcome-screen');
  const startScreen = document.querySelector('.quiz-start-screen');
  const inProgressScreen = document.querySelector('.quiz-in-progress');
  
  console.log('Welcome screen element:', welcomeScreen);
  console.log('Start screen element:', startScreen);
  console.log('In progress screen element:', inProgressScreen);
  
  if (welcomeScreen) welcomeScreen.style.display = 'block';
  if (startScreen) startScreen.style.display = 'none';
  if (inProgressScreen) inProgressScreen.style.display = 'none';

  // Detect user/guest
  quizState.userId = window.CG_USER_ID || null;
  quizState.username = window.CG_USERNAME || null;
  quizState.isGuest = !quizState.userId;
  
  console.log('User detection:', {
    userId: quizState.userId,
    username: quizState.username,
    isGuest: quizState.isGuest,
    CG_USER_ID: window.CG_USER_ID,
    CG_USERNAME: window.CG_USERNAME
  });

  // Hide nickname input for logged-in users
  const nickInputDiv = document.querySelector('.nickname-input');
  if (!quizState.isGuest && nickInputDiv) {
    console.log('Hiding nickname input for logged-in user');
    nickInputDiv.style.display = 'none';
  } else if (quizState.isGuest) {
    console.log('Showing nickname input for guest');
  }

  // Nickname availability check
  if (quizState.isGuest) {
    const nickInput = document.getElementById('guest-nickname');
    const statusSpan = document.getElementById('nickname-status');
    if (nickInput && statusSpan) {
      nickInput.addEventListener('input', debounce(async function() {
        const nickname = nickInput.value.trim();
        if (!nickname) {
          statusSpan.textContent = '';
          statusSpan.className = 'nickname-status';
          return;
        }
        statusSpan.textContent = 'Checking...';
        statusSpan.className = 'nickname-status checking';
        try {
          const res = await fetch('api/check-guest-nickname.php?nickname=' + encodeURIComponent(nickname));
          const data = await res.json();
          if (data.available) {
            statusSpan.textContent = 'Available!';
            statusSpan.className = 'nickname-status available';
          } else {
            statusSpan.textContent = 'Taken!';
            statusSpan.className = 'nickname-status taken';
          }
        } catch (err) {
          statusSpan.textContent = 'Error checking nickname';
          statusSpan.className = 'nickname-status error';
        }
      }, 400));
    }
  }

  // Welcome button event handler
  const welcomeBtn = document.getElementById('startQuizBtn') || document.querySelector('.welcome-start-btn');
  if (welcomeBtn) {
    welcomeBtn.onclick = function(e) {
      e.preventDefault();
      if (welcomeScreen) welcomeScreen.style.display = 'none';
      if (startScreen) {
        startScreen.style.display = 'block';
        setTimeout(initDifficultySelection, 0);
      }
    };
  }

  // Instructions modal
  // Only attach event handler once
  let instructionsBtn = document.querySelector('.instructions-btn');
  if (instructionsBtn && !instructionsBtn._handlerAttached) {
    instructionsBtn.onclick = function(e) {
      e.preventDefault();
      showInstructionsModal();
    };
    instructionsBtn._handlerAttached = true;
  }


  // Always re-initialize difficulty selection and button
  window.initDifficultySelection = function() {
    console.log('Initializing difficulty selection...');
    const difficultyButtons = document.querySelectorAll('.difficulty-btn');
    console.log('Found difficulty buttons:', difficultyButtons.length);
    
    difficultyButtons.forEach((btn, index) => {
      console.log(`Button ${index}:`, btn, 'data-difficulty:', btn.dataset.difficulty);
      btn.onclick = function(e) {
        console.log('Button clicked!', this.dataset.difficulty);
        console.log('Button element:', this);
        console.log('Current classes:', this.className);
        
        // Remove selected from all buttons
        document.querySelectorAll('.difficulty-btn').forEach(b => {
          b.classList.remove('selected');
          console.log('Removed selected from:', b);
        });
        
        // Add selected to clicked button
        this.classList.add('selected');
        console.log('Added selected to:', this);
        console.log('New classes:', this.className);
        
        quizState.difficulty = this.dataset.difficulty;
        console.log('Quiz state difficulty set to:', quizState.difficulty);
        
        const startBtn = document.querySelector('.start-quiz-btn');
        if (startBtn) {
          startBtn.disabled = false;
          console.log('Start button enabled');
        } else {
          console.error('Start button not found!');
        }
      }
    });
    
    // Ensure only one event handler for Start Quiz button
    const startBtn = document.querySelector('.start-quiz-btn');
    if (startBtn) {
      startBtn.onclick = startQuizHandler;
      startBtn.disabled = true;
      console.log('Start button handler attached, disabled initially');
    } else {
      console.error('Start quiz button not found!');
    }
  }

  // Initialize difficulty selection and Start Quiz button reliably
  window.initDifficultySelection();
}

async function startQuizHandler() {
  console.log('Start quiz handler called');
  console.log('Quiz state:', quizState);
  
  // Get difficulty
  if (!quizState.difficulty) {
    showNotification('Please select a difficulty level!', 'warning');
    return;
  }
  
  console.log('Is guest?', quizState.isGuest);
  
  // Get nickname if guest
  if (quizState.isGuest) {
    console.log('User is guest, checking nickname...');
    const nickInput = document.getElementById('guest-nickname');
    quizState.nickname = nickInput ? nickInput.value.trim() : '';
    console.log('Nickname:', quizState.nickname);
    
    if (!quizState.nickname) {
      showNotification('Please enter a nickname to continue!', 'warning');
      return;
    }
    if (quizState.nickname.length < 2) {
      showNotification('Nickname must be at least 2 characters long!', 'warning');
      return;
    }
    // Create guest session
    console.log('Creating guest session...');
    await createGuestSession(quizState.nickname);
  } else {
    // For logged-in users, use username
    console.log('User is logged in, using username:', quizState.username);
    quizState.nickname = quizState.username;
  }
  
  // Show loading state
  const startBtn = document.querySelector('.start-quiz-btn');
  startBtn.textContent = 'Loading...';
  startBtn.disabled = true;
  
  // Fetch questions
  const success = await fetchQuestions(quizState.difficulty);
  if (!success) {
    startBtn.textContent = 'Start Quiz';
    startBtn.disabled = false;
    return;
  }
  
  // Initialize quiz state
  quizState.current = 0;
  quizState.hearts = 7;
  quizState.score = 0;
  quizState.finished = false;
  quizState.isExpert = quizState.difficulty === 'expert';
  quizState.startTime = Date.now();
  
  // Hide start, show quiz
  document.querySelector('.quiz-start-screen').style.display = 'none';
  document.querySelector('.quiz-in-progress').style.display = 'flex';
  
  // Show motivational message
  showNotification(`Good luck, ${quizState.nickname}! Ready to conquer the ${quizState.difficulty} challenge?`, 'success');
  
  displayQuestion();
}

async function createGuestSession(nickname) {
  try {
    // Use sessionStorage for session_id
    let session_id = sessionStorage.getItem('cg_quiz_session_id');
    if (!session_id) {
      session_id = 'gs_' + Math.random().toString(36).slice(2, 14) + Date.now();
      sessionStorage.setItem('cg_quiz_session_id', session_id);
    }
    // Use a dummy IP if not available (required by backend)
    const ip = '0.0.0.0';
    const ua = navigator.userAgent;
    const res = await fetch('api/guest-session.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nickname, session_id, ip_address: ip, user_agent: ua })
    });
    const data = await res.json();
    if (data.success) {
      quizState.guestSessionId = data.guest_session_id;
      return true;
    } else {
      showNotification('Failed to create guest session. Please try again.', 'error');
      return false;
    }
  } catch (error) {
    showNotification('Network error. Please check your connection.', 'error');
    return false;
  }
}

async function fetchQuestions(difficulty) {
  try {
    const res = await fetch('api/quiz-questions.php?difficulty=' + encodeURIComponent(difficulty));
    const data = await res.json();
    if (data.success) {
      quizState.questions = data.questions;
      return true;
    } else {
      showNotification('Failed to load questions. Please try again.', 'error');
      return false;
    }
  } catch (error) {
    showNotification('Network error. Please check your connection.', 'error');
    return false;
  }
}

function displayQuestion() {
  if (quizState.current >= quizState.questions.length || quizState.hearts <= 0) {
    showEndModal();
    return;
  }
  
  const q = quizState.questions[quizState.current];
  
  // Update UI
  updateHearts();
  updateProgress();
  
  // Timer for Expert
  if (quizState.isExpert) {
    quizState.timeLeft = 30;
    updateTimer();
    if (quizState.timer) clearInterval(quizState.timer);
    quizState.timer = setInterval(() => {
      quizState.timeLeft--;
      updateTimer();
      if (quizState.timeLeft <= 0) {
        clearInterval(quizState.timer);
        handleAnswer(null, true); // Timeout
      }
    }, 1000);
    document.querySelector('.quiz-timer').style.display = 'inline-block';
  } else {
    document.querySelector('.quiz-timer').style.display = 'none';
    if (quizState.timer) clearInterval(quizState.timer);
  }
  
  // Render question
  document.querySelector('.quiz-question-box').textContent = q.question;
  
  // Render choices with better styling
  const choicesDiv = document.querySelector('.quiz-choices');
  choicesDiv.innerHTML = '';
  q.choices.forEach((choice, index) => {
    const btn = document.createElement('button');
    btn.textContent = choice.text;
    btn.className = 'quiz-choice-btn';
    btn.setAttribute('data-choice-id', choice.id);
    btn.setAttribute('data-choice-index', index);
    btn.onclick = () => handleAnswer(choice.id, false);
    choicesDiv.appendChild(btn);
  });
}

async function handleAnswer(selectedId, isTimeout) {
  const q = quizState.questions[quizState.current];
  
  // Clear timer
  if (quizState.timer) {
    clearInterval(quizState.timer);
  }
  
  // Determine correctness by checking if the selected answer is marked as correct
  let isCorrect = false;
  if (!isTimeout && selectedId) {
    const selectedChoice = q.choices.find(choice => choice.id == selectedId);
    isCorrect = selectedChoice && selectedChoice.is_correct === 1;
  }
  
  // Submit attempt
  await submitAttempt({
    user_id: quizState.userId,
    guest_session_id: quizState.guestSessionId,
    question_id: q.id,
    selected_choice_id: selectedId,
    is_correct: isCorrect
  });
  
  // Update score and hearts
  if (isTimeout) {
    quizState.hearts--;
    showFeedbackModal('timeout');
  } else if (isCorrect) {
    quizState.score++;
    showFeedbackModal('correct');
  } else {
    quizState.hearts--;
    showFeedbackModal('wrong');
  }
  
  // Move to next question
  quizState.current++;
  
  // Delay before next question
  setTimeout(() => {
    closeFeedbackModal();
    displayQuestion();
  }, 2000);
}

// Feedback message pools
const FEEDBACK_CORRECT = [
  { title: 'EXCELLENT!', message: 'You got it right! Keep up the great work!', icon: '🚀', gif: 'assets/gif/correct1.gif' },
  { title: 'AWESOME!', message: 'That’s the right answer! You’re on fire!', icon: '🔥', gif: 'assets/gif/correct2.gif' },
  { title: 'GREAT JOB!', message: 'Correct! Your skills are showing. ;)', icon: '😎' },
  { title: 'NAILED IT!', message: 'You’re coding like a pro!', icon: '🤖' },
  { title: 'BRAVO!', message: 'Spot on! Keep the streak going!', icon: '🏆' }
];
const FEEDBACK_WRONG = [
  { title: 'OOPS!', message: 'That’s not quite right. Don’t give up!', icon: '💫', gif: 'assets/gif/wrong1.gif' },
  { title: 'NOT QUITE!', message: 'Almost! Try the next one.', icon: '😅', gif: 'assets/gif/wrong2.gif' },
  { title: 'KEEP GOING!', message: 'Mistakes are part of learning. You got this!', icon: '💡' },
  { title: 'TRY AGAIN!', message: 'Don’t worry, you’ll get the next one!', icon: '🔄' },
  { title: 'ALMOST!', message: 'Close, but not quite. Stay focused! ^_^', icon: '🧐' }
];

function showFeedbackModal(type) {
  const modal = document.getElementById('quiz-feedback-modal');
  const content = modal.querySelector('.modal-content');

  let title, message, icon, color;

  if (type === 'correct') {
    const pick = FEEDBACK_CORRECT[Math.floor(Math.random() * FEEDBACK_CORRECT.length)];
    title = pick.title;
    message = pick.message;
    icon = pick.gif
      ? `<img src=\"${pick.gif}\" alt=\"correct\" class=\"feedback-gif\">`
      : pick.icon;
    color = '#2ecc71';
    playSound('correct');
  } else if (type === 'wrong') {
    const pick = FEEDBACK_WRONG[Math.floor(Math.random() * FEEDBACK_WRONG.length)];
    title = pick.title;
    message = pick.message;
    icon = pick.gif
      ? `<img src=\"${pick.gif}\" alt=\"wrong\" class=\"feedback-gif\">`
      : pick.icon;
    color = '#e74c3c';
    playSound('wrong');
  } else if (type === 'timeout') {
    title = 'TIME\'S UP!';
    message = 'The clock ran out! Try to be faster next time!';
    icon = '⏰';
    color = '#f39c12';
    playSound('timeout');
  }

  content.innerHTML = `
    <div class=\"feedback-icon\">${icon}</div>
    <h2 style=\"color: ${color};\">${title}</h2>
    <div class=\"feedback-message\">${message}</div>
    <div class=\"feedback-stats\">
      <div>Score: ${quizState.score}</div>
      <div>Hearts: ${'♥'.repeat(quizState.hearts)}</div>
    </div>
  `;

  modal.style.display = 'flex';
}

function closeFeedbackModal() {
  const modal = document.getElementById('quiz-feedback-modal');
  modal.style.display = 'none';
}

function updateHearts() {
  const heartsDiv = document.querySelector('.quiz-hearts');
  heartsDiv.innerHTML = '♥'.repeat(quizState.hearts);
  
  // Add animation class
  heartsDiv.classList.add('hearts-update');
  setTimeout(() => heartsDiv.classList.remove('hearts-update'), 300);
}

function updateProgress() {
  const progressDiv = document.querySelector('.quiz-progress');
  progressDiv.textContent = `Q${quizState.current + 1}/${quizState.questions.length}`;
  
  // Add animation class
  progressDiv.classList.add('progress-update');
  setTimeout(() => progressDiv.classList.remove('progress-update'), 300);
}

function updateTimer() {
  const timerDiv = document.querySelector('.quiz-timer');
  timerDiv.textContent = quizState.timeLeft + 's';
  
  // Add warning colors
  if (quizState.timeLeft <= 10) {
    timerDiv.style.color = '#e74c3c';
    timerDiv.style.animation = 'pulse 1s infinite';
  } else if (quizState.timeLeft <= 20) {
    timerDiv.style.color = '#f39c12';
    timerDiv.style.animation = '';
  } else {
    timerDiv.style.color = '#ffb3ff';
    timerDiv.style.animation = '';
  }
}

async function submitAttempt(data) {
  try {
    await fetch('api/quiz-attempt.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
  } catch (error) {
    console.error('Failed to submit attempt:', error);
  }
}

function showEndModal() {
  quizState.finished = true;
  quizState.totalTime = Math.floor((Date.now() - quizState.startTime) / 1000);

  if (quizState.timer) clearInterval(quizState.timer);

  const modal = document.getElementById('quiz-end-modal');
  const content = modal.querySelector('.modal-content');

  const percentage = Math.round((quizState.score / quizState.questions.length) * 100);
  const timeFormatted = formatTime(quizState.totalTime);

  let achievement = '';
  if (percentage >= 90) achievement = '🏆 MASTER CODER!';
  else if (percentage >= 75) achievement = '🎯 SHARP SHOOTER!';
  else if (percentage >= 60) achievement = '⭐ RISING STAR!';
  else if (percentage >= 40) achievement = '🌱 LEARNING!';
  else achievement = '💪 KEEP TRYING!';

  content.innerHTML = `
    <button class="end-modal-x-btn" title="Exit">✕</button>
    <div class="end-achievement">${achievement}</div>
    <h2>Quiz Complete!</h2>
    <div class="end-stats">
      <div class="stat-item">
        <span class="stat-label">Score:</span>
        <span class="stat-value">${quizState.score} / ${quizState.questions.length}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Percentage:</span>
        <span class="stat-value">${percentage}%</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Hearts Left:</span>
        <span class="stat-value">${quizState.hearts}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Time:</span>
        <span class="stat-value">${timeFormatted}</span>
      </div>
    </div>
    <div class="end-actions">
      <button class="btn-pixel" onclick="restartQuiz()">Play Again</button>
      <button class="btn-pixel exit-btn">Exit</button>
      <button class="btn-pixel" onclick="showLeaderboard()">Leaderboard</button>
    </div>
    <div id="quiz-leaderboard" class="end-leaderboard"></div>
  `;

  // Add event listeners for Exit and X
  const exitBtn = content.querySelector('.exit-btn');
  if (exitBtn) exitBtn.onclick = exitQuiz;
  const xBtn = content.querySelector('.end-modal-x-btn');
  if (xBtn) xBtn.onclick = exitQuiz;

  modal.style.display = 'flex';
  fetchLeaderboard(quizState.difficulty, 'alltime');
}

function restartQuiz() {
  document.getElementById('quiz-end-modal').style.display = 'none';
  document.querySelector('.quiz-start-screen').style.display = 'block';
  document.querySelector('.quiz-in-progress').style.display = 'none';
  
  // Reset state
  quizState.current = 0;
  quizState.hearts = 7;
  quizState.score = 0;
  quizState.finished = false;
  quizState.timer = null;
  quizState.timeLeft = 0;
  quizState.startTime = null;
  quizState.totalTime = 0;
  
  // Reset UI
  document.querySelector('.start-quiz-btn').textContent = 'Start Quiz';
  document.querySelector('.start-quiz-btn').disabled = false;
  document.querySelectorAll('.difficulty-btn').forEach(b => b.classList.remove('selected'));
  quizState.difficulty = null;
}

function showLeaderboard() {
  const modal = document.getElementById('quiz-leaderboard-modal');
  const content = modal.querySelector('.modal-content');
  
  content.innerHTML = `
    <div class="leaderboard-header">
      <h2>Leaderboard - ${quizState.difficulty.toUpperCase()}</h2>
      <div class="leaderboard-controls">
        <button class="btn-pixel refresh-btn" onclick="refreshLeaderboard()">🔄</button>
        <button class="btn-pixel close-btn" onclick="closeLeaderboard()">✕</button>
      </div>
    </div>
    <div class="leaderboard-tabs">
      <button class="tab-btn active" onclick="switchLeaderboardTab('alltime')">All Time</button>
      <button class="tab-btn" onclick="switchLeaderboardTab('weekly')">This Week</button>
    </div>
    <div id="leaderboard-content" class="leaderboard-content">
      <div class="loading">Loading leaderboard...</div>
    </div>
  `;
  
  modal.style.display = 'flex';
  fetchLeaderboard(quizState.difficulty, 'alltime');
}

function closeLeaderboard() {
  document.getElementById('quiz-leaderboard-modal').style.display = 'none';
}

function refreshLeaderboard() {
  const activeTab = document.querySelector('.tab-btn.active');
  const scope = activeTab ? activeTab.getAttribute('onclick').match(/'([^']+)'/)[1] : 'alltime';
  fetchLeaderboard(quizState.difficulty, scope);
}

function switchLeaderboardTab(scope) {
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  fetchLeaderboard(quizState.difficulty, scope);
}

async function fetchLeaderboard(difficulty, scope) {
  try {
    const res = await fetch(`api/quiz-leaderboard.php?difficulty=${encodeURIComponent(difficulty)}&scope=${encodeURIComponent(scope)}`);
    const data = await res.json();
    const lbDiv = document.getElementById('leaderboard-content') || document.getElementById('quiz-leaderboard');
    
    if (data.success && data.leaderboard.length > 0) {
      lbDiv.innerHTML = `
        <div class="leaderboard-list">
          ${data.leaderboard.map((row, index) => `
            <div class="leaderboard-item ${index < 3 ? 'top-' + (index + 1) : ''}">
              <div class="rank">${index + 1}</div>
              <div class="player-info">
                <div class="player-name">${row.username}</div>
                <div class="player-time">${formatTimeAgo(row.played_at)}</div>
              </div>
              <div class="player-score">${row.score}</div>
            </div>
          `).join('')}
        </div>
      `;
    } else {
      lbDiv.innerHTML = '<div class="no-scores">No scores yet. Be the first!</div>';
    }
  } catch (error) {
    lbDiv.innerHTML = '<div class="error">Failed to load leaderboard.</div>';
  }
}

function showInstructionsModal() {
  // Remove any existing modals first
  const existingModal = document.querySelector('.instructions-modal');
  if (existingModal) {
    existingModal.remove();
  }

  // Create the modal container
  const modal = document.createElement('div');
  modal.className = 'instructions-modal';
  
  // Set the modal content
  modal.innerHTML = `
    <div class="modal-header">
      <h2>📚 Quiz Instructions</h2>
      <button class="close-btn">✕</button>
    </div>
    <div class="modal-content">
      <div class="difficulty-section">
        <h3>🎯 Beginner Mode</h3>
        <p>Perfect for newcomers! Basic concepts with no time pressure. Take your time to learn.</p>
        <ul>
          <li>40 questions</li>
          <li>No time limit</li>
          <li>7 hearts (lives)</li>
          <li>Basic programming concepts</li>
        </ul>
      </div>
      <div class="difficulty-section">
        <h3>⚡ Intermediate Mode</h3>
        <p>For those with some experience. Test your knowledge with moderate difficulty.</p>
        <ul>
          <li>40 questions</li>
          <li>No time limit</li>
          <li>7 hearts (lives)</li>
          <li>Intermediate concepts</li>
        </ul>
      </div>
      <div class="difficulty-section">
        <h3>🚀 Expert Mode</h3>
        <p>The ultimate challenge! Advanced questions with a 30-second timer per question.</p>
        <ul>
          <li>40 questions</li>
          <li>30 seconds per question</li>
          <li>7 hearts (lives)</li>
          <li>Advanced programming concepts</li>
        </ul>
      </div>
      <div class="general-tips">
        <h3>💡 Tips</h3>
        <ul>
          <li>Read questions carefully</li>
          <li>Use process of elimination</li>
          <li>Don't rush in Expert mode</li>
          <li>Practice regularly to improve</li>
        </ul>
      </div>
    </div>
  `;
  
  // Add the modal to the body
  document.body.appendChild(modal);
  
  // Add click handler for the close button
  const closeBtn = modal.querySelector('.close-btn');
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      modal.remove();
    });
  }
  
  // Add click handler to close when clicking outside content
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.remove();
    }
  });
}

function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function formatTimeAgo(timestamp) {
  const now = new Date();
  const played = new Date(timestamp);
  const diff = Math.floor((now - played) / 1000);

  if (diff < 60) return `${diff} second${diff === 1 ? '' : 's'} ago`;
  if (diff < 3600) {
    const mins = Math.floor(diff / 60);
    return `${mins} minute${mins === 1 ? '' : 's'} ago`;
  }
  if (diff < 86400) {
    const hours = Math.floor(diff / 3600);
    return `${hours} hour${hours === 1 ? '' : 's'} ago`;
  }
  if (diff < 604800) {
    const days = Math.floor(diff / 86400);
    return `${days} day${days === 1 ? '' : 's'} ago`;
  }
  if (diff < 2592000) {
    const weeks = Math.floor(diff / 604800);
    return `${weeks} week${weeks === 1 ? '' : 's'} ago`;
  }
  const months = Math.floor(diff / 2592000);
  return `${months} month${months === 1 ? '' : 's'} ago`;
}

function playSound(type) {
  // Placeholder: play retro sound effect based on type
  // TODO: Implement actual sound effects
  console.log(`Playing ${type} sound`);
}

function exitQuiz() {
  window.location.href = 'home_page.php';
}

// Debug: Log when script loads
console.log('Quiz script loaded');

// Global click handler for direct button clicks
window.handleQuizClick = function(e) {
  e.preventDefault();
  console.log('Direct button click handler called');
  
  const welcomeScreen = document.querySelector('.quiz-welcome-screen');
  const startScreen = document.querySelector('.quiz-start-screen');
  
  if (welcomeScreen) {
    console.log('Hiding welcome screen (direct)');
    welcomeScreen.style.display = 'none';
  }
  
  if (startScreen) {
    console.log('Showing start screen (direct)');
    startScreen.style.display = 'block';
    
    if (typeof initDifficultySelection === 'function') {
      console.log('Initializing difficulty selection (direct)');
      initDifficultySelection();
    }
  }
};

// Use event delegation for better reliability
function handleQuizButtonClick(e) {
  const target = e.target;
  
  // Check if the clicked element or any of its parents is our button
  const button = target.closest('#startQuizBtn, .welcome-start-btn');
  if (!button) return;
  
  console.log('Quiz button clicked');
  e.preventDefault();
  
  const welcomeScreen = document.querySelector('.quiz-welcome-screen');
  const startScreen = document.querySelector('.quiz-start-screen');
  
  if (welcomeScreen) {
    console.log('Hiding welcome screen');
    welcomeScreen.style.display = 'none';
  }
  
  if (startScreen) {
    console.log('Showing start screen');
    startScreen.style.display = 'block';
    
    // Initialize difficulty selection if the function exists
    if (typeof initDifficultySelection === 'function') {
      console.log('Initializing difficulty selection');
      initDifficultySelection();
    }
  }
}

// Initialize quiz
function initializeQuiz() {
  console.log('=== INITIALIZE QUIZ WRAPPER CALLED ===');
  console.log('Document ready state:', document.readyState);
  
  try {
    // Initialize main quiz functionality
    if (typeof initQuiz === 'function') {
      console.log('initQuiz function exists, calling it...');
      initQuiz();
    } else {
      console.error('initQuiz function NOT FOUND!');
    }
    
    console.log('Quiz initialization complete');
  } catch (error) {
    console.error('Error initializing quiz:', error);
    console.error('Error stack:', error.stack);
  }
}

console.log('=== QUIZ.JS LOADED ===');
console.log('Document ready state at load:', document.readyState);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  console.log('=== DOMContentLoaded EVENT FIRED ===');
  initializeQuiz();
});

// Fallback initialization in case DOMContentLoaded already fired
if (document.readyState === 'loading') {
  console.log('Document still loading, waiting for DOMContentLoaded...');
} else {
  // DOMContentLoaded has already fired
  console.log('Document already loaded, initializing immediately...');
  setTimeout(initializeQuiz, 0);
}