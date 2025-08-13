<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No review ID provided.";
    exit;
}

$review_id = intval($_GET['id']);

// Fetch review details
$stmt = $conn->prepare("SELECT * FROM review WHERE review_id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    echo "Review not found.";
    exit;
}
include 'logger.php';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = trim($_POST['comment']);
    $rating = intval($_POST['rating']);

    if ($comment === '' || $rating < 1 || $rating > 5) {
        $error = "Please enter a valid comment and rating (1-5).";
    } else {
        $update = $conn->prepare("UPDATE review SET comment = ?, rating = ? WHERE review_id = ?");
        $update->execute([$comment, $rating, $review_id]);
        log_admin_action($conn, $_SESSION['admin_id'], 'update', 'manage reviews');
        header("Location: review_management.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Review</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Update Review</h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="post">
        <div class="form-group">
            <label>User ID:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($review['user_id']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Comment:</label>
            <textarea name="comment" class="form-control" required><?= htmlspecialchars($review['comment']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" class="form-control" value="<?= htmlspecialchars($review['rating']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Review</button>
        <a href="review_management.php" class="btn btn-default">Cancel</a>
    </form>
</div>
</body>
</html>
