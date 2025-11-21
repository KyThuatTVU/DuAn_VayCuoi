<?php
session_start();

// Xóa tất cả session admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_logged_in']);

// Xóa cookie remember
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/');
}

// Thông báo đăng xuất thành công
$_SESSION['admin_success'] = 'Đăng xuất thành công!';

// Redirect về trang đăng nhập
header('Location: admin-login.php');
exit();
?>
