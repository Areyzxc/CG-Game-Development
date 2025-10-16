-- =================================================================
-- Filename: 2025_10_06_add_profile_fields.sql
-- Description: Adds new profile-related fields to the users table
-- to support the enhanced profile page.
-- =================================================================

-- Add new columns to users table
ALTER TABLE `users`
ADD COLUMN `bio` TEXT NULL DEFAULT NULL AFTER `profile_picture`,
ADD COLUMN `location` VARCHAR(100) NULL DEFAULT NULL AFTER `bio`,
ADD COLUMN `title` VARCHAR(100) NULL DEFAULT 'Code Enthusiast' AFTER `location`,
ADD COLUMN `social_instagram` VARCHAR(100) NULL DEFAULT NULL AFTER `title`,
ADD COLUMN `social_facebook` VARCHAR(100) NULL DEFAULT NULL AFTER `social_instagram`,
ADD COLUMN `social_twitter` VARCHAR(100) NULL DEFAULT NULL AFTER `social_facebook`,
ADD COLUMN `social_pinterest` VARCHAR(100) NULL DEFAULT NULL AFTER `social_twitter`,
ADD COLUMN `email_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `social_pinterest`,
ADD COLUMN `profile_views` INT NOT NULL DEFAULT 0 AFTER `email_verified`,
ADD COLUMN `preferences` JSON NULL DEFAULT NULL COMMENT 'User preferences in JSON format' AFTER `profile_views`;


ALTER TABLE `users`
ADD COLUMN `bio` TEXT NULL DEFAULT NULL AFTER `profile_picture`,
ADD COLUMN `location` VARCHAR(100) NULL DEFAULT NULL AFTER `bio`,
ADD COLUMN `title` VARCHAR(100) NULL DEFAULT 'Code Enthusiast' AFTER `location`,
ADD COLUMN `social_instagram` VARCHAR(100) NULL DEFAULT NULL AFTER `title`,
ADD COLUMN `social_facebook` VARCHAR(100) NULL DEFAULT NULL AFTER `social_instagram`,
ADD COLUMN `social_twitter` VARCHAR(100) NULL DEFAULT NULL AFTER `social_facebook`,
ADD COLUMN `social_pinterest` VARCHAR(100) NULL DEFAULT NULL AFTER `social_twitter`,
ADD COLUMN `email_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `social_pinterest`,
ADD COLUMN `profile_views` INT NOT NULL DEFAULT 0 AFTER `email_verified`,
ADD COLUMN `last_activity` TIMESTAMP NULL DEFAULT NULL AFTER `profile_views`,
ADD COLUMN `preferences` JSON NULL DEFAULT NULL COMMENT 'User preferences in JSON format' AFTER `last_activity`;

-- Add index for better performance on commonly queried fields
CREATE INDEX `idx_user_activity` ON `users` (`last_activity`);
CREATE INDEX `idx_user_views` ON `users` (`profile_views`);

-- Create table for user activity logs
CREATE TABLE IF NOT EXISTS `user_activities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `activity_type` VARCHAR(50) NOT NULL COMMENT 'e.g., login, profile_view, quiz_complete, etc.',
    `activity_details` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_activity_user` (`user_id`, `activity_type`),
    INDEX `idx_activity_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for user stats
CREATE TABLE IF NOT EXISTS `user_stats` (
    `user_id` INT PRIMARY KEY,
    `points` INT NOT NULL DEFAULT 0,
    `rank` INT NULL,
    `challenges_completed` INT NOT NULL DEFAULT 0,
    `quizzes_passed` INT NOT NULL DEFAULT 0,
    `total_learning_time` INT NOT NULL DEFAULT 0 COMMENT 'in minutes',
    `last_week_progress` TINYINT NOT NULL DEFAULT 0,
    `last_month_progress` TINYINT NOT NULL DEFAULT 0,
    `overall_progress` TINYINT NOT NULL DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for user badges/achievements
CREATE TABLE IF NOT EXISTS `user_badges` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `badge_type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(100) NULL,
    `awarded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_badges` (`user_id`, `badge_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for user connections/followers
CREATE TABLE IF NOT EXISTS `user_connections` (
    `follower_id` INT NOT NULL,
    `following_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`follower_id`, `following_id`),
    FOREIGN KEY (`follower_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`following_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_connections` (`follower_id`, `following_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add default preferences for existing users
UPDATE `users` SET 
    `preferences` = '{
        "email_notifications": true,
        "dark_mode": false,
        "show_online_status": true,
        "activity_privacy": "public",
        "language": "en",
        "timezone": "UTC"
    }';

-- Create a trigger to automatically create a stats record when a new user is created
DELIMITER //
CREATE TRIGGER after_user_insert
AFTER INSERT ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO `user_stats` (`user_id`) VALUES (NEW.id);
END //
DELIMITER ;

-- Create a procedure to update user's last activity
DELIMITER //
CREATE PROCEDURE update_user_activity(IN p_user_id INT, IN p_activity_type VARCHAR(50), IN p_ip_address VARCHAR(45), IN p_user_agent TEXT)
BEGIN
    -- Update last_activity timestamp
    UPDATE `users` 
    SET `last_activity` = CURRENT_TIMESTAMP,
        `last_seen` = IF(p_activity_type = 'login', CURRENT_TIMESTAMP, `last_seen`)
    WHERE `id` = p_user_id;
    
    -- Log the activity
    INSERT INTO `user_activities` (`user_id`, `activity_type`, `ip_address`, `user_agent`)
    VALUES (p_user_id, p_activity_type, p_ip_address, p_user_agent);
    
    -- Update profile views if it's a profile view
    IF p_activity_type = 'profile_view' THEN
        UPDATE `users` 
        SET `profile_views` = `profile_views` + 1 
        WHERE `id` = p_user_id;
    END IF;
END //
DELIMITER ;
