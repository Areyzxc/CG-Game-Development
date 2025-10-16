-- Add user_mini_game_attempts table to track user attempts in mini-games
-- This table will store each attempt a user makes in any mini-game mode

CREATE TABLE IF NOT EXISTS user_mini_game_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    mode_key VARCHAR(50) NOT NULL,
    difficulty_level VARCHAR(20) NOT NULL,
    language_used VARCHAR(50) NOT NULL,
    score INT DEFAULT 0,
    time_spent_seconds INT DEFAULT 0,
    is_correct BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_mini_game_attempts_user (user_id),
    INDEX idx_user_mini_game_attempts_mode (mode_key),
    INDEX idx_user_mini_game_attempts_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add a comment to describe the table
ALTER TABLE user_mini_game_attempts COMMENT 'Tracks user attempts and performance in mini-games';

-- Add foreign key to reference mini_game_modes table if it exists
-- This is a conditional addition in case the mini_game_modes table exists
SET @dbname = DATABASE();
SET @tablename = "mini_game_modes";
SET @column_name = "mode_key";
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = @dbname 
        AND TABLE_NAME = @tablename
    ) > 0,
    CONCAT(
        'ALTER TABLE user_mini_game_attempts ADD CONSTRAINT fk_mini_game_mode ',
        'FOREIGN KEY (mode_key) REFERENCES mini_game_modes(mode_key) ON DELETE CASCADE'
    ),
    'SELECT 1'
));

PREPARE alterTable FROM @preparedStatement;
EXECUTE alterTable;
DEALLOCATE PREPARE alterTable;
