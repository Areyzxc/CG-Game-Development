/**
 * Home Page Enhancements
 * - Background slideshow
 * - Motivational quotes rotation
 * - Dynamic progress loading
 * - Real-time announcements
 */

// Global interval variables
let quoteInterval = null;
let slideshowInterval = null;

// Coding quotes collection
const motivationalQuotes = [
    {
        text: "Code is poetry.",
        author: "WordPress"
    },
    {
        text: "Programming is thinking, not typing.",
        author: "Casey Patton"
    },
    {
        text: "The best error message is the one that never shows up.",
        author: "Thomas Fuchs"
    },
    {
        text: "Clean code is simple and direct. Clean code reads like well-written prose. Clean code never obscures the designers’ intent but rather is full of crisp abstractions and straightforward lines of control.",
        author: "Grady Booch"
    },
    {
        text: "Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live.",
        author: "John Woods"
    },
    {
        text: "A long descriptive name is better than a short enigmatic name. A long descriptive name is better than a long descriptive comment.",
        author: "Robert C. Martin"
    },
    {
        text: "Talk is cheap. Show me the code.",
        author: "Linus Torvalds"
    },
    {
        text: "First, solve the problem. Then, write the code.",
        author: "John Johnson"
    },
    {
        text: "Experience is the name everyone gives to their mistakes.",
        author: "Oscar Wilde"
    },
    {
        text: "In order to be irreplaceable, one must always be different.",
        author: "Coco Chanel"
    },
    {
        text: "Java is to JavaScript what car is to Carpet.",
        author: "Chris Heilmann"
    },
    {
        text: "The most important property of a program is whether it accomplishes the intention of its user.",
        author: "C.A.R. Hoare"
    },
    {
        text: "Any fool can write code that a computer can understand. Good programmers write code that humans can understand.",
        author: "Martin Fowler"
    },
    {
        text: "Programming isn't about what you know; it's about what you can figure out.",
        author: "Chris Pine"
    },
    {
        text: "The only way to learn a new programming language is by writing programs in it.",
        author: "Dennis Ritchie"
    },
    {
        text: "Code never lies, comments sometimes do.",
        author: "Ron Jeffries"
    },
    {
        text: "Simplicity is the ultimate sophistication.",
        author: "Leonardo da Vinci"
    },
    {
        text: "Make it work, make it right, make it fast.",
        author: "Kent Beck"
    },
    {  
        text: "The most damaging phrase in the language is, It's always been done this way.",
        author: "Grace Hopper"
    },
    {  
        text: "Coding like poetry should be short and concise.",
        author: "Santosh Kalwar"
    },
    {  
        text: "C++ is designed to allow you to express ideas, but if you don't have ideas or don't have any clue about how to express them, C++ doesn't offer much help.",
        author: "Bjarne Stroustrup"
    },
    {  
        text: "Perfection is achieved not when there is nothing more to add, but rather when there is nothing more to take away.",
        author: "Antoine de Saint-Exupéry"
    },
    {  
        text: "Good code is its own best documentation.",
        author: "Steve McConnell"
    },
    {  
        text: "Programs must be written for people to read, and only incidentally for machines to execute.",
        author: "Harold Abelson"
    },
];


// Actual background images (add these files to assets/images/ directory)
const backgroundImages = [
    'assets/images/background-1.jpg',
    'assets/images/background-2.jpg', 
    'assets/images/background-3.jpg',
    'assets/images/background-4.jpg',
    'assets/images/background-5.jpg',
    'assets/images/background-6.jpg',
    'assets/images/background-7.jpg',
    'assets/images/background-8.jpg',
    'assets/images/background-9.gif',
    'assets/images/background-110.gif',
    'assets/images/background-11.gif',
    'assets/images/background-12.jpg',
    'assets/images/background-13.jpg',
    'assets/images/background-14.gif',
    'assets/images/background-15.gif',
    'assets/images/background-16.gif',
    'assets/images/background-17.gif',
    'assets/images/background-18.gif',
    'assets/images/background-19.gif',
    'assets/images/background-20.jpg',
    'assets/images/bg-coding-1.jpg',
    'assets/images/bg-coding-2.jpg',
    'assets/images/bg-coding-3.jpg',
    'assets/images/bg-programming-1.jpg',
    'assets/images/bg-tech-1.jpg'
];

