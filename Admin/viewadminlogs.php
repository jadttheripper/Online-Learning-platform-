<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch logs with admin name
$stmt = $conn->query("
    SELECT l.*, a.name 
    FROM admin_logs l
    JOIN admin a ON l.admin_id = a.admin_id
    ORDER BY l.change_time DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Action Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .back-btn {
            margin-top: 20px;
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
            <a class="navbar-brand" href="panel.php"><span class="glyphicon glyphicon-user"></span> <?= htmlspecialchars($_SESSION['admin_name']) ?></a>
        </div>
        <div id='adminNavbar'>
        <ul class="nav navbar-nav">
        <li><a href="addadmin.php">Add New Admin</a></li>
                <li><a href="manageuser.php">Manage Users</a></li>
                <li ><a href="manageuser_skill.php">Manage User Skills</a></li>
                <li class="active"><a href="viewadminlogs.php">view admin logs </a></li>
                <li><a href="contact_management.php">Contact Messages</a></li>
                <li ><a href="review_management.php">Manage Reviews</a></li>
            
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
        </ul>
    </div>
    </div>
</nav>

<div class="container">
    <h2>Admin Action Logs</h2>
    <!-- Search Form -->
<div class="row text-center" style="margin-bottom: 20px;">
    <div class="col-md-6 col-md-offset-3">
        <form method="GET" class="form-inline">
            <div class="form-group">
                <input type="text" name="search" class="form-control input-lg" placeholder="Search logs..." style="width: 250px;" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Search</button>
            <a href="viewadminlogs.php" class="btn btn-default btn-lg">Reset</a>
        </form>
    </div>
</div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Admin Name</th>
                    <th>Change Type</th>
                    <th>Page Name</th>
                    <th>Change Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $query = "SELECT al.*, a.name 
                          FROM admin_logs al 
                          JOIN admin a ON al.admin_id = a.admin_id";

                if (!empty($_GET['search'])) {
                    $search = "%" . $_GET['search'] . "%";
                    $query .= " WHERE a.name LIKE ? OR al.change_type LIKE ? OR al.page_name LIKE ? OR al.change_time LIKE ?";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$search, $search, $search, $search]);
                } else {
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                }

                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (count($logs) > 0): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['log_id']) ?></td>
                            <td><?= htmlspecialchars($log['name']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($log['change_type'])) ?></td>
                            <td><?= htmlspecialchars($log['page_name']) ?></td>
                            <td><?= htmlspecialchars($log['change_time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No log entries found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
             
         

    <div class="text-center back-btn">
        <a href="panel.php" class="btn btn-primary">Back to Admin Panel</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
