/**
 * File: functionhome.js
 * Purpose: Handles dynamic background, theme toggling, UI animations, notifications, analytics, and leaderboard logic for the CodeGaming home page.
 * Features:
 *   - Initializes animated particle background and manages responsive resizing.
 *   - Implements dark/light theme toggle with persistent localStorage.
 *   - Controls modal blur (Bootstrap), Typed.js welcome text, Rellax parallax, and ScrollReveal for achievements.
 *   - Displays login notifications and manages quick-access card animations and drift effects.
 *   - Loads and displays quiz and challenge analytics, leaderboards, and user stats with refresh and tab switching.
 *   - Note: More additional features may be added in the future.
 * Usage:
 *   - Included on the home page for enhanced UI, analytics, and interactive features.
 *   - Requires HTML elements for theme toggle, notifications, quick cards, analytics containers, and leaderboard.
 *   - Relies on API endpoints: api/quiz-leaderboard.php, api/challenge-leaderboard.php.
 * Included Files/Dependencies:
 *   - Bootstrap (modals)
 *   - Typed.js, Rellax.js, anime.js, ScrollReveal.js
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Dynamic Background
  initDynamicBackground();

// ─────────────────────────────────────────────────────────
  /**
   * Get time-appropriate greeting
   * @returns {string} Greeting based on time of day
   */
  function getTimeBasedGreeting() {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good Morning';
    if (hour < 18) return 'Good Afternoon';
    return 'Good Evening';
  }

  // 1. Typed.js Triggers (for logged-in users only)
  // ─────────────────────────────────────────────────────────
  const usernameDisplay = document.getElementById('usernameDisplay');
  const welcomeTypedElement = document.getElementById('welcomeTyped');
  
  if (welcomeTypedElement) {
    const username = usernameDisplay ? usernameDisplay.textContent.trim() : 'Coder';
    const greeting = getTimeBasedGreeting();

    // Enhanced Typed.js with glitch effect
    new Typed('#welcomeTyped', {
      strings: [
        `${greeting}, ${username}!`,
        'Ready to conquer your next coding challenge?',
        'Level up your coding skills!',
        'Join the coding adventure!'
      ],
      typeSpeed: 50,
      backSpeed: 25,
      backDelay: 2000,
      loop: true,
      onStringTyped: () => {
        const element = document.querySelector('.glitch-text');
        if (element) {
          element.style.animation = 'none';
          void element.offsetWidth; // Trigger reflow
          element.style.animation = 'glitch 0.3s linear';
        }
      }
    });
  }
  // ─────────────────────────────────────────────────────────
  // 2. Rellax Initialization (only if elements exist)
  // ─────────────────────────────────────────────────────────
  const rellaxElements = document.querySelectorAll('.rellax-bg');
  if (rellaxElements.length > 0 && typeof Rellax !== 'undefined') {
    new Rellax('.rellax-bg', {
      speed: -2,
      center: true
    });
  }

  // Handle Login Notification
  const loginNotification = document.getElementById('loginNotificationHome');
  if (loginNotification) {
      // Clean the URL to prevent notification on reload
      const cleanUrl = window.location.href.split('?')[0];
      window.history.replaceState({}, document.title, cleanUrl);

      const closeBtn = document.getElementById('closeLoginNotificationHome');
      const dismissNotification = () => {
          loginNotification.classList.add('dismissed');
          setTimeout(() => {
              if (loginNotification) {
                  loginNotification.remove();
              }
          }, 500);
      };
      const autoDismissTimer = setTimeout(dismissNotification, 6000);
          closeBtn.addEventListener('click', () => {
              clearTimeout(autoDismissTimer);
              dismissNotification();
          });
      }

  // ─────────────────────────────────────────────────────────
  // 3. Welcome Modal Functionality
  // ─────────────────────────────────────────────────────────
  if (typeof initWelcomeModal === 'function') {
    initWelcomeModal();
  }

  // ─────────────────────────────────────────────────────────
  // 4. Quick-Access Cards Fade-In (only if anime is available and cards exist)
  // ─────────────────────────────────────────────────────────
  const quickCards = document.querySelectorAll('.quick-cards .quick-card');
  if (quickCards.length > 0 && typeof anime !== 'undefined') {
    anime({
      targets: '.quick-cards .quick-card',
      opacity: [0, 1],
      translateY: [20, 0],
      easing: 'easeOutExpo',
      duration: 800,
      delay: anime.stagger(200, { start: 600 }) // 0.2s between cards, after 0.5s
    });
  }

  // ─────────────────────────────────────────────────────────
  // 5. Pause/Resume Drift & Glitch
  // ─────────────────────────────────────────────────────────
  document.querySelectorAll('.quick-card.drift').forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.classList.remove('drift');
    });
    card.addEventListener('mouseleave', () => {
      card.classList.add('drift');
    });
  });

