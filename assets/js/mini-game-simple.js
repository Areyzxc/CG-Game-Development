/**
 * File: mini-game-simple.js
 * Purpose: Simple mini-game logic following challenges.js pattern
 * Features:
 *   - Direct button event handling (no modals)
 *   - Screen-based transitions
 *   - Simple state management
 *   - AJAX-based challenge loading
 * 
 * Author: CodeGaming Team
 */
// Test if script is loading
console.log('🔥 mini-game-simple.js loaded successfully!');

// Mini-Game state management (similar to challenges.js)
const miniGameState = {
    currentMode: null,
    isActive: false,
    currentLanguage: 'javascript',
    currentDifficulty: 'beginner',
    currentChallenge: null,
    score: 0,
    streak: 0,
    startTime: null,
    userId: window.CG_USER_ID || null,
    // Session tracking
    correctAnswers: 0,
    incorrectAnswers: 0,
    totalQuestions: 0,
    sessionStartTime: null
};

// Initialize mini-game (following challenges.js pattern)
function initMiniGame() {
    console.log('🎮 Initializing Mini-Game');
    
    // Detect user/guest (same as challenges.js)
    miniGameState.userId = (window.CG_USER_ID !== null && window.CG_USER_ID !== 'null' && window.CG_USER_ID !== undefined) ? window.CG_USER_ID : null;
    miniGameState.username = window.CG_USERNAME || null;
    miniGameState.isGuest = (miniGameState.userId === null || miniGameState.userId === undefined);
    
    console.log('User detection:', {
        userId: miniGameState.userId,
        username: miniGameState.username,
        isGuest: miniGameState.isGuest
    });
    
    // Set initial language and difficulty from selects
    const languageSelect = document.getElementById('languageSelect');
    const difficultySelect = document.getElementById('difficultySelect');
    
    if (languageSelect) {
        miniGameState.currentLanguage = languageSelect.value;
        languageSelect.addEventListener('change', function() {
            miniGameState.currentLanguage = this.value;
            updateCurrentLanguageDisplay();
        });
    }
    
    if (difficultySelect) {
        miniGameState.currentDifficulty = difficultySelect.value;
        difficultySelect.addEventListener('change', function() {
            miniGameState.currentDifficulty = this.value;
        });
    }
    
    updateCurrentLanguageDisplay();
    
    // Load initial leaderboard
    loadLeaderboard();
    
    // Test leaderboard API directly
    testLeaderboardAPI();
}

// Attach event listeners (following challenges.js pattern)
function attachMiniGameEventListeners() {
    console.log('🔗 Attaching Mini-Game Event Listeners');
    
    // Debug: Check if buttons exist
    const startModeButtons = document.querySelectorAll('.btn-start-mode');
    console.log('Found start mode buttons:', startModeButtons.length);
    startModeButtons.forEach((btn, index) => {
        console.log(`Button ${index}:`, btn, 'data-mode:', btn.getAttribute('data-mode'));
    });
    
    // Start mode buttons (equivalent to .btn-start in challenges.js)
    document.querySelectorAll('.btn-start-mode').forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.getAttribute('data-mode');
            console.log('🚀 Start mode button clicked:', mode);
            startMiniGame(mode);
        });
    });
    
    // Back to menu button - show confirmation modal
    const backBtn = document.querySelector('.btn-back-menu');
    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showExitConfirmation();
        });
    }
    
    // Confirm exit button in modal
    const confirmExitBtn = document.getElementById('confirmExitBtn');
    if (confirmExitBtn) {
        confirmExitBtn.addEventListener('click', function() {
            // Hide modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('exitConfirmModal'));
            if (modal) modal.hide();
            
            // Then go back to menu
            backToMenu();
        });
    }
    
    // Game action buttons
    const checkGuessBtn = document.getElementById('checkGuessBtn');
    if (checkGuessBtn) {
        checkGuessBtn.addEventListener('click', submitGuessAnswer);
    }
    
    const startTypingBtn = document.getElementById('startTypingBtn');
    if (startTypingBtn) {
        startTypingBtn.addEventListener('click', startTypingChallenge);
    }
    
    const submitTypingBtn = document.getElementById('submitTypingBtn');
    if (submitTypingBtn) {
        submitTypingBtn.addEventListener('click', submitTypingChallenge);
    }
    
    const typingInput = document.getElementById('typingInput');
    if (typingInput) {
        typingInput.addEventListener('input', handleTypingInput);
    }
    
    // Reload leaderboard button
    const reloadBtn = document.getElementById('reloadLeaderboardBtn');
    if (reloadBtn) {
        reloadBtn.addEventListener('click', function() {
            console.log('🔄 Reloading leaderboard...');
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Get current active filters
            const currentScope = document.querySelector('.time-scope-selector.active')?.getAttribute('data-scope') || 'alltime';
            const currentGameType = document.querySelector('.game-mode-selector.active')?.getAttribute('data-game-type') || null;
            
            console.log('🔄 Reloading with filters:', { scope: currentScope, gameType: currentGameType });
            
            loadLeaderboard(currentScope, currentGameType, 1).finally(() => {
                this.innerHTML = '<i class="fas fa-sync-alt"></i>';
            });
        });
    }
    
    // Game mode selector buttons
    document.querySelectorAll('.game-mode-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.game-mode-selector').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Load leaderboard for selected game type
            const gameType = this.getAttribute('data-game-type');
            const currentScope = document.querySelector('.time-scope-selector.active')?.getAttribute('data-scope') || 'alltime';
            console.log('🎮 Switching to game type:', gameType);
            loadLeaderboard(currentScope, gameType, 1);
        });
    });
    
    // Time scope selector buttons
    document.querySelectorAll('.time-scope-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.time-scope-selector').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Load leaderboard for selected scope
            const scope = this.getAttribute('data-scope');
            const currentGameType = document.querySelector('.game-mode-selector.active')?.getAttribute('data-game-type') || null;
            console.log('📅 Switching to scope:', scope);
            loadLeaderboard(scope, currentGameType, 1);
        });
    });
    
    console.log('✅ Event listeners attached');
}

