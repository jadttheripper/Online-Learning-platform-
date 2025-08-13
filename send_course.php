<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    header('Location: login.php');
    exit();
}

$currentUserId = (int)$_SESSION['user_id'];
$receiverId = (int)$_GET['receiver_id'];

// Fetch user's skills
$stmt = $conn->prepare("SELECT skill_id FROM user_skill WHERE user_id = ?");
$stmt->execute([$currentUserId]);
$userSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$userSkills) {
    die("You don't have any skills assigned.");
}

// Fetch course IDs based on the user's skills
$skillIds = array_column($userSkills, 'skill_id');
$placeholders = implode(',', array_fill(0, count($skillIds), '?'));

// Fetch courses
$stmt = $conn->prepare("
    SELECT c.c_id, c.title, c.description
    FROM course c
    JOIN user_skill us ON c.user_skill_id = us.user_skill_id
    WHERE us.skill_id IN ($placeholders) AND us.user_id = ?
");
$stmt->execute(array_merge($skillIds, [$currentUserId]));
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Correct Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-2xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Send a Course</h1>

        <?php if ($courses): ?>
            <ul class="space-y-4">
                <?php foreach ($courses as $course): ?>
                    <li>
                        <form method="POST" action="send_course_message.php">
                            <input type="hidden" name="course_id" value="<?= $course['c_id'] ?>">
                            <input type="hidden" name="receiver_id" value="<?= $receiverId ?>">
                            <button type="submit"
                                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                                <?= htmlspecialchars($course['title']) ?>
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-gray-600">No courses found for your skills.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="messages.php?receiver_id=<?= $receiverId ?>" class="text-blue-500 hover:underline">
                ← Back to Messages
            </a>
        </div>
    </div>
</body>
</html>
