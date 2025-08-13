<?php 
session_start();
header('Content-Type: application/json');

// Error logging (in logs, not to browser)
error_log("PHP Upload Limits:");
error_log("upload_max_filesize: " . ini_get('upload_max_filesize'));
error_log("post_max_size: " . ini_get('post_max_size'));
error_log("memory_limit: " . ini_get('memory_limit'));
error_log("max_execution_time: " . ini_get('max_execution_time'));
ini_set('display_errors', 1);

include 'connection.php';

function json_error($message, $httpCode = 400) {
    http_response_code($httpCode);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    json_error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];
$content = trim($_POST['thought'] ?? '');

if ($content === '') {
    json_error('Post content cannot be empty');
}

$media_url = null;

function saveUploadedFile($file, $folder, $allowedTypes, $maxSizeMB) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error: " . $file['error']);
        return null;
    }

    if ($file['size'] > $maxSizeMB * 1024 * 1024) {
        error_log("File too large: " . $file['size']);
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowedTypes)) {
        error_log("Invalid file type: " . $mime);
        return null;
    }

    if (!is_dir($folder)) {
        if (!mkdir($folder, 0755, true)) {
            error_log("Failed to create directory: " . $folder);
            return null;
        }
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $filepath = rtrim($folder, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }

    error_log("Failed to move uploaded file");
    return null;
}

// Paths
$imagePathRoot = realpath(__DIR__ . '/../image');
$imageUrlPrefix = '/image/';

$videoPathRoot = realpath(__DIR__ . '/../image');
$videoUrlPrefix = '/image/';

// Handle image
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $imagePath = saveUploadedFile($_FILES['image'], $imagePathRoot, [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp'
    ], 5); // Max 5MB
    if ($imagePath) {
        $media_url = $imageUrlPrefix . basename($imagePath);
    } else {
        json_error('Invalid image file or upload failed');
    }
}

// Handle video (only if no image uploaded)
if (!$media_url && isset($_FILES['video']) && $_FILES['video']['error'] !== UPLOAD_ERR_NO_FILE) {
    $videoPath = saveUploadedFile($_FILES['video'], $videoPathRoot, [
        'video/mp4', 'video/ogg', 'video/webm', 'video/mpeg'
    ], 50); // Max 50MB
    if ($videoPath) {
        $media_url = $videoUrlPrefix . basename($videoPath);
    } else {
        error_log("Video upload failed. MIME: " . (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['video']['tmp_name']));
        error_log("Video size: " . $_FILES['video']['size']);
        error_log("Upload error code: " . $_FILES['video']['error']);
        json_error('Invalid video file or upload failed');
    }
}

// Insert into database
try {
    $stmt = $conn->prepare("INSERT INTO post (user_id, content, media_url, created_at) VALUES (:user_id, :content, :media_url, NOW())");
    $stmt->execute([
        ':user_id' => $user_id,
        ':content' => $content,
        ':media_url' => $media_url
    ]);

    $full_url = $media_url ? 'http://' . $_SERVER['HTTP_HOST'] . $media_url : null;

    echo json_encode([
        'success' => true,
        'message' => 'Post added successfully',
        'media_url' => $full_url
    ]);
} catch (PDOException $e) {
    error_log("DB error: " . $e->getMessage());
    json_error('Database error. Please try again later.', 500);
}
?>
