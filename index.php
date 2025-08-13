<?php
// home 

 

session_start();
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';
include 'connection.php';
include 'unreadcheck.php';
$userLoggedIn = isset($_SESSION['user_id']);
$hasUnreadMessages = false;

if ($userLoggedIn) {
    $loggedInUserId = $_SESSION['user_id'];
    $hasUnreadMessages = hasUnreadMessages($conn, $loggedInUserId);
}


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap | Home</title>
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
        <a href="messages.php" title="Messages" class="message-icon-wrapper position-relative"><i class="fas fa-comments"></i>  <?php if ($hasUnreadMessages): ?>
    <span class="notification-dot"></span>
  <?php endif; ?></a>
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
      <a href="dashboard.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Dashboard</a>
<a href="search.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
<a href="review.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
<a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About Us</a>
<a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>

<a href="messages.php" title="Messages" class="message-icon-wrapper position-relative"><i class="fas fa-comments"></i>  <?php if ($hasUnreadMessages): ?>
    <span class="notification-dot"></span>
  <?php endif; ?></a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
      <a href="search.php">Search</a>
      <a href="review.php">Home</a>
      <a href="aboutus.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="login.php" class="text-blue-600 font-semibold hover:bg-green hover:text-white-600 transition">Login</a>
      <a href="signupuser.php" class="text-white bg-green-600 px-4 py-2 rounded hover:bg-white-700 transition">Sign Up</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Navbar Styles -->
<style>
  
  .message-icon-wrapper {
  position: relative;
  display: inline-block;
}

.notification-dot {
  position: absolute;
  top: 2px;       /* Adjust this based on icon size */
  right: 2px;     /* Adjust this as needed */
  height: 8px;
  width: 8px;
  background-color: red;
  border-radius: 50%;
  border: 2px solid white; /* Gives a clean separation */
  z-index: 2;
}


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
<section 
  x-data="{ show: false }" 
  x-init="setTimeout(() => show = true, 200)" 
  x-show="show" 
  x-transition:enter="transition ease-out duration-700" 
  x-transition:enter-start="opacity-0 translate-y-10" 
  x-transition:enter-end="opacity-100 translate-y-0"
  class="pt-32 pb-20 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-center"
>
  <?php if ($userLoggedIn): ?>
    <h1 class="text-4xl md:text-5xl font-bold mb-4">
      Welcome back, <?= htmlspecialchars($userName) ?>!
    </h1>
    <p class="text-lg md:text-xl mb-6">
      confused about learning a new skill? enter the dashboard and let us recommend a skill for you 
    </p>
    <a href="dashboard.php" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded hover:bg-gray-100 transition">
      Go to Dashboard
    </a>
  <?php else: ?>
    <h1 class="text-4xl md:text-5xl font-bold mb-4">
      Empower Your Learning Journey
    </h1>
    <p class="text-lg md:text-xl mb-6">
      Find, teach, and grow your skills in an interactive, enhanced community.
    </p>
    <a href="signupuser.php" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded hover:bg-gray-100 transition">
      Get Started
    </a>
  <?php endif; ?>
</section>

<!-- Features Section -->
<section class="py-16 px-6 bg-white">
    <h2 class="text-3xl font-bold text-center mb-12">What Makes SkillSwap Unique?</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <div class="bg-gray-100 p-6 rounded-lg text-center shadow hover:shadow-lg transition">
            <i class="fa-solid fa-users text-blue-500 text-4xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Interactive Community</h3>
            <p>Connect with learners and mentors to share and exchange valuable skills.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg text-center shadow hover:shadow-lg transition">
            <i class="fa-solid fa-robot text-purple-500 text-4xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Skill recommendation</h3>
            <p>Personalized learning powered by skill reccomendations for better user guidance</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg text-center shadow hover:shadow-lg transition">
            <i class="fa-solid fa-comments text-green-500 text-4xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Real-time Chat</h3>
            <p>Chat with other users to collaborate, ask questions, and exchange tips instantly.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg text-center shadow hover:shadow-lg transition">
            <i class="fa-solid fa-book-open text-red-500 text-4xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Share Your Courses</h3>
            <p>Create and share your own courses and skill paths with the community.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg text-center shadow hover:shadow-lg transition">
            <i class="fa-solid fa-chart-line text-yellow-500 text-4xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Track Progress</h3>
            <p>Stay on top of your goals with intuitive course tracking</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg text-center shadow hover:shadow-lg transition">
            <i class="fa-solid fa-globe text-indigo-500 text-4xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Global Learning</h3>
            <p>Engage with a global network of learners and contributors from around the world.</p>
        </div>
    </div>
