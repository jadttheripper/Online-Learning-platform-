<?php
session_start();
include '../connection.php';
include 'logger.php';
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Skill ID not provided.");
}

$skill_id = intval($_GET['id']);

// Fetch the skill details
$stmt = $conn->prepare("SELECT * FROM skill WHERE skill_id = :id");
$stmt->execute(['id' => $skill_id]);
$skill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$skill) {
    die("Skill not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $image_url = trim($_POST['image_url']);

    if ($title && $category) {
        $updateStmt = $conn->prepare("UPDATE skill SET title = :title, description = :description, skill_category = :category, image_url = :image_url WHERE skill_id = :id");
        $updateStmt->execute([
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'image_url' => $image_url,
            'id' => $skill_id
        ]);
        log_admin_action($conn, $_SESSION['admin_id'], 'update', 'manage skill');
        header("Location: manageskill.php?success=1");
        exit;
    } else {
        $error = "Title and category are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Skill</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f5f5f5;
        }
        .form-container {
            max-width: 700px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="panel.php"><span class="glyphicon glyphicon-user"></span> <?= htmlspecialchars($_SESSION['admin_name']) ?></a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="addadmin.php">Add Admin</a></li>
            <li><a href="manageuser.php">Manage Users</a></li>
            <li><a href="manageuser_skill.php">User Skills</a></li>
            <li class="active"><a href="manageskill.php">Manage Skills</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
        </ul>
    </div>
</nav>

<div class="form-container">
    <h2>Update Skill</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Skill Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($skill['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Description (optional)</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($skill['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <?php
                $categories = [
                    "Technology_Programming" => "Technology & Programming",
                    "Design_Creativity" => "Design & Creativity",
                    "Business_Marketing" => "Business & Marketing",
                    "Finance_Accounting" => "Finance & Accounting",
                    "Communication_Writing" => "Communication & Writing",
                    "Music" => "Music",
                    "Sports" => "Sports",
                    "Math_Science" => "Math & Science"
                ];
                foreach ($categories as $key => $label) {
                    $selected = ($skill['skill_category'] === $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Image URL</label>
            <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($skill['image_url']) ?>">
        </div>

        <div class="text-right">
            <a href="manageskill.php" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Skill</button>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
