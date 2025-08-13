<?php
session_start();
require 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$currentUserId = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("SELECT skill_id FROM user_skill WHERE user_id = ?");
$stmt->execute([$currentUserId]);
$userSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$userSkills) {
    echo json_encode(['success' => false, 'message' => 'No skills assigned.']);
    exit();
}

$skillIds = array_column($userSkills, 'skill_id');
$placeholders = implode(',', array_fill(0, count($skillIds), '?'));

$stmt = $conn->prepare("
    SELECT c.c_id, c.title, c.description, c.user_skill_id
    FROM course c 
    JOIN user_skill us ON c.user_skill_id = us.user_skill_id 
    WHERE us.skill_id IN ($placeholders) AND us.user_id = ?
");
$stmt->execute([...$skillIds, $currentUserId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transform the data to include 'id' field that JavaScript expects
$transformedCourses = array_map(function($course) {
    return [
        'id' => (int)$course['c_id'],        // Add 'id' field for JavaScript
        'c_id' => (int)$course['c_id'],      // Keep original for compatibility
        'title' => $course['title'],
        'description' => $course['description'],
        'user_skill_id' => (int)$course['user_skill_id']
    ];
}, $courses);

echo json_encode(['success' => true, 'courses' => $transformedCourses]);
?>