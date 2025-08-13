<?php
session_start();
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$userId = $_SESSION['user_id'];
$message = '';
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';
if (!isset($_GET['skill_id'])) {
    die('Skill ID is missing.');
}

$skillId = intval($_GET['skill_id']);
if ($skillId <= 0) {
    die('Invalid skill ID.');
}
// Fetch skill details
$stmt = $conn->prepare("SELECT title FROM skill WHERE skill_id = ?");
$stmt->execute([$skillId]);
$skill = $stmt->fetch();

if (!$skill) {
    die('Skill not found.');
}

// Fetch users associated with the skill
$stmt = $conn->prepare("
    SELECT u.user_id, u.name, u.email, u.profile_pic, u.education_institute, u.language_preference
    FROM user u
    INNER JOIN user_skill us ON u.user_id = us.user_id
    WHERE us.skill_id = ?
");
$stmt->execute([$skillId]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$userIds = array_column($users, 'user_id');
$placeholders = rtrim(str_repeat('?,', count($userIds)), ',');

// Fetch user skills
$skillStmt = $conn->prepare("
    SELECT us.user_id, s.title 
    FROM user_skill us
    JOIN skill s ON us.skill_id = s.skill_id
    WHERE us.user_id IN ($placeholders)
");
$skillStmt->execute($userIds);
$skillResults = $skillStmt->fetchAll(PDO::FETCH_ASSOC);

// Group skills by user_id
$userSkillsMap = [];
foreach ($skillResults as $row) {
    $userSkillsMap[$row['user_id']][] = $row['title'];
}
?>

<!DOCTYPE html>
<html lang="en" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users for Skill: <?= htmlspecialchars($skill['title']) ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>


<body class="bg-gray-100 min-h-screen p-6 ">
<nav class="navbar pt-1">
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
    margin-top: 0px;
    top:0;
    width: 100%;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    <div class="container mx-auto max-w-4xl bg-white shadow-lg rounded-lg p-8 mt-12">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">
            Users with skill: <span class="text-indigo-600"><?= htmlspecialchars($skill['title']) ?></span>
        </h1>

        <?php if (count($users) === 0): ?>
            <div class="alert alert-warning text-center">
                No users are associated with this skill.
            </div>
        <?php else: ?>
            <div class="grid gap-4 md:grid-cols-2">
            <?php foreach ($users as $user):  
    $avatarUrl = !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'image/defaultavatar.jpg';
    $education = !empty($user['education_institute']) ? htmlspecialchars($user['education_institute']) : 'Not specified';
    $language = !empty($user['language_preference']) ? htmlspecialchars($user['language_preference']) : 'Not specified';
    $skills = isset($userSkillsMap[$user['user_id']]) ? $userSkillsMap[$user['user_id']] : [];
?>
    <div class="flex items-start space-x-4 p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all">
        <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-14 h-14 rounded-full object-cover border-2 border-indigo-500">

        <div class="flex-1">
            <h2 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h2>
            <p class="text-sm text-gray-600 mb-3"><?= htmlspecialchars($user['email']) ?></p>

            <div class="text-sm text-gray-700 space-y-2 mb-2">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                    <span><?= $education ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-language text-green-600"></i>
                    <span><?= $language ?></span>
                </div>
            </div>

            <?php if (!empty($skills)): ?>
                <div class="text-xs text-gray-600 mt-1">
                    <span class="font-medium text-gray-900">Other Skills:</span>
                    <span><?= htmlspecialchars(implode(', ', $skills)) ?></span>
                </div>
            <?php endif; ?>
            <!-- Message Button -->
            <a href="messages.php?receiver_id=<?= urlencode($user['user_id']) ?>" class="inline-flex items-center text-sm text-blue-600 hover:text-yellow-600 font-medium mt-2">
                <i class="fa-solid fa-message mr-2"></i> Message User
            </a>
        </div>
    </div>
<?php endforeach; ?>




            </div>
        <?php endif; ?>
        <div class="mt-6 text-center">
        <a href="profile.php" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-green-700 transition">
            Back to profile
        </a>
    </div>
    </div>
    
    <footer class="bg-gray-900 text-white py-10 mt-10" style="width: 100%;">
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
