<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM khuyen_mai WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Promotion not found']);
    exit;
}

$promotion = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'promotion' => $promotion
]);
?>