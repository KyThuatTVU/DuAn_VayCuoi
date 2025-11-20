<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Chi Tiết Đơn Hàng';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=order-detail.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);

if ($order_id <= 0) {
    header('Location: my-orders.php');
    exit;
}

// Lấy thông tin đơn hàng
$order_query = $conn->prepare("SELECT 
    dh.*,
    t.transaction_id,
    t.status as payment_status,
    t.paid_at,
    t.amount as payment_amount,
    hd.ma_hoa_don,
    TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) as minutes_ago
FROM don_hang dh
LEFT JOIN thanh_toan t ON dh.id = t.don_hang_id
LEFT JOIN hoa_don hd ON dh.id = hd.don_hang_id
WHERE dh.id = ? AND dh.nguoi_dung_id = ?");

$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows === 0) {
    header('Location: my-orders.php');
    exit;
}

$order = $order_result->fetch_assoc();

// Lấy chi tiết sản phẩm
$details_query = $conn->prepare("SELECT * FROM chi_tiet_hoa_don WHERE hoa_don_id = (SELECT id FROM hoa_don WHERE don_hang_id = ?)");
$details_query->bind_param("i", $order_id);
$details_query->execute();
$details = $details_query->get_result();

require_once 'includes/header.php';
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">
                            Chi Tiết Đơn Hàng
                        </h1>
                        <p class="text-gray-600">
                            Mã đơn hàng: <span class="font-mono font-bold text-pink-600"><?php echo htmlspecialchars($order['ma_don_hang']); ?></span>
                        </p>
                        <p class="text-sm text-gray-500">
                            Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Tổng tiền</p>
                        <p class="text-3xl font-bold text-pink-600">
                            <?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ
                        </p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 pt-4 border-t">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Trạng thái đơn hàng</p>
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-bold
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
                        <p class="text-sm text-gray-600 mb-1">Trạng thái thanh toán</p>
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-bold
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
            </div>

            <!-- Thông tin người nhận -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-user text-blue-500 mr-2"></i>
                    Thông Tin Người Nhận
                </h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Họ và tên</p>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['ho_ten']); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Số điện thoại</p>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['so_dien_thoai']); ?></p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Địa chỉ nhận váy</p>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['dia_chi']); ?></p>
                    </div>
                    
                    <?php if (!empty($order['ghi_chu'])): ?>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Ghi chú</p>
                        <p class="text-gray-800"><?php echo htmlspecialchars($order['ghi_chu']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-shopping-bag text-pink-500 mr-2"></i>
                    Chi Tiết Sản Phẩm
                </h2>
                
                <div class="space-y-4">
                    <?php if ($details && $details->num_rows > 0): ?>
                        <?php while ($item = $details->fetch_assoc()): ?>
                        <div class="flex justify-between items-start p-4 bg-gray-50 rounded-xl">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="text-sm text-gray-600">Số lượng: <?php echo $item['quantity']; ?></p>
                            </div>
                            <p class="font-bold text-gray-800"><?php echo number_format($item['amount'], 0, ',', '.'); ?>đ</p>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-600">Không có chi tiết sản phẩm</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-credit-card text-green-500 mr-2"></i>
                    Thông Tin Thanh Toán
                </h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Phương thức thanh toán:</span>
                        <span class="font-semibold text-gray-800">
                            <?php 
                            switch($order['phuong_thuc_thanh_toan']) {
                                case 'qr_code': echo '📱 Quét mã QR'; break;
                                case 'bank_transfer': echo '🏦 Chuyển khoản'; break;
                                case 'cash': echo '💵 Tiền mặt'; break;
                                default: echo $order['phuong_thuc_thanh_toan'];
                            }
                            ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($order['transaction_id'])): ?>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Mã giao dịch:</span>
                        <span class="font-mono text-sm text-gray-800"><?php echo htmlspecialchars($order['transaction_id']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($order['ma_hoa_don'])): ?>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Mã hóa đơn:</span>
                        <span class="font-mono text-sm text-gray-800"><?php echo htmlspecialchars($order['ma_hoa_don']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['paid_at']): ?>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Thời gian thanh toán:</span>
                        <span class="font-semibold text-green-600">
                            <?php echo date('d/m/Y H:i', strtotime($order['paid_at'])); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-between py-3 text-xl font-bold">
                        <span class="text-gray-800">Tổng cộng:</span>
                        <span class="text-pink-600"><?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <a href="my-orders.php" class="flex-1 bg-gray-200 text-gray-700 px-6 py-4 rounded-xl font-bold text-center hover:bg-gray-300 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại danh sách
                </a>
                
                <?php if ($order['trang_thai_thanh_toan'] === 'pending' && $order['minutes_ago'] < 10): ?>
                <a href="payment-qr.php?order_id=<?php echo $order['id']; ?>" 
                   class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl font-bold text-center hover:shadow-lg transition-all">
                    <i class="fas fa-qrcode mr-2"></i>
                    Tiếp tục thanh toán
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
