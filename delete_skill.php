<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$userId = $_SESSION['user_id'];

// Sanitize inputs
$skillId = isset($_POST['skill_id']) ? intval($_POST['skill_id']) : null;
$courseId = isset($_POST['course_id']) ? intval($_POST['course_id']) : null;

if (!$skillId) {
    http_response_code(400);
    echo "Skill ID missing";
    exit();
}

try {
    // Begin a transaction for the entire process
    $conn->beginTransaction();

    // Step 1: Delete from user_skill table (unlink the user from the skill)
    $stmt = $conn->prepare("DELETE FROM user_skill WHERE user_id = ? AND skill_id = ?");
    $stmt->execute([$userId, $skillId]);

    // Step 2: If a course is associated with the skill, handle course deletion
    if ($courseId) {
        // Delete the course (this will also delete related sections and lessons due to ON DELETE CASCADE)
        $stmt = $conn->prepare("DELETE FROM course WHERE c_id = ?");
        $stmt->execute([$courseId]);
    }

    // Step 3: Optionally, delete the skill itself if there are no users with that skill
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_skill WHERE skill_id = ?");
    $stmt->execute([$skillId]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $conn->prepare("DELETE FROM skill WHERE skill_id = ?");
        $stmt->execute([$skillId]);
    }

    // Commit the transaction
    $conn->commit();

    // Redirect the user back to the profile page
    header("Location: profile.php");
    exit();
} catch (Exception $e) {
    // If something goes wrong, roll back the transaction
    $conn->rollBack();
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
