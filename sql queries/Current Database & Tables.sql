-- =================================================================
-- Filename: Current Database & Tables.sql
-- Description: The complete database schema for the Code Gaming application.
-- This script creates the database, all necessary tables, and defines their relationships.
-- It is designed to be the single source of truth for the database structure.
-- Last Updated: [07/22/25]
-- =================================================================

-- Create the database if it doesn't exist
DROP DATABASE IF EXISTS coding_game;
CREATE DATABASE coding_game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE coding_game;

-- =================================================================
-- Table Creation
-- =================================================================

-- (1) --
-- Core: User Accounts
-- Stores primary user information, including credentials and role.
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_seen TIMESTAMP NULL DEFAULT NULL,
    first_visit BOOLEAN DEFAULT TRUE,
    welcome_dont_show BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB;

-- (2) --
-- Core: Admin Accounts
-- Separate table for admin users for enhanced security and specific admin roles.
CREATE TABLE IF NOT EXISTS admin_users (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') NOT NULL DEFAULT 'admin',
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen TIMESTAMP NULL DEFAULT NULL,
    first_visit BOOLEAN DEFAULT TRUE,
    welcome_dont_show BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB;

-- (3) --
-- Core: Programming Languages
-- Stores the programming languages offered in the tutorials.
CREATE TABLE IF NOT EXISTS programming_languages (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(10) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (4) --
-- Logging: User Login History
-- Tracks login attempts and session information for users.
CREATE TABLE IF NOT EXISTS login_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    role VARCHAR(20) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    session_id VARCHAR(255),
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- (5) --
-- Logging: Anonymous Visitor Traffic
-- Tracks visits from non-logged-in users for analytics.
CREATE TABLE IF NOT EXISTS visitor_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visit_time (visit_time)
) ENGINE=InnoDB;

-- (5.1) --
-- Guest Sessions: Tracks guest quiz/game sessions and nicknames.
CREATE TABLE IF NOT EXISTS guest_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    nickname VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (6) --
-- System: Password Reset Tokens
-- Stores tokens for the "Forgot Password" functionality.
CREATE TABLE IF NOT EXISTS password_reset_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reset_token VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (reset_token)
) ENGINE=InnoDB;

-- (7) --
-- Content: Site-wide Announcements
-- Stores announcements created by admins to be displayed on the site.
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES admin_users(admin_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- (8) --
-- Content: Quiz Questions
-- Stores all questions for the quizzes, linked to a tutorial topic ID.
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    topic_id VARCHAR(50) NOT NULL,
    question TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'code') NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'expert') NOT NULL,
    points INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (9) --
-- Content: Quiz Answers
-- Stores the possible answers for each quiz question.
CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    answer TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    explanation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

-- (10) --
-- Content: Coding Challenges
-- Stores coding challenges, including starter code and test cases.
-- NOTE: Only challenges with difficulty = 'expert' will be available for play in the future.
CREATE TABLE IF NOT EXISTS code_challenges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    topic_id VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    starter_code TEXT,
    test_cases JSON,
    difficulty ENUM('beginner', 'intermediate', 'expert') NOT NULL DEFAULT 'expert',
    points INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (10.1) --
-- Content: Challenge Questions (Extended)
-- Stores challenge questions with varied types for the Expert Challenge mode.
CREATE TABLE IF NOT EXISTS challenge_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('fill_blank', 'output', 'case_study', 'code') NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    starter_code TEXT,
    expected_output TEXT,
    difficulty ENUM('expert') NOT NULL DEFAULT 'expert',
    points INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (10.2) --
-- Content: Challenge Answers
-- Stores answers for challenge questions.
CREATE TABLE IF NOT EXISTS challenge_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    answer_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT TRUE,
    explanation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES challenge_questions(id) ON DELETE CASCADE
);

-- (10.3) --
-- Content: Challenge Test Cases
-- Stores test cases for code-based challenges.
CREATE TABLE IF NOT EXISTS challenge_test_cases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    input TEXT,
    expected_output TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES challenge_questions(id) ON DELETE CASCADE
);

-- (10.4) --
-- Progress: User Challenge Attempts
-- Records user submissions for challenge questions.
CREATE TABLE IF NOT EXISTS user_challenge_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    submitted_answer TEXT,
    is_correct BOOLEAN,
    points_earned INT DEFAULT 0,
    time_taken FLOAT,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES challenge_questions(id) ON DELETE CASCADE
);

