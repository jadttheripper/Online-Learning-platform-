<?php 
function log_admin_action($conn, $admin_id, $change_type, $page_name) {
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, change_type, page_name) VALUES (:admin_id, :change_type, :page_name)");
    $stmt->execute([
        'admin_id' => $admin_id,
        'change_type' => $change_type,
        'page_name' => $page_name
    ]);
}
?>