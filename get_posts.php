<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

try {
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    
    $stmt = $conn->prepare("
      SELECT 
        p.id, 
        p.user_id, 
        p.content, 
        p.media_url, 
        p.created_at, 
        u.name AS username,
        u.profile_pic,
        (SELECT COUNT(*) FROM post_like WHERE post_id = p.id) AS like_count,
        EXISTS (
            SELECT 1 FROM post_like 
            WHERE post_id = p.id AND user_id = :currentUserId
        ) AS liked_by_user
    FROM post p
    JOIN user u ON p.user_id = u.user_id
    ORDER BY p.created_at DESC
    ");
    
    $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'posts' => $posts]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>