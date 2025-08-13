<?php
session_start();
require 'connection.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$currentUserId = (int)$_SESSION['user_id'];

// Verify user exists
$stmt = $conn->prepare("SELECT user_id, name, profile_pic FROM user WHERE user_id = ?");
$stmt->execute([$currentUserId]);
$currentUser = $stmt->fetch();

if (!$currentUser) {
    session_destroy();
    die("Your account could not be verified. Please <a href='login.php'>login</a> again.");
}

// Get receiver ID
$receiverId = isset($_REQUEST['receiver_id']) ? (int)$_REQUEST['receiver_id'] : null;
if ($receiverId) {
    // Mark incoming messages as read
    $markRead = $conn->prepare("
        UPDATE message 
        SET is_read = 1 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ");
    $markRead->execute([$receiverId, $currentUserId]);
}


// Handle sending a message
if (isset($_GET['action']) && $_GET['action'] === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $content = trim($_POST['content'] ?? '');
        
        if (empty($content)) throw new Exception("Message cannot be empty");
        if (empty($receiverId)) throw new Exception("Receiver not specified");

        // Verify receiver exists
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE user_id = ?");
        $stmt->execute([$receiverId]);
        if (!$stmt->fetch()) throw new Exception("Receiver user doesn't exist");

        // Insert message
        $stmt = $conn->prepare("INSERT INTO message (sender_id, receiver_id, content, time_stamp) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$currentUserId, $receiverId, $content]);
        
        // Get the inserted message
        $messageId = $conn->lastInsertId();
        $stmt = $conn->prepare("SELECT * FROM message WHERE message_id = ?");
        $stmt->execute([$messageId]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format the timestamp
        $timestamp = date('h:i A', strtotime($message['time_stamp']));
        
        // Prepare response
        $response = [
            'status' => 'success',
            'message' => [
                'id' => $message['message_id'],
                'content' => htmlspecialchars($message['content']),
                'timestamp' => $timestamp,
                'sender_id' => $message['sender_id']
            ]
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
    }
    exit;
}

// Handle fetching messages
if (isset($_GET['action']) && $_GET['action'] === 'fetch' && $receiverId) {
    header('Content-Type: application/json');
    
    try {
        $lastMessageId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
        
        $stmt = $conn->prepare("
            SELECT * FROM message
            WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
            AND message_id > ?
            ORDER BY time_stamp ASC
        ");
        $stmt->execute([$currentUserId, $receiverId, $receiverId, $currentUserId, $lastMessageId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format messages for response
        $formattedMessages = [];
        foreach ($messages as $msg) {
            $formattedMessages[] = [
                'id' => $msg['message_id'],
                'content' => htmlspecialchars($msg['content']),
                'timestamp' => date('h:i A', strtotime($msg['time_stamp'])),
                'sender_id' => $msg['sender_id']
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'messages' => $formattedMessages,
            'last_id' => !empty($messages) ? end($messages)['message_id'] : $lastMessageId
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
    }
    exit;
}

// Fetch other users
$sql = "
SELECT u.user_id, u.name, u.profile_pic,
   (SELECT content FROM message 
    WHERE (sender_id = u.user_id AND receiver_id = :userId) 
       OR (sender_id = :userId AND receiver_id = u.user_id)
    ORDER BY time_stamp DESC LIMIT 1) as last_message,
   (SELECT time_stamp FROM message 
    WHERE (sender_id = u.user_id AND receiver_id = :userId) 
       OR (sender_id = :userId AND receiver_id = u.user_id)
    ORDER BY time_stamp DESC LIMIT 1) as last_message_time,
   (SELECT COUNT(*) FROM message 
    WHERE sender_id = u.user_id AND receiver_id = :userId AND is_read = 0) as unread_count
FROM user u 
WHERE u.user_id != :userId
ORDER BY COALESCE(last_message_time, '1970-01-01') DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute(['userId' => $currentUserId]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Navbar variables
$userId = $_SESSION['user_id'];
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap Messenger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Improved Chat Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
        }
        
        .messenger-container {
            display: flex;
            height: calc(100vh - 4rem);
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar {
            width: 350px;
            background: white;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }
        
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #f1f5f9;
        }
        
        .course-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            margin-bottom: 0.75rem;
            max-width: 75%;
            border: 1px solid #e2e8f0;
            position: relative;
        }
        
        .course-card-sent {
            margin-left: auto;
            background-color: #3b82f6;
            color: white;
            border-color: #2563eb;
        }
        
        .course-card-received {
            margin-right: auto;
            background-color: white;
            color: #1e293b;
        }
        
        .course-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .course-description {
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            opacity: 0.9;
        }
        
        .course-start-btn {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background-color: #10b981;
            color: white;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .course-start-btn:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }
        
        .course-card-sent .course-start-btn {
            background-color: white;
            color: #3b82f6;
        }
        
        .course-card-sent .course-start-btn:hover {
            background-color: #f0f4ff;
            color: #2563eb;
        }
        
        .course-message-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.5rem;
            text-align: right;
        }
        
        .course-card-received .course-message-time {
            color: #64748b;
        }
        
        .messages-container {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: #e5e7eb;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .msg-sent {
            background-color: #3b82f6;
            color: white;
            margin-left: auto;
            max-width: 75%;
            border-radius: 1rem;
            border-bottom-right-radius: 0.25rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            word-wrap: break-word;
        }
        
        .msg-received {
            background-color: white;
            color: #1e293b;
            margin-right: auto;
            max-width: 75%;
            border-radius: 1rem;
            border-bottom-left-radius: 0.25rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            word-wrap: break-word;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.25rem;
            text-align: right;
        }
        
        .msg-received .message-time {
            color: #64748b;
        }
        
        .user-item {
            transition: all 0.2s ease;
        }
        
        .user-item:hover {
            background-color: #f8fafc;
        }
        
        .user-item.active {
            background-color: #eff6ff;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Message input */
        .message-input {
            border-radius: 1.5rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.5);
        }
        
        /* Typing indicator animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .typing-indicator span {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #64748b;
            margin-right: 2px;
            animation: pulse 1.5s infinite ease-in-out;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
            margin-right: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .messenger-container {
                flex-direction: column;
                height: calc(100vh - 4rem);
            }
            
            .sidebar {
                width: 100%;
                height: 40%;
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
            }
            
            .chat-area {
                height: 60%;
            }
            
            .desktop-only {
                display: none;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-only {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- NAVBAR -->
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

    <!-- MESSENGER CONTENT -->
    <div class="messenger-container mt-4">
        <div class="sidebar">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Messages</h2>
                
            </div>
<div class="flex-1 overflow-y-auto">
<?php foreach ($users as $user): 
    $hasUnread = $user['unread_count'] > 0;
    $isSelected = $receiverId == $user['user_id'];
?>
    <div class="relative flex items-center justify-between px-4 py-3 border-b border-gray-200 
        <?= $isSelected ? 'bg-blue-100' : ($hasUnread ? 'bg-yellow-50' : '') ?>">
        
        <!-- Clickable user area -->
        <a href="?receiver_id=<?= $user['user_id'] ?>" class="flex items-center flex-1 space-x-3">
            
            <!-- Profile Picture with unread dot -->
            <div class="relative">
                <?php if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])): ?>
                    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="<?= htmlspecialchars($user['name']) ?>" 
                         class="h-10 w-10 rounded-full object-cover">
                <?php else: ?>
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasUnread): ?>
                    <!-- Red notification dot -->
                    <span class="absolute top-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-red-500"></span>
                <?php endif; ?>
            </div>

            <!-- User Info -->
            <div class="min-w-0">
                <p class="text-sm font-medium <?= $hasUnread ? 'text-blue-600 font-semibold' : 'text-gray-900' ?> truncate">
                    <?= htmlspecialchars($user['name']) ?>
                </p>
                <p class="text-sm text-gray-500 truncate">
                    <?= !empty($user['last_message']) 
                        ? (strpos($user['last_message'], 'create_course_progress.php') !== false ? 'Course' : htmlspecialchars($user['last_message']))
                        : 'No messages yet'; ?>
                </p>
            </div>
        </a>

        <!-- Kebab Menu -->
        <div x-data="{ open: false, reportOpen: false }" class="relative ml-2">
            <!-- Kebab button -->
            <button @click.stop="open = !open" class="text-gray-500 hover:text-gray-800 focus:outline-none">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/>
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                <!-- Delete Chat Form -->
                <form action="delete_reciever.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this conversation?')">
                    <input type="hidden" name="receiver_id" value="<?= $user['user_id'] ?>">
                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm text-red-600">
                        Delete Chat
                    </button>
                </form>

                <!-- Report User trigger -->
                <button @click="reportOpen = true; open = false" 
                        class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">
                    Report User
                </button>
            </div>

            <!-- Report User Modal -->
            <div x-show="reportOpen" x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div @click.away="reportOpen = false"
                     class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800">Report User</h2>

                    <form method="POST" action="report_user.php" class="space-y-4">
                        <!-- Hidden inputs -->
                        <input type="hidden" name="reporter_id" value="<?= $_SESSION['user_id'] ?>">
                        <input type="hidden" name="reported_user_id" value="<?= $user['user_id'] ?>">

                        <!-- Predefined reasons -->
                        <div class="space-y-2">
                            <?php
                            $reasons = [
                                "Spam or fake account",
                                "Inappropriate language or behavior",
                                "Harassment or bullying",
                                "Suspicious or fraudulent activity"
                            ];
                            foreach ($reasons as $reason): ?>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="reason" value="<?= htmlspecialchars($reason) ?>" class="text-blue-500">
                                    <span class="text-sm text-gray-700"><?= $reason ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div>
                            <label for="custom_reason" class="block text-sm font-medium text-gray-700">Or write a custom report:</label>
                            <textarea name="reason_custom" id="custom_reason" rows="3"
                                      class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="reportOpen = false"
                                    class="px-4 py-2 text-sm bg-gray-200 rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
<?php endforeach; ?>


</div>
        </div>
        
        <div class="chat-area">
            <?php if ($receiverId): 
                $stmt = $conn->prepare("SELECT name, profile_pic FROM user WHERE user_id = ?");
                $stmt->execute([$receiverId]);
                $receiver = $stmt->fetch();
            ?>
                <div class="chat-header flex items-center p-3 border-b border-gray-200 bg-white">
                    <div class="back-button mr-2 md:hidden">
                        <button onclick="window.location.href='?'" class="p-2 rounded-full hover:bg-gray-100 text-gray-600">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile" class="profile-pic" onerror="this.src='image/defaultavatar.jpg'">
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($receiver['name']) ?></div>
                        <div class="text-xs text-gray-500" id="typing-indicator">
                            <span class="typing-indicator hidden"><span></span><span></span><span></span></span>
                        </div>
                    </div>
                </div>
                
                <div class="messages-container" id="messages-container">
                    <!-- Messages will be loaded here via JavaScript -->
                </div>
                
                <div class="message-input-area p-3 border-t border-gray-200 bg-white"> 
    <form id="message-form" class="flex items-center">
        <!-- Plus button on the left -->
        <a href="send_course.php?receiver_id=<?= $receiverId ?>" 
   class="mr-3 inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-500 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" 
   title="Send Course">
            <i class="fas fa-plus"></i>
        </a>

        <input type="hidden" name="receiver_id" value="<?= $receiverId ?>">
        <input type="text" name="content" class="message-input flex-1" placeholder="Type a message..." autocomplete="off" required>

        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <i class="fas fa-comments text-blue-600"></i>
                    </div>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No conversation selected</h3>
                    <p class="mt-1 text-sm text-gray-500">Choose a contact from the sidebar to start chatting</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Messenger functionality
    let lastMessageId = 0;
    let isTyping = false;
    let typingTimer;
    let messagePolling;
    let currentReceiverId = <?= $receiverId ?? 'null' ?>;
    function decodeHTMLEntities(text) {
  const txt = document.createElement('textarea');
  txt.innerHTML = text;
  return txt.value;
}

    
    function translateMessage(text, callback) {
        $.post("translate.php", { text: text, target: "en" }, function (data) {
            if (data && data.translated) {
                callback(data.translated);
            } else {
                callback("Translation failed.");
            }
        }, 'json')
        .fail(() => {
            callback("Translation error.");
        });
    }
    
    function renderMessage(message, isSent) {
    const container = document.getElementById('messages-container');

    if (document.querySelector(`[data-id="msg-${message.id}"]`)) return;



    const isCourseCard = message.content.includes('class="course-card"') || 
                        message.content.includes("class='course-card'") || 
                        message.content.includes('class=course-card');

    if (isCourseCard) {
        const messageDiv = document.createElement('div');
        messageDiv.className = isSent ? 'msg-sent' : 'msg-received';
        messageDiv.setAttribute('data-id', `msg-${message.id}`);
        
        // Create a wrapper div with course card styling that matches message styling
        const courseWrapper = document.createElement('div');
        courseWrapper.className = 'course-card-message';
        
        // Decode and insert the HTML content
        courseWrapper.innerHTML = decodeHTMLEntities(message.content);
        
        // Style the inner elements to match message styling
        const title = courseWrapper.querySelector('h4');
        if (title) title.className = 'course-title';
        
        const description = courseWrapper.querySelector('p');
        if (description) description.className = 'course-description';
        
        const link = courseWrapper.querySelector('a');
        if (link) {
            link.className = 'course-start-btn';
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = new URL(link.href);
                const courseId = url.searchParams.get('c_id');
                if (courseId) {
                    window.location.href = `create_course_progress.php?c_id=${courseId}`;
                }
            });
        }
        
        // Add timestamp
        const timeElement = document.createElement('small');
        timeElement.className = 'message-time';
        timeElement.textContent = message.timestamp || formatTime(new Date());
        courseWrapper.appendChild(timeElement);
        
        messageDiv.appendChild(courseWrapper);
        container.appendChild(messageDiv);
    } else {
        // Normal message rendering unchanged
        const messageDiv = document.createElement('div');
        messageDiv.className = isSent ? 'msg-sent' : 'msg-received';
        messageDiv.setAttribute('data-id', `msg-${message.id}`);

        messageDiv.innerHTML = `
            <div class="message-text">${message.content}</div>
            <br>
            <small class="message-time">${message.timestamp}</small>
            <button class="translate-btn mt-1 text-gray-500 hover:text-blue-600" title="Translate">
                <i class="fas fa-language mr-1"></i>
            </button>
        `;

        container.appendChild(messageDiv);

        const translateBtn = messageDiv.querySelector('.translate-btn');
        const msgTextElem = messageDiv.querySelector('.message-text');

        translateBtn.addEventListener('click', () => {
            const rawText = msgTextElem.textContent;
            translateBtn.disabled = true;
            translateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            translateMessage(rawText, (translatedText) => {
                msgTextElem.innerText = translatedText;
                translateBtn.innerHTML = '<i class="fab fa-google"></i>';
                translateBtn.disabled = false;
            });
        });
    }

    scrollToBottom();
}




    
    function loadMessages() {
        if (!currentReceiverId) return;
        
        $.ajax({
            url: "messages.php",
            type: "GET",
            data: {
                action: "fetch",
                receiver_id: currentReceiverId,
                last_id: lastMessageId
            },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success' && data.messages) {
                    data.messages.forEach(message => {
                        const isSent = message.sender_id == <?= $currentUserId ?>;
                        renderMessage(message, isSent);
                        
                        // Update last message ID
                        if (message.id > lastMessageId) {
                            lastMessageId = message.id;
                        }
                    });
                    
                    // Update last_id if we got new messages
                    if (data.last_id && data.last_id > lastMessageId) {
                        lastMessageId = data.last_id;
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error loading messages:", error);
            }
        });
    }
    
    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }
    
    function initializeChat(receiverId) {
        // Clear existing interval 
        if (messagePolling) {
            clearInterval(messagePolling);
        }
        
        // Reset variables
        currentReceiverId = receiverId;
        lastMessageId = 0;
        
        // Clear messages container
        document.getElementById('messages-container').innerHTML = '';
        
        // Load initial messages
        loadMessages();
        
        // Start polling for new messages
        messagePolling = setInterval(loadMessages, 1500);
        
        // Focus input field
        $('input[name="content"]').focus();
    }
    
    // Handle message form submission
    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const input = form.find('input[name="content"]');
        const message = input.val().trim();
        
        if (message && currentReceiverId) {
            $.ajax({
                url: "messages.php?action=send",
                type: "POST",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success' && data.message) {
                        renderMessage(data.message, true);
                        input.val('');
                        
                        // Update last message ID
                        if (data.message.id > lastMessageId) {
                            lastMessageId = data.message.id;
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error sending message:", error);
                    alert("Failed to send message. Please try again.");
                }
            });
        }
    });
    
    // Typing indicator
    $('input[name="content"]').on('input', function() {
        if (!isTyping) {
            isTyping = true;
            $('.typing-indicator').removeClass('hidden');
        }
        
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            isTyping = false;
            $('.typing-indicator').addClass('hidden');
        }, 1000);
    });
    
    // Initialize chat if receiver is selected
    <?php if ($receiverId): ?>
        initializeChat(<?= $receiverId ?>);
    <?php endif; ?>
    
    // Clear interval when leaving the page
    $(window).on('beforeunload', function() {
        if (messagePolling) {
            clearInterval(messagePolling);
        }
    });
    </script>
</body>
</html>