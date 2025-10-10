<?php
/**
 * File: api/get-playlist.php
 * Purpose: Fetches coding playlist items for the About page music section
 * Features:
 *   - Retrieves playlist tracks with metadata
 *   - Supports featured tracks and play count tracking
 *   - Returns formatted data for audio player integration
 * Usage:
 *   - Called via GET from the About page playlist section
 *   - Optional parameters: featured_only, limit
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: September 29, 2025
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/Database.php';

try {
    $conn = Database::getInstance()->getConnection();
    
    // Get parameters
    $featuredOnly = isset($_GET['featured_only']) && $_GET['featured_only'] === 'true';
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $limit = min($limit, 50); // Max 50 tracks per request
    
    // Build query
    $whereClause = $featuredOnly ? "WHERE is_featured = 1" : "";
    
    // Fetch playlist items
    $stmt = $conn->prepare("
        SELECT 
            id,
            title,
            artist,
            file_path,
            external_url,
            duration,
            genre,
            is_featured,
            play_count,
            display_order,
            created_at
        FROM coding_playlist
        {$whereClause}
        ORDER BY display_order ASC, created_at DESC
        LIMIT ?
    ");
    
    $params = $featuredOnly ? [$limit] : [$limit];
    $stmt->execute($params);
    $playlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend consumption
    $formattedPlaylist = [];
    foreach ($playlistItems as $item) {
        $formattedPlaylist[] = [
            'id' => $item['id'],
            'title' => htmlspecialchars($item['title']),
            'artist' => htmlspecialchars($item['artist']),
            'file_path' => $item['file_path'],
            'external_url' => $item['external_url'],
            'duration' => intval($item['duration']),
            'duration_formatted' => formatDuration($item['duration']),
            'genre' => htmlspecialchars($item['genre']),
            'is_featured' => (bool)$item['is_featured'],
            'play_count' => intval($item['play_count']),
            'display_order' => intval($item['display_order'])
        ];
    }
    
    // Get genre statistics
    $genreStmt = $conn->prepare("
        SELECT genre, COUNT(*) as count 
        FROM coding_playlist 
        GROUP BY genre 
        ORDER BY count DESC
    ");
    $genreStmt->execute();
    $genres = $genreStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'playlist' => $formattedPlaylist,
        'genres' => $genres,
        'total' => count($formattedPlaylist),
        'featured_only' => $featuredOnly
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

/**
 * Helper function to format duration in seconds to MM:SS format
 */
function formatDuration($seconds) {
    if (!$seconds || $seconds <= 0) return '0:00';
    
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;
    
    return sprintf('%d:%02d', $minutes, $remainingSeconds);
}
?>
