<?php
include 'connection.php';

$skillId = isset($_GET['skill_id']) ? intval($_GET['skill_id']) : 0;

if ($skillId <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT u.user_id, u.name, u.email
FROM user u
JOIN user_skill us ON u.user_id = us.user_id
WHERE us.skill_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->execute([$skillId]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($users);
