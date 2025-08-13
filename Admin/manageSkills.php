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

$stmt = $conn->prepare("SELECT * FROM skill WHERE title LIKE :searchQuery");
$stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skills</title>
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

        .add-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }

        .add-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#adminNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>                        
            </button>
            <a class="navbar-brand" href="panel.php"><span class="glyphicon glyphicon-user"></span> <?= htmlspecialchars($adminName) ?></a>
        </div>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="nav navbar-nav">
            <li><a href="addadmin.php">Add New Admin</a></li>
                <li><a href="manageuser.php">Manage Users</a></li>
                <li ><a href="manageuser_skill.php">Manage User Skills</a></li>
                <li><a href="viewadminlogs.php">view admin logs </a></li>
                <li><a href="contact_management.php">Contact Messages</a></li>
                <li><a href="review_management.php">Manage Reviews</a></li>>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">
    <h2>Manage Skills</h2>

    <div class="search-bar-container">
        <form method="GET" action="manageskills.php" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Search by skill title" value="<?= htmlspecialchars($searchQuery) ?>" />
            <button type="submit" class="btn"><span class="glyphicon glyphicon-search"></span></button>
        </form>
    </div>
    <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
    <a href="addskill.php" style="
        padding: 10px 18px;
        background-color: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    " onmouseover="this.style.backgroundColor='#2980b9'" onmouseout="this.style.backgroundColor='#3498db'">
        +add a skill for users use 
    </a>
</div>
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Skill ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($skills as $skill): ?>
                    <tr>
                        <td><?= htmlspecialchars($skill['skill_id']) ?></td>
                        <td><?= htmlspecialchars($skill['title']) ?></td>
                        <td><?= htmlspecialchars($skill['description']) ?></td>
                        <td><?= htmlspecialchars($skill['skill_category']) ?></td>
                        <td><img src="<?= htmlspecialchars($skill['image_url']) ?>" alt="Skill Image" style="height:40px;"></td>
                        <td class="action-icons">
                            <i class="glyphicon glyphicon-edit" title="Update" onclick="window.location.href='updateskill.php?id=<?= $skill['skill_id'] ?>'"></i>
                            <i class="glyphicon glyphicon-trash" title="Delete" onclick="window.location.href='deleteskill.php?id=<?= $skill['skill_id'] ?>'"></i>
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
