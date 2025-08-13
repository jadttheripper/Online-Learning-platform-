<?php
session_start();
include '../connection.php';
include 'logger.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit();
}


$categoryImageMap = [
    'Technology_Programming' => 'image/Technology_Programming.png',
    'Design_Creativity'      => 'image/Design_Creativity.png',
    'Business_Marketing'     => 'image/Business_Marketing.png',
    'Finance_Accounting'     => 'image/Finance_Accounting.png',
    'Communication_Writing'  => 'image/Communication_Writing.png',
    'Music'                  => 'image/Music.png',
    'Sports'                 => 'image/Sports.png',
    'Math_Science'           => 'image/Math_Science.png'
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim(strip_tags($_POST['title']));
    $description = trim(strip_tags($_POST['description']));
    $category = $_POST['category'] ?? '';

    if ($title === '' || $category === '') {
        $error = "Please fill in both the title and category.";
    } else {
        $imageUrl = $categoryImageMap[$category] ?? '';

        try {
            // Check if skill with the same title already exists
            $stmt = $conn->prepare("SELECT skill_id FROM skill WHERE title = ?");
            $stmt->execute([$title]);
            if ($stmt->fetch()) {
                $error = "A skill with this title already exists.";
            } else {
                // Insert new skill
                $stmt = $conn->prepare("INSERT INTO skill (title, description, skill_category, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $category, $imageUrl]);
                log_admin_action($conn, $_SESSION['admin_id'], 'insert', 'manage skill');
                header("Location: manageskill.php");
                exit();
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New Skill</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            background-color: #eef2f7;
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 480px;
        }
        h1 {
            margin-bottom: 24px;
            color: #2c3e50;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #34495e;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1.8px solid #bdc3c7;
            border-radius: 5px;
            font-size: 1rem;
            color: #2c3e50;
            box-sizing: border-box;
            resize: vertical;
        }
        textarea {
            min-height: 80px;
        }
        button {
            background-color: #3498db;
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error-message {
            margin-bottom: 15px;
            color: #e74c3c;
            font-weight: 600;
            text-align: center;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            color: #7f8c8d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Skill</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="addskill.php" method="POST" novalidate>
            <label for="title">Skill Title</label>
            <input type="text" id="title" name="title" required maxlength="100" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">

            <label for="description">Description (optional)</label>
            <textarea id="description" name="description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>

            <label for="category">Select Category</label>
            <select id="category" name="category" required>
                <option value="" disabled <?= !isset($_POST['category']) ? 'selected' : '' ?>>-- Choose a category --</option>
                <?php foreach ($categoryImageMap as $key => $img): ?>
                    <option value="<?= htmlspecialchars($key) ?>" <?= (isset($_POST['category']) && $_POST['category'] === $key) ? 'selected' : '' ?>>
                        <?= str_replace('_', ' & ', $key) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Add Skill</button>
        </form>

        <a href="manageskills.php" class="back-link">← Back to Skill Management</a>
    </div>
</body>
</html>
