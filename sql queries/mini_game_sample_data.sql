-- Sample data for mini-game modes
-- Insert default game modes with proper JSON formatting

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

-- Insert some sample results for testing leaderboard
INSERT INTO mini_game_results (user_id, game_type, score, time_taken, details) VALUES
(1, 'guess', 850, NULL, '{"language": "javascript", "difficulty": "intermediate", "correct_answers": 8, "total_questions": 10}'),
(1, 'typing', 65, 45.2, '{"language": "javascript", "difficulty": "beginner", "wpm": 65, "accuracy": 95}'),
(2, 'guess', 720, NULL, '{"language": "python", "difficulty": "beginner", "correct_answers": 7, "total_questions": 10}'),
(2, 'typing', 58, 52.1, '{"language": "python", "difficulty": "beginner", "wpm": 58, "accuracy": 92}');
