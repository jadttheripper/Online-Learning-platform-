<?php
$email = isset($_GET['email']) ? trim(strip_tags($_GET['email'])) : '';
$token = isset($_GET['token']) ? trim(strip_tags($_GET['token'])) : '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set New Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f0f2f5;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 400px;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      color: #333;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: #444;
      font-weight: 500;
    }

    input[type="password"] {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 0.25rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.2s;
    }

    input[type="password"]:focus {
      border-color: #007BFF;
      outline: none;
    }

    .validation-hint {
      font-size: 0.875rem;
      color: #777;
      margin-bottom: 0.5rem;
    }

    button {
      width: 100%;
      background-color: #007BFF;
      color: #fff;
      padding: 0.75rem;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    button:hover {
      background-color: #0056b3;
    }

    .error {
      color: #d00;
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Set a New Password</h2>
    <form id="resetForm" action="update_password.php" method="post" novalidate>
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

      <label for="new_password">New Password</label>
      <input type="password" id="new_password" name="new_password" required
        pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}">
      <div class="validation-hint">
        Must be at least 8 characters, include uppercase, lowercase, number, and special character.
      </div>
      <div class="error" id="error-message"></div>

      <button type="submit">Set New Password</button>
    </form>
  </div>

  <script>
    const form = document.getElementById('resetForm');
    const passwordInput = document.getElementById('new_password');
    const errorMessage = document.getElementById('error-message');
    const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    passwordInput.addEventListener('input', function () {
      const password = passwordInput.value;
      if (!pattern.test(password)) {
        errorMessage.textContent = "Password doesn't meet the required criteria.";
      } else {
        errorMessage.textContent = "";
      }
    });

    form.addEventListener('submit', function (e) {
      const password = passwordInput.value;
      if (!pattern.test(password)) {
        e.preventDefault();
        errorMessage.textContent = "Password doesn't meet the required criteria.";
      }
    });
  </script>

</body>
</html>
