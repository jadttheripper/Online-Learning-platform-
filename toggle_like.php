<?php
session_start();
require 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$postId = (int)$_POST['post_id'];

try {
    // Check if already liked - using like_id instead of id
    $stmt = $conn->prepare("SELECT like_id FROM post_like WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$userId, $postId]);

    if ($stmt->fetch()) {
        // Unlike
        $conn->prepare("DELETE FROM post_like WHERE user_id = ? AND post_id = ?")->execute([$userId, $postId]);
        $liked = false;
    } else {
        // Like
        $conn->prepare("INSERT INTO post_like (user_id, post_id) VALUES (?, ?)")->execute([$userId, $postId]);
        $liked = true;
    }

    // Get updated count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM post_like WHERE post_id = ?");
    $stmt->execute([$postId]);
    $likeCount = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'like_count' => $likeCount
    ]);
} catch (PDOException $e) {
    error_log("Like error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>