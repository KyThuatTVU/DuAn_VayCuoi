<?php
/**
 * API XÁC NHẬN THANH TOÁN
 * Dùng để admin xác nhận đã nhận tiền hoặc webhook từ ngân hàng
 */

session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Lấy thông tin
$order_id = intval($_POST['order_id'] ?? $_GET['order_id'] ?? 0);
$transaction_id = $_POST['transaction_id'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);
$payment_time = $_POST['payment_time'] ?? date('Y-m-d H:i:s');

// Validate
if ($order_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID không hợp lệ'
    ]);
    exit;
}

try {
    $conn->begin_transaction();
    
    // Kiểm tra đơn hàng
    $check_order = $conn->prepare("SELECT 
        dh.*,
        t.status as payment_status
    FROM don_hang dh
    LEFT JOIN thanh_toan t ON dh.id = t.don_hang_id
    WHERE dh.id = ?");
    
    $check_order->bind_param("i", $order_id);
    $check_order->execute();
    $order = $check_order->get_result()->fetch_assoc();
    
    if (!$order) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    // Kiểm tra đã thanh toán chưa
    if ($order['payment_status'] === 'success' || $order['trang_thai_thanh_toan'] === 'paid') {
        echo json_encode([
            'success' => true,
            'message' => 'Đơn hàng đã được thanh toán trước đó',
            'already_paid' => true
        ]);
        exit;
    }
    
    // Cập nhật trạng thái thanh toán trong bảng thanh_toan
    $update_payment = $conn->prepare("UPDATE thanh_toan 
        SET status = 'success', 
            paid_at = ? 
        WHERE don_hang_id = ?");
    
    $update_payment->bind_param("si", $payment_time, $order_id);
    $update_payment->execute();
    
    // Cập nhật trạng thái đơn hàng
    $update_order = $conn->prepare("UPDATE don_hang 
        SET trang_thai_thanh_toan = 'paid',
            trang_thai = 'processing',
            updated_at = NOW()
        WHERE id = ?");
    
    $update_order->bind_param("i", $order_id);
    $update_order->execute();
    
    // Cập nhật trạng thái hóa đơn
    $update_invoice = $conn->prepare("UPDATE hoa_don 
        SET status = 'paid'
        WHERE don_hang_id = ?");
    
    $update_invoice->bind_param("i", $order_id);
    $update_invoice->execute();
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Xác nhận thanh toán thành công',
        'order_id' => $order_id,
        'payment_time' => $payment_time
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
