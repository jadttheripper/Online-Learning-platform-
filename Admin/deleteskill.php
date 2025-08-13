<?php
session_start();
include '../connection.php';
include 'logger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $skillId = (int) $_GET['id'];

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Delete from user_skill first
        $deleteUserSkills = $conn->prepare("DELETE FROM user_skill WHERE skill_id = :skillId");
        $deleteUserSkills->execute(['skillId' => $skillId]);

        // Delete the skill itself
        $deleteSkill = $conn->prepare("DELETE FROM skill WHERE skill_id = :skillId");
        $deleteSkill->execute(['skillId' => $skillId]);

        // Log the action
        log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage skill');

        // Commit changes
        $conn->commit();

        header("Location: manageskills.php?deleted=1");
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error deleting skill: " . htmlspecialchars($e->getMessage());
    }

} else {
    echo "Invalid skill ID.";
}
?>

