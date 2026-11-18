<?php
/**
 * Trang hiển thị sau khi thanh toán MoMo
 * Tự động kiểm tra trạng thái thanh toán và cập nhật database
 */
session_start();
require_once 'includes/config.php';

$order_id = $_GET['order_id'] ?? $_SESSION['momo_order_id'] ?? null;
$page_title = 'Kết quả thanh toán MoMo';

if (!$order_id) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT dh.*, tt.transaction_id, tt.status as payment_status 
    FROM don_hang dh 
    LEFT JOIN thanh_toan tt ON dh.id = tt.don_hang_id AND tt.payment_gateway = 'momo'
    WHERE dh.id = ?
    ORDER BY tt.created_at DESC LIMIT 1");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: index.php');
    exit;
}

require_once 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto">
            
            <!-- Loading state -->
            <div id="checking-status" class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-pink-500 border-t-transparent mx-auto mb-4"></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Đang kiểm tra thanh toán...</h2>
                <p class="text-gray-600">Vui lòng đợi trong giây lát</p>
            </div>
            
            <!-- Success state -->
            <div id="payment-success" class="bg-white rounded-2xl shadow-lg p-8 text-center hidden">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Thanh toán thành công!</h2>
                <p class="text-gray-600 mb-6">Đơn hàng của bạn đã được thanh toán qua MoMo</p>
                
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Mã đơn hàng:</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($order['ma_don_hang']); ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Số tiền:</span>
                        <span class="font-semibold text-green-600"><?php echo number_format($order['tong_tien']); ?> VNĐ</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Trạng thái:</span>
                        <span class="font-semibold text-green-600">Đã thanh toán</span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <a href="order-detail.php?id=<?php echo $order_id; ?>" 
                       class="block w-full bg-pink-600 text-white py-3 rounded-xl font-semibold hover:bg-pink-700 transition">
                        Xem chi tiết đơn hàng
                    </a>
                    <a href="my-orders.php" 
                       class="block w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition">
                        Đơn hàng của tôi
                    </a>
                </div>
            </div>
            
            <!-- Failed/Pending state -->
            <div id="payment-failed" class="bg-white rounded-2xl shadow-lg p-8 text-center hidden">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Chưa nhận được thanh toán</h2>
                <p class="text-gray-600 mb-6" id="failed-message">Vui lòng kiểm tra lại hoặc thử thanh toán lại</p>
                
                <div class="space-y-3">
                    <button onclick="checkPaymentStatus()" 
                            class="block w-full bg-pink-600 text-white py-3 rounded-xl font-semibold hover:bg-pink-700 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Kiểm tra lại
                    </button>
                    <a href="order-detail.php?id=<?php echo $order_id; ?>" 
                       class="block w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition">
                        Xem đơn hàng
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
const orderId = <?php echo $order_id; ?>;
let checkCount = 0;
const maxChecks = 10;

async function checkPaymentStatus() {
    document.getElementById('checking-status').classList.remove('hidden');
    document.getElementById('payment-success').classList.add('hidden');
    document.getElementById('payment-failed').classList.add('hidden');
    
    try {
        const response = await fetch('api/momo-check-status.php?order_id=' + orderId);
        const data = await response.json();
        
        document.getElementById('checking-status').classList.add('hidden');
        
        if (data.success && data.paid) {
            document.getElementById('payment-success').classList.remove('hidden');
        } else if (data.pending && checkCount < maxChecks) {
            // Đang chờ - thử lại sau 3 giây
            checkCount++;
            setTimeout(checkPaymentStatus, 3000);
            document.getElementById('checking-status').classList.remove('hidden');
        } else {
            document.getElementById('payment-failed').classList.remove('hidden');
            if (data.message) {
                document.getElementById('failed-message').textContent = data.message;
            }
        }
    } catch (error) {
        document.getElementById('checking-status').classList.add('hidden');
        document.getElementById('payment-failed').classList.remove('hidden');
        document.getElementById('failed-message').textContent = 'Lỗi kết nối. Vui lòng thử lại.';
    }
}

// Tự động kiểm tra khi load trang
document.addEventListener('DOMContentLoaded', function() {
    checkPaymentStatus();
});
</script>

<?php require_once 'includes/footer.php'; ?>
