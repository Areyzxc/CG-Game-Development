<?php
/**
 * File: includes/UserProgressManager.php
 * Purpose: Utility class for managing user progress, achievements, and analytics.
 * Features:
 *   - Centralized user progress tracking
 *   - Achievement management and awarding
 *   - Statistics calculation and formatting
 *   - Progress data aggregation
 * Usage:
 *   - Use in API endpoints and pages that need user progress data
 *   - Provides consistent data formatting across the application
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */

class UserProgressManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get comprehensive user statistics
     */
    public function getUserStats($userId) {
        $stats = [];
        
        // Quiz stats
        $stats['quiz'] = $this->getQuizStats($userId);
        
        // Challenge stats
        $stats['challenge'] = $this->getChallengeStats($userId);
        
        // Mini-game stats
        $stats['minigame'] = $this->getMinigameStats($userId);
        
        // Tutorial stats
        $stats['tutorial'] = $this->getTutorialStats($userId);
        
        // Calculate total points
        $stats['total_points'] = $this->calculateTotalPoints($stats);
        
        return $stats;
    }
    
    /**
     * Get quiz statistics for a user
     */
    private function getQuizStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                MAX(attempted_at) as last_played
            FROM user_quiz_attempts 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Get challenge statistics for a user
     */
    private function getChallengeStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                SUM(points_earned) as total_points,
                MAX(attempted_at) as last_played
            FROM user_challenge_attempts 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Get mini-game statistics for a user
     */
    private function getMinigameStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_games,
                MAX(score) as best_score,
                AVG(score) as avg_score,
                MAX(played_at) as last_played
            FROM mini_game_results 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Get tutorial statistics for a user
     */
    private function getTutorialStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_topics,
                SUM(CASE WHEN status = 'done_reading' THEN 1 ELSE 0 END) as completed_topics
            FROM user_progress 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Calculate total points from all activities
     */
    private function calculateTotalPoints($stats) {
        $points = 0;
        $points += $stats['challenge']['total_points'] ?? 0;
        $points += ($stats['minigame']['best_score'] ?? 0) * 10;
        $points += ($stats['quiz']['correct_answers'] ?? 0) * 5;
        return $points;
    }
    
    /**
     * Get user achievements
     */
    public function getUserAchievements($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                ua.achievement_id,
                ua.awarded_at,
                CASE 
                    WHEN ua.achievement_id = 'first_quiz' THEN 'First Quiz Completed'
                    WHEN ua.achievement_id = 'quiz_master' THEN 'Quiz Master (10+ quizzes)'
                    WHEN ua.achievement_id = 'challenge_expert' THEN 'Challenge Expert'
                    WHEN ua.achievement_id = 'tutorial_complete' THEN 'Tutorial Graduate'
                    WHEN ua.achievement_id = 'minigame_champion' THEN 'Mini-Game Champion'
                    ELSE ua.achievement_id
                END as achievement_name,
                CASE 
                    WHEN ua.achievement_id = 'first_quiz' THEN 'medal'
                    WHEN ua.achievement_id = 'quiz_master' THEN 'trophy'
                    WHEN ua.achievement_id = 'challenge_expert' THEN 'bolt'
                    WHEN ua.achievement_id = 'tutorial_complete' THEN 'graduation-cap'
                    WHEN ua.achievement_id = 'minigame_champion' THEN 'crown'
                    ELSE 'star'
                END as achievement_icon,
                CASE 
                    WHEN ua.achievement_id = 'first_quiz' THEN 'warning'
                    WHEN ua.achievement_id = 'quiz_master' THEN 'info'
                    WHEN ua.achievement_id = 'challenge_expert' THEN 'danger'
                    WHEN ua.achievement_id = 'tutorial_complete' THEN 'success'
                    WHEN ua.achievement_id = 'minigame_champion' THEN 'primary'
                    ELSE 'secondary'
                END as achievement_color
            FROM user_achievements ua
            WHERE ua.user_id = ?
            ORDER BY ua.awarded_at DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user progress for tutorials
     */
    public function getUserProgress($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                topic_id,
                status,
                progress,
                last_accessed,
                completed_at
            FROM user_progress 
            WHERE user_id = ?
            ORDER BY last_accessed DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get personalized data for user
     */
    public function getPersonalizationData($userId, $username) {
        // Get user's profile picture
        $stmt = $this->db->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Generate personalized greeting based on time of day
        $hour = date('H');
        $greeting = '';
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }
        
        return [
            'username' => $username,
            'profile_picture' => $userData['profile_picture'] ?? null,
            'greeting' => $greeting,
            'last_seen' => date('Y-m-d H:i:s'),
            'level' => $this->calculateUserLevel($userId),
            'next_level_progress' => $this->calculateNextLevelProgress($userId)
        ];
    }
    
    /**
     * Calculate user level based on points
     */
    private function calculateUserLevel($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(points_earned), 0) as total_points
            FROM user_challenge_attempts 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $totalPoints = $result['total_points'] ?? 0;
        return min(floor($totalPoints / 100) + 1, 50); // Max level 50
    }
    
    /**
     * Calculate progress to next level
     */
    private function calculateNextLevelProgress($userId) {
        $currentLevel = $this->calculateUserLevel($userId);
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(points_earned), 0) as total_points
            FROM user_challenge_attempts 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $totalPoints = $result['total_points'] ?? 0;
        $currentLevelPoints = ($currentLevel - 1) * 100;
        $nextLevelPoints = $currentLevel * 100;
        $progressPoints = $totalPoints - $currentLevelPoints;
        
        return [
            'current' => $progressPoints,
            'needed' => $nextLevelPoints - $currentLevelPoints,
            'percentage' => min(round(($progressPoints / ($nextLevelPoints - $currentLevelPoints)) * 100), 100)
        ];
    }
    
    /**
     * Get guest statistics
     */
    public function getGuestStats($guestSessionId) {
        // Quiz stats for guest
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                MAX(attempted_at) as last_played
            FROM guest_quiz_attempts 
            WHERE guest_session_id = ?
        ");
        $stmt->execute([$guestSessionId]);
        $quizStats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
        // Challenge stats for guest
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_attempts,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                SUM(points_earned) as total_points,
                MAX(attempted_at) as last_played
            FROM guest_challenge_attempts 
            WHERE guest_session_id = ?
        ");
        $stmt->execute([$guestSessionId]);
        $challengeStats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
        return [
            'quiz' => $quizStats,
            'challenge' => $challengeStats,
            'minigame' => ['total_games' => 0, 'best_score' => 0, 'avg_score' => 0, 'last_played' => null],
            'tutorial' => ['total_topics' => 0, 'completed_topics' => 0],
            'total_points' => ($challengeStats['total_points'] ?? 0)
        ];
    }
    
    /**
     * Get guest personalization data
     */
    public function getGuestPersonalizationData($nickname) {
        $hour = date('H');
        $greeting = '';
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }
        
        return [
            'nickname' => $nickname,
            'greeting' => $greeting,
            'is_guest' => true,
            'level' => 1,
            'next_level_progress' => 0
        ];
    }
}
?>
