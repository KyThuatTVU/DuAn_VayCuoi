<?php
session_start();
require_once 'includes/config.php';

// Debug mode - bỏ comment dòng dưới để xem lỗi
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Kiểm tra kết nối database
if (!$conn) {
    die("Lỗi: Không thể kết nối database");
}

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// Lấy dữ liệu từ form
$email = sanitizeInput($_POST['email'] ?? '');
$mat_khau = $_POST['mat_khau'] ?? '';
$remember = isset($_POST['remember']);

// Validate dữ liệu
$errors = [];

if (empty($email)) {
    $errors[] = "Vui lòng nhập email";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}

if (empty($mat_khau)) {
    $errors[] = "Vui lòng nhập mật khẩu";
}

// Nếu có lỗi, quay lại trang đăng nhập
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_email'] = $email;
    redirect('login.php');
}

// Kiểm tra thông tin đăng nhập
try {
    $stmt = $conn->prepare("SELECT id, ho_ten, email, mat_khau, avt, COALESCE(status, 'active') as status FROM nguoi_dung WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['errors'] = ["Email hoặc mật khẩu không đúng"];
        $_SESSION['old_email'] = $email;
        $stmt->close();
        redirect('login.php');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Kiểm tra mật khẩu
    if (!password_verify($mat_khau, $user['mat_khau'])) {
        $_SESSION['errors'] = ["Email hoặc mật khẩu không đúng"];
        $_SESSION['old_email'] = $email;
        redirect('login.php');
    }
    
    // Kiểm tra trạng thái tài khoản
    if ($user['status'] === 'locked') {
        $_SESSION['errors'] = ["Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ."];
        $_SESSION['old_email'] = $email;
        redirect('login.php');
    }
    
    if ($user['status'] === 'disabled') {
        $_SESSION['errors'] = ["Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên để biết thêm chi tiết."];
        $_SESSION['old_email'] = $email;
        redirect('login.php');
    }
    
    // Đăng nhập thành công
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['ho_ten'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_avatar'] = $user['avt'];
    $_SESSION['logged_in'] = true;
    
    // Xử lý remember me
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
        
        // Lưu token vào database (cần tạo bảng remember_tokens)
        // Tạm thời bỏ qua phần này
    }
    
    $_SESSION['success'] = "Đăng nhập thành công! Chào mừng " . $user['ho_ten'];
    
    // Redirect về trang trước đó hoặc trang chủ
    $redirect_url = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);
    redirect($redirect_url);
    
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('login.php');
}
?>
