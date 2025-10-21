// Handles user progress functionality in admin retro modal
document.addEventListener('DOMContentLoaded', () => {
    // Progress ring initialization
    function initProgressRing(percent) {
        const circle = document.querySelector('.progress-ring__circle-fill');
        if (!circle) {
            console.error('Progress ring circle element not found');
            return;
        }
        
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        const offset = circumference - (percent / 100) * circumference;
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = offset;
    }
    
    // Helper function to get CSRF token
    async function getCSRFToken() {
        // 1. Try to get from meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag && metaTag.content) {
            console.log('CSRF Token found in meta tag');
            return metaTag.content;
        }
        
        // 2. Try to get from window object
        if (window.csrfToken) {
            console.log('CSRF Token found in window.csrfToken');
            return window.csrfToken;
        }
        
        // 3. Try to get from cookie
        const match = document.cookie.match(/csrftoken=([^;]+)/);
        if (match && match[1]) {
            console.log('CSRF Token found in cookie');
            return decodeURIComponent(match[1]);
        }
        
        // 4. Try to fetch a new token from the server
        console.log('Fetching new CSRF token from server...');
        try {
            const response = await fetch('/CodeGaming/api/get-csrf-token.php', {
                method: 'GET',
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (data.token) {
                console.log('Successfully retrieved new CSRF token');
                return data.token;
            }
        } catch (e) {
            console.error('Failed to retrieve CSRF token:', e);
        }
        
        console.error('CSRF token not found in any expected location');
        return null;
    }
    
    // Load and display user progress
    async function loadUserProgress(userId) {
        console.log('Loading progress for user ID:', userId);
        
        // Show loading state
        const progressContent = document.getElementById('progressContent');
        if (progressContent) {
            progressContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`;
        }

        // Get CSRF token from meta tag (same as profile.js)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // Make the request with minimal headers (matching profile.js pattern)
        return fetch(`api/get-user-progress.php?user_id=${userId}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    let error;
                    try {
                        // Try to parse error as JSON
                        error = JSON.parse(text);
                        throw new Error(error.error || error.message || `HTTP error! status: ${response.status}`);
                    } catch (e) {
                        // If not JSON, use text as error
                        throw new Error(text || `HTTP error! status: ${response.status}`);
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            
            // Check if the response has an error
            if (data.error) {
                throw new Error(data.error);
            }
            
            // The response data is already the progress data
            const progressData = data;
            console.log('Progress data:', progressData);
            
            // Get progress by category
            const tutorials = progressData.progress_by_category?.tutorials || { completed: 0, total: 1, percentage: 0 };
            const quizzes = progressData.progress_by_category?.quizzes || { completed: 0, total: 1, percentage: 0 };
            const challenges = progressData.progress_by_category?.challenges || { completed: 0, total: 1, percentage: 0 };
            const miniGames = progressData.progress_by_category?.miniGames || { completed: 0, total: 2, percentage: 0 };
            
            // Calculate overall progress (weighted average similar to profile.php)
            const overallProgress = Math.min(100, Math.round(progressData.overall_progress || 0));
            const lastWeekProgress = Math.min(100, Math.round(progressData.last_week_progress || 0));
            const lastMonthProgress = Math.min(100, Math.round(progressData.last_month_progress || 0));
            
            console.log(`Overall Progress: ${overallProgress}%`);
            console.log('Progress by category:', { tutorials, quizzes, challenges, miniGames });
            
            // Update the progress display with a similar style to profile.php
            const progressDisplay = document.getElementById('progressContent') || document.createElement('div');
            progressDisplay.id = 'progressContent';
            progressDisplay.className = 'progress-content';
            
            // Calculate circumference for the progress ring
            const radius = 54;
            const circumference = 2 * Math.PI * radius;
            const offset = circumference * (1 - (overallProgress / 100));
            
            progressDisplay.innerHTML = `
                <div class="progress-ring-container">
                    <div class="progress-ring">
                        <svg class="progress-ring__circle" width="120" height="120" viewBox="0 0 120 120">
                            <circle class="progress-ring__circle-bg" cx="60" cy="60" r="${radius}" stroke-width="6" />
                            <circle class="progress-ring__circle-fill" 
                                    cx="60" cy="60" r="${radius}" stroke-width="6"
                                    stroke-dasharray="${circumference}" 
                                    stroke-dashoffset="${offset}" />
                        </svg>
                        <div class="progress-ring__content">
                            <div class="progress-ring__percent">${overallProgress}%</div>
                            <div class="progress-ring__label">Complete</div>
                        </div>
                    </div>
                    
                    <div class="progress-stats">
                        <div class="stat-item">
                            <div class="stat-value">${lastWeekProgress}%</div>
                            <div class="stat-label">Last Week</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${lastMonthProgress}%</div>
                            <div class="stat-label">Last Month</div>
                        </div>
                    </div>
                </div>
                
                <div class="progress-details">
                    <div class="progress-category">
                        <div class="category-info">
                            <span class="category-name">Tutorials</span>
                            <span class="category-progress">${tutorials.completed}/${tutorials.total}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: ${tutorials.percentage}%" 
                                 aria-valuenow="${tutorials.percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="progress-category">
                        <div class="category-info">
                            <span class="category-name">Quizzes</span>
                            <span class="category-progress">${quizzes.completed}/${quizzes.total}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: ${quizzes.percentage}%" 
                                 aria-valuenow="${quizzes.percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="progress-category">
                        <div class="category-info">
                            <span class="category-name">Challenges</span>
                            <span class="category-progress">${challenges.completed}/${challenges.total}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: ${challenges.percentage}%" 
                                 aria-valuenow="${challenges.percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="progress-category">
                        <div class="category-info">
                            <span class="category-name">Mini Games</span>
                            <span class="category-progress">${miniGames.completed}/${miniGames.total}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: ${miniGames.percentage}%" 
                                 aria-valuenow="${miniGames.percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                
                <div class="xp-display mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total XP:</span>
                        <span class="badge bg-primary">${progressData.points || 0} XP</span>
                    </div>
                </div>
                <style>
                    .progress-content {
                        margin: 20px 0;
                        padding: 20px;
                        background: #fff;
                        border-radius: 10px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                    }
                    
                    .progress-ring-container {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 25px;
                    }
                    
                    .progress-ring {
                        position: relative;
                        width: 120px;
                        height: 120px;
                    }
                    
                    .progress-ring__circle {
                        transform: rotate(-90deg);
                    }
                    
                    .progress-ring__circle-bg {
                        fill: none;
                        stroke: #e9ecef;
                    }
                    
                    .progress-ring__circle-fill {
                        fill: none;
                        stroke: #4e73df;
                        stroke-linecap: round;
                        transition: stroke-dashoffset 0.5s ease;
                    }
                    
                    .progress-ring__content {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .progress-ring__percent {
                        font-size: 1.75rem;
                        font-weight: 700;
                        color: #2d3748;
                        line-height: 1;
                    }
                    
                    .progress-ring__label {
                        font-size: 0.75rem;
                        color: #718096;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        margin-top: 4px;
                    }
                    
                    .progress-stats {
                        display: flex;
                        gap: 2rem;
                    }
                    
                    .stat-item {
                        text-align: center;
                    }
                    
                    .stat-value {
                        font-size: 1.25rem;
                        font-weight: 600;
                        color: #2d3748;
                        margin-bottom: 2px;
                    }
                    
                    .stat-label {
                        font-size: 0.75rem;
                        color: #718096;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                    }
                    
                    .progress-details {
                        margin-top: 1.5rem;
                    }
                    
                    .progress-category {
                        margin-bottom: 1rem;
                    }
                    
                    .progress-category:last-child {
                        margin-bottom: 0;
                    }
                    
                    .category-info {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 0.25rem;
                        font-size: 0.875rem;
                    }
                    
                    .category-name {
                        color: #4a5568;
                    }
                    
                    .category-progress {
                        color: #718096;
                        font-weight: 500;
                    }
                    
                    .xp-display {
                        padding: 0.75rem 1rem;
                        background-color: #f8f9fc;
                        border-radius: 0.5rem;
                        border-left: 4px solid #4e73df;
                    }
                    
                    .xp-display .badge {
                        font-size: 0.875rem;
                        padding: 0.35em 0.65em;
                        font-weight: 600;
                    }
                    /* Animation for progress bars */
                    @keyframes progress-bar-stripes {
                        0% { background-position: 1rem 0; }
                        100% { background-position: 0 0; }
                    }
                    
                    /* Responsive adjustments */
                    @media (max-width: 768px) {
                        .progress-ring-container {
                            flex-direction: column;
                            align-items: center;
                            gap: 1.5rem;
                        }
                        
                        .progress-stats {
                            width: 100%;
                            justify-content: space-around;
                        }
                    }
                    
                    /* Print styles */
                    @media print {
                        .progress-content {
                            box-shadow: none;
                            border: 1px solid #dee2e6;
                            page-break-inside: avoid;
                        }
                        
                        .progress-ring__circle-fill {
                            stroke: #4a6cf7 !important;
                        }
                    }
                    font-weight: 600;
                    color: #2e59d9;
                    </style>`;
                
                // Make sure to replace the content in the DOM
                const container = document.getElementById('userProgressContainer');
                if (container) {
                    container.innerHTML = '';
                    container.appendChild(progressDisplay);
                }
                
                // Insert the progress display if it doesn't exist
                if (!document.getElementById('progressDisplay')) {
                    const progressContent = document.getElementById('progressContent') || document.body;
                    progressContent.insertBefore(progressDisplay, progressContent.firstChild);
                }
                
                // Update the stats grid
                const updateElement = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value;
                };
                
                updateElement('completedTutorials', progressData.tutorials || 0);
                updateElement('completedQuizzes', progressData.quizzes || 0);
                updateElement('completedChallenges', progressData.challenges || 0);
                updateElement('completedMiniGames', `${progressData.miniGames || 0}/2`);
                updateElement('totalXP', progressData.xp || 0);
                
                // Show the progress content if it was hidden
                if (progressContent) {
                    progressContent.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading user progress:', error);
                
                // Show error message in progress content area
                const errorContent = `
                    <div class="alert alert-danger">
                        Failed to load user progress: ${error.message}
                    </div>`;

                if (progressContent) {
                    progressContent.innerHTML = errorContent;
                }
            });
    }
});

// Use event delegation for the progress button in the modal
document.addEventListener('click', (event) => {
    const progressBtn = event.target.closest('.view-progress-btn');
    if (!progressBtn) return;

    console.log('Progress button clicked');

    // Get the user ID from the data attribute of the clicked button
    const userId = progressBtn.dataset.userId;
    
    if (!userId) {
        console.error('No user ID found on the clicked button');
        return;
    }
    
    console.log('Loading progress for user ID:', userId);

    // Get the progress content container
    const progressContent = document.getElementById('progressContent');
    
    if (!progressContent) {
        console.error('Progress content element not found');
        return;
    }

    // Show loading state
    progressContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>`;
    
    // Show progress content
    progressContent.style.display = 'block';
    
    // Load the progress for the selected user
    loadUserProgress(userId).catch(error => {
        console.error('Error loading progress:', error);
        progressContent.innerHTML = `
            <div class="alert alert-danger">
                Error loading progress: ${error.message}
            </div>`;
    });
});
