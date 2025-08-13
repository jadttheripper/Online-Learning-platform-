<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // Find admin by name
    $stmt = $conn->prepare("SELECT * FROM admin WHERE name = :name");
    $stmt->execute(['name' => $name]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $message = "❌ Admin not found.";
    } else {
        // Generate token and hash
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

        // Store token
        $update = $conn->prepare("UPDATE admin SET admin_reset_token_hash = :hash, admin_token_expires_at = :expires WHERE name = :name");
        $update->execute([
            'hash' => $hashedToken,
            'expires' => $expires,
            'name' => $name
        ]);

        // Send reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '12031634@students.liu.edu.lb';
            $mail->Password = 'ebnuqvwjiwgjrbrq'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('12031634@students.liu.edu.lb', 'SkillSwap Admin');
            $mail->addAddress($admin['email'], $admin['name']);

            $mail->Subject = 'Admin Password Reset';
            $resetLink = "http://localhost/SKILLSWAP/Admin/resetadminpass.php?token=$token&name=" . urlencode($name);
            $mail->Body = "Hello {$admin['name']},\n\nClick the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";

            $mail->send();
            $message = "✅ A reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "❌ Mail Error: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Admin Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f7f9;
            padding-top: 60px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        .form-group label {
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center">Forgot Admin Password</h3>

    <?php if (!empty($message)): ?>
        <div class="alert <?= strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="post" id="forgotForm">
        <div class="form-group">
            <label for="name">Enter Your Admin Username</label>
            <input 
                type="text" 
                class="form-control" 
                id="name" 
                name="name" 
                required 
                pattern="^[a-zA-Z0-9_]{3,}$"
                title="Admin name must be at least 3 characters and contain only letters, numbers, or underscores"
            >
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
    </form>
</div>
</body>
</html>
