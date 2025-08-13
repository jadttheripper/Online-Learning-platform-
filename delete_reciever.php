<?php
session_start();
require_once 'connection.php'; // replace with your DB connection file

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
$receiverId = $_POST['receiver_id'] ?? null;

if ($receiverId) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM message 
                           WHERE (sender_id = :user AND receiver_id = :receiver)
                              OR (sender_id = :receiver AND receiver_id = :user)");
    $stmt->execute([
        ':user' => $currentUserId,
        ':receiver' => $receiverId
    ]);

    // Redirect back to inbox or chat list
    header("Location: messages.php");
    exit;
} else {
    echo "Invalid request.";
}
