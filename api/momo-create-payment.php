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
    
    // Kiểm tra giới hạn số tiền (MoMo Test)
    $amount_int = (int)$order['tong_tien'];
    if ($amount_int < 10000) {
        throw new Exception('Số tiền tối thiểu là 10,000 VNĐ');
    }
    if ($amount_int > 50000000) {
        throw new Exception('Số tiền vượt quá giới hạn 50,000,000 VNĐ của MoMo Test. Vui lòng chọn phương thức thanh toán khác hoặc chia nhỏ đơn hàng.');
    }
    
    // Cấu hình MoMo
    $partnerCode = $_ENV['MOMO_PARTNER_CODE'] ?? getenv('MOMO_PARTNER_CODE');
    $accessKey = $_ENV['MOMO_ACCESS_KEY'] ?? getenv('MOMO_ACCESS_KEY');
    $secretKey = $_ENV['MOMO_SECRET_KEY'] ?? getenv('MOMO_SECRET_KEY');
    $endpoint = $_ENV['MOMO_ENDPOINT'] ?? getenv('MOMO_ENDPOINT');
    $redirectUrl = $_ENV['MOMO_REDIRECT_URL'] ?? getenv('MOMO_REDIRECT_URL');
    $ipnUrl = $_ENV['MOMO_IPN_URL'] ?? getenv('MOMO_IPN_URL');
    
    // Kiểm tra cấu hình
    if (!$partnerCode || !$accessKey || !$secretKey || !$endpoint) {
        throw new Exception('Thiếu cấu hình MoMo. Vui lòng kiểm tra file .env');
    }
    
    $orderId = 'MOMO_' . $order_id . '_' . time();
    $requestId = $orderId;
    $amount = (string)(int)$order['tong_tien']; // Phải là integer, không có dấu thập phân
    $orderInfo = 'Thanh toan don hang #' . $order['ma_don_hang']; // Không dấu để tránh encoding issue
    $requestType = 'payWithMoMo'; // Thanh toán bằng ví MoMo
    $extraData = '';
    
    // Tạo chữ ký
    $rawHash = "accessKey=" . $accessKey . 
               "&amount=" . $amount . 
               "&extraData=" . $extraData . 
               "&ipnUrl=" . $ipnUrl . 
               "&orderId=" . $orderId . 
               "&orderInfo=" . $orderInfo . 
               "&partnerCode=" . $partnerCode . 
               "&redirectUrl=" . $redirectUrl . 
               "&requestId=" . $requestId . 
               "&requestType=" . $requestType;
    
    $signature = hash_hmac("sha256", $rawHash, $secretKey);
    
    $requestData = [
        'partnerCode' => $partnerCode,
        'accessKey' => $accessKey,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature,
        'lang' => 'vi'
    ];
    
    // Log request để debug
    $logFile = '../debug-momo-request.txt';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Request:\n" . json_encode($requestData, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
    
    // Gửi request đến MoMo
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($requestData))
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt verify SSL cho test
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Log response
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Response (HTTP $httpCode):\n" . $result . "\n", FILE_APPEND);
    if ($curlError) {
        file_put_contents($logFile, "CURL Error: $curlError\n\n", FILE_APPEND);
    }
    
    if ($result === false || $curlError) {
        throw new Exception('Lỗi kết nối MoMo: ' . $curlError);
    }
    
    if ($httpCode !== 200) {
        throw new Exception('Lỗi kết nối MoMo (HTTP ' . $httpCode . ')');
    }
    
    $response = json_decode($result, true);
    
    if (!$response) {
        throw new Exception('Lỗi parse response từ MoMo');
    }
    
    if ($response['resultCode'] == 0) {
        // Kiểm tra xem đã có giao dịch với transaction_id này chưa
        $check_stmt = $conn->prepare("SELECT id FROM thanh_toan WHERE transaction_id = ?");
        $check_stmt->bind_param("s", $orderId);
        $check_stmt->execute();
        $existing = $check_stmt->get_result()->fetch_assoc();
        
        if (!$existing) {
            // Lấy hoa_don_id nếu có
            $hoa_don_id = null;
            $stmt_hd = $conn->prepare("SELECT id FROM hoa_don WHERE don_hang_id = ?");
            $stmt_hd->bind_param("i", $order_id);
            $stmt_hd->execute();
            $result_hd = $stmt_hd->get_result();
            if ($row_hd = $result_hd->fetch_assoc()) {
                $hoa_don_id = $row_hd['id'];
            }
            
            // Lưu thông tin giao dịch vào bảng thanh_toan
            if ($hoa_don_id) {
                $stmt = $conn->prepare("INSERT INTO thanh_toan (hoa_don_id, don_hang_id, payment_gateway, transaction_id, amount, status, created_at) VALUES (?, ?, 'momo', ?, ?, 'initiated', NOW())");
                $stmt->bind_param("iisd", $hoa_don_id, $order_id, $orderId, $order['tong_tien']);
            } else {
                $stmt = $conn->prepare("INSERT INTO thanh_toan (don_hang_id, payment_gateway, transaction_id, amount, status, created_at) VALUES (?, 'momo', ?, ?, 'initiated', NOW())");
                $stmt->bind_param("isd", $order_id, $orderId, $order['tong_tien']);
            }
            $stmt->execute();
        }
        
        echo json_encode([
            'success' => true,
            'payUrl' => $response['payUrl']
        ]);
    } else {
        throw new Exception($response['message'] ?? 'Lỗi tạo thanh toán MoMo');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
