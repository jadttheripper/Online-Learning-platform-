<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

session_start();
header('Content-Type: application/json');
include 'connection.php';

// Debug logging
file_put_contents('debug_markasdone.log', print_r([
    'timestamp' => date('Y-m-d H:i:s'),
    'user_id' => $_SESSION['user_id'] ?? null,
    'input' => file_get_contents('php://input')
], true), FILE_APPEND);

$userId = $_SESSION['user_id'] ?? null;
$input = json_decode(file_get_contents('php://input'), true);
$lessonId = $input['lesson_id'] ?? null;
$courseId = $input['course_id'] ?? null;

if (!$userId || !$lessonId || !$courseId) {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters']);
    exit;
}

try {
    // Begin transaction
    $conn->beginTransaction();

    // 1. First ensure the lesson exists in the course
    $lessonCheck = $conn->prepare("SELECT 1 FROM lesson WHERE lesson_id = ? AND c_id = ?");
    $lessonCheck->execute([$lessonId, $courseId]);
    
    if (!$lessonCheck->fetch()) {
        throw new Exception("Lesson not found in this course");
    }

    // 2. Check if progress record exists, create if not
    $checkStmt = $conn->prepare("
        INSERT INTO lesson_progress 
        (user_id, lesson_id, c_id, is_completed, completed_at)
        VALUES (?, ?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE 
        is_completed = 1,
        completed_at = NOW()
    ");
    $checkStmt->execute([$userId, $lessonId, $courseId]);

    // 3. Update course progress timestamp
    $updateCourseStmt = $conn->prepare("
        UPDATE course_progress 
        SET last_accessed_at = NOW()
        WHERE user_id = ? AND c_id = ?
    ");
    $updateCourseStmt->execute([$userId, $courseId]);

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollBack();
    
    // Log the error
    error_log("markasdone error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>