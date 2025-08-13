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

$lessonId = $_GET['lesson_id'] ?? null;

if (!$lessonId) {
    die('Lesson ID missing.');
}

// Fetch the existing lesson data
$stmt = $conn->prepare("SELECT * FROM lesson WHERE lesson_id = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    die('Lesson not found.');
}
function isYouTubeUrl($url) {
    return preg_match('/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/', $url);
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $title = htmlspecialchars(trim($_POST['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content'] ?? ''), ENT_QUOTES, 'UTF-8');
    $video_url = htmlspecialchars(trim($_POST['video_url'] ?? ''), ENT_QUOTES, 'UTF-8');

if (!empty($video_url) && !isYouTubeUrl($video_url)) {
    $error = 'Only YouTube video URLs are allowed.';
}


    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE lesson SET title = ?, content = ?, video_url = ? WHERE lesson_id = ?");
            $stmt->execute([$title, $content, $video_url, $lessonId]);

            header('Location:profile.php');
            exit();
        } catch (PDOException $e) {
            $error = 'Database error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Lesson - <?= htmlspecialchars($lesson['title']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
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

<div class="max-w-4xl mx-auto mt-24 p-6 bg-white rounded-lg shadow">
  <h1 class="text-3xl font-bold mb-4 text-indigo-500" style="text-align: center;">Edit Lesson: <?= htmlspecialchars($lesson['title']) ?></h1>

  <?php if (isset($error)): ?>
    <div class="bg-red-500 text-white p-4 mb-4 rounded"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form action="" method="POST">
    <div class="mb-4">
      <label for="title" class="block text-m font-semibold text-aqua-500" style="text-align:center;">Title</label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" class="w-full p-3 border rounded-lg mt-1" required>
    </div>

    <div class="mb-4">
      <label for="content" class="block text-m font-semibold text-yellow-500" style="text-align:center;">Content</label>
      <textarea id="content" name="content" class="w-full p-3 border rounded-lg mt-1" rows="6" required><?= htmlspecialchars($lesson['content']) ?></textarea>
    </div>

    <div class="mb-4">
      <label for="video_url" class="block text-m font-bold text-red-500" style="text-align:center; ">Video URL (Optional)</label>
      <input type="url" id="video_url" name="video_url" pattern="https?://(www\.)?(youtube\.com|youtu\.be)/.+"
  title="Please enter a valid YouTube URL"
  value="<?= htmlspecialchars($lesson['video_url'], ENT_QUOTES, 'UTF-8') ?>"
  class="w-full p-3 border rounded-lg mt-1" class="w-full p-3 border rounded-lg mt-1">
    </div>

    <div class="flex justify-between items-center">
      <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg">Save Changes</button>
      <a href="profile.php" class=" bg-blue-500 text-white px-6 py-2 rounded-lg">Cancel</a>
    </div>
  </form>
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
