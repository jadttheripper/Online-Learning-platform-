<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

$adminName = $_SESSION['admin_name'];

$stmt = $conn->prepare("
    SELECT u.user_id, u.name AS user_name, s.skill_id, s.title AS skill_title, s.description
    FROM user_skill us
    JOIN user u ON us.user_id = u.user_id
    JOIN skill s ON us.skill_id = s.skill_id
    ORDER BY u.name ASC, s.title ASC
");

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$userSkills = [];
foreach ($rows as $row) {
    $userSkills[$row['user_name']][] = [
        'skill_title' => $row['skill_title'],
        'skill_id' => $row['skill_id'],
        'user_id' => $row['user_id'],
        'description' => $row['description']
    ];
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage User Skills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .table-container {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .panel-heading {
            background: linear-gradient(to right, #007bff, #00c6ff);
            color: white;
            border-radius: 12px 12px 0 0;
            font-weight: bold;
            font-size: 18px;
        }

        .skill-badge {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            margin: 3px 6px 3px 0;
            font-size: 0.9rem;
        }

        .remove-icon {
            cursor: pointer;
            color: #fff;
            margin-left: 8px;
        }

        .remove-icon:hover {
            color: #ff4d4d;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
            color: #343a40;
        }

        #searchInput {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        @media (max-width: 768px) {
            .skill-badge {
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            h2 {
                font-size: 1.5rem;
            }
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
                <li class="active"><a href="#">Manage User Skills</a></li>
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

<div class="container">
    <h2>Users and Their Skills</h2>

    <!-- Search Bar -->
    <input type="text" id="searchInput" placeholder="Search user by name...">

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="userSkillTable">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Skills Taught</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userSkills as $userName => $skills): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($userName) ?></strong></td>
                            <td>
                                <?php foreach ($skills as $skill): ?>
                                    <span class="skill-badge" title="<?= htmlspecialchars($skill['description']) ?>">
                                        <?= htmlspecialchars($skill['skill_title']) ?>
                                        <span class="glyphicon glyphicon-remove remove-icon"
                                            onclick="confirmRemove(<?= $skill['user_id'] ?>, <?= $skill['skill_id'] ?>)"></span>
                                    </span>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmRemove(userId, skillId) {
        if (confirm("Are you sure you want to remove this skill from the user?")) {
            window.location.href = `remove_user_skill.php?user_id=${userId}&skill_id=${skillId}`;
        }
    }

    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userSkillTable tbody tr');
        rows.forEach(row => {
            const username = row.querySelector('td').textContent.toLowerCase();
            row.style.display = username.includes(filter) ? '' : 'none';
        });
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
