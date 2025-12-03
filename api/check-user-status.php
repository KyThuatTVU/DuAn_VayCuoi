<?php
/**
 * API kiểm tra trạng thái tài khoản người dùng
 * Được gọi định kỳ từ client để kiểm tra xem user có bị khóa không
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';

// Kiểm tra user đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    echo json_encode([
        'success' => true,
        'logged_in' => false,
        'status' => null
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Kiểm tra trạng thái user trong database
$stmt = $conn->prepare("SELECT COALESCE(status, 'active') as status, ho_ten FROM nguoi_dung WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Nếu user không tồn tại hoặc bị khóa/vô hiệu hóa
if (!$user) {
    // User đã bị xóa
    session_destroy();
    echo json_encode([
        'success' => true,
        'logged_in' => false,
        'status' => 'deleted',
        'message' => 'Tài khoản của bạn đã bị xóa.',
        'force_logout' => true
    ]);
    exit;
}

$status = $user['status'];

if ($status === 'locked') {
    // User bị khóa - đăng xuất ngay
    session_destroy();
    echo json_encode([
        'success' => true,
        'logged_in' => false,
        'status' => 'locked',
        'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin để được hỗ trợ.',
        'force_logout' => true
    ]);
    exit;
}

if ($status === 'disabled') {
    // User bị vô hiệu hóa - đăng xuất ngay
    session_destroy();
    echo json_encode([
        'success' => true,
        'logged_in' => false,
        'status' => 'disabled',
        'message' => 'Tài khoản của bạn đã bị vô hiệu hóa.',
        'force_logout' => true
    ]);
    exit;
}

// User vẫn active
echo json_encode([
    'success' => true,
    'logged_in' => true,
    'status' => 'active',
    'user_name' => $user['ho_ten']
]);
?>
