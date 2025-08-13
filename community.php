<?php
error_log("upload_max_filesize: " . ini_get('upload_max_filesize'));
error_log("post_max_size: " . ini_get('post_max_size'));

session_start();
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Community</title>
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
<script>
  const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
  // Add this helper function before your post rendering code
  function formatRelativeTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });
    
    const units = [
        { name: 'year', seconds: 31536000 },
        { name: 'month', seconds: 2592000 },
        { name: 'week', seconds: 604800 },
        { name: 'day', seconds: 86400 },
        { name: 'hour', seconds: 3600 },
        { name: 'minute', seconds: 60 },
        { name: 'second', seconds: 1 }
    ];
    
    for (const unit of units) {
        const interval = Math.floor(diffInSeconds / unit.seconds);
        if (interval >= 1) {
            return rtf.format(-interval, unit.name);
        }
    }
    
    return 'just now';
}
</script>


<div id="communityFeed" class="max-w-2xl mx-auto mt-6 space-y-6 px-4"></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const feed = document.getElementById('communityFeed');

    // Helper functions
    const isImage = url => /\.(jpg|jpeg|png|webp|gif)$/i.test(url);
    const isVideo = url => /\.(mp4|webm|ogg)$/i.test(url);

    // Function to format time (assuming you have this defined elsewhere, or include it)
    function formatRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.round((now - date) / 1000);
        const minutes = Math.round(seconds / 60);
        const hours = Math.round(minutes / 60);
        const days = Math.round(hours / 24);

        if (seconds < 60) return `${seconds}s ago`;
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        if (days < 30) return `${days}d ago`;

        // Fallback to a standard date format if older
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    fetch('get_posts.php')
        .then(res => {
            if (!res.ok) throw new Error(`Network error: ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Failed to fetch posts.');

            feed.innerHTML = ''; // Clear feed before adding posts

            data.posts.forEach(post => {
                // Defensive property checks with defaults
                const liked = post.liked_by_user === true;
                const likeCount = Number(post.like_count) || 0;
                const heartClass = liked ? 'fas text-red-600' : 'far text-gray-500';

                // Media block (image or video)
                let mediaHTML = '';
                if (post.media_url) {
                    if (isImage(post.media_url)) {
                        mediaHTML = `
                            <div class="mt-2 w-full overflow-hidden rounded-lg">
                                <img
                                    src="${post.media_url}"
                                    class="w-full h-auto max-h-[500px] object-contain"
                                    onerror="this.onerror=null;this.style.display='none'"
                                    loading="lazy"
                                >
                            </div>`;
                    } else if (isVideo(post.media_url)) {
                        mediaHTML = `
                            <div class="mt-2 w-full overflow-hidden rounded-lg">
                                <video
                                    controls
                                    class="w-full h-auto max-h-[500px] object-contain"
                                    loading="lazy"
                                >
                                    <source src="${post.media_url}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>`;
                    }
                }

                const postEl = document.createElement('div');
                postEl.className = 'bg-white p-4 rounded shadow relative';

                // --- START: Course Card Rendering Logic ---
                let postContentHTML = '';
                // A more robust way to check for course posts if not just using string includes
                // You might ideally have a 'type' field in your post data (e.g., post.type === 'course')
                const courseCardPattern = /<div class='course-card' style='([^']*)'[^>]*>([\s\S]*?)<\/div>/;
                const match = post.content.match(courseCardPattern);

                if (match) {
                    // If it's a course post, reconstruct the HTML with inline styles
                    const innerHtmlContent = match[0]; // This captures the entire course-card div
                    postContentHTML = innerHtmlContent; // Directly use the content with its inline styles

                    // If you wanted to *override* or *add* specific inline styles from JS
                    // This example assumes the PHP already gives you the full desired styling
                    // If not, you'd parse innerHtmlContent to extract title, desc, link and rebuild
                    /*
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = innerHtmlContent;
                    const courseCardDiv = tempDiv.querySelector('.course-card');
                    if (courseCardDiv) {
                        // Example of re-applying styles if needed, or ensuring they are present
                        Object.assign(courseCardDiv.style, {
                            border: '1px solid #ccc',
                            padding: '16px',
                            borderRadius: '8px',
                            background: '#f9f9f9',
                            maxWidth: '300px', // Apply max-width
                            margin: '10px 0'
                        });
                        const titleEl = courseCardDiv.querySelector('h4');
                        if(titleEl) Object.assign(titleEl.style, { margin: '0 0 8px', fontSize: '18px', color: '#1d4ed8' });
                        const descEl = courseCardDiv.querySelector('p');
                        if(descEl) Object.assign(descEl.style, { margin: '0 0 12px', color: '#555', fontSize: '14px' });
                        const linkEl = courseCardDiv.querySelector('a');
                        if(linkEl) Object.assign(linkEl.style, { display: 'inline-block', padding: '10px 16px', background: '#2563eb', color: '#fff', textDecoration: 'none', borderRadius: '5px', fontWeight: '600' });
                        postContentHTML = tempDiv.innerHTML;
                    }
                    */

                } else {
                    // It's a regular text post or a post with media
                    postContentHTML = `<p class="mt-2 text-gray-800">${post.content || ''}</p>${mediaHTML}`;
                }
                // --- END: Course Card Rendering Logic ---


                postEl.innerHTML = `
                    <div class="flex items-start space-x-3">
                        <img src="${post.profile_pic || '/defaultavatar.jpg'}" loading="lazy" class="w-10 h-10 rounded-full object-cover" alt="Profile picture">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-800">${post.username || 'Unknown'}</div>
                            <div class="text-sm text-gray-600">${formatRelativeTime(post.created_at)}</div>
                            ${postContentHTML}
                        </div>
                        <div class="relative group">
                            <button class="text-gray-600 hover:text-gray-900">&#8942;</button>
                            <div class="absolute right-0 mt-2 bg-white border rounded shadow-md text-sm w-40 hidden group-hover:block z-10">
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-100">Add to Bookmarks</button>
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-100">Report Post</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 text-gray-600 text-sm">
                        <button class="like-btn flex items-center space-x-1 transition cursor-pointer"
                                data-post-id="${post.id}"
                                data-liked="${liked}">
                            <i class="${heartClass} fa-heart text-lg"></i>
                            <span>${likeCount}</span>
                        </button>

                        <button class="flex items-center space-x-1 hover:text-blue-600 transition cursor-pointer">
                            <i class="fas fa-comment-alt text-lg"></i>
                            <span>Comment</span>
                        </button>

                        <button class="flex items-center space-x-1 hover:text-blue-600 transition cursor-pointer">
                            <i class="fas fa-share-alt text-lg"></i>
                            <span>Share</span>
                        </button>
                    </div>
                `;

                feed.appendChild(postEl);
            });

            // Add event listeners to like buttons AFTER rendering
            document.querySelectorAll('.like-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const button = e.currentTarget;
                    const postId = button.dataset.postId;
                    const liked = button.dataset.liked === 'true';

                    try {
                        const res = await fetch('toggle_like.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `post_id=${encodeURIComponent(postId)}&like=${liked ? 0 : 1}`
                        });

                        const result = await res.json();

                        if (!result.success) throw new Error(result.message || 'Failed to update like.');

                        // Update button UI & data attribute
                        button.dataset.liked = liked ? 'false' : 'true';
                        const icon = button.querySelector('i');
                        icon.className = liked ? 'far fa-heart text-gray-500' : 'fas fa-heart text-red-600';
                        button.querySelector('span').textContent = result.like_count;

                    } catch (err) {
                        alert('Error updating like: ' + err.message);
                        console.error(err);
                    }
                });
            });

        })
        .catch(err => {
            console.error('Fetch error:', err);
            feed.innerHTML = `<p class="text-red-600 text-center">Error fetching posts: ${err.message}</p>`;
        });
});
</script>


