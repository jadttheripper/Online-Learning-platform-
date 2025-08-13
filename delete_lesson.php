<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include 'connection.php';

$lessonId = $_GET['lesson_id'] ?? null;

if (!$lessonId) {
    echo json_encode(['success' => false, 'message' => 'Lesson ID is missing.']);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM lesson WHERE lesson_id = ?");
    $stmt->execute([$lessonId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Lesson deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lesson not found or could not be deleted.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