let currentSlideIndex = 0;

// Load announcements from the server
async function loadAnnouncements() {
    console.log('Loading announcements...');
    const container = document.getElementById('announcementsContainer');
    
    if (!container) {
        console.warn('Announcements container not found');
        return;
    }
    
    // Show loading state
    container.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading announcements...</span>
            </div>
        </div>`;
    
    try {
        const response = await fetch('/CodeGaming/api/get-announcements.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Announcements loaded:', data);
        
        if (data.success && data.data && data.data.length > 0) {
            if (typeof renderAnnouncements === 'function') {
                renderAnnouncements(data.data);
            } else {
                console.error('renderAnnouncements function not found');
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bx bx-error-circle me-2"></i>
                        Error displaying announcements. Please refresh the page.
                    </div>`;
            }
        } else {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-2"></i>
                    No announcements available. Check back later!
                </div>`;
        }
    } catch (error) {
        console.error('Error loading announcements:', error);
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="bx bx-error-circle me-2"></i>
                Failed to load announcements. Please try again later.
            </div>`;
    }
}

// Render announcements in the UI
function renderAnnouncements(announcements) {
    console.log('Rendering announcements:', announcements);
    const container = document.getElementById('announcementsContainer');
    
    if (!container) {
        console.warn('Announcements container not found');
        return;
    }
    
    if (!Array.isArray(announcements) || announcements.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="bx bx-info-circle me-2"></i>
                No announcements available. Check back later!
            </div>`;
        return;
    }
    
    // Sort announcements by date (newest first)
    announcements.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    
    // Limit to 5 most recent announcements
    const recentAnnouncements = announcements.slice(0, 5);
    
    // Generate HTML for each announcement
    const announcementsHTML = recentAnnouncements.map(announcement => {
        // Format date
        const date = new Date(announcement.created_at);
        const formattedDate = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        return `
        <div class="announcement-item mb-3">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="bx bx-bullhorn text-primary fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">${announcement.title || 'Announcement'}</h6>
                    <p class="mb-1 small text-muted">${announcement.content || ''}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${formattedDate}</small>
                        ${announcement.author ? `<small class="text-muted">By ${announcement.author}</small>` : ''}
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-2">`;
    }).join('');
    
    container.innerHTML = `
        <div class="announcements-list">
            ${announcementsHTML}
        </div>
        <div class="text-end mt-2">
            <a href="/CodeGaming/announcements.php" class="btn btn-sm btn-outline-primary">
                View All Announcements <i class="bx bx-chevron-right"></i>
            </a>
        </div>`;
        
    // Add animation to announcements
    const items = container.querySelectorAll('.announcement-item');
    items.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(10px)';
        item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, 100 * index);
    });
}

