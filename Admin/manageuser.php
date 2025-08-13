<?php
// Access the already started session, no need to start it again.
session_start();
include '../connection.php';

// Check if the admin is logged in, i.e., if the session variable exists
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch the admin name from the session for display in the navbar
$adminName = $_SESSION['admin_name'];

// Initialize search query
$searchQuery = '';

// Check if a search query is submitted
if (isset($_GET['search'])) {
    $searchQuery = htmlspecialchars($_GET['search']);
}

// Prepare the query with optional filtering
$stmt = $conn->prepare("SELECT user_id, name, email, profile_pic, pass, education_institute, language_preference FROM user WHERE name LIKE :searchQuery");
$stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage users</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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

        /* Modern search bar design */
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

        .search-bar input::placeholder {
            color: #aaa;
        }

        .search-bar button:focus {
            outline: none;
        }

        @media (max-width: 768px) {
            .search-bar {
                max-width: 100%;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Start of the Admin Panel-Style Navbar -->
<!-- Responsive Navbar -->
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
                <li class="active"><a href="manageuser.php">Manage Users</a></li>
                <li ><a href="manageuser_skill.php">Manage User Skills</a></li>
                <li><a href="contact_management.php">Contact Messages</a></li>
                <li><a href="viewadminlogs.php">view admin logs </a></li>
                <li><a href="review_management.php">Manage Reviews</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- End of the Navbar -->

<div class="container">
    <h2 class="text-center">Manage users</h2>

    <!-- Modern Search Bar -->
    <div class="search-bar-container">
        <form method="GET" action="manageuser.php" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?= htmlspecialchars($searchQuery) ?>" />
            <button type="submit" class="btn"><span class="glyphicon glyphicon-search"></span></button>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Profile Picture</th>
                        <th>Password</th>
                        <th>Education Institute</th>
                        <th>Language Preference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td style="background-color: red; color:#f8f9fa; border: radius 17px;"><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['profile_pic']) ?></td>
                            <td><?= htmlspecialchars($user['pass']) ?></td>
                            <td><?= htmlspecialchars($user['education_institute']) ?></td>
                            <td><?= htmlspecialchars($user['language_preference']) ?></td>
                            <td class="action-icons">
                                <i class="glyphicon glyphicon-edit" title="Update" onclick="window.location.href='updateuser.php?id=<?= $user['user_id'] ?>'"></i>
                                <i class="glyphicon glyphicon-trash" title="Delete" onclick="window.location.href='deleteuser.php?id=<?= $user['user_id'] ?>'"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
