<?php
require_once 'connection.php';

$courseId = 9; // Replace with your actual course_id

try {
    $stmt = $conn->prepare("SELECT lesson_id, title, content, video_url FROM lesson WHERE c_id = :course_id ORDER BY position ASC");
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->execute();

    $lessons = $stmt->fetchAll();

    echo '<pre>';
    print_r($lessons);
    echo '</pre>';
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
}
?>
