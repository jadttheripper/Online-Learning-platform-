<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lesson['title']) ?> - Lesson</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg mt-8">
        <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($lesson['title']) ?></h1>
        <h2 class="text-xl mb-4">Course: <?= htmlspecialchars($lesson['course_title']) ?></h2>
        
        <!-- Lesson Content -->
        <div class="text-gray-700 mb-6">
            <p><?= nl2br(htmlspecialchars($lesson['content'])) ?></p>
        </div>

        <!-- Video if available -->
        <?php if (!empty($lesson['video_url'])): ?>
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Lesson Video</h3>
                <div class="video-container mb-4">
                    <iframe 
                        width="100%" 
                        height="315" 
                        src="<?= htmlspecialchars($lesson['video_url']) ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lesson Completion Status -->
        <div class="mt-4">
            <button id="markCompletedButton" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600 <?= $lesson['is_completed'] ? 'opacity-50 cursor-not-allowed' : '' ?>" <?= $lesson['is_completed'] ? 'disabled' : '' ?>>
                <?= $lesson['is_completed'] ? 'Completed' : 'Mark as Completed' ?>
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '#markCompletedButton', function() {
            const lessonId = <?= $lesson['lesson_id'] ?>;
            const button = $(this);

            // AJAX request to mark the lesson as completed
            $.post("mark_lesson_completed.php", { lesson_id: lessonId }, function(response) {
                const data = JSON.parse(response);

                if (data.success) {
                    // Update button to show completed status
                    button.text("Completed");
                    button.removeClass("bg-blue-500 hover:bg-blue-600").addClass("bg-green-500");
                    button.prop('disabled', true);  // Disable the button
                } else {
                    alert("Failed to mark lesson as completed.");
                }
            });
        });
    </script>
</body>
</html>
