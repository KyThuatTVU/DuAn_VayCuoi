<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$payment_id = $_GET['id'] ?? null;

if (!$payment_id) {
    echo json_encode(['success' => false, 'message' => 'Missing payment ID']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT 
        tt.*,
        dh.ma_don_hang,
        dh.ho_ten,
        dh.so_dien_thoai,
        dh.dia_chi,
        dh.tong_tien as order_amount,
        dh.trang_thai as order_status,
        dh.trang_thai_thanh_toan as payment_status,
        hd.ma_hoa_don
    FROM thanh_toan tt
    LEFT JOIN don_hang dh ON tt.don_hang_id = dh.id
    LEFT JOIN hoa_don hd ON tt.hoa_don_id = hd.id
    WHERE tt.id = ?");
    
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    
    if ($payment) {
        echo json_encode([
            'success' => true,
            'payment' => $payment
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Payment not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
