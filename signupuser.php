<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connection.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(strip_tags(trim($_POST['name'] ?? '')));
    $email = trim(strip_tags(trim($_POST['email'] ?? '')));
    $passwordInput = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($passwordInput) || empty($confirmPassword)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif ($passwordInput !== $confirmPassword) {
        $message = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $passwordInput)) {
        $message = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Email is already registered.";
        } else {
            $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (name, email, pass) VALUES (:name, :email, :password)");
            try {
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);
                $message = "Account created successfully! <a href='login.php'>Login here</a>";
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SkillSwap – Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div style="background: linear-gradient(135deg, #003366, #005b96); min-height: 100vh; display: flex; justify-content: center; align-items: center;">
  <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%; border-radius: 15px;">
    <h2 class="text-center mb-4 text-primary">sign up on skillswap</h2>
  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" action="signupuser.php" novalidate>
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
      <div class="invalid-feedback">Enter your name.</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
      <div class="invalid-feedback">Enter a valid email.</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" id="pw" name="password" class="form-control" required>
      <div id="pw-rules" class="form-text text-muted">
        <ul style="list-style-type: none; padding-left: 0;">
          <li id="length" class="text-danger">✔ At least 8 characters</li>
          <li id="uppercase" class="text-danger">✔ One uppercase letter</li>
          <li id="lowercase" class="text-danger">✔ One lowercase letter</li>
          <li id="number" class="text-danger">✔ One number</li>
          <li id="special" class="text-danger">✔ One special character</li>
        </ul>
      </div>
      <div class="invalid-feedback">
        Please follow all password rules.
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm Password</label>
      <input type="password" id="cpw" name="confirm_password" class="form-control" required>
      <div class="invalid-feedback">Passwords must match.</div>
    </div>
    <button type="submit" class="btn btn-success w-100">Sign Up</button>
    <br>
    <br>
  </form>
  <div class="forgot-link">
      <a href="login.php">already have an account?</a>
  </div>
  <style>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Real-time password validation
(() => {
  const form = document.querySelector('form');
  const pw = form.querySelector('#pw');
  const cpw = form.querySelector('#cpw');
  const pwRules = {
    length: document.getElementById('length'),
    uppercase: document.getElementById('uppercase'),
    lowercase: document.getElementById('lowercase'),
    number: document.getElementById('number'),
    special: document.getElementById('special'),
  };

  const checkRules = (password) => {
    const checks = {
      length: password.length >= 8,
      uppercase: /[A-Z]/.test(password),
      lowercase: /[a-z]/.test(password),
      number: /\d/.test(password),
      special: /[\W_]/.test(password),
    };

    for (let key in checks) {
      pwRules[key].classList.toggle('text-success', checks[key]);
      pwRules[key].classList.toggle('text-danger', !checks[key]);
    }

    return Object.values(checks).every(Boolean);
  };

  pw.addEventListener('input', () => {
    checkRules(pw.value);
  });

  cpw.addEventListener('input', () => {
    if (pw.value !== cpw.value) {
      cpw.setCustomValidity('Passwords do not match.');
    } else {
      cpw.setCustomValidity('');
    }
  });

  form.addEventListener('submit', e => {
    let valid = checkRules(pw.value);
    if (pw.value !== cpw.value) {
      cpw.setCustomValidity('Passwords do not match.');
      valid = false;
    } else {
      cpw.setCustomValidity('');
    }

    if (!valid) {
      e.preventDefault();
      form.classList.add('was-validated');
    }
  });
})();
</script>
</div>

</body>
</html>
