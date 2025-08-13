<?php
// Start session and include DB connection
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

$adminName = $_SESSION['admin_name'];

// Fetch contact messages
$stmt = $conn->prepare("SELECT message_id, name, email, subject, message FROM contact");
$stmt->execute();
$contactMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact Messages</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .action-icons i {
            font-size: 1.5rem;
            margin: 0 10px;
            cursor: pointer;
            transition: color 0.3s ease-in-out;
        }
        .action-icons i:hover {
            color: #d9534f;
        }
    </style>
</head>
<body>

<!-- Admin Navbar -->
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
                <li><a href="manageuser.php">Manage Users</a></li>
                <li ><a href="manageuser_skill.php">Manage User Skills</a></li>
                <li><a href="viewadminlogs.php">view admin logs </a></li>
                <li class="active"><a href="contact_management.php">Contact Messages</a></li>
                <li><a href="review_management.php">Manage Reviews</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Table Display -->
<div class="container">
    <h2 class="text-center">Contact Form Messages</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Message ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contactMessages as $msg): ?>
                <tr>
                    <td><?= htmlspecialchars($msg['message_id']) ?></td>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td>
                        <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=RE: <?= rawurlencode($msg['subject']) ?>&body=Hi <?= rawurlencode($msg['name']) ?>,%0D%0A%0D%0A" class="btn btn-link">
                            <?= htmlspecialchars($msg['email']) ?>
                            <span class="glyphicon glyphicon-envelope"></span>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td class="action-icons">
                        <i class="glyphicon glyphicon-trash" title="Delete" onclick="if(confirm('Are you sure you want to delete this message?')) window.location.href='delete_message.php?id=<?= $msg['message_id'] ?>'"></i>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
