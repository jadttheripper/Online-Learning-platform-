
<?php
session_start();
include '../connection.php';
include 'logger.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Validate course ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: managecourse.php");
    exit;
}

$courseId = (int)$_GET['id'];

// Fetch existing course data
$stmt = $conn->prepare("SELECT * FROM course WHERE c_id = :id");
$stmt->execute(['id' => $courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if no such course
if (!$course) {
    header("Location: managecourse.php");
    exit;
}

$message = '';

// Fetch user_skill options with descriptive tooltip data
$userSkillStmt = $conn->prepare("
    SELECT us.user_skill_id, u.name AS user_name, s.title AS skill_title, us.user_skill_description
    FROM user_skill us
    JOIN user u ON us.user_id = u.user_id
    JOIN skill s ON us.skill_id = s.skill_id
");
$userSkillStmt->execute();
$userSkills = $userSkillStmt->fetchAll(PDO::FETCH_ASSOC);

// Create array of valid user_skill_ids for validation
$validUserSkillIds = array_column($userSkills, 'user_skill_id');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim(strip_tags($_POST['title']));
    $description = trim(strip_tags($_POST['description']));
    $user_skill_id = (int)$_POST['user_skill_id'];
    $version = trim(strip_tags($_POST['version']));
    $last_updated = date('Y-m-d H:i:s');

    if (!empty($title) && !empty($description) && !empty($version) && in_array($user_skill_id, $validUserSkillIds)) {
        $update = $conn->prepare("UPDATE course SET 
            title = :title, 
            description = :description, 
            user_skill_id = :user_skill_id, 
            version = :version, 
            last_updated = :last_updated 
            WHERE c_id = :id
        ");
        $update->execute([
            'title' => $title,
            'description' => $description,
            'user_skill_id' => $user_skill_id,
            'version' => $version,
            'last_updated' => $last_updated,
            'id' => $courseId
        ]);

        // Log action
        log_admin_action($conn, $_SESSION['admin_id'], 'update', 'manage course');

        // Redirect on success
        header("Location: managecourse.php?updated=1");
        exit;
    } else {
        $message = "All fields are required, and the selected User Skill ID must be valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Course</title>
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
    <h2>Update Course</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($course['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" class="form-control" required><?= htmlspecialchars($course['description']) ?></textarea>
        </div>

        <div class="form-group">
    <label>User Skill ID</label>
    <select name="user_skill_id" class="form-control" required>
        <option value="">-- Select Skill --</option>
        <?php foreach ($userSkills as $us): 
            $tooltip = "User: " . $us['user_name'] . " | Skill: " . $us['skill_title'] . " | Description: " . $us['user_skill_description'];
        ?>
            <option value="<?= $us['user_skill_id'] ?>" 
                <?= $us['user_skill_id'] == $course['user_skill_id'] ? 'selected' : '' ?>
                title="<?= htmlspecialchars($tooltip) ?>">
                <?= $us['user_skill_id'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


        <div class="form-group">
            <label>Version</label>
            <input type="text" name="version" class="form-control" value="<?= htmlspecialchars($course['version']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>

</body>
</html>
