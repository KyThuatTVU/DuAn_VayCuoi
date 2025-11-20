<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy order_id từ session hoặc URL
$order_id = intval($_GET['order_id'] ?? $_SESSION['order_info']['order_id'] ?? 0);

if ($order_id <= 0) {
    header('Location: cart.php');
    exit;
}

// Lấy thông tin đơn hàng từ database
$order_query = $conn->prepare("SELECT 
    dh.*,
    t.transaction_id,
    DATE_ADD(dh.created_at, INTERVAL 10 MINUTE) as thoi_gian_het_han
FROM don_hang dh
LEFT JOIN thanh_toan t ON dh.id = t.don_hang_id
WHERE dh.id = ? AND dh.nguoi_dung_id = ?");

$order_query->bind_param("ii", $order_id, $_SESSION['user_id']);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows === 0) {
    header('Location: my-orders.php');
    exit;
}

$order_data = $order_result->fetch_assoc();

// Kiểm tra đã thanh toán chưa
if ($order_data['trang_thai_thanh_toan'] === 'paid') {
    header('Location: order-success.php?order_id=' . $order_id);
    exit;
}

// Kiểm tra đã hết hạn chưa
$now = new DateTime();
$expiry = new DateTime($order_data['thoi_gian_het_han']);
if ($now > $expiry) {
    // Đã hết hạn
    header('Location: my-orders.php?expired=1');
    exit;
}

// Gán biến
$ma_giao_dich = $order_data['transaction_id'] ?? $order_data['ma_don_hang'];
$total = $order_data['tong_tien'];
$noi_dung = $order_data['ma_don_hang'] . ' ' . $order_data['ho_ten'];
$thoi_gian_het_han = $order_data['thoi_gian_het_han'];

// Thông tin ngân hàng (từ ảnh của bạn)
$bank_id = 'VCB'; // Vietcombank
$account_no = '1052053578';
$account_name = 'NGUYEN HUYNH KY THUAT';
$amount = $total;
$addInfo = $noi_dung;

// Tạo URL QR code sử dụng API VietQR
$template = 'compact'; // compact, print, qr_only
$qr_url = "https://img.vietqr.io/image/{$bank_id}-{$account_no}-{$template}.png?amount={$amount}&addInfo=" . urlencode($addInfo) . "&accountName=" . urlencode($account_name);

$page_title = 'Thanh Toán QR Code';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-slow {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 283; }
        }
        .countdown-circle {
            animation: countdown 600s linear;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">💳 Thanh Toán Đơn Hàng</h1>
            <p class="text-gray-600">Quét mã QR để hoàn tất thanh toán</p>
        </div>

        <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8">
            <!-- QR Code Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Quét Mã QR</h2>
                    <p class="text-gray-600">Sử dụng app ngân hàng để quét mã</p>
                </div>

                <!-- QR Code -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-6 mb-6">
                        <div class="bg-white rounded-xl p-4">
                            <img src="<?php echo $qr_url; ?>" 
                                 alt="QR Code" 
                                 class="w-full h-auto"
                                 id="qr-image">
                        </div>
                        
                        <!-- Bank Info -->
                        <div class="text-center mt-4 text-white">
                            <p class="font-bold text-lg"><?php echo $account_name; ?></p>
                            <p class="text-sm opacity-90"><?php echo $account_no; ?></p>
                        </div>
                    </div>
                    
                    <!-- Countdown Timer -->
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center gap-3 bg-yellow-50 px-6 py-3 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Thời gian còn lại</p>
                                <p class="text-2xl font-bold text-yellow-600" id="countdown">10:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                <div id="payment-status" class="text-center">
                    <div class="pulse-slow">
                        <div class="inline-flex items-center gap-2 bg-blue-50 px-6 py-3 rounded-full">
                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-ping"></div>
                            <span class="text-blue-700 font-semibold">Đang chờ thanh toán...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Info -->
            <div class="space-y-6">
                <!-- Order Details -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">📋 Thông Tin Đơn Hàng</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Mã đơn hàng:</span>
                            <span class="font-bold text-gray-800"><?php echo htmlspecialchars($order_data['ma_don_hang']); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Mã giao dịch:</span>
                            <span class="font-mono text-sm text-gray-800"><?php echo htmlspecialchars($ma_giao_dich); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">Số tiền:</span>
                            <span class="font-bold text-2xl text-pink-600"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Nội dung CK:</span>
                            <span class="font-mono text-sm text-gray-800"><?php echo htmlspecialchars($noi_dung); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">📱 Hướng Dẫn Thanh Toán</h3>
                    
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            <span>Mở app <strong>Ngân hàng</strong> trên điện thoại</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            <span>Chọn <strong>Quét mã QR</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                            <span>Quét mã QR bên trái</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</span>
                            <span>Kiểm tra thông tin và <strong>Xác nhận thanh toán</strong></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">5</span>
                            <span>Chờ hệ thống xác nhận (tự động)</span>
                        </li>
                    </ol>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                    <div class="flex gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        <div class="text-sm text-gray-700">
                            <p class="font-bold mb-1">Lưu ý quan trọng:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Mã QR có hiệu lực <strong>10 phút</strong></li>
                                <li>• Chuyển khoản <strong>đúng số tiền</strong> và <strong>nội dung</strong></li>
                                <li>• Không tắt trang này cho đến khi thanh toán thành công</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button onclick="window.print()" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                        <i class="fas fa-print mr-2"></i>In QR
                    </button>
                    <button onclick="location.reload()" class="flex-1 bg-blue-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-600 transition-all">
                        <i class="fas fa-sync mr-2"></i>Làm mới
                    </button>
                </div>
                

            </div>
        </div>
    </div>

    <script>
    // Countdown timer
    const endTime = new Date('<?php echo $thoi_gian_het_han; ?>').getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endTime - now;
        
        if (distance < 0) {
            document.getElementById('countdown').textContent = '00:00';
            document.getElementById('payment-status').innerHTML = `
                <div class="bg-red-50 px-6 py-4 rounded-xl">
                    <p class="text-red-600 font-bold">⏰ Mã QR đã hết hạn</p>
                    <button onclick="location.href='checkout.php'" class="mt-3 bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600">
                        Tạo mã mới
                    </button>
                </div>
            `;
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('countdown').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    }
    
    setInterval(updateCountdown, 1000);
    updateCountdown();
    
    // Check payment status
    function checkPaymentStatus() {
        fetch('api/check-payment.php?order_id=<?php echo $order_id; ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status === 'paid') {
                document.getElementById('payment-status').innerHTML = `
                    <div class="bg-green-50 px-6 py-4 rounded-xl">
                        <i class="fas fa-check-circle text-green-500 text-4xl mb-2"></i>
                        <p class="text-green-600 font-bold text-lg">Thanh toán thành công!</p>
                        <p class="text-sm text-gray-600 mt-2">Đang chuyển hướng...</p>
                    </div>
                `;
                setTimeout(() => {
                    window.location.href = 'order-success.php?order_id=<?php echo $order_id; ?>';
                }, 2000);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Check every 5 seconds
    setInterval(checkPaymentStatus, 5000);
    checkPaymentStatus();
    </script>
</body>
</html>
