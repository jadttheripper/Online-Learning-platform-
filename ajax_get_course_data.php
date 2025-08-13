<?php
session_start();
header('Content-Type: application/json');
include 'connection.php';

$userId = $_SESSION['user_id'] ?? null;
$courseId = $_GET['course_id'] ?? null;

if (!$userId || !$courseId || !is_numeric($courseId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT l.lesson_id, l.title, l.content, l.video_url,
               COALESCE(lp.is_completed, 0) AS is_completed
        FROM lesson l
        LEFT JOIN lesson_progress lp ON lp.lesson_id = l.lesson_id AND lp.user_id = ? AND lp.c_id = l.c_id
        WHERE l.c_id = ?
    ");
    $stmt->execute([$userId, $courseId]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'lessons' => $lessons]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>