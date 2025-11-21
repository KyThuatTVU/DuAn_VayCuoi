<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['admin_id'])) {
    header('Location: admin-dashboard.php');
    exit();
}

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-login.php');
    exit();
}

// Lấy dữ liệu từ form
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate
$errors = [];

if (empty($email)) {
    $errors[] = 'Vui lòng nhập email.';
}

if (empty($password)) {
    $errors[] = 'Vui lòng nhập mật khẩu.';
}

if (!empty($errors)) {
    $_SESSION['admin_errors'] = $errors;
    $_SESSION['admin_email'] = $email;
    header('Location: admin-login.php');
    exit();
}

try {
    // Kiểm tra email trong bảng admin
    $stmt = $conn->prepare("SELECT id, username, password, full_name, email FROM admin WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Email không tồn tại
        $_SESSION['admin_errors'] = ['Email hoặc mật khẩu không chính xác.'];
        $_SESSION['admin_email'] = $email;
        $stmt->close();
        header('Location: admin-login.php');
        exit();
    }

    $admin = $result->fetch_assoc();
    $stmt->close();

    // Kiểm tra mật khẩu
    if (!password_verify($password, $admin['password'])) {
        $_SESSION['admin_errors'] = ['Email hoặc mật khẩu không chính xác.'];
        $_SESSION['admin_email'] = $email;
        header('Location: admin-login.php');
        exit();
    }

    // Đăng nhập thành công
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_logged_in'] = true;

    // Ghi nhớ đăng nhập (nếu chọn)
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('admin_remember', $token, time() + (86400 * 30), '/'); // 30 days
        // Lưu token vào database (nếu cần)
    }

    // Redirect đến dashboard
    header('Location: admin-dashboard.php');
    exit();

} catch (Exception $e) {
    $_SESSION['admin_errors'] = ['Lỗi hệ thống: ' . $e->getMessage()];
    $_SESSION['admin_email'] = $email;
    header('Location: admin-login.php');
    exit();
}
?>
