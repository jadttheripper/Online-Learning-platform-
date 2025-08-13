<?php
session_start();
require 'connection.php';

header('Content-Type: application/json');

// Simple version for testing
try {
    // Get course_id from POST or GET
    $courseId = null;
    if (isset($_POST['course_id'])) {
        $courseId = (int)$_POST['course_id'];
    } elseif (isset($_GET['course_id'])) {
        $courseId = (int)$_GET['course_id'];
    }
    
    if (!$courseId || $courseId <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid or missing course ID',
            'received_post' => $_POST,
            'received_get' => $_GET
        ]);
        exit;
    }

    // Get course data
    $stmt = $conn->prepare("SELECT c_id, title, description, user_skill_id FROM course WHERE c_id = ?");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        echo json_encode([
            'success' => false, 
            'message' => 'Course not found',
            'course_id' => $courseId
        ]);
        exit;
    }

    // Return course data
    echo json_encode([
        'success' => true,
        'course' => [
            'id' => (int)$course['c_id'],
            'c_id' => (int)$course['c_id'],
            'title' => $course['title'],
            'description' => $course['description'],
            'user_skill_id' => (int)$course['user_skill_id'],
            'link' => "create_course_progress.php?c_id=" . $course['c_id']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>