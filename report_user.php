<?php
session_start();

require 'connection.php'; // Your DB connection file
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$reporterId = $_POST['reporter_id'];
$reportedId = $_POST['reported_user_id'];
$reason = !empty($_POST['reason']) ? $_POST['reason'] : trim($_POST['reason_custom']);

if (!empty($reason)) {
    $stmt = $conn->prepare("INSERT INTO user_report (reporter_id, reported_user_id, reason) VALUES (?, ?, ?)");
    $stmt->execute([$reporterId, $reportedId, $reason]);

    // Optional: Redirect with success message
    header("Location: messages.php?report=success");
    exit();
} else {
    // Handle missing reason
    header("Location: messages.php?report=failed");
    exit();
}
