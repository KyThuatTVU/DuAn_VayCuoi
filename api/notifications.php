<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập', 'require_login' => true]);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Kiểm tra bảng thông báo có tồn tại không
function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

if (!tableExists($conn, 'thong_bao')) {
    echo json_encode(['success' => true, 'notifications' => [], 'unread_count' => 0, 'message' => 'Bảng thông báo chưa được tạo']);
    exit();
}

// Lấy danh sách thông báo
if ($action === 'get' || $action === '') {
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Lấy thông báo
    $sql = "SELECT * FROM thong_bao WHERE nguoi_dung_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['time_ago'] = timeAgo($row['created_at']);
        $row['icon'] = getNotificationIcon($row['loai']);
        $notifications[] = $row;
    }
    
    // Đếm số chưa đọc
    $count_sql = "SELECT COUNT(*) as count FROM thong_bao WHERE nguoi_dung_id = ? AND da_doc = 0";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $unread_count = $count_stmt->get_result()->fetch_assoc()['count'];
    
    echo json_encode([
        'success' => true, 
        'notifications' => $notifications,
        'unread_count' => (int)$unread_count
    ]);
    exit();
}

// Đánh dấu đã đọc một thông báo
if ($action === 'mark_read') {
    $notification_id = (int)($_POST['notification_id'] ?? 0);
    
    if ($notification_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        exit();
    }
    
    $sql = "UPDATE thong_bao SET da_doc = 1, read_at = NOW() WHERE id = ? AND nguoi_dung_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notification_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã đánh dấu đã đọc']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
    exit();
}

// Đánh dấu tất cả đã đọc
if ($action === 'mark_all_read') {
    $sql = "UPDATE thong_bao SET da_doc = 1, read_at = NOW() WHERE nguoi_dung_id = ? AND da_doc = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã đánh dấu tất cả đã đọc', 'affected' => $stmt->affected_rows]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
    exit();
}

// Xóa thông báo
if ($action === 'delete') {
    $notification_id = (int)($_POST['notification_id'] ?? 0);
    
    $sql = "DELETE FROM thong_bao WHERE id = ? AND nguoi_dung_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notification_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa thông báo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
    exit();
}

// Chỉ lấy số lượng chưa đọc (cho polling)
if ($action === 'count_unread') {
    $sql = "SELECT COUNT(*) as count FROM thong_bao WHERE nguoi_dung_id = ? AND da_doc = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'];
    
    echo json_encode(['success' => true, 'unread_count' => (int)$count]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);

// Helper functions
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    if ($diff < 2592000) return floor($diff / 604800) . ' tuần trước';
    
    return date('d/m/Y', $time);
}

function getNotificationIcon($type) {
    $icons = [
        'admin_reply' => '💬',
        'comment_reply' => '💬',
        'comment_reaction' => '❤️',
        'order_update' => '📦',
        'new_blog' => '📰',
        'promotion' => '🎉',
        'system' => '🔔'
    ];
    return $icons[$type] ?? '🔔';
}
?>
