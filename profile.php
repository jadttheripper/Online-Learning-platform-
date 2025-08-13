<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';

// Fetch user data from the database
include 'connection.php';
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("location: login.php");
    exit();
}
$stmt = $conn->prepare("
    SELECT 
        c.c_id,
        c.title,
        c.description,
        COUNT(l.lesson_id) AS total_lessons,
        COUNT(lp.lesson_id) AS completed_lessons
    FROM course_progress cp
    JOIN course c ON cp.c_id = c.c_id
    LEFT JOIN lesson l ON l.c_id = c.c_id
    LEFT JOIN lesson_progress lp ON lp.lesson_id = l.lesson_id 
        AND lp.user_id = cp.user_id 
        AND lp.c_id = c.c_id 
        AND lp.is_completed = 1
    WHERE cp.user_id = ?
    GROUP BY c.c_id, c.title, c.description
");
$stmt->execute([$userId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate progress for each course
foreach ($courses as &$course) {
    $course['progress'] = $course['total_lessons'] > 0 
        ? round(($course['completed_lessons'] / $course['total_lessons']) * 100)
        : 0;
    
    // Determine progress color
    if ($course['progress'] <= 25) {
        $course['progress_color'] = 'red';
    } elseif ($course['progress'] <= 50) {
        $course['progress_color'] = 'yellow';
    } elseif ($course['progress'] <= 75) {
        $course['progress_color'] = 'lightgreen';
    } else {
        $course['progress_color'] = 'green';
    }
}
unset($course);

// Fetch user skills with titles and descriptions from the skills table
$stmt = $conn->prepare("
 SELECT
    s.skill_id,
    s.title AS skill_title,
    s.description AS skill_description,
    s.skill_category,
    s.image_url,
    us.user_skill_id,
    us.user_id,
    us.user_skill_description,
    c.c_id AS course_id,
    c.title AS course_title,
    c.description AS course_description
FROM skill s
INNER JOIN user_skill us
    ON s.skill_id = us.skill_id
LEFT JOIN course c
    ON c.user_skill_id = us.user_skill_id
WHERE us.user_id = ?


");
$stmt->execute([$userId]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch wishlist (learn later) skills
$stmt = $conn->prepare("
    SELECT s.skill_id, s.title, s.description, s.image_url, s.skill_category
    FROM wishlist_skills ws
    INNER JOIN skill s ON ws.skill_id = s.skill_id
    WHERE ws.user_id = ?
");
$stmt->execute([$userId]);
$wishlistSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);

$completionPoints = 0;
$totalPoints = 5; 

// Profile Picture
if (!empty($user['profile_pic']) && $user['profile_pic'] !== 'image/defaultavatar.jpg') {
    $completionPoints++;
}

// Education
if (!empty($user['education_institute'])) {
    $completionPoints++;
}

// Language
if (!empty($user['language_preference'])) {
    $completionPoints++;
}

// Skills
$skillCount = count($skills);
if ($skillCount >= 1) $completionPoints++;       // 1 point for at least one skill
if ($skillCount >= 3) $completionPoints++;       // Additional point for 3 or more skills

$completionPercent = round(($completionPoints / $totalPoints) * 100);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name']) ?>'s Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.js" defer></script>
    <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap | profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
</head>
<!-- Navbar -->
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
        <a href="index.php
" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
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
        <a href="index.php
" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
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
<style>.progress-ring__circle {
            transition: stroke-dashoffset 0.5s ease;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        .progress-red {
            stroke: #ef4444;
        }
        .progress-yellow {
            stroke: #f59e0b;
        }
        .progress-lightgreen {
            stroke: #86efac;
        }
        .progress-green {
            stroke: #10b981;
        }
        .skill-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .skill-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }</style>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<div x-data="{ show: true }" x-show="show" x-transition
     class="fixed top-4 left-1/2 transform -translate-x-1/2 w-[90%] max-w-xl bg-blue-100 border border-blue-300 text-blue-800 px-6 py-4 rounded-xl shadow-lg z-50">
    <div class="flex items-start justify-between space-x-4">
        <div>
            <h3 class="text-lg font-semibold mb-1">Complete Your Profile</h3>
            <p class="text-sm">Upload a profile picture, add your education, set a language preference, and include at least 3 skills to complete your profile!</p>
        </div>
        <button @click="show = false" class="text-blue-500 hover:text-blue-700 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 111.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>

<!-- Profile Container -->
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg mt-10">

  <!-- Row 1: Progress + Avatar -->
<div class="flex items-start justify-between">
  <!-- Avatar -->
  <img
    src="<?= htmlspecialchars($user['profile_pic']) ?>"
    alt="Profile Picture"
    class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md"
    onerror="this.src='image/defaultavatar.jpg'"
  >

  <!-- Profile Completion -->
  <div class="flex-1 text-right ml-6">
    <h2 class="text-lg font-semibold mb-1" style="text-align:center;">Profile Completion</h2>
    <div class="w-full bg-gray-200 rounded-full h-3 mb-1">
      <div
        class="bg-green-500 h-3 rounded-full"
        style="width: <?= $completionPercent ?>%;"
      ></div>
    </div>
    <p class="text-sm text-gray-600"><?= $completionPercent ?>% complete</p>
  </div>
</div>


  <!-- Row 2: Main Content -->
  <div class="flex items-start justify-between mt-8 gap-8">
    <!-- Left Column -->
    <div class="flex-1 space-y-6">
      <!-- Name & Badges -->
      <div class="space-y-2">
        <h1 class="text-3xl font-semibold"><?= htmlspecialchars($user['name']) ?></h1>
        <div class="flex flex-wrap gap-2">
          <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-sm">
            <i class="fas fa-school mr-1"></i>
            <?= htmlspecialchars($user['education_institute']) ?>
          </span>
          <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-600 rounded-full text-sm">
            <i class="fas fa-language mr-1"></i>
            <?= htmlspecialchars($user['language_preference']) ?>
          </span>
        </div>
      </div>
  <!-- 2) Render the skills list -->
  <div class="space-y-4" id="skillsList" x-data>
  <?php foreach ($skills as $skill):
      // Choose URL based on whether course_id is present
      if (!empty($skill['course_id'])) {
          $url = "view_courses.php?course_id=" . $skill['course_id'];
      } else {
          $url = "add_course.php?skill_id=" . $skill['skill_id'];
      }
  ?>
    <div
      class="bg-blue-50 p-4 rounded-xl flex justify-between items-start shadow-sm hover:shadow-lg transition cursor-pointer"
      onclick="window.location='<?= $url ?>';"
    >
      <div>
        <h3 class="text-lg font-semibold"><?= htmlspecialchars($skill['skill_title']) ?></h3>
        <p class="text-gray-600"> <?= htmlspecialchars(!empty($skill['user_skill_description']) ? $skill['user_skill_description'] : $skill['skill_description']) ?></p>
      </div>
      <form
        action="delete_skill.php"
        method="POST"
        class="ml-4"
        onsubmit="return confirm('Are you sure you want to delete this skill?');"
        onclick="event.stopPropagation();"
      >
        <input type="hidden" name="skill_id" value="<?= $skill['skill_id'] ?>">
        <button type="submit" class="text-red-500 hover:text-red-700">
          <i class="fas fa-trash-alt"></i>
        </button>
      </form>
    </div>
  <?php endforeach; ?>
</div>


<div class="flex space-x-4 mt-4 justify-center">
    <a href="edit_profile.php" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition">
        Edit Profile
    </a>
    <a href="#" id="addSkillBtn" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-700 transition">
        Add Skill
    </a>
</div>
    </div>
  </div>
  
</div>

<br>

<div class="w-full md:w-2/3 px-4">
<div class="flex justify-center mb-6 " style="text-align: center; align-items:center; justify-content:center">
        <h2 class="text-2xl font-bold">My Courses</h2>
    </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($courses as $course): ?>
                        <div class="skill-card bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-2 text-center"><?= htmlspecialchars($course['title']) ?></h3>
                                <div class="flex justify-center mb-4">
                                    <div class="relative w-24 h-24">
                                        <svg class="w-full h-full" viewBox="0 0 36 36">
                                            <!-- Background circle -->
                                            <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2"></circle>
                                            <!-- Progress circle -->
                                            <circle class="progress-ring__circle progress-<?= $course['progress_color'] ?>"
                                                cx="18" cy="18" r="16" fill="none" stroke-width="2"
                                                stroke-dasharray="100"
                                                stroke-dashoffset="<?= 100 - $course['progress'] ?>"></circle>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-lg font-bold"><?= $course['progress'] ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm mb-3"><?= htmlspecialchars($course['description']) ?></p>
                                <div class="text-xs text-gray-500">
                                    <?= $course['completed_lessons'] ?> of <?= $course['total_lessons'] ?> lessons completed
                                </div>
                                <a href="viewcourseasuser.php?course_id=<?= $course['c_id'] ?>" 
                                   class="block mt-4 text-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Continue Learning
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
</div>
<br>
<?php
$quotes = [
  "The expert in anything was once a beginner. ~Helen Hayes",
  "Live as if you were to die tomorrow. Learn as if you were to live forever. ~Mahatma Gandhi",
  "Success is the sum of small efforts, repeated day in and day out. ~Robert Collier",
  "You don't have to be great to start, but you have to start to be great. ~Zig Ziglar",
  "Learning never exhausts the mind. ~Leonardo da Vinci",
  "Education is the most powerful weapon which you can use to change the world. ~Nelson Mandela",
  "The beautiful thing about learning is that no one can take it away from you. ~B.B. King",
  "An investment in knowledge pays the best interest. ~Benjamin Franklin",
  "Your time is limited, so don't waste it living someone else's life. ~Steve Jobs",
  "Motivation is what gets you started. Habit is what keeps you going. ~Jim Rohn",
  "Never stop learning because life never stops teaching. ~Unknown",
  "The future belongs to those who learn more skills and combine them in creative ways. ~Robert Greene",
  "Push yourself, because no one else is going to do it for you. ~Unknown",
];
;

$randomQuote = $quotes[array_rand($quotes)];
?>
<div class="mb-8 px-4 py-6 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-900 rounded-xl shadow">
    <p class="text-center text-lg font-medium italic">💡 <?= $randomQuote ?></p>
</div>

<!-- Learn Later Section -->
<?php if (!empty($wishlistSkills)): ?>
<div class="mt-12">
  <h2 class="text-3xl font-bold text-purple-800 mb-6 flex items-center gap-2" style="justify-content: center;">
    <i class="fas fa-star text-yellow-400"></i> Learn Later
  </h2>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach ($wishlistSkills as $skill): ?>
  <a href="skill_detail.php?skill_id=<?= urlencode($skill['skill_id']) ?>" class="block group">
    <div class="relative bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 p-5 rounded-2xl shadow-lg border border-purple-300 hover:shadow-xl transition-all duration-300">

      <!-- Delete Icon Form -->
      <form action="delete_wishlist.php" method="POST" class="absolute top-2 right-2" onClick="event.stopPropagation();">
        <input type="hidden" name="skill_id" value="<?= htmlspecialchars($skill['skill_id']) ?>">
        <button type="submit" title="Remove from Wishlist" class="text-gray-500 hover:text-red-600 transition">
          <i class="fas fa-trash-alt"></i>
        </button>
      </form>

      <div class="flex items-center gap-4 mb-3">
        <img src="<?= htmlspecialchars($skill['image_url'] ?? 'image/skilldefault.png') ?>"
             alt="Skill Icon"
             class="w-12 h-12 rounded-full object-cover border-2 border-white shadow"
             onerror="this.src='image/skilldefault.png'">
        <div>
          <h3 class="text-lg font-semibold text-purple-900"><?= htmlspecialchars($skill['title']) ?></h3>
          <span class="text-xs text-purple-700 bg-purple-200 px-2 py-1 rounded-full"><?= htmlspecialchars($skill['skill_category']) ?></span>
        </div>
      </div>
      <p class="text-sm text-gray-700 italic">"<?= htmlspecialchars($skill['description']) ?>"</p>
    </div>
  </a>
<?php endforeach; ?>

  </div>
</div>
<?php endif; ?>



<script>
// 1) Prevent the delete button from bubbling up to the card click
document.addEventListener('DOMContentLoaded', function () {
  const addSkillBtn = document.getElementById('addSkillBtn');

  addSkillBtn.addEventListener('click', function (e) {
    const currentSkillCount = document.querySelectorAll('#skillsList > div').length;

    if (currentSkillCount >= 3) {
      alert('Maximum number of skills reached (3). Please delete a skill to add a new one.');
    } else {
      window.location.href = 'add_skill.php';
    }
  });
});
document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('click', e => e.stopPropagation());
});

// 2) Confirm deletion
function confirmDelete(event) {
  if (!confirm('Are you sure you want to delete this skill?')) {
    event.preventDefault();
    return false;
  }
  return true;
}
</script>


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
