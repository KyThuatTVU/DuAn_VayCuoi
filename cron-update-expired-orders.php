<?php
/**
 * CRON JOB: Tự động cập nhật trạng thái đơn hàng hết hạn
 * Chạy mỗi phút: * * * * * php /path/to/cron-update-expired-orders.php
 * Hoặc chạy thủ công: php cron-update-expired-orders.php
 */

require_once 'includes/config.php';

echo "=== CRON: Cập nhật đơn hàng hết hạn ===\n";
echo "Thời gian: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Tìm các đơn hàng chờ thanh toán quá 10 phút
    $expired_query = "SELECT 
        dh.id,
        dh.ma_don_hang,
        dh.created_at,
        TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) as minutes_passed
    FROM don_hang dh
    WHERE dh.trang_thai_thanh_toan = 'pending'
    AND TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) >= 10
    AND dh.trang_thai != 'cancelled'";
    
    $result = $conn->query($expired_query);
    
    if ($result && $result->num_rows > 0) {
        echo "Tìm thấy " . $result->num_rows . " đơn hàng hết hạn\n\n";
        
        $conn->begin_transaction();
        
        while ($order = $result->fetch_assoc()) {
            echo "Đơn hàng: {$order['ma_don_hang']}\n";
            echo "  - Thời gian tạo: {$order['created_at']}\n";
            echo "  - Đã qua: {$order['minutes_passed']} phút\n";
            
            // Cập nhật trạng thái đơn hàng
            $update_order = $conn->prepare("UPDATE don_hang 
                SET trang_thai_thanh_toan = 'expired',
                    trang_thai = 'cancelled',
                    updated_at = NOW()
                WHERE id = ?");
            
            $update_order->bind_param("i", $order['id']);
            
            if ($update_order->execute()) {
                echo "  ✓ Đã cập nhật trạng thái: expired\n";
                
                // Cập nhật trạng thái thanh toán
                $update_payment = $conn->prepare("UPDATE thanh_toan 
                    SET status = 'failed'
                    WHERE don_hang_id = ? AND status = 'initiated'");
                
                $update_payment->bind_param("i", $order['id']);
                $update_payment->execute();
                
                // Cập nhật hóa đơn
                $update_invoice = $conn->prepare("UPDATE hoa_don 
                    SET status = 'cancelled'
                    WHERE don_hang_id = ?");
                
                $update_invoice->bind_param("i", $order['id']);
                $update_invoice->execute();
                
                echo "  ✓ Đã cập nhật thanh toán và hóa đơn\n";
            } else {
                echo "  ✗ Lỗi: " . $update_order->error . "\n";
            }
            
            echo "\n";
        }
        
        $conn->commit();
        echo "=== Hoàn tất ===\n";
        
    } else {
        echo "Không có đơn hàng nào hết hạn\n";
    }
    
} catch (Exception $e) {
    if ($conn) {
        $conn->rollback();
    }
    echo "LỖI: " . $e->getMessage() . "\n";
}

$conn->close();
?>
