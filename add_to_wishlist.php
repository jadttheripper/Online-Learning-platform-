<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

require 'connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$skillId = $data['skill_id'] ?? null;
$userId = $_SESSION['user_id'];

if (!$skillId) {
    echo json_encode(['success' => false, 'message' => 'Skill ID missing']);
    exit;
}

try {
    // Optional: Prevent duplicates
    $stmt = $conn->prepare("SELECT * FROM wishlist_skills WHERE user_id = ? AND skill_id = ?");
    $stmt->execute([$userId, $skillId]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Already in wishlist']);
        exit;
    }

    // Insert
    $stmt = $conn->prepare("INSERT INTO wishlist_skills (user_id, skill_id) VALUES (?, ?)");
    $stmt->execute([$userId, $skillId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
