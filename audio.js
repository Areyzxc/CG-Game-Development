// audio.js

(() => {
    // ─────────────────────────────────────────────────────────
    // 0️⃣ Audio Initialization
    // ─────────────────────────────────────────────────────────
    const tracks = [
      'audio/vhs.mp3',
      'audio/flying.m4a',
      'audio/track3.mp3',
      'audio/track4.mp3',
      'audio/track5.mp3',
      'audio/track6.mp3',
      'audio/track7.mp3'
    ];
    // Pick a random track to start
    const initialTrack = tracks[Math.floor(Math.random() * tracks.length)];
    const bgMusic      = new Audio(initialTrack);
    bgMusic.loop       = true;
    bgMusic.volume     = 0.5;
    bgMusic.muted      = false;
    bgMusic.play().catch(err => console.warn('Autoplay blocked:', err));
  
    // ─────────────────────────────────────────────────────────
    // 1️⃣ Toolbar Element References
    // ─────────────────────────────────────────────────────────
    const playBtn     = document.getElementById('audioPlayPause');
    const playIcon    = document.getElementById('audioPlayIcon');
    const titleSpan   = document.getElementById('audioTitle');
    const volSlider   = document.getElementById('volumeSlider');
    const trackSelect = document.getElementById('trackSelect');
    const visualizer  = document.getElementById('audioVisualizer');
    const ctxVis      = visualizer.getContext('2d');
  
    // Set initial UI state
    titleSpan.textContent = trackSelect.options[trackSelect.selectedIndex].text;
    volSlider.value       = bgMusic.volume;
  
    // ─────────────────────────────────────────────────────────
    // 2️⃣ Play/Pause Toggle
    // ─────────────────────────────────────────────────────────
    playBtn.addEventListener('click', () => {
      if (bgMusic.paused) {
        bgMusic.play();
        playIcon.classList.replace('fa-play', 'fa-pause');
      } else {
        bgMusic.pause();
        playIcon.classList.replace('fa-pause', 'fa-play');
      }
  
      // Resume AudioContext if needed for visualizer
      if (audioCtx.state === 'suspended') {
        audioCtx.resume();
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
      titleSpan.textContent = trackSelect.options[trackSelect.selectedIndex].text;
  
      bgMusic.pause();
      bgMusic.src = newSrc;
      bgMusic.load();
      bgMusic.play().catch(err => console.warn('Playback error:', err));
    });
  
    // ─────────────────────────────────────────────────────────
    // 5️⃣ Visualizer Setup (Web Audio API)
    // ─────────────────────────────────────────────────────────
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const analyser = audioCtx.createAnalyser();
    const source   = audioCtx.createMediaElementSource(bgMusic);
    source.connect(analyser);
    analyser.connect(audioCtx.destination);
    analyser.fftSize = 64;
  
    const bufferLength = analyser.frequencyBinCount;
    const dataArray    = new Uint8Array(bufferLength);
  
    function drawVisualizer() {
      requestAnimationFrame(drawVisualizer);
      analyser.getByteFrequencyData(dataArray);
      ctxVis.clearRect(0, 0, visualizer.width, visualizer.height);
  
      const barWidth = visualizer.width / bufferLength;
      dataArray.forEach((val, i) => {
        const barHeight = (val / 255) * visualizer.height;
        ctxVis.fillStyle = barHeight > visualizer.height * 0.6
          ? getComputedStyle(document.documentElement).getPropertyValue('--code-fg').trim()
          : 'rgba(255,255,255,0.3)';
        ctxVis.fillRect(i * barWidth, visualizer.height - barHeight, barWidth * 0.8, barHeight);
      });
    }
  
    // Kick off visualizer once user interacts (satisfy autoplay policy)
    document.body.addEventListener('click', function resumeOnClick() {
      if (audioCtx.state === 'suspended') {
        audioCtx.resume().then(() => {
          bgMusic.play().catch(() => {});
          drawVisualizer();
        });
      } else {
        drawVisualizer();
      }
      document.body.removeEventListener('click', resumeOnClick);
    });
  })();
  