// B) ScrollReveal for Achievements
ScrollReveal().reveal('.achievement-item', {
  origin: 'left',
  distance: '20px',
  duration: 600,
  easing: 'ease-out',
  opacity: 1,
  interval: 200
});

// Dynamic Background Implementation
function initDynamicBackground() {
  const canvas = document.getElementById('bgCanvas');
  const ctx = canvas.getContext('2d');
  let particles = [];
  let animationFrameId;

  // Set canvas size
  function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  resizeCanvas();
  window.addEventListener('resize', resizeCanvas);

  // Particle class
  class Particle {
    constructor() {
      this.reset();
    }

    reset() {
      this.x = Math.random() * canvas.width;
      this.y = Math.random() * canvas.height;
      this.size = Math.random() * 2 + 1;
      this.speedX = Math.random() * 2 - 1;
      this.speedY = Math.random() * 2 - 1;
      this.opacity = Math.random() * 0.5 + 0.2;
    }

    update() {
      this.x += this.speedX;
      this.y += this.speedY;

      if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
      if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
    }

    draw() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(128, 24, 24, ${this.opacity})`;
      ctx.fill();
    }
  }

  // Create particles
  function createParticles() {
    const particleCount = Math.floor((canvas.width * canvas.height) / 10000);
    for (let i = 0; i < particleCount; i++) {
      particles.push(new Particle());
    }
  }

  // Animation loop
  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    particles.forEach(particle => {
      particle.update();
      particle.draw();
    });

    // Draw connections
    particles.forEach((p1, i) => {
      particles.slice(i + 1).forEach(p2 => {
        const dx = p1.x - p2.x;
        const dy = p1.y - p2.y;
        const distance = Math.sqrt(dx * dx + dy * dy);

        if (distance < 100) {
          ctx.beginPath();
          ctx.strokeStyle = `rgba(128, 24, 24, ${0.1 * (1 - distance/100)})`;
          ctx.lineWidth = 0.5;
          ctx.moveTo(p1.x, p1.y);
          ctx.lineTo(p2.x, p2.y);
          ctx.stroke();
        }
      });
    });

    animationFrameId = requestAnimationFrame(animate);
  }

  // Initialize
  createParticles();
  animate();

  // Cleanup
  return () => {
    if (animationFrameId) {
      cancelAnimationFrame(animationFrameId);
    }
  };
}

// ─────────────────────────────────────────────────────────
// 6. Quiz Analytics & Leaderboard Functionality
// ─────────────────────────────────────────────────────────
const analyticsContainer = document.getElementById('home-quiz-analytics');
if (analyticsContainer) {
  let currentScope = 'alltime';
  let currentDifficulty = 'beginner';

  // Add refresh button next to X
  const windowBar = analyticsContainer.querySelector('.window-title-bar');
  if (windowBar && !windowBar.querySelector('.window-refresh')) {
    const refreshBtn = document.createElement('span');
    refreshBtn.className = 'window-refresh';
    refreshBtn.title = 'Refresh';
    refreshBtn.innerHTML = '⟳';
    refreshBtn.style.marginLeft = '1em';
    refreshBtn.style.cursor = 'pointer';
    refreshBtn.onclick = () => loadLeaderboard(true);
    windowBar.insertBefore(refreshBtn, windowBar.querySelector('.window-x'));
  }

  // Tab switching
  analyticsContainer.querySelectorAll('.analytics-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      analyticsContainer.querySelectorAll('.analytics-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentScope = this.getAttribute('data-scope');
      loadLeaderboard();
    });
  });
  analyticsContainer.querySelectorAll('.difficulty-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      analyticsContainer.querySelectorAll('.difficulty-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentDifficulty = this.getAttribute('data-difficulty');
      loadLeaderboard();
    });
  });

  // Initial load
  loadLeaderboard();
  
  // Load user progress and achievements
  loadUserProgress();

  async function loadUserProgress() {
    try {
      let params = '';
      if (window.CG_USER_ID) {
        params = `user_id=${window.CG_USER_ID}`;
      } else if (sessionStorage.getItem('cg_quiz_session_id')) {
        params = `guest_session_id=${encodeURIComponent(sessionStorage.getItem('cg_quiz_session_id'))}`;
        if (window.CG_NICKNAME) params += `&nickname=${encodeURIComponent(window.CG_NICKNAME)}`;
      }
      
      const res = await fetch(`api/user-progress.php?${params}`, {
        headers: {
          'X-CSRF-Token': window.CSRF_TOKEN || '',
          'Content-Type': 'application/json'
        }
      });
      
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      
      const data = await res.json();
      
      if (data.success) {
        updateStatCards(data.user_stats);
        updateAchievements(data.achievements);
        updatePersonalization(data.personalization);
      } else {
        console.warn('Failed to load user progress:', data.error);
        showFallbackData();
      }
    } catch (e) {
      console.error('Failed to load user progress:', e);
      showFallbackData();
    }
  }

  function showFallbackData() {
    // Show fallback data when API fails
    const achievementsContainer = document.getElementById('achievements-container');
    if (achievementsContainer) {
      achievementsContainer.innerHTML = `
        <div class="text-center text-muted">
          <i class="fas fa-exclamation-triangle"></i>
          <div>Unable to load achievements</div>
          <small>Please check your connection and try again</small>
        </div>
      `;
    }
  }

  function updateStatCards(userStats) {
    // Update quiz stats
    const quizStats = userStats.quiz || {};
    const quizBestScore = document.getElementById('quiz-best-score');
    const quizRecentScore = document.getElementById('quiz-recent-score');
    const quizRecentTime = document.getElementById('quiz-recent-time');
    
    if (quizBestScore) {
      quizBestScore.textContent = `${quizStats.correct_answers || 0}/40`;
    }
    if (quizRecentScore) {
      quizRecentScore.textContent = `${quizStats.correct_answers || 0}/40`;
    }
    if (quizRecentTime) {
      quizRecentTime.textContent = quizStats.last_played ? 
        `Played ${formatTimeAgo(quizStats.last_played)}` : 'No recent game';
    }

    // Update mini-game stats
    const minigameStats = userStats.minigame || {};
    const minigameBestScore = document.getElementById('minigame-best-score');
    const minigameRecentScore = document.getElementById('minigame-recent-score');
    const minigameRecentTime = document.getElementById('minigame-recent-time');
    
    if (minigameBestScore) {
      minigameBestScore.textContent = minigameStats.best_score || '--';
    }
    if (minigameRecentScore) {
      minigameRecentScore.textContent = minigameStats.best_score || '--';
    }
    if (minigameRecentTime) {
      minigameRecentTime.textContent = minigameStats.last_played ? 
        `Played ${formatTimeAgo(minigameStats.last_played)}` : 'No recent game';
    }

    // Update challenge stats
    const challengeStats = userStats.challenge || {};
    const challengeBestScore = document.getElementById('challenge-best-score');
    const challengeRecentScore = document.getElementById('challenge-recent-score');
    const challengeRecentTime = document.getElementById('challenge-recent-time');
    
    if (challengeBestScore) {
      challengeBestScore.textContent = challengeStats.total_points || '--';
    }
    if (challengeRecentScore) {
      challengeRecentScore.textContent = challengeStats.total_points || '--';
    }
    if (challengeRecentTime) {
      challengeRecentTime.textContent = challengeStats.last_played ? 
        `Played ${formatTimeAgo(challengeStats.last_played)}` : 'No recent game';
    }
  }

  function updateAchievements(achievements) {
    const container = document.getElementById('achievements-container');
    if (!container) return;

    if (!achievements || achievements.length === 0) {
      container.innerHTML = '<div class="text-center text-muted">No achievements yet. Start playing to unlock them!</div>';
      return;
    }

    container.innerHTML = achievements.map(achievement => `
      <div class="achievement-item mb-3">
        <i class="fas fa-${achievement.achievement_icon} text-${achievement.achievement_color} me-2"></i>
        <strong>${achievement.achievement_name}</strong>
        <small class="text-muted d-block">${formatTimeAgo(achievement.awarded_at)}</small>
      </div>
    `).join('');
  }

  function updatePersonalization(personalization) {
    if (!personalization) return;

    // Update welcome message with personalized greeting
    const welcomeElement = document.getElementById('welcomeTyped');
    if (welcomeElement && personalization.greeting) {
      const username = personalization.username || personalization.nickname || 'Coder';
      const greeting = getTimeBasedGreeting();
      
      const welcomeMessages = [
        `${greeting}, ${username}!`,
        `${personalization.greeting}, ${username}!`,
        'Ready to conquer your next coding challenge?',
        'Level up your coding skills!',
        'Join the coding adventure!'
      ];
      
      // Update Typed.js with new messages
      if (window.typedInstance) {
        window.typedInstance.destroy();
      }
      
      window.typedInstance = new Typed('#welcomeTyped', {
        strings: welcomeMessages,
        typeSpeed: 40,
        backSpeed: 20,
        backDelay: 2000,
        loop: true,
        onStringTyped: () => {
          const element = document.querySelector('.glitch-text');
          if (element) {
            element.style.animation = 'none';
            element.offsetHeight; // Trigger reflow
            element.style.animation = 'glitchText 3s infinite';
          }
        }
      });
    }
  }

  async function loadLeaderboard() {
    const statsDiv = analyticsContainer.querySelector('.user-quiz-stats');
    const lbDiv = analyticsContainer.querySelector('.quiz-leaderboard-list');
    const bestCard = analyticsContainer.querySelector('.stat-card-best');
    const recentCard = analyticsContainer.querySelector('.stat-card-recent');
    const topCard = analyticsContainer.querySelector('.stat-card-top');
    statsDiv.innerHTML = '<span>Loading your stats...</span>';
    lbDiv.innerHTML = '<span>Loading leaderboard...</span>';
    if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '...';
    if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '...';
    if (topCard) topCard.querySelector('.stat-card-value').textContent = '...';
    try {
      // Determine user/guest
      let params = `difficulty=${encodeURIComponent(currentDifficulty)}&scope=${encodeURIComponent(currentScope)}`;
      if (window.CG_USER_ID) {
        params += `&user_id=${window.CG_USER_ID}&username=${encodeURIComponent(window.CG_USERNAME)}`;
      } else if (sessionStorage.getItem('cg_quiz_session_id')) {
        params += `&guest_session_id=${encodeURIComponent(sessionStorage.getItem('cg_quiz_session_id'))}`;
        if (window.CG_NICKNAME) params += `&nickname=${encodeURIComponent(window.CG_NICKNAME)}`;
      }
      const res = await fetch(`api/quiz-leaderboard.php?${params}`);
      const data = await res.json();
      // User/guest stats
      if (data.success && data.user_stats) {
        if (bestCard) {
          bestCard.querySelector('.stat-card-value').textContent = `${data.user_stats.best_score ?? 0}/40`;
          bestCard.querySelector('.stat-card-desc').textContent = 'Your all-time best';
        }
        if (recentCard) {
          recentCard.querySelector('.stat-card-value').textContent = `${data.user_stats.best_score ?? 0}/40`;
          recentCard.querySelector('.stat-card-desc').textContent = data.user_stats.last_played ? `Played ${formatTimeAgo(data.user_stats.last_played)}` : 'No recent game';
        }
      } else {
        if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '--';
        if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '--';
      }
      // Top player
      if (data.success && data.top_player && topCard) {
        topCard.querySelector('.stat-card-value').textContent = data.top_player.username;
        topCard.querySelector('.stat-card-desc').textContent = `#1 this ${currentScope === 'alltime' ? 'all time' : currentScope}`;
      } else if (topCard) {
        topCard.querySelector('.stat-card-value').textContent = '--';
        topCard.querySelector('.stat-card-desc').textContent = 'No top player';
      }
      // Leaderboard
      if (data.success && data.leaderboard && data.leaderboard.length > 0) {
        lbDiv.innerHTML = data.leaderboard.map((row, idx) => {
          // Use nickname for guests, username for users
          const name = row.nickname || row.username || '-';
          // Use played_at or completed_at for time
          const playedAt = row.played_at || row.completed_at || null;
          // Use score or total_score for points
          const score = (typeof row.score !== 'undefined' && row.score !== null) ? row.score : (row.total_score ?? '-');
          return `
            <div class="leaderboard-row${row.is_me ? ' me' : ''} top-${idx < 3 ? idx + 1 : ''}">
              <span class="leaderboard-rank">${idx + 1}</span>
              <span class="leaderboard-name">${name}</span>
              <span class="leaderboard-score">${score}</span>
              <span class="leaderboard-time">${formatTimeAgo(playedAt)}</span>
            </div>
          `;
        }).join('');
      } else {
        lbDiv.innerHTML = '<span>No scores yet. Be the first!</span>';
      }
      // Stats summary
      if (data.success && data.user_stats) {
        // Only show stats if we have at least one game played (best_score exists)
        if (data.user_stats.best_score !== undefined) {
          statsDiv.innerHTML = `
            <div><strong>Best Score:</strong> ${data.user_stats.best_score} / 40 (${data.user_stats.best_percentage ?? '0'}%)</div>
            <div><strong>Last Played:</strong> ${data.user_stats.last_played ? formatTimeAgo(data.user_stats.last_played) : 'Never'}</div>
            <div><strong>Rank:</strong> ${data.user_stats.rank ? '#' + data.user_stats.rank : 'Unranked'}</div>
          `;
        } else {
          statsDiv.innerHTML = '<span>No stats available. Play a quiz to get started!</span>';
        }
      } else {
        statsDiv.innerHTML = '<span>No stats available. Play a quiz to get started!</span>';
      }
    } catch (e) {
      console.error('Failed to load leaderboard:', e);
      statsDiv.innerHTML = '<span class="text-warning">Unable to load stats. Please try again later.</span>';
      lbDiv.innerHTML = '<span class="text-warning">Unable to load leaderboard. Please check your connection.</span>';
      if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '--';
      if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '--';
      if (topCard) topCard.querySelector('.stat-card-value').textContent = '--';
      
      // Show retry button
      const retryBtn = document.createElement('button');
      retryBtn.className = 'btn btn-sm btn-outline-primary mt-2';
      retryBtn.textContent = 'Retry';
      retryBtn.onclick = () => loadLeaderboard();
      lbDiv.appendChild(retryBtn);
    }
  }

  // Helper: format time ago (reuse from quiz.js if available)
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

  // Responsive/mobile adjustments
  function adjustAnalyticsLayout() {
    const width = window.innerWidth;
    const bestCard = analyticsContainer.querySelector('.stat-card-best');
    const recentCard = analyticsContainer.querySelector('.stat-card-recent');
    const topCard = analyticsContainer.querySelector('.stat-card-top');
    if (width < 700) {
      // Stack cards vertically, center them, reduce size
      [bestCard, recentCard, topCard].forEach(card => {
        if (card) {
          card.style.position = 'static';
          card.style.transform = 'none';
          card.style.margin = '0.5rem auto';
          card.style.display = 'block';
          card.style.maxWidth = '90vw';
        }
      });
    } else {
      // Restore original positions
      if (bestCard) {
        bestCard.style.position = '';
        bestCard.style.top = '';
        bestCard.style.left = '';
        bestCard.style.transform = '';
        bestCard.style.margin = '';
        bestCard.style.display = '';
        bestCard.style.maxWidth = '';
      }
      if (recentCard) {
        recentCard.style.position = '';
        recentCard.style.top = '';
        recentCard.style.right = '';
        recentCard.style.transform = '';
        recentCard.style.margin = '';
        recentCard.style.display = '';
        recentCard.style.maxWidth = '';
      }
      if (topCard) {
        topCard.style.position = '';
        topCard.style.bottom = '';
        topCard.style.left = '';
        topCard.style.transform = '';
        topCard.style.margin = '';
        topCard.style.display = '';
        topCard.style.maxWidth = '';
      }
    }
  }
  window.addEventListener('resize', adjustAnalyticsLayout);
  adjustAnalyticsLayout();
}

