/**
 * File: mini-game.js
 * Purpose: Implements interactive mini-game logic for CodeGaming, including guess and typing challenges, state management, feedback, and leaderboard integration.
 * Features:
 *   - Manages game state, language/difficulty selection, and game type switching.
 *   - Loads code snippets for guess and typing games across multiple languages and difficulties.
 *   - Handles answer checking, typing speed calculation, and feedback display.
 *   - Saves results and updates leaderboards via API.
 *   - Loads user results and leaderboard data for display.
 *   - Note: More additional features may be added in the future.
 * 
 * Usage:
 *   - Included on mini-game pages requiring interactive coding challenges.
 *   - Requires specific HTML structure for game controls, displays, and feedback.
 *   - Relies on API endpoints: api/mini-game/save-result.php, api/mini-game/leaderboard.php, api/mini-game/user-results.php, api/get_leaderboard.php, api/check_answer.php, api/save_result.php.
 * Included Files/Dependencies:
 *   - highlight.js (syntax highlighting)
 *   - Bootstrap (UI)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// Game state
const gameState = {
    currentLanguage: 'javascript',
    currentGameType: 'guess',
    currentDifficulty: 'beginner',
    isTypingActive: false,
    typingStartTime: null,
    typingTimer: null,
    currentSnippet: null
};

// Code snippets for each language, game type, and difficulty
const codeSnippets = {
    guess: {
        javascript: {
            beginner: [
                {
                    code: 'console.log(2 + "2");',
                    answer: '22',
                    explanation: 'The + operator concatenates strings. 2 is converted to a string.'
                },
                {
                    code: 'console.log([1, 2, 3].map(x => x * 2));',
                    answer: '[2, 4, 6]',
                    explanation: 'map() creates a new array with the results of calling a function for every array element.'
                }
            ],
            intermediate: [
                {
                    code: 'console.log(typeof typeof 1);',
                    answer: 'string',
                    explanation: 'typeof 1 returns "number", then typeof "number" returns "string".'
                },
                {
                    code: 'console.log([..."hello"].map(c => c.toUpperCase()));',
                    answer: '["H", "E", "L", "L", "O"]',
                    explanation: 'Spread operator splits string into array, map() converts each character to uppercase.'
                }
            ],
            expert: [
                {
                    code: 'console.log([1, 2, 3].reduce((a, b) => a + b, 0));',
                    answer: '6',
                    explanation: 'reduce() accumulates array values, starting from 0.'
                },
                {
                    code: 'console.log(new Set([1, 2, 2, 3, 3, 3]).size);',
                    answer: '3',
                    explanation: 'Set removes duplicates, size gives count of unique values.'
                }
            ]
        },
        python: {
            beginner: [
                {
                    code: 'print(2 + "2")',
                    answer: 'TypeError',
                    explanation: 'Python cannot concatenate integers and strings directly.'
                }
            ],
            intermediate: [
                {
                    code: 'print([x * 2 for x in [1, 2, 3]])',
                    answer: '[2, 4, 6]',
                    explanation: 'List comprehension creates a new list with each element multiplied by 2.'
                }
            ],
            expert: [
                {
                    code: 'print(sum(x for x in range(10) if x % 2 == 0))',
                    answer: '20',
                    explanation: 'Generator expression sums even numbers from 0 to 9.'
                }
            ]
        },
        html: [
            {
                code: '<div style="color: red">Hello</div>',
                answer: 'Hello',
                explanation: 'The text "Hello" will be displayed in red color.'
            }
        ],
        css: [
            {
                code: 'div { color: red; }',
                answer: 'Red text',
                explanation: 'All div elements will have red text color.'
            }
        ],
        java: [
            {
                code: 'System.out.println("Hello" + 2 + 2);',
                answer: 'Hello22',
                explanation: 'String concatenation happens from left to right.'
            }
        ],
        cpp: [
            {
                code: 'cout << "Hello" << 2 + 2;',
                answer: 'Hello4',
                explanation: 'The + operator has higher precedence than <<.'
            }
        ],
        bootstrap: [
            {
                code: '<button class="btn btn-primary">Click</button>',
                answer: 'Blue button',
                explanation: 'Bootstrap btn-primary class creates a blue button.'
            }
        ]
    },
    typing: {
        javascript: {
            beginner: [
                'function greet(name) {\n  return `Hello, ${name}!`;\n}',
                'const numbers = [1, 2, 3];\nconst doubled = numbers.map(n => n * 2);'
            ],
            intermediate: [
                'class Person {\n  constructor(name) {\n    this.name = name;\n  }\n  greet() {\n    return `Hello, ${this.name}!`;\n  }\n}',
                'const asyncFunction = async () => {\n  const result = await fetch(url);\n  return result.json();\n};'
            ],
            expert: [
                'const memoize = (fn) => {\n  const cache = new Map();\n  return (...args) => {\n    const key = JSON.stringify(args);\n    return cache.has(key) ? cache.get(key) : cache.set(key, fn(...args)).get(key);\n  };\n};',
                'class Observable {\n  constructor() {\n    this.observers = new Set();\n  }\n  subscribe(fn) {\n    this.observers.add(fn);\n    return () => this.observers.delete(fn);\n  }\n  notify(data) {\n    this.observers.forEach(fn => fn(data));\n  }\n}'
            ]
        },
        python: [
            'def greet(name):\n    return f"Hello, {name}!"',
            'numbers = [1, 2, 3]\ndoubled = [n * 2 for n in numbers]',
            'class Person:\n    def __init__(self, name):\n        self.name = name'
        ],
        html: [
            '<div class="container">\n  <h1>Hello World</h1>\n  <p>Welcome!</p>\n</div>',
            '<nav class="navbar">\n  <a href="#">Home</a>\n  <a href="#">About</a>\n</nav>'
        ],
        css: [
            '.container {\n  max-width: 1200px;\n  margin: 0 auto;\n  padding: 20px;\n}',
            '@media (max-width: 768px) {\n  .container {\n    padding: 10px;\n  }\n}'
        ],
        java: [
            'public class Main {\n  public static void main(String[] args) {\n    System.out.println("Hello");\n  }\n}',
            'for (int i = 0; i < 10; i++) {\n  System.out.println(i);\n}'
        ],
        cpp: [
            '#include <iostream>\n\nint main() {\n  std::cout << "Hello";\n  return 0;\n}',
            'for (int i = 0; i < 10; i++) {\n  std::cout << i << std::endl;\n}'
        ],
        bootstrap: [
            '<div class="container">\n  <div class="row">\n    <div class="col-md-6">\n      <div class="card">\n        <div class="card-body">\n          Hello\n        </div>\n      </div>\n    </div>\n  </div>\n</div>'
        ]
    }
};

// Initialize the game
document.addEventListener('DOMContentLoaded', () => {
    // Set up event listeners
    document.getElementById('languageSelect').addEventListener('change', handleLanguageChange);
    document.getElementById('difficultySelect').addEventListener('change', handleDifficultyChange);
    document.getElementById('checkGuessBtn').addEventListener('click', checkGuessAnswer);
    document.getElementById('startTypingBtn').addEventListener('click', startTypingChallenge);
    document.getElementById('typingInput').addEventListener('input', handleTypingInput);
    
    // Set up tab change listener
    const gameTabs = document.querySelectorAll('#gameTabs button');
    gameTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            gameState.currentGameType = e.target.id.split('-')[0];
            loadNewChallenge();
        });
    });

    // Load initial challenge
    loadNewChallenge();
    
    // Load leaderboard if user is logged in
    if (document.getElementById('leaderboard')) {
        loadLeaderboard();
        loadUserResults();
    }
});

// Handle language change
function handleLanguageChange(e) {
    gameState.currentLanguage = e.target.value;
    loadNewChallenge();
}

// Handle difficulty change
function handleDifficultyChange(e) {
    gameState.currentDifficulty = e.target.value;
    loadNewChallenge();
}

// Load a new challenge based on current game type, language, and difficulty
function loadNewChallenge() {
    const snippets = codeSnippets[gameState.currentGameType][gameState.currentLanguage]?.[gameState.currentDifficulty];
    if (!snippets || snippets.length === 0) {
        showFeedback(`${gameState.currentGameType}Feedback`, false, 'No challenges available for this combination of language and difficulty.');
        return;
    }

    // Reset game state
    resetGameState();

    // Get random snippet
    const randomIndex = Math.floor(Math.random() * snippets.length);
    gameState.currentSnippet = snippets[randomIndex];

    // Display the code
    const codeDisplay = document.getElementById(`${gameState.currentGameType}CodeDisplay`);
    if (gameState.currentGameType === 'guess') {
        codeDisplay.textContent = gameState.currentSnippet.code;
        document.getElementById('guessAnswer').value = '';
        document.getElementById('guessFeedback').style.display = 'none';
    } else {
        codeDisplay.textContent = gameState.currentSnippet;
        document.getElementById('typingInput').value = '';
        document.getElementById('typingFeedback').style.display = 'none';
        document.getElementById('typingTimer').textContent = '0';
        document.getElementById('typingWPM').textContent = '0';
    }

    // Apply syntax highlighting
    hljs.highlightElement(codeDisplay);
}

// Check guess answer
function checkGuessAnswer() {
    const userAnswer = document.getElementById('guessAnswer').value.trim();
    
    if (!userAnswer) {
        showFeedback('guessFeedback', false, 'Please enter your answer.');
        return;
    }

    const isCorrect = userAnswer.toLowerCase() === gameState.currentSnippet.answer.toLowerCase();
    
    if (isCorrect) {
        showFeedback('guessFeedback', true, 'Correct! Well done!', {
            explanation: gameState.currentSnippet.explanation
        });
        saveResult('guess', 100);
    } else {
        showFeedback('guessFeedback', false, 'Incorrect answer.', {
            correctAnswer: gameState.currentSnippet.answer,
            explanation: gameState.currentSnippet.explanation
        });
        saveResult('guess', 0);
    }

    // Update leaderboard after answer
    updateLeaderboard('guess');
}

// Start typing challenge
function startTypingChallenge() {
    if (gameState.isTypingActive) {
        resetGameState();
        return;
    }

    gameState.isTypingActive = true;
    gameState.typingStartTime = Date.now();
    document.getElementById('startTypingBtn').textContent = 'Reset';
    document.getElementById('typingInput').focus();
    
    // Start timer
    gameState.typingTimer = setInterval(updateTypingStats, 100);
}

// Handle typing input
function handleTypingInput(e) {
    if (!gameState.isTypingActive) return;

    const input = e.target.value;
    const targetCode = gameState.currentSnippet;
    
    // Check if challenge is complete
    if (input === targetCode) {
        completeTypingChallenge();
    }
}

// Update typing statistics
function updateTypingStats() {
    const elapsedTime = (Date.now() - gameState.typingStartTime) / 1000;
    const input = document.getElementById('typingInput').value;
    const words = input.trim().split(/\s+/).length;
    const wpm = Math.round((words / elapsedTime) * 60);

    document.getElementById('typingTimer').textContent = elapsedTime.toFixed(1);
    document.getElementById('typingWPM').textContent = wpm;
}

// Complete typing challenge
function completeTypingChallenge() {
    clearInterval(gameState.typingTimer);
    gameState.isTypingActive = false;
    
    const elapsedTime = (Date.now() - gameState.typingStartTime) / 1000;
    const wpm = parseInt(document.getElementById('typingWPM').textContent);
    
    showFeedback(`Challenge completed! Your speed: ${wpm} WPM`, 'success');
    saveResult('typing', wpm);
    
    document.getElementById('startTypingBtn').textContent = 'Start Challenge';
    
    // Load new challenge after a delay
    setTimeout(loadNewChallenge, 2000);
}

// Reset game state
function resetGameState() {
    gameState.isTypingActive = false;
    if (gameState.typingTimer) {
        clearInterval(gameState.typingTimer);
    }
    gameState.typingStartTime = null;
    document.getElementById('startTypingBtn').textContent = 'Start Challenge';
    document.getElementById('typingInput').value = '';
    document.getElementById('typingTimer').textContent = '0';
    document.getElementById('typingWPM').textContent = '0';
}

// Show feedback message
function showFeedback(elementId, isCorrect, message, details = null) {
    const feedback = document.getElementById(elementId);
    const feedbackMessage = feedback.querySelector('.feedback-message');
    const correctAnswerDiv = feedback.querySelector('.correct-answer');
    const correctAnswerPre = feedback.querySelector('pre');
    const performanceStats = feedback.querySelector('.performance-stats');

    feedback.className = `result-feedback mt-3 ${isCorrect ? 'success' : 'error'}`;
    feedbackMessage.textContent = message;
    
    if (details) {
        correctAnswerDiv.style.display = 'block';
        if (details.correctAnswer) {
            correctAnswerPre.innerHTML = `<strong>Correct Answer:</strong> ${details.correctAnswer}`;
        }
        if (details.explanation) {
            const explanationDiv = document.createElement('div');
            explanationDiv.className = 'explanation mt-2';
            explanationDiv.innerHTML = `<strong>Explanation:</strong> ${details.explanation}`;
            correctAnswerDiv.appendChild(explanationDiv);
        }
        if (details.time) {
            performanceStats.innerHTML = `
                <div>Time: ${details.time}s</div>
                <div>WPM: ${details.wpm}</div>
                <div>Accuracy: ${details.accuracy}%</div>
            `;
        }
    } else {
        correctAnswerDiv.style.display = 'none';
    }

    feedback.style.display = 'flex';
}

// Dismiss feedback
document.querySelectorAll('.dismiss-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.result-feedback').style.display = 'none';
    });
});

// Leaderboard handling
let currentGameType = 'guess';

function updateLeaderboard(gameType) {
    currentGameType = gameType;
    fetch(`api/get_leaderboard.php?game_type=${gameType}&difficulty=${gameState.currentDifficulty}`)
        .then(response => response.json())
        .then(data => {
            const leaderboardList = document.getElementById('leaderboardList');
            leaderboardList.innerHTML = '';

            data.forEach((entry, index) => {
                const li = document.createElement('li');
                li.className = 'leaderboard-item';
                li.innerHTML = `
                    <div class="player-info">
                        <span class="player-rank">#${index + 1}</span>
                        <span class="player-name">${entry.username}</span>
                        <span class="player-difficulty badge bg-secondary ms-2">${entry.difficulty}</span>
                    </div>
                    <div class="player-score">
                        ${gameType === 'guess' ? 
                            `${entry.correct_answers}/${entry.total_attempts}` : 
                            `${entry.wpm} WPM`}
                    </div>
                `;
                leaderboardList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching leaderboard:', error));
}

// Game mode selector handling
document.querySelectorAll('.game-mode-selector').forEach(selector => {
    selector.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.game-mode-selector').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');

        // Update leaderboard
        updateLeaderboard(this.dataset.gameType);
    });
});

// Update leaderboard when game type changes
document.querySelectorAll('[data-game-type]').forEach(element => {
    element.addEventListener('click', function() {
        if (this.dataset.gameType) {
            updateLeaderboard(this.dataset.gameType);
        }
    });
});

// Initialize leaderboard
updateLeaderboard('guess');

// Modify existing checkAnswer function
function checkAnswer() {
    const answer = document.getElementById('guessAnswer').value.trim();
    const codeDisplay = document.getElementById('guessCodeDisplay').textContent;
    
    fetch('api/check_answer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            answer: answer,
            code: codeDisplay,
            game_type: 'guess',
            language: document.getElementById('languageSelect').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.correct) {
            showFeedback('guessFeedback', true, 'Correct! Well done!');
        } else {
            showFeedback('guessFeedback', false, 'Incorrect. Here\'s the correct answer:', data.correct_answer);
        }
        
        // Update leaderboard after answer
        updateLeaderboard('guess');
    })
    .catch(error => console.error('Error:', error));
}

// Modify existing typing game completion
function completeTypingGame(stats) {
    const feedback = document.getElementById('typingFeedback');
    showFeedback('typingFeedback', true, 'Challenge completed!', {
        time: stats.time,
        wpm: stats.wpm,
        accuracy: stats.accuracy
    });

    // Save result and update leaderboard
    fetch('api/save_result.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            game_type: 'typing',
            language: document.getElementById('languageSelect').value,
            difficulty: gameState.currentDifficulty,
            wpm: stats.wpm,
            accuracy: stats.accuracy,
            time: stats.time
        })
    })
    .then(() => updateLeaderboard('typing'))
    .catch(error => console.error('Error saving result:', error));
}

// Save game result
async function saveResult(gameType, score) {
    try {
        const response = await fetch('api/mini-game/save-result.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                gameType,
                language: gameState.currentLanguage,
                difficulty: gameState.currentDifficulty,
                score,
                timeTaken: gameState.currentGameType === 'typing' ? 
                    (Date.now() - gameState.typingStartTime) / 1000 : null
            })
        });

        if (!response.ok) throw new Error('Failed to save result');
        
        // Refresh leaderboard and user results
        if (document.getElementById('leaderboard')) {
            loadLeaderboard();
            loadUserResults();
        }
    } catch (error) {
        console.error('Error saving result:', error);
    }
}

// Load leaderboard
async function loadLeaderboard() {
    try {
        const response = await fetch('api/mini-game/leaderboard.php');
        if (!response.ok) throw new Error('Failed to load leaderboard');
        
        const data = await response.json();
        const leaderboardHtml = data.map((entry, index) => `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="badge bg-primary me-2">#${index + 1}</span>
                    ${entry.username}
                </div>
                <div>
                    <span class="badge bg-secondary">${entry.score} ${entry.gameType === 'typing' ? 'WPM' : 'points'}</span>
                </div>
            </div>
        `).join('');
        
        document.getElementById('leaderboard').innerHTML = leaderboardHtml;
    } catch (error) {
        console.error('Error loading leaderboard:', error);
    }
}

// Load user results
async function loadUserResults() {
    try {
        const response = await fetch('api/mini-game/user-results.php');
        if (!response.ok) throw new Error('Failed to load user results');
        
        const data = await response.json();
        const resultsHtml = data.map(entry => `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="badge bg-info me-2">${entry.gameType}</span>
                    ${entry.language}
                </div>
                <div>
                    <span class="badge bg-secondary">${entry.score} ${entry.gameType === 'typing' ? 'WPM' : 'points'}</span>
                </div>
            </div>
        `).join('');
        
        document.getElementById('userResults').innerHTML = resultsHtml;
    } catch (error) {
        console.error('Error loading user results:', error);
    }
}