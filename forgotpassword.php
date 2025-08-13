<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f2f4f8;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 400px;
    }

    .form-container h2 {
      margin-bottom: 1rem;
      font-size: 1.5rem;
      font-weight: 600;
      color: #333;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: #444;
      font-weight: 500;
    }

    input[type="email"] {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1.5rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.2s;
    }

    input[type="email"]:focus {
      border-color: #007BFF;
      outline: none;
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

    .note {
      text-align: center;
      font-size: 0.9rem;
      color: #666;
      margin-top: 1rem;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Reset Your Password</h2>
    <form action="send_reset_link.php" method="post">
      <label for="email">Enter your email</label>
      <input type="email" name="email" id="email" required>
      <button type="submit">Send Reset Link</button>
    </form>
    <div class="note">
      You'll receive an email if your address is registered.
    </div>
  </div>

</body>
</html>
