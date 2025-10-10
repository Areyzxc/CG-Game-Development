<?php
/**
 * File: api/get-faq.php
 * Purpose: Fetches FAQ items for the About page with search functionality
 * Features:
 *   - Retrieves FAQ items with optional search filtering
 *   - Supports category filtering and featured items
 *   - Returns formatted data for FAQ accordion display
 * Usage:
 *   - Called via GET from the About page FAQ section
 *   - Optional parameters: search, category, featured_only
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
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $featuredOnly = isset($_GET['featured_only']) && $_GET['featured_only'] === 'true';
    
    // Build query
    $whereConditions = [];
    $params = [];
    
    if (!empty($search)) {
        $whereConditions[] = "(question LIKE ? OR answer LIKE ? OR tags LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $whereConditions[] = "category = ?";
        $params[] = $category;
    }
    
    if ($featuredOnly) {
        $whereConditions[] = "is_featured = 1";
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Fetch FAQ items
    $stmt = $conn->prepare("
        SELECT 
            id,
            question,
            answer,
            category,
            tags,
            is_featured,
            view_count,
            display_order,
            created_at,
            updated_at
        FROM faq_items
        {$whereClause}
        ORDER BY display_order ASC, created_at DESC
    ");
    
    $stmt->execute($params);
    $faqItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend consumption
    $formattedFaq = [];
    foreach ($faqItems as $item) {
        $formattedFaq[] = [
            'id' => $item['id'],
            'question' => htmlspecialchars($item['question']),
            'answer' => htmlspecialchars($item['answer']),
            'category' => $item['category'],
            'tags' => $item['tags'] ? explode(',', $item['tags']) : [],
            'is_featured' => (bool)$item['is_featured'],
            'view_count' => intval($item['view_count']),
            'display_order' => intval($item['display_order'])
        ];
    }
    
    // Get categories for filter options
    $categoriesStmt = $conn->prepare("SELECT DISTINCT category FROM faq_items ORDER BY category");
    $categoriesStmt->execute();
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        'success' => true,
        'faq_items' => $formattedFaq,
        'categories' => $categories,
        'total' => count($formattedFaq),
        'search_term' => $search,
        'category_filter' => $category,
        'featured_only' => $featuredOnly
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