-- (10.5) --
-- Progress: Guest Challenge Attempts
-- Records guest submissions for challenge questions.
CREATE TABLE IF NOT EXISTS guest_challenge_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guest_session_id INT NOT NULL,
    question_id INT NOT NULL,
    submitted_answer TEXT,
    is_correct BOOLEAN,
    points_earned INT DEFAULT 0,
    time_taken FLOAT,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guest_session_id) REFERENCES guest_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES challenge_questions(id) ON DELETE CASCADE
);

-- (10.6) --
-- Analytics: Challenge Leaderboard
-- Stores aggregated challenge scores for leaderboard display.
CREATE TABLE IF NOT EXISTS challenge_leaderboard (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    guest_session_id INT,
    nickname VARCHAR(100),
    total_score INT NOT NULL,
    total_time FLOAT,
    questions_attempted INT DEFAULT 0,
    questions_correct INT DEFAULT 0,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (guest_session_id) REFERENCES guest_sessions(id) ON DELETE CASCADE,
    INDEX idx_score (total_score DESC),
    INDEX idx_completed (completed_at DESC)
);

-- (11) --
-- Content: User Feedback Messages
-- Stores feedback messages submitted by users through the contact/feedback form.
CREATE TABLE IF NOT EXISTS feedback_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(100) NOT NULL,
    sender_email VARCHAR(100) NOT NULL,
    proponent_email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    likes INT DEFAULT 0
) ENGINE=InnoDB;

-- (12) --
-- Progress: User Tutorial Topic Progress
-- Tracks a user's progress for each specific tutorial topic.
-- Status: 'pending' (not started), 'currently_reading' (in progress), 'done_reading' (completed)
CREATE TABLE IF NOT EXISTS user_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    topic_id VARCHAR(50) NOT NULL,
    status ENUM('pending', 'currently_reading', 'done_reading') DEFAULT 'pending',
    progress INT DEFAULT 0,
    last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_topic (user_id, topic_id)
);

-- (13) --
-- Progress: User Tutorial Game Mode Progress
-- Tracks completion status for the introductory tutorials of game modes.
CREATE TABLE IF NOT EXISTS user_tutorial_modes_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    mode VARCHAR(50) NOT NULL, -- e.g., 'mini-game', 'quiz', 'challenge'
    status ENUM('pending', 'completed') DEFAULT 'pending',
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_mode (user_id, mode)
);

-- (14) --
-- Progress: User Quiz Attempts
-- Records each attempt a user makes on a quiz question.
CREATE TABLE IF NOT EXISTS user_quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer_id INT,
    is_correct BOOLEAN,
    points_earned INT DEFAULT 0,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_answer_id) REFERENCES quiz_answers(id) ON DELETE SET NULL
);

-- (14.1) --
-- Progress: Guest Quiz Attempts
-- Records each attempt a guest makes on a quiz question.
CREATE TABLE IF NOT EXISTS guest_quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guest_session_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer_id INT,
    is_correct BOOLEAN,
    points_earned INT DEFAULT 0,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guest_session_id) REFERENCES guest_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_answer_id) REFERENCES quiz_answers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- (15) --
-- Progress: User Code Submissions
-- Records user submissions for coding challenges.
CREATE TABLE IF NOT EXISTS user_code_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    code TEXT NOT NULL,
    status ENUM('pending', 'running', 'passed', 'failed') DEFAULT 'pending',
    test_results JSON,
    points_earned INT DEFAULT 0,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES code_challenges(id) ON DELETE CASCADE
);

-- (16) --
-- Progress: Mini-Game Scores
-- Stores scores and results from the various mini-games.
CREATE TABLE IF NOT EXISTS mini_game_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    game_type VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    time_taken FLOAT,
    details TEXT,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- (17) --
-- Tracking: Tutorial Page Visits
-- Tracks which tutorial topics users are viewing.
CREATE TABLE IF NOT EXISTS tutorial_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(255),  -- Session ID for visitors
    user_id INT NULL,         -- NULL for visitors, user ID for logged-in users
    language_id VARCHAR(50),
    topic_id VARCHAR(50) NULL,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duration INT DEFAULT 0,   -- Time spent in seconds
    is_completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (language_id) REFERENCES programming_languages(id) ON DELETE CASCADE,
    INDEX idx_visitor (visitor_id),
    INDEX idx_visit_time (visit_time)
) ENGINE=InnoDB;

-- (18) --
-- Tracking: User Achievements
-- Tracks achievements unlocked by users.
CREATE TABLE IF NOT EXISTS user_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    achievement_id VARCHAR(50) NOT NULL,
    awarded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_achievement (user_id, achievement_id)
) ENGINE=InnoDB; 