// Start mini-game (equivalent to startChallenge in challenges.js)
async function startMiniGame(mode) {
    console.log('🎯 Starting mini-game with mode:', mode);
    
    if (!mode) {
        showNotification('Please select a game mode!', 'warning');
        return;
    }
    
    // Reset game state
    miniGameState.currentMode = mode;
    miniGameState.isActive = true;
    miniGameState.score = 0;
    miniGameState.streak = 0;
    miniGameState.startTime = Date.now();
    miniGameState.sessionStartTime = Date.now();
    miniGameState.correctAnswers = 0;
    miniGameState.incorrectAnswers = 0;
    miniGameState.totalQuestions = 0;
    
    // Hide welcome, show progress (same pattern as challenges.js)
    document.getElementById('mini-game-welcome').classList.remove('active');
    document.getElementById('mini-game-progress').classList.add('active');
    
    // Update game title and stats
    const gameTitle = mode === 'guess' ? 'Guess the Output' : 'Fast Code Typing';
    document.getElementById('current-game-title').textContent = gameTitle;
    updateGameStats();
    
    // Hide all game containers first
    document.querySelectorAll('.game-container').forEach(container => {
        container.classList.add('d-none');
    });
    
    // Reset typing challenge UI if switching to typing mode
    if (mode === 'typing') {
        resetTypingChallenge();
    }
    
    // Show appropriate game container
    const gameContainer = document.getElementById(`${mode}-content`);
    if (gameContainer) {
        gameContainer.classList.remove('d-none');
    }
    
    // Load first challenge
    await loadNewChallenge();
    
    showNotification(`${gameTitle} started! Good luck!`, 'success');
}

// Load new challenge (AJAX call)
async function loadNewChallenge() {
    console.log('📥 Loading new challenge...');
    
    if (!miniGameState.isActive) {
        console.log('Game not active, skipping challenge load');
        return;
    }
    
    try {
        const response = await fetch('api/mini-game/get-challenge.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                game_type: miniGameState.currentMode,
                language: miniGameState.currentLanguage,
                difficulty: miniGameState.currentDifficulty
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Challenge loaded:', data);
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to load challenge');
        }
        
        miniGameState.currentChallenge = data.challenge;
        displayChallenge(data.challenge);
        
    } catch (error) {
        console.error('Error loading challenge:', error);
        console.error('Challenge load details:', {
            mode: miniGameState.currentMode,
            language: miniGameState.currentLanguage,
            difficulty: miniGameState.currentDifficulty,
            isActive: miniGameState.isActive
        });
        showNotification('Failed to load challenge. Please try again.', 'error');
    }
}

