<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra quyền admin (tạm thời bỏ qua - bạn có thể thêm sau)
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: admin-login.php');
//     exit;
// }

$page_title = 'Xác Nhận Thanh Toán';

// Lấy danh sách đơn hàng chờ thanh toán
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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Xác Nhận Thanh Toán
            </h1>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-blue-700">
                    <strong>Hướng dẫn:</strong> Kiểm tra tài khoản ngân hàng, nếu đã nhận được tiền thì click "Xác nhận" để hoàn tất đơn hàng.
                </p>
            </div>

            <?php if ($pending_orders && $pending_orders->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-3 text-left">Mã ĐH</th>
                                <th class="px-4 py-3 text-left">Khách hàng</th>
                                <th class="px-4 py-3 text-left">SĐT</th>
                                <th class="px-4 py-3 text-right">Số tiền</th>
                                <th class="px-4 py-3 text-center">Thời gian</th>
                                <th class="px-4 py-3 text-center">Trạng thái</th>
                                <th class="px-4 py-3 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $pending_orders->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-sm font-bold"><?php echo $order['ma_don_hang']; ?></span>
                                </td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($order['so_dien_thoai']); ?></td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold text-green-600">
                                        <?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">
                                    <?php echo $order['minutes_ago']; ?> phút trước
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                        Chờ thanh toán
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="confirmPayment(<?php echo $order['id']; ?>, '<?php echo $order['ma_don_hang']; ?>', <?php echo $order['tong_tien']; ?>)" 
                                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all">
                                        <i class="fas fa-check mr-1"></i>
                                        Xác nhận
                                    </button>
                                    <button onclick="viewDetails(<?php echo $order['id']; ?>)" 
                                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all ml-2">
                                        <i class="fas fa-eye mr-1"></i>
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 text-lg">Không có đơn hàng nào chờ xác nhận</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

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
    
    function viewDetails(orderId) {
        window.open('order-detail.php?id=' + orderId, '_blank');
    }
    
    // Auto refresh mỗi 30 giây
    setInterval(() => {
        location.reload();
    }, 30000);
    </script>
</body>
</html>
<?php $conn->close(); ?>
