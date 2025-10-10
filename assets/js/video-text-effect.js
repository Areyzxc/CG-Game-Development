document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('video-text-canvas');
    const video = document.getElementById('video-source');
    const fallbackText = document.querySelector('.video-text-fallback');
    const container = document.querySelector('.video-text-container');
    
    // Debug info
    console.log('Initializing video text effect...');
    console.log('Canvas:', canvas);
    console.log('Video:', video);
    console.log('Fallback text:', fallbackText);
    
    // Check for required elements and features
    if (!canvas || !video || !fallbackText || !container) {
        console.error('Required elements not found');
        if (fallbackText) fallbackText.style.display = 'flex';
        return;
    }
    
    if (!canvas.getContext) {
        console.warn('Canvas not supported, using fallback');
        fallbackText.style.display = 'flex';
        return;
    }
    
    const ctx = canvas.getContext('2d');
    let animationId = null;
    let isPlaying = false;
    
    // Set canvas size
    function resizeCanvas() {
        const width = container.offsetWidth;
        const height = 150; // Fixed height for better control
        
        // Set canvas dimensions
        canvas.width = width * 2; // Double for retina displays
        canvas.height = height * 2;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        
        // Scale for high DPI displays
        ctx.scale(2, 2);
        
        console.log('Canvas resized:', { width, height });
    }
    
    // Draw the text with video
    function draw() {
        if (!isPlaying) return;
        
        try {
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw the video frame
            ctx.save();
            ctx.drawImage(video, 0, 0, canvas.width / 2, canvas.height / 2);
            
            // Set text properties
            const text = 'CODE GAME';
            const fontSize = Math.min(80, canvas.width / 8);
            ctx.font = `bold ${fontSize}px 'Tourner', sans-serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Create a clipping region for the text
            ctx.globalCompositeOperation = 'source-in';
            
            // Draw the text (this will be clipped by the video)
            ctx.fillStyle = '#fff';
            ctx.fillText(text, canvas.width / 4, canvas.height / 4);
            
            // Add a subtle glow
            ctx.shadowColor = 'rgba(0, 255, 255, 0.8)';
            ctx.shadowBlur = 10;
            ctx.fillText(text, canvas.width / 4, canvas.height / 4);
            
            ctx.restore();
            
            // Show the canvas
            canvas.classList.add('visible');
            
        } catch (error) {
            console.error('Error in draw loop:', error);
            showFallback();
            return;
        }
        
        // Continue the animation
        animationId = requestAnimationFrame(draw);
    }
    
    function showFallback() {
        console.log('Showing fallback text');
        cancelAnimationFrame(animationId);
        canvas.style.display = 'none';
        fallbackText.style.display = 'flex';
    }
    
    // Initialize
    function init() {
        try {
            console.log('Initializing video text effect...');
            
            // Set initial canvas size
            resizeCanvas();
            
            // Handle video events
            video.addEventListener('playing', onVideoPlay);
            video.addEventListener('error', onVideoError);
            
            // Start video playback
            const playPromise = video.play();
            
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.error('Video play failed:', error);
                    showFallback();
                });
            }
            
            // Handle window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    cancelAnimationFrame(animationId);
                    resizeCanvas();
                    if (isPlaying) {
                        draw();
                    }
                }, 100);
            });
            
        } catch (error) {
            console.error('Initialization error:', error);
            showFallback();
        }
    }
    
    function onVideoPlay() {
        console.log('Video started playing');
        isPlaying = true;
        draw();
    }
    
    function onVideoError(error) {
        console.error('Video error:', error);
        showFallback();
    }
    
    // Start everything when video metadata is loaded
    if (video.readyState >= 2) { // HAVE_CURRENT_DATA
        console.log('Video already has metadata');
        init();
    } else {
        console.log('Waiting for video metadata...');
        video.addEventListener('loadedmetadata', init);
        
        // Fallback in case metadata never loads
        setTimeout(() => {
            if (!isPlaying) {
                console.warn('Video metadata load timeout, attempting to play anyway');
                init();
            }
        }, 3000);
    }
    
    // Fallback if video can't play after a delay
    setTimeout(() => {
        if (!isPlaying) {
            console.warn('Video playback timeout, showing fallback');
            showFallback();
        }
    }, 5000);
    
    // Cleanup
    return () => {
        cancelAnimationFrame(animationId);
        video.removeEventListener('playing', onVideoPlay);
        video.removeEventListener('error', onVideoError);
        video.removeEventListener('loadedmetadata', init);
    };
});
