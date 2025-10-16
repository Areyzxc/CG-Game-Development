<?php
/**
 * Visitor Tracker Class
 * 
 * Tracks and manages website visitor statistics
 * 
 * @package CodeGaming
 * @subpackage Core
 * @version 1.0.0
 * @author CodeGaming Team
 */

require_once __DIR__ . '/Database.php';

class VisitorTracker {
    private $conn;
    private $ip;
    private $userAgent;
    private $pageUrl;
    private $referrer;
    private $sessionId;
    private $isNewVisitor = false;
    private $isNewSession = false;

    /**
     * Constructor - Initializes the visitor tracker
     */
    public function __construct() {
        // Don't start session here - let Auth handle session initialization
        // Session will be started by Auth::getInstance()
        
        // Initialize database connection
        $this->initDatabase();
        
        // Set visitor data
        $this->setVisitorData();
    }

    /**
     * Initialize database connection
     */
    private function initDatabase() {
        try {
            $db = Database::getInstance();
            $this->conn = $db->getConnection();
        } catch (Exception $e) {
            // Log error but don't stop execution
            error_log('Database connection failed in VisitorTracker: ' . $e->getMessage());
            $this->conn = null;
        }
    }

    /**
     * Set visitor data from server variables
     */
    private function setVisitorData() {
        $this->ip = $this->getClientIP();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $this->pageUrl = $_SERVER['REQUEST_URI'];
        $this->referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $this->sessionId = session_id();
        
        // Check if this is a new session
        if (!isset($_SESSION['visitor_tracked'])) {
            $this->isNewSession = true;
            $_SESSION['visitor_tracked'] = true;
        }
    }

