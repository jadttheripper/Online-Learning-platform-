<?php
header('Content-Type: application/json');
include 'connection.php';

$courseId = $_GET['course_id'] ?? null;

if (!$courseId || !is_numeric($courseId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid course ID.'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT lesson_id, title, content, video_url FROM lesson WHERE c_id = ?");
    $stmt->execute([$courseId]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'lessons' => $lessons
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
