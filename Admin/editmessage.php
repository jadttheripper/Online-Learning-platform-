<?php
session_start();
include '../connection.php';
include 'logger.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Validate message ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: managemessages.php");
    exit;
}

$messageId = (int)$_GET['id'];

// Fetch existing message data
$stmt = $conn->prepare("SELECT * FROM message WHERE message_id = :id");
$stmt->execute(['id' => $messageId]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if no such message
if (!$message) {
    header("Location: managemessages.php");
    exit;
}

$messageText = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim(strip_tags($_POST['content']));

    if (!empty($content)) {
        $update = $conn->prepare("UPDATE message SET content = :content WHERE message_id = :id");
        $update->execute([
            'content' => $content,
            'id' => $messageId
        ]);

        // Log action
        log_admin_action($conn, $_SESSION['admin_id'], 'update', 'manage message');

        // Redirect on success
        header("Location: managemessages.php?updated=1");
        exit;
    } else {
        $error = "Content cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Message</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding: 40px;
            background-color: #f9f9f9;
        }
        .form-container {
            max-width: 600px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            width: 100%;
        }
        .alert {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Message</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Content</label>
            <textarea name="content" rows="4" class="form-control" required><?= htmlspecialchars($message['content']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Message</button>
    </form>
</div>

</body>
</html>
