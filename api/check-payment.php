<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

$order_id = intval($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Order ID không hợp lệ']);
    exit;
}

// Kiểm tra trạng thái thanh toán
$query = $conn->prepare("SELECT 
    t.status,
    t.paid_at,
    dh.trang_thai as order_status,
    hd.status as invoice_status
FROM thanh_toan t
JOIN don_hang dh ON t.don_hang_id = dh.id
JOIN hoa_don hd ON t.hoa_don_id = hd.id
WHERE t.don_hang_id = ?
ORDER BY t.created_at DESC
LIMIT 1");

$query->bind_param("i", $order_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $payment = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'status' => $payment['status'],
        'paid_at' => $payment['paid_at'],
        'order_status' => $payment['order_status'],
        'invoice_status' => $payment['invoice_status']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy thông tin thanh toán'
    ]);
}

$conn->close();
?>
