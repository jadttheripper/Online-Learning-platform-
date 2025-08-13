<?php
// recommend.php

header('Content-Type: application/json');

// Read JSON body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryScores']) || !is_array($data['categoryScores'])) {
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Get the category with the highest score
$topCategory = '';
$maxScore = -1;

foreach ($data['categoryScores'] as $category => $score) {
    if ($score > $maxScore) {
        $topCategory = $category;
        $maxScore = $score;
    }
}

// Return the top category as the recommended skill
echo json_encode(['skill' => $topCategory]);
exit;
?>
