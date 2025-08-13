<?php
require 'connection.php'; // your DB connection file

if (isset($_GET['skill_category'])) {
  $category = $_GET['skill_category'];
  $stmt = $conn->prepare("SELECT skill_id, title, description, image_url FROM skill WHERE skill_category = :skill_category ORDER BY skill_id DESC");
  $stmt->bindParam(':skill_category', $category);
  $stmt->execute();
  $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($skills);
} else {
  echo json_encode([]);
}
?>
