<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'connection.php';


$userId = $_SESSION['user_id'];
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';

$courseId = $_GET['course_id'] ?? null;
if (!$courseId) {
    die('Course ID missing.');
}

// Fetch course info
$stmt = $conn->prepare("SELECT title, description FROM course WHERE c_id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$course) {
    die('Course not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($course['title']) ?> - Course</title>
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
        <a href="review.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
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
        <a href="review.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
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
      <a href="review.php">Home</a>
      <a href="aboutus.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="messages.php"><i class="fas fa-comments"></i> Messages</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
      <a href="search.php">Search</a>
      <a href="review.php">Home</a>
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
  <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($course['title']) ?></h1>
  <p class="text-gray-600 mb-6"><?= htmlspecialchars($course['description']) ?></p>

  <!-- Lessons Accordion -->
  <div x-data="lessonsAccordion(<?= $courseId ?>)" x-init="fetchLessons()" class="space-y-4">
    <template x-if="loading">
      <div class="text-center text-gray-500">Loading lessons...</div>
    </template>

    <template x-for="(lesson, index) in lessons" :key="lesson.lesson_id">
      <div class="border rounded-lg overflow-hidden">
        <!-- Accordion Header -->
        <div class="flex justify-between items-center">
          <button
            @click="toggle(index)"
            class="w-full px-4 py-2 bg-gray-200 flex justify-between items-center text-left focus:outline-none">
            <span class="font-semibold text-lg" x-text="lesson.title"></span>
            <i :class="openIndex === index ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
          </button>

          <!-- Edit and Delete Buttons -->
          <div class="flex space-x-2">
            <button @click="editLesson(lesson.lesson_id)" class="text-yellow-500">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button @click="deleteLesson(lesson.lesson_id)" class="text-red-500">
              <i class="fas fa-trash"></i> Delete
            </button>
          </div>
        </div>

        <!-- Accordion Content -->
        <div x-show="openIndex === index" x-collapse>
          <div class="p-4 bg-gray-50 space-y-4">
            <template x-if="lesson.video_url">
              <iframe class="w-full aspect-video rounded" 
                      :src="convertToEmbedUrl(lesson.video_url)" 
                      frameborder="0" 
                      allowfullscreen>
              </iframe>
            </template>
            <p class="text-gray-700 whitespace-pre-line" x-text="lesson.content"></p>
          </div>
        </div>
      </div>
    </template>

    <template x-if="error">
      <div class="text-red-500" x-text="error"></div>
    </template>
  </div>
  <!-- Add Lesson Button -->
  <div class="mt-6 flex justify-center">
  <a href="insert_lesson.php?c_id=<?= $courseId ?>" 
     class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition shadow-lg text-xl"
     title="Add Lesson">
    <i class="fas fa-plus"></i>
  </a>
</div>
</div>

<script>
  function lessonsAccordion(courseId) {
    return {
      lessons: [],
      loading: true,
      error: '',
      openIndex: null,
      
      fetchLessons() {
        fetch(`get_lesson.php?course_id=${courseId}`)
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              this.lessons = data.lessons;
            } else {
              this.error = data.message || "Failed to fetch lessons.";
            }
          })
          .catch(() => {
            this.error = "Unable to load lessons.";
          })
          .finally(() => {
            this.loading = false;
          });
      },

      toggle(idx) {
        this.openIndex = this.openIndex === idx ? null : idx;
      },

      // Convert YouTube URL to Embed URL
      convertToEmbedUrl(url) {
        const youtubeRegex = /(?:https?:\/\/)?(?:www\.)?youtube\.com\/(?:[^\/]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*\?v=)([a-zA-Z0-9_-]{11})/;
        const match = url.match(youtubeRegex);
        if (match && match[1]) {
          return `https://www.youtube.com/embed/${match[1]}`;
        }
        return url; // Return original URL if it's not YouTube
      },

      // Edit lesson (you can open a form or modal for editing)
      editLesson(lessonId) {
        alert(`Editing lesson with ID: ${lessonId}`);
        // Here you can redirect to an edit page or show a modal to edit the lesson
        window.location.href = `edit_lesson.php?lesson_id=${lessonId}`;
      },

      // Delete lesson
      deleteLesson(lessonId) {
        if (confirm("Are you sure you want to delete this lesson?")) {
          fetch(`delete_lesson.php?lesson_id=${lessonId}`, { method: 'DELETE' })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                alert('Lesson deleted successfully!');
                this.fetchLessons();  // Reload lessons after delete
              } else {
                alert('Failed to delete lesson.');
              }
            })
            .catch(() => alert('Error occurred while deleting the lesson.'));
        }
      }
    };
  }
</script>
<footer class="bg-gray-800 text-white py-14 mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
