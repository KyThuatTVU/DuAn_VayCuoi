<?php
// Load environment variables
require_once __DIR__ . '/env.php';

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'TVU@842004');
define('DB_NAME', getenv('DB_NAME') ?: 'cua_hang_vay_cuoi_db');

// Site configuration
if (!defined('SITE_NAME')) define('SITE_NAME', getenv('SITE_NAME') ?: 'Váy Cưới Thiên Thần');
if (!defined('SITE_URL')) define('SITE_URL', getenv('SITE_URL') ?: 'https://cuahangvaycuoi.byethost22.com');
if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@vaycuoi.com');

// Connect to database
/** @var mysqli $conn */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    // Chỉ hiển thị lỗi khi không phải trang auth
    $current_page = basename($_SERVER['PHP_SELF']);
    if (!in_array($current_page, ['login.php', 'register.php', 'forgot-password.php'])) {
        die("Lỗi kết nối database: " . $conn->connect_error);
    }
}

$conn->set_charset("utf8mb4");

// Helper functions
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>
