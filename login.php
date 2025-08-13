
<?php


session_start();

include 'connection.php';
// Initialize message
$message = '';

// form submission backend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize input using strip_tags() and escape using trim()
  $email = trim(strip_tags($_POST['email']));
  $passwordInput = trim(strip_tags($_POST['password']));

  if (empty($email) || empty($passwordInput)) {
      $message = "Please fill in all fields.";
  } else {
      // Look for a user by email (prepared statements still used — good)
      $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email LIMIT 1");
      $stmt->execute([':email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($passwordInput, $user['pass'])) {
          $_SESSION['user_id'] = $user['user_id'];
          $_SESSION['user_name'] = $user['name'];
          $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'image/defaultavatar.jpg';

          header("Location: index.php");
          exit();
      } else {
          $message = "Incorrect email or password.";
      }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SkillSwap – Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(45deg, #003366, #005b96);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 40px 30px;
      max-width: 400px;
      width: 100%;
      margin: 20px;
    }
    .login-card h2 {
      color: #003366;
      font-weight: 600;
      text-align: center;
      margin-bottom: 30px;
    }
    .form-label {
      font-weight: 500;
      color: #333;
    }
    .form-control {
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-primary {
      background-color: #003366;
      border-color: #003366;
      transition: background-color .3s, color .3s;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #ffc107;
      color: #003366;
    }
    .forgot-link {
      display: block;
      text-align: right;
      margin-top: -15px;
      margin-bottom: 20px;
    }
    .forgot-link a {
      color: #005b96;
      font-size: 0.9rem;
      text-decoration: none;
      transition: color .3s;
    }
    .forgot-link a:hover {
      color: #003366;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h2>Login to SkillSwap</h2>

  <?php if (!empty($message)): ?>
    <div class="alert alert-danger">
      <?= $message ?>
    </div>
  <?php endif; ?>

  <form method="post" action="login.php" novalidate>
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input id="email" type="email" name="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
    <div class="invalid-feedback">Please enter your email.</div>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input id="password" type="password" name="password" class="form-control" required>
    <div class="invalid-feedback">Please enter your password.</div>
  </div>

  <div class="forgot-link">
    <a href="forgotpassword.php">Forgot Password?</a>
  </div>

  <button type="submit" class="btn btn-primary w-100">Login</button>

  <div class="text-center mt-4">
    <a href="signupuser.php" class="text-decoration-none fw-semibold" style="color: #005b96;">
      New to SkillSwap? <span class="text-decoration-underline">Sign up here</span>
    </a>
  </div>
</form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // form validation using bootstrap5.js 
  (() => {
    const form = document.querySelector('form');
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        form.classList.add('was-validated');
      }
    });
  })();
</script>

</body>
</html>
