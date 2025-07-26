/**
 * File: about-function.js
 * Purpose: Handles interactive logic for the CodeGaming About page, including scrollspy, feedback, proponent modal, and AJAX actions.
 * Features:
 *   - Implements scrollspy navigation for proponent cards.
 *   - Handles floating feedback button and AJAX feedback form submission.
 *   - Supports feedback wall with like and pagination via AJAX.
 *   - Manages proponent modal open/close and dynamic content population.
 * Usage:
 *   - Included on the About page for team info, feedback, and interactive UI.
 *   - Requires HTML elements for scrollspy, feedback form, feedback wall, proponent cards, and modal.
 *   - Relies on API endpoints: api/send-feedback.php, api/like-feedback.php.
 * Included Files/Dependencies:
 *   - Bootstrap (modals, carousel)
 *   - FontAwesome (icons)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// Scrollspy for proponents
window.addEventListener('DOMContentLoaded', function() {
  // Scrollspy highlight
  const links = document.querySelectorAll('#proponent-scrollspy a');
  const cards = document.querySelectorAll('.proponent-card');
  window.addEventListener('scroll', function() {
    let fromTop = window.scrollY + 120;
    links.forEach(link => {
      let section = document.querySelector(link.getAttribute('href'));
      if (section.offsetTop <= fromTop && section.offsetTop + section.offsetHeight > fromTop) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  });

  // Floating feedback button scrolls to form
  const feedbackBtn = document.getElementById('floating-feedback-btn');
  const inquirySection = document.getElementById('about-inquiry');
  if (feedbackBtn && inquirySection) {
    feedbackBtn.addEventListener('click', function() {
      inquirySection.scrollIntoView({ behavior: 'smooth' });
    });
  }

  // AJAX Feedback form submission
  const feedbackForm = document.getElementById('feedback-form');
  const feedbackModal = document.getElementById('feedback-modal');
  const feedbackList = document.getElementById('feedback-list');
  if (feedbackForm) {
    feedbackForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(feedbackForm);
      fetch('api/send-feedback.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Add to feedback wall
          const name = formData.get('name');
          const message = formData.get('message');
          if (feedbackList && name && message) {
            const li = document.createElement('li');
            li.innerHTML = `<strong>${name}:</strong> ${message}`;
            feedbackList.prepend(li);
          }
          // Show modal (Bootstrap 5)
          if (feedbackModal) {
            var modal = new bootstrap.Modal(feedbackModal);
            modal.show();
          }
          feedbackForm.reset();
        } else {
          alert(data.error || 'An error occurred.');
        }
      })
      .catch(() => {
        alert('An error occurred while sending feedback.');
      });
    });
  }

  // Placeholder: Carousel logic (if needed)
  // Bootstrap handles carousel by default

  // Like button AJAX
  function bindLikeButtons() {
    document.querySelectorAll('.like-btn').forEach(btn => {
      btn.onclick = function() {
        const feedbackId = this.getAttribute('data-id');
        fetch('api/like-feedback.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'feedback_id=' + encodeURIComponent(feedbackId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            this.querySelector('.like-count').textContent = data.likes;
            this.disabled = true;
            this.classList.add('liked');
          } else if (data.error === 'Already liked') {
            this.disabled = true;
            this.classList.add('liked');
          }
        });
      };
    });
  }
  // Pagination AJAX
  function bindPaginationButtons() {
    document.querySelectorAll('.feedback_page_btn').forEach(btn => {
      btn.onclick = function() {
        const page = this.getAttribute('data-page');
        fetch(window.location.pathname + '?feedback_page=' + page)
          .then(res => res.text())
          .then(html => {
            // Extract feedback wall content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newWall = doc.querySelector('.feedback-wall');
            if (newWall) {
              document.querySelector('.feedback-wall').innerHTML = newWall.innerHTML;
              bindLikeButtons();
              bindPaginationButtons();
            }
          });
      };
    });
  }
  // Initial bind
  bindLikeButtons();
  bindPaginationButtons();

  // === Proponent Modal Functionality ===
  const proponents = [
    {
      name: 'Belza, John Jaylyn I.',
      role: 'Lead Developer',
      age: '21',
      email: 'jibelza@paterostechnologicalcollege.edu.ph',
      code: 'CG-001',
      socials: [
        { icon: 'fab fa-facebook', url: '#' },
        { icon: 'fab fa-instagram', url: '#' },
        { icon: 'fab fa-github', url: '#' }
      ],
      funfact: 'Loves debugging at 3AM and retro games.',
      mission: 'Always pushing the boundaries of code!',
      image: 'images/background.png'
    },
    {
      name: 'Constantino, Alvin Jr. B.',
      role: 'UI/UX Designer',
      age: '22',
      email: 'ajbconstantino@paterostechnologicalcollege.edu.ph',
      code: 'CG-002',
      socials: [
        { icon: 'fab fa-facebook', url: '#' },
        { icon: 'fab fa-instagram', url: '#' },
        { icon: 'fab fa-github', url: '#' }
      ],
      funfact: 'Sketches wireframes on napkins.',
      mission: 'Designs with both fun and function in mind.',
      image: 'images/background.png'
    },
    {
      name: 'Sabangan, Ybo T.',
      role: 'Backend Designer',
      age: '22',
      email: 'ytsabangan@paterostechnologicalcollege.edu.ph',
      code: 'CG-003',
      socials: [
        { icon: 'fab fa-facebook', url: '#' },
        { icon: 'fab fa-instagram', url: '#' },
        { icon: 'fab fa-github', url: '#' }
      ],
      funfact: 'API wizard and database whisperer.',
      mission: 'Keeps the engine running smoothly.',
      image: 'images/background.png'
    },
    {
      name: 'Santiago, James Aries G.',
      role: 'Frontend Developer',
      age: '21',
      email: 'jgsantiago@paterostechnologicalcollege.edu.ph',
      code: 'CG-004',
      socials: [
        { icon: 'fab fa-facebook', url: 'https://www.facebook.com/Areyszxc' },
        { icon: 'fab fa-instagram', url: 'https://www.instagram.com/areys27_tiago.san/?hl=en' },
        { icon: 'fab fa-github', url: 'https://github.com/Areyzxc' }
      ],
      funfact: 'Can code and meme at the same time.',
      mission: 'Lezzgoooo, the show must go onnnnnnnnnnnnnnnnnnnnnnnn. We hope our Capstone will be successful (again).',
      image: 'images/background.png'
    },
    {
      name: 'Silvestre, Jesse Lei C.',
      role: 'QA & Tester',
      age: '22',
      email: 'jcsilvestre@paterostechnologicalcollege.edu.ph',
      code: 'CG-005',
      socials: [
        { icon: 'fab fa-facebook', url: '#' },
        { icon: 'fab fa-instagram', url: '#' },
        { icon: 'fab fa-github', url: '#' }
      ],
      funfact: 'Finds bugs even in dreams.',
      mission: 'Ensures every feature is pixel-perfect.',
      image: 'images/background.png'
    },
    {
      name: 'Valencia, Paul Dexter',
      role: 'Documentation & Support',
      age: '21',
      email: 'psvalencia@paterostechnologicalcollege.edu.ph',
      code: 'CG-006',
      socials: [
        { icon: 'fab fa-facebook', url: '#' },
        { icon: 'fab fa-instagram', url: '#' },
        { icon: 'fab fa-github', url: '#' }
      ],
      funfact: 'Writes docs with style and substance.',
      mission: 'Bridges users and devs with clear guides.',
      image: 'images/background.png'
    }
  ];

  function openProponentModal(idx) {
    const modal = document.getElementById('proponent-modal');
    const p = proponents[idx];
    if (!modal || !p) return;
    // Populate modal
    document.getElementById('modal-photo-img').src = p.image;
    document.getElementById('modal-photo-img').alt = p.name;
    document.getElementById('modal-role').textContent = p.role;
    document.getElementById('modal-name').textContent = p.name;
    document.getElementById('modal-age').textContent = p.age;
    document.getElementById('modal-email').innerHTML = '<a href="mailto:' + p.email + '">' + p.email + '</a>';
    document.getElementById('modal-code').textContent = p.code;
    // Socials
    const socialsDiv = document.getElementById('modal-socials');
    socialsDiv.innerHTML = '';
    p.socials.forEach(s => {
      const a = document.createElement('a');
      a.href = s.url;
      a.target = '_blank';
      a.rel = 'noopener noreferrer';
      a.innerHTML = `<i class="${s.icon}"></i>`;
      socialsDiv.appendChild(a);
    });
    document.getElementById('modal-funfact').textContent = p.funfact;
    document.getElementById('modal-mission').textContent = p.mission;
    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      modal.querySelector('.modal-content').focus();
    }, 100);
  }

  function closeProponentModal() {
    const modal = document.getElementById('proponent-modal');
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
    // Optionally reset modal content
    document.getElementById('modal-photo-img').src = '';
    document.getElementById('modal-photo-img').alt = '';
    document.getElementById('modal-role').textContent = '';
    document.getElementById('modal-name').textContent = '';
    document.getElementById('modal-age').textContent = '';
    document.getElementById('modal-email').textContent = '';
    document.getElementById('modal-code').textContent = '';
    document.getElementById('modal-socials').innerHTML = '';
    document.getElementById('modal-funfact').textContent = '';
    document.getElementById('modal-mission').textContent = '';
  }

  // Proponent modal open
  document.querySelectorAll('.proponent-card').forEach((el) => {
    el.addEventListener('click', function() {
      const idx = parseInt(el.getAttribute('data-proponent'), 10) - 1;
      openProponentModal(idx);
    });
  });
  // Close modal on X (both buttons)
  const modal = document.getElementById('proponent-modal');
  if (modal) {
    modal.querySelector('.futuristic-x').addEventListener('click', closeProponentModal);
    const closeX = modal.querySelector('.modal-close-x');
    if (closeX) closeX.addEventListener('click', closeProponentModal);
    // Close on outside click
    modal.addEventListener('mousedown', function(e) {
      if (e.target === modal) closeProponentModal();
    });
    // Close on Esc
    window.addEventListener('keydown', function(e) {
      if (modal.style.display === 'flex' && (e.key === 'Escape' || e.key === 'Esc')) {
        closeProponentModal();
      }
    });
  }
});