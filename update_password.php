<?php
require 'connection.php';

$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$token = isset($_POST['token']) ? trim(strip_tags($_POST['token'])) : '';
$newPassword = isset($_POST['new_password']) ? trim(strip_tags($_POST['new_password'])) : '';

$message = '';
$success = false;

$stmt = $conn->prepare("SELECT reset_token_hash, reset_token_expires_at FROM user WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && $user['reset_token_hash']) {
    if (strtotime($user['reset_token_expires_at']) >= time()) {
        $isValid = hash_equals($user['reset_token_hash'], hash('sha256', $token));
        if ($isValid) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE user SET pass = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);

            $message = "✅ Your password has been reset successfully.";
            $success = true;
        } else {
            $message = "❌ Invalid token.";
        }
    } else {
        $message = "❌ This reset link has expired.";
    }
} else {
    $message = "❌ Invalid or missing reset data.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Password Reset Result</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f0f2f5;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
      text-align: center;
      max-width: 400px;
      width: 100%;
    }

    .message {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      color: <?= $success ? '#28a745' : '#dc3545' ?>;
    }

    a.button {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background-color: #007BFF;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 500;
      transition: background-color 0.2s;
    }

    a.button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="message"><?= htmlspecialchars($message) ?></div>
    <a href="login.php" class="button">Back to Login</a>
  </div>
</body>
</html>
