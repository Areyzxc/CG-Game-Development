/**
 * File: mini-game-play.js
 * Purpose: Dedicated gameplay mechanics for CodeGaming mini-games
 * Features:
 *   - AJAX-based game data fetching
 *   - Real-time gameplay handling
 *   - Score tracking and submission
 *   - Leaderboard updates
 * 
 * Author: CodeGaming Team
 * Last Updated: September 29, 2025
 */

// Game state management
const GameState = {
    currentMode: null,
    currentLanguage: 'javascript',
    currentDifficulty: 'beginner',
    isActive: false,
    score: 0,
    streak: 0,
    currentChallenge: null,
    startTime: null,
    timer: null,
    
    // Reset game state
    reset() {
        this.currentMode = null;
        this.isActive = false;
        this.score = 0;
        this.streak = 0;
        this.currentChallenge = null;
        this.startTime = null;
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    },
    
    // Start new game
    start(mode) {
        this.reset();
        this.currentMode = mode;
        this.isActive = true;
        this.startTime = Date.now();
        console.log(`Game started: ${mode}`);
    }
};

// AJAX utility functions
const API = {
    // Fetch game challenge
    async getChallenge(gameType, language, difficulty) {
        try {
            const response = await fetch('api/mini-game/get-challenge.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    game_type: gameType,
                    language: language,
                    difficulty: difficulty
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Challenge fetched:', data);
            return data;
        } catch (error) {
            console.error('Error fetching challenge:', error);
            return { success: false, error: error.message };
        }
    },
    
    // Submit game result
    async submitResult(gameType, score, details = {}) {
        try {
            const payload = {
                game_type: gameType,
                language: GameState.currentLanguage,
                difficulty: GameState.currentDifficulty,
                score: score,
                time_taken: GameState.startTime ? (Date.now() - GameState.startTime) / 1000 : null,
                details: {
                    ...details,
                    streak: GameState.streak,
                    timestamp: new Date().toISOString()
                }
            };
            
            console.log('Submitting result:', payload);
            
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
            return data;
        } catch (error) {
            console.error('Error submitting result:', error);
            return { success: false, error: error.message };
        }
    },
    
    // Fetch leaderboard
    async getLeaderboard(scope = 'alltime') {
        try {
            let params = `scope=${encodeURIComponent(scope)}`;
            
            // Add user identification
            if (window.CG_USER_ID) {
                params += `&user_id=${encodeURIComponent(window.CG_USER_ID)}`;
            }
            
            const response = await fetch(`api/mini-game/leaderboard.php?${params}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Leaderboard fetched:', data);
            return data;
        } catch (error) {
            console.error('Error fetching leaderboard:', error);
            return { success: false, error: error.message };
        }
    }
};

// Game mechanics
const GameMechanics = {
    // Start a new game
    async startGame(mode) {
        console.log('Starting game mechanics for mode:', mode);
        
        if (!mode) {
            console.error('No game mode specified');
            return false;
        }
        
        // Initialize game state
        GameState.start(mode);
        
        // Show game interface
        this.showGameInterface(mode);
        
        // Load first challenge
        await this.loadNewChallenge();
        
        return true;
    },
    
    // Show game interface
    showGameInterface(mode) {
        const elements = {
            gameModeCards: document.getElementById('gameModeCards'),
            gameSettings: document.getElementById('gameSettings'),
            gameContent: document.getElementById('gameContent'),
            gameContainer: document.getElementById(`${mode}-content`),
            gameTitle: document.getElementById('currentGameTitle')
        };
        
        // Validate elements exist
        for (const [name, element] of Object.entries(elements)) {
            if (!element) {
                console.error(`Element not found: ${name}`);
                return false;
            }
        }
        
        // Hide mode selection, show game
        elements.gameModeCards.classList.add('d-none');
        elements.gameSettings.classList.remove('d-none');
        elements.gameContent.classList.remove('d-none');
        elements.gameContainer.classList.remove('d-none');
        
        // Update title
        const titleText = mode === 'guess' ? 'Guess the Output' : 'Fast Code Typing';
        elements.gameTitle.textContent = titleText;
        
        // Update score display
        this.updateScoreDisplay();
        
        console.log('Game interface shown successfully');
        return true;
    },
    
    // Load new challenge
    async loadNewChallenge() {
        if (!GameState.isActive) {
            console.log('Game not active, skipping challenge load');
            return;
        }
        
        console.log('Loading new challenge...');
        
        // Show loading state
        this.showLoadingState();
        
        // Fetch challenge from API
        const challengeData = await API.getChallenge(
            GameState.currentMode,
            GameState.currentLanguage,
            GameState.currentDifficulty
        );
        
        if (!challengeData.success) {
            this.showError('Failed to load challenge: ' + challengeData.error);
            return;
        }
        
        // Store current challenge
        GameState.currentChallenge = challengeData.challenge;
        
        // Display challenge based on game mode
        if (GameState.currentMode === 'guess') {
            this.displayGuessChallenge(challengeData.challenge);
        } else if (GameState.currentMode === 'typing') {
            this.displayTypingChallenge(challengeData.challenge);
        }
        
        console.log('Challenge loaded successfully');
    },
    
    // Display guess challenge
    displayGuessChallenge(challenge) {
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
        
        console.log('Guess challenge displayed');
    },
    
    // Display typing challenge
    displayTypingChallenge(challenge) {
        const codeDisplay = document.getElementById('typingCodeDisplay');
        const typingInput = document.getElementById('typingInput');
        const feedback = document.getElementById('typingFeedback');
        const startBtn = document.getElementById('startTypingBtn');
        
        if (!codeDisplay || !typingInput || !startBtn) {
            console.error('Typing game elements not found');
            return;
        }
        
        // Clear previous state
        typingInput.value = '';
        typingInput.disabled = true;
        if (feedback) feedback.style.display = 'none';
        
        // Reset timer display
        this.updateTypingStats(0, 0, 100);
        
        // Display code
        codeDisplay.textContent = challenge.code || challenge.content;
        
        // Apply syntax highlighting if available
        if (typeof hljs !== 'undefined') {
            hljs.highlightElement(codeDisplay);
        }
        
        // Reset start button
        startBtn.textContent = 'Start Challenge';
        startBtn.disabled = false;
        
        console.log('Typing challenge displayed');
    },
    
    // Show loading state
    showLoadingState() {
        const containers = ['guessCodeDisplay', 'typingCodeDisplay'];
        containers.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading challenge...</div>';
            }
        });
    },
    
    // Show error message
    showError(message) {
        const containers = ['guessCodeDisplay', 'typingCodeDisplay'];
        containers.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = `<div class="text-center p-4 text-danger"><i class="fas fa-exclamation-triangle"></i> ${message}</div>`;
            }
        });
    },
    
    // Update score display
    updateScoreDisplay() {
        const scoreElements = {
            guessScore: document.getElementById('guessScore'),
            guessStreak: document.getElementById('guessStreak')
        };
        
        if (scoreElements.guessScore) {
            scoreElements.guessScore.textContent = GameState.score;
        }
        if (scoreElements.guessStreak) {
            scoreElements.guessStreak.textContent = GameState.streak;
        }
    },
    
    // Update typing statistics
    updateTypingStats(time, wpm, accuracy) {
        const elements = {
            timer: document.getElementById('typingTimer'),
            wpm: document.getElementById('typingWPM'),
            accuracy: document.getElementById('typingAccuracy')
        };
        
        if (elements.timer) elements.timer.textContent = time.toFixed(1);
        if (elements.wpm) elements.wpm.textContent = Math.round(wpm);
        if (elements.accuracy) elements.accuracy.textContent = Math.round(accuracy);
    },
    
    // Back to menu
    backToMenu() {
        console.log('Returning to menu...');
        
        // Reset game state
        GameState.reset();
        
        // Hide game content, show mode selection
        const elements = {
            gameContent: document.getElementById('gameContent'),
            gameSettings: document.getElementById('gameSettings'),
            gameModeCards: document.getElementById('gameModeCards'),
            guessContent: document.getElementById('guess-content'),
            typingContent: document.getElementById('typing-content')
        };
        
        Object.values(elements).forEach(element => {
            if (element) {
                if (element.id === 'gameModeCards') {
                    element.classList.remove('d-none');
                } else {
                    element.classList.add('d-none');
                }
            }
        });
        
        // Refresh leaderboard
        LeaderboardManager.refresh();
        
        console.log('Returned to menu successfully');
    }
};

// Leaderboard management
const LeaderboardManager = {
    currentGameType: 'guess',
    
    // Refresh leaderboard
    async refresh(gameType = null) {
        if (gameType) {
            this.currentGameType = gameType;
        }
        
        console.log('Refreshing leaderboard for:', this.currentGameType);
        
        const leaderboardList = document.getElementById('leaderboardList');
        if (!leaderboardList) {
            console.error('Leaderboard list element not found');
            return;
        }
        
        // Show loading
        leaderboardList.innerHTML = '<li class="leaderboard-item text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</li>';
        
        // Fetch data
        const data = await API.getLeaderboard('alltime');
        
        if (!data.success) {
            leaderboardList.innerHTML = '<li class="leaderboard-item text-center text-danger">Failed to load leaderboard</li>';
            return;
        }
        
        // Display leaderboard
        if (data.leaderboard && data.leaderboard.length > 0) {
            leaderboardList.innerHTML = data.leaderboard.map((entry, index) => `
                <li class="leaderboard-item${entry.is_me ? ' highlight' : ''}">
                    <div class="player-info">
                        <span class="player-rank">#${index + 1}</span>
                        <span class="player-name">${entry.username || entry.nickname || 'Guest'}</span>
                        <span class="player-game badge bg-secondary ms-2">${entry.game_type || 'Mixed'}</span>
                    </div>
                    <div class="player-score">
                        ${entry.score || 0}
                    </div>
                </li>
            `).join('');
        } else {
            leaderboardList.innerHTML = '<li class="leaderboard-item text-center text-muted">No scores yet. Be the first to play!</li>';
        }
        
        console.log('Leaderboard refreshed successfully');
    }
};

// Game event handlers
const GameHandlers = {
    // Handle guess answer submission
    async handleGuessAnswer() {
        const answerInput = document.getElementById('guessAnswer');
        const feedback = document.getElementById('guessFeedback');
        
        if (!answerInput || !GameState.currentChallenge) {
            console.error('Cannot submit answer: missing elements or challenge');
            return;
        }
        
        const userAnswer = answerInput.value.trim();
        if (!userAnswer) {
            this.showFeedback(feedback, false, 'Please enter your answer.');
            return;
        }
        
        const isCorrect = userAnswer.toLowerCase() === GameState.currentChallenge.answer.toLowerCase();
        
        if (isCorrect) {
            GameState.score += 100;
            GameState.streak += 1;
            this.showFeedback(feedback, true, `Correct! Well done! (+100 points)`, {
                explanation: GameState.currentChallenge.explanation
            });
            
            // Submit result
            await API.submitResult('guess', 100, {
                correct: true,
                user_answer: userAnswer,
                correct_answer: GameState.currentChallenge.answer
            });
        } else {
            GameState.streak = 0;
            this.showFeedback(feedback, false, 'Incorrect answer.', {
                correctAnswer: GameState.currentChallenge.answer,
                explanation: GameState.currentChallenge.explanation
            });
            
            // Submit result
            await API.submitResult('guess', 0, {
                correct: false,
                user_answer: userAnswer,
                correct_answer: GameState.currentChallenge.answer
            });
        }
        
        // Update displays
        GameMechanics.updateScoreDisplay();
        LeaderboardManager.refresh();
        
        // Load new challenge after delay
        setTimeout(() => {
            GameMechanics.loadNewChallenge();
            answerInput.value = '';
            if (feedback) feedback.style.display = 'none';
        }, 3000);
    },
    
    // Handle typing challenge start
    handleTypingStart() {
        const typingInput = document.getElementById('typingInput');
        const startBtn = document.getElementById('startTypingBtn');
        
        if (!typingInput || !startBtn) {
            console.error('Typing elements not found');
            return;
        }
        
        if (GameState.timer) {
            // Reset if already running
            this.resetTypingChallenge();
            return;
        }
        
        // Start typing challenge
        typingInput.disabled = false;
        typingInput.focus();
        startBtn.textContent = 'Reset';
        
        GameState.startTime = Date.now();
        GameState.timer = setInterval(() => {
            this.updateTypingProgress();
        }, 100);
        
        console.log('Typing challenge started');
    },
    
    // Handle typing input
    handleTypingInput() {
        if (!GameState.timer || !GameState.currentChallenge) return;
        
        const typingInput = document.getElementById('typingInput');
        if (!typingInput) return;
        
        const input = typingInput.value;
        const target = GameState.currentChallenge.code || GameState.currentChallenge.content;
        
        // Check if completed
        if (input === target) {
            this.completeTypingChallenge();
        }
    },
    
    // Update typing progress
    updateTypingProgress() {
        if (!GameState.startTime) return;
        
        const typingInput = document.getElementById('typingInput');
        if (!typingInput) return;
        
        const elapsedTime = (Date.now() - GameState.startTime) / 1000;
        const input = typingInput.value;
        const target = GameState.currentChallenge.code || GameState.currentChallenge.content;
        
        // Calculate WPM (words per minute)
        const words = input.trim().split(/\s+/).length;
        const wpm = elapsedTime > 0 ? (words / elapsedTime) * 60 : 0;
        
        // Calculate accuracy
        const accuracy = this.calculateAccuracy(input, target);
        
        // Update display
        GameMechanics.updateTypingStats(elapsedTime, wpm, accuracy);
    },
    
    // Complete typing challenge
    async completeTypingChallenge() {
        if (!GameState.timer) return;
        
        clearInterval(GameState.timer);
        GameState.timer = null;
        
        const typingInput = document.getElementById('typingInput');
        const startBtn = document.getElementById('startTypingBtn');
        const feedback = document.getElementById('typingFeedback');
        
        if (typingInput) typingInput.disabled = true;
        if (startBtn) startBtn.textContent = 'Start Challenge';
        
        const elapsedTime = (Date.now() - GameState.startTime) / 1000;
        const wpm = Math.round(((typingInput.value.trim().split(/\s+/).length) / elapsedTime) * 60);
        const accuracy = this.calculateAccuracy(typingInput.value, GameState.currentChallenge.code);
        
        // Calculate score
        const score = Math.round(wpm * (accuracy / 100));
        GameState.score += score;
        
        this.showFeedback(feedback, true, `Challenge completed! Score: ${score}`, {
            time: elapsedTime.toFixed(1),
            wpm: wpm,
            accuracy: accuracy
        });
        
        // Submit result
        await API.submitResult('typing', wpm, {
            time_taken: elapsedTime,
            accuracy: accuracy,
            wpm: wpm,
            score: score
        });
        
        // Update displays
        GameMechanics.updateScoreDisplay();
        LeaderboardManager.refresh();
        
        // Load new challenge after delay
        setTimeout(() => {
            GameMechanics.loadNewChallenge();
            if (typingInput) typingInput.value = '';
            if (feedback) feedback.style.display = 'none';
        }, 3000);
    },
    
    // Reset typing challenge
    resetTypingChallenge() {
        if (GameState.timer) {
            clearInterval(GameState.timer);
            GameState.timer = null;
        }
        
        const typingInput = document.getElementById('typingInput');
        const startBtn = document.getElementById('startTypingBtn');
        
        if (typingInput) {
            typingInput.value = '';
            typingInput.disabled = true;
        }
        if (startBtn) startBtn.textContent = 'Start Challenge';
        
        GameMechanics.updateTypingStats(0, 0, 100);
    },
    
    // Calculate typing accuracy
    calculateAccuracy(input, target) {
        if (input.length === 0) return 100;
        
        let correct = 0;
        const minLength = Math.min(input.length, target.length);
        
        for (let i = 0; i < minLength; i++) {
            if (input[i] === target[i]) {
                correct++;
            }
        }
        
        return Math.round((correct / input.length) * 100);
    },
    
    // Show feedback message
    showFeedback(feedbackElement, isCorrect, message, details = null) {
        if (!feedbackElement) return;
        
        const feedbackMessage = feedbackElement.querySelector('.feedback-message');
        const correctAnswerDiv = feedbackElement.querySelector('.correct-answer');
        
        if (feedbackMessage) {
            feedbackMessage.textContent = message;
        }
        
        feedbackElement.className = `result-feedback mt-3 ${isCorrect ? 'success' : 'error'}`;
        
        if (details && correctAnswerDiv) {
            correctAnswerDiv.style.display = 'block';
            let content = '';
            
            if (details.correctAnswer) {
                content += `<strong>Correct Answer:</strong> ${details.correctAnswer}<br>`;
            }
            if (details.explanation) {
                content += `<strong>Explanation:</strong> ${details.explanation}<br>`;
            }
            if (details.time && details.wpm && details.accuracy) {
                content += `<strong>Stats:</strong> Time: ${details.time}s, WPM: ${details.wpm}, Accuracy: ${details.accuracy}%`;
            }
            
            correctAnswerDiv.innerHTML = content;
        } else if (correctAnswerDiv) {
            correctAnswerDiv.style.display = 'none';
        }
        
        feedbackElement.style.display = 'flex';
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('Mini-game play mechanics loaded');
    
    // Set up game event listeners
    const checkGuessBtn = document.getElementById('checkGuessBtn');
    const startTypingBtn = document.getElementById('startTypingBtn');
    const typingInput = document.getElementById('typingInput');
    
    if (checkGuessBtn) {
        checkGuessBtn.addEventListener('click', () => GameHandlers.handleGuessAnswer());
    }
    
    if (startTypingBtn) {
        startTypingBtn.addEventListener('click', () => GameHandlers.handleTypingStart());
    }
    
    if (typingInput) {
        typingInput.addEventListener('input', () => GameHandlers.handleTypingInput());
    }
    
    // Initial leaderboard load
    LeaderboardManager.refresh();
});

// Export for global access
window.GameMechanics = GameMechanics;
window.GameState = GameState;
window.LeaderboardManager = LeaderboardManager;
window.GameHandlers = GameHandlers;