// Fallback function if loadAnnouncements is not available
function loadAnnouncementsFallback() {
    console.log('Using fallback announcements loading');
    const container = document.getElementById('announcementsContainer');
    if (container) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="bx bx-info-circle me-2"></i>
                Announcements will be loaded shortly...
            </div>`;
    }
}

// Function to initialize all components
function initializeAll() {
    console.log('Initializing all components...');
    
    // Initialize quote spotlight if available
    if (typeof initializeQuoteSpotlight === 'function') {
        try {
            initializeQuoteSpotlight();
        } catch (error) {
            console.error('Error initializing quote spotlight:', error);
            if (typeof showGlobalError === 'function') {
                showGlobalError('Failed to initialize quote spotlight. Please refresh the page.');
            }
        }
    }
    
    // Initialize background slideshow if available
    if (typeof initializeBackgroundSlideshow === 'function') {
        try {
            initializeBackgroundSlideshow();
        } catch (error) {
            console.error('Error initializing background slideshow:', error);
            if (typeof showGlobalError === 'function') {
                showGlobalError('Failed to initialize background slideshow. Please refresh the page.');
            }
        }
    }
    
    // Load user progress if available
    if (typeof loadUserProgress === 'function') {
        loadUserProgress().catch(error => {
            console.error('Error in loadUserProgress:', error);
            if (typeof showGlobalError === 'function') {
                showGlobalError('Failed to load progress data. Please refresh the page.');
            }
        });
    } else {
        console.error('loadUserProgress function not found!');
    }
    
    // Load announcements
    try {
        loadAnnouncements();
    } catch (error) {
        console.error('Error loading announcements:', error);
        try {
            loadAnnouncementsFallback();
        } catch (e) {
            console.error('Fallback announcements loading failed:', e);
        }
    }
}

// Initialize when DOM is fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAll);
} else {
    // DOM is already ready
    initializeAll();
}

// Initialize motivational quotes
function initializeQuoteSpotlight() {
    displayRandomQuote();
    
    // Change quote every 10 seconds
    quoteInterval = setInterval(displayRandomQuote, 10000);
}

// Display a random motivational quote
function displayRandomQuote() {
    const randomIndex = Math.floor(Math.random() * motivationalQuotes.length);
    const quote = motivationalQuotes[randomIndex];
    
    const quoteElement = document.getElementById('currentQuote');
    const authorElement = document.getElementById('quoteAuthor');
    
    if (quoteElement && authorElement) {
        // Fade out
        quoteElement.style.opacity = '0';
        authorElement.style.opacity = '0';
        
        setTimeout(() => {
            quoteElement.textContent = quote.text;
            authorElement.textContent = `— ${quote.author}`;
            
            // Fade in
            quoteElement.style.opacity = '1';
            authorElement.style.opacity = '1';
        }, 300);
    }
}

// Initialize background slideshow
function initializeBackgroundSlideshow() {
    const slideshowElement = document.getElementById('bgSlideshow');
    if (!slideshowElement) return;
    
    // Start with first image
    changeBackgroundImage();
    
    // Change background every 9 seconds
    slideshowInterval = setInterval(changeBackgroundImage, 9000);
}

// Change background image with fade effect
function changeBackgroundImage() {
    const slideshowElement = document.getElementById('bgSlideshow');
    if (!slideshowElement) return;
    
    // Try to load actual image first, fallback to gradient
    const currentImage = backgroundImages[currentSlideIndex];
    
    // Create new image element to preload
    const img = new Image();
    img.onload = function() {
        slideshowElement.style.background = `url('${currentImage}')`;
        slideshowElement.style.backgroundSize = 'cover';
        slideshowElement.style.backgroundPosition = 'center';
        slideshowElement.style.backgroundRepeat = 'no-repeat';
    };
    img.onerror = function() {
        // Fallback to a nice gradient if image fails to load
        const fallbackGradient = 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)';
        slideshowElement.style.background = fallbackGradient;
    };
    img.src = currentImage;
    
    // Move to next image
    currentSlideIndex = (currentSlideIndex + 1) % Math.max(backgroundImages.length);
}

// Load user progress data via AJAX
async function loadUserProgress() {
    const progressContainer = document.getElementById('progressContainer');
    if (!progressContainer) {
        console.warn('Progress container not found, progress tracking disabled');
        return;
    }
    
    // Show loading state
    const loadingHtml = `
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading your progress...</span>
            </div>
            <p class="mt-2 text-muted">Loading your coding journey...</p>
        </div>`;
    
    progressContainer.innerHTML = loadingHtml;
    
    try {
        if (!window.CG_USER_ID) {
            // Handle guest user
            showProgressError('Please log in to track your progress', true);
            return;
        }

        console.log('Loading user progress for user ID:', window.CG_USER_ID);
        
        // Fetch user progress using APIHelper
        const response = await APIHelper.fetchWithAuth('api/get-user-progress.php');
        console.log('API Response:', JSON.stringify(response, null, 2));
        
        if (response && response.error) {
            // Handle API errors
            if (response.error.includes('CSRF') || response.status === 403) {
                console.error('CSRF token issue detected, reloading page...');
                window.location.reload();
                return;
            }
            throw new Error(response.error);
        }
        
        if (response && response.success && response.data) {
            renderProgressCards(response.data);
        } else {
            throw new Error('Invalid response format from server');
        }
    } catch (error) {
        console.error('Error loading progress:', error);
        
        // More specific error handling
        let errorMessage = 'Failed to load progress data';
        let isWarning = false;
        
        if (error.message.includes('NetworkError')) {
            errorMessage = 'Network error. Please check your connection.';
            isWarning = true;
        } else if (error.message.includes('CSRF')) {
            errorMessage = 'Session expired. Refreshing...';
            setTimeout(() => window.location.reload(), 2000);
            return;
        } else if (error.message.includes('Invalid response')) {
            errorMessage = 'Invalid data received from server. Please try again.';
            isWarning = true;
        }
        
        if (typeof showGlobalError === 'function') {
            showGlobalError(errorMessage, isWarning);
        } else {
            console.error('showGlobalError is not defined');
            if (progressContainer) {
                progressContainer.innerHTML = `
                    <div class="alert alert-${isWarning ? 'warning' : 'danger'}">
                        ${errorMessage}
                    </div>`;
            }
        }
    }
}

// Render progress cards with user data
function renderProgressCards(progressData) {
    console.log('Rendering progress cards with data:', progressData);
    
    const progressContainer = document.getElementById('progressContainer');
    const userBanner = document.getElementById('userBanner');
    const userAvatar = document.getElementById('userAvatar');
    const userDisplayName = document.querySelector('.user-display-name') || document.getElementById('userDisplayName');
    const userLevel = document.getElementById('userLevel');
    
    // Check if user is a guest
    const isGuest = !window.CG_USER_ID || window.CG_USER_ID === 'guest';
    
    // Extract user data from progressData with proper fallbacks
    const userData = progressData.user || {};
    
    if (!progressContainer) {
        console.error('Progress container not found');
        return;
    }
    
    // Check if we have valid progress data
    if (!progressData || typeof progressData !== 'object') {
        throw new Error('Invalid progress data received');
    }
    
    // Process profile picture URL with proper error handling
    function processImagePath(path, defaultPath) {
        if (!path) return defaultPath;
        
        try {
            // Clean up the filename - remove any duplicate .jpg/.jpeg/.png extensions
            let cleanPath = path.replace(/\.(jpg|jpeg|png|gif)(\.[^\.]+)?$/i, '.$1');
            
            // If it's already a full URL, use it as is
            if (/^https?:\/\//i.test(cleanPath)) {
                return cleanPath;
            }
            
            // Otherwise, prepend the base path
            cleanPath = '/CodeGaming/' + cleanPath.replace(/^[\\/]+/, '');
            
            // Ensure the path doesn't have double slashes
            return cleanPath.replace(/([^:]\/)\/+/g, '$1');
        } catch (error) {
            console.error('Error processing image path:', { path, error });
            return defaultPath;
        }
    }
    
    try {
        // Extract user data from the response with proper fallbacks
        const userData = progressData.user || progressData.profile || {};
        const stats = progressData.stats || {};
        
        // Process profile picture URL with proper error handling
        function processImagePath(path, defaultPath) {
            if (!path) return defaultPath;
            
            try {
                // Clean up the filename - remove any duplicate .jpg/.jpeg/.png extensions
                let cleanPath = path.replace(/\.(jpg|jpeg|png|gif)(\.[^\.]+)?$/i, '.$1');
                
                // If it's already a full URL, use it as is
                if (/^https?:\/\//i.test(cleanPath)) {
                    return cleanPath;
                }
                
                // Otherwise, prepend the base path
                cleanPath = '/CodeGaming/' + cleanPath.replace(/^[\\/]+/, '');
                
                // Ensure the path doesn't have double slashes
                return cleanPath.replace(/([^:]\/)\/+/g, '$1');
            } catch (error) {
                console.error('Error processing image path:', { path, error });
                return defaultPath;
            }
        }
        
        console.log('Processing user data:', userData);
        console.log('Processing stats:', stats);
        
        // Update user profile section if elements exist
        if (userAvatar) {
            const defaultAvatar = '/CodeGaming/assets/images/default-avatar.png';
            userAvatar.src = processImagePath(userData.profile_picture, defaultAvatar);
        }
        
        // Process banner URL with timestamp to prevent caching
        let bannerUrl = '/CodeGaming/assets/images/default-banner.jpg';
        if (userBanner) {
            bannerUrl = processImagePath(userData.header_banner, bannerUrl);
            const timestamp = new Date().getTime();
            const bannerWithTimestamp = `${bannerUrl}${bannerUrl.includes('?') ? '&' : '?'}t=${timestamp}`;
            userBanner.style.backgroundImage = `url('${bannerWithTimestamp}')`;
            console.log('Setting banner URL:', bannerWithTimestamp);
        }
        
        if (userDisplayName) {
            userDisplayName.textContent = userData.username || userData.display_name || 'User';
        }
        
        if (userLevel) {
            userLevel.textContent = 'Coding Enthusiast';
        }
        
        // Prepare progress cards data
        const cardsData = [
            {
                title: 'Tutorials',
                icon: 'bx-book',
                progress: stats.tutorials_completed || 0,
                total: stats.total_tutorials || 10,
                description: 'Completed tutorials',
                color: 'primary'
            },
            {
                title: 'Quizzes',
                icon: 'bx-check-circle',
                progress: stats.quizzes_passed || 0,
                total: stats.total_quizzes || 0,
                description: 'Quizzes passed',
                color: 'success'
            },
            {
                title: 'Challenges',
                icon: 'bx-trophy',
                progress: stats.challenges_completed || 0,
                total: stats.total_challenges || 0,
                description: 'Challenges completed',
                color: 'warning'
            }
        ];
        
        // Generate progress cards HTML
        const cardsHTML = cardsData.map(card => `
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="icon-circle bg-${card.color}-subtle text-${card.color} mb-3">
                            <i class="bx ${card.icon} fs-4"></i>
                        </div>
                        <h5 class="card-title mb-1">${card.title}</h5>
                        <h3 class="mb-2">${card.progress}<small class="text-muted">/${card.total}</small></h3>
                        <p class="text-muted small mb-0">${card.description}</p>
                        <div class="progress mt-3" style="height: 4px;">
                            <div class="progress-bar bg-${card.color}" role="progressbar" 
                                 style="width: ${card.total > 0 ? Math.min((card.progress / card.total) * 100, 100) : 0}%" 
                                 aria-valuenow="${card.progress}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="${card.total}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Update the progress container
        progressContainer.innerHTML = `
            <div class="row g-4">
                ${cardsHTML}
            </div>
            <div class="text-center mt-4">
                <a href="profile.php" class="btn btn-outline-primary">
                    <i class="bx bx-user me-2"></i>View Full Profile
                </a>
            </div>
        `;
    } catch (error) {
        console.error('Error rendering progress cards:', error);
        // Make sure showProgressError is defined before calling it
        if (typeof showProgressError === 'function') {
            showProgressError('Failed to display progress data. Please try again later.', true);
        } else {
            console.error('showProgressError function is not defined');
            const progressContainer = document.getElementById('progressContainer');
            if (progressContainer) {
                progressContainer.innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load progress data. Please refresh the page.
                    </div>`;
            }
        }
        return; // Exit the function if there was an error
    }
    
    // Update avatar if element exists
    if (userAvatar) {
        try {
            const defaultAvatar = '/CodeGaming/assets/images/default-avatar.png';
            const avatarUrl = userData && userData.profile_picture ? 
                processImagePath(userData.profile_picture, defaultAvatar) : 
                defaultAvatar;
            
            // Update the avatar source with a timestamp to prevent caching
            const timestamp = new Date().getTime();
            const cleanAvatarUrl = avatarUrl.split('?')[0]; // Remove any existing query parameters
            const avatarWithTimestamp = `${cleanAvatarUrl}?t=${timestamp}`;
            
            // Set up the onload and onerror handlers first
            userAvatar.onload = function() {
                console.log('Avatar image loaded successfully');
                this.style.opacity = '1';
                this.style.visibility = 'visible';
            };
            
            userAvatar.onerror = function() {
                console.error('Error loading profile image, using default avatar');
                this.src = defaultAvatar;
                this.alt = 'Default Profile Picture';
                this.style.opacity = '1';
                this.style.visibility = 'visible';
            };
            
            // Log the avatar loading
            console.log('Setting avatar source:', {
                originalPath: userData?.profile_picture || 'N/A',
                processedPath: avatarUrl,
                finalUrl: avatarWithTimestamp
            });
            
            // Set the source last to trigger loading
            userAvatar.src = avatarWithTimestamp;
            userAvatar.alt = `${userData?.username || 'User'}'s Profile Picture`;
            userAvatar.style.opacity = '0';
            userAvatar.style.visibility = 'hidden';
            userAvatar.style.transition = 'opacity 0.3s ease-in-out';
        } catch (error) {
            console.error('Error updating avatar:', error);
            userAvatar.src = '/CodeGaming/assets/images/default-avatar.png';
            userAvatar.alt = 'Default Profile Picture';
            userAvatar.style.opacity = '1';
            userAvatar.style.visibility = 'visible';
        }
    }
    
    // Update display name if element exists
    if (userDisplayName && userData) {
        const displayName = userData.username || userData.display_name || 'User';
        userDisplayName.textContent = `Welcome, ${displayName}!`;
    }
    
    // Debug: Log the progress data structure
    console.log('Progress Data:', progressData);
    
    // Tutorial data - using the structure from get-user-progress.php
    const tutorialData = progressData.tutorials || {};
    console.log('Tutorial Data:', tutorialData);
    
    // Extract tutorial progress data with proper fallbacks
    const tutorialCompleted = tutorialData.completed || 0;
    const tutorialTotal = tutorialData.total || 1; // Avoid division by zero
    const tutorialPercentage = tutorialData.percentage || 0;
    const nextTopic = 'Continue Learning'; // Default next topic
    
   
    // Profile completeness - use the value from the API if available
    const profileInfo = progressData.profile || {};
    const profilePercentage = profileInfo.completeness || 0;
    const profileCompleted = profileInfo.completed_fields || 0;
    const profileTotal = profileInfo.total_fields || 6; // Default to 6 fields
    
    // Activity data - we'll keep this simple for now
    // If you need to track specific activities, we can add them back later
    
    progressContainer.innerHTML = `
        <div class="col-md-6">
            <div class="progress-card">
                <div class="progress-icon">
                    <i class="bx bx-book-open"></i>
                </div>
                <h5>Tutorials</h5>
                <p class="progress-text">${isGuest ? 'Start learning' : 'Enhance your skills'}</p>
                <div class="mt-3">
                    <a href="tutorial.php" class="btn btn-primary btn-sm">
                        ${isGuest ? 'Start Learning' : 'Continue Learning'}
                    </a>
                </div>
                ${isGuest ? '' : `
                <small class="text-muted">
                    ${tutorialCompleted} of ${tutorialTotal} topics completed
                </small>
                <div class="next-topic">Next: ${nextTopic}</div>
                `}
            </div>
        </div>
        <div class="col-md-6">
            <div class="progress-card">
                <div class="progress-icon">
                    <i class="bx bx-${isGuest ? 'log-in' : 'user-circle'}"></i>
                </div>
                <h5>${isGuest ? 'Get Started' : 'Profile'}</h5>
                <p class="progress-text">${isGuest ? 'Create your account' : 'Customize your coding profile'}</p>
                <div class="profile-completeness">
                    <span class="completeness-text">${isGuest ? 'Join the community' : `${profilePercentage}% Complete`}</span>
                </div>
                <small class="text-muted">
                    ${isGuest ? 'Save your progress and track achievements' : `${profileCompleted}/${profileTotal} fields completed`}
                </small>
            </div>
        </div>
    `;
    
    // Animate progress bars
    setTimeout(() => {
        const progressBars = progressContainer.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }, 200);

// Show error state for progress loading
function showProgressError(message, isWarning = false) {
    const progressContainer = document.getElementById('progressContainer');
    if (!progressContainer) {
        console.error('Progress container not found for error:', message);
        return;
    }
    
    const alertClass = isWarning ? 'alert-warning' : 'alert-danger';
    const icon = isWarning ? 'exclamation-triangle' : 'exclamation-octagon';
    
    progressContainer.innerHTML = `
        <div class="col-12">
            <div class="alert ${alertClass} d-flex align-items-center" role="alert">
                <i class="bi bi-${icon} me-2"></i>
                <div>${message}</div>
                ${isWarning ? `
                <button type="button" class="btn btn-sm btn-outline-${isWarning ? 'warning' : 'danger'} ms-auto" 
                        onclick="loadUserProgress()">
                    <i class="bi bi-arrow-repeat"></i> Retry
                </button>` : ''}
            </div>
        </div>`;
}

// Make the function available globally for inline event handlers
if (typeof window.showGlobalError === 'undefined') {
    window.showGlobalError = showGlobalError;
}

// Load user progress via AJAX
window.loadUserProgress = async function() {
    console.log('loadUserProgress function called');
    const progressContainer = document.getElementById('progressContainer');
    if (!progressContainer) {
        console.error('Progress container not found. Check if the element with id="progressContainer" exists in the HTML.');
        return;
    }

    // Show loading state
    progressContainer.innerHTML = `
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading your progress...</span>
            </div>
            <p class="mt-2 text-muted">Loading your coding journey...</p>
        </div>`;

    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        // Fetch user progress data
        const response = await fetch('api/get-user-progress.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            credentials: 'same-origin'
        });

        console.log('Progress API response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Progress data received:', data);

        if (data.success && data.data) {
            renderProgressCards(data.data);
        } else {
            const errorMsg = data.message || 'Failed to load progress data';
            console.error('API error:', errorMsg);
            showProgressError(errorMsg, true);
        }
    } catch (error) {
        console.error('Error loading user progress:', error);
        showProgressError('Failed to load your progress. Please try again later.', true);
    }
};

