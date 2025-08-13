<?php
session_start();
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';
?>
<!-- Navbar -->
<!DOCTYPE html>
<html lang="en" class="ht-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap | about</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    html {
      scroll-behavior: smooth;
    }
    .scroll-indicator {
      animation: bounce 2s infinite;
    }
    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-5px);
      }
    }
  </style>
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
        <a href="dashboard.php"class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Dashboard</a>
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
      <a href="dashboard.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Dashboard</a>
      <a href="search.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
      <a href="index.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
      <a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About Us</a>
      <a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>
      <a href="messages.php" ><i class="fas fa-comments"></i> Messages</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
      <a href="search.php"class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
      <a href="index.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
      <a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About</a>
      <a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>
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

<body class="bg-gradient-to-b from-white via-blue-50 to-purple-50 text-gray-800 font-sans flex flex-col min-h-screen">
   <!-- Hero Section -->
   <section class="relative h-screen flex items-center justify-center text-center bg-cover bg-center px-4"
           style="background-image: url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1350&q=80');">
    <div class="bg-white/70 backdrop-blur-md p-10 rounded-2xl shadow-xl max-w-2xl" x-show="reveal" x-transition.duration.1000ms>
      <h1 class="text-5xl font-extrabold text-blue-700 mb-4">Welcome to <span class="text-purple-600">SkillSwap</span></h1>
      <p class="text-lg text-gray-700 mb-6">Your Learning and Sharing Platform</p>
      <a href="#about" class="scroll-indicator inline-block text-blue-600 text-3xl mt-4">
        <i class="fa-solid fa-chevron-down"></i>
      </a>
    </div>
  </section>

  <main class="max-w-7xl mx-auto px-6 py-20 space-y-24" id="about">

    <!-- Our Story -->
    <section x-show="reveal" x-transition.delay.100ms.duration.800ms>
      <h2 class="text-3xl font-bold text-blue-800 mb-4">Our Story</h2>
      <p class="text-gray-700 text-lg leading-relaxed max-w-3xl">
        SkillSwap was born out of a simple idea: to make learning and sharing skills accessible to everyone. Our founders recognized the need for a platform that bridges the gap between teachers and learners worldwide.
      </p>
    </section>

    <!-- Our Values -->
    <section x-show="reveal" x-transition.delay.300ms.duration.800ms>
      <h3 class="text-2xl font-bold text-purple-700 mb-6">Our Core Values</h3>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105">
          <i class="fa-solid fa-users text-blue-500 text-3xl mb-4"></i>
          <h4 class="text-xl font-semibold text-blue-600 mb-2">Community</h4>
          <p class="text-gray-600">A global network of learners and teachers connected by passion.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105">
          <i class="fa-solid fa-handshake text-blue-500 text-3xl mb-4"></i>
          <h4 class="text-xl font-semibold text-blue-600 mb-2">Integrity</h4>
          <p class="text-gray-600">Transparency and honesty define everything we do.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105">
          <i class="fa-solid fa-bolt text-blue-500 text-3xl mb-4"></i>
          <h4 class="text-xl font-semibold text-blue-600 mb-2">Empowerment</h4>
          <p class="text-gray-600">We help people grow skills and confidence to transform their lives.</p>
        </div>
      </div>
    </section>

    <!-- What We Do -->
    <section x-show="reveal" x-transition.delay.500ms.duration.800ms>
      <h3 class="text-2xl font-bold text-purple-700 mb-4">What We Do</h3>
      <p class="text-lg text-gray-700 max-w-3xl leading-relaxed">
        SkillSwap connects passionate individuals who want to share what they know with those eager to learn. Whether it's coding, design, music, or language — we help people teach what they love and grow in the process.
      </p>
    </section>

    <!-- Our Impact -->
    <section x-show="reveal" x-transition.delay.700ms.duration.800ms>
      <h3 class="text-2xl font-bold text-purple-700 mb-6">Our Impact</h3>
      <div class="grid md:grid-cols-3 gap-6 text-center">
        <div class="bg-white p-8 rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105">
          <h4 class="text-4xl font-bold text-blue-500">5,000+</h4>
          <p class="mt-2 text-gray-600">Active Users</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105">
          <h4 class="text-4xl font-bold text-blue-500">300+</h4>
          <p class="mt-2 text-gray-600">Skills Offered</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105">
          <h4 class="text-4xl font-bold text-blue-500">200+</h4>
          <p class="mt-2 text-gray-600">Partnerships Formed</p>
        </div>
      </div>
    </section>

   
   

  </main>
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

 
