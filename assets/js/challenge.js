/**
 * File: challenge.js
 * Purpose: Implements interactive challenge logic for CodeGaming, including state management, question display, answer handling, feedback, leaderboard, and notifications.
 * Features:
 *   - Manages challenge state, user/guest sessions, and nickname validation.
 *   - Fetches challenge questions and handles answer validation, scoring, and hearts.
 *   - Displays feedback modals, motivational messages, and end-of-challenge stats.
 *   - Integrates leaderboard with all-time and tabbed scopes.
 *   - Provides instructions modal and notification system.
 *   - Supports code and text question types, timer, and retro UI theme.
 * Usage:
 *   - Included on challenge pages requiring interactive challenge functionality.
 *   - Requires specific HTML structure for screens, modals, and buttons.
 *   - Relies on API endpoints: api/challenge-questions.php, api/challenge-attempt.php, api/challenge-leaderboard.php, api/check-guest-nickname.php, api/guest-session.php.
 * Included Files/Dependencies:
 *   - Bootstrap (modals)
 *   - HTML/CSS for challenge UI
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// Challenge Page - Interactive Logic
// Retro Macintosh System 6/7 Theme

let challengeState = {
  questions: [],
  current: 0,
  hearts: 3,
  timer: null,
  timeLeft: 150, // 2:30 in seconds
  userId: null,
  guestSessionId: null,
  nickname: null,
  isGuest: true,
  score: 0,
  finished: false,
  startTime: null,
  totalTime: 0,
  currentScope: 'alltime'
};

let lastFeedbackExplanation = '';

// Debounce helper
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

function initChallenge() {
  // Detect user/guest
  challengeState.userId = (window.CG_USER_ID !== null && window.CG_USER_ID !== 'null' && window.CG_USER_ID !== undefined) ? window.CG_USER_ID : null;
  challengeState.username = window.CG_USERNAME || null;
  challengeState.isGuest = (challengeState.userId === null || challengeState.userId === undefined);

  // Hide nickname input for logged-in users
  const nickInputDiv = document.querySelector('.guest-input-section');
  if (!challengeState.isGuest && nickInputDiv) {
    nickInputDiv.style.display = 'none';
    // Also clear any guest nickname value
    const nickInput = document.getElementById('guest-nickname');
    if (nickInput) nickInput.value = '';
  }

  // Nickname availability check for guests
  if (challengeState.isGuest) {
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
            statusSpan.textContent = 'Already taken';
            statusSpan.className = 'nickname-status taken';
          }
        } catch (e) {
          statusSpan.textContent = '';
          statusSpan.className = 'nickname-status';
        }
      }, 400));
    }
  }

  // Update status bar time
  updateStatusTime();
  setInterval(updateStatusTime, 1000);
}

function updateStatusTime() {
  const timeSpan = document.getElementById('status-time');
  if (timeSpan) {
    timeSpan.textContent = new Date().toLocaleTimeString('en-US', { 
      hour12: false, 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  }
}

async function startChallenge() {
  // Debug log for user/guest detection
  console.log('userId:', challengeState.userId, 'isGuest:', challengeState.isGuest, 'username:', challengeState.username);
  // Get nickname if guest
  if (challengeState.isGuest) {
    const nickInput = document.getElementById('guest-nickname');
    challengeState.nickname = nickInput ? nickInput.value.trim() : '';
    if (!challengeState.nickname) {
      showNotification('Please enter a nickname to continue!', 'warning');
      return;
    }
    if (challengeState.nickname.length < 2) {
      showNotification('Nickname must be at least 2 characters long!', 'warning');
      return;
    }
    // Create guest session
    await createGuestSession(challengeState.nickname);
  } else {
    // For logged-in users, use username or fallback
    challengeState.nickname = challengeState.username || 'Player';
  }

  // Show loading state
  const startBtn = document.querySelector('.btn-start');
  startBtn.textContent = 'Loading...';
  startBtn.disabled = true;

  // Fetch questions
  const success = await fetchQuestions();
  if (!success) {
    startBtn.textContent = 'START CHALLENGE';
    startBtn.disabled = false;
    return;
  }

  // Initialize challenge state
  challengeState.current = 0;
  challengeState.hearts = 3;
  challengeState.score = 0;
  challengeState.finished = false;
  challengeState.timeLeft = 150;
  challengeState.startTime = Date.now();

  // Hide welcome, show progress
  document.getElementById('challenge-welcome').classList.remove('active');
  document.getElementById('challenge-progress').classList.add('active');

  // Show motivational message
  showNotification(`Good luck, ${challengeState.nickname}! Ready for the Expert Challenge?`, 'success');

  displayQuestion();
  startTimer();
}

async function createGuestSession(nickname) {
  try {
    let session_id = sessionStorage.getItem('cg_challenge_session_id');
    if (!session_id) {
      session_id = 'cs_' + Math.random().toString(36).slice(2, 14) + Date.now();
      sessionStorage.setItem('cg_challenge_session_id', session_id);
    }
    const ip = '0.0.0.0';
    const ua = navigator.userAgent;
    const res = await fetch('api/guest-session.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nickname, session_id, ip_address: ip, user_agent: ua })
    });
    const data = await res.json();
    if (data.success) {
      challengeState.guestSessionId = data.guest_session_id;
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

async function fetchQuestions() {
  try {
    const res = await fetch('api/challenge-questions.php');
    const data = await res.json();
    if (data.success) {
      challengeState.questions = data.questions;
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
  if (challengeState.current >= challengeState.questions.length || challengeState.hearts <= 0) {
    endChallenge();
    return;
  }

  const q = challengeState.questions[challengeState.current];

  // Update UI
  updateHearts();
  updateProgress();

  // Render question
  document.getElementById('question-title').textContent = q.title;
  document.getElementById('question-description').textContent = q.description;
  document.getElementById('question-type').textContent = q.type.toUpperCase().replace('_', ' ');
  document.getElementById('question-points').textContent = '30 pts';

  // Show appropriate input based on question type
  const codeContainer = document.getElementById('code-editor-container');
  const textContainer = document.getElementById('text-input-container');
  const codeEditor = document.getElementById('code-editor');
  const answerInput = document.getElementById('answer-input');

  if (q.type === 'code') {
    codeContainer.style.display = 'block';
    textContainer.style.display = 'none';
    codeEditor.value = q.starter_code || '';
    codeEditor.focus();
  } else {
    codeContainer.style.display = 'none';
    textContainer.style.display = 'block';
    answerInput.value = '';
    answerInput.focus();
  }
}

function startTimer() {
  if (challengeState.timer) clearInterval(challengeState.timer);
  
  challengeState.timer = setInterval(() => {
    challengeState.timeLeft--;
    updateTimer();
    
    if (challengeState.timeLeft <= 0) {
      clearInterval(challengeState.timer);
      endChallenge();
    }
  }, 1000);
}

function updateTimer() {
  const timerDiv = document.getElementById('challenge-timer');
  const minutes = Math.floor(challengeState.timeLeft / 60);
  const seconds = challengeState.timeLeft % 60;
  timerDiv.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

  // Add warning colors
  if (challengeState.timeLeft <= 30) {
    timerDiv.style.color = '#800000';
    timerDiv.style.animation = 'pulse 1s infinite';
  } else if (challengeState.timeLeft <= 60) {
    timerDiv.style.color = '#808000';
    timerDiv.style.animation = '';
  } else {
    timerDiv.style.color = '#000000';
    timerDiv.style.animation = '';
  }
}

function updateHearts() {
  const heartsDiv = document.getElementById('challenge-hearts');
  heartsDiv.innerHTML = '♥'.repeat(challengeState.hearts);
  
  heartsDiv.classList.add('hearts-update');
  setTimeout(() => heartsDiv.classList.remove('hearts-update'), 300);
}

function updateProgress() {
  const progressDiv = document.getElementById('current-question');
  const scoreDiv = document.getElementById('current-score');
  progressDiv.textContent = challengeState.current + 1;
  scoreDiv.textContent = challengeState.score;
}

async function submitAnswer() {
  const q = challengeState.questions[challengeState.current];
  let submittedAnswer = '';

  if (q.type === 'code') {
    submittedAnswer = document.getElementById('code-editor').value;
  } else {
    submittedAnswer = document.getElementById('answer-input').value.trim();
  }

  if (!submittedAnswer) {
    showNotification('Please provide an answer!', 'warning');
    return;
  }

  // Submit attempt and get explanation
  let explanation = '';
  let isCorrect = false;
  try {
    const res = await fetch('api/challenge-attempt.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        user_id: challengeState.userId,
        guest_session_id: challengeState.guestSessionId,
        question_id: q.id,
        submitted_answer: submittedAnswer,
        time_taken: 150 - challengeState.timeLeft
      })
    });
    const data = await res.json();
    isCorrect = data.correct || false;
    explanation = data.explanation || '';
  } catch (error) {
    showNotification('Failed to submit/check answer.', 'error');
    return;
  }
  lastFeedbackExplanation = explanation;

  if (isCorrect) {
    challengeState.score += 30;
    showFeedbackModal('correct');
  } else {
    challengeState.hearts--;
    showFeedbackModal('wrong');
  }

  // Move to next question
  challengeState.current++;

  // Delay before next question
  setTimeout(() => {
    closeFeedbackModal();
    displayQuestion();
  }, 2000);
}

async function checkAnswer(questionId, answer) {
  try {
    const res = await fetch('api/check-challenge-answer.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question_id: questionId, answer: answer })
    });
    const data = await res.json();
    return data.correct || false;
  } catch (error) {
    console.error('Failed to check answer:', error);
    return false;
  }
}

async function submitAttempt(data) {
  try {
    await fetch('api/challenge-attempt.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
  } catch (error) {
    console.error('Failed to submit attempt:', error);
  }
}

function skipQuestion() {
  challengeState.current++;
  displayQuestion();
}

function endChallenge() {
  challengeState.finished = true;
  challengeState.totalTime = Math.floor((Date.now() - challengeState.startTime) / 1000);

  if (challengeState.timer) clearInterval(challengeState.timer);

  // Hide progress, show end screen
  document.getElementById('challenge-progress').classList.remove('active');
  document.getElementById('challenge-end').classList.add('active');

  // Update end screen stats
  const percentage = Math.round((challengeState.score / (challengeState.questions.length * 30)) * 100);
  const timeFormatted = formatTime(challengeState.totalTime);

  let achievement = '';
  if (percentage >= 90) achievement = '🏆 MASTER CODER!';
  else if (percentage >= 75) achievement = '🎯 SHARP SHOOTER!';
  else if (percentage >= 60) achievement = '⭐ RISING STAR!';
  else if (percentage >= 40) achievement = '🌱 LEARNING!';
  else achievement = '💪 KEEP TRYING!';

  document.getElementById('end-achievement').textContent = achievement;
  document.getElementById('final-score').textContent = challengeState.score;
  document.getElementById('questions-correct').textContent = `${Math.floor(challengeState.score / 30)}/${challengeState.questions.length}`;
  document.getElementById('time-taken').textContent = timeFormatted;
  document.getElementById('accuracy').textContent = `${percentage}%`;

  // Submit final score to leaderboard
  submitFinalScore();

  // Load leaderboard preview
  fetchLeaderboard('alltime');
}

async function submitFinalScore() {
  try {
    await fetch('api/challenge-leaderboard.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        user_id: challengeState.userId,
        guest_session_id: challengeState.guestSessionId,
        nickname: challengeState.nickname,
        total_score: challengeState.score,
        total_time: challengeState.totalTime,
        questions_attempted: challengeState.questions.length,
        questions_correct: Math.floor(challengeState.score / 30)
      })
    });
  } catch (error) {
    console.error('Failed to submit final score:', error);
  }
}

// Feedback message pools
const FEEDBACK_CORRECT = [
  { title: 'EXCELLENT!', message: 'You got it right! Keep up the great work!', icon: '🚀' },
  { title: 'AWESOME!', message: 'That\'s the right answer! You\'re on fire!', icon: '🔥' },
  { title: 'GREAT JOB!', message: 'Correct! Your skills are showing. ;)', icon: '😎' },
  { title: 'NAILED IT!', message: 'You\'re coding like a pro!', icon: '🤖' },
  { title: 'BRAVO!', message: 'Spot on! Keep the streak going!', icon: '🏆' }
];

const FEEDBACK_WRONG = [
  { title: 'OOPS!', message: 'That\'s not quite right. Don\'t give up!', icon: '💫' },
  { title: 'NOT QUITE!', message: 'Almost! Try the next one.', icon: '😅' },
  { title: 'KEEP GOING!', message: 'Mistakes are part of learning. You got this!', icon: '💡' },
  { title: 'TRY AGAIN!', message: 'Don\'t worry, you\'ll get the next one!', icon: '🔄' },
  { title: 'ALMOST!', message: 'Close, but not quite. Stay focused! ^_^', icon: '🧐' }
];

function showFeedbackModal(type) {
  const modal = document.getElementById('feedback-modal');
  const content = modal.querySelector('.modal-content');

  let title, message, icon, color;

  if (type === 'correct') {
    const pick = FEEDBACK_CORRECT[Math.floor(Math.random() * FEEDBACK_CORRECT.length)];
    title = pick.title;
    message = pick.message;
    icon = pick.icon;
    color = '#008000';
  } else if (type === 'wrong') {
    const pick = FEEDBACK_WRONG[Math.floor(Math.random() * FEEDBACK_WRONG.length)];
    title = pick.title;
    message = pick.message;
    icon = pick.icon;
    color = '#800000';
  }

  document.getElementById('feedback-title').textContent = title;
  document.getElementById('feedback-title').style.color = color;
  document.getElementById('feedback-icon').textContent = icon;
  document.getElementById('feedback-message').textContent = message;
  document.getElementById('feedback-score').textContent = challengeState.score;
  document.getElementById('feedback-lives').textContent = '♥'.repeat(challengeState.hearts);
  document.getElementById('feedback-explanation').textContent = lastFeedbackExplanation || '';

  modal.style.display = 'flex';
}

function closeFeedback() {
  document.getElementById('feedback-modal').style.display = 'none';
}

function showInstructions() {
  document.getElementById('instructions-modal').style.display = 'flex';
}

function closeInstructions() {
  document.getElementById('instructions-modal').style.display = 'none';
}

function showLeaderboard() {
  document.getElementById('leaderboard-modal').style.display = 'flex';
  fetchLeaderboard(challengeState.currentScope);
}

function closeLeaderboard() {
  document.getElementById('leaderboard-modal').style.display = 'none';
}

function refreshLeaderboard() {
  fetchLeaderboard(challengeState.currentScope);
}

function switchScope(scope) {
  challengeState.currentScope = scope;
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  fetchLeaderboard(scope);
}

async function fetchLeaderboard(scope) {
  try {
    const res = await fetch(`api/challenge-leaderboard.php?scope=${encodeURIComponent(scope)}`);
    const data = await res.json();
    const lbDiv = document.getElementById('leaderboard-list') || document.getElementById('leaderboard-content');
    if (data.success && data.leaderboard.length > 0) {
      lbDiv.innerHTML = `
        <div class="leaderboard-list">
          ${data.leaderboard.map((row, index) => {
            // Use nickname for guests, username for users
            const name = row.nickname || row.username || '-';
            // Use played_at or completed_at for time
            const playedAt = row.played_at || row.completed_at || null;
            // Use score or total_score for points
            const score = (typeof row.score !== 'undefined' && row.score !== null) ? row.score : (row.total_score ?? '-');
            return `
              <div class="leaderboard-item ${index < 3 ? 'top-' + (index + 1) : ''}">
                <div class="rank">${index + 1}</div>
                <div class="player-info">
                  <div class="player-name">${name}</div>
                  <div class="player-time">${formatTimeAgo(playedAt)}</div>
                </div>
                <div class="player-score">${score}</div>
              </div>
            `;
          }).join('')}
        </div>
      `;
    } else {
      lbDiv.innerHTML = '<div class="no-scores">No scores yet. Be the first!</div>';
    }
  } catch (error) {
    const lbDiv = document.getElementById('leaderboard-list') || document.getElementById('leaderboard-content');
    lbDiv.innerHTML = '<div class="error">Failed to load leaderboard.</div>';
  }
}

function restartChallenge() {
  document.getElementById('challenge-end').classList.remove('active');
  document.getElementById('challenge-welcome').classList.add('active');
  
  // Reset state
  challengeState.current = 0;
  challengeState.hearts = 3;
  challengeState.score = 0;
  challengeState.finished = false;
  challengeState.timer = null;
  challengeState.timeLeft = 150;
  challengeState.startTime = null;
  challengeState.totalTime = 0;
  
  // Reset UI
  const startBtn = document.querySelector('.btn-start');
  startBtn.textContent = 'START CHALLENGE';
  startBtn.disabled = false;
  // Re-attach listeners for new DOM
  attachChallengeEventListeners();
}

function exitChallenge() {
  window.location.href = 'home_page.php';
}

function runCode() {
  const code = document.getElementById('code-editor').value;
  const output = document.getElementById('code-output');
  
  // Simple code execution (for demo purposes)
  // In production, this would be handled server-side for security
  try {
    // This is a simplified example - real implementation would be more secure
    output.textContent = 'Code execution would happen here...';
  } catch (error) {
    output.textContent = 'Error: ' + error.message;
  }
}

// Attach all main action button listeners
function attachChallengeEventListeners() {
  const startBtn = document.querySelector('.btn-start');
  if (startBtn) startBtn.addEventListener('click', startChallenge);

  const instructionsBtn = document.querySelector('.btn-instructions');
  if (instructionsBtn) instructionsBtn.addEventListener('click', showInstructions);

  const leaderboardBtn = document.querySelector('.btn-leaderboard');
  if (leaderboardBtn) leaderboardBtn.addEventListener('click', showLeaderboard);

  const playAgainBtn = document.querySelector('.btn-play-again');
  if (playAgainBtn) playAgainBtn.addEventListener('click', restartChallenge);

  const exitBtn = document.querySelector('.btn-exit');
  if (exitBtn) exitBtn.addEventListener('click', exitChallenge);

  const submitBtn = document.querySelector('.btn-submit');
  if (submitBtn) submitBtn.addEventListener('click', submitAnswer);

  const skipBtn = document.querySelector('.btn-skip');
  if (skipBtn) skipBtn.addEventListener('click', skipQuestion);

  // Modal close buttons
  const feedbackClose = document.querySelector('#feedback-modal .modal-close');
  if (feedbackClose) feedbackClose.addEventListener('click', closeFeedback);

  const instructionsClose = document.querySelector('#instructions-modal .modal-close');
  if (instructionsClose) instructionsClose.addEventListener('click', closeInstructions);

  const leaderboardClose = document.querySelector('#leaderboard-modal .modal-close');
  if (leaderboardClose) leaderboardClose.addEventListener('click', closeLeaderboard);

  // Leaderboard modal controls
  const refreshLeaderboardBtn = document.getElementById('btn-refresh-leaderboard');
  if (refreshLeaderboardBtn) refreshLeaderboardBtn.addEventListener('click', refreshLeaderboard);

  document.querySelectorAll('#leaderboard-modal .tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      switchScope(this.getAttribute('data-scope'));
    });
  });
}

// Improved notification display: center, high z-index, always visible
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  // Style the notification
  notification.style.cssText = `
    position: fixed;
    top: 40px;
    left: 50%;
    transform: translateX(-50%) scale(0.95);
    background: #fff;
    color: #111;
    border: 2px solid #222;
    border-radius: 8px;
    font-family: 'Fira Mono', 'VT323', monospace;
    font-size: 1rem;
    font-weight: bold;
    z-index: 5000;
    padding: 16px 32px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s, transform 0.3s;
    min-width: 220px;
    max-width: 90vw;
    text-align: center;
  `;
  document.body.appendChild(notification);
  // Animate in
  setTimeout(() => {
    notification.style.opacity = '1';
    notification.style.pointerEvents = 'auto';
    notification.style.transform = 'translateX(-50%) scale(1)';
  }, 100);
  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.opacity = '0';
    notification.style.pointerEvents = 'none';
    notification.style.transform = 'translateX(-50%) scale(0.95)';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
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

// Initialize when DOM is loaded
// Attach listeners and initialize challenge state
document.addEventListener('DOMContentLoaded', function() {
  initChallenge();
  attachChallengeEventListeners();
});

// In restartChallenge, re-attach listeners after showing welcome screen
function restartChallenge() {
  document.getElementById('challenge-end').classList.remove('active');
  document.getElementById('challenge-welcome').classList.add('active');
  // Reset state
  challengeState.current = 0;
  challengeState.hearts = 3;
  challengeState.score = 0;
  challengeState.finished = false;
  challengeState.timer = null;
  challengeState.timeLeft = 150;
  challengeState.startTime = null;
  challengeState.totalTime = 0;
  // Reset UI
  const startBtn = document.querySelector('.btn-start');
  startBtn.textContent = 'START CHALLENGE';
  startBtn.disabled = false;
  // Re-attach listeners for new DOM
  attachChallengeEventListeners();
}