// Load announcements via AJAX
window.loadAnnouncements = async function() {
    console.log('loadAnnouncements function called');
    const announcementsContainer = document.getElementById('announcementsContainer');
    if (!announcementsContainer) {
        console.error('Announcements container not found. Check if the element with id="announcementsContainer" exists in the HTML.');
        return;
    }
    console.log('Found announcements container:', announcementsContainer);
    
    // Show loading state
    announcementsContainer.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading announcements...</p>
        </div>
    `;
    
    try {
        // Use relative URL to avoid CORS issues
        const apiUrl = 'api/get-announcements.php?guest=1';
        console.log('Fetching announcements from:', apiUrl);
        
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        console.log('Response status:', response.status, response.statusText);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response text:', errorText);
            throw new Error(`HTTP error! status: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json().catch(e => {
            console.error('Failed to parse JSON response:', e);
            throw new Error('Invalid JSON response from server');
        });
        
        console.log('Announcements API response:', data);
        
        if (data.success && data.data && data.data.length > 0) {
            renderAnnouncements(data.data);
        } else {
            const errorMsg = data.message || 'No announcements available';
            console.warn('No announcements found:', errorMsg);
            showAnnouncementsError(errorMsg, true);
        }
    } catch (error) {
        console.error('Error loading announcements:', error);
        showAnnouncementsError(error.message || 'Failed to load announcements', false);
    }
}

