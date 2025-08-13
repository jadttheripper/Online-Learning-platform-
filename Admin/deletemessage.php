<?php
session_start();
include '../connection.php';
include 'logger.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Validate message ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: managemessages.php");
    exit;
}

$messageId = (int)$_GET['id'];

// Delete the message
$delete = $conn->prepare("DELETE FROM message WHERE message_id = :id");
$delete->execute(['id' => $messageId]);

// Log action
log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage message');

// Redirect on success
header("Location: managemessages.php?deleted=1");
exit;
?>
