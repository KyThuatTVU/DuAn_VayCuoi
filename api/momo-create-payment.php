<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

// Log file
$logFile = '../debug-momo-request.txt';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? null;
    
    if (!$order_id) {
        throw new Exception('Thiếu thông tin đơn hàng');
    }
    
    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("SELECT * FROM don_hang WHERE id = ? AND nguoi_dung_id = ?");
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    if ($order['trang_thai_thanh_toan'] === 'paid') {
        throw new Exception('Đơn hàng đã được thanh toán');
    }
    
    // Kiểm tra giới hạn số tiền (MoMo Test: 10,000 - 50,000,000)
    $amount_int = (int)$order['tong_tien'];
    if ($amount_int < 10000) {
        throw new Exception('Số tiền tối thiểu là 10,000 VNĐ');
    }
    if ($amount_int > 50000000) {
        throw new Exception('Số tiền vượt quá giới hạn 50,000,000 VNĐ của MoMo Test');
    }
    
    // Cấu hình MoMo - Thông tin test chính thức
    $partnerCode = $_ENV['MOMO_PARTNER_CODE'] ?? getenv('MOMO_PARTNER_CODE') ?: 'MOMO';
    $accessKey = $_ENV['MOMO_ACCESS_KEY'] ?? getenv('MOMO_ACCESS_KEY') ?: 'F8BBA842ECF85';
    $secretKey = $_ENV['MOMO_SECRET_KEY'] ?? getenv('MOMO_SECRET_KEY') ?: 'K951B6PE1waDMi640xX08PD3vg6EkVlz';
    $endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
    
    // URL redirect - lấy từ .env (phải là public URL, không phải localhost)
    $redirectUrl = $_ENV['MOMO_REDIRECT_URL'] ?? getenv('MOMO_REDIRECT_URL') ?: 'https://momo.vn';
    $ipnUrl = $_ENV['MOMO_IPN_URL'] ?? getenv('MOMO_IPN_URL') ?: 'https://momo.vn';
    
    // Tạo orderId và requestId unique - format: MOMO_orderid_timestamp
    $orderId = 'MOMO_' . $order_id . '_' . time() . rand(100, 999);
    $requestId = $orderId;
    $amount = (string)$amount_int;
    $orderInfo = 'Thanh toan don hang ' . $order['ma_don_hang'];
    // Sử dụng payWithATM để hiển thị form nhập thẻ ATM test
    // Thẻ test: 9704 0000 0000 0018 | NGUYEN VAN A | 03/07 | OTP: OTP
    $requestType = 'payWithATM';
    $extraData = base64_encode(json_encode(['order_id' => $order_id]));
    
    // Tạo chữ ký theo đúng format MoMo API v2
    $rawSignature = "accessKey=" . $accessKey 
        . "&amount=" . $amount 
        . "&extraData=" . $extraData 
        . "&ipnUrl=" . $ipnUrl 
        . "&orderId=" . $orderId 
        . "&orderInfo=" . $orderInfo 
        . "&partnerCode=" . $partnerCode 
        . "&redirectUrl=" . $redirectUrl 
        . "&requestId=" . $requestId 
        . "&requestType=" . $requestType;
    
    $signature = hash_hmac("sha256", $rawSignature, $secretKey);
    
    // Request data
    $requestData = [
        'partnerCode' => $partnerCode,
        'partnerName' => 'Vay Cuoi Thien Than',
        'storeId' => $partnerCode,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'lang' => 'vi',
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature
    ];
    
    // Log request
    file_put_contents($logFile, "\n" . date('Y-m-d H:i:s') . " - NEW REQUEST\n", FILE_APPEND);
    file_put_contents($logFile, "Raw Signature: " . $rawSignature . "\n", FILE_APPEND);
    file_put_contents($logFile, "Request: " . json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
    
    // Gửi request đến MoMo
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($requestData))
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Log response
    file_put_contents($logFile, "HTTP Code: $httpCode\n", FILE_APPEND);
    file_put_contents($logFile, "Response: " . $result . "\n\n", FILE_APPEND);
    
    if ($curlError) {
        throw new Exception('Lỗi kết nối: ' . $curlError);
    }
    
    $response = json_decode($result, true);
    
    if (!$response) {
        throw new Exception('Không thể đọc response từ MoMo');
    }
    
    // Kiểm tra kết quả
    if (isset($response['resultCode']) && $response['resultCode'] == 0) {
        // Lưu giao dịch vào database
        $hoa_don_id = null;
        $stmt_hd = $conn->prepare("SELECT id FROM hoa_don WHERE don_hang_id = ?");
        $stmt_hd->bind_param("i", $order_id);
        $stmt_hd->execute();
        $result_hd = $stmt_hd->get_result();
        if ($row_hd = $result_hd->fetch_assoc()) {
            $hoa_don_id = $row_hd['id'];
        }
        
        // Insert thanh_toan record
        if ($hoa_don_id) {
            $stmt = $conn->prepare("INSERT INTO thanh_toan (hoa_don_id, don_hang_id, payment_gateway, transaction_id, amount, status, created_at) VALUES (?, ?, 'momo', ?, ?, 'initiated', NOW())");
            $stmt->bind_param("iisd", $hoa_don_id, $order_id, $orderId, $order['tong_tien']);
        } else {
            $stmt = $conn->prepare("INSERT INTO thanh_toan (don_hang_id, payment_gateway, transaction_id, amount, status, created_at) VALUES (?, 'momo', ?, ?, 'initiated', NOW())");
            $stmt->bind_param("isd", $order_id, $orderId, $order['tong_tien']);
        }
        $stmt->execute();
        
        // Lưu order_id vào session để trang success có thể kiểm tra
        $_SESSION['momo_order_id'] = $order_id;
        $_SESSION['momo_transaction_id'] = $orderId;
        
        // Trả về URL thanh toán và URL kiểm tra sau khi thanh toán
        $siteUrl = $_ENV['SITE_URL'] ?? getenv('SITE_URL') ?: 'http://localhost:8000';
        
        echo json_encode([
            'success' => true,
            'payUrl' => $response['payUrl'],
            'successUrl' => $siteUrl . '/momo-success.php?order_id=' . $order_id,
            'qrCodeUrl' => $response['qrCodeUrl'] ?? null,
            'deeplink' => $response['deeplink'] ?? null
        ]);
    } else {
        $errorMsg = $response['message'] ?? 'Lỗi không xác định';
        $resultCode = $response['resultCode'] ?? 'N/A';
        throw new Exception("MoMo Error ($resultCode): $errorMsg");
    }
    
} catch (Exception $e) {
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