// Make renderAnnouncements globally available
window.renderAnnouncements = function(announcements) {
    const announcementsContainer = document.getElementById('announcementsContainer');
    if (!announcementsContainer) return;
    
    // Ensure announcements is an array
    if (!Array.isArray(announcements)) {
        console.error('Expected announcements to be an array, got:', announcements);
        showAnnouncementsError();
        return;
    }
    
    if (announcements.length === 0) {
        announcementsContainer.innerHTML = `
            <div class="alert alert-info text-center">
                <i class="bx bx-info-circle me-2"></i>
                No announcements yet. <a href="announcements.php" class="alert-link">Check back later</a>
            </div>
        `;
        return;
    }
    
    try {
        const announcementsHTML = announcements.map(announcement => {
            // Handle different possible property names
            const id = announcement.id || announcement.announcement_id || '';
            const title = announcement.title || 'No Title';
            const content = announcement.brief_content || announcement.content || '';
            const author = announcement.author || 'Admin';
            const relativeTime = announcement.relative_time || 
                               (announcement.created_at ? getRelativeTime(announcement.created_at) : 'recently');
            
            // Get icon based on type or use default
            let icon = 'bx-info-circle';
            if (announcement.icon) {
                icon = announcement.icon;
            } else if (announcement.type) {
                const typeIcons = {
                    'update': 'bx-star',
                    'maintenance': 'bx-wrench',
                    'event': 'bx-calendar-event',
                    'feature': 'bx-rocket',
                    'bug_fix': 'bx-bug',
                    'general': 'bx-info-circle'
                };
                icon = typeIcons[announcement.type] || 'bx-info-circle';
            }
            
            // Truncate content for preview
            const previewContent = content.length > 100 
                ? content.substring(0, 100) + '...' 
                : content;
            
            return `
                <div class="announcement-item" onclick="window.location.href='announcements.php?id=${id}'">
                    <div class="announcement-header">
                        <div class="announcement-icon">
                            <i class="bx ${icon}"></i>
                        </div>
                        <h5 class="announcement-title">${escapeHtml(title)}</h5>
                    </div>
                    <div class="announcement-content">
                        ${escapeHtml(previewContent)}
                    </div>
                    <div class="announcement-meta">
                        <span>By ${escapeHtml(author)} • ${relativeTime}</span>
                        <a href="announcements.php?id=${id}" class="read-more-link">Read More</a>
                    </div>
                </div>
            `;
        }).join('');
        
        announcementsContainer.innerHTML = announcementsHTML;
        
    } catch (error) {
        console.error('Error rendering announcements:', error);
    }
}