// 7. Challenge Analytics & Leaderboard Functionality
const challengeContainer = document.getElementById('home-challenge-analytics');
if (challengeContainer) {
  let currentScope = 'alltime';

  // Add refresh button next to X
  const windowBar = challengeContainer.querySelector('.window-title-bar');
  if (windowBar && !windowBar.querySelector('.window-refresh')) {
    const refreshBtn = document.createElement('span');
    refreshBtn.className = 'window-refresh';
    refreshBtn.title = 'Refresh';
    refreshBtn.innerHTML = '⟳';
    refreshBtn.style.marginLeft = '1em';
    refreshBtn.style.cursor = 'pointer';
    refreshBtn.onclick = () => loadChallengeLeaderboard(true);
    windowBar.insertBefore(refreshBtn, windowBar.querySelector('.window-x'));
  }

  // Tab switching
  challengeContainer.querySelectorAll('.analytics-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      challengeContainer.querySelectorAll('.analytics-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentScope = this.getAttribute('data-scope');
      loadChallengeLeaderboard();
    });
  });

  // Initial load
  loadChallengeLeaderboard();

  async function loadChallengeLeaderboard() {
    const statsDiv = challengeContainer.querySelector('.user-quiz-stats');
    const lbDiv = challengeContainer.querySelector('.quiz-leaderboard-list');
    const bestCard = challengeContainer.querySelector('.stat-card-best');
    const recentCard = challengeContainer.querySelector('.stat-card-recent');
    const topCard = challengeContainer.querySelector('.stat-card-top');
    statsDiv.innerHTML = '<span>Loading your stats...</span>';
    lbDiv.innerHTML = '<span>Loading leaderboard...</span>';
    if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '...';
    if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '...';
    if (topCard) topCard.querySelector('.stat-card-value').textContent = '...';
    try {
      let params = `scope=${encodeURIComponent(currentScope)}`;
      const res = await fetch(`api/challenge-leaderboard.php?${params}`);
      const data = await res.json();
      // Leaderboard
      if (data.success && data.leaderboard && data.leaderboard.length > 0) {
        lbDiv.innerHTML = data.leaderboard.map((row, idx) => {
          const name = row.nickname || row.username || '-';
          const playedAt = row.played_at || row.completed_at || null;
          const score = (typeof row.score !== 'undefined' && row.score !== null) ? row.score : (row.total_score ?? '-');
          return `
            <div class="leaderboard-row${row.is_me ? ' me' : ''} top-${idx < 3 ? idx + 1 : ''}">
              <span class="leaderboard-rank">${idx + 1}</span>
              <span class="leaderboard-name">${name}</span>
              <span class="leaderboard-score">${score}</span>
              <span class="leaderboard-time">${formatTimeAgo(playedAt)}</span>
            </div>
          `;
        }).join('');
      } else {
        lbDiv.innerHTML = '<span>No scores yet. Be the first!</span>';
      }
      // Stats summary (optional: can be extended for challenge stats)
      statsDiv.innerHTML = '';
    } catch (e) {
      statsDiv.innerHTML = '<span class="text-danger">Failed to load stats.</span>';
      lbDiv.innerHTML = '<span class="text-danger">Failed to load leaderboard.</span>';
      if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '--';
      if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '--';
      if (topCard) topCard.querySelector('.stat-card-value').textContent = '--';
      
      // Add retry button
      const retryBtn = document.createElement('button');
      retryBtn.className = 'btn btn-sm btn-outline-primary mt-2';
      retryBtn.textContent = 'Retry';
      retryBtn.onclick = () => loadChallengeLeaderboard();
      lbDiv.appendChild(retryBtn);
    }
  }
}

