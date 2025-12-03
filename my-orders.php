<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Đơn Hàng Của Tôi';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=my-orders.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng
$orders_query = $conn->prepare("SELECT 
    dh.*,
    t.transaction_id,
    t.status as payment_transaction_status,
    TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) as minutes_ago,
    CASE 
        WHEN dh.trang_thai_thanh_toan = 'pending' 
        AND dh.trang_thai != 'cancelled'
        AND TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) < 10 
        AND (t.status IS NULL OR t.status NOT IN ('success', 'completed'))
        THEN 1
        ELSE 0
    END as can_continue_payment
FROM don_hang dh
LEFT JOIN thanh_toan t ON dh.id = t.don_hang_id
WHERE dh.nguoi_dung_id = ?
ORDER BY dh.created_at DESC");

$orders_query->bind_param("i", $user_id);
$orders_query->execute();
$orders = $orders_query->get_result();

require_once 'includes/header.php';
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">📦 Đơn Hàng Của Tôi</h1>
        
        <?php if ($orders && $orders->num_rows > 0): ?>
            <div class="space-y-4">
                <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">
                                Đơn hàng #<?php echo htmlspecialchars($order['ma_don_hang']); ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                (<?php echo $order['minutes_ago']; ?> phút trước)
                            </p>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-2xl font-bold text-pink-600">
                                <?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Trạng thái đơn hàng:</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                <?php 
                                switch($order['trang_thai']) {
                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                    case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                    case 'completed': echo 'bg-green-100 text-green-800'; break;
                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                }
                                ?>">
                                <?php 
                                switch($order['trang_thai']) {
                                    case 'pending': echo '⏳ Chờ xử lý'; break;
                                    case 'processing': echo '🔄 Đang xử lý'; break;
                                    case 'completed': echo '✅ Hoàn thành'; break;
                                    case 'cancelled': echo '❌ Đã hủy'; break;
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Trạng thái thanh toán:</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                <?php 
                                switch($order['trang_thai_thanh_toan']) {
                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                    case 'paid': echo 'bg-green-100 text-green-800'; break;
                                    case 'failed': echo 'bg-red-100 text-red-800'; break;
                                    case 'expired': echo 'bg-gray-100 text-gray-800'; break;
                                }
                                ?>">
                                <?php 
                                switch($order['trang_thai_thanh_toan']) {
                                    case 'pending': echo '⏳ Chờ thanh toán'; break;
                                    case 'paid': echo '✅ Đã thanh toán'; break;
                                    case 'failed': echo '❌ Thất bại'; break;
                                    case 'expired': echo '⏰ Hết hạn'; break;
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <?php if ($order['trang_thai_thanh_toan'] == 'paid'): ?>
                            <!-- Đã thanh toán thành công -->
                            <span class="text-green-600 px-6 py-3 font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>
                                Đã thanh toán thành công
                            </span>
                        <?php elseif ($order['can_continue_payment'] == 1): ?>
                            <!-- Có thể tiếp tục thanh toán -->
                            <a href="payment-qr.php?order_id=<?php echo $order['id']; ?>" 
                               class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                                <i class="fas fa-qrcode mr-2"></i>
                                Tiếp tục thanh toán (còn <?php echo 10 - $order['minutes_ago']; ?> phút)
                            </a>
                        <?php elseif ($order['trang_thai_thanh_toan'] == 'pending' && $order['minutes_ago'] >= 10): ?>
                            <!-- Đã hết hạn -->
                            <span class="text-gray-500 px-6 py-3">
                                <i class="fas fa-clock mr-2"></i>
                                Đã hết hạn thanh toán
                            </span>
                        <?php elseif ($order['trang_thai_thanh_toan'] == 'failed'): ?>
                            <!-- Thanh toán thất bại -->
                            <span class="text-red-600 px-6 py-3">
                                <i class="fas fa-times-circle mr-2"></i>
                                Thanh toán thất bại
                            </span>
                        <?php endif; ?>
                        
                        <a href="order-detail.php?id=<?php echo $order['id']; ?>" 
                           class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-300 transition-all">
                            <i class="fas fa-eye mr-2"></i>
                            Xem chi tiết
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Chưa có đơn hàng</h3>
                <p class="text-gray-600 mb-6">Bạn chưa có đơn hàng nào</p>
                <a href="products.php" class="inline-block bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Khám phá váy cưới
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
