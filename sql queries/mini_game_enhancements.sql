-- Mini-Game Page Enhancements
-- Database schema for dynamic game modes and user preferences

-- Create mini_game_modes table for dynamic mode management
CREATE TABLE IF NOT EXISTS mini_game_modes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mode_key VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    instructions JSON NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-code',
    difficulty_levels JSON NOT NULL,
    supported_languages JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default game modes
INSERT INTO mini_game_modes (mode_key, name, description, instructions, icon, difficulty_levels, supported_languages) VALUES
('guess', 'Guess the Output', 'Test your code comprehension by predicting what code snippets will output. Perfect for understanding language syntax and behavior!', 
 '["1. A code snippet will be displayed", "2. Analyze the code carefully", "3. Type what you think the output will be", "4. Submit your answer to see if you\'re correct", "5. Learn from explanations for each answer"]',
 'fas fa-search', 
 '["beginner", "intermediate", "expert"]',
 '["javascript", "python", "java", "cpp", "html", "css", "bootstrap"]'),

('typing', 'Fast Code Typing', 'Improve your coding speed and accuracy by typing code snippets as fast as possible. Great for muscle memory and syntax familiarity!',
 '["1. A code snippet will appear on screen", "2. Click \'Start Challenge\' to begin", "3. Type the code exactly as shown", "4. Complete before time runs out", "5. Achieve high WPM (Words Per Minute) scores"]',
 'fas fa-keyboard',
 '["beginner", "intermediate", "expert"]', 
 '["javascript", "python", "java", "cpp", "html", "css", "bootstrap"]');

-- Create user_preferences table for welcome modal settings
CREATE TABLE IF NOT EXISTS user_mini_game_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    show_welcome_modal BOOLEAN DEFAULT TRUE,
    preferred_language VARCHAR(50) DEFAULT 'javascript',
    preferred_difficulty VARCHAR(20) DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add indexes for better performance
CREATE INDEX idx_mini_game_modes_active ON mini_game_modes(is_active);
CREATE INDEX idx_user_preferences_user_id ON user_mini_game_preferences(user_id);
