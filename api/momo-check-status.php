<?php
/**
 * API kiểm tra trạng thái thanh toán MoMo
 * Gọi API này sau khi user thanh toán xong để cập nhật trạng thái đơn hàng
 */
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$logFile = '../debug-momo-check.txt';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? $_GET['order_id'] ?? null;
    
    if (!$order_id) {
        throw new Exception('Thiếu order_id');
    }
    
    // Lấy thông tin giao dịch MoMo gần nhất của đơn hàng
    $stmt = $conn->prepare("SELECT * FROM thanh_toan WHERE don_hang_id = ? AND payment_gateway = 'momo' ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    
    if (!$payment) {
        throw new Exception('Không tìm thấy giao dịch MoMo');
    }
    
    $orderId = $payment['transaction_id'];
    
    // Cấu hình MoMo
    $partnerCode = $_ENV['MOMO_PARTNER_CODE'] ?? getenv('MOMO_PARTNER_CODE') ?: 'MOMO';
    $accessKey = $_ENV['MOMO_ACCESS_KEY'] ?? getenv('MOMO_ACCESS_KEY') ?: 'F8BBA842ECF85';
    $secretKey = $_ENV['MOMO_SECRET_KEY'] ?? getenv('MOMO_SECRET_KEY') ?: 'K951B6PE1waDMi640xX08PD3vg6EkVlz';
    
    // Tạo request kiểm tra trạng thái
    $requestId = $orderId . '_check_' . time();
    
    $rawSignature = "accessKey=" . $accessKey 
        . "&orderId=" . $orderId 
        . "&partnerCode=" . $partnerCode 
        . "&requestId=" . $requestId;
    
    $signature = hash_hmac("sha256", $rawSignature, $secretKey);
    
    $requestData = [
        'partnerCode' => $partnerCode,
        'requestId' => $requestId,
        'orderId' => $orderId,
        'signature' => $signature,
        'lang' => 'vi'
    ];
    
    // Log request
    file_put_contents($logFile, "\n" . date('Y-m-d H:i:s') . " - CHECK STATUS\n", FILE_APPEND);
    file_put_contents($logFile, "Request: " . json_encode($requestData, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
    
    // Gọi API kiểm tra trạng thái
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://test-payment.momo.vn/v2/gateway/api/query',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    
    $result = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    file_put_contents($logFile, "Response: " . $result . "\n\n", FILE_APPEND);
    
    if ($curlError) {
        throw new Exception('Lỗi kết nối: ' . $curlError);
    }
    
    $response = json_decode($result, true);
    
    if (!$response) {
        throw new Exception('Không thể đọc response');
    }
    
    // Kiểm tra kết quả
    if ($response['resultCode'] == 0) {
        // Thanh toán thành công - cập nhật database
        $stmt = $conn->prepare("UPDATE thanh_toan SET status = 'success', paid_at = NOW() WHERE transaction_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE don_hang SET trang_thai_thanh_toan = 'paid', trang_thai = 'processing', updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // Cập nhật hóa đơn
        $stmt = $conn->prepare("UPDATE hoa_don SET status = 'paid' WHERE don_hang_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        file_put_contents($logFile, "SUCCESS: Order #$order_id marked as paid\n", FILE_APPEND);
        
        echo json_encode([
            'success' => true,
            'paid' => true,
            'message' => 'Thanh toán thành công',
            'transId' => $response['transId'] ?? null
        ]);
    } else if ($response['resultCode'] == 1000) {
        // Giao dịch đang chờ xử lý
        echo json_encode([
            'success' => true,
            'paid' => false,
            'pending' => true,
            'message' => 'Đang chờ thanh toán'
        ]);
    } else {
        // Thanh toán thất bại hoặc hủy
        $stmt = $conn->prepare("UPDATE thanh_toan SET status = 'failed' WHERE transaction_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'paid' => false,
            'message' => $response['message'] ?? 'Thanh toán không thành công',
            'resultCode' => $response['resultCode']
        ]);
    }
    
} catch (Exception $e) {
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
