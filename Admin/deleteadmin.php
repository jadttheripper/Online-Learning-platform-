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
    header("Location: manage_admins.php");
    exit;
}

$adminId = $_GET['id'];

// Delete the admin record based on the ID
$stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = :admin_id");
$stmt->execute(['admin_id' => $adminId]);
log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage admin');

// Redirect back to the manage admins page after deleting
header("Location: adminmanagement.php");
exit;
?>
