<?php
session_start();
include 'connection.php';
include 'data.php'; // Include the data.php file to access the arrays

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';
// Fetch user info
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs using strip_tags() and trim()
    $name = isset($_POST['name']) ? trim(strip_tags(trim($_POST['name']))) : '';
    $education = isset($_POST['education_institute']) ? trim(strip_tags(trim($_POST['education_institute']))) : '';
    $language = isset($_POST['language_preference']) ? trim(strip_tags(trim($_POST['language_preference']))) : '';

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['profile_pic']['tmp_name'];
        $filename = 'image/' . uniqid() . '_' . basename($_FILES['profile_pic']['name']);
        
        // Ensure the file is an image and move the file
        $allowedTypes = ['image/jpeg', 'image/png'];
$fileType = mime_content_type($tmpName);

if (in_array($fileType, $allowedTypes)) {
    move_uploaded_file($tmpName, $filename);
} else {
    $filename = $user['profile_pic']; // fallback to current pic
    $message = "Invalid file type. Only JPG and PNG are allowed.";
}

    } else {
        $filename = $user['profile_pic'];  // Keep existing profile picture if no new upload.
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE user SET name = ?, education_institute = ?, language_preference = ?, profile_pic = ? WHERE user_id = ?");
    $stmt->execute([$name, $education, $language, $filename, $userId]);

    // Update session
    $_SESSION['user_name'] = $name;
    $_SESSION['profile_pic'] = $filename;
    $message = "Profile updated successfully.";

    // Redirect
    header("Location: profile.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
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
    top: 0; /* Ensure it's at the top of the page */
    width: 100%;
    z-index: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  body {
    padding-top: 16px /* Make sure content doesn't hide behind the navbar */
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
<body class="bg-gray-100 py-10">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-red-600" style="text-align: center;">Edit Profile</h1>

        <?php if ($message): ?>
            <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <!-- Name -->
            <div>
                <label class="block font-semibold mb-1">Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full border p-2 rounded">
            </div>

            <!-- Education -->
            <div>
                <label class="block font-semibold mb-1">Education Institute</label>
                <select name="education_institute" class="w-full border p-2 rounded">
                    <option value="">Select Institute</option>

                    <!-- Lebanon Institutes -->
                    <optgroup label="Lebanon">
                        <?php foreach ($lebanonInstitutes as $institute): ?>
                            <option value="<?= htmlspecialchars($institute) ?>" <?= $user['education_institute'] == $institute ? 'selected' : '' ?>>
                                <?= htmlspecialchars($institute) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>

                    <!-- Global Institutes -->
                    <optgroup label="Global">
                        <?php foreach ($globalInstitutes as $institute): ?>
                            <option value="<?= htmlspecialchars($institute) ?>" <?= $user['education_institute'] == $institute ? 'selected' : '' ?>>
                                <?= htmlspecialchars($institute) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

            <!-- Language -->
            <div>
                <label class="block font-semibold mb-1">Language Preference</label>
                <select name="language_preference"  class="w-full border p-2 rounded">
                    <option value="">Select Language</option>
                    <?php foreach ($languages as $language): ?>
                        <option value="<?= htmlspecialchars($language) ?>" <?= $user['language_preference'] == $language ? 'selected' : '' ?>>
                            <?= htmlspecialchars($language) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

      
            <!-- Profile Picture -->
<div>
    <label class="block font-semibold mb-1 text-center">Profile Picture</label>
    <div class="flex flex-col items-center gap-4">
        <img id="preview" src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile" class="w-24 h-24 rounded-full object-cover border" onerror="this.src='image/defaultavatar.jpg'">
        <div class="text-center">
            <input type="file" id="profile_pic" name="profile_pic" accept=".jpg,.jpeg,.png" class="hidden" onchange="previewImage(event)">
            <label for="profile_pic" class="cursor-pointer px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                Choose Image
            </label>
            <p class="text-xs text-gray-500 mt-1">Accepted: JPG, PNG. Max size: 2MB.</p>
        </div>
    </div>
</div>


            <!-- Buttons -->
            <div class="pt-2 flex justify-center gap-4">
    <a href="profile.php" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
        Cancel
    </a>
    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Save Changes
    </button>
</div>
        </form>
        <hr class="my-6">

<!-- Delete Account Section -->
<div class="bg-red-50 p-4 rounded border border-red-200 mt-6">
    <h2 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h2>
    <p class="text-sm text-red-600 mb-4">Deleting your account is irreversible. All your data will be permanently removed.</p>
    <form action="delete_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Delete My Account
        </button>
    </form>
</div>

    </div>
    

</body>
</html>
