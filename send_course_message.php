<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user_id'], $_POST['course_id'], $_POST['receiver_id'])) {
    die("Invalid request.");
}

$currentUserId = (int)$_SESSION['user_id'];
$receiverId = (int)$_POST['receiver_id'];
$courseId = (int)$_POST['course_id'];

// Fetch course info (title and description)
$stmt = $conn->prepare("SELECT title, description FROM course WHERE c_id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found.");
}

$courseTitle = htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8');
$courseDescription = htmlspecialchars($course['description'], ENT_QUOTES, 'UTF-8');

$cardStyle = "border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:300px;";
$titleStyle = "margin:0 0 8px; font-size:18px; color:#1d4ed8;";
$descStyle = "margin:0 0 12px; color:#555; font-size:14px;";
$linkStyle = "display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;";

$messageContent = "
<div class='course-card' style='{$cardStyle}'>
    <h4 style='{$titleStyle}'>📘 {$courseTitle}</h4>
    <p style='{$descStyle}'>{$courseDescription}</p>
    <a href='create_course_progress.php?c_id={$courseId}' style='{$linkStyle}'>Start Course</a>
</div>
";

$stmt = $conn->prepare("INSERT INTO message (sender_id, receiver_id, content, time_stamp) VALUES (?, ?, ?, NOW())");
$stmt->execute([$currentUserId, $receiverId, $messageContent]);

header("Location: messages.php?receiver_id=$receiverId");
exit;

