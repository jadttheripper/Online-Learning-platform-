<?php
session_start();
include '../connection.php';
include 'logger.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}
$adminName=$_SESSION['admin_name'];
function extractCourseTitle($html) {
    if (strpos($html, 'course-card') === false) return false;

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();

    $h4s = $doc->getElementsByTagName('h4');
    return $h4s->length > 0 ? trim($h4s->item(0)->textContent) : false;
}

$stmt = $conn->prepare("SELECT m.*, 
    us.name AS sender_name, 
    ur.name AS receiver_name 
FROM message m
LEFT JOIN user us ON m.sender_id = us.user_id
LEFT JOIN user ur ON m.receiver_id = ur.user_id
ORDER BY m.time_stamp DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Messages</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #343a40;
            text-align: center;
            margin-bottom: 15px;
        }

        .tooltip-custom {
            position: relative;
            display: inline-block;
        }

        .tooltip-custom .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 5px;
            border-radius: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip-custom:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .table th {
            background-color: #343a40;
            color: white;
        }

        .btn-action {
            margin-right: 5px;
        }

        /* Popup message styling */
        .info-popup {
            background-color: #e7f3fe;
            color: #31708f;
            border: 1px solid #bce8f1;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 1rem;
            text-align: center;
            box-shadow: 0 2px 6px rgba(49, 112, 143, 0.2);
        }
    </style>
</head>
<body>
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

<div class="container">
    <h2>Manage Messages</h2>

    <!-- Popup info -->
    <div class="info-popup">
        💡 Hover over the <strong>Sender ID</strong> or <strong>Receiver ID</strong> to view their names.
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender ID</th>
                        <th>Receiver ID</th>
                        <th>Content</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?= htmlspecialchars($msg['message_id']) ?></td>
                            <td>
                                <div class="tooltip-custom">
                                    <?= htmlspecialchars($msg['sender_id']) ?>
                                    <span class="tooltiptext">Sender: <?= htmlspecialchars($msg['sender_name'] ?? 'Unknown') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="tooltip-custom">
                                    <?= htmlspecialchars($msg['receiver_id']) ?>
                                    <span class="tooltiptext">Receiver: <?= htmlspecialchars($msg['receiver_name'] ?? 'Unknown') ?></span>
                                </div>
                            </td>
                            <td>
                                <?php
                                $courseTitle = extractCourseTitle($msg['content']);
                                if ($courseTitle) {
                                    echo "<strong>Course: " . htmlspecialchars($courseTitle) . "</strong>";
                                } else {
                                    echo htmlspecialchars($msg['content']);
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($msg['time_stamp']) ?></td>
                            <td class="action-icons">
    <?php if (!$courseTitle): ?>
        <i class="glyphicon glyphicon-edit" title="Edit" style="cursor:pointer; margin-right:10px;" onclick="window.location.href='editmessage.php?id=<?= $msg['message_id'] ?>'"></i>
    <?php endif; ?>
    <i class="glyphicon glyphicon-trash" title="Delete" style="cursor:pointer;" onclick="if(confirm('Are you sure you want to delete this message?')) window.location.href='deletemessage.php?id=<?= $msg['message_id'] ?>'"></i>
</td>

                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="6" class="text-center">No messages found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
