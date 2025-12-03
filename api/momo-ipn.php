<?php
require_once '../includes/config.php';

// Log IPN request
$logFile = '../debug-momo-ipn.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - IPN Request:\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

header('Content-Type: application/json');

try {
    $partnerCode = $_POST['partnerCode'] ?? '';
    $orderId = $_POST['orderId'] ?? '';
    $requestId = $_POST['requestId'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $orderInfo = $_POST['orderInfo'] ?? '';
    $orderType = $_POST['orderType'] ?? '';
    $transId = $_POST['transId'] ?? '';
    $resultCode = $_POST['resultCode'] ?? '';
    $message = $_POST['message'] ?? '';
    $payType = $_POST['payType'] ?? '';
    $responseTime = $_POST['responseTime'] ?? '';
    $extraData = $_POST['extraData'] ?? '';
    $signature = $_POST['signature'] ?? '';
    
    // Xác thực chữ ký
    $secretKey = $_ENV['MOMO_SECRET_KEY'];
    $rawHash = "accessKey=" . $_ENV['MOMO_ACCESS_KEY'] . 
               "&amount=" . $amount . 
               "&extraData=" . $extraData . 
               "&message=" . $message . 
               "&orderId=" . $orderId . 
               "&orderInfo=" . $orderInfo . 
               "&orderType=" . $orderType . 
               "&partnerCode=" . $partnerCode . 
               "&payType=" . $payType . 
               "&requestId=" . $requestId . 
               "&responseTime=" . $responseTime . 
               "&resultCode=" . $resultCode . 
               "&transId=" . $transId;
    
    $checkSignature = hash_hmac("sha256", $rawHash, $secretKey);
    
    if ($signature !== $checkSignature) {
        file_put_contents($logFile, "Invalid signature\n\n", FILE_APPEND);
        echo json_encode(['resultCode' => 97, 'message' => 'Invalid signature']);
        exit;
    }
    
    // Lấy order_id từ orderId
    preg_match('/MOMO_(\d+)_/', $orderId, $matches);
    $order_id = $matches[1] ?? null;
    
    if (!$order_id) {
        throw new Exception('Invalid order ID format');
    }
    
    // Cập nhật trạng thái giao dịch
    if ($resultCode == 0) {
        // Thanh toán thành công
        $stmt = $conn->prepare("UPDATE thanh_toan SET status = 'success', paid_at = NOW() WHERE transaction_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        
        // Cập nhật đơn hàng
        $stmt = $conn->prepare("UPDATE don_hang SET trang_thai_thanh_toan = 'paid', trang_thai = 'processing', updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        file_put_contents($logFile, "Payment successful for order #$order_id\n\n", FILE_APPEND);
    } else {
        // Thanh toán thất bại
        $stmt = $conn->prepare("UPDATE thanh_toan SET status = 'failed' WHERE transaction_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        
        file_put_contents($logFile, "Payment failed for order #$order_id: $message\n\n", FILE_APPEND);
    }
    
    echo json_encode(['resultCode' => 0, 'message' => 'Success']);
    
} catch (Exception $e) {
    file_put_contents($logFile, "Error: " . $e->getMessage() . "\n\n", FILE_APPEND);
    echo json_encode(['resultCode' => 99, 'message' => $e->getMessage()]);
}
