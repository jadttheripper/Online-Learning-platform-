<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No review ID provided.";
    exit;
}

$review_id = intval($_GET['id']);

// Optional: confirm the review exists before deleting
$stmt = $conn->prepare("SELECT * FROM review WHERE review_id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);
include 'logger.php';
if (!$review) {
    echo "Review not found.";
    exit;
}

// Delete the review
$delete = $conn->prepare("DELETE FROM review WHERE review_id = ?");
$delete->execute([$review_id]);
log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage reviews');
// Redirect after deletion
header("Location: review_management.php");
exit;
?>
