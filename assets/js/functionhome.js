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
  // 1️⃣ Theme Toggle
  // ─────────────────────────────────────────────────────────
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon   = document.getElementById('themeIcon');
  const root        = document.documentElement;

  // Apply saved theme or default to dark
  const savedTheme = localStorage.getItem('theme') || 'dark';
  root.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  // Toggle handler
  themeToggle.addEventListener('click', () => {
    const current = root.getAttribute('data-theme');
    const next    = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
  });

  function updateThemeIcon(theme) {
    if (theme === 'dark') {
      themeIcon.classList.replace('fa-sun', 'fa-moon');
    } else {
      themeIcon.classList.replace('fa-moon', 'fa-sun');
    }
  }

// ─────────────────────────────────────────────────────────
  // 2. Typed.js Triggers
  // ─────────────────────────────────────────────────────────
  // Grab the username from the navbar
  const username = document.getElementById('usernameDisplay').textContent.trim();

  // Enhanced Typed.js with glitch effect
  new Typed('#welcomeTyped', {
    strings: [
      `Welcome back, ${username}!`,
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
      element.style.animation = 'none';
      element.offsetHeight; // Trigger reflow
      element.style.animation = 'glitchText 3s infinite';
    }
  });

  // ─────────────────────────────────────────────────────────
  // 3. Rellax Initialization
  // ─────────────────────────────────────────────────────────
  new Rellax('.rellax-bg', {
    speed: -2,
    center: true
  });

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
      if (closeBtn) {
          closeBtn.addEventListener('click', () => {
              clearTimeout(autoDismissTimer);
              dismissNotification();
          });
      }
  }

  // ─────────────────────────────────────────────────────────
  // 4. Quick-Access Cards Fade-In
  // ─────────────────────────────────────────────────────────
  // Staggered fade-in on load
  anime({
    targets: '.quick-cards .quick-card',
    opacity: [0, 1],
    translateY: [20, 0],
    easing: 'easeOutExpo',
    duration: 800,
    delay: anime.stagger(200, { start: 600 }) // 0.2s between cards, after 0.5s
  });

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
        statsDiv.innerHTML = `
          <div><strong>Best Score:</strong> ${data.user_stats.best_score ?? '-'} / 40 (${data.user_stats.best_percentage ?? '-'}%)</div>
          <div><strong>Last Played:</strong> ${data.user_stats.last_played ? formatTimeAgo(data.user_stats.last_played) : '-'}</div>
          <div><strong>Rank:</strong> ${data.user_stats.rank ? '#' + data.user_stats.rank : '-'}</div>
        `;
      } else {
        statsDiv.innerHTML = '<span>No stats available. Play a quiz to get started!</span>';
      }
    } catch (e) {
      statsDiv.innerHTML = '<span class="text-danger">Failed to load stats.</span>';
      lbDiv.innerHTML = '<span class="text-danger">Failed to load leaderboard.</span>';
      if (bestCard) bestCard.querySelector('.stat-card-value').textContent = '--';
      if (recentCard) recentCard.querySelector('.stat-card-value').textContent = '--';
      if (topCard) topCard.querySelector('.stat-card-value').textContent = '--';
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
    }
  }
}
});




