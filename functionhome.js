document.addEventListener('DOMContentLoaded', () => {
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
  // 32. Typed.js Triggers
  // ─────────────────────────────────────────────────────────
  // Grab the username from the navbar
  const username = document.getElementById('usernameDisplay').textContent.trim();

  new Typed('#welcomeTyped', {
    strings: [
      `Welcome back, ${username}!`,
      'Ready to conquer your next coding challenge?'
    ],
    typeSpeed: 50,
    backSpeed: 25,
    loop: true
  });

  // ─────────────────────────────────────────────────────────
  // 3. Rellax Initialization
  // ─────────────────────────────────────────────────────────
  new Rellax('.rellax-bg', {
    speed: -2,
    center: true
  });
});

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

  // ─────────────────────────────────────────────────────────
  // 9️⃣ Back-to-Top Button
  // ─────────────────────────────────────────────────────────
  const backBtn = document.getElementById('backToTop');

  // Show button after scrolling down 300px
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backBtn.style.display = 'block';
    } else {
      backBtn.style.display = 'none';
    }
  });

  // Smooth scroll to top
  backBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });




