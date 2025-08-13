<?php
include '../connection.php';
$message = '';
$showForm = false;

$name = isset($_GET['name']) ? trim(strip_tags($_GET['name'])) : '';
$token = isset($_GET['token']) ? trim(strip_tags($_GET['token'])) : '';

if ($name && $token) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE name = :name");
    $stmt->execute(['name' => $name]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && strtotime($admin['admin_token_expires_at']) >= time()) {
        if (hash_equals($admin['admin_reset_token_hash'], hash('sha256', $token))) {
            $showForm = true;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPass = trim($_POST['password']);
                $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);

                $update = $conn->prepare("UPDATE admin SET password = :pass, admin_reset_token_hash = NULL, admin_token_expires_at = NULL WHERE name = :name");
                $update->execute([
                    'pass' => $hashedPass,
                    'name' => $name
                ]);

                $message = "✅ Password has been reset. <a href='adminlogin.php' class='alert-link'>Login here</a>.";
                $showForm = false;
            }
        } else {
            $message = "❌ Invalid token.";
        }
    } else {
        $message = "❌ Invalid or expired token.";
    }
} else {
    $message = "❌ Missing token or name.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Admin Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #f7f9fb;
            padding-top: 50px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .alert {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center">Reset Admin Password</h3>

    <?php if (!empty($message)): ?>
        <div class="alert <?= $showForm ? 'alert-warning' : 'alert-info' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($showForm): ?>
    <form method="post" id="resetForm">
        <div class="form-group">
            <label for="password">New Password</label>
            <input 
                type="password" 
                class="form-control" 
                id="password" 
                name="password" 
                required
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}"
                title="At least 8 characters, including uppercase, lowercase, number, and special character"
            >
            <p class="help-block">Must be 8+ chars with uppercase, lowercase, number, and special character.</p>
            <div id="error-msg" class="text-danger small"></div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
    </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('resetForm').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value;
    var errorMsg = document.getElementById('error-msg');
    var pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!pattern.test(password)) {
        e.preventDefault();
        errorMsg.textContent = "Password does not meet the required criteria.";
    } else {
        errorMsg.textContent = "";
    }
});
</script>

</body>
</html>

