<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập admin']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Kiểm tra bảng có tồn tại không
$check = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
if ($check->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Bảng thông báo chưa được tạo']);
    exit();
}

// Lấy danh sách thông báo
if ($action === 'get') {
    $limit = (int)($_GET['limit'] ?? 20);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $result = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['time_ago'] = timeAgo($row['created_at']);
        $notifications[] = $row;
    }
    
    $unread = $conn->query("SELECT COUNT(*) as cnt FROM admin_notifications WHERE is_read = 0")->fetch_assoc()['cnt'];
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => (int)$unread
    ]);
    exit();
}

// Đánh dấu đã đọc 1 thông báo
if ($action === 'mark_read') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    }
    exit();
}

// Đánh dấu tất cả đã đọc
if ($action === 'mark_all_read') {
    $conn->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
    echo json_encode(['success' => true]);
    exit();
}

// Xóa thông báo
if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM admin_notifications WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    }
    exit();
}

// Xóa tất cả thông báo đã đọc
if ($action === 'delete_read') {
    $conn->query("DELETE FROM admin_notifications WHERE is_read = 1");
    echo json_encode(['success' => true]);
    exit();
}

// Helper function
function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff/60) . ' phút trước';
    if ($diff < 86400) return floor($diff/3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff/86400) . ' ngày trước';
    return date('d/m/Y', strtotime($datetime));
}

echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
