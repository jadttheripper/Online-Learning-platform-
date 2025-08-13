<?php
require 'vendor/autoload.php';
require 'mailer.php';
require 'connection.php';

$email = trim(strip_tags($_POST['email'])) ?? '';

$stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

$message = "If that email is registered, a reset link has been sent to your inbox.";

if ($user) {
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    $expires = date("Y-m-d H:i:s", time() + 3600);

    $stmt = $conn->prepare("UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?");
    $stmt->execute([$hashedToken, $expires, $email]);

    $resetLink = "http://localhost/SkillSwap/reset_password.php?token=$token&email=" . urlencode($email);
    sendPasswordResetEmail($email, $resetLink);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Password Reset Sent</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .message-box {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      max-width: 500px;
      width: 100%;
      text-align: center;
    }

    .message-box h2 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: #333;
    }

    .message-box p {
      color: #555;
      font-size: 1rem;
      line-height: 1.6;
    }

    .message-box a {
      display: inline-block;
      margin-top: 1.5rem;
      padding: 0.6rem 1.2rem;
      background-color: #007BFF;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .message-box a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="message-box">
    <h2>Check Your Email</h2>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="forgotpassword.php">Go Back</a>
  </div>

</body>
</html>

