<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('change-password.php');
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Lấy dữ liệu từ form
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate
if (empty($current_password)) {
    $errors[] = "Vui lòng nhập mật khẩu hiện tại";
}

if (empty($new_password)) {
    $errors[] = "Vui lòng nhập mật khẩu mới";
} elseif (strlen($new_password) < 6) {
    $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự";
}

if (empty($confirm_password)) {
    $errors[] = "Vui lòng xác nhận mật khẩu mới";
} elseif ($new_password !== $confirm_password) {
    $errors[] = "Mật khẩu xác nhận không khớp";
}

// Kiểm tra mật khẩu hiện tại
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT mat_khau FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user || !password_verify($current_password, $user['mat_khau'])) {
        $errors[] = "Mật khẩu hiện tại không đúng";
    }
}

// Nếu có lỗi
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    redirect('change-password.php');
}

// Cập nhật mật khẩu mới
try {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đổi mật khẩu thành công!";
    } else {
        throw new Exception("Không thể cập nhật mật khẩu");
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
}

redirect('change-password.php');
?>
