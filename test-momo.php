<?php
require_once 'includes/config.php';

echo "<style>
    body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
    h1 { color: #333; border-bottom: 3px solid #d82d8b; padding-bottom: 10px; }
    h2 { color: #555; margin-top: 30px; background: #f5f5f5; padding: 10px; border-left: 4px solid #d82d8b; }
    h3 { color: #666; }
    pre { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    table td, table th { padding: 8px; border: 1px solid #ddd; text-align: left; }
    table th { background: #f5f5f5; font-weight: bold; }
    .button { display: inline-block; background: #d82d8b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    .button:hover { background: #b8256f; }
</style>";
echo "<h1>Test MoMo Payment Configuration</h1>";

// Kiểm tra cấu hình
echo "<h2>1. Kiểm tra cấu hình MoMo</h2>";
echo "<pre>";
echo "MOMO_PARTNER_CODE: " . ($_ENV['MOMO_PARTNER_CODE'] ?? 'KHÔNG TÌM THẤY') . "\n";
echo "MOMO_ACCESS_KEY: " . ($_ENV['MOMO_ACCESS_KEY'] ?? 'KHÔNG TÌM THẤY') . "\n";
echo "MOMO_SECRET_KEY: " . (isset($_ENV['MOMO_SECRET_KEY']) ? '***' . substr($_ENV['MOMO_SECRET_KEY'], -4) : 'KHÔNG TÌM THẤY') . "\n";
echo "MOMO_ENDPOINT: " . ($_ENV['MOMO_ENDPOINT'] ?? 'KHÔNG TÌM THẤY') . "\n";
echo "MOMO_REDIRECT_URL: " . ($_ENV['MOMO_REDIRECT_URL'] ?? 'KHÔNG TÌM THẤY') . "\n";
echo "MOMO_IPN_URL: " . ($_ENV['MOMO_IPN_URL'] ?? 'KHÔNG TÌM THẤY') . "\n";
echo "</pre>";

// Kiểm tra bảng thanh_toan
echo "<h2>2. Kiểm tra bảng thanh_toan</h2>";
$result = $conn->query("SHOW TABLES LIKE 'thanh_toan'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Bảng thanh_toan tồn tại</p>";
    
    // Xem cấu trúc bảng
    echo "<h3>Cấu trúc bảng thanh_toan:</h3>";
    echo "<pre>";
    $columns = $conn->query("DESCRIBE thanh_toan");
    while ($col = $columns->fetch_assoc()) {
        echo $col['Field'] . " - " . $col['Type'] . " - " . $col['Null'] . " - " . $col['Key'] . "\n";
    }
    echo "</pre>";
    
    // Xem dữ liệu mẫu
    echo "<h3>Dữ liệu trong bảng thanh_toan:</h3>";
    $payments = $conn->query("SELECT * FROM thanh_toan ORDER BY created_at DESC LIMIT 5");
    if ($payments->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Don Hang ID</th><th>Gateway</th><th>Transaction ID</th><th>Amount</th><th>Status</th><th>Created At</th></tr>";
        while ($payment = $payments->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $payment['id'] . "</td>";
            echo "<td>" . $payment['don_hang_id'] . "</td>";
            echo "<td>" . $payment['payment_gateway'] . "</td>";
            echo "<td>" . $payment['transaction_id'] . "</td>";
            echo "<td>" . number_format($payment['amount']) . "</td>";
            echo "<td>" . $payment['status'] . "</td>";
            echo "<td>" . $payment['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Chưa có giao dịch nào</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Bảng thanh_toan không tồn tại</p>";
}

// Kiểm tra bảng don_hang
echo "<h2>3. Kiểm tra bảng don_hang</h2>";
$result = $conn->query("SHOW TABLES LIKE 'don_hang'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Bảng don_hang tồn tại</p>";
    
    // Xem đơn hàng mẫu
    echo "<h3>Đơn hàng gần đây:</h3>";
    $orders = $conn->query("SELECT id, ma_don_hang, ho_ten, tong_tien, trang_thai, trang_thai_thanh_toan, phuong_thuc_thanh_toan, created_at FROM don_hang ORDER BY created_at DESC LIMIT 5");
    if ($orders->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Mã ĐH</th><th>Họ tên</th><th>Tổng tiền</th><th>Trạng thái</th><th>TT Thanh toán</th><th>PT Thanh toán</th><th>Ngày tạo</th></tr>";
        while ($order = $orders->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $order['id'] . "</td>";
            echo "<td>" . $order['ma_don_hang'] . "</td>";
            echo "<td>" . $order['ho_ten'] . "</td>";
            echo "<td>" . number_format($order['tong_tien']) . "</td>";
            echo "<td>" . $order['trang_thai'] . "</td>";
            echo "<td>" . $order['trang_thai_thanh_toan'] . "</td>";
            echo "<td>" . $order['phuong_thuc_thanh_toan'] . "</td>";
            echo "<td>" . $order['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Chưa có đơn hàng nào</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Bảng don_hang không tồn tại</p>";
}

// Test tạo signature MoMo
echo "<h2>4. Test tạo chữ ký MoMo</h2>";
$partnerCode = $_ENV['MOMO_PARTNER_CODE'] ?? getenv('MOMO_PARTNER_CODE');
$accessKey = $_ENV['MOMO_ACCESS_KEY'] ?? getenv('MOMO_ACCESS_KEY');
$secretKey = $_ENV['MOMO_SECRET_KEY'] ?? getenv('MOMO_SECRET_KEY');

if ($accessKey && $secretKey) {
    $orderId = 'TEST_' . time();
    $testData = [
        'accessKey' => $accessKey,
        'amount' => '100000', // Phải là integer string, không có dấu thập phân
        'extraData' => '',
        'ipnUrl' => $_ENV['MOMO_IPN_URL'] ?? getenv('MOMO_IPN_URL'),
        'orderId' => $orderId,
        'orderInfo' => 'Thanh toan test', // Không dấu
        'partnerCode' => $partnerCode,
        'redirectUrl' => $_ENV['MOMO_REDIRECT_URL'] ?? getenv('MOMO_REDIRECT_URL'),
        'requestId' => $orderId,
        'requestType' => 'payWithATM' // Cho phép thanh toán bằng thẻ ATM/Credit/Ví MoMo
    ];
    
    $rawHash = "accessKey=" . $testData['accessKey'] . 
               "&amount=" . $testData['amount'] . 
               "&extraData=" . $testData['extraData'] . 
               "&ipnUrl=" . $testData['ipnUrl'] . 
               "&orderId=" . $testData['orderId'] . 
               "&orderInfo=" . $testData['orderInfo'] . 
               "&partnerCode=" . $testData['partnerCode'] . 
               "&redirectUrl=" . $testData['redirectUrl'] . 
               "&requestId=" . $testData['requestId'] . 
               "&requestType=" . $testData['requestType'];
    
    $signature = hash_hmac("sha256", $rawHash, $secretKey);
    
    echo "<pre>";
    echo "Raw Hash String:\n" . $rawHash . "\n\n";
    echo "Signature: " . $signature . "\n";
    echo "</pre>";
    echo "<p style='color: green;'>✓ Có thể tạo chữ ký MoMo</p>";
    
    // Test gửi request thật đến MoMo
    echo "<h3>Test gửi request đến MoMo:</h3>";
    $endpoint = $_ENV['MOMO_ENDPOINT'] ?? getenv('MOMO_ENDPOINT');
    
    $requestData = [
        'partnerCode' => $partnerCode,
        'accessKey' => $accessKey,
        'requestId' => $testData['requestId'],
        'amount' => $testData['amount'],
        'orderId' => $testData['orderId'],
        'orderInfo' => $testData['orderInfo'],
        'redirectUrl' => $testData['redirectUrl'],
        'ipnUrl' => $testData['ipnUrl'],
        'extraData' => $testData['extraData'],
        'requestType' => $testData['requestType'],
        'signature' => $signature,
        'lang' => 'vi'
    ];
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($requestData))
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    if ($curlError) {
        echo "<p style='color: red;'><strong>CURL Error:</strong> $curlError</p>";
    }
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
    
    $response = json_decode($result, true);
    if ($response && isset($response['resultCode'])) {
        if ($response['resultCode'] == 0) {
            echo "<p style='color: green;'>✓ Kết nối MoMo thành công!</p>";
            echo "<p><a href='" . $response['payUrl'] . "' target='_blank' class='button'>Test thanh toán</a></p>";
        } else {
            echo "<p style='color: orange;'>⚠ MoMo trả về lỗi: " . ($response['message'] ?? 'Unknown') . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>✗ Thiếu cấu hình MoMo</p>";
}

echo "<hr>";
echo "<p><a href='checkout.php'>← Quay lại trang thanh toán</a></p>";
?>
