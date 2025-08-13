<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

$adminName = $_SESSION['admin_name'];
$searchQuery = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "
    SELECT ur.*, 
           reporter.name AS reporter_name, 
           reported.name AS reported_name 
    FROM user_report ur
    JOIN user reporter ON ur.reporter_id = reporter.user_id
    JOIN user reported ON ur.reported_user_id = reported.user_id
    WHERE 
        (
            reporter.name LIKE :search OR 
            reported.name LIKE :search OR 
            ur.reason LIKE :search OR
            DATE_FORMAT(ur.created_at, '%Y-%m-%d %H:%i:%s') LIKE :search
        )";

if (!empty($statusFilter)) {
    $sql .= " AND ur.status = :status";
}

$stmt = $conn->prepare($sql);
$params = ['search' => '%' . $searchQuery . '%'];
if (!empty($statusFilter)) {
    $params['status'] = $statusFilter;
}

$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Moderate Reports</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
    <style>
        body {
            padding-top: 70px;
            background: #f0f2f5;
        }
        .container {
            max-width: 1000px;
        }
        h2 {
            margin-bottom: 30px;
            font-weight: bold;
        }
        .search-bar-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-bar input, .search-bar select {
            padding: 8px 12px;
            border-radius: 20px;
            width: 250px;
            margin-right: 10px;
        }
        .table th {
            background: #343a40;
            color: white;
            text-align: center;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        .label-status {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            display: inline-block;
            min-width: 70px;
        }
        .label-pending { background-color: #ffc107; }
        .label-resolved { background-color: #28a745; color: white; }
        .btn-action {
            padding: 4px 10px;
            font-size: 13px;
        }
        form.inline { display: inline; }
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
                <li><a href="manageuser_skill.php">Manage User Skills</a></li>
                <li><a href="viewadminlogs.php">View Admin Logs</a></li>
                <li><a href="contact_management.php">Contact Messages</a></li>
                <li><a href="review_management.php">Manage Reviews</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">
    <h2 style="text-align: center;">Moderate User Reports</h2>

    <div class="search-bar-container">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by reason, name, or date"
                   class="form-control search-input"
                   value="<?= htmlspecialchars($searchQuery) ?>">

            <select name="status" class="form-control search-select">
                <option value="">All Statuses</option>
                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="resolved" <?= $statusFilter == 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>

            <button type="submit" class="btn btn-primary search-button">Search</button>
        </form>
    </div>
</div>
<style>
    .search-bar-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .search-form {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        width: 100%;
        max-width: 700px;
    }

    .search-form .form-control,
    .search-form .search-button {
        flex: 1 1 auto;
        min-width: 180px;
        max-width: 300px;
    }

    .search-button {
        min-width: 120px;
    }

    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            align-items: center;
        }

        .search-form .form-control,
        .search-form .search-button {
            width: 90%;
            max-width: none;
        }
    }
</style>




    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Report ID</th>
                <th>Reporter</th>
                <th>Reported User</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($reports) === 0): ?>
                <tr><td colspan="7">No reports found.</td></tr>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= $report['id'] ?></td>
                        <td><?= htmlspecialchars($report['reporter_name']) ?></td>
                        <td><?= htmlspecialchars($report['reported_name']) ?></td>
                        <td><?= htmlspecialchars($report['reason']) ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime($report['created_at'] ?? $report['timestamp'] ?? 'now')) ?></td>
                        <td>
                            <span class="label label-status label-<?= $report['status'] === 'resolved' ? 'resolved' : 'pending' ?>">
                                <?= ucfirst($report['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($report['status'] === 'pending'): ?>
                                <form method="POST" action="resolve_report.php" style="display:inline;">
                                    <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                    <button class="btn btn-success btn-action btn-sm">Mark as Resolved</button>
                                </form>
                            <?php else: ?>
                                <em>-</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
