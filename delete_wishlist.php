<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['skill_id'])) {
    $skillId = intval($_POST['skill_id']); // sanitize input

    // Assuming $pdo is your PDO instance, already connected to DB
   include 'connection.php';

    try {
        $stmt = $conn->prepare('DELETE FROM wishlist_skills WHERE skill_id = ?');
        $stmt->execute([$skillId]);

        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        echo "Database error: " . htmlspecialchars($e->getMessage());
        exit;
    }
} else {
    header('Location: profile.php');
    exit;
}
