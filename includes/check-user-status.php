<?php
/**
 * Middleware kiểm tra trạng thái user
 * Include file này ở đầu các trang cần bảo vệ
 */

// Chỉ kiểm tra nếu user đã đăng nhập
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra trạng thái user trong database
    $check_stmt = $conn->prepare("SELECT COALESCE(status, 'active') as status FROM nguoi_dung WHERE id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $user_status = $check_result->fetch_assoc();
    $check_stmt->close();
    
    // Nếu user không tồn tại hoặc bị khóa/vô hiệu hóa
    if (!$user_status || $user_status['status'] === 'locked' || $user_status['status'] === 'disabled') {
        // Xác định thông báo lỗi
        if (!$user_status) {
            $_SESSION['login_errors'] = ['Tài khoản của bạn đã bị xóa.'];
        } elseif ($user_status['status'] === 'locked') {
            $_SESSION['login_errors'] = ['Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin để được hỗ trợ.'];
        } else {
            $_SESSION['login_errors'] = ['Tài khoản của bạn đã bị vô hiệu hóa.'];
        }
        
        // Xóa session đăng nhập
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_avatar']);
        unset($_SESSION['logged_in']);
        
        // Redirect về trang login
        header('Location: login.php');
        exit();
    }
}
?>
