<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Đặt Hàng Thành Công';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin đơn hàng
$order_query = $conn->prepare("SELECT 
    dh.*,
    hd.ma_hoa_don,
    hd.tong_thanh_toan,
    t.transaction_id,
    t.paid_at
FROM don_hang dh
LEFT JOIN hoa_don hd ON dh.id = hd.don_hang_id
LEFT JOIN thanh_toan t ON dh.id = t.don_hang_id
WHERE dh.id = ? AND dh.nguoi_dung_id = ?");

$order_query->bind_param("ii", $order_id, $_SESSION['user_id']);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Lấy chi tiết đơn hàng
$details_query = $conn->prepare("SELECT * FROM chi_tiet_hoa_don WHERE hoa_don_id = (SELECT id FROM hoa_don WHERE don_hang_id = ?)");
$details_query->bind_param("i", $order_id);
$details_query->execute();
$details = $details_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Xóa thông tin order khỏi session
unset($_SESSION['order_info']);

require_once 'includes/header.php';
?>

<section class="py-16 bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Success Message -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 mb-8 text-center">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-green-500 text-6xl"></i>
                </div>
                
                <h1 class="text-4xl font-bold text-gray-800 mb-4">🎉 Đặt Hàng Thành Công!</h1>
                <p class="text-gray-600 text-lg mb-6">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi</p>
                
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6 mb-6">
                    <p class="text-sm text-gray-600 mb-2">Mã đơn hàng</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $order['ma_hoa_don']; ?></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-600 mb-1">Tổng tiền</p>
                        <p class="text-2xl font-bold text-pink-600"><?php echo number_format($order['tong_thanh_toan'], 0, ',', '.'); ?>đ</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-600 mb-1">Thời gian</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo date('H:i d/m/Y', strtotime($order['paid_at'] ?? $order['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">📋 Chi Tiết Đơn Hàng</h2>
                
                <div class="space-y-4">
                    <?php foreach ($details as $item): ?>
                    <div class="flex justify-between items-center py-3 border-b">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="text-sm text-gray-600">Số lượng: <?php echo $item['quantity']; ?></p>
                        </div>
                        <p class="font-bold text-gray-800"><?php echo number_format($item['amount'], 0, ',', '.'); ?>đ</p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">📱 Bước Tiếp Theo</h3>
                
                <ol class="space-y-3 text-gray-700">
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</span>
                        <div>
                            <p class="font-semibold">Chúng tôi sẽ liên hệ xác nhận</p>
                            <p class="text-sm text-gray-600">Trong vòng 24h để xác nhận thông tin và lịch nhận váy</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">2</span>
                        <div>
                            <p class="font-semibold">Chuẩn bị váy cưới</p>
                            <p class="text-sm text-gray-600">Chúng tôi sẽ chuẩn bị và kiểm tra váy trước khi giao</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">3</span>
                        <div>
                            <p class="font-semibold">Nhận váy và thanh toán phần còn lại</p>
                            <p class="text-sm text-gray-600">Thanh toán 70% còn lại khi nhận váy</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">4</span>
                        <div>
                            <p class="font-semibold">Trả váy và hoàn cọc</p>
                            <p class="text-sm text-gray-600">Hoàn cọc sau khi trả váy nguyên vẹn</p>
                        </div>
                    </li>
                </ol>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-2 gap-4">
                <a href="index.php" class="bg-gray-200 text-gray-700 px-6 py-4 rounded-xl font-bold text-center hover:bg-gray-300 transition-all">
                    <i class="fas fa-home mr-2"></i>
                    Về Trang Chủ
                </a>
                <a href="products.php" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-4 rounded-xl font-bold text-center hover:shadow-lg transition-all">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Tiếp Tục Mua Sắm
                </a>
            </div>

            <!-- Contact -->
            <div class="mt-8 text-center text-gray-600">
                <p class="mb-2">Cần hỗ trợ? Liên hệ với chúng tôi:</p>
                <p class="font-bold text-gray-800">
                    <i class="fas fa-phone mr-2"></i>0901 234 567
                    <span class="mx-3">|</span>
                    <i class="fas fa-envelope mr-2"></i>contact@vaycuoi.com
                </p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
