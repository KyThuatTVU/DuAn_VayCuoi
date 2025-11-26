<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$vay_id = intval($_GET['vay_id'] ?? 0);

if (!$vay_id) {
    echo json_encode(['error' => 'Invalid vay_id']);
    exit;
}

// Lấy thông tin váy
$dress = $conn->query("SELECT * FROM vay_cuoi WHERE id = $vay_id")->fetch_assoc();

// Lấy hình ảnh
$images = $conn->query("SELECT * FROM hinh_anh_vay_cuoi WHERE vay_id = $vay_id ORDER BY is_primary DESC, sort_order ASC")->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'dress' => $dress,
    'images' => $images
]);
