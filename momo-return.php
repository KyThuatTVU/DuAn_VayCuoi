<?php
session_start();
require_once 'includes/config.php';

// Log return request
$logFile = 'debug-momo-return.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Return Request:\n" . print_r($_GET, true) . "\n\n", FILE_APPEND);

$partnerCode = $_GET['partnerCode'] ?? '';
$orderId = $_GET['orderId'] ?? '';
$requestId = $_GET['requestId'] ?? '';
$amount = $_GET['amount'] ?? '';
$orderInfo = $_GET['orderInfo'] ?? '';
$orderType = $_GET['orderType'] ?? '';
$transId = $_GET['transId'] ?? '';
$resultCode = $_GET['resultCode'] ?? '';
$message = $_GET['message'] ?? '';
$payType = $_GET['payType'] ?? '';
$responseTime = $_GET['responseTime'] ?? '';
$extraData = $_GET['extraData'] ?? '';
$signature = $_GET['signature'] ?? '';

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

// Lấy order_id từ orderId
preg_match('/MOMO_(\d+)_/', $orderId, $matches);
$order_id = $matches[1] ?? null;

// Cập nhật database nếu chữ ký hợp lệ
if ($signature === $checkSignature && $order_id) {
    if ($resultCode == 0) {
        // Thanh toán thành công - Cập nhật database
        // Kiểm tra xem đã cập nhật chưa để tránh duplicate
        $check_stmt = $conn->prepare("SELECT trang_thai_thanh_toan FROM don_hang WHERE id = ?");
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result()->fetch_assoc();
        
        if ($check_result && $check_result['trang_thai_thanh_toan'] !== 'paid') {
            // Cập nhật bảng thanh_toan
            $stmt = $conn->prepare("UPDATE thanh_toan SET status = 'success', paid_at = NOW() WHERE transaction_id = ?");
            $stmt->bind_param("s", $orderId);
            $stmt->execute();
            
            // Cập nhật đơn hàng
            $stmt = $conn->prepare("UPDATE don_hang SET trang_thai_thanh_toan = 'paid', trang_thai = 'processing', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            
            file_put_contents($logFile, "Database updated: Order #$order_id marked as paid\n\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "Order #$order_id already paid, skipping update\n\n", FILE_APPEND);
        }
    } else {
        // Thanh toán thất bại
        $stmt = $conn->prepare("UPDATE thanh_toan SET status = 'failed' WHERE transaction_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE don_hang SET trang_thai_thanh_toan = 'failed', updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        file_put_contents($logFile, "Database updated: Order #$order_id marked as failed\n\n", FILE_APPEND);
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán MoMo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <?php if ($signature === $checkSignature && $resultCode == 0): ?>
                <!-- Thanh toán thành công -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Thanh toán thành công!</h2>
                    <p class="text-gray-600 mb-6">Đơn hàng của bạn đã được thanh toán qua MoMo</p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Mã đơn hàng:</span>
                            <span class="font-semibold">#<?php echo htmlspecialchars($order_id); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Mã giao dịch:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($transId); ?></span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Số tiền:</span>
                            <span class="font-semibold text-green-600"><?php echo number_format($amount); ?> VNĐ</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="order-detail.php?id=<?php echo $order_id; ?>" 
                           class="block w-full bg-pink-600 text-white py-3 rounded-lg hover:bg-pink-700 transition">
                            Xem chi tiết đơn hàng
                        </a>
                        <a href="index.php" 
                           class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition">
                            Về trang chủ
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Thanh toán thất bại -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Thanh toán thất bại</h2>
                    <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($message); ?></p>
                    
                    <?php if ($order_id): ?>
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Mã đơn hàng:</span>
                            <span class="font-semibold">#<?php echo htmlspecialchars($order_id); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="space-y-3">
                        <?php if ($order_id): ?>
                        <a href="checkout.php?order_id=<?php echo $order_id; ?>" 
                           class="block w-full bg-pink-600 text-white py-3 rounded-lg hover:bg-pink-700 transition">
                            Thử lại thanh toán
                        </a>
                        <?php endif; ?>
                        <a href="index.php" 
                           class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition">
                            Về trang chủ
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