// Display challenge based on game mode
function displayChallenge(challenge) {
    console.log('🎨 Displaying challenge:', challenge);
    
    if (miniGameState.currentMode === 'guess') {
        displayGuessChallenge(challenge);
    } else if (miniGameState.currentMode === 'typing') {
        displayTypingChallenge(challenge);
    }
}

// Display guess challenge
function displayGuessChallenge(challenge) {
    const codeDisplay = document.getElementById('guessCodeDisplay');
    const answerInput = document.getElementById('guessAnswer');
    const feedback = document.getElementById('guessFeedback');
    
    if (!codeDisplay || !answerInput) {
        console.error('Guess game elements not found');
        return;
    }
    
    // Clear previous state
    answerInput.value = '';
    if (feedback) feedback.style.display = 'none';
    
    // Display code
    codeDisplay.textContent = challenge.code || challenge.content;
    
    // Apply syntax highlighting if available
    if (typeof hljs !== 'undefined') {
        hljs.highlightElement(codeDisplay);
    }
    
    // Focus on input
    answerInput.focus();
}

// Display typing challenge
function displayTypingChallenge(challenge) {
    const codeDisplay = document.getElementById('typingCodeDisplay');
    const typingInput = document.getElementById('typingInput');
    const startBtn = document.getElementById('startTypingBtn');
    
    if (!codeDisplay || !typingInput || !startBtn) {
        console.error('Typing game elements not found');
        return;
    }
    
    // Clear previous state
    typingInput.value = '';
    typingInput.disabled = true;
    
    // Display code
    codeDisplay.textContent = challenge.code || challenge.content;
    
    // Apply syntax highlighting if available
    if (typeof hljs !== 'undefined') {
        hljs.highlightElement(codeDisplay);
    }
    
    // Reset start button
    startBtn.textContent = 'Start Challenge';
    startBtn.disabled = false;
}

// Submit guess answer
async function submitGuessAnswer() {
    console.log('📝 Submitting guess answer...');
    
    const answerInput = document.getElementById('guessAnswer');
    const feedback = document.getElementById('guessFeedback');
    
    if (!answerInput || !miniGameState.currentChallenge) {
        console.error('Cannot submit answer: missing elements or challenge');
        return;
    }
    
    const userAnswer = answerInput.value.trim();
    if (!userAnswer) {
        showNotification('Please enter your answer!', 'warning');
        return;
    }
    
    const isCorrect = userAnswer.toLowerCase() === miniGameState.currentChallenge.answer.toLowerCase();
    
    if (isCorrect) {
        miniGameState.score += 100;
        miniGameState.streak += 1;
        miniGameState.correctAnswers += 1;
        showFeedback(feedback, true, 'Correct! Well done! (+100 points)', {
            explanation: miniGameState.currentChallenge.explanation
        });
    } else {
        miniGameState.streak = 0;
        miniGameState.incorrectAnswers += 1;
        showFeedback(feedback, false, 'Incorrect answer.', {
            correctAnswer: miniGameState.currentChallenge.answer,
            explanation: miniGameState.currentChallenge.explanation
        });
    }
    
    // Track total questions answered
    miniGameState.totalQuestions += 1;
    
    // Update displays
    updateGameStats();
    
    // Load new challenge after delay
    setTimeout(() => {
        loadNewChallenge();
        answerInput.value = '';
        if (feedback) feedback.style.display = 'none';
    }, 3000);
}

// Reset typing challenge
function resetTypingChallenge() {
    console.log('🔄 Resetting typing challenge...');
    
    const typingInput = document.getElementById('typingInput');
    const startBtn = document.getElementById('startTypingBtn');
    const submitBtn = document.getElementById('submitTypingBtn');
    
    if (miniGameState.timer) {
        clearInterval(miniGameState.timer);
        miniGameState.timer = null;
    }
    
    if (typingInput) {
        typingInput.disabled = true;
        typingInput.value = '';
    }
    
    if (startBtn) {
        startBtn.textContent = 'Start Challenge';
        startBtn.disabled = false;
    }
    
    if (submitBtn) {
        submitBtn.style.display = 'none';
    }
    
    // Reset displays
    const timerEl = document.getElementById('typingTimer');
    const wpmEl = document.getElementById('typingWPM');
    const accuracyEl = document.getElementById('typingAccuracy');
    
    if (timerEl) timerEl.textContent = '0';
    if (wpmEl) wpmEl.textContent = '0';
    if (accuracyEl) accuracyEl.textContent = '100';
}

