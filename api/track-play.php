<?php
/**
 * File: api/track-play.php
 * Purpose: Tracks when a song from the coding playlist is played
 * Features:
 *   - Increments play count for playlist tracks
 *   - Prevents spam by limiting tracking frequency per IP
 *   - Returns updated play count
 * Usage:
 *   - Called via POST when a track starts playing on the About page
 *   - Required parameter: track_id
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: September 29, 2025
 */
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track_id'])) {
    $trackId = intval($_POST['track_id']);
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    // Prevent spam by checking if this IP played this track recently (within 30 seconds)
    $sessionKey = "track_play_{$trackId}_{$ipAddress}";
    $lastPlayTime = isset($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : 0;
    $currentTime = time();
    
    if (($currentTime - $lastPlayTime) < 30) {
        echo json_encode(['success' => false, 'error' => 'Play tracked too recently']);
        exit;
    }
    
    try {
        $conn = Database::getInstance()->getConnection();
        
        // Verify track exists
        $checkStmt = $conn->prepare('SELECT id FROM coding_playlist WHERE id = ?');
        $checkStmt->execute([$trackId]);
        
        if (!$checkStmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Track not found']);
            exit;
        }
        
        // Increment play count
        $updateStmt = $conn->prepare('UPDATE coding_playlist SET play_count = play_count + 1 WHERE id = ?');
        $updateStmt->execute([$trackId]);
        
        // Get updated play count
        $countStmt = $conn->prepare('SELECT play_count FROM coding_playlist WHERE id = ?');
        $countStmt->execute([$trackId]);
        $playCount = $countStmt->fetchColumn();
        
        // Update session to prevent spam
        $_SESSION[$sessionKey] = $currentTime;
        
        echo json_encode([
            'success' => true,
            'play_count' => intval($playCount),
            'track_id' => $trackId
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
