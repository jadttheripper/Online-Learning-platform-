<?php
session_start();
include 'connection.php'; // Your DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit a review.");
}

// Validation
$user_id = $_SESSION['user_id'];
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

// Apply strip_tags and addslashes to sanitize input
$raw_comment = isset($_POST['comment']) ? $_POST['comment'] : '';
$comment = trim(strip_tags(($raw_comment)));

if ($rating < 1 || $rating > 5 || empty($comment)) {
    die("Invalid rating or empty comment.");
}

try {
    $stmt = $conn->prepare("INSERT INTO review (user_id, rating, comment) VALUES (:user_id, :rating, :comment)");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->execute();

    header("Location: index.php?review=success");
    exit;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
