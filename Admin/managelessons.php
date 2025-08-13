<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch lessons
$lessonQuery = $conn->prepare("SELECT * FROM lesson ORDER BY c_id, position");
$lessonQuery->execute();
$lessons = $lessonQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch progress info per lesson
$progressQuery = $conn->prepare("SELECT lesson_id, is_completed FROM lesson_progress");
$progressQuery->execute();
$progressData = $progressQuery->fetchAll(PDO::FETCH_ASSOC);

$lessonProgressMap = [];
foreach ($progressData as $row) {
    if (!isset($lessonProgressMap[$row['lesson_id']])) {
        $lessonProgressMap[$row['lesson_id']] = ['completed' => 0, 'total' => 0];
    }
    $lessonProgressMap[$row['lesson_id']]['total']++;
    if ($row['is_completed']) {
        $lessonProgressMap[$row['lesson_id']]['completed']++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Lessons</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            padding: 20px;
            background-color: #f9f9f9;
        }
        iframe {
            max-width: 100%;
            height: 300px;
            border: none;
        }
        .lesson-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .lesson-header {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Manage Lessons</h2>

        <?php foreach ($lessons as $lesson): ?>
            <div class="lesson-card">
                <h4 class="lesson-header">
                    Course ID: <?= htmlspecialchars($lesson['c_id']) ?> |
                    Lesson ID: <?= htmlspecialchars($lesson['lesson_id']) ?> |
                    Position: <?= htmlspecialchars($lesson['position']) ?> |
                    Type: <?= htmlspecialchars($lesson['lesson_type']) ?>
                </h4>
                <h3><?= htmlspecialchars($lesson['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($lesson['content'])) ?></p>

                <?php if (!empty($lesson['video_url'])): ?>
                    <div>
                        <strong>Video Source: </strong><?= htmlspecialchars($lesson['video_source_type']) ?><br>
                        <iframe src="<?= htmlspecialchars($lesson['video_url']) ?>" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <p>
                    <strong>Created At:</strong> <?= htmlspecialchars($lesson['created_at']) ?><br>
                    <strong>Completion:</strong>
                    <?php
                        $lid = $lesson['lesson_id'];
                        if (isset($lessonProgressMap[$lid])) {
                            $data = $lessonProgressMap[$lid];
                            echo $data['completed'] . ' of ' . $data['total'] . ' completed';
                        } else {
                            echo 'No progress recorded';
                        }
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
