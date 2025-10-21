-- Activity Logging System for CodeGaming
-- Author: CodeGaming Team
-- Last Updated: October 21, 2025

-- Table for tracking all user/admin activities
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    admin_id INT NULL,
    username VARCHAR(100) NOT NULL,
    user_type ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    action VARCHAR(100) NOT NULL,
    action_details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    status ENUM('success', 'failed', 'pending') NOT NULL DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for system notifications
CREATE TABLE IF NOT EXISTS system_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('info', 'warning', 'error', 'success') NOT NULL DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-info-circle',
    is_read BOOLEAN DEFAULT FALSE,
    related_user_id INT NULL,
    related_admin_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO activity_log (user_id, username, user_type, action, action_details, ip_address, status, created_at) VALUES
(10, 'Army10', 'user', 'logged_in', 'Logged in from ::1', '::1', 'success', NOW() - INTERVAL 2 HOUR),
(11, 'Areysssssssssssssss', 'user', 'logged_in', 'Logged in from ::1', '::1', 'success', NOW() - INTERVAL 5 HOUR),
(12, 'Areys2990', 'user', 'logged_out', 'Logged out', '::1', 'success', NOW() - INTERVAL 1 DAY),
(NULL, 'unknown', 'user', 'login_failed', 'Failed login attempt', '192.168.1.100', 'failed', NOW() - INTERVAL 3 HOUR),
(NULL, 'unknown', 'user', 'login_failed', 'Failed login attempt', '192.168.1.100', 'failed', NOW() - INTERVAL 3 HOUR),
(NULL, 'unknown', 'user', 'login_failed', 'Failed login attempt', '192.168.1.100', 'failed', NOW() - INTERVAL 3 HOUR);

INSERT INTO system_notifications (type, title, message, icon, created_at) VALUES
('error', 'Failed Login Attempts', '3 failed login attempts detected from IP 192.168.1.100', 'fa-exclamation-triangle', NOW() - INTERVAL 3 HOUR),
('info', 'New User Registration', 'New user registered: coder123', 'fa-user-plus', NOW() - INTERVAL 1 HOUR),
('success', 'System Backup', 'System backup completed successfully', 'fa-check-circle', NOW() - INTERVAL 30 MINUTE),
('warning', 'High Server Load', 'Server CPU usage is above 80%', 'fa-server', NOW() - INTERVAL 15 MINUTE);