</section>
<!-- Reviews Section -->
<section class="py-16 px-6 bg-gray-50">
    <h2 class="text-3xl font-bold text-center mb-8">What Our Users Say</h2>
    <div class="overflow-x-auto">
        <div class="flex space-x-4 px-4 py-6 w-full">
            <?php
            include 'connection.php';
           
            $currentUserId = $_SESSION['user_id'] ?? null;

            $stmt = $conn->prepare("SELECT r.review_id, r.comment, r.rating, r.user_id, u.name, u.profile_pic 
                                    FROM review r 
                                    JOIN user u ON r.user_id = u.user_id 
                                    ORDER BY r.review_id DESC");
            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($reviews as $review):
              $picFile = $review['profile_pic'] ?? '';
$cleanedPic = htmlspecialchars($picFile);
$hasValidPic = !empty($picFile) && file_exists(__DIR__ . "/$cleanedPic");

$profilePic = $hasValidPic ? $cleanedPic : "image/defaultavatar.jpg";

            ?>
                <div class="min-w-[280px] max-w-xs bg-white p-6 rounded-lg shadow text-center flex-shrink-0 relative group">
                    <!-- Profile Picture -->
                    <img src="<?= $profilePic ?>" alt="User Picture"
     onerror="this.src='image/defaultavatar.jpg'"
     class="w-16 h-16 rounded-full mx-auto mb-3 object-cover border border-gray-200">


                    <!-- Star Rating -->
                    <div class="text-yellow-400 mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $review['rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>

                    <!-- Name and Comment -->
                    <h4 class="text-lg font-semibold"><?= htmlspecialchars($review['name']) ?></h4>
                    <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($review['comment']) ?></p>

                    <!-- Delete button if current user -->
                    <?php if ($currentUserId && $currentUserId == $review['user_id']): ?>
                        <form action="deletereview.php" method="POST" onsubmit="return confirmDelete();" class="absolute top-2 right-2">
                            <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700" title="Delete Review">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Leave Review -->
   <!-- Leave Review -->
<?php if ($userLoggedIn): ?>
<div class="mt-12 max-w-2xl mx-auto text-center">
    <h3 class="text-2xl font-bold mb-4">Leave a Review</h3>

    <!-- Review Form -->
    <form id="reviewForm" action="submitreview.php" method="POST" class="space-y-4">
        <div>
            <div id="starRating" class="text-yellow-400 text-2xl flex justify-center space-x-2 cursor-pointer">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-regular fa-star star" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="ratingInput" required>
            <p id="ratingError" class="text-red-500 text-sm mt-1 hidden">Please select a rating.</p>
        </div>

        <textarea name="comment" id="comment" rows="4" class="w-full p-3 border rounded-md" placeholder="Share your thoughts..." required></textarea>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Submit Review</button>
    </form>
</div>

<?php else: ?>
<!-- Login Prompt -->
<div class="mt-12 max-w-2xl mx-auto text-center">
    <h3 class="text-2xl font-bold mb-4 text-red-600">
        <i class="fa-solid fa-circle-exclamation mr-2"></i> You must be logged in to leave a review
    </h3>
    <a href="login.php" class="text-blue-600 underline text-lg hover:text-blue-800">
        Click here to log in
    </a>
</div>
<?php endif; ?>


<!-- JavaScript for frontend validation and star interaction -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('ratingInput');
        const ratingError = document.getElementById('ratingError');
        const form = document.getElementById('reviewForm');
        let selectedRating = 0;

        // Star click interaction
        stars.forEach(star => {
            star.addEventListener('click', function () {
                selectedRating = this.getAttribute('data-value');
                ratingInput.value = selectedRating;
                updateStars(selectedRating);
                ratingError.classList.add('hidden');
            });
        });

        // Update star colors based on selection
        function updateStars(rating) {
            stars.forEach(star => {
                const value = parseInt(star.getAttribute('data-value'));
                if (value <= rating) {
                    star.classList.remove('fa-regular');
                    star.classList.add('fa-solid');
                } else {
                    star.classList.add('fa-regular');
                    star.classList.remove('fa-solid');
                }
            });
        }

        // Form submission validation
        form.addEventListener('submit', function (e) {
            if (!ratingInput.value) {
                e.preventDefault(); // Stop form submission
                ratingError.classList.remove('hidden'); // Show error message
            }
        });
    });
</script>


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

<!-- JavaScript for Star Rating -->
 <script>
  function confirmDelete() {
    return confirm("Are you sure you want to delete your review?");
}
document.querySelectorAll('.star').forEach(star => {
  star.addEventListener('click', function () {
    const rating = this.dataset.value;
    document.getElementById('ratingInput').value = rating;

    document.querySelectorAll('.star').forEach(s => {
      s.classList.remove('fa-solid');
      s.classList.add('fa-regular');
    });

    for (let i = 1; i <= rating; i++) {
      const targetStar = document.querySelector(`.star[data-value="${i}"]`);
      if (targetStar) {
        targetStar.classList.remove('fa-regular');
        targetStar.classList.add('fa-solid');
      }
    }
  });
});
</script>

</body>
</html>