// Submit typing challenge
async function submitTypingChallenge() {
    console.log('📝 Submitting typing challenge...');
    
    if (!miniGameState.timer || !miniGameState.currentChallenge) {
        showNotification('No active typing challenge to submit!', 'warning');
        return;
    }
    
    const typingInput = document.getElementById('typingInput');
    if (!typingInput) return;
    
    const elapsedTime = (Date.now() - miniGameState.startTime) / 1000;
    const input = typingInput.value;
    const target = miniGameState.currentChallenge.code || miniGameState.currentChallenge.content;
    
    // Calculate metrics
    const words = input.trim().split(/\s+/).length;
    const wpm = elapsedTime > 0 ? Math.round((words / elapsedTime) * 60) : 0;
    const accuracy = calculateAccuracy(input, target);
    
    // Calculate score based on WPM, accuracy, and time
    const timeBonus = Math.max(0, 60 - elapsedTime); // Bonus for completing quickly
    const accuracyMultiplier = accuracy / 100;
    const score = Math.round((wpm * accuracyMultiplier) + timeBonus);
    
    // Add to session score
    miniGameState.score += score;
    miniGameState.correctAnswers += 1; // Count as completed challenge
    miniGameState.totalQuestions += 1;
    
    // Stop the timer
    clearInterval(miniGameState.timer);
    miniGameState.timer = null;
    typingInput.disabled = true;
    
    // Show feedback
    showNotification(`Typing completed! WPM: ${wpm}, Accuracy: ${accuracy}%, Score: +${score}`, 'success');
    
    // Update displays
    updateGameStats();
    
    // Reset after delay
    setTimeout(() => {
        resetTypingChallenge();
        loadNewChallenge();
    }, 3000);
}

// Start typing challenge
function startTypingChallenge() {
    console.log('⌨️ Starting typing challenge...');
    
    const typingInput = document.getElementById('typingInput');
    const startBtn = document.getElementById('startTypingBtn');
    const submitBtn = document.getElementById('submitTypingBtn');
    
    if (!typingInput || !startBtn) {
        console.error('Typing elements not found');
        return;
    }
    
    if (miniGameState.timer) {
        // Reset if already running
        resetTypingChallenge();
        return;
    }
    
    // Enable input and start timer
    typingInput.disabled = false;
    typingInput.focus();
    startBtn.textContent = 'Reset';
    
    // Show submit button
    if (submitBtn) {
        submitBtn.style.display = 'inline-block';
    }
    
    miniGameState.startTime = Date.now();
    miniGameState.timer = setInterval(() => {
        updateTypingProgress();
    }, 100);
}

// Handle typing input
function handleTypingInput() {
    if (!miniGameState.timer || !miniGameState.currentChallenge) return;
    
    const typingInput = document.getElementById('typingInput');
    if (!typingInput) return;
    
    const input = typingInput.value;
    const target = miniGameState.currentChallenge.code || miniGameState.currentChallenge.content;
    
    // Check if completed
    if (input === target) {
        completeTypingChallenge();
    }
}

// Update typing progress
function updateTypingProgress() {
    if (!miniGameState.startTime || !miniGameState.currentChallenge) return;
    
    const typingInput = document.getElementById('typingInput');
    if (!typingInput) return;
    
    const elapsedTime = (Date.now() - miniGameState.startTime) / 1000;
    const input = typingInput.value;
    const target = miniGameState.currentChallenge.code || miniGameState.currentChallenge.content;
    
    if (!target) return;
    
    // Calculate WPM and accuracy
    const words = input.trim().split(/\s+/).length;
    const wpm = elapsedTime > 0 ? (words / elapsedTime) * 60 : 0;
    const accuracy = calculateAccuracy(input, target);
    
    // Update display elements if they exist
    const timerEl = document.getElementById('typingTimer');
    const wpmEl = document.getElementById('typingWPM');
    const accuracyEl = document.getElementById('typingAccuracy');
    
    if (timerEl) timerEl.textContent = elapsedTime.toFixed(1);
    if (wpmEl) wpmEl.textContent = Math.round(wpm);
    if (accuracyEl) accuracyEl.textContent = Math.round(accuracy);
}

