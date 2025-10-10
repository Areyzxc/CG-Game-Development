/**
 * File: tutorial.js
 * Purpose: Handles all tutorial-related UI logic, including popups, progress tracking, topic navigation, filtering, and achievement notifications for CodeGaming.
 * Features:
 *   - Displays interactive tutorials for mini-game, quiz, and challenge modes.
 *   - Tracks user progress and completion via API.
 *   - Manages topic selection, filtering, and search.
 *   - Handles achievement notifications and UI updates.
 *   - Supports mobile sidebar toggling and smooth navigation.
 * Usage:
 *   - Included on tutorial-related pages.
 *   - Requires HTML elements with specific IDs/classes for popups, modals, filters, and navigation.
 *   - Relies on API endpoints: api/tutorial/track-progress.php, api/topics.php.
 * Included Files/Dependencies:
 *   - Bootstrap (for modals)
 *   - HTML structure for tutorial popups, modals, and sidebar.
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */
// Helper function to capitalize the first letter of a string
function capitalizeFirst(string) {
    return string ? string.charAt(0).toUpperCase() + string.slice(1) : '';
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    const tutorialPopup = document.getElementById('tutorialPopup');
    const popupContent = tutorialPopup?.querySelector('.popup-content');
    const progressBar = tutorialPopup?.querySelector('.progress-bar');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const modalOverlay = document.getElementById('tutorialModalOverlay');
    const popupCloseBtn = document.getElementById('popupCloseBtn');
    const topicModal = document.getElementById('topicModal');
    const searchInput = document.getElementById('topicSearch');
    const languageFilter = document.getElementById('languageFilter');
    const difficultyFilter = document.getElementById('difficultyFilter');
    const progressFilter = document.getElementById('progressFilter');
    
    let currentSlide = 0;
    let totalSlides = 0;
    let currentTutorial = null;
    let currentTopicData = null;
    let debounceTimer;

    // Tutorial content (to be replaced with API data)
    const tutorials = {
        'mini-game': [
            {
                title: 'Welcome to Mini-Game Mode',
                content: 'Learn coding concepts through fun and interactive games. Each game focuses on specific programming skills.'
            },
            {
                title: 'How to Play',
                content: 'Use keyboard controls or mouse clicks to interact with the games. Follow on-screen instructions and complete the objectives.'
            },
            {
                title: 'Scoring System',
                content: 'Earn points based on your performance. The faster and more accurate you are, the higher your score!'
            }
        ],
        'quiz': [
            {
                title: 'Quiz Mode Overview',
                content: 'Test your knowledge with multiple-choice questions, true/false statements, and code snippets.'
            },
            {
                title: 'Question Types',
                content: 'You\'ll encounter various question types designed to test different aspects of your programming knowledge.'
            },
            {
                title: 'Time Management',
                content: 'Some quizzes may have time limits. Manage your time wisely and read questions carefully.'
            }
        ],
        'challenge': [
            {
                title: 'Challenge Mode Introduction',
                content: 'Face real-world coding challenges that test your problem-solving abilities.'
            },
            {
                title: 'Submission Process',
                content: 'Write your code solution, test it thoroughly, and submit for evaluation. Our system will check your code against test cases.'
            },
            {
                title: 'Getting Help',
                content: 'Stuck on a challenge? Use hints or review related tutorials to improve your approach.'
            }
        ]
    };

    // Handle tutorial trigger clicks
    document.querySelectorAll('.tutorial-trigger').forEach(button => {
        button.addEventListener('click', function() {
            const mode = this.dataset.mode;
            openTutorial(mode);
        });
    });

    // Open tutorial
    function openTutorial(mode) {
        currentTutorial = mode;
        currentSlide = 0;
        totalSlides = tutorials[mode].length;
        updateTutorialContent();
        tutorialPopup.classList.add('active');
        modalOverlay.classList.add('active');
        updateProgress();
        
        // Track tutorial start
        trackTutorialProgress(mode, 'started');
    }

    // Update tutorial content
    function updateTutorialContent() {
        const tutorial = tutorials[currentTutorial][currentSlide];
        popupContent.innerHTML = `
            <h3>${tutorial.title}</h3>
            <p>${tutorial.content}</p>
        `;
        
        // Update button states
        prevBtn.disabled = currentSlide === 0;
        nextBtn.textContent = currentSlide === totalSlides - 1 ? 'Complete' : 'Next';
    }

    // Update progress bar
    function updateProgress() {
        const progress = ((currentSlide + 1) / totalSlides) * 100;
        progressBar.style.width = `${progress}%`;
    }

    // Navigation button handlers
    prevBtn.addEventListener('click', () => {
        if (currentSlide > 0) {
            currentSlide--;
            updateTutorialContent();
            updateProgress();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateTutorialContent();
            updateProgress();
        } else {
            completeTutorial();
        }
    });

    // Complete tutorial
    function completeTutorial() {
        trackTutorialProgress(currentTutorial, 'completed');
        closeTutorialPopup();
        // Show completion message
        const gameCard = document.querySelector(`#${currentTutorial}`);
        if (gameCard && !gameCard.querySelector('.progress-badge')) {
            const badge = document.createElement('span');
            badge.className = 'progress-badge';
            badge.innerHTML = '<i class="bx bx-check"></i> Completed';
            gameCard.querySelector('.nav-button').before(badge);
        }
    }

    // Track tutorial progress
    async function trackTutorialProgress(mode, status) {
        try {
            const response = await fetch('api/tutorial/track-progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    mode: mode,
                    status: status
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to track progress');
            }
        } catch (error) {
            console.error('Error tracking tutorial progress:', error);
        }
    }

    // Close modal logic
    function closeTutorialPopup() {
        tutorialPopup.classList.remove('active');
        modalOverlay.classList.remove('active');
    }
    if (popupCloseBtn) {
        popupCloseBtn.addEventListener('click', closeTutorialPopup);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeTutorialPopup);
    }

    // Mobile sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.tutorial-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar on link click (mobile)
    document.querySelectorAll('.tutorial-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('active');
            }
        });
    });

    // Initialize scrollspy
    document.querySelectorAll('.tutorial-nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Programming Language Selection
    document.querySelectorAll('.tutorial-nav-link[href^="#"]').forEach(link => {
        link.addEventListener('click', async (e) => {
            e.preventDefault();
            const languageId = link.getAttribute('href').substring(1);
            
            // Remove active class from all language cards
            document.querySelectorAll('.language-card').forEach(card => {
                card.classList.remove('active');
            });

            // Add active class to selected language card
            const targetCard = document.getElementById(languageId);
            if (targetCard) {
                targetCard.classList.add('active');
                targetCard.scrollIntoView({ behavior: 'smooth' });

                // Update topics for the selected language
                try {
                    const response = await fetch(`api/topics.php?language=${languageId}`);
                    if (!response.ok) throw new Error('Failed to fetch topics');
                    const topics = await response.json();
                    
                    // Update topics display
                    const topicList = targetCard.querySelector('.topic-list');
                    if (topicList) {
                        topicList.innerHTML = topics.map((topic, index) => `
                            <div class="topic-item d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="topic-number me-2">${index + 1}.</span>
                                    <span>${topic.title}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-${getDifficultyBadgeClass(topic.difficulty)} me-2">
                                        ${capitalizeFirst(topic.difficulty)}
                                    </span>
                                    ${topic.progress ? `
                                        <span class="badge bg-${getProgressBadgeClass(topic.progress.status)}">
                                            ${capitalizeFirst(topic.progress.status)}
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('') + generateComingSoonTopics(topics.length);
                    }
                } catch (error) {
                    console.error('Error fetching topics:', error);
                    // Show error message in the card
                    if (targetCard) {
                        targetCard.querySelector('.topic-list').innerHTML = `
                            <div class="alert alert-danger">
                                Failed to load topics. Please try again later.
                            </div>
                        `;
                    }
                }
            }

            // Handle mobile sidebar
            if (window.innerWidth < 992) {
                document.querySelector('.tutorial-sidebar')?.classList.remove('active');
            }
        });
    });

    // Load topics for a language
    async function loadTopics(languageId, shouldHighlight = true) {
        const topicList = document.querySelector(`.topic-list[data-language="${languageId}"]`);
        if (!topicList) return;

        try {
            const response = await fetch(`api/topics.php?language=${languageId}`);
            if (!response.ok) throw new Error('Failed to fetch topics');
            const data = await response.json();

            if (data.success && data.data) {
                renderTopics(topicList, data.data);
                if (shouldHighlight) {
                    // Highlight the selected language card
                    document.querySelectorAll('.language-card').forEach(card => {
                        card.classList.remove('active');
                    });
                    document.getElementById(languageId)?.classList.add('active');
                }
            }
        } catch (error) {
            console.error('Error loading topics:', error);
            topicList.innerHTML = `
                <div class="alert alert-danger">
                    Failed to load topics. Please try again later.
                </div>
            `;
        }
    }

    // Render topics in the topic list
    function renderTopics(container, topics) {
        if (!topics.length) {
            container.innerHTML = generateComingSoonTopics(0);
            return;
        }

        const topicsHtml = topics.map((topic, index) => `
            <div class="topic-item" data-topic-id="${topic.id}">
                <div class="topic-header">
                    <div class="topic-title">
                        <span class="topic-number">${index + 1}.</span>
                        <span>${topic.title}</span>
                    </div>
                    <div class="topic-badges">
                        <span class="badge bg-${getDifficultyBadgeClass(topic.difficulty)}">
                            ${capitalizeFirst(topic.difficulty)}
                        </span>
                        <span class="status-badge ${topic.progress?.status || 'pending'}">
                            ${capitalizeFirst(topic.progress?.status || 'Start')}
                        </span>
                    </div>
                </div>
                <p class="topic-description">${topic.description || ''}</p>
                <div class="topic-actions">
                    <div class="progress-indicator">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${topic.progress?.progress || 0}%"></div>
                        </div>
                        <span>${topic.progress?.progress || 0}%</span>
                    </div>
                    <button class="btn btn-primary btn-sm view-topic">View Topic</button>
                </div>
            </div>
        `).join('') + generateComingSoonTopics(topics.length);

        container.innerHTML = topicsHtml;

        // Add click handlers for topic items
        container.querySelectorAll('.topic-item').forEach(item => {
            if (!item.classList.contains('coming-soon')) {
                item.querySelector('.view-topic')?.addEventListener('click', () => {
                    const topicId = item.dataset.topicId;
                    const topic = topics.find(t => t.id === topicId);
                    if (topic) openTopicModal(topic);
                });
            }
        });
    }

    // Open topic detail modal
    function openTopicModal(topic) {
        currentTopicData = topic;
        const modal = document.getElementById('topicModal');
        modal.querySelector('.modal-title').textContent = topic.title;
        modal.querySelector('.modal-content').innerHTML = `
            <div class="topic-detail">
                <p class="topic-description">${topic.description}</p>
                <div class="topic-metadata">
                    <span class="badge bg-${getDifficultyBadgeClass(topic.difficulty)}">
                        ${capitalizeFirst(topic.difficulty)}
                    </span>
                    <span class="status-badge ${topic.progress?.status || 'pending'}">
                        ${capitalizeFirst(topic.progress?.status || 'Not Started')}
                    </span>
                </div>
                ${topic.content ? `<div class="topic-content">${topic.content}</div>` : ''}
            </div>
        `;

        // Update button states based on current progress
        updateProgressButtons(topic.progress?.status || 'pending');

        modal.classList.add('active');
        modalOverlay.classList.add('active');
    }

    // Update progress buttons in the modal
    function updateProgressButtons(currentStatus) {
        const progressBtn = topicModal.querySelector('[data-action="mark-progress"]');
        const completeBtn = topicModal.querySelector('[data-action="mark-complete"]');

        switch (currentStatus) {
            case 'completed':
                progressBtn.disabled = true;
                completeBtn.disabled = true;
                break;
            case 'in_progress':
                progressBtn.disabled = true;
                completeBtn.disabled = false;
                break;
            default:
                progressBtn.disabled = false;
                completeBtn.disabled = false;
        }
    }

    // Handle progress button clicks
    async function updateTopicProgress(status) {
        if (!currentTopicData) return;

        try {
            const response = await fetch('api/topics.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    topic_id: currentTopicData.id,
                    status: status
                })
            });

            const data = await response.json();
            if (data.success) {
                // Update UI
                updateProgressButtons(status);
                // Reload topics to refresh progress
                loadTopics(currentTopicData.language_id, false);
                
                // Show achievement notification if any
                if (data.achievements?.length) {
                    showAchievementNotification(data.achievements);
                }
            }
        } catch (error) {
            console.error('Error updating progress:', error);
            alert('Failed to update progress. Please try again.');
        }
    }

    // Show achievement notification
    function showAchievementNotification(achievements) {
        achievements.forEach(achievement => {
            const notification = document.createElement('div');
            notification.className = 'achievement-notification';
            notification.innerHTML = `
                <div class="achievement-icon">🏆</div>
                <div class="achievement-content">
                    <h4>${achievement.title}</h4>
                    <p>${achievement.description}</p>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Remove notification after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        });
    }

    // Search and filter functionality
    // Note: This function is currently not used but kept for future implementation
    // of advanced filtering functionality.

    // Initialize event listeners
    function initializeEventListeners() {
        // Modal overlay click handler
        if (modalOverlay) {
            modalOverlay.addEventListener('click', () => {
                if (tutorialPopup) tutorialPopup.classList.remove('active');
                if (topicModal) topicModal.classList.remove('active');
                modalOverlay.classList.remove('active');
            });
        }

        // Progress button click handler
        const markProgressBtn = document.querySelector('[data-action="mark-progress"]');
        if (markProgressBtn) {
            markProgressBtn.addEventListener('click', () => {
                updateTopicProgress('currently_reading');
            });
        }
        
        // Add event listeners for filter inputs if they exist
        const filterInputs = [searchInput, languageFilter, difficultyFilter, progressFilter]
            .filter(Boolean); // Remove null/undefined
            
        filterInputs.forEach(input => {
            input.addEventListener('input', () => {
                // Filter functionality can be implemented here when needed
                console.log('Filter changed:', input.id || input.name);
            });
        });
    }

    // Initialize the application
    function initialize() {
        initializeEventListeners();
        
        // Load initial topics for each language
        document.querySelectorAll('.language-card').forEach(card => {
            loadTopics(card.id);
        });
    }

    // Helper Functions
    function getDifficultyBadgeClass(difficulty) {
        return {
            'beginner': 'success',
            'intermediate': 'warning',
            'expert': 'danger'
        }[difficulty] || 'secondary';
    }

    function getProgressBadgeClass(status) {
        return {
            'completed': 'success',
            'in_progress': 'warning',
            'pending': 'secondary'
        }[status] || 'secondary';
    }

    // capitalizeFirst function moved to the top of the file for better scope

    function generateComingSoonTopics(currentCount) {
        const comingSoonCount = 2;
        let html = '';
        
        for (let i = currentCount + 1; i <= currentCount + comingSoonCount; i++) {
            html += `
                <div class="topic-item coming-soon">
                    <div class="topic-header">
                        <div class="topic-title">
                            <span class="topic-number">${i}.</span>
                            <span class="text-muted">Coming Soon</span>
                        </div>
                        <span class="badge bg-secondary">Future Topic</span>
                    </div>
                </div>
            `;
        }
        
        return html;
    }

    // Initialize the application
    initialize();
}); // End of first DOMContentLoaded

// Second DOMContentLoaded handler for additional functionality
document.addEventListener('DOMContentLoaded', function() {
    // --- Variables ---
    const languageLinks = document.querySelectorAll('.language-select');
    const languageTopicContainer = document.getElementById('language-topic-container');
    const topicDropdown = document.getElementById('topicDropdown');
    const topicDetails = document.getElementById('topicDetails');
    const currentlyReadingBtn = document.getElementById('currentlyReadingBtn');
    const doneReadingBtn = document.getElementById('doneReadingBtn');
    const selectPrompt = document.getElementById('selectPrompt');
    const selectedLanguageTitle = document.getElementById('selected-language-title');
    const topicSearch = document.getElementById('topicSearch');

    let currentLanguageId = null;
    let currentTopic = null;
    let topics = [];

    // --- Helper: Get badge class ---
    function getDifficultyBadgeClass(difficulty) {
        return {
            'beginner': 'success',
            'intermediate': 'warning',
            'expert': 'danger'
        }[difficulty] || 'secondary';
    }

    // --- Language Selection ---
    languageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const langId = this.getAttribute('data-language-id');
            currentLanguageId = langId;
            topics = (window.topicsConfig && window.topicsConfig[langId]) ? window.topicsConfig[langId] : [];
            // Update UI
            updateLanguageUI(langId);
        });
    });

    function updateLanguageUI(langId) {
        // Update title
        const lang = (window.languages || []).find(l => l.id === langId);
        selectedLanguageTitle.innerHTML = `<i class='bx bx-code-alt'></i> ${lang ? lang.name : ''}`;
        // Show container
        languageTopicContainer.classList.remove('d-none');
        selectPrompt.classList.add('d-none');
        // Populate topic dropdown
        renderTopicDropdown(topics);
        // Reset details
        topicDetails.innerHTML = '';
        currentlyReadingBtn.disabled = true;
        doneReadingBtn.disabled = true;
    }

    // --- Topic Dropdown Rendering ---
    function renderTopicDropdown(topicsList, searchTerm = '') {
        topicDropdown.innerHTML = '';
        topicsList.forEach(topic => {
            let title = topic.title;
            if (searchTerm) {
                // Highlight search term in title
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                title = title.replace(regex, '<mark>$1</mark>');
            }
            const option = document.createElement('option');
            option.value = topic.id;
            option.innerHTML = `${title} <span class="badge bg-${getDifficultyBadgeClass(topic.difficulty)} ms-2">${capitalizeFirst(topic.difficulty)}</span>`;
            topicDropdown.appendChild(option);
        });
        // Select first topic by default
        if (topicsList.length > 0) {
            topicDropdown.selectedIndex = 0;
            showTopicDetails(topicsList[0]);
        } else {
            topicDetails.innerHTML = '<div class="alert alert-info">No keywords found.</div>';
            currentlyReadingBtn.disabled = true;
            doneReadingBtn.disabled = true;
        }
    }

    // --- Topic Selection ---
    topicDropdown.addEventListener('change', function() {
        const topicId = this.value;
        const topic = topics.find(t => t.id === topicId);
        if (topic) {
            showTopicDetails(topic);
        }
    });

    function showTopicDetails(topic) {
        currentTopic = topic;
        topicDetails.innerHTML = `
            <h4>${highlightSearch(topic.title)}</h4>
            <span class="badge bg-${getDifficultyBadgeClass(topic.difficulty)} mb-2">${capitalizeFirst(topic.difficulty)}</span>
            <p>${highlightSearch(topic.description)}</p>
            ${topic.content ? topic.content : ''}
        `;
        currentlyReadingBtn.disabled = false;
        doneReadingBtn.disabled = false;
    }

    // --- Topic Search ---
    topicSearch.addEventListener('input', function() {
        const searchTerm = this.value.trim().toLowerCase();
        if (!searchTerm) {
            renderTopicDropdown(topics);
            return;
        }
        // Filter topics (now includes content)
        const filtered = topics.filter(topic =>
            topic.title.toLowerCase().includes(searchTerm) ||
            topic.description.toLowerCase().includes(searchTerm) ||
            (topic.content && topic.content.toLowerCase().includes(searchTerm))
        );
        renderTopicDropdown(filtered, searchTerm);
    });

    // --- Highlight search term in details ---
    function highlightSearch(text) {
        const searchTerm = topicSearch.value.trim();
        if (!searchTerm) return text;
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // --- Reading Buttons ---
    currentlyReadingBtn.addEventListener('click', function() {
        if (!currentTopic) return;
        // Mark as currently reading
        trackTopicProgress('currently_reading').then(() => {
            // Update UI to reflect progress
            const topicItem = document.querySelector(`.topic-item[data-topic-id="${currentTopic.id}"]`);
            if (topicItem) {
                const statusBadge = topicItem.querySelector('.topic-status');
                if (statusBadge) {
                    statusBadge.textContent = 'In Progress';
                    statusBadge.className = 'badge bg-warning topic-status';
                }
            }
            this.classList.add('active');
            doneReadingBtn.classList.remove('active');
        }).catch(error => {
            console.error('Error tracking progress:', error);
            alert('Failed to save your progress. Please try again.');
        });
    });

    doneReadingBtn.addEventListener('click', function() {
        if (!currentTopic) return;
        // Mark as completed
        trackTopicProgress('done_reading').then(() => {
            // Show congrats modal after successful save
            const congratsModal = new bootstrap.Modal(document.getElementById('congratsModal'));
            congratsModal.show();
            
            // Update UI to reflect completion
            const topicItem = document.querySelector(`.topic-item[data-topic-id="${currentTopic.id}"]`);
            if (topicItem) {
                topicItem.classList.add('completed');
                const statusBadge = topicItem.querySelector('.topic-status');
                if (statusBadge) {
                    statusBadge.textContent = 'Completed';
                    statusBadge.className = 'badge bg-success topic-status';
                }
            }
            this.classList.add('active');
            currentlyReadingBtn.classList.remove('active');
        }).catch(error => {
            console.error('Error tracking progress:', error);
            alert('Failed to save your progress. Please try again.');
        });
    });

    // --- API Call for Progress Tracking ---
    function trackTopicProgress(status) {
        if (!currentTopic) return Promise.resolve();
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        return new Promise((resolve, reject) => {
            fetch('/CodeGaming/api/tutorial/track-progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    topic_id: currentTopic.id,
                    status: status
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to track progress');
                }
                resolve(data);
            })
            .catch(error => {
                console.error('Error tracking progress:', error);
                reject(error);
            });
        });
    }

    // Load topic progress for the current user
    function loadTopicProgress() {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const progressUrl = '/CodeGaming/api/tutorial/track-progress.php';
        
        console.log('Fetching progress from:', progressUrl);
        
        return fetch(progressUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-Token': csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            const responseText = await response.text();
            
            // Check if the response is HTML (which indicates an error)
            if (responseText.trim().startsWith('<!DOCTYPE') || 
                responseText.includes('<html') || 
                responseText.includes('<br />')) {
                console.error('Received HTML response instead of JSON:', responseText.substring(0, 200) + '...');
                throw new Error('Server returned an HTML error page');
            }
            
            // Try to parse as JSON
            try {
                return JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                console.error('Response text:', responseText);
                throw new Error('Invalid JSON response from server');
            }
        })
        .then(data => {
            if (!data) {
                console.warn('No data received from API');
                return;
            }
            
            if (data.success && data.data) {
                console.log('Received progress data:', data.data);
                // Update UI for each topic with progress
                Object.entries(data.data).forEach(([topicId, progress]) => {
                    if (!topicId) return;
                    
                    const topicItem = document.querySelector(`.topic-item[data-topic-id="${topicId}"]`);
                    if (!topicItem) {
                        console.warn(`Topic item not found for ID: ${topicId}`);
                        return;
                    }
                    
                    const status = (typeof progress === 'object' && progress !== null) ? progress.status : progress;
                    const statusBadge = topicItem.querySelector('.topic-status');
                    
                    if (statusBadge) {
                        // Map database status to display text
                        let statusText = 'Not Started';
                        let statusClass = 'bg-secondary';
                        
                        switch(status) {
                            case 'done_reading':
                                statusText = 'Completed';
                                statusClass = 'bg-success';
                                topicItem.classList.add('completed');
                                break;
                            case 'currently_reading':
                                statusText = 'In Progress';
                                statusClass = 'bg-warning';
                                break;
                            case 'pending':
                            default:
                                statusText = 'Not Started';
                                statusClass = 'bg-secondary';
                        }
                        
                        statusBadge.textContent = statusText;
                        statusBadge.className = `badge ${statusClass} topic-status`;
                    }
                });
            } else {
                console.warn('API request was not successful:', data.message || 'Unknown error');
                // Show a user-friendly error message
                showNotification('Failed to load progress data. ' + (data.message || 'Please try again later.'), 'error');
            }
        })
        .catch(error => {
            console.error('Error in loadTopicProgress:', error);
            // Show a user-friendly error message
            showNotification('Failed to load progress. Please check your connection and refresh the page.', 'error');
        });
        
    // Helper function to show user notifications
    function showNotification(message, type = 'info') {
        // Check if notification system exists, if not, use alert as fallback
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: type,
                title: message
            });
        } else {
            // Fallback to browser alert
            alert(`${type.toUpperCase()}: ${message}`);
        }
    }
    }

    // Load topic progress when the page loads
    loadTopicProgress();
    
    // --- Sidebar mobile toggle ---
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.tutorial-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
});