-- (19) --
-- Tracking: Admin Actions Log
-- Tracks actions taken by admins.
CREATE TABLE IF NOT EXISTS admin_actions_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NULL,
  action_type VARCHAR(50) NOT NULL,
  target_type ENUM('user', 'admin') NOT NULL,
  target_id INT NOT NULL,
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES admin_users(admin_id) ON DELETE SET NULL
); 

-- (20) --
-- Tracking: Welcome Modal Interactions
-- Tracks interactions with the welcome modal for personalization.
CREATE TABLE IF NOT EXISTS user_welcome_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    section_clicked VARCHAR(50) NOT NULL,
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    click_count INT DEFAULT 1,
    is_admin BOOLEAN DEFAULT FALSE,
    action_type VARCHAR(20) DEFAULT 'click',
    INDEX idx_user_section (user_id, section_clicked),
    INDEX idx_clicked_at (clicked_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) COMMENT 'Tracks user interactions with welcome modal for personalization';

-- (21) --
-- Tracking: User Feedback Likes
-- Tracks likes on user feedback messages.
CREATE TABLE IF NOT EXISTS user_feedback_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    feedback_id INT NOT NULL,
    liked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (feedback_id) REFERENCES feedback_messages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_feedback_like (user_id, feedback_id)
) ENGINE=InnoDB;

-- (22) --
-- Team Members Table
-- Stores detailed information about team members for the About page
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    age INT,
    email VARCHAR(100),
    code VARCHAR(20) UNIQUE,
    photo VARCHAR(255) DEFAULT 'images/background.png',
    bio TEXT,
    fun_fact TEXT,
    mission_statement TEXT,
    facebook_url VARCHAR(255),
    instagram_url VARCHAR(255),
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (23) --
-- Timeline Events Table
-- Stores project development milestones for the timeline section
CREATE TABLE IF NOT EXISTS timeline_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    image_url VARCHAR(255),
    icon VARCHAR(50) DEFAULT 'fas fa-calendar',
    category ENUM('milestone', 'development', 'testing', 'launch', 'update') DEFAULT 'milestone',
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (24) --
-- Coding Playlist Table
-- Stores music tracks for the coding playlist section
CREATE TABLE IF NOT EXISTS coding_playlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255),
    file_path VARCHAR(255),
    external_url VARCHAR(255),
    duration INT, -- in seconds
    genre VARCHAR(50),
    is_featured BOOLEAN DEFAULT FALSE,
    play_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (25) --
-- FAQ Items Table
-- Stores frequently asked questions with search functionality
CREATE TABLE IF NOT EXISTS faq_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category ENUM('project', 'technology', 'team', 'general') DEFAULT 'general',
    tags VARCHAR(255), -- comma-separated for search
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (26) --
-- Project Statistics Table
-- Stores key project metrics for the impact section
CREATE TABLE IF NOT EXISTS project_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_name VARCHAR(100) NOT NULL UNIQUE,
    stat_value INT NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-chart-line',
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- (27) --
-- Mini-Game Modes Table
-- Stores different game modes for the mini-game section
CREATE TABLE IF NOT EXISTS mini_game_modes (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
) ENGINE=InnoDB;

-- (28) --
-- Mini-Game Preferences Table
-- Stores user preferences for the mini-game section
CREATE TABLE IF NOT EXISTS user_mini_game_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    show_welcome_modal BOOLEAN DEFAULT TRUE,
    preferred_language VARCHAR(50) DEFAULT 'javascript',
    preferred_difficulty VARCHAR(20) DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Add indexes for better performance
CREATE INDEX idx_mini_game_modes_active ON mini_game_modes(is_active);
CREATE INDEX idx_user_preferences_user_id ON user_mini_game_preferences(user_id);

-- (29) --
-- Visitor Tracking Table
-- Tracks visitor activity on the website
CREATE TABLE IF NOT EXISTS `visitor_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `page_visited` varchar(255) NOT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `visit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_unique` tinyint(1) DEFAULT '0',
  `session_id` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `session_id` (`session_id`),
  KEY `visit_time` (`visit_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- (30) --
-- Visitor Stats Table
-- Stores aggregated visitor data for analytics
CREATE TABLE IF NOT EXISTS `visitor_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_date` date NOT NULL,
  `total_visits` int(11) NOT NULL DEFAULT '0',
  `unique_visits` int(11) NOT NULL DEFAULT '0',
  `page_views` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_date` (`visit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


