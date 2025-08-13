<?php
session_start();
require 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['c_id'])) {
    echo json_encode(['success' => false, 'message' => 'Course ID not provided']);
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$courseId = (int)$_GET['c_id'];

// Fetch course info (title and description)
$stmt = $conn->prepare("SELECT title, description FROM course WHERE c_id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit;
}

$courseTitle = htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8');
$courseDescription = htmlspecialchars($course['description'], ENT_QUOTES, 'UTF-8');

// Create the styled course card HTML
$messageContent = "
<div class='course-card' style='border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:300px; margin:10px 0;'>
    <h4 style='margin:0 0 8px; font-size:18px; color:#1d4ed8;'>📘 {$courseTitle}</h4>
    <p style='margin:0 0 12px; color:#555; font-size:14px;'>{$courseDescription}</p>
    <a href='create_course_progress.php?c_id={$courseId}' style='display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;'>Start Course</a>
</div>
";

try {
    $stmt = $conn->prepare("INSERT INTO post (user_id, content, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$currentUserId, $messageContent]);

    echo json_encode([
        'success' => true,
        'message' => 'Course posted successfully',
        'redirect' => 'index.php' // Or wherever you want to redirect after posting
    ]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to post course. Please try again.'
    ]);
}
?>