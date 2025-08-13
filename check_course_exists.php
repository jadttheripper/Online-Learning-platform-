<?php
// check_course_exists.php
include 'connection.php';

if (isset($_GET['skill_id'])) {
    $skillId = (int) trim(strip_tags($_GET['skill_id']));

    // Count how many courses are tied to this skill
    $stmt = $conn->prepare("SELECT COUNT(*) FROM course WHERE skill_id = ?");
    $stmt->execute([$skillId]);
    $courseExists = $stmt->fetchColumn() > 0;

    // Return JSON { exists: true/false }
    header('Content-Type: application/json');
    echo json_encode(['exists' => $courseExists]);
    exit;
}

// If no skill_id provided:
http_response_code(400);
echo json_encode(['error' => 'Missing skill_id']);
