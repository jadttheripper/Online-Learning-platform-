<?php
// Start the session
session_start();
include '../connection.php';
// Ensure admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}
include 'logger.php';
// Ensure ID is provided
if (!isset($_GET['id'])) {
    header("Location: manageuser.php");
    exit;
}

$userId = trim(strip_tags($_GET['id']));

try {
    // Begin transaction
    $conn->beginTransaction();

    // Delete messages where the user is sender or receiver
    $stmt = $conn->prepare("DELETE FROM message WHERE sender_id = :id OR receiver_id = :id");
    $stmt->execute(['id' => $userId]);

    // Delete from user_skill
    $stmt = $conn->prepare("DELETE FROM user_skill WHERE user_id = :id");
    $stmt->execute(['id' => $userId]);

    // Delete from review
    $stmt = $conn->prepare("DELETE FROM review WHERE user_id = :id");
    $stmt->execute(['id' => $userId]);

    // Delete the user
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = :id");
    $stmt->execute(['id' => $userId]);
    log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage user');
    // Commit all changes
    $conn->commit();

    // Redirect to management page
    header("Location: manageuser.php");
    exit;

} catch (Exception $e) {
    // Roll back changes if something fails
    $conn->rollBack();
    echo "Error deleting user and related data: " . $e->getMessage();
}
?>
