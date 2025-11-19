<?php
session_start();

// Xóa tất cả session
$_SESSION = array();

// Xóa cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Xóa remember token cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Hủy session
session_destroy();

// Redirect về trang chủ
header("Location: index.php");
exit();
?>
