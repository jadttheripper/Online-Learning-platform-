<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$userId = $_SESSION['user_id'];
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';

include 'connection.php';

$errors = [];
$success = false;

// Get course ID from URL
$course_id = isset($_GET['c_id']) ? (int)$_GET['c_id'] : 0;

if ($course_id <= 0) {
    die("Invalid course ID.");
}

// Fetch only the course matching the course_id
$stmt = $conn->prepare("SELECT c_id, title FROM course WHERE c_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $video_url = trim($_POST['video_url']);
    if (!empty($video_url)) {
        if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/', $video_url)) {
            $errors[] = 'Only YouTube URLs are allowed for the video.';
        }
    }
    
    // Use course_id from hidden input or form submission to avoid tampering
    $course_id_post = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;

    if (empty($title) || $course_id_post <= 0) {
        $errors[] = 'Title and course are required.';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO lesson (title, content, video_url, c_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $video_url, $course_id_post]);
            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Lesson</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<nav class="navbar">
  <div class="navbar-container">
    <!-- Logo or User Info -->
    <div class="navbar-left">
      <?php if ($userLoggedIn): ?>
        <div class="user-info cursor-pointer" onclick="window.location.href='profile.php';">
  <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="profile-pic" onerror="this.src='image/defaultavatar.jpg'">
  <div class="user-name"><?= htmlspecialchars($userName) ?></div>
</div>
      <?php else: ?>
        <h1 class="logo text-xl font-bold text-blue-600">SkillSwap</h1>
      <?php endif; ?>
    </div>

    <!-- Desktop Links -->
    <?php if ($userLoggedIn): ?>
      <div class="navbar-links left-links desktop-only">
        <a href="dashboard.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Dashboard</a>
        <a href="search.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
        <a href="index.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
        <a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About Us</a>
        <a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>
      </div>
      <div class="navbar-links right-icons desktop-only">
        <a href="messages.php" title="Messages"><i class="fas fa-comments"></i></a>
        <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
      </div>
    <?php else: ?>
      <div class="navbar-links left-links desktop-only">
        <a href="search.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
        <a href="index.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
        <a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About</a>
        <a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>
      </div>
      <div class="navbar-links right-icons desktop-only gap-2">
        <a href="login.php" class="px-4 py-1 border border-blue-600 text-blue-600 rounded hover:bg-green-600 hover:text-white transition">Login</a>
        <a href="signupuser.php" class="px-4 py-1 border border-green-600 text-green-600 rounded hover:bg-blue-700 hover:text-white transition">Sign Up</a>
      </div>
    <?php endif; ?>

    <!-- Hamburger Icon -->
    <button class="hamburger" onclick="toggleMenu()">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="navLinks">
    <?php if ($userLoggedIn): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="search.php">Search</a>
      <a href="index.php">Home</a>
      <a href="aboutus.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="messages.php"><i class="fas fa-comments"></i> Messages</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
      <a href="search.php">Search</a>
      <a href="index.php">Home</a>
      <a href="aboutus.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="login.php" class="text-blue-600 font-semibold hover:underline">Login</a>
      <a href="signupuser.php" class="text-white bg-blue-600 px-4 py-2 rounded hover:bg-white-700 transition">Sign Up</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Navbar Styles -->
<style>
  .navbar {
    background: white;
    color: #333;
    padding: 1rem;
    position: fixed;
    top: 0; /* Ensure it's at the top of the page */
    width: 100%;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  body {
    padding-top: 4rem; /* Make sure content doesn't hide behind the navbar */
}

  .navbar-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: auto;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-right: 2rem;
  }

  .profile-pic {
    width: 36px;
    height: 36px;
    border-radius: 50%;
  }

  .user-name {
    font-size: 0.9rem;
    font-weight: 600;
  }

  .navbar-links {
    display: flex;
    gap: 1rem;
    align-items: center;
  }

  .left-links {
    flex: 1;
    margin-left: 2rem;
  }

  .right-icons {
    display: flex;
    gap: 1rem;
    align-items: center;
  }

  .hamburger {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
  }

  .mobile-menu {
    display: none;
    flex-direction: column;
    background: white;
    padding: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }

  .mobile-menu.open {
    display: flex;
  }

  .mobile-menu a {
    display: block;
    margin-bottom: 0.75rem;
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #333;
    border-radius: 5px;
    transition: background-color 0.2s, color 0.2s;
  }

  .mobile-menu a:hover {
    background-color: #f0f0f0;
  }

  @media (max-width: 1024px) {
    .desktop-only {
      display: none;
    }

    .hamburger {
      display: block;
    }
  }
</style>

<!-- Navbar Toggle Script -->
<script>
  function toggleMenu() {
    const nav = document.getElementById('navLinks');
    nav.classList.toggle('open');
}

// Automatically close the mobile menu if window is resized above a certain width (e.g., 768px)
window.addEventListener('resize', () => {
    const nav = document.getElementById('navLinks');
    if (window.innerWidth > 768) {
        nav.classList.remove('open');
    }
});

</script>
<body class="bg-gray-100">

  <div class="flex justify-center items-center min-h-screen py-6">
    <div class="max-w-lg w-full bg-white p-8 rounded-xl shadow-lg">
      <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Add New <span class="text-indigo-600">Lesson</span></h1>

      <!-- Success Message -->
      <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">
        <strong>Success:</strong> Lesson added successfully!
        <script>
          setTimeout(function () {
            window.location.href = 'profile.php';
          }, 1500);
        </script>
      </div>
      <?php endif; ?>

      <!-- Error Message -->
      <?php if (!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
        <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Form -->
      <form method="post" class="space-y-6">

        <!-- Course Dropdown -->
        <div x-data="{ open: false }">
  <label class="block text-red-700 font-medium text-center">Course</label>
  <div class="relative">
    <!-- Show course title but disable select -->
    <select name="course_id" disabled class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="<?= $course['c_id'] ?>" selected><?= htmlspecialchars($course['title']) ?></option>
    </select>
    <!-- Hidden input to actually submit the course_id -->
    <input type="hidden" name="course_id" value="<?= $course['c_id'] ?>">
  </div>
</div>


        <!-- Lesson Title -->
        <div>
          <label class="block text-green-700 font-medium" style="text-align: center;">Lesson Title</label>
          <input type="text" name="title" class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <!-- Content -->
        <div>
          <label class="block text-pink-700 font-medium" style="text-align: center;">Content</label>
          <textarea name="content" class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"></textarea>
        </div>

        <!-- Video URL -->
        <div>
          <label class="block text-violet-700 font-medium" style="text-align: center;">Video URL (optional)</label>
          <input type="url" 
    name="video_url" 
    pattern="https?://(www\.)?(youtube\.com|youtu\.be)/.+" 
    title="Please enter a valid YouTube URL" 
    class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
          Add Lesson
        </button>
      </form>
    </div>
  </div>
  <footer class="bg-gray-900 text-white py-10 mt-10">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-center">
      <div class="mb-6 md:mb-0 text-center md:text-left">
        <h2 class="text-xl font-semibold mb-2">SkillSwap</h2>
        <p class="text-gray-400 text-sm">© <?= date('Y') ?> SkillSwap. All rights reserved.</p>
      </div>
      <div class="flex space-x-6 text-xl">
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
        <a href="mailto:contact@skillswap.com" class="text-gray-400 hover:text-white transition"><i class="fas fa-envelope"></i></a>
      </div>
    </div>
  </div>
</footer>
</body>

</html>

