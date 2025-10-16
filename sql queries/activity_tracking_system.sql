-- ==========================================================
-- Activity Tracking System for Code Gaming
-- 
-- This script creates tables for tracking user activities
-- and system notifications in the admin dashboard.
--
-- Author: CodeGaming Team
-- Last Updated: October 3, 2025
-- ==========================================================

-- Table: user_activities
-- Tracks all user activities across the platform
CREATE TABLE IF NOT EXISTS user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    guest_session_id INT NULL,
    activity_type ENUM('quiz', 'challenge', 'tutorial', 'mini_game', 'feedback', 'login', 'signup') NOT NULL,
    action_type VARCHAR(50) NOT NULL, -- e.g., 'started', 'completed', 'failed', 'submitted'
    item_id INT NULL, -- References the specific quiz/challenge/tutorial ID
    item_name VARCHAR(255) NULL, -- Cached name for display
    difficulty ENUM('beginner', 'intermediate', 'expert') NULL,
    status ENUM('success', 'failed', 'ongoing') NOT NULL,
    points_earned INT DEFAULT 0,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    metadata JSON, -- Additional data like score, time taken, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_activity (user_id, activity_type, created_at),
    INDEX idx_guest_activity (guest_session_id, created_at),
    INDEX idx_activity_type (activity_type, created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (guest_session_id) REFERENCES guest_sessions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: system_notifications
-- System-wide notifications for administrators
CREATE TABLE IF NOT EXISTS system_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_type ENUM('security', 'user', 'system', 'maintenance') NOT NULL,
    severity ENUM('info', 'warning', 'danger', 'success') NOT NULL DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    source_module VARCHAR(50) NULL, -- e.g., 'auth', 'quiz', 'tutorial'
    related_id INT NULL, -- Reference to related entity (user_id, quiz_id, etc.)
    ip_address VARCHAR(45) NULL,
    user_agent TEXT,
    metadata JSON, -- Additional context data
    expires_at TIMESTAMP NULL, -- When the notification should be automatically removed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_notification_type (notification_type, created_at),
    INDEX idx_notification_severity (severity, created_at),
    INDEX idx_notification_read (is_read, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: user_notification_preferences
-- Stores notification preferences for users
CREATE TABLE IF NOT EXISTS user_notification_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email_notifications BOOLEAN DEFAULT TRUE,
    push_notifications BOOLEAN DEFAULT TRUE,
    activity_digest_frequency ENUM('never', 'daily', 'weekly') DEFAULT 'daily',
    notify_on_mentions BOOLEAN DEFAULT TRUE,
    notify_on_replies BOOLEAN DEFAULT TRUE,
    notify_on_achievements BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_preferences (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notification_deliveries
-- Tracks delivery status of notifications
CREATE TABLE IF NOT EXISTS notification_deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    user_id INT NOT NULL,
    delivery_method ENUM('in_app', 'email', 'push') NOT NULL DEFAULT 'in_app',
    status ENUM('pending', 'sent', 'delivered', 'read', 'failed') NOT NULL DEFAULT 'pending',
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES system_notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_delivery_status (status, delivery_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- Sample Data for Testing
-- ==========================================================

-- Sample activity types and their display templates
INSERT IGNORE INTO system_settings (setting_key, setting_value, description) VALUES
('activity_templates', '{
    "quiz_completed": "{user} completed {difficulty} quiz: {item_name} - {status}",
    "challenge_attempted": "{user} attempted {difficulty} challenge: {item_name} - {status}",
    "tutorial_completed": "{user} completed tutorial: {item_name}",
    "feedback_submitted": "{user} submitted feedback",
    "user_registered": "New user registered: {user}",
    "login_successful": "{user} logged in successfully",
    "login_failed": "Failed login attempt for user: {user}"
}', 'Templates for displaying user activities'),
('notification_defaults', '{
    "security_alert": {
        "title": "Security Alert",
        "message": "{details}",
        "severity": "danger"
    },
    "new_user": {
        "title": "New User Registration",
        "message": "New user registered: {username} ({email})",
        "severity": "info"
    },
    "system_update": {
        "title": "System Update",
        "message": "{details}",
        "severity": "info"
    }
}', 'Default notification templates');

-- ==========================================================
-- Stored Procedures
-- ==========================================================

-- Procedure: Log User Activity
DELIMITER //
CREATE PROCEDURE log_user_activity(
    IN p_user_id INT,
    IN p_guest_session_id INT,
    IN p_activity_type VARCHAR(50),
    IN p_action_type VARCHAR(50),
    IN p_item_id INT,
    IN p_item_name VARCHAR(255),
    IN p_difficulty VARCHAR(20),
    IN p_status VARCHAR(20),
    IN p_points_earned INT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT,
    IN p_metadata JSON
)
BEGIN
    INSERT INTO user_activities (
        user_id,
        guest_session_id,
        activity_type,
        action_type,
        item_id,
        item_name,
        difficulty,
        status,
        points_earned,
        ip_address,
        user_agent,
        metadata,
        created_at
    ) VALUES (
        NULLIF(p_user_id, 0),
        NULLIF(p_guest_session_id, 0),
        p_activity_type,
        p_action_type,
        NULLIF(p_item_id, 0),
        p_item_name,
        p_difficulty,
        p_status,
        p_points_earned,
        p_ip_address,
        p_user_agent,
        p_metadata,
        NOW()
    );
END //

-- Procedure: Create System Notification
CREATE PROCEDURE create_system_notification(
    IN p_type VARCHAR(20),
    IN p_severity VARCHAR(10),
    IN p_title VARCHAR(255),
    IN p_message TEXT,
    IN p_source_module VARCHAR(50),
    IN p_related_id INT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT,
    IN p_metadata JSON,
    IN p_expires_in_hours INT
)
BEGIN
    DECLARE v_expires_at TIMESTAMP;
    
    IF p_expires_in_hours > 0 THEN
        SET v_expires_at = TIMESTAMPADD(HOUR, p_expires_in_hours, NOW());
    ELSE
        SET v_expires_at = NULL;
    END IF;
    
    INSERT INTO system_notifications (
        notification_type,
        severity,
        title,
        message,
        source_module,
        related_id,
        ip_address,
        user_agent,
        metadata,
        expires_at,
        created_at
    ) VALUES (
        p_type,
        p_severity,
        p_title,
        p_message,
        p_source_module,
        NULLIF(p_related_id, 0),
        p_ip_address,
        p_user_agent,
        p_metadata,
        v_expires_at,
        NOW()
    );
    
    -- Return the ID of the created notification
    SELECT LAST_INSERT_ID() AS notification_id;
END //

DELIMITER ;

-- ==========================================================
-- Triggers
-- ==========================================================

-- Trigger: Log new user registration
DELIMITER //
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    -- Log the activity
    CALL log_user_activity(
        NEW.id,                  -- user_id
        NULL,                    -- guest_session_id
        'signup',                -- activity_type
        'registered',            -- action_type
        NULL,                    -- item_id
        NULL,                    -- item_name
        NULL,                    -- difficulty
        'success',               -- status
        0,                       -- points_earned
        NEW.registration_ip,     -- ip_address
        NEW.user_agent,          -- user_agent
        NULL                     -- metadata
    );
    
    -- Create a system notification
    CALL create_system_notification(
        'user',                  -- type
        'info',                  -- severity
        'New User Registration', -- title
        CONCAT('New user registered: ', NEW.username, ' (', NEW.email, ')'), -- message
        'auth',                  -- source_module
        NEW.id,                  -- related_id
        NEW.registration_ip,     -- ip_address
        NEW.user_agent,          -- user_agent
        NULL,                    -- metadata
        24                       -- expires_in_hours
    );
END //

-- Trigger: Log failed login attempts
DELIMITER //
CREATE TRIGGER after_failed_login
AFTER INSERT ON login_attempts
FOR EACH ROW
BEGIN
    IF NOT NEW.success THEN
        -- Log the activity
        CALL log_user_activity(
            NEW.user_id,          -- user_id
            NULL,                 -- guest_session_id
            'login',              -- activity_type
            'failed_attempt',     -- action_type
            NULL,                 -- item_id
            NULL,                 -- item_name
            NULL,                 -- difficulty
            'failed',             -- status
            0,                    -- points_earned
            NEW.ip_address,       -- ip_address
            NEW.user_agent,       -- user_agent
            JSON_OBJECT('attempt_id', NEW.id) -- metadata
        );
        
        -- Check for multiple failed attempts
        SET @failed_attempts = (
            SELECT COUNT(*) 
            FROM login_attempts 
            WHERE user_id = NEW.user_id 
            AND success = 0 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        );
        
        -- Create a security notification if multiple failed attempts
        IF @failed_attempts >= 3 THEN
            CALL create_system_notification(
                'security',
                'warning',
                'Multiple Failed Login Attempts',
                CONCAT('User ID ', NEW.user_id, ' has had ', @failed_attempts, ' failed login attempts in the last 15 minutes.'),
                'auth',
                NEW.user_id,
                NEW.ip_address,
                NEW.user_agent,
                JSON_OBJECT('attempts', @failed_attempts, 'last_attempt', NEW.attempt_time),
                24
            );
        END IF;
    END IF;
END //

DELIMITER ;

-- ==========================================================
-- Views for Reporting
-- ==========================================================

-- View: Recent User Activities
CREATE OR REPLACE VIEW vw_recent_activities AS
SELECT 
    ua.id,
    COALESCE(u.username, gs.nickname, 'Guest') AS user_display_name,
    ua.activity_type,
    ua.action_type,
    ua.item_name,
    ua.difficulty,
    ua.status,
    ua.points_earned,
    ua.created_at,
    TIMESTAMPDIFF(SECOND, ua.created_at, NOW()) AS seconds_ago,
    CASE 
        WHEN TIMESTAMPDIFF(SECOND, ua.created_at, NOW()) < 60 
            THEN CONCAT(TIMESTAMPDIFF(SECOND, ua.created_at, NOW()), ' seconds ago')
        WHEN TIMESTAMPDIFF(MINUTE, ua.created_at, NOW()) < 60 
            THEN CONCAT(TIMESTAMPDIFF(MINUTE, ua.created_at, NOW()), ' minutes ago')
        WHEN TIMESTAMPDIFF(HOUR, ua.created_at, NOW()) < 24 
            THEN CONCAT(TIMESTAMPDIFF(HOUR, ua.created_at, NOW()), ' hours ago')
        ELSE CONCAT(TIMESTAMPDIFF(DAY, ua.created_at, NOW()), ' days ago')
    END AS time_ago,
    ua.metadata
FROM 
    user_activities ua
LEFT JOIN 
    users u ON ua.user_id = u.id
LEFT JOIN
    guest_sessions gs ON ua.guest_session_id = gs.id
ORDER BY 
    ua.created_at DESC
LIMIT 1000;

-- View: Unread Notifications
CREATE OR REPLACE VIEW vw_unread_notifications AS
SELECT 
    n.*,
    TIMESTAMPDIFF(MINUTE, n.created_at, NOW()) AS minutes_old,
    CASE 
        WHEN TIMESTAMPDIFF(SECOND, n.created_at, NOW()) < 60 
            THEN CONCAT(TIMESTAMPDIFF(SECOND, n.created_at, NOW()), ' seconds ago')
        WHEN TIMESTAMPDIFF(MINUTE, n.created_at, NOW()) < 60 
            THEN CONCAT(TIMESTAMPDIFF(MINUTE, n.created_at, NOW()), ' minutes ago')
        WHEN TIMESTAMPDIFF(HOUR, n.created_at, NOW()) < 24 
            THEN CONCAT(TIMESTAMPDIFF(HOUR, n.created_at, NOW()), ' hours ago')
        ELSE CONCAT(TIMESTAMPDIFF(DAY, n.created_at, NOW()), ' days ago')
    END AS time_ago
FROM 
    system_notifications n
WHERE 
    n.is_read = FALSE
    AND (n.expires_at IS NULL OR n.expires_at > NOW())
ORDER BY 
    n.created_at DESC;

-- ==========================================================
-- Indexes for Performance
-- ==========================================================

-- Add indexes for common query patterns
ALTER TABLE user_activities ADD INDEX idx_activity_created (created_at);
ALTER TABLE user_activities ADD INDEX idx_user_activity_type (user_id, activity_type, created_at);
ALTER TABLE system_notifications ADD INDEX idx_notification_created (created_at);
ALTER TABLE system_notifications ADD INDEX idx_notification_type_created (notification_type, created_at);

-- ==========================================================
-- Database Events for Maintenance
-- ==========================================================

-- Event: Clean up old activities and notifications
DELIMITER //
CREATE EVENT IF NOT EXISTS cleanup_old_activities
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    -- Delete activities older than 90 days
    DELETE FROM user_activities 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
    
    -- Delete read notifications older than 30 days
    DELETE FROM system_notifications 
    WHERE is_read = TRUE 
    AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- Delete expired notifications
    DELETE FROM system_notifications 
    WHERE expires_at IS NOT NULL 
    AND expires_at < NOW();
END //

DELIMITER ;

-- ==========================================================
-- Example Queries
-- ==========================================================

-- Get recent activities for the admin dashboard
-- SELECT * FROM vw_recent_activities LIMIT 10;

-- Get unread notifications
-- SELECT * FROM vw_unnotifications WHERE is_read = FALSE ORDER BY created_at DESC;

-- Get user activity summary
-- SELECT 
--     user_id,
--     COUNT(*) as total_activities,
--     SUM(CASE WHEN activity_type = 'quiz' THEN 1 ELSE 0 END) as quiz_count,
--     SUM(CASE WHEN activity_type = 'challenge' THEN 1 ELSE 0 END) as challenge_count,
--     MAX(created_at) as last_activity
-- FROM user_activities 
-- WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
-- GROUP BY user_id
-- ORDER BY total_activities DESC;
