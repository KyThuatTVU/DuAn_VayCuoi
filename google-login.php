<?php
session_start();
require_once 'includes/config.php';

// Lấy thông tin từ .env
$client_id = getenv('GOOGLE_CLIENT_ID');
$redirect_uri = getenv('GOOGLE_REDIRECT_URI');

// Tạo URL đăng nhập Google
$params = [
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'online',
    'prompt' => 'select_account'
];

$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

// Redirect đến Google
header('Location: ' . $google_login_url);
exit();
?>