<!-- Add Post Modal Overlay -->
<div id="addPostModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-60 hidden">
  <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
    <button id="closeAddPost" class="absolute top-3 right-3 text-gray-500 hover:text-gray-900 text-2xl font-bold">&times;</button>

    <h2 class="text-xl font-semibold mb-4 text-center">Create a Post</h2>

    <form id="addPostForm" enctype="multipart/form-data" class="space-y-4">

      <textarea 
        name="thought" 
        id="thoughtInput" 
        rows="4" 
        placeholder="What's on your mind?" 
        class="w-full border border-gray-300 rounded p-2 resize-none focus:outline-blue-500" 
        required
      ></textarea>

      <!-- File upload buttons side by side -->
      <div class="flex space-x-6 justify-center mb-4">

        <!-- Image Upload -->
        <div class="flex flex-col items-center cursor-pointer text-green-600 hover:text-green-800 select-none">
          <label for="imageUpload" class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l6 6 4-4 6 6" />
            </svg>
            <span class="text-sm font-semibold">Image</span>
          </label>
          <input type="file" id="imageUpload" name="image" accept="image/*" class="hidden" />
        </div>

        <!-- Video Upload -->
        <div class="flex flex-col items-center cursor-pointer text-red-600 hover:text-red-800 select-none">
          <label for="videoUpload" class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h11a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
            </svg>
            <span class="text-sm font-semibold">Video</span>
          </label>
          <input type="file" id="videoUpload" name="video" accept="video/*" class="hidden" />
        </div>

        <!-- Course Upload -->
         <!-- Trigger Button -->
  <div id="openCourseSelector" class="flex flex-col items-center cursor-pointer text-blue-700 hover:text-blue-900 select-none">
    <div class="flex flex-col items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 19V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
      </svg>
      <span class="text-sm font-semibold">Course</span>
    </div>
  </div>

  <!-- Hidden input to hold selected course ID -->
  <input type="hidden" name="course_id" id="courseUpload" />

  <!-- Optional display area to confirm course selection -->
  <div id="mediaPreview" class="mt-4"></div>

  <!-- Course Selection Modal -->
  <div id="courseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl max-w-lg w-full max-h-[80vh] overflow-y-auto relative">
    <button id="closeCourseModal" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
    <h2 class="text-xl font-semibold mb-4 text-center">Select a Course</h2>
    <div id="courseList" class="space-y-3 text-center text-sm text-gray-700 flex flex-col items-center justify-center">
      <p class="text-gray-500 text-sm">Loading courses...</p>
    </div>
  </div>
