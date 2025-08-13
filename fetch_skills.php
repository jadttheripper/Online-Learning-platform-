<?php
include 'connection.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM skill WHERE 1";
$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category !== '') {
    $sql .= " AND skill_category = ?";
    $params[] = $category;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure proper image fallback
foreach ($skills as &$skill) {
    if (empty($skill['image_url'])) {
        $skill['image_url'] = 'image/default.png';
    }
}

header('Content-Type: application/json');
echo json_encode($skills);
