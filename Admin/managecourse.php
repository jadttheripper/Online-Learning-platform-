<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

$adminName = $_SESSION['admin_name'];
$searchQuery = '';

if (isset($_GET['search'])) {
    $searchQuery = htmlspecialchars($_GET['search']);
}

$stmt = $conn->prepare("SELECT * FROM course WHERE title LIKE :searchQuery");
$stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .table-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .action-icons i {
            font-size: 1.5rem;
            margin: 0 10px;
            cursor: pointer;
            transition: color 0.3s ease-in-out;
        }
        .action-icons i:hover {
            color: #007bff;
        }
        h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #343a40;
            text-align: center;
            margin-top: 30px;
        }
        .search-bar-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .search-bar {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            font-size: 15px;
            border: 2px solid #ddd;
            border-radius: 30px;
            outline: none;
            transition: all 0.3s ease-in-out;
        }
        .search-bar input:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }
        .search-bar button {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background-color: transparent;
            border: none;
            color: #007bff;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .search-bar button:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="panel.php"><span class="glyphicon glyphicon-user"></span> <?= htmlspecialchars($adminName) ?></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="adminmanagement.php">Manage Admins</a></li>
                <li><a href="manageuser.php">Manage Users</a></li>
                <li><a href="manageuser_skill.php">Manage User Skills</a></li>
                <li><a href="manageskills.php">Manage Skills</a></li>
                <li class="active"><a href="managecourse.php">Manage Courses</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Manage Courses</h2>

    <div class="search-bar-container">
        <form method="GET" action="managecourse.php" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Search by title" value="<?= htmlspecialchars($searchQuery) ?>">
            <button type="submit" class="btn"><span class="glyphicon glyphicon-search"></span></button>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>User Skill ID</th>
                    <th>Last Updated</th>
                    <th>Version</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['c_id']) ?></td>
                        <td><?= htmlspecialchars($course['title']) ?></td>
                        <td><?= htmlspecialchars($course['description']) ?></td>
                        <td><?= htmlspecialchars($course['user_skill_id']) ?></td>
                        <td><?= htmlspecialchars($course['last_updated']) ?></td>
                        <td><?= htmlspecialchars($course['version']) ?></td>
                        <td class="action-icons">
                            <i class="glyphicon glyphicon-edit" title="Update" onclick="window.location.href='updatecourse.php?id=<?= $course['c_id'] ?>'"></i>
                            <i class="glyphicon glyphicon-trash" title="Delete" onclick="window.location.href='deletecourse.php?id=<?= $course['c_id'] ?>'"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
