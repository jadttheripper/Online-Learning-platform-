<?php
session_start();
include '../connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = trim($_POST['name']);
    $passwordInput = trim($_POST['password']);

    if (empty($name) || empty($passwordInput)) {
        $message = "Please fill in all fields.";
    } else {
        // Securely query admin data
        $stmt = $conn->prepare("SELECT admin_id, name, password FROM admin WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $message = "User not found. Please check your credentials.";
        } elseif (!password_verify($passwordInput, $admin['password'])) {
            $message = "Incorrect password.";
        } else {
            // Login success - store admin_id and name
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: panel.php");
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel Login</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="cshop.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Required libraries -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-size: 16px;
        }
        .jumbotron {
            background-color: #343a40;
            color: white;
            padding: 2rem 1rem;
            margin-bottom: 2rem;
        }
        .container {
            max-width: 500px;
        }
        .btn-block {
            width: 100%;
            font-size: 16px;
        }
        .forgot-link {
            display: flex;
            justify-content: space-between;
            margin-top: -10px;
            margin-bottom: 20px;
        }
        .forgot-link a {
            color: #005b96;
            font-size: 14px;
            text-decoration: none;
            transition: color .3s;
        }
        .forgot-link a:hover {
            color: #003366;
        }
        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>

    <div class="jumbotron text-center">
        <h1>SkillSwap Admin Login</h1>
        <p>Access your admin account</p>
    </div>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" action="adminlogin.php" novalidate>
            <div class="form-group mb-3">
                <label for="name">Full Name</label>
                <input type="text" style="font-size: 16px;" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group mb-1">
                <label for="password">Password</label>
                <input type="password" style="font-size: 16px;" class="form-control" id="password" name="password" required>
            </div>
<br>
            <div class="forgot-link">
                <a href="addadmin.php">Don't have an account?</a>
                <a href="forgotadmin.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Jad Soubra & Mohammad Kaadan. All rights reserved.</p>
    </footer>

</body>
</html>
