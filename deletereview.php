<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['review_id'])) {
    header('Location: review.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$review_id = isset($_POST['review_id']) ? trim(strip_tags($_POST['review_id'])) : '';

// Ensure the review ID is a valid number (optional, depending on your use case)
if (!is_numeric($review_id)) {
    header('Location: index.php');
    exit;
}

// Only delete if the review belongs to the logged-in user
$stmt = $conn->prepare("DELETE FROM review WHERE review_id = ? AND user_id = ?");
$stmt->execute([$review_id, $user_id]);

header('Location: review.php');
exit;
?>