    /**
     * Get client IP address
     */
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Check if visitor is a search engine bot
     */
    private function isBot() {
        $bots = array(
            'googlebot', 'bingbot', 'slurp', 'yahoo', 'msnbot', 'ask', 'facebook', 'twitter',
            'linkedin', 'pinterest', 'embedly', 'baiduspider', 'rogerbot', 'quora',
            'outbrain', 'slackbot', 'vkShare', 'W3C_Validator', 'whatsapp', 'telegrambot'
        );

        $userAgent = strtolower($this->userAgent);
        foreach ($bots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get device type from user agent
     */
    private function getDeviceType() {
        $userAgent = strtolower($this->userAgent);
        
        if (strpos($userAgent, 'mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'tablet') !== false) {
            return 'Tablet';
        } elseif (strpos($userAgent, 'laptop') !== false) {
            return 'Laptop';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Get browser from user agent
     */
    private function getBrowser() {
        $userAgent = $this->userAgent;
        
        if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident/') !== false) {
            return 'Internet Explorer';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Microsoft Edge';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR/') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'Brave') !== false) {
            return 'Brave';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Get operating system from user agent
     */
    private function getOS() {
        $userAgent = $this->userAgent;
        
        if (strpos($userAgent, 'Windows NT 10.0') !== false) return 'Windows 10';
        if (strpos($userAgent, 'Windows NT 11.0') !== false) return 'Windows 11';
        if (strpos($userAgent, 'Windows NT 6.3') !== false) return 'Windows 8.1';
        if (strpos($userAgent, 'Windows NT 6.2') !== false) return 'Windows 8';
        if (strpos($userAgent, 'Windows NT 6.1') !== false) return 'Windows 7';
        if (strpos($userAgent, 'Windows NT 6.0') !== false) return 'Windows Vista';
        if (strpos($userAgent, 'Windows NT 5.1') !== false) return 'Windows XP';
        if (strpos($userAgent, 'Windows NT 5') !== false) return 'Windows 2000';
        if (strpos($userAgent, 'Mac') !== false) return 'Mac OS X';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false || strpos($userAgent, 'iPhone') !== false) return 'iOS';
        
        return 'Unknown';
    }

    /**
     * Track the current visitor
     */
    public function track() {
        // Don't track bots or if database connection failed
        if ($this->isBot() || $this->conn === null) {
            return false;
        }

        try {
            // Check if this IP has visited in the last 30 minutes
            $stmt = $this->conn->prepare("
                SELECT id, visit_time 
                FROM visitor_tracking
                WHERE ip_address = :ip 
                AND visit_time > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
                ORDER BY visit_time DESC 
                LIMIT 1
            ");
            
            $stmt->bindParam(':ip', $this->ip, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            // If no recent visit from this IP, it's a new visitor
            if (empty($result)) {
                $this->isNewVisitor = true;
            }

            // Insert visit into database
            $this->insertVisit();
            
            // Update daily stats
            $this->updateDailyStats();
            
            return true;
        } catch (PDOException $e) {
            error_log('Error in VisitorTracker::track(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert visit into database
     */
    private function insertVisit() {
        try {
            $isUnique = $this->isNewVisitor ? 1 : 0;
            $deviceType = $this->getDeviceType();
            $browser = $this->getBrowser();
            $os = $this->getOS();
            $country = $this->getCountryFromIP($this->ip);
            
            $stmt = $this->conn->prepare("
                INSERT INTO visitor_tracking (
                    ip_address, user_agent, page_visited, referrer, 
                    session_id, is_unique, country, device_type, browser, os
                ) VALUES (
                    :ip_address, :user_agent, :page_visited, :referrer,
                    :session_id, :is_unique, :country, :device_type, :browser, :os
                )
            ");
            
            $stmt->execute([
                ':ip_address' => $this->ip,
                ':user_agent' => $this->userAgent,
                ':page_visited' => $this->pageUrl,
                ':referrer' => $this->referrer,
                ':session_id' => $this->sessionId,
                ':is_unique' => $isUnique,
                ':country' => $country,
                ':device_type' => $deviceType,
                ':browser' => $browser,
                ':os' => $os
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log('Error inserting visitor data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get country from IP using IP-API
     */
    private function getCountryFromIP($ip) {
        // Skip localhost and private IPs
        if ($ip === '127.0.0.1' || $ip === '::1' || substr($ip, 0, 8) === '192.168.') {
            return 'Local';
        }
        
        // Make API request to ip-api.com (free tier)
        $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country");
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['country'])) {
                return $data['country'];
            }
        }
        
        return 'Unknown';
    }

    /**
     * Update daily statistics
     */
    private function updateDailyStats() {
        $today = date('Y-m-d');
        
        // Try to update existing record
        $isNewVisitor = $this->isNewVisitor ? 1 : 0;
        
        // First try to update existing record
        $stmt = $this->conn->prepare("
            UPDATE visitor_stats 
            SET page_views = page_views + 1,
                unique_visits = unique_visits + :isNewVisitor,
                total_visits = total_visits + 1
            WHERE visit_date = :visitDate
        ");
        
        $stmt->execute([
            ':isNewVisitor' => $isNewVisitor,
            ':visitDate' => $today
        ]);
        
        // If no rows were updated, insert new record
        if ($stmt->rowCount() === 0) {
            $insertStmt = $this->conn->prepare("
                INSERT INTO visitor_stats (
                    visit_date, total_visits, unique_visits, page_views
                ) VALUES (:visitDate, 1, :isNewVisitor, 1)
                ON DUPLICATE KEY UPDATE 
                    total_visits = total_visits + 1,
                    unique_visits = unique_visits + VALUES(unique_visits),
                    page_views = page_views + 1
            ");
            $insertStmt->execute([
                ':visitDate' => $today,
                ':isNewVisitor' => $isNewVisitor
            ]);
        }
    }

    /**
     * Get visitor statistics
     */
    public function getStats($days = 30) {
        $stats = [
            'total_visits' => 0,
            'unique_visits' => 0,
            'page_views' => 0,
            'daily_stats' => [],
            'top_pages' => [],
            'referrers' => [],
            'browsers' => [],
            'os' => [],
            'devices' => []
        ];

        try {
            // Get overall stats
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_visits,
                    COUNT(DISTINCT ip_address) as unique_visits,
                    (SELECT COUNT(*) FROM visitor_tracking) as page_views
                FROM visitor_tracking
                WHERE visit_time > DATE_SUB(NOW(), INTERVAL :days DAY)
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['total_visits'] = (int)$row['total_visits'];
                $stats['unique_visits'] = (int)$row['unique_visits'];
                $stats['page_views'] = (int)$row['page_views'];
            }

            // Get daily stats
            $stmt = $this->conn->prepare("
                SELECT 
                    visit_date as date,
                    total_visits,
                    unique_visits,
                    page_views
                FROM visitor_stats
                WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                ORDER BY visit_date ASC
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
        
            // Process daily stats
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['daily_stats'][] = [
                    'date' => $row['date'],
                    'total_visits' => (int)$row['total_visits'],
                    'unique_visits' => (int)$row['unique_visits'],
                    'page_views' => (int)$row['page_views']
                ];
            }

            // Get top pages
            $stmt = $this->conn->prepare("
                SELECT 
                    page_visited as page,
                    COUNT(*) as visits
                FROM visitor_tracking
                WHERE visit_time > DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY page_visited
                ORDER BY visits DESC
                LIMIT 10
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['top_pages'][] = [
                    'page' => $row['page'],
                    'visits' => (int)$row['visits']
                ];
            }

            // Get referrers
            $stmt = $this->conn->prepare("
                SELECT 
                    COALESCE(NULLIF(referrer, ''), 'Direct') as referrer,
                    COUNT(*) as count
                FROM visitor_tracking
                WHERE visit_time > DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY COALESCE(NULLIF(referrer, ''), 'Direct')
                ORDER BY count DESC
                LIMIT 10
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['referrers'][] = [
                    'referrer' => $row['referrer'],
                    'count' => (int)$row['count']
                ];
            }

            // Get browser stats
            $stmt = $this->conn->prepare("
                SELECT 
                    browser,
                    COUNT(*) as count
                FROM visitor_tracking
                WHERE visit_time > DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY browser
                ORDER BY count DESC
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['browsers'][] = [
                    'browser' => $row['browser'],
                    'count' => (int)$row['count']
                ];
            }

            // Get OS stats
            $stmt = $this->conn->prepare("
                SELECT 
                    os,
                    COUNT(*) as count
                FROM visitor_tracking
                WHERE visit_time > DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY os
                ORDER BY count DESC
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['os'][] = [
                    'os' => $row['os'],
                    'count' => (int)$row['count']
                ];
            }

            // Get device stats
            $stmt = $this->conn->prepare("
                SELECT 
                    device_type,
                    COUNT(*) as count
                FROM visitor_tracking
                WHERE visit_time > DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY device_type
                ORDER BY count DESC
            ");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['devices'][] = [
                    'device' => $row['device_type'],
                    'count' => (int)$row['count']
                ];
            }
        } catch (PDOException $e) {
            // Log the error
            error_log('Error in VisitorTracker::getStats(): ' . $e->getMessage());
        }

        return $stats;
    }
}
?>