// Complete typing challenge
async function completeTypingChallenge() {
    if (!miniGameState.timer) return;
    
    clearInterval(miniGameState.timer);
    miniGameState.timer = null;
    
    const typingInput = document.getElementById('typingInput');
    const startBtn = document.getElementById('startTypingBtn');
    
    if (typingInput) typingInput.disabled = true;
    if (startBtn) startBtn.textContent = 'Start Challenge';
    
    const elapsedTime = (Date.now() - miniGameState.startTime) / 1000;
    const wpm = Math.round(((typingInput.value.trim().split(/\s+/).length) / elapsedTime) * 60);
    const accuracy = calculateAccuracy(typingInput.value, miniGameState.currentChallenge.code);
    
    // Calculate score
    const score = Math.round(wpm * (accuracy / 100));
    miniGameState.score += score;
    
    showNotification(`Challenge completed! WPM: ${wpm}, Accuracy: ${accuracy}%, Score: +${score}`, 'success');
    
    // Update displays
    updateGameStats();
    
    // Load new challenge after delay
    setTimeout(() => {
        loadNewChallenge();
        if (typingInput) typingInput.value = '';
    }, 3000);
}

// Calculate typing accuracy
function calculateAccuracy(input, target) {
    if (input.length === 0) return 100;
    
    let correct = 0;
    const minLength = Math.min(input.length, target.length);
    
    for (let i = 0; i < minLength; i++) {
        if (input[i] === target[i]) {
            correct++;
        }
    }
    
    return Math.round((correct / input.length) * 100);
}

// Submit result to API
async function submitResult(gameType, score, details = {}) {
    try {
        const payload = {
            game_type: gameType,
            language: miniGameState.currentLanguage,
            difficulty: miniGameState.currentDifficulty,
            score: score,
            time_taken: miniGameState.startTime ? (Date.now() - miniGameState.startTime) / 1000 : null,
            details: {
                ...details,
                streak: miniGameState.streak,
                timestamp: new Date().toISOString()
            }
        };
        
        const response = await fetch('api/mini-game/save-result.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Result submitted:', data);
        
        // Don't refresh leaderboard after individual answers
        // loadLeaderboard();
        
    } catch (error) {
        console.error('Error submitting result:', error);
    }
}

// Back to menu (equivalent to challenges.js pattern)
async function backToMenu() {
    console.log('🏠 Returning to menu...');
    
    // Save session result if there were any questions answered
    if (miniGameState.totalQuestions > 0) {
        await saveSessionResult();
    }
    
    // Reset game state
    miniGameState.currentMode = null;
    miniGameState.isActive = false;
    miniGameState.currentChallenge = null;
    
    // Hide progress, show welcome (same pattern as challenges.js)
    document.getElementById('mini-game-progress').classList.remove('active');
    document.getElementById('mini-game-welcome').classList.add('active');
    
    // Hide all game containers
    document.querySelectorAll('.game-container').forEach(container => {
        container.classList.add('d-none');
    });
    
    showNotification('Returned to menu', 'info');
}

// Save session result (only called when session ends)
async function saveSessionResult() {
    if (miniGameState.totalQuestions === 0) return;
    
    console.log('💾 Saving session result...', {
        score: miniGameState.score,
        questions: miniGameState.totalQuestions,
        correct: miniGameState.correctAnswers
    });
    
    try {
        const sessionDuration = miniGameState.sessionStartTime ? 
            (Date.now() - miniGameState.sessionStartTime) / 1000 : null;
            
        const payload = {
            game_type: miniGameState.currentMode,
            language: miniGameState.currentLanguage,
            difficulty: miniGameState.currentDifficulty,
            score: miniGameState.score, // Total session score
            time_taken: sessionDuration,
            details: {
                total_questions: miniGameState.totalQuestions,
                correct_answers: miniGameState.correctAnswers,
                incorrect_answers: miniGameState.incorrectAnswers,
                accuracy: Math.round((miniGameState.correctAnswers / Math.max(1, miniGameState.totalQuestions)) * 100),
                session_duration: sessionDuration,
                timestamp: new Date().toISOString()
            }
        };
        
        const response = await fetch('/api/mini-game/save-result.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        });
        
        // First, get the response as text to handle potential HTML errors
        const responseText = await response.text();
        let data;
        
        try {
            // Try to parse as JSON
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON response:', responseText);
            throw new Error('Received invalid JSON response from server');
        }
        
        if (!response.ok) {
            throw new Error(data.error || `HTTP error! status: ${response.status}`);
        }
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to save session result');
        }
        
        console.log('Session result saved:', data);
        
        // Refresh leaderboard after saving session
        loadLeaderboard();
        
        return data;
        
    } catch (error) {
        console.error('Error saving session result:', error);
        // Show error to user in a non-intrusive way
        showNotification(`Failed to save results: ${error.message}`, 'error');
        throw error; // Re-throw to allow callers to handle the error if needed
    }
}

