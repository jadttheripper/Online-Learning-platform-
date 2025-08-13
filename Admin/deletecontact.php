<?php
session_start();
include '../connection.php';
include 'logger.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM contact WHERE message_id = ?");
    $stmt->execute([$id]);
    log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage contact ');
}

header("Location: contact_management.php");
exit;
?>