</div>


      </div>

      <!-- Preview container -->
      <div id="mediaPreview" class="flex justify-center space-x-4 mb-4"></div>

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">Post</button>

      <div id="postMessage" class="text-center text-sm mt-2 text-red-600 hidden"></div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addPostModal');
    const openBtn = document.getElementById('openAddPost');
    const closeBtn = document.getElementById('closeAddPost');
    const form = document.getElementById('addPostForm');
    const message = document.getElementById('postMessage');

    const imageInput = document.getElementById('imageUpload');
    const videoInput = document.getElementById('videoUpload');
    const courseInput = document.getElementById('selectedCourseId');
    const previewContainer = document.getElementById('mediaPreview');

    // Course modal elements
    const openCourseSelector = document.getElementById('openCourseSelector');
    const courseModal = document.getElementById('courseModal');
    const closeCourseModal = document.getElementById('closeCourseModal');
    const courseList = document.getElementById('courseList');

    function clearPreviews() {
        previewContainer.innerHTML = '';
    }

    function createCancelButton(onClick) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = '&times;';
        // Using inline styles for consistency with the request
        Object.assign(btn.style, {
            position: 'absolute',
            top: '-8px',
            right: '-8px',
            backgroundColor: '#4a5568', // bg-gray-700
            color: '#fff',
            borderRadius: '9999px', // rounded-full
            width: '24px', // w-6
            height: '24px', // h-6
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            cursor: 'pointer',
            border: 'none',
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)', // shadow-lg
            transition: 'background-color 0.15s ease-in-out' // transition
        });
        btn.onmouseover = () => btn.style.backgroundColor = '#dc2626'; // hover:bg-red-600
        btn.onmouseout = () => btn.style.backgroundColor = '#4a5568'; // Reset
        btn.addEventListener('click', onClick);
        return btn;
    }

    function createImagePreview(file) {
        const container = document.createElement('div');
        Object.assign(container.style, {
            position: 'relative'
        });
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = 'Image preview';
        Object.assign(img.style, {
            maxHeight: '160px', // max-h-40
            borderRadius: '8px', // rounded
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)' // shadow-md
        });
        img.onload = () => URL.revokeObjectURL(img.src);
        const cancelBtn = createCancelButton(() => {
            imageInput.value = '';
            updatePreviews();
        });
        container.appendChild(img);
        container.appendChild(cancelBtn);
        return container;
    }

    function createVideoPreview(file) {
        const container = document.createElement('div');
        Object.assign(container.style, {
            position: 'relative'
        });
        const video = document.createElement('video');
        video.controls = true;
        Object.assign(video.style, {
            maxHeight: '160px', // max-h-40
            borderRadius: '8px', // rounded
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)' // shadow-md
        });
        video.src = URL.createObjectURL(file);
        video.onload = () => URL.revokeObjectURL(video.src);
        const cancelBtn = createCancelButton(() => {
            videoInput.value = '';
            updatePreviews();
        });
        container.appendChild(video);
        container.appendChild(cancelBtn);
        return container;
    }

    function createCoursePreview(course) {
        const container = document.createElement('div');
        Object.assign(container.style, {
            position: 'relative'
        });

        const card = document.createElement('div');
        // Apply inline styles directly from post_course.php's structure
        Object.assign(card.style, {
            border: '1px solid #ccc',
            padding: '16px',
            borderRadius: '8px',
            background: '#f9f9f9',
            maxWidth: '300px',
            margin: '10px 0',
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)', // Added shadow for visual consistency
            fontSize: '14px' // text-sm equivalent for the card itself
        });

        const title = document.createElement('h4');
        Object.assign(title.style, {
            margin: '0 0 8px',
            fontSize: '18px', // font-semibold and text-lg equivalent
            color: '#1d4ed8', // text-blue-700
            fontWeight: '600' // Ensure boldness
        });
        title.textContent = `📘 ${course.title}`;

        const desc = document.createElement('p');
        Object.assign(desc.style, {
            margin: '0 0 12px',
            color: '#555', // text-gray-700, slightly darker for readability
            fontSize: '14px' // text-sm
        });
        desc.textContent = course.description;

        const link = document.createElement('a');
        link.href = `create_course_progress.php?c_id=${course.id}`; // Changed to match backend link
        Object.assign(link.style, {
            display: 'inline-block', // inline-block
            padding: '10px 16px', // px-4 py-2 equivalent
            background: '#2563eb', // bg-blue-600
            color: '#fff', // text-white
            textDecoration: 'none', // no underline
            borderRadius: '5px', // rounded
            fontWeight: '600', // font-semibold
            transition: 'background-color 0.15s ease-in-out' // transition
        });
        link.textContent = 'Start Course';
        link.target = '_blank'; // Opens in new tab
        // Add hover effect with JS for inline styles
        link.onmouseover = () => link.style.backgroundColor = '#1e40af'; // hover:bg-blue-700
        link.onmouseout = () => link.style.backgroundColor = '#2563eb'; // Reset

        const cancelBtn = createCancelButton(() => {
            courseInput.value = '';
            courseInput.removeAttribute('data-course-title');
            courseInput.removeAttribute('data-course-description');
            updatePreviews();
        });

        card.appendChild(title);
        card.appendChild(desc);
        card.appendChild(link);
        container.appendChild(card);
        container.appendChild(cancelBtn);

        return container;
    }

    function updatePreviews() {
        clearPreviews();

        if (imageInput.files.length > 0) {
            previewContainer.appendChild(createImagePreview(imageInput.files[0]));
            // Clear other inputs if image is selected
            videoInput.value = '';
            courseInput.value = '';
            courseInput.removeAttribute('data-course-title');
            courseInput.removeAttribute('data-course-description');
        } else if (videoInput.files.length > 0) {
            previewContainer.appendChild(createVideoPreview(videoInput.files[0]));
            // Clear other inputs if video is selected
            imageInput.value = '';
            courseInput.value = '';
            courseInput.removeAttribute('data-course-title');
            courseInput.removeAttribute('data-course-description');
        } else if (courseInput.value && courseInput.dataset.courseTitle) {
            const course = {
                id: courseInput.value,
                title: courseInput.dataset.courseTitle,
                description: courseInput.dataset.courseDescription || ''
            };
            previewContainer.appendChild(createCoursePreview(course));
            // Clear other inputs if course is selected
            imageInput.value = '';
            videoInput.value = '';
        }
    }

    // Event listeners to trigger preview updates
    imageInput.addEventListener('change', updatePreviews);
    videoInput.addEventListener('change', updatePreviews);
    // No need for a direct listener on courseInput. Its value changes via the modal selection.
    // The updatePreviews() call in the course selection handler will take care of it.

    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        message.classList.add('hidden');
        message.textContent = '';
        form.reset();
        clearPreviews();
        modal.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    openCourseSelector.addEventListener('click', async () => {
        courseModal.classList.remove('hidden');
        courseList.innerHTML = '<p style="color:#6b7280; font-size:0.875rem; text-align:center;">Loading courses...</p>'; // Inline styles for loading message

        try {
            const response = await fetch('get_courses.php');
            const result = await response.json();

            if (result.success && Array.isArray(result.courses)) {
                courseList.innerHTML = '';

                // Create a container for the course cards with inline grid styles
                const coursesContainer = document.createElement('div');
                Object.assign(coursesContainer.style, {
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', // Responsive grid
                    gap: '16px', // gap-4
                    padding: '16px' // p-4
                });

                result.courses.forEach(course => {
                    const card = document.createElement('div');
                    Object.assign(card.style, {
                        border: '1px solid #d1d5db', // border-gray-300
                        borderRadius: '8px', // rounded-lg
                        overflow: 'hidden',
                        boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)', // shadow-md
                        transition: 'box-shadow 0.15s ease-in-out', // transition
                        cursor: 'pointer'
                    });
                    // Add hover effect for the card
                    card.onmouseover = () => card.style.boxShadow = '0 10px 15px rgba(0, 0, 0, 0.1)'; // hover:shadow-lg
                    card.onmouseout = () => card.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)'; // Reset

                    const cardBody = document.createElement('div');
                    Object.assign(cardBody.style, {
                        padding: '16px' // p-4
                    });

                    const title = document.createElement('h3');
                    Object.assign(title.style, {
                        fontSize: '1.125rem', // text-lg
                        fontWeight: '600', // font-semibold
                        color: '#1d4ed8', // text-blue-700
                        marginBottom: '8px' // mb-2
                    });
                    title.textContent = course.title;

                    const description = document.createElement('p');
                    Object.assign(description.style, {
                        color: '#4b5563', // text-gray-700
                        fontSize: '0.875rem', // text-sm
                        marginBottom: '16px' // mb-4
                    });
                    description.textContent = course.description;

                    const selectBtn = document.createElement('button');
                    Object.assign(selectBtn.style, {
                        width: '100%', // w-full
                        paddingTop: '8px', // py-2
                        paddingBottom: '8px', // py-2
                        backgroundColor: '#2563eb', // bg-blue-600
                        color: '#fff', // text-white
                        borderRadius: '4px', // rounded
                        transition: 'background-color 0.15s ease-in-out', // transition
                        border: 'none',
                        cursor: 'pointer'
                    });
                    selectBtn.textContent = 'Select Course';
                    // Add hover effect for the button
                    selectBtn.onmouseover = () => selectBtn.style.backgroundColor = '#1e40af'; // hover:bg-blue-700
                    selectBtn.onmouseout = () => selectBtn.style.backgroundColor = '#2563eb'; // Reset

                    selectBtn.addEventListener('click', () => {
                        courseInput.value = course.id;
                        courseInput.dataset.courseTitle = course.title;
                        courseInput.dataset.courseDescription = course.description || '';
                        updatePreviews(); // This will now render the preview with inline styles
                        courseModal.classList.add('hidden');
                    });

                    cardBody.appendChild(title);
                    cardBody.appendChild(description);
                    cardBody.appendChild(selectBtn);
                    card.appendChild(cardBody);
                    coursesContainer.appendChild(card);
                });

                courseList.appendChild(coursesContainer);
            } else {
                courseList.innerHTML = '<p style="color:#ef4444; text-align:center;">No courses available.</p>'; // Inline styles for error message
            }
        } catch (err) {
            courseList.innerHTML = `<p style="color:#ef4444; text-align:center;">Error loading courses: ${err.message}</p>`; // Inline styles for error message
        }
    });

    closeCourseModal.addEventListener('click', () => {
        courseModal.classList.add('hidden');
    });

    courseModal.addEventListener('click', (e) => {
        if (e.target === courseModal) {
            courseModal.classList.add('hidden');
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        message.classList.add('hidden');
        message.textContent = '';

        try {
            const response = await fetch('add_post.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                message.classList.remove('text-red-600');
                message.classList.add('text-green-600');
                message.textContent = 'Post added successfully!';
                message.classList.remove('hidden');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    window.location.reload(); // Refresh to show new post
                }, 1500);
            } else {
                throw new Error(result.message || 'Failed to add post.');
            }
        } catch (err) {
            message.classList.remove('hidden');
            message.classList.remove('text-green-600');
            message.classList.add('text-red-600');
            message.textContent = err.message;
        }
    });
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Protect links/buttons
    document.querySelectorAll('.requires-auth').forEach(el => {
      el.addEventListener('click', function(e) {
        if (!isLoggedIn) {
          e.preventDefault();
          window.location.href = 'login.php'; // redirect to your login page
        }
      });
    });
  });
