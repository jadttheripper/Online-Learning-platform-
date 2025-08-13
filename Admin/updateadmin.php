
<?php
session_start();
include '../connection.php';
include 'logger.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Validate admin ID from query parameter
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: adminmanagement.php");
    exit;
}

$adminId = (int)$_GET['id'];

// Fetch admin record
$stmt = $conn->prepare("SELECT admin_id, name, email, password FROM admin WHERE admin_id = :admin_id");
$stmt->execute(['admin_id' => $adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    header("Location: adminmanagement.php");
    exit;
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $updateFields = [];
    $params = ['admin_id' => $adminId];

    if (!empty($name)) {
        $updateFields[] = "name = :name";
        $params['name'] = htmlspecialchars($name);
    }

    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            $updateFields[] = "email = :email";
            $params['email'] = htmlspecialchars($email);
        }
    }

    if (!empty($password)) {
        $updateFields[] = "password = :password";
        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($message) && !empty($updateFields)) {
        $updateQuery = "UPDATE admin SET " . implode(', ', $updateFields) . " WHERE admin_id = :admin_id";

        $stmt = $conn->prepare($updateQuery);
        $stmt->execute($params);

        log_admin_action($conn, $_SESSION['admin_id'], 'update', 'manage admin');

        header("Location: adminmanagement.php?updated=1");
        exit;
    } elseif (empty($updateFields)) {
        $message = "Please fill out at least one field to update.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .container {
            margin-top: 70px;
        }
    </style>
</head>
<body>

<!-- Start of Navbar -->


<div class="container mt-5">
    <h2>Update Admin</h2>

    <!-- Display message if any -->
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Admin Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Admin Name</label>
            <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Update Admin</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
