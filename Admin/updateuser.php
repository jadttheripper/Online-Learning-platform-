<?php
session_start();
include '../connection.php';
include 'logger.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header("Location: ../adminlogin.php");
    exit;
}

$userId = $_GET['id'] ?? null;
if (!$userId) {
    header("Location: user_management.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [':user_id' => $userId];

    // Sanitize inputs using strip_tags and addslashes
    $name = strip_tags(addslashes(trim($_POST['name'])));
    $email = strip_tags(addslashes(trim($_POST['email'])));
    $pass = strip_tags(addslashes(trim($_POST['pass'])));
    $education_institute = strip_tags(addslashes(trim($_POST['education_institute'])));
    $language_preference = strip_tags(addslashes(trim($_POST['language_preference'])));

    // Update the fields
    if (!empty($name)) {
        $updates[] = "name = :name";
        $params[':name'] = $name;
    }

    if (!empty($email)) {
        $updates[] = "email = :email";
        $params[':email'] = $email;
    }

    if (!empty($pass)) {
        $updates[] = "pass = :pass";
        $params[':pass'] = password_hash($pass, PASSWORD_DEFAULT); // Hash password for security
    }

    if (!empty($education_institute)) {
        $updates[] = "education_institute = :education_institute";
        $params[':education_institute'] = $education_institute;
    }

    if (!empty($language_preference)) {
        $updates[] = "language_preference = :language_preference";
        $params[':language_preference'] = $language_preference;
    }

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = '../image/';
        $fileName = basename($_FILES['profile_pic']['name']);
        $targetFile = $targetDir . $fileName;

        // Validate the file type and size
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $check = getimagesize($_FILES['profile_pic']['tmp_name']); // Check if file is an image

        if ($check !== false && in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
                // Save relative path
                $updates[] = "profile_pic = :profile_pic";
                $params[':profile_pic'] = 'image/' . $fileName;
            }
        } else {
            $message = "Invalid image file or file size too large.";
        }
    }

    // If any fields are updated, execute the query
    if (!empty($updates)) {
        $sql = "UPDATE user SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }
    log_admin_action($conn, $_SESSION['admin_id'], 'update', 'manage user');
    header("Location: manageuser.php");
    exit;
}

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 80px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        img {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Update User: <?= htmlspecialchars($user['name']) ?></h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_pic" class="form-control">
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="../<?= htmlspecialchars($user['profile_pic']) ?>" width="100" alt="Profile Picture">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pass" class="form-control" placeholder="New Password">
        </div>
        <div class="form-group">
            <label>Education Institute</label>
            <input type="text" name="education_institute" class="form-control" value="<?= htmlspecialchars($user['education_institute'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Language Preference</label>
            <input type="text" name="language_preference" class="form-control" value="<?= htmlspecialchars($user['language_preference'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manageuser.php" class="btn btn-default">Cancel</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
