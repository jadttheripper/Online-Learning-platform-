<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['course_id'])) {
    die("Unauthorized");
}

require_once 'connection.php';

function convertToEmbedUrl($url) {
    if (empty($url)) return null;
    // Only handle YouTube URLs
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0';
    }
    return null; // Return null for non-YouTube URLs
}

$courseId = (int)$_GET['course_id'];
$userId = $_SESSION['user_id'];

// 1. Get current course version and update time
$courseMetaStmt = $conn->prepare("
    SELECT c.version, c.last_updated, cp.course_version, cp.last_synced
    FROM course c
    LEFT JOIN course_progress cp ON cp.c_id = c.c_id AND cp.user_id = ?
    WHERE c.c_id = ?
");
$courseMetaStmt->execute([$userId, $courseId]);
$courseMeta = $courseMetaStmt->fetch(PDO::FETCH_ASSOC);

// 2. Check if synchronization is needed
$needsSync = false;
if (!$courseMeta || 
    !$courseMeta['last_synced'] || 
    $courseMeta['version'] > $courseMeta['course_version'] ||
    new DateTime($courseMeta['last_updated']) > new DateTime($courseMeta['last_synced'])) {
    $needsSync = true;
}

// 3. Synchronize if needed
if ($needsSync) {
    // Get current lessons in course
    $currentLessonsStmt = $conn->prepare("
        SELECT lesson_id FROM lesson 
        WHERE c_id = ? 
        ORDER BY position
    ");
    $currentLessonsStmt->execute([$courseId]);
    $currentLessonIds = $currentLessonsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get user's existing progress
    $userProgressStmt = $conn->prepare("
        SELECT lesson_id, is_completed 
        FROM lesson_progress 
        WHERE user_id = ? AND c_id = ?
    ");
    $userProgressStmt->execute([$userId, $courseId]);
    $userProgress = $userProgressStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Delete progress for removed lessons
        $deleteStmt = $conn->prepare("
            DELETE FROM lesson_progress 
            WHERE user_id = ? AND c_id = ? AND lesson_id NOT IN (" . 
            implode(',', array_fill(0, count($currentLessonIds), '?')) . ")
        ");
        $deleteParams = array_merge([$userId, $courseId], $currentLessonIds);
        $deleteStmt->execute($deleteParams);
        
        // Insert progress for new lessons
        $newLessonIds = array_diff($currentLessonIds, array_keys($userProgress));
        if (!empty($newLessonIds)) {
            $insertStmt = $conn->prepare("
                INSERT INTO lesson_progress (user_id, c_id, lesson_id, is_completed)
                VALUES (?, ?, ?, 0)
            ");
            foreach ($newLessonIds as $lessonId) {
                $insertStmt->execute([$userId, $courseId, $lessonId]);
            }
        }
        
        // Update course progress version
        $updateStmt = $conn->prepare("
            UPDATE course_progress 
            SET course_version = ?, last_synced = NOW()
            WHERE user_id = ? AND c_id = ?
        ");
        $updateStmt->execute([
            $courseMeta['version'],
            $userId,
            $courseId
        ]);
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Sync failed: " . $e->getMessage());
    }
}

// Get course details
$courseStmt = $conn->prepare("
    SELECT c.c_id, c.title, c.description, c.user_skill_id,
           COUNT(l.lesson_id) as total_lessons,
           COUNT(lp.lesson_id) as completed_lessons
    FROM course c
    LEFT JOIN lesson l ON l.c_id = c.c_id
    LEFT JOIN lesson_progress lp ON lp.lesson_id = l.lesson_id AND lp.user_id = ? AND lp.is_completed = 1
    WHERE c.c_id = ?
    GROUP BY c.c_id
");
$courseStmt->execute([$userId, $courseId]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

// Get lessons with completion status
$lessonStmt = $conn->prepare("
    SELECT l.lesson_id, l.title, l.content, l.video_url, l.video_source_type, l.position,
           l.lesson_type, lp.is_completed
    FROM lesson l
    LEFT JOIN lesson_progress lp ON lp.lesson_id = l.lesson_id AND lp.user_id = ? AND lp.c_id = ?
    WHERE l.c_id = ?
    ORDER BY l.position
");
$lessonStmt->execute([$userId, $courseId, $courseId]);
$lessons = $lessonStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate progress
$progressPercentage = $course['total_lessons'] > 0 
    ? round(($course['completed_lessons'] / $course['total_lessons']) * 100) 
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']) ?> - Course</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .lesson-content { display: none; }
        .lesson-content.active { display: block; }
        .progress-bar { transition: width 0.5s ease; }
        .mark-done-btn.loading { opacity: 0.7; pointer-events: none; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto mt-24 p-6 bg-white rounded-lg shadow">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium">Course Progress</span>
                <span class="text-sm font-medium"><?= $progressPercentage ?>%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" 
                     style="width: <?= $progressPercentage ?>%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                <?= $course['completed_lessons'] ?> of <?= $course['total_lessons'] ?> lessons completed
            </p>
        </div>

        <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($course['title']) ?></h1>
        <p class="text-gray-600 mb-6"><?= htmlspecialchars($course['description']) ?></p>

        <?php if (empty($lessons)): ?>
            <div class="text-center py-8 text-gray-500">No lessons available yet.</div>
        <?php else: ?>
            <?php foreach ($lessons as $index => $lesson): ?>
                <div class="border rounded-lg overflow-hidden bg-white shadow mb-4">
                    <div class="flex justify-between items-center p-4 bg-gray-100 cursor-pointer lesson-header"
                         onclick="toggleLesson(<?= $index ?>)">
                        <div>
                            <span class="font-semibold"><?= htmlspecialchars($lesson['title']) ?></span>
                            <?php if ($lesson['is_completed']): ?>
                                <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                    <i class="fas fa-check"></i> Completed
                                </span>
                            <?php endif; ?>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    
                    <div class="lesson-content" id="lesson-<?= $index ?>">
                        <div class="p-4 space-y-4">
                        <?php if (!empty($lesson['video_url'])): 
    $embedUrl = convertToEmbedUrl($lesson['video_url']);
    if ($embedUrl): ?>
        <iframe class="w-full aspect-video rounded" 
                src="<?= $embedUrl ?>" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen></iframe>
    <?php endif; ?>
<?php endif; ?>
                            
                            <div class="prose max-w-none">
                                <?= nl2br(htmlspecialchars($lesson['content'])) ?>
                            </div>
                            
                            <div class="pt-4">
                                <button class="mark-done-btn px-4 py-2 rounded text-white 
                                    <?= $lesson['is_completed'] ? 'bg-gray-400' : 'bg-green-500 hover:bg-green-600' ?>"
                                        data-lesson-id="<?= $lesson['lesson_id'] ?>"
                                        <?= $lesson['is_completed'] ? 'disabled' : '' ?>
                                        onclick="markLessonDone(this, <?= $index ?>)">
                                    <?= $lesson['is_completed'] ? 
                                        '<i class="fas fa-check"></i> Completed' : 
                                        'Mark as Done' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        // Toggle lesson visibility
        function toggleLesson(index) {
            const content = document.getElementById(`lesson-${index}`);
            const header = document.querySelectorAll('.lesson-header')[index];
            const icon = header.querySelector('i');
            
            content.classList.toggle('active');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        }

        // Mark lesson as done
        async function markLessonDone(button, index) {
            if (button.disabled) return;
            
            const lessonId = button.dataset.lessonId;
            const originalHTML = button.innerHTML;
            
            button.disabled = true;
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing';
            
            try {
                const response = await fetch('markasdone.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        lesson_id: lessonId,
                        course_id: <?= $courseId ?>
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update UI
                    button.innerHTML = '<i class="fas fa-check"></i> Completed';
                    button.classList.remove('bg-green-500', 'hover:bg-green-600');
                    button.classList.add('bg-gray-400');
                    
                    // Update progress display
                    location.reload();
                } else {
                    throw new Error(data.message || 'Failed to mark lesson as done');
                }
            } catch (error) {
                button.disabled = false;
                button.classList.remove('loading');
                button.innerHTML = originalHTML;
                alert('Error: ' + error.message);
            }
        }

        // Open first lesson by default
        document.addEventListener('DOMContentLoaded', () => {
            if (document.querySelector('.lesson-content')) {
                toggleLesson(0);
            }
        });
    </script>
</body>
</html>