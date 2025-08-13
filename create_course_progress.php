<?php
session_start();
require 'connection.php'; // Make sure this connects to your database using PDO

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Validate and sanitize course ID
$courseId = filter_input(INPUT_GET, 'c_id', FILTER_VALIDATE_INT);
if (!$courseId) {
    http_response_code(400);
    echo "Invalid course ID.";
    exit;
}

// Step 1: Get the last message containing this course ID
$stmt = $conn->prepare("SELECT sender_id FROM message WHERE content LIKE ? ORDER BY time_stamp DESC LIMIT 1");
$searchPattern = '%c_id=' . $courseId . '%';
$stmt->execute([$searchPattern]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

$senderId = $message['sender_id'] ?? null;

if ($senderId === $currentUserId) {
    // User is the sender - redirect to their course management page
    header("Location: view_courses.php?course_id=" . urlencode($courseId));
    exit;
}

// Step 2: Check if this user already started the course
$stmt = $conn->prepare("SELECT progress_id FROM course_progress WHERE user_id = ? AND c_id = ?");
$stmt->execute([$currentUserId, $courseId]);
$existingProgress = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingProgress) {
    // Already started
    header("Location: viewcourseasuser.php?course_id=" . urlencode($courseId));
    exit;
}

// Step 3: Insert course progress
$stmt = $conn->prepare("
    INSERT INTO course_progress (user_id, c_id, started_at, last_accessed_at)
    VALUES (?, ?, NOW(), NOW())
");
$stmt->execute([$currentUserId, $courseId]);

$stmt = $conn->prepare("SELECT lesson_id FROM lesson WHERE c_id = ?");
$stmt->execute([$courseId]);
$lessonIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($lessonIds) {
    $insertLesson = $conn->prepare("
        INSERT INTO lesson_progress (user_id, c_id, lesson_id, is_completed)
        VALUES (?, ?, ?, 0)
    ");
    foreach ($lessonIds as $lessonId) {
        $insertLesson->execute([$currentUserId, $courseId, $lessonId]);
    }
} else {
    // Log or handle the case where no lessons are found
    error_log("No lessons found for course_id: $courseId");
}
// Step 5: Redirect to user view of the course
header("Location: viewcourseasuser.php?course_id=" . urlencode($courseId));
exit;
?>
