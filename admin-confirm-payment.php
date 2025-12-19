<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$page_title = 'Xác Nhận Thanh Toán';
$page_subtitle = 'Xác nhận các giao dịch chuyển khoản';

// Kiểm tra bảng thanh_toan có tồn tại không
$check_table = $conn->query("SHOW TABLES LIKE 'thanh_toan'");
$has_thanh_toan = $check_table && $check_table->num_rows > 0;

// Lấy danh sách đơn hàng chờ thanh toán
if ($has_thanh_toan) {
    $pending_orders = $conn->query("SELECT 
        dh.id,
        dh.ma_don_hang,
        dh.ho_ten,
        dh.so_dien_thoai,
        dh.tong_tien,
        dh.trang_thai_thanh_toan,
        dh.created_at,
        t.transaction_id,
        t.amount,
        TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) as minutes_ago
    FROM don_hang dh
    LEFT JOIN thanh_toan t ON dh.id = t.don_hang_id
    WHERE dh.trang_thai_thanh_toan = 'pending'
    ORDER BY dh.created_at DESC");
} else {
    $pending_orders = $conn->query("SELECT 
        id,
        ma_don_hang,
        ho_ten,
        so_dien_thoai,
        tong_tien,
        trang_thai_thanh_toan,
        created_at,
        NULL as transaction_id,
        NULL as amount,
        TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_ago
    FROM don_hang
    WHERE trang_thai_thanh_toan = 'pending'
    ORDER BY created_at DESC");
}

include 'includes/admin-layout.php';
?>

<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
    <p class="text-blue-700">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Hướng dẫn:</strong> Kiểm tra tài khoản ngân hàng, nếu đã nhận được tiền thì click "Xác nhận" để hoàn tất đơn hàng.
    </p>
</div>

<?php if ($pending_orders && is_object($pending_orders) && $pending_orders->num_rows > 0): ?>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-navy-50">
                        <th class="px-4 py-3 text-left text-navy-700 font-semibold">Mã ĐH</th>
                        <th class="px-4 py-3 text-left text-navy-700 font-semibold">Khách hàng</th>
                        <th class="px-4 py-3 text-left text-navy-700 font-semibold">SĐT</th>
                        <th class="px-4 py-3 text-right text-navy-700 font-semibold">Số tiền</th>
                        <th class="px-4 py-3 text-center text-navy-700 font-semibold">Thời gian</th>
                        <th class="px-4 py-3 text-center text-navy-700 font-semibold">Trạng thái</th>
                        <th class="px-4 py-3 text-center text-navy-700 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $pending_orders->fetch_assoc()): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-bold text-navy-900"><?php echo $order['ma_don_hang']; ?></span>
                        </td>
                        <td class="px-4 py-3 text-navy-700"><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                        <td class="px-4 py-3 text-navy-600"><?php echo htmlspecialchars($order['so_dien_thoai']); ?></td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold text-green-600">
                                <?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-navy-500">
                            <?php echo $order['minutes_ago']; ?> phút trước
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                Chờ thanh toán
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="confirmPayment(<?php echo $order['id']; ?>, '<?php echo $order['ma_don_hang']; ?>', <?php echo $order['tong_tien']; ?>)" 
                                        class="bg-green-500 text-white px-3 py-1.5 rounded-lg hover:bg-green-600 transition-all text-sm">
                                    <i class="fas fa-check mr-1"></i>Xác nhận
                                </button>
                                <a href="admin-order-detail.php?id=<?php echo $order['id']; ?>" 
                                   class="bg-navy-500 text-white px-3 py-1.5 rounded-lg hover:bg-navy-600 transition-all text-sm">
                                    <i class="fas fa-eye mr-1"></i>Chi tiết
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <i class="fas fa-check-circle text-6xl text-gray-300 mb-4"></i>
        <p class="text-navy-600 text-lg">Không có đơn hàng nào chờ xác nhận thanh toán</p>
    </div>
<?php endif; ?>

<script>
function confirmPayment(orderId, orderCode, amount) {
    if (!confirm(`Xác nhận đã nhận được ${amount.toLocaleString('vi-VN')}đ cho đơn hàng ${orderCode}?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('amount', amount);
    formData.append('transaction_id', 'MANUAL_' + Date.now());
    
    fetch('api/confirm-payment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Đã xác nhận thanh toán thành công!');
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra: ' + error.message);
    });
}

// Auto refresh mỗi 60 giây
setInterval(() => {
    location.reload();
}, 60000);
</script>

<?php include 'includes/admin-footer.php'; ?>
