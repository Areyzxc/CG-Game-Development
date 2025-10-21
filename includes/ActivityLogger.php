<?php
/**
 * File: ActivityLogger.php
 * Purpose: Centralized activity logging system for CodeGaming
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

class ActivityLogger {
    private static $instance = null;
    private $db;

    private function __construct() {
        $this->db = Database::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Log user/admin activity
     */
    public function logActivity($data) {
        $userId = $data['user_id'] ?? null;
        $adminId = $data['admin_id'] ?? null;
        $username = $data['username'] ?? 'Unknown';
        $userType = $data['user_type'] ?? 'user';
        $action = $data['action'] ?? 'unknown';
        $actionDetails = $data['action_details'] ?? null;
        $status = $data['status'] ?? 'success';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_log 
                (user_id, admin_id, username, user_type, action, action_details, ip_address, user_agent, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $userId,
                $adminId,
                $username,
                $userType,
                $action,
                $actionDetails,
                $ipAddress,
                $userAgent,
                $status
            ]);
        } catch (PDOException $e) {
            error_log("Activity logging failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create system notification
     */
    public function createNotification($data) {
        $type = $data['type'] ?? 'info';
        $title = $data['title'] ?? 'System Notification';
        $message = $data['message'] ?? '';
        $icon = $data['icon'] ?? 'fa-info-circle';
        $relatedUserId = $data['related_user_id'] ?? null;
        $relatedAdminId = $data['related_admin_id'] ?? null;

        try {
            $stmt = $this->db->prepare("
                INSERT INTO system_notifications 
                (type, title, message, icon, related_user_id, related_admin_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $type,
                $title,
                $message,
                $icon,
                $relatedUserId,
                $relatedAdminId
            ]);
        } catch (PDOException $e) {
            error_log("Notification creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM activity_log 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch activities: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get system notifications
     */
    public function getNotifications($limit = 10, $unreadOnly = false) {
        try {
            $sql = "SELECT * FROM system_notifications ";
            if ($unreadOnly) {
                $sql .= "WHERE is_read = FALSE ";
            }
            $sql .= "ORDER BY created_at DESC LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($notificationId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE system_notifications 
                SET is_read = TRUE, read_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([$notificationId]);
        } catch (PDOException $e) {
            error_log("Failed to mark notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get failed login attempts count
     */
    public function getFailedLoginAttempts($hours = 24) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM activity_log 
                WHERE action = 'login_failed' 
                AND created_at >= NOW() - INTERVAL ? HOUR
            ");
            $stmt->execute([$hours]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Failed to get failed login attempts: " . $e->getMessage());
            return 0;
        }
    }
}