// Show exit confirmation modal
function showExitConfirmation() {
    console.log('🚪 Showing exit confirmation...');
    
    const modal = new bootstrap.Modal(document.getElementById('exitConfirmModal'));
    modal.show();
}

// Update game stats display
function updateGameStats() {
    const scoreEl = document.getElementById('current-score');
    const streakEl = document.getElementById('current-streak');
    
    if (scoreEl) scoreEl.textContent = miniGameState.score;
    if (streakEl) streakEl.textContent = miniGameState.streak;
}

// Update current language display
function updateCurrentLanguageDisplay() {
    const languageEl = document.getElementById('current-language');
    if (languageEl) {
        languageEl.textContent = miniGameState.currentLanguage.charAt(0).toUpperCase() + miniGameState.currentLanguage.slice(1);
    }
}

// Show feedback (simplified version)
function showFeedback(feedbackElement, isCorrect, message, details = null) {
    if (!feedbackElement) return;
    
    feedbackElement.className = `alert ${isCorrect ? 'alert-success' : 'alert-danger'} mt-3`;
    feedbackElement.innerHTML = `
        <strong>${message}</strong>
        ${details && details.explanation ? `<br><small>${details.explanation}</small>` : ''}
        ${details && details.correctAnswer ? `<br><small><strong>Correct Answer:</strong> ${details.correctAnswer}</small>` : ''}
    `;
    feedbackElement.style.display = 'block';
}

// Show notification (same as challenges.js)
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#17a2b8'};
        color: white;
        padding: 12px 20px;
        border-radius: 4px;
        font-weight: bold;
        z-index: 9999;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Load leaderboard
async function loadLeaderboard(scope = 'alltime', gameType = null, page = 1) {
    console.log('📊 Loading leaderboard...', { scope, gameType, page });
    
    try {
        let url = `api/mini-game/leaderboard.php?scope=${scope}&user_id=${miniGameState.userId || ''}&page=${page}`;
        if (gameType) {
            url += `&game_type=${gameType}`;
        }
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Leaderboard loaded:', data);
        
        if (data.success) {
            displayLeaderboard(data.leaderboard);
            displayPagination(data.pagination, scope, gameType);
        } else {
            console.error('Leaderboard error:', data.error);
            displayLeaderboardError('Failed to load leaderboard');
        }
        
    } catch (error) {
        console.error('Error loading leaderboard:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            url: `api/mini-game/leaderboard.php?scope=${scope}&user_id=${miniGameState.userId || ''}`
        });
        displayLeaderboardError(`Network error: ${error.message}`);
    }
}

// Display leaderboard
function displayLeaderboard(leaderboard) {
    const leaderboardList = document.getElementById('leaderboardList');
    if (!leaderboardList) {
        console.warn('Leaderboard list element not found');
        return;
    }
    
    if (!leaderboard || leaderboard.length === 0) {
        leaderboardList.innerHTML = '<li class="text-muted text-center py-3">No scores yet. Be the first to play!</li>';
        return;
    }
    
    const leaderboardHTML = leaderboard.slice(0, 10).map((entry, index) => {
        const rank = index + 1;
        const username = entry.username || 'Guest Player';
        const score = entry.score || 0;
        const gameType = entry.game_type || 'unknown';
        const language = entry.language || 'N/A';
        const timeAgo = formatTimeAgo(entry.played_at);
        const isMe = entry.is_me ? ' (You)' : '';
        
        return `
            <li class="leaderboard-item ${entry.is_me ? 'is-me' : ''}">
                <div class="rank">#${rank}</div>
                <div class="player-avatar">
                    <div class="avatar-circle">
                        <img src="assets/images/avatars/classmate_avatar.gif" alt="Player Avatar" class="avatar-image" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-user\'></i>'">
                    </div>
                </div>
                <div class="player-info">
                    <div class="player-name">${username}${isMe}</div>
                    <div class="game-details">
                        <span class="game-type">${gameType}</span> • 
                        <span class="language">${language}</span> • 
                        <span class="time-ago">${timeAgo}</span>
                    </div>
                </div>
                <div class="score">${score}</div>
            </li>
        `;
    }).join('');
    
    leaderboardList.innerHTML = leaderboardHTML;
}

