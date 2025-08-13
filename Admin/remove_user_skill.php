<?php

session_start();
include '../connection.php';
include'logger.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Check if user_id and skill_id are set in the URL
if (isset($_GET['user_id'], $_GET['skill_id'])) {
    $userId = $_GET['user_id'];
    $skillId = $_GET['skill_id'];

    // Sanitize and validate input
    if (filter_var($userId, FILTER_VALIDATE_INT) === false || filter_var($skillId, FILTER_VALIDATE_INT) === false) {
        // Redirect to manageuser_skill.php if invalid data is found
        header("Location: manageuser_skill.php");
        exit;
    }

    try {
        // Prepare and execute the DELETE statement
        $stmt = $conn->prepare("DELETE FROM user_skill WHERE user_id = ? AND skill_id = ?");
        $stmt->execute([$userId, $skillId]);
        log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage user_ skill');

        // Redirect back to manageuser_skill.php after successful deletion
        header("Location: manageuser_skill.php");
        exit;
    } catch (PDOException $e) {
        // Handle error (optional: log the error or show a message)
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // Redirect if the required parameters are not passed
    header("Location: manageuser_skill.php");
    exit;
}
?>

