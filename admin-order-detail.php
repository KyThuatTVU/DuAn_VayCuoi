<?php
session_start();
require_once 'includes/config.php';

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    header('Location: admin-orders.php');
    exit();
}

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT d.*, n.email as user_email FROM don_hang d LEFT JOIN nguoi_dung n ON d.nguoi_dung_id = n.id WHERE d.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: admin-orders.php');
    exit();
}

// Lấy chi tiết đơn hàng từ hóa đơn
$stmt = $conn->prepare("SELECT c.*, v.ten_vay, v.ma_vay FROM chi_tiet_hoa_don c 
    JOIN hoa_don h ON c.hoa_don_id = h.id 
    LEFT JOIN vay_cuoi v ON c.vay_id = v.id 
    WHERE h.don_hang_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = 'Chi Tiết Đơn Hàng';
$page_subtitle = 'Mã đơn: ' . $order['ma_don_hang'];

include 'includes/admin-layout.php';
?>

<!-- Back button -->
<div class="mb-6">
    <a href="admin-orders.php" class="inline-flex items-center text-navy-500 hover:text-navy-700">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách đơn hàng
    </a>
</div>

<!-- Thông tin đơn hàng -->
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-bold text-accent-500"><?php echo htmlspecialchars($order['ma_don_hang']); ?></h2>
            <p class="text-sm text-navy-500 mt-1">
                <i class="fas fa-clock mr-1"></i>Ngày tạo: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
            </p>
        </div>
        <div class="text-right space-y-2">
            <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo match($order['trang_thai']) {
                'pending' => 'bg-yellow-100 text-yellow-700',
                'processing' => 'bg-blue-100 text-blue-700',
                'completed' => 'bg-green-100 text-green-700',
                'cancelled' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700'
            }; ?>">
                <?php echo match($order['trang_thai']) {
                    'pending' => 'Chờ xử lý',
                    'processing' => 'Đang xử lý',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                    default => $order['trang_thai']
                }; ?>
            </span>
            <br>
            <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo match($order['trang_thai_thanh_toan']) {
                'pending' => 'bg-yellow-100 text-yellow-700',
                'paid' => 'bg-green-100 text-green-700',
                'failed' => 'bg-red-100 text-red-700',
                default => 'bg-gray-100 text-gray-700'
            }; ?>">
                <?php echo match($order['trang_thai_thanh_toan']) {
                    'pending' => 'Chưa thanh toán',
                    'paid' => 'Đã thanh toán',
                    'failed' => 'Thanh toán thất bại',
                    default => $order['trang_thai_thanh_toan']
                }; ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold text-navy-700 mb-3">
                <i class="fas fa-user mr-2 text-accent-500"></i>Thông tin khách hàng
            </h3>
            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                <p class="flex justify-between">
                    <span class="text-navy-500">Họ tên:</span>
                    <span class="font-medium text-navy-900"><?php echo htmlspecialchars($order['ho_ten']); ?></span>
                </p>
                <p class="flex justify-between">
                    <span class="text-navy-500">SĐT:</span>
                    <span class="font-medium text-navy-900"><?php echo htmlspecialchars($order['so_dien_thoai']); ?></span>
                </p>
                <?php if ($order['user_email']): ?>
                <p class="flex justify-between">
                    <span class="text-navy-500">Email:</span>
                    <span class="font-medium text-navy-900"><?php echo htmlspecialchars($order['user_email']); ?></span>
                </p>
                <?php endif; ?>
                <p class="flex justify-between">
                    <span class="text-navy-500">Địa chỉ:</span>
                    <span class="font-medium text-navy-900 text-right max-w-xs"><?php echo htmlspecialchars($order['dia_chi']); ?></span>
                </p>
            </div>
        </div>
        <div>
            <h3 class="font-semibold text-navy-700 mb-3">
                <i class="fas fa-credit-card mr-2 text-accent-500"></i>Thanh toán
            </h3>
            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                <p class="flex justify-between">
                    <span class="text-navy-500">Phương thức:</span>
                    <span class="font-medium text-navy-900"><?php echo htmlspecialchars($order['phuong_thuc_thanh_toan'] ?? 'QR Code'); ?></span>
                </p>
                <p class="flex justify-between items-center">
                    <span class="text-navy-500">Tổng tiền:</span>
                    <span class="text-2xl font-bold text-green-600"><?php echo number_format($order['tong_tien']); ?>đ</span>
                </p>
            </div>
        </div>
    </div>

    <?php if ($order['ghi_chu']): ?>
    <div class="mt-6">
        <h3 class="font-semibold text-navy-700 mb-3">
            <i class="fas fa-sticky-note mr-2 text-accent-500"></i>Ghi chú
        </h3>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
            <p class="text-navy-700"><?php echo nl2br(htmlspecialchars($order['ghi_chu'])); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Chi tiết sản phẩm -->
<div class="bg-white rounded-2xl shadow-sm p-6">
    <h3 class="font-semibold text-navy-700 mb-4">
        <i class="fas fa-tshirt mr-2 text-accent-500"></i>Sản phẩm trong đơn
    </h3>
    <?php if (!empty($items)): ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Sản phẩm</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-navy-600 uppercase">SL</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-navy-600 uppercase">Đơn giá</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-navy-600 uppercase">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4">
                        <div class="font-medium text-navy-900"><?php echo htmlspecialchars($item['ten_vay'] ?? $item['description']); ?></div>
                        <?php if ($item['ma_vay']): ?>
                        <div class="text-sm text-accent-500"><?php echo htmlspecialchars($item['ma_vay']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="px-3 py-1 bg-navy-100 text-navy-700 rounded-full"><?php echo $item['quantity']; ?></span>
                    </td>
                    <td class="px-4 py-4 text-right text-navy-600"><?php echo number_format($item['amount']); ?>đ</td>
                    <td class="px-4 py-4 text-right font-bold text-navy-900"><?php echo number_format($item['amount'] * $item['quantity']); ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-50">
                    <td colspan="3" class="px-4 py-4 text-right font-semibold text-navy-700">Tổng cộng:</td>
                    <td class="px-4 py-4 text-right text-2xl font-bold text-green-600"><?php echo number_format($order['tong_tien']); ?>đ</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="text-center py-8 text-navy-500">
        <i class="fas fa-box-open text-4xl mb-4 text-navy-300"></i>
        <p>Không có chi tiết sản phẩm</p>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin-footer.php'; ?>