// Display leaderboard error
function displayLeaderboardError(message) {
    const leaderboardList = document.getElementById('leaderboardList');
    if (leaderboardList) {
        leaderboardList.innerHTML = `<li class="text-danger text-center py-3">${message}</li>`;
    }
    
    // Clear pagination on error
    const paginationDiv = document.getElementById('leaderboardPagination');
    if (paginationDiv) {
        paginationDiv.innerHTML = '';
    }
}

// Display pagination
function displayPagination(pagination, scope, gameType) {
    const paginationDiv = document.getElementById('leaderboardPagination');
    if (!paginationDiv || !pagination) return;
    
    const { current_page, total_pages, total_results, has_prev, has_next } = pagination;
    
    if (total_pages <= 1) {
        paginationDiv.innerHTML = `<small class="text-muted">${total_results} result${total_results !== 1 ? 's' : ''}</small>`;
        return;
    }
    
    let paginationHTML = '<div class="pagination-controls d-flex justify-content-between align-items-center">';
    
    // Previous button
    paginationHTML += `
        <button class="btn btn-sm btn-outline-light pagination-btn" 
                data-page="${current_page - 1}" 
                ${!has_prev ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i> Previous
        </button>
    `;
    
    // Page info
    paginationHTML += `
        <span class="pagination-info">
            Page ${current_page} of ${total_pages} 
            <small class="text-muted">(${total_results} total)</small>
        </span>
    `;
    
    // Next button
    paginationHTML += `
        <button class="btn btn-sm btn-outline-light pagination-btn" 
                data-page="${current_page + 1}" 
                ${!has_next ? 'disabled' : ''}>
            Next <i class="fas fa-chevron-right"></i>
        </button>
    `;
    
    paginationHTML += '</div>';
    paginationDiv.innerHTML = paginationHTML;
    
    // Add event listeners to pagination buttons
    paginationDiv.querySelectorAll('.pagination-btn:not([disabled])').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.getAttribute('data-page'));
            loadLeaderboard(scope, gameType, page);
        });
    });
}

// Format time ago (simplified version)
function formatTimeAgo(timestamp) {
    if (!timestamp) return 'Unknown';
    
    const now = new Date();
    const played = new Date(timestamp);
    const diff = Math.floor((now - played) / 1000);

    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
    return `${Math.floor(diff / 604800)}w ago`;
}

// Test leaderboard API
async function testLeaderboardAPI() {
    console.log('🧪 Testing leaderboard API...');
    
    try {
        const response = await fetch('/api/mini-game/test-leaderboard.php', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        // First get the response as text to handle potential HTML errors
        const responseText = await response.text();
        let data;
        
        try {
            // Try to parse as JSON
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('❌ Failed to parse JSON response:', responseText.substring(0, 200));
            throw new Error('Received invalid JSON response from server');
        }
        
        console.log('🧪 Test results:', data);
        
        if (data && data.success) {
            console.log('✅ Database tests passed:', {
                tableExists: data.tests?.table_exists || false,
                totalRecords: data.tests?.total_records || 0,
                sampleRecords: data.tests?.sample_records?.length || 0
            });
            return true;
        } else {
            const errorMsg = data?.error || 'Unknown error occurred';
            console.error('❌ Database tests failed:', errorMsg);
            return false;
        }
    } catch (error) {
        console.error('❌ Test API failed:', error);
        return false;
    }
}

// Initialize when DOM is loaded (same pattern as challenges.js)
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎮 Mini-Game DOM Loaded');
    
    // Debug: Check if key elements exist
    console.log('Welcome screen:', document.getElementById('mini-game-welcome'));
    console.log('Progress screen:', document.getElementById('mini-game-progress'));
    console.log('Language select:', document.getElementById('languageSelect'));
    console.log('Difficulty select:', document.getElementById('difficultySelect'));
    
    initMiniGame();
    attachMiniGameEventListeners();
});
