<?php
session_start();
include 'connection.php'; // Assuming this file has the PDO connection setup

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Check if user exists
$stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
if ($stmt->fetchColumn() == 0) {
    echo "Error: User not found!";
    exit();
}

try {
    // Begin transaction
    $conn->beginTransaction();

    // Step 1: Delete from message (sender or receiver)
    $stmt = $conn->prepare("DELETE FROM message WHERE sender_id = ? OR receiver_id = ?");
    $stmt->execute([$userId, $userId]);

    // Step 2: Delete from user_skill
    $stmt = $conn->prepare("DELETE FROM user_skill WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Step 3: Delete from review
    $stmt = $conn->prepare("DELETE FROM review WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Step 4: Delete the user
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Commit transaction
    $conn->commit();

    // Destroy session
    session_destroy();

    // Redirect
    header('location:goodbye.php');
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error deleting account: " . $e->getMessage();
}
?>
