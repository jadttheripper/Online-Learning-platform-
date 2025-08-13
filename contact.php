<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize inputs using strip_tags() and trim()
  $name = isset($_POST['name']) ? ((trim($_POST['name']))) : '';
  $email = isset($_POST['email']) ? trim(strip_tags(trim($_POST['email']))) : '';
  $subject = isset($_POST['subject']) ? trim(strip_tags(trim($_POST['subject']))) : '';
  $message = isset($_POST['message']) ? trim(strip_tags(trim($_POST['message']))) : '';

  // Validate email format
  if ($name && $email && $subject && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO contact(name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    // Set success message and redirect
    $_SESSION['contact_success'] = true;
    header("Location: contact.php");
    exit;
  } else {
    // Set error message
    $_SESSION['contact_error'] = "Please fill in all fields correctly.";
  }
}


$contactSuccess = $_SESSION['contact_success'] ?? false;
$contactError = $_SESSION['contact_error'] ?? '';
unset($_SESSION['contact_success'], $_SESSION['contact_error']);

$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';



?>
<!-- Navbar -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap | contact</title>
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

<!-- Hero Section -->
<section class="page-hero">
  <h1>Contact Us</h1>
</section>

<div class="container">
  <!-- Contact Info Section -->
  <!-- Contact Section -->
   
  <section class="relative pt-28 pb-24 px-6 md:px-12 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=1600&q=80');">

  <div class="absolute inset-0 bg-black opacity-60"></div>
  
  <div class="relative z-0 max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-start text-white">

    <!-- Contact Info -->
    <div class="space-y-6">
      <h3 class="text-4xl font-extrabold">We'd Love to Hear From You!</h3>
      <p class="text-lg text-gray-300">
        Have questions, suggestions, or want to say hi? Fill out the form or reach us through our contact details below.
      </p>

      <div class="mt-6 space-y-3 text-gray-300">
        <p><i class="fas fa-envelope mr-2 text-yellow-400"></i> MohammadKaadan@gmail.com</p>
        <p><i class="fas fa-phone-alt mr-2 text-yellow-400"></i> +96170412276</p>
        <p><i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i> Salim Salam St., Beirut</p>
      </div>
    </div>

    <!-- Contact Form -->
    <!-- Contact Form -->
<div class="bg-white p-8 rounded-2xl shadow-2xl text-gray-800">
  <h2 class="text-2xl font-bold mb-6">Contact Form</h2>
  <form method="post" action="contact.php" novalidate id="contactForm" class="space-y-5">

  <?php if ($contactSuccess): ?>
  <div class="bg-green-500 text-white p-4 rounded mb-4">Your message was sent successfully!</div>
<?php elseif ($contactError): ?>
  <div class="bg-red-500 text-white p-4 rounded mb-4"><?= htmlspecialchars($contactError) ?></div>
<?php endif; ?>

    
    <!-- Full Name -->
    <div class="relative">
      <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
      <input type="text" id="name" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <i class="fas fa-user absolute left-3 top-10 text-gray-400"></i>
    </div>
    
    <!-- Email -->
    <div class="relative">
      <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
      <input type="email" id="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <i class="fas fa-envelope absolute left-3 top-10 text-gray-400"></i>
    </div>
    
    <!-- Subject -->
    <div class="relative">
      <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
      <input type="text" id="subject" name="subject" required class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <i class="fas fa-pen absolute left-3 top-10 text-gray-400"></i>
    </div>
    
    <!-- Message -->
    <div class="relative">
      <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Your Message</label>
      <textarea id="message" name="message" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
    </div>
    
    <!-- Submit -->
    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-300">
      Send Message
    </button>
  </form>

  <!-- Message Sent Icon (Hidden by Default) -->
  <div id="messageSent" class="hidden flex items-center justify-center mt-6 bg-green-500 text-white p-4 rounded-lg shadow-lg">
    <i class="fas fa-check-circle mr-3"></i> <span>Your message has been sent successfully!</span>
  </div>
</div>



    </div>

  </div>
</section>

<!-- Footer -->
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


<!-- Custom Validation Script -->
<script>
  const form = document.getElementById('contactForm');
  form.addEventListener('submit', function (e) {
    let valid = true;

    // Check all required fields
    ['name', 'email', 'subject', 'message'].forEach(field => {
      const input = document.getElementById(field);
      const error = document.getElementById(field + 'Error');
      if (!input.value.trim()) {
        error.classList.remove('hidden');
        valid = false;
      } else {
        error.classList.add('hidden');
      }
    });

    // If form is not valid, prevent default submit
    if (!valid) {
      e.preventDefault();
      return;
    }

    // Simulate successful form submission
    e.preventDefault(); // Prevent default form submission for demo
    setTimeout(function () {
      // After successful submission, show the success message
      document.getElementById('messageSent').classList.remove('hidden');
      form.reset(); // Reset the form fields
    }, 1000); // Simulated delay (e.g., server-side response delay)
  });
</script>



</body>
</html>