</script>
<!-- Bottom Navbar -->
<nav class="fixed bottom-0 left-0 w-full bg-white shadow-xl border-t border-gray-200 z-50"> 
  <div class="max-w-md mx-auto flex justify-between items-center py-3 px-6 text-gray-600 relative">

    <!-- Search -->
    <a href="/search" class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition transform hover:scale-110">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <span class="text-xs mt-1 font-semibold select-none hidden sm:inline">Search</span>
    </a>

    <!-- Following Feed -->
    <a href="/following-feed" class=" requires-auth  flex flex-col items-center text-gray-600 hover:text-blue-600 transition transform hover:scale-110">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
      <span class="text-xs mt-1 font-semibold select-none hidden sm:inline">Following</span>
    </a>

    <!-- Centered Add Post Button -->
    <a href="#" id="openAddPost" class=" requires-auth absolute -top-6 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white rounded-full p-4 shadow-lg hover:bg-blue-700 transition transform hover:scale-110 flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
    </a>

    <!-- Bookmark -->
    <a href="/bookmarks" class=" requires-auth flex flex-col items-center text-gray-600 hover:text-blue-600 transition transform hover:scale-110">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5v14l7-7 7 7V5a2 2 0 00-2-2H7a2 2 0 00-2 2z"/>
      </svg>
      <span class="text-xs mt-1 font-semibold select-none hidden sm:inline">Bookmarks</span>
    </a>

    <!-- Notifications -->
    <a href="/notifications" class="requires-auth relative flex flex-col items-center text-gray-600 hover:text-blue-600 transition transform hover:scale-110">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>

      <!-- Notification Badge -->
      <span class="absolute top-0 right-0 -mt-1 -mr-2 w-4 h-4 bg-red-600 rounded-full flex items-center justify-center animate-pulse text-white text-[10px] font-bold select-none">
        3
      </span>

      <span class="text-xs mt-1 font-semibold select-none hidden sm:inline">Alerts</span>
    </a>

  </div>
</nav>
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





