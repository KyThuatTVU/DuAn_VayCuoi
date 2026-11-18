<?php
require_once '../includes/config.php';

// Log IPN request - hỗ trợ cả POST và JSON body
$logFile = '../debug-momo-ipn.txt';
$rawInput = file_get_contents('php://input');
file_put_contents($logFile, date('Y-m-d H:i:s') . " - IPN Request:\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "RAW: " . $rawInput . "\n\n", FILE_APPEND);

header('Content-Type: application/json');

try {
    // MoMo có thể gửi dữ liệu qua POST hoặc JSON body
    $data = $_POST;
    if (empty($data) && !empty($rawInput)) {
        $data = json_decode($rawInput, true) ?? [];
    }
    
    $partnerCode = $data['partnerCode'] ?? '';
    $orderId = $data['orderId'] ?? '';
    $requestId = $data['requestId'] ?? '';
    $amount = $data['amount'] ?? '';
    $orderInfo = $data['orderInfo'] ?? '';
    $orderType = $data['orderType'] ?? '';
    $transId = $data['transId'] ?? '';
    $resultCode = $data['resultCode'] ?? '';
    $message = $data['message'] ?? '';
    $payType = $data['payType'] ?? '';
    $responseTime = $data['responseTime'] ?? '';
    $extraData = $data['extraData'] ?? '';
    $signature = $data['signature'] ?? '';
    
    // Lấy cấu hình MoMo từ env (hỗ trợ cả $_ENV và getenv)
    $secretKey = $_ENV['MOMO_SECRET_KEY'] ?? getenv('MOMO_SECRET_KEY');
    $accessKey = $_ENV['MOMO_ACCESS_KEY'] ?? getenv('MOMO_ACCESS_KEY');
    
    // Xác thực chữ ký - theo đúng format MoMo API v2
    $rawHash = "accessKey=" . $accessKey . 
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
    
    // Log để debug
    file_put_contents($logFile, "Raw Hash: " . $rawHash . "\n", FILE_APPEND);
    file_put_contents($logFile, "Expected: " . $checkSignature . "\n", FILE_APPEND);
    file_put_contents($logFile, "Received: " . $signature . "\n\n", FILE_APPEND);
    
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
