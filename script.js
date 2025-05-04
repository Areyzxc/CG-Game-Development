document.addEventListener('DOMContentLoaded', () => {
  // ---------------------------------------------------
  // 1. Typed.js Intro
  // ---------------------------------------------------
  new Typed('#intro-text', {
    strings: [
      "Let's practice Programming Languages effectively.",
      "Learn. Compete. Conquer."
    ],
    typeSpeed: 50,
    backSpeed: 25,
    loop: true
  });

  // ---------------------------------------------------
  // 2. Parallax Background
  // ---------------------------------------------------
  new Rellax('.rellax-bg', {
    speed: -2,
    center: true
  });

  // ---------------------------------------------------
  // 3. Entry Animations (Anime.js)
  // ---------------------------------------------------
  anime({
    targets: '.display-4',
    opacity: [0, 1],
    translateY: [-50, 0],
    easing: 'easeOutExpo',
    duration: 1500,
  });

  anime({
    targets: '.lead',
    opacity: [0, 1],
    translateY: [20, 0],
    easing: 'easeOutExpo',
    duration: 1500,
    delay: 500,
  });

   // ---------------------------------------------------
  // 4. Scroll‑triggered Section Animation
  // ---------------------------------------------------
  let hasAnimated = false;
  const extra = document.querySelector('.extra-section');
  
  if (extra) {
    document.addEventListener('scroll', () => {
      const top = extra.getBoundingClientRect().top;
      const triggerPoint = window.innerHeight - 100;
  
      if (top < triggerPoint && !hasAnimated) {
        anime({
          targets: '.extra-section',
          opacity: [0, 1],
          translateY: [50, 0],
          easing: 'easeOutExpo',
          duration: 1000,
        });
        hasAnimated = true; // prevent re-triggering
      }
    });
  }
  
 // ---------------------------------------------------
  // 5. AJAX Form Submission + Validation (with role-based redirect)
  // ---------------------------------------------------
  const handleForm = (config) => {
    const { formId, url, fields, loadingId, redirectMap } = config;
    const form = document.getElementById(formId);
    const loading = document.getElementById(loadingId);

    form.addEventListener('submit', (e) => {
      e.preventDefault();

      // Reset validation
      fields.forEach(f => {
        f.errorEl.textContent = '';
        f.errorEl.style.display = 'none';
        f.input.classList.remove('is-invalid');
      });

      // Gather form data
      const data = new FormData(form);
      let hasError = false;

      // Field‑by‑field validation
      fields.forEach(f => {
        const val = data.get(f.name)?.trim();
        if (f.required && !val) {
          f.errorEl.textContent = `${f.label} is required.`;
          f.errorEl.style.display = 'block';
          f.input.classList.add('is-invalid');
          hasError = true;
        }
        if (f.name === 'email' && val && !val.includes('@')) {
          f.errorEl.textContent = 'Invalid email address.';
          f.errorEl.style.display = 'block';
          f.input.classList.add('is-invalid');
          hasError = true;
        }
        if (f.name === 'password' && val && val.length < 6) {
          f.errorEl.textContent = 'Password must be at least 6 characters.';
          f.errorEl.style.display = 'block';
          f.input.classList.add('is-invalid');
          hasError = true;
        }
      });

      if (hasError) {
        anime({
          targets: `#${formId} .modal-content`,
          translateX: [-10, 10, -10, 0],
          duration: 500,
          easing: 'easeInOutQuad'
        });
        return;
      }

      // Show loader
      loading.textContent = 'Processing...';
      loading.classList.add('active');

      // Send AJAX
      fetch(url, { method: 'POST', body: data })
        .then(res => res.json())
        .then(json => {
          loading.classList.remove('active');
          if (json.success) {
            // Close modal via Bootstrap API
            const modalEl = form.closest('.modal');
            bootstrap.Modal.getInstance(modalEl).hide();

            // If a redirectMap is provided and the role matches, redirect
            if (redirectMap && json.role && redirectMap[json.role]) {
              window.location.href = redirectMap[json.role];
              return;
            }

            // Default success feedback
            alert('Success! ✅');
          } else {
            alert(`Error: ${json.error}`);
          }
        })
        .catch(err => {
          loading.classList.remove('active');
          console.error('AJAX Error:', err);
        });
    });
  };

  // Sign‑Up Form Config
  handleForm({
    formId: 'signInForm',
    url: 'sign_in.php',
    loadingId: 'loadingIndicator',
    fields: [
      {
        name: 'username',
        label: 'Username',
        required: true,
        input: document.getElementById('signUsername'),
        errorEl: document.getElementById('nameError')
      },
      {
        name: 'email',
        label: 'Email',
        required: true,
        input: document.getElementById('signEmail'),
        errorEl: document.getElementById('emailError')
      },
      {
        name: 'password',
        label: 'Password',
        required: true,
        input: document.getElementById('signPassword'),
        errorEl: document.getElementById('passwordError')
      },
    ]
  });

// Login Form Config with role‑based redirects
handleForm({
  formId: 'loginForm',
  url: 'login.php',
  loadingId: 'loginLoadingIndicator',
  fields: [
    {
      name: 'email',
      label: 'Email',
      required: true,
      input: document.getElementById('loginEmail'),
      errorEl: document.getElementById('loginEmailError')
    },
    {
      name: 'password',
      label: 'Password',
      required: true,
      input: document.getElementById('loginPassword'),
      errorEl: document.getElementById('loginPasswordError')
    },
  ],
  redirectMap: {
    admin: 'admin-dashboard.php',
    player: 'home.php'
  }
});