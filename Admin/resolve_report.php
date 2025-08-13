<?php
session_start();
include '../connection.php';
include 'logger.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: adminlogin.php");
    exit;
}

if (isset($_POST['report_id'])) {
    $reportId = $_POST['report_id'];
    $stmt = $conn->prepare("UPDATE user_report SET status = 'resolved' WHERE id = ?");
    $stmt->execute([$reportId]);

}
log_admin_action($conn, $_SESSION['admin_id'], 'update', 'moderate reports');
header("Location: moderate_reports.php");
exit;
