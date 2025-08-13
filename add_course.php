<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$skillId = isset($_GET['skill_id']) ? (int) $_GET['skill_id'] : null;

if (!$skillId) {
    die("No skill selected.");
}

// Fetch skill details
$stmt = $conn->prepare("SELECT * FROM skill WHERE skill_id = ?");
$stmt->execute([$skillId]);
$skill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$skill) {
    die("Skill not found.");
}

// Fetch user's association with the skill
$stmt = $conn->prepare("SELECT user_skill_id FROM user_skill WHERE user_id = ? AND skill_id = ?");
$stmt->execute([$userId, $skillId]);
$userSkill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userSkill) {
    die("You haven't added this skill yet.");
}

$userSkillId = $userSkill['user_skill_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseTitle = isset($_POST['course_title']) ? strip_tags(trim($_POST['course_title'])) : '';
    $courseDescription = isset($_POST['course_description']) ? strip_tags(trim($_POST['course_description'])) : '';

    if (empty($courseTitle)) {
        $error = "Course title is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO course (title, description, user_skill_id) VALUES (?, ?, ?)");
        $stmt->execute([$courseTitle, $courseDescription, $userSkillId]);

        $courseId = $conn->lastInsertId();
        header("Location:profile.php"); // Now points to new lesson handler
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Course</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
  <div class="bg-white p-8 rounded shadow-lg w-full max-w-lg">
    <h1 class="text-2xl font-bold mb-6 text-center text-blue-600">
      Add a New Course for "<?php echo htmlspecialchars($skill['title']); ?>"
    </h1>

    <?php if (!empty($error)): ?>
      <div class="mb-4 text-red-600 font-semibold text-center">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form action="add_course.php?skill_id=<?php echo $skillId; ?>" method="POST" class="space-y-5">
      <div>
        <label for="course_title" class="block text-sm font-medium text-gray-700">Course Title</label>
        <input type="text" name="course_title" id="course_title" required
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
      </div>

      <div>
        <label for="course_description" class="block text-sm font-medium text-gray-700">Course Description</label>
        <textarea name="course_description" id="course_description" rows="4"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                  placeholder="Optional description about your course..."></textarea>
      </div>

      <div class="flex justify-between items-center">
        <a href="profile.php" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-all">
          Add Course
        </button>
      </div>
    </form>
  </div>
</body>
</html>
