<?php 
// admin panel 

session_start();
include '../connection.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch admin name from session (name already stored after login)
$adminName = $_SESSION['admin_name'];

// Get total number of users

// Get total number of users
$totalUsers = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM user");
if ($stmt->execute()) {
    $totalUsers = $stmt->fetchColumn();
}

// Get average rating
$averageRating = 0;
$stmt = $conn->prepare("SELECT AVG(rating) FROM review");
if ($stmt->execute()) {
    $averageRating = round($stmt->fetchColumn(), 2);
}


$mostTaughtSkill = "N/A";

$stmt = $conn->prepare("
    SELECT s.title, COUNT(us.skill_id) AS count 
    FROM user_skill us
    JOIN skill s ON us.skill_id = s.skill_id
    GROUP BY us.skill_id 
    ORDER BY count DESC 
    LIMIT 1
");

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $mostTaughtSkill = $result['title'];
    }
}




?>
                    
             
<!DOCTYPE html>
<html lang="en">
<head>
    <title>SkillSwap Admin Panel</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="cshop.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        .jumbotron {
            background-color: #343a40;
            color: #fff;
            margin-bottom: 0;
        }

        .navbar-inverse {
            margin-bottom: 0;
            border-radius: 0;
        }

        header {
            background-color: #495057;
            color: #fff;
            padding: 2rem 1rem;
            text-align: center;
        }

        .admin-actions {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        .admin-actions h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #343a40;
        }

        .action-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-card h3 {
            color: #007bff;
        }

        .action-card p {
            color: #555;
        }

        .action-card a.btn {
            margin-top: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
        }

        .action-card a.btn:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8rem;
            }

            .action-card {
                padding: 1rem;
            }
        }
    </style>
    <
    <div class="copy">
<html lang="en">
<head>
    <title>SkillSwap Admin Panel</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="cshop.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="jumbotron text-center">
        <h1>SkillSwap Admin Panel</h1>
        <p>Welcome back, admin! Manage your platform here.</p>
    </div>

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
                <li><a href="contact_management.php">Contact Messages</a></li>
                <li><a href="review_management.php">Manage Reviews</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="adminlogout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
    </div>
</head>
<body>

   

<div class="container" style="margin-top: 30px;">
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-sm-4 col-xs-12">
            <div class="panel panel-primary text-center" style="border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div class="panel-heading" style="border-radius: 10px 10px 0 0;">
                    <h3>Total Users</h3>
                </div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-user" style="font-size: 40px; color: #337ab7;"></span>
                    <h2 style="margin-top: 15px;"><?= $totalUsers ?></h2>
                    <p>Registered platform users</p>
                </div>
            </div>
        </div>

        <!-- Average Rating Card -->
        <div class="col-sm-4 col-xs-12">
            <div class="panel panel-success text-center" style="border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div class="panel-heading" style="border-radius: 10px 10px 0 0;">
                    <h3>Average Rating</h3>
                </div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-star" style="font-size: 40px; color: #5cb85c;"></span>
                    <h2 style="margin-top: 15px;"><?= $averageRating ?>/5</h2>
                    <p>Based on user reviews</p>
                </div>
            </div>
        </div>

        <!-- Most Taught Skill Card -->
        <div class="col-sm-4 col-xs-12">
            <div class="panel panel-info text-center" style="border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div class="panel-heading" style="border-radius: 10px 10px 0 0;">
                    <h3>Most Taught Skill</h3>
                </div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-education" style="font-size: 40px; color: #5bc0de;"></span>
                    <h2 style="margin-top: 15px;"><?= htmlspecialchars($mostTaughtSkill) ?></h2>
                    <p>Based on user contributions</p>
                </div>
            </div>
        </div>
    </div>
</div>



    <section class="admin-actions">

        <h2>Admin Actions</h2>

        <div class="action-card">
            <h3>User Management</h3>
            <p>View, update or delete user accounts and assign roles.</p>
            <a class="btn btn-primary" href="manageuser.php">Go to User Management</a>
        </div>

<div class="action-card">
            <h3>admin Management</h3>
            <p>manage the admins of the platform.</p>
            <a class="btn btn-primary" href="adminmanagement.php">Go to admin Management</a>
        </div>
        <div class="action-card">
            <h3>report Moderation</h3>
            <p>manage and resolve reports.</p>
            <a class="btn btn-primary" href="moderate_reports.php">Go to report Moderation</a>
        </div>
        <div class="action-card">
            <h3>Contact Management</h3>
            <p>Review and respond to messages from users.</p>
            <a class="btn btn-primary" href="contact_management.php">Go to Contact Management</a>
        </div>
        <div class="action-card">
            <h3>skill Management</h3>
            <p>manage or add a new skill</p>
            <a class="btn btn-primary" href="manageskills.php">Go to skill management</a>
        </div>
        <div class="action-card">
            <h3>courses Management</h3>
            <p>manage a course</p>
            <a class="btn btn-primary" href="managecourse.php">Go to course management</a>
        </div>
        <div class="action-card">
            <h3>Message Management</h3>
            <p>manage messages </p>
            <a class="btn btn-primary" href="managemessages.php">Go to message management</a>
        </div>

        <div class="action-card">
            <h3>Review Management</h3>
            <p>Approve or delete reviews shown on the home page.</p>
            <a class="btn btn-primary" href="review_management.php">Go to Review Management</a>
        </div>

    </section>

    <footer>
        <p>&copy; 2025 Jad Soubra & Mohammad Kaadan. All rights reserved.</p>
    </footer>

</body>
</html>
