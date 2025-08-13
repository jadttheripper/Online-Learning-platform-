<?php
// Access the already started session, no need to start it again.
session_start();
include '../connection.php';


// Check if the admin is logged in, i.e., if the session variable exists
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}



// Fetch the admin records from the database
$stmt = $conn->prepare("SELECT admin_id, name,email, password FROM admin");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the admin name from the session for display in the navbar
$adminName = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Admins</title>
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
    </style>


</head>
<body>

<!-- Start of the Admin Panel-Style Navbar -->
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
    <h2 class="text-center">Manage Admins</h2>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Admin ID</th>
                    <th>Name</th>
                    <th>email</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['admin_id']) ?></td>
                        <td><?= htmlspecialchars($admin['name']) ?></td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                        <td>******</td> <!-- Masking the password for security -->
                        <td class="action-icons">
                            <i class="glyphicon glyphicon-edit" title="Update" onclick="window.location.href='updateadmin.php?id=<?= $admin['admin_id'] ?>'"></i>
                            <i class="glyphicon glyphicon-trash" title="Delete" onclick="window.location.href='deleteadmin.php?id=<?= $admin['admin_id'] ?>'"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