// 8. Mini-Game Analytics & Leaderboard Functionality
const minigameContainer = document.getElementById('home-minigame-analytics');
if (minigameContainer) {
  let currentScope = 'alltime';

  // Add refresh button functionality
  const windowBar = minigameContainer.querySelector('.window-title-bar');
  if (windowBar) {
    const refreshBtn = windowBar.querySelector('.window-refresh');
    if (refreshBtn) {
      refreshBtn.onclick = () => loadMinigameLeaderboard(true);
    }
  }

  // Tab switching
  minigameContainer.querySelectorAll('.analytics-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      minigameContainer.querySelectorAll('.analytics-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      currentScope = this.getAttribute('data-scope');
      loadMinigameLeaderboard();
    });
  });

  // Initial load
  loadMinigameLeaderboard();

  async function loadMinigameLeaderboard() {
    const statsDiv = minigameContainer.querySelector('.user-quiz-stats');
    const lbDiv = minigameContainer.querySelector('.quiz-leaderboard-list');
    const bestCard = minigameContainer.querySelector('.stat-card-best');
    const recentCard = minigameContainer.querySelector('.stat-card-recent');
    const topCard = minigameContainer.querySelector('.stat-card-top');
    
    console.log('Loading mini-game leaderboard...'); // Debug log
    
    statsDiv.innerHTML = '<span>Loading your stats...</span>';
    lbDiv.innerHTML = '<span>Loading leaderboard...</span>';
    if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '...';
    if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '...';
    if (topCard) topCard.querySelector('.stat-card-value').textContent = '...';
    
    try {
      let params = `scope=${encodeURIComponent(currentScope)}`;
      
      // Add user/guest identification
      if (window.CG_USER_ID) {
        params += `&user_id=${encodeURIComponent(window.CG_USER_ID)}`;
        console.log('Using user ID:', window.CG_USER_ID); // Debug log
      } else {
        params += `&guest_session_id=${encodeURIComponent(sessionStorage.getItem('cg_minigame_session_id') || 'guest')}`;
        if (window.CG_NICKNAME) params += `&nickname=${encodeURIComponent(window.CG_NICKNAME)}`;
        console.log('Using guest session'); // Debug log
      }
      
      console.log('Fetching:', `api/mini-game/leaderboard.php?${params}`); // Debug log
      const res = await fetch(`api/mini-game/leaderboard.php?${params}`);
      const data = await res.json();
      console.log('Mini-game leaderboard response:', data); // Debug log
      
      // User/guest stats
      if (data.success && data.user_stats) {
        const stats = data.user_stats;
        if (bestCard) {
          bestCard.querySelector('.stat-card-value').textContent = stats.best_score !== undefined ? stats.best_score : '--';
          bestCard.querySelector('.stat-card-desc').textContent = stats.best_score !== undefined ? 'Your all-time best' : 'No games played';
        }
        if (recentCard) {
          recentCard.querySelector('.stat-card-value').textContent = stats.recent_score !== undefined ? stats.recent_score : '--';
          recentCard.querySelector('.stat-card-desc').textContent = stats.last_played ? 
            `Played ${formatTimeAgo(stats.last_played)}` : 'No recent game';
        }
        
        // Only show stats if we have at least one game played
        if (stats.total_games > 0 || stats.best_score !== undefined) {
          statsDiv.innerHTML = `
            <div class="user-stats-summary">
              <span><strong>Games Played:</strong> ${stats.total_games || 0}</span>
              <span><strong>Average Score:</strong> ${stats.avg_score !== undefined ? stats.avg_score : '--'}</span>
              ${stats.best_wpm !== undefined ? `<span><strong>Best WPM:</strong> ${stats.best_wpm}</span>` : ''}
            </div>
          `;
        } else {
          statsDiv.innerHTML = '<span>No stats available. Play a mini-game to get started!</span>';
        }
      }
      
      // Top player info
      if (data.success && data.top_player) {
        const top = data.top_player;
        if (topCard) {
          topCard.querySelector('.stat-card-value').textContent = top.username || top.nickname || '--';
          topCard.querySelector('.stat-card-desc').textContent = top.score ? `Score: ${top.score}` : 'No top player';
        }
      } else {
        if (topCard) {
          topCard.querySelector('.stat-card-value').textContent = '--';
          topCard.querySelector('.stat-card-desc').textContent = 'No top player';
        }
      }
      
      // Leaderboard
      if (data.success && data.leaderboard && data.leaderboard.length > 0) {
        lbDiv.innerHTML = data.leaderboard.map((row, idx) => {
          const name = row.nickname || row.username || '-';
          const playedAt = row.played_at || row.completed_at || row.created_at;
          const score = row.score || row.total_score || '-';
          const gameType = row.game_type || 'Mixed';
          
          return `
            <div class="leaderboard-row${row.is_me ? ' me' : ''} top-${idx < 3 ? idx + 1 : ''}">
              <span class="leaderboard-rank">${idx + 1}</span>
              <span class="leaderboard-name">${name}</span>
              <span class="leaderboard-score">${score}</span>
              <span class="leaderboard-type">${gameType}</span>
              <span class="leaderboard-time">${formatTimeAgo(playedAt)}</span>
            </div>
          `;
        }).join('');
      } else {
        lbDiv.innerHTML = '<div class="no-data">No leaderboard data available. Be the first to play!</div>';
      }
      
      // Clear loading message if no stats are available
      if (!data.success || !data.user_stats) {
        statsDiv.innerHTML = '<span>No stats available. Play a mini-game to get started!</span>';
      }
    } catch (e) {
      console.error('Failed to load mini-game leaderboard:', e);
      statsDiv.innerHTML = '<span class="text-warning">Unable to load stats. Please try again later.</span>';
      lbDiv.innerHTML = '<span class="text-warning">Unable to load leaderboard. Please check your connection.</span>';
      if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '--';
      if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '--';
      if (topCard) topCard.querySelector('.stat-card-value').textContent = '--';
      
      // Add retry button
      const retryBtn = document.createElement('button');
      retryBtn.className = 'btn btn-sm btn-outline-primary mt-2';
      retryBtn.textContent = 'Retry';
      retryBtn.onclick = () => loadMinigameLeaderboard();
      lbDiv.appendChild(retryBtn);
    }
  }
}

});
