<?php
// deletecourse.php
session_start();
include '../connection.php';
include 'logger.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $courseId = (int)$_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM course WHERE c_id = :id");
        $stmt->execute(['id' => $courseId]);

        log_admin_action($conn, $_SESSION['admin_id'], 'delete', 'manage course');

        header("Location: managecourse.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        echo "Error deleting course: " . $e->getMessage();
    }
} else {
    echo "Invalid course ID.";
}
