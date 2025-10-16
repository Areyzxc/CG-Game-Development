-- =================================================================
-- Filename: add_first_visit_tracking.sql
-- Description: Adds first_visit tracking to users table for welcome modal
-- This script adds the first_visit boolean field to track if a user
-- has seen the welcome modal on their first visit.
-- Last Updated: [September 27, 2025]
-- =================================================================

USE coding_game;

-- Add first_visit column to users table
ALTER TABLE users 
ADD COLUMN first_visit BOOLEAN DEFAULT TRUE 
COMMENT 'Tracks if user has seen the welcome modal on first visit';

-- Add first_visit column to admin_users table  
ALTER TABLE admin_users 
ADD COLUMN first_visit BOOLEAN DEFAULT TRUE 
COMMENT 'Tracks if admin has seen the welcome modal on first visit';

-- Create index for better performance on first_visit queries
CREATE INDEX idx_users_first_visit ON users(first_visit);
CREATE INDEX idx_admin_users_first_visit ON admin_users(first_visit);

-- Add welcome_dont_show column to track users who don't want to see the modal again
ALTER TABLE users 
ADD COLUMN welcome_dont_show BOOLEAN DEFAULT FALSE 
COMMENT 'User preference to not show welcome modal again';

ALTER TABLE admin_users 
ADD COLUMN welcome_dont_show BOOLEAN DEFAULT FALSE 
COMMENT 'Admin preference to not show welcome modal again';

-- Create table for tracking welcome modal interactions for personalization
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

-- Optional: Update existing users to have first_visit = FALSE 
-- (so they don't see the modal unless they want to)
-- Uncomment the lines below if you want existing users to not see the modal:
-- UPDATE users SET first_visit = FALSE WHERE created_at < NOW();
-- UPDATE admin_users SET first_visit = FALSE WHERE created_at < NOW();
