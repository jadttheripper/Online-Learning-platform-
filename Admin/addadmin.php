<?php
include '../connection.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = addslashes(strip_tags(trim($_POST['name'] ?? '')));
    $email = addslashes(strip_tags(trim($_POST['email'] ?? '')));
    $passwordInput = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($passwordInput) || empty($confirmPassword)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif ($passwordInput !== $confirmPassword) {
        $message = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $passwordInput)) {
        $message = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM admin WHERE name = :name OR email = :email");
        $stmt->execute(['name' => $name, 'email' => $email]);

        if ($stmt->fetchColumn() > 0) {
            $message = "Admin with this name or email already exists.";
        } else {
            $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin (name, email, password) VALUES (:name, :email, :password)");

            try {
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);
                $message = "Account created successfully!";
                $showLoginLink = true;
            } catch (PDOException $e) {
                $message = "Error creating account: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Signup</title>
    <link rel="icon" type="image/x-icon" href="cshop.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
            font-size: 17px;
        }

        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .form-group label {
            font-size: 17px;
        }

        .form-control {
            font-size: 17px;
            padding: 10px;
        }

        .btn-block {
            width: 100%;
            font-size: 17px;
            padding: 10px;
        }

        .validation-msg {
            font-size: 14px;
            margin-top: 5px;
        }

        .text-success {
            color: green;
        }

        .text-danger {
            color: red;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="text-center">🛡️ Admin Signup</h2>
        <p class="text-center">Create your admin account</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
                <?php if (!empty($showLoginLink)): ?>
                    <a href="adminlogin.php" class="ms-2">Login here</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="addadmin.php" novalidate>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" name="name" id="name" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" required autocomplete="off">
                <div id="passwordHelp" class="validation-msg text-danger"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required autocomplete="off">
                <div id="matchHelp" class="validation-msg text-danger"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>

            <div class="login-link">
                <a href="adminlogin.php">Already have an account?</a>
            </div>
        </form>
    </div>
</div>

<footer>
    &copy; 2025 Jad Soubra & Mohammad Kaadan. All rights reserved.
</footer>

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('#password').on('input', function () {
            const pass = $(this).val();
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (regex.test(pass)) {
                $('#passwordHelp').text("✅ Strong password.").removeClass("text-danger").addClass("text-success");
            } else {
                $('#passwordHelp').text("❌ Must be 8+ chars, upper/lowercase, number & symbol.").removeClass("text-success").addClass("text-danger");
            }
        });

        $('#confirm_password').on('input', function () {
            if ($(this).val() === $('#password').val()) {
                $('#matchHelp').text("✅ Passwords match.").removeClass("text-danger").addClass("text-success");
            } else {
                $('#matchHelp').text("❌ Passwords do not match.").removeClass("text-success").addClass("text-danger");
            }
        });
    });
</script>

</body>
</html>