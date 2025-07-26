<?php
/**
 * File: includes/Database.php
 * 
 * Purpose: Provides a singleton database connection for CodeGaming, handling all database operations.
 * 
 * Features:
 *  - Implements singleton pattern for a single database connection instance.
 *  - Provides methods for user authentication, session management, and CRUD operations.
 *  - Includes error handling and logging for database operations.
 *  - Supports prepared statements for secure database queries.
 * Usage:
 *  - Used across various API endpoints and includes for database access.
 *  - Provides helper functions for input sanitization and error logging.
 *  - Supports user and admin operations, including login, registration, and progress tracking.
 * Included Files/Dependencies:
 *  - PDO for database interactions.
 *  - Requires PHP 7.4+ for type declarations and error handling.
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
class Database {
    private static $instance = null;
    private $connection;
    private $host = 'localhost';
    private $port = '3327';
    private $username = 'root';
    private $password = '';
    private $database = 'coding_game';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            $errorMessage = sprintf(
                "Database connection failed: [%s] %s (DSN: %s, User: %s)",
                $e->getCode(),
                $e->getMessage(),
                $dsn,
                $this->username
            );
            error_log($errorMessage);
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Helper functions
    public function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public function logError($message, $context = []) {
        $logMessage = date('Y-m-d H:i:s') . " - " . $message;
        if (!empty($context)) {
            $logMessage .= " - Context: " . json_encode($context);
        }
        error_log($logMessage);
    }

    // User operations
    public function createUser($username, $email, $passwordHash) {
        try {
            $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$username, $email, $passwordHash]);
        } catch (PDOException $e) {
            $this->logError("User creation failed", ['username' => $username, 'email' => $email, 'error' => $e->getMessage()]);
            throw new Exception('Failed to create user account.');
        }
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function updateUserLastActivity($userId) {
        // Note: last_activity column doesn't exist in the current schema
        // This function is kept for compatibility but doesn't perform any action
        return true;
    }

    // Admin operations
    public function getAdminByEmail($email) {
        $sql = "SELECT * FROM admin_users WHERE email = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getAdminById($adminId) {
        $sql = "SELECT * FROM admin_users WHERE admin_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$adminId]);
        return $stmt->fetch();
    }

    // Session and login logging
    public function logLogin($userId, $role, $ipAddress, $sessionId = null) {
        try {
            $sql = "INSERT INTO login_logs (user_id, role, ip_address, session_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$userId, $role, $ipAddress, $sessionId]);
        } catch (PDOException $e) {
            $this->logError("Login logging failed", ['userId' => $userId, 'role' => $role, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function logVisitor($ipAddress, $userAgent) {
        try {
            $sql = "INSERT INTO visitor_logs (ip_address, user_agent) VALUES (?, ?)";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$ipAddress, $userAgent]);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logError("Visitor logging failed", ['ip' => $ipAddress, 'error' => $e->getMessage()]);
            return false;
        }
    }

    // Password reset operations
    public function createPasswordResetRequest($userId, $token, $expiresAt) {
        try {
            $sql = "INSERT INTO password_reset_requests (user_id, reset_token, expires_at) VALUES (?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$userId, $token, $expiresAt]);
        } catch (PDOException $e) {
            $this->logError("Password reset request failed", ['userId' => $userId, 'error' => $e->getMessage()]);
            throw new Exception('Failed to process password reset request.');
        }
    }

    public function validateResetToken($token) {
        $sql = "SELECT pr.*, u.email 
                FROM password_reset_requests pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.reset_token = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    // Statistics and reporting
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->connection->query($sql);
        return $stmt->fetch()['total'];
    }

    public function getActiveUsers() {
        $sql = "SELECT COUNT(DISTINCT user_id) as total FROM login_logs WHERE login_time > DATE_SUB(NOW(), INTERVAL 5 MINUTE) AND user_id IS NOT NULL";
        $stmt = $this->connection->query($sql);
        return $stmt->fetch()['total'];
    }

    // Programming language operations
    public function getProgrammingLanguages() {
        try {
            $stmt = $this->connection->query("SELECT * FROM programming_languages ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching programming languages: " . $e->getMessage());
            return [];
        }
    }

    public function getLanguageById($id) {
        $sql = "SELECT * FROM programming_languages WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Topic operations
    public function getTopicsByLanguage($languageId) {
        try {
            $stmt = $this->connection->prepare("
                SELECT * FROM topics 
                WHERE language_id = ?
                ORDER BY order_index
            ");
            $stmt->execute([$languageId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching topics: " . $e->getMessage());
            return [];
        }
    }

    public function getTopicById($id) {
        $sql = "SELECT t.*, l.name as language_name, l.icon as language_icon 
                FROM topics t 
                JOIN programming_languages l ON t.language_id = l.id 
                WHERE t.id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // User progress operations
    public function getUserTopicProgress($userId, $topicId) {
        try {
            $stmt = $this->connection->prepare("
                SELECT * FROM user_progress 
                WHERE user_id = ? AND topic_id = ?
            ");
            $stmt->execute([$userId, $topicId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user progress: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserProgress($userId, $topicId, $status, $progress) {
        $sql = "INSERT INTO user_progress (user_id, topic_id, status, progress) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                status = VALUES(status), 
                progress = VALUES(progress),
                last_accessed = CURRENT_TIMESTAMP,
                completed_at = CASE WHEN VALUES(status) = 'completed' THEN CURRENT_TIMESTAMP ELSE completed_at END";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$userId, $topicId, $status, $progress]);
    }

    // Quiz operations
    public function getQuizQuestions($topicId, $difficulty = null) {
        $sql = "SELECT * FROM quiz_questions WHERE topic_id = ?";
        $params = [$topicId];
        
        if ($difficulty) {
            $sql .= " AND difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY RAND()";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getQuizAnswers($questionId) {
        $sql = "SELECT * FROM quiz_answers WHERE question_id = ? ORDER BY RAND()";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$questionId]);
        return $stmt->fetchAll();
    }

    public function recordQuizAttempt($userId, $questionId, $selectedAnswerId, $isCorrect, $pointsEarned) {
        $sql = "INSERT INTO user_quiz_attempts 
                (user_id, question_id, selected_answer_id, is_correct, points_earned) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$userId, $questionId, $selectedAnswerId, $isCorrect, $pointsEarned]);
    }

    // Code challenge operations
    public function getCodeChallenges($topicId, $difficulty = null) {
        $sql = "SELECT * FROM code_challenges WHERE topic_id = ?";
        $params = [$topicId];
        
        if ($difficulty) {
            $sql .= " AND difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY difficulty, id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCodeChallenge($id) {
        $sql = "SELECT * FROM code_challenges WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function submitCode($userId, $challengeId, $code, $status, $testResults, $pointsEarned) {
        $sql = "INSERT INTO user_code_submissions 
                (user_id, challenge_id, code, status, test_results, points_earned) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$userId, $challengeId, $code, $status, json_encode($testResults), $pointsEarned]);
    }

    // Admin operations
    public function createTopic($languageId, $title, $description, $difficulty, $orderIndex) {
        $sql = "INSERT INTO topics (language_id, title, description, difficulty, order_index) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$languageId, $title, $description, $difficulty, $orderIndex]);
    }

    public function createQuizQuestion($topicId, $question, $questionType, $difficulty, $points) {
        $sql = "INSERT INTO quiz_questions (topic_id, question, question_type, difficulty, points) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$topicId, $question, $questionType, $difficulty, $points]);
        return $this->connection->lastInsertId();
    }

    public function createQuizAnswer($questionId, $answer, $isCorrect, $explanation) {
        $sql = "INSERT INTO quiz_answers (question_id, answer, is_correct, explanation) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$questionId, $answer, $isCorrect, $explanation]);
    }

    public function createCodeChallenge($topicId, $title, $description, $starterCode, $testCases, $difficulty, $points) {
        $sql = "INSERT INTO code_challenges 
                (topic_id, title, description, starter_code, test_cases, difficulty, points) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            $topicId, 
            $title, 
            $description, 
            $starterCode, 
            json_encode($testCases), 
            $difficulty, 
            $points
        ]);
    }

    /**
     * Get topics by language with filters
     */
    public function getFilteredTopics($filters, $userId = null) {
        $sql = "SELECT t.*, pl.name as language_name 
                FROM topics t 
                JOIN programming_languages pl ON t.language_id = pl.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (t.title LIKE ? OR t.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['difficulty'])) {
            $sql .= " AND t.difficulty = ?";
            $params[] = $filters['difficulty'];
        }

        if (!empty($filters['language'])) {
            $sql .= " AND t.language_id = ?";
            $params[] = $filters['language'];
        }

        if (!empty($filters['progress']) && $userId) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM user_progress up 
                WHERE up.topic_id = t.id 
                AND up.user_id = ? 
                AND up.status = ?
            )";
            $params[] = $userId;
            $params[] = $filters['progress'];
        }

        $sql .= " ORDER BY pl.name, t.order_index";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get visitor's progress for a topic
     */
    public function getVisitorTopicProgress($visitorId, $topicId) {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as visit_count,
                   MAX(is_completed) as is_completed,
                   SUM(duration) as total_duration
            FROM tutorial_visits
            WHERE visitor_id = ? AND topic_id = ?
        ");
        $stmt->execute([$visitorId, $topicId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['visit_count'] > 0) {
            return [
                'status' => $result['is_completed'] ? 'completed' : 'in_progress',
                'progress' => min(100, ($result['total_duration'] / 300) * 100) // 5 minutes = 100%
            ];
        }
        return null;
    }

    /**
     * Update user's topic progress
     */
    public function updateUserTopicProgress($userId, $topicId, $status) {
        $progress = $status === 'completed' ? 100 : ($status === 'in_progress' ? 50 : 0);
        $completedAt = $status === 'completed' ? 'CURRENT_TIMESTAMP' : 'NULL';

        $stmt = $this->connection->prepare("
            INSERT INTO user_progress (user_id, topic_id, status, progress, last_accessed, completed_at)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, " . $completedAt . ")
            ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                progress = VALUES(progress),
                last_accessed = VALUES(last_accessed),
                completed_at = " . $completedAt
        );

        return $stmt->execute([$userId, $topicId, $status, $progress]);
    }

    /**
     * Update visitor's topic progress
     */
    public function updateVisitorTopicProgress($visitorId, $topicId, $status) {
        $isCompleted = $status === 'completed' ? 1 : 0;

        $stmt = $this->connection->prepare("
            INSERT INTO tutorial_visits (visitor_id, topic_id, is_completed, visit_time)
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");

        return $stmt->execute([$visitorId, $topicId, $isCompleted]);
    }

    /**
     * Get user's progress statistics
     */
    public function getUserProgressStats($userId) {
        $stmt = $this->connection->prepare("
            SELECT 
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_topics,
                COUNT(DISTINCT language_id) as languages_started,
                (
                    SELECT COUNT(DISTINCT t.language_id)
                    FROM user_progress up
                    JOIN topics t ON up.topic_id = t.id
                    WHERE up.user_id = ?
                    AND up.status = 'completed'
                    GROUP BY t.language_id
                    HAVING COUNT(*) = (
                        SELECT COUNT(*)
                        FROM topics t2
                        WHERE t2.language_id = t.language_id
                    )
                ) as languages_completed,
                (
                    SELECT COALESCE(
                        ROUND(
                            (COUNT(CASE WHEN up.status = 'completed' THEN 1 END) * 100.0) /
                            NULLIF(COUNT(*), 0)
                        ),
                        0
                    )
                    FROM topics t
                    LEFT JOIN user_progress up ON t.id = up.topic_id AND up.user_id = ?
                    WHERE t.language_id = (
                        SELECT t2.language_id
                        FROM topics t2
                        JOIN user_progress up2 ON t2.id = up2.topic_id
                        WHERE up2.user_id = ?
                        ORDER BY up2.last_accessed DESC
                        LIMIT 1
                    )
                ) as language_completion
            FROM user_progress
            WHERE user_id = ?
        ");
        
        $stmt->execute([$userId, $userId, $userId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user has an achievement
     */
    public function hasAchievement($userId, $achievementId) {
        $stmt = $this->connection->prepare("
            SELECT 1 FROM user_achievements
            WHERE user_id = ? AND achievement_id = ?
        ");
        $stmt->execute([$userId, $achievementId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Award an achievement to a user
     */
    public function awardAchievement($userId, $achievementId) {
        $stmt = $this->connection->prepare("
            INSERT INTO user_achievements (user_id, achievement_id, awarded_at)
            VALUES (?, ?, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([$userId, $achievementId]);
    }

    /**
     * Get tutorial analytics for admin dashboard
     */
    public function getTutorialAnalytics($timeRange = 'week') {
        try {
            $timeFilter = match($timeRange) {
                'day' => 'DATE(visit_time) = CURDATE()',
                'week' => 'visit_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)',
                'month' => 'visit_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)',
                default => 'TRUE'
            };

            $stmt = $this->connection->prepare("
                SELECT 
                    pl.name as language_name,
                    COUNT(DISTINCT CASE WHEN tv.user_id IS NOT NULL THEN tv.user_id ELSE tv.visitor_id END) as unique_visitors,
                    COUNT(DISTINCT tv.user_id) as registered_users,
                    COUNT(DISTINCT CASE WHEN tv.user_id IS NULL THEN tv.visitor_id END) as visitors,
                    AVG(tv.duration) as avg_duration,
                    COUNT(CASE WHEN tv.is_completed = 1 THEN 1 END) as completions
                FROM tutorial_visits tv
                JOIN programming_languages pl ON tv.language_id = pl.id
                WHERE {$timeFilter}
                GROUP BY pl.id, pl.name
                ORDER BY unique_visitors DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching tutorial analytics: " . $e->getMessage());
            return [];
        }
    }

    // Ban/unban operations
    public function banUser($userId) {
        $sql = "UPDATE users SET is_banned=1 WHERE id=?";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$userId]);
    }
    public function unbanUser($userId) {
        $sql = "UPDATE users SET is_banned=0 WHERE id=?";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$userId]);
    }
    public function banAdmin($adminId) {
        $sql = "UPDATE admin_users SET is_banned=1 WHERE admin_id=?";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$adminId]);
    }
    public function unbanAdmin($adminId) {
        $sql = "UPDATE admin_users SET is_banned=0 WHERE admin_id=?";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$adminId]);
    }
    public function logAdminAction($adminId, $actionType, $targetType, $targetId, $details = null) {
        $sql = "INSERT INTO admin_actions_log (admin_id, action_type, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([$adminId, $actionType, $targetType, $targetId, $details]);
    }

    // Proxy prepare method to underlying PDO connection
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
} 