/**
 * ==========================================================
 * File: audio.js
 * 
 * Description:
 *   - Handles the global audio player toolbar in the footer.
 *   - Features:
 *       • Play/pause, volume, and track selection controls
 *       • Audio visualizer using Web Audio API
 *       • Dynamic track list (syncs with <select> in footer)
 *       • Remembers and cleans up any background music from anchor page
 *       • Handles user interaction, page visibility, and resource cleanup
 * 
 * Usage:
 *   - Included globally via footer.php on all pages with the audio toolbar.
 *   - Requires the footer HTML structure and element IDs/classes to match.
 * 
 * Author: [Santiago]
 * Last Updated: [June 13, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

(() => {
    // ─────────────────────────────────────────────────────────
    // 0️⃣ Audio Initialization
    // ─────────────────────────────────────────────────────────
    
    // Check if there's already background music playing from anchor page
    const existingBackgroundMusic = document.getElementById('backgroundMusic');
    if (existingBackgroundMusic && !existingBackgroundMusic.paused) {
        console.log('Background music already playing, pausing it');
        existingBackgroundMusic.pause();
        existingBackgroundMusic.muted = true;
    }
    
    // Toolbar Element References
    const playBtn = document.getElementById('audioPlayPause');
    const playIcon = document.getElementById('audioPlayIcon');
    const titleSpan = document.getElementById('audioTitle');
    const volSlider = document.getElementById('volumeSlider');
    const trackSelect = document.getElementById('trackSelect');
    const visualizer = document.getElementById('audioVisualizer');
    const ctxVis = visualizer.getContext('2d');

    // Dynamically build tracks array from <select> options
    const tracks = Array.from(trackSelect.options).map(opt => opt.value);
    
    // Pick a random track to start
    const initialTrack = tracks[Math.floor(Math.random() * tracks.length)];
    const bgMusic = new Audio(initialTrack);
    bgMusic.loop = true;
    bgMusic.volume = 0.5;
    bgMusic.muted = false;
    
    // Set initial UI state
    const initialTrackIndex = tracks.indexOf(initialTrack);
    if (initialTrackIndex !== -1) {
        trackSelect.selectedIndex = initialTrackIndex;
    }
    volSlider.value = bgMusic.volume;
    playIcon.classList.replace('fa-pause', 'fa-play'); // Initially show play icon
    
    // ─────────────────────────────────────────────────────────
    // 2️⃣ Play/Pause Toggle
    // ─────────────────────────────────────────────────────────
    playBtn.addEventListener('click', () => {
      if (bgMusic.paused) {
        startAudio(); // Use the unified startAudio function
      } else {
        bgMusic.pause();
        playIcon.classList.replace('fa-pause', 'fa-play');
      }
    });
  
    // ─────────────────────────────────────────────────────────
    // 3️⃣ Volume Control
    // ─────────────────────────────────────────────────────────
    volSlider.addEventListener('input', () => {
      bgMusic.volume = parseFloat(volSlider.value);
    });
  
    // ─────────────────────────────────────────────────────────
    // 4️⃣ Track Selection
    // ─────────────────────────────────────────────────────────
    trackSelect.addEventListener('change', () => {
      const newSrc = trackSelect.value;
      bgMusic.pause();
      bgMusic.src = newSrc;
      bgMusic.load();
      
      startAudio(); // Use the unified startAudio function to play and update UI
    });
  
    // ─────────────────────────────────────────────────────────
    // 5️⃣ Visualizer Setup (Web Audio API)
    // ─────────────────────────────────────────────────────────
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const analyser = audioCtx.createAnalyser();
    const source = audioCtx.createMediaElementSource(bgMusic);
    source.connect(analyser);
    analyser.connect(audioCtx.destination);
    analyser.fftSize = 64;
  
    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);
  
    function drawVisualizer() {
      requestAnimationFrame(drawVisualizer);
      analyser.getByteFrequencyData(dataArray);
      ctxVis.clearRect(0, 0, visualizer.width, visualizer.height);
  
      const barWidth = visualizer.width / bufferLength;
      dataArray.forEach((val, i) => {
        const barHeight = (val / 255) * visualizer.height;
        
        // Use consistent maroon theme colors based on intensity
        if (barHeight > visualizer.height * 0.6) {
          // High intensity: Bright maroon
          ctxVis.fillStyle = '#b03030'; 
        } else if (barHeight > visualizer.height * 0.3) {
          // Medium intensity: Primary maroon
          ctxVis.fillStyle = '#801818';
        } else {
          // Low intensity: Muted maroon
          ctxVis.fillStyle = 'rgba(128, 24, 24, 0.4)';
        }
        
        ctxVis.fillRect(i * barWidth, visualizer.height - barHeight, barWidth * 0.8, barHeight);
      });
    }
  
    // ─────────────────────────────────────────────────────────
    // 6️⃣ Enhanced Autoplay & User Interaction
    // ─────────────────────────────────────────────────────────
    
    // Function to start audio with proper state management
    function startAudio() {
      bgMusic.play().then(() => {
        playIcon.classList.replace('fa-play', 'fa-pause');
        titleSpan.textContent = trackSelect.options[trackSelect.selectedIndex].text; // Update title on successful play
        if (audioCtx.state === 'suspended') {
          audioCtx.resume().then(() => {
            drawVisualizer();
          });
        } else {
          drawVisualizer();
        }
      }).catch(err => {
        console.warn('Autoplay blocked:', err);
        // Keep play icon since autoplay failed
        playIcon.classList.replace('fa-pause', 'fa-play');
      });
    }
    
    // Try autoplay on page load
    startAudio();
    
    // Enhanced user interaction handling
    let userInteracted = false;
    
    function handleUserInteraction() {
      if (!userInteracted) {
        userInteracted = true;
        
        if (audioCtx.state === 'suspended') {
          audioCtx.resume().then(() => {
            if (bgMusic.paused) {
              startAudio();
            } else {
              drawVisualizer();
            }
          });
        } else if (bgMusic.paused) {
          startAudio();
        } else {
          drawVisualizer();
        }
        
        // Remove event listeners after first interaction
        document.removeEventListener('click', handleUserInteraction);
        document.removeEventListener('keydown', handleUserInteraction);
        document.removeEventListener('touchstart', handleUserInteraction);
      }
    }
    
    // Listen for user interactions to enable audio
    document.addEventListener('click', handleUserInteraction);
    document.addEventListener('keydown', handleUserInteraction);
    document.addEventListener('touchstart', handleUserInteraction);
    
    // ─────────────────────────────────────────────────────────
    // 7️⃣ Page Visibility Handling
    // ─────────────────────────────────────────────────────────
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        // Page is hidden, pause audio
        if (!bgMusic.paused) {
          bgMusic.pause();
          playIcon.classList.replace('fa-pause', 'fa-play');
        }
      } else {
        // Page is visible again, resume if it was playing
        if (bgMusic.paused && userInteracted) {
          startAudio();
        }
      }
    });
    
    // ─────────────────────────────────────────────────────────
    // 8️⃣ Cleanup on Page Unload
    // ─────────────────────────────────────────────────────────
    window.addEventListener('beforeunload', () => {
      // Clean up audio context and pause music when leaving page
      if (bgMusic) {
        bgMusic.pause();
        bgMusic.src = '';
      }
      if (audioCtx) {
        audioCtx.close();
      }
    });
    
    // ─────────────────────────────────────────────────────────
    // 9️⃣ Global Audio Management
    // ─────────────────────────────────────────────────────────
    // Store reference to this audio instance globally for cleanup
    window.footerAudioPlayer = {
      pause: () => {
        bgMusic.pause();
        playIcon.classList.replace('fa-pause', 'fa-play');
      },
      play: () => {
        startAudio();
      },
      cleanup: () => {
        bgMusic.pause();
        bgMusic.src = '';
        if (audioCtx) {
          audioCtx.close();
        }
      }
    };
    
  })();
  