// Show error state for announcements
function showAnnouncementsError(message = '', isWarning = false) {
    const announcementsContainer = document.getElementById('announcementsContainer');
    if (!announcementsContainer) {
        console.error('Announcements container not found when showing error');
        return;
    }
    
    const alertType = isWarning ? 'info' : 'warning';
    const icon = isWarning ? 'bx-info-circle' : 'bx-error-circle';
    const title = isWarning ? 'No Announcements' : 'Unable to load announcements';
    
    const errorDetails = message ? 
        `<div class="small text-muted mt-2">${escapeHtml(message)}</div>` : '';
    
    // Create a more user-friendly message for common errors
    let friendlyMessage = message;
    if (message.includes('Failed to fetch')) {
        friendlyMessage = 'Unable to connect to the server. Please check your internet connection.';
    } else if (message.includes('JSON')) {
        friendlyMessage = 'Received an invalid response from the server.';
    }
    
    announcementsContainer.innerHTML = `
        <div class="alert alert-${alertType} mb-0">
            <div class="d-flex align-items-start">
                <i class="bx ${icon} fs-4 mt-1 me-2"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">${title}</h5>
                    ${friendlyMessage ? `<p class="mb-2">${escapeHtml(friendlyMessage)}</p>` : ''}
                    ${errorDetails}
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button class="btn btn-sm btn-outline-${alertType}" onclick="loadAnnouncements()">
                            <i class="bx bx-refresh me-1"></i> Try Again
                        </button>
                        ${!isWarning ? `
                        <a href="announcements.php" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="bx bx-list-ul me-1"></i> View All Announcements
                        </a>` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Utility function to escape HTML
function escapeHtml(unsafe) {
    if (typeof unsafe !== 'string') return '';
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


// Initialize the page when DOM is loaded
function initializeHomePage() {
    console.log('Initializing home page...');
    
    // Load announcements
    if (typeof loadAnnouncements === 'function') {
        console.log('Loading announcements...');
        loadAnnouncements().catch(error => {
            console.error('Error loading announcements:', error);
        });
    } else {
        console.error('loadAnnouncements function not found!');
    }
    
    // Initialize other components if they exist
    if (typeof initializeQuoteSpotlight === 'function') {
        initializeQuoteSpotlight();
    }
    
    if (typeof initializeBackgroundSlideshow === 'function') {
        initializeBackgroundSlideshow();
    }
    
    if (typeof loadUserProgress === 'function') {
        loadUserProgress();
    }
}

// Cleanup intervals when page unloads
function cleanupIntervals() {
    console.log('Cleaning up intervals...');
    if (typeof quoteInterval !== 'undefined' && quoteInterval) {
        clearInterval(quoteInterval);
        quoteInterval = null;
    }
    if (typeof slideshowInterval !== 'undefined' && slideshowInterval) {
        clearInterval(slideshowInterval);
        slideshowInterval = null;
    }
}

// Make loadAnnouncements available globally
if (typeof window.loadAnnouncements === 'undefined') {
    window.loadAnnouncements = loadAnnouncements;
}

// Clean up on page unload
window.addEventListener('beforeunload', cleanupIntervals);}