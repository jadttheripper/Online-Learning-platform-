<?php
// unreadcheck.php

function hasUnreadMessages(PDO $conn, int $userId): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM message WHERE receiver_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() > 0;
}
