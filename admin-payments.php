<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Thanh Toán';
$page_subtitle = 'Xem và quản lý tất cả giao dịch thanh toán';

// Xử lý cập nhật trạng thái thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_payment_status') {
        $payment_id = intval($_POST['payment_id']);
        $new_status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE thanh_toan SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $payment_id);
        
        if ($stmt->execute()) {
            // Nếu thanh toán thành công, cập nhật đơn hàng
            if ($new_status === 'success') {
                $stmt2 = $conn->prepare("UPDATE don_hang dh 
                    INNER JOIN thanh_toan tt ON dh.id = tt.don_hang_id 
                    SET dh.trang_thai_thanh_toan = 'paid', dh.trang_thai = 'processing', dh.updated_at = NOW() 
                    WHERE tt.id = ?");
                $stmt2->bind_param("i", $payment_id);
                $stmt2->execute();
            }
            $_SESSION['admin_success'] = 'Cập nhật trạng thái thanh toán thành công!';
        } else {
            $_SESSION['admin_error'] = 'Lỗi cập nhật trạng thái!';
        }
        
        header('Location: admin-payments.php');
        exit();
    }
}

// Lọc và phân trang
$status_filter = $_GET['status'] ?? '';
$gateway_filter = $_GET['gateway'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Xây dựng query
$where = "1=1";
$params = [];
$types = "";

if ($status_filter) {
    $where .= " AND tt.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($gateway_filter) {
    $where .= " AND tt.payment_gateway = ?";
    $params[] = $gateway_filter;
    $types .= "s";
}

if ($search) {
    $where .= " AND (tt.transaction_id LIKE ? OR dh.ma_don_hang LIKE ? OR dh.ho_ten LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Đếm tổng số
$count_sql = "SELECT COUNT(*) as total FROM thanh_toan tt 
              LEFT JOIN don_hang dh ON tt.don_hang_id = dh.id 
              WHERE $where";
$count_stmt = $conn->prepare($count_sql);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Lấy dữ liệu
$sql = "SELECT 
    tt.*,
    dh.ma_don_hang,
    dh.ho_ten,
    dh.so_dien_thoai,
    dh.tong_tien as order_amount,
    dh.trang_thai as order_status,
    dh.trang_thai_thanh_toan as payment_status,
    hd.ma_hoa_don
FROM thanh_toan tt
LEFT JOIN don_hang dh ON tt.don_hang_id = dh.id
LEFT JOIN hoa_don hd ON tt.hoa_don_id = hd.id
WHERE $where
ORDER BY tt.created_at DESC
LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Thống kê
$stats = $conn->query("SELECT 
    COUNT(*) as total_payments,
    SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as total_success_amount,
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as total_success,
    SUM(CASE WHEN status = 'initiated' THEN 1 ELSE 0 END) as total_pending,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as total_failed
FROM thanh_toan")->fetch_assoc();

// Lấy thống kê thanh toán cho sidebar
$payment_stats = $stats;

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
    </div>
<?php endif; ?>

<!-- Thống kê Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Tổng giao dịch -->
    <div class="card bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Tổng giao dịch</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_payments']); ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-receipt text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Thành công -->
    <div class="card bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Thành công</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_success']); ?></p>
                <p class="text-xs text-green-600 mt-1"><?php echo number_format($stats['total_success_amount']/1000000, 1); ?>M VNĐ</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Đang xử lý -->
    <div class="card bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Đang xử lý</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_pending']); ?></p>
                <p class="text-xs text-yellow-600 mt-1">Chờ xác nhận</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Thất bại -->
    <div class="card bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Thất bại</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_failed']); ?></p>
                <p class="text-xs text-red-600 mt-1">Cần kiểm tra</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <select name="status" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
            <option value="">Tất cả trạng thái</option>
            <option value="initiated" <?php echo $status_filter === 'initiated' ? 'selected' : ''; ?>>Đang xử lý</option>
            <option value="success" <?php echo $status_filter === 'success' ? 'selected' : ''; ?>>Thành công</option>
            <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
            <option value="refunded" <?php echo $status_filter === 'refunded' ? 'selected' : ''; ?>>Hoàn tiền</option>
        </select>
        
        <select name="gateway" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
            <option value="">Tất cả cổng</option>
            <option value="momo" <?php echo $gateway_filter === 'momo' ? 'selected' : ''; ?>>MoMo</option>
            <option value="qr_code" <?php echo $gateway_filter === 'qr_code' ? 'selected' : ''; ?>>QR Code</option>
        </select>
        
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
               placeholder="Tìm mã GD, đơn hàng, tên KH..." 
               class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
        
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition">
                <i class="fas fa-search mr-2"></i>Lọc
            </button>
            <?php if($status_filter || $gateway_filter || $search): ?>
            <a href="admin-payments.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-times"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Bảng thanh toán -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-navy-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Mã GD</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Đơn hàng</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Khách hàng</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Cổng TT</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Số tiền</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Trạng thái</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Ngày tạo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-navy-700 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            Không có giao dịch nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                    <tr class="hover:bg-navy-50 transition">
                        <td class="px-4 py-3 text-sm font-semibold">#<?php echo $payment['id']; ?></td>
                        <td class="px-4 py-3 text-sm">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                <?php echo htmlspecialchars(substr($payment['transaction_id'], 0, 15)); ?>...
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="admin-order-detail.php?id=<?php echo $payment['don_hang_id']; ?>" 
                               class="text-accent-500 hover:text-accent-600 font-medium">
                                <?php echo htmlspecialchars($payment['ma_don_hang']); ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium"><?php echo htmlspecialchars($payment['ho_ten']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($payment['so_dien_thoai']); ?></div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if ($payment['payment_gateway'] === 'momo'): ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700">
                                    MoMo
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    <?php echo htmlspecialchars($payment['payment_gateway']); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-green-600">
                            <?php echo number_format($payment['amount']); ?> đ
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_payment_status">
                                <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                <select name="status" onchange="if(confirm('Xác nhận thay đổi?')) this.form.submit();" 
                                        class="text-xs border-0 rounded-full px-3 py-1 font-medium cursor-pointer
                                        <?php echo match($payment['status']) {
                                            'initiated' => 'bg-yellow-100 text-yellow-700',
                                            'success' => 'bg-green-100 text-green-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                            'refunded' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        }; ?>">
                                    <option value="initiated" <?php echo $payment['status'] === 'initiated' ? 'selected' : ''; ?>>Đang xử lý</option>
                                    <option value="success" <?php echo $payment['status'] === 'success' ? 'selected' : ''; ?>>Thành công</option>
                                    <option value="failed" <?php echo $payment['status'] === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
                                    <option value="refunded" <?php echo $payment['status'] === 'refunded' ? 'selected' : ''; ?>>Hoàn tiền</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?php echo date('d/m/Y H:i', strtotime($payment['created_at'])); ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="viewPaymentDetail(<?php echo $payment['id']; ?>)" 
                                    class="text-accent-500 hover:text-accent-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages > 1): ?>
    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Hiển thị <span class="font-medium"><?php echo $offset + 1; ?></span> 
                đến <span class="font-medium"><?php echo min($offset + $per_page, $total); ?></span> 
                trong tổng số <span class="font-medium"><?php echo $total; ?></span> giao dịch
            </div>
            <div class="flex gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&gateway=<?php echo $gateway_filter; ?>&search=<?php echo urlencode($search); ?>" 
                       class="px-3 py-1 rounded <?php echo $i === $page ? 'bg-accent-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal chi tiết thanh toán -->
<div id="paymentDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 w-11/12 md:w-3/4 lg:w-1/2 shadow-2xl rounded-2xl bg-white">
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <h3 class="text-xl font-bold text-navy-900">Chi tiết giao dịch</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-600 p-2 rounded-lg transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentDetailContent" class="text-sm">
            <!-- Nội dung sẽ được load bằng JavaScript -->
        </div>
    </div>
</div>

<script>
function viewPaymentDetail(paymentId) {
    document.getElementById('paymentDetailModal').classList.remove('hidden');
    document.getElementById('paymentDetailContent').innerHTML = '<p class="text-center py-4">Đang tải...</p>';
    
    fetch('api/get-payment-detail.php?id=' + paymentId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const payment = data.payment;
                let html = `
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">ID Giao dịch</div>
                                <div class="font-semibold">#${payment.id}</div>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">Trạng thái</div>
                                <div>${getStatusBadge(payment.status)}</div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Mã giao dịch</div>
                            <div class="font-mono text-xs">${payment.transaction_id || 'N/A'}</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Đơn hàng</div>
                            <a href="admin-order-detail.php?id=${payment.don_hang_id}" class="text-accent-500 font-semibold">${payment.ma_don_hang}</a>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Khách hàng</div>
                            <div class="font-semibold">${payment.ho_ten}</div>
                            <div class="text-xs text-gray-600 mt-1">${payment.so_dien_thoai || 'N/A'}</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Cổng thanh toán</div>
                            <div class="font-semibold">${payment.payment_gateway}</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                            <div class="text-xs text-green-600 mb-1">Số tiền</div>
                            <div class="font-bold text-green-600 text-xl">${new Intl.NumberFormat('vi-VN').format(payment.amount)} VNĐ</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">Ngày tạo</div>
                                <div class="text-sm">${payment.created_at}</div>
                            </div>
                            ${payment.paid_at ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">Ngày thanh toán</div>
                                <div class="text-sm">${payment.paid_at}</div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                document.getElementById('paymentDetailContent').innerHTML = html;
            } else {
                document.getElementById('paymentDetailContent').innerHTML = '<p class="text-red-600">Lỗi tải dữ liệu</p>';
            }
        })
        .catch(error => {
            document.getElementById('paymentDetailContent').innerHTML = '<p class="text-red-600">Lỗi: ' + error.message + '</p>';
        });
}

function closeModal() {
    document.getElementById('paymentDetailModal').classList.add('hidden');
}

function getStatusBadge(status) {
    const badges = {
        'initiated': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Đang xử lý</span>',
        'success': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Thành công</span>',
        'failed': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Thất bại</span>',
        'refunded': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Hoàn tiền</span>'
    };
    return badges[status] || status;
}

window.onclick = function(event) {
    const modal = document.getElementById('paymentDetailModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<?php require_once 'includes/admin-footer.php'; ?>
