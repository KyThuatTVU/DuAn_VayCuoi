<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Đơn Hàng';
$page_subtitle = 'Xem và quản lý tất cả đơn hàng';

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    
    // Lấy thông tin đơn hàng để gửi thông báo
    $order_info = $conn->query("SELECT nguoi_dung_id, ma_don_hang FROM don_hang WHERE id = $order_id")->fetch_assoc();
    
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE don_hang SET trang_thai = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        
        // Gửi thông báo cho người dùng
        if ($order_info && $order_info['nguoi_dung_id']) {
            notifyOrderUpdate($conn, $order_info['nguoi_dung_id'], $order_id, $order_info['ma_don_hang'], $status);
        }
        
        $_SESSION['admin_success'] = 'Cập nhật trạng thái đơn hàng thành công!';
    }
    
    if ($_POST['action'] === 'update_payment') {
        $payment_status = $_POST['payment_status'];
        $stmt = $conn->prepare("UPDATE don_hang SET trang_thai_thanh_toan = ? WHERE id = ?");
        $stmt->bind_param("si", $payment_status, $order_id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật trạng thái thanh toán thành công!';
    }
    
    header('Location: admin-orders.php');
    exit();
}

// Lọc và phân trang
$status_filter = $_GET['status'] ?? '';
$payment_filter = $_GET['payment'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($status_filter) {
    $where .= " AND trang_thai = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($payment_filter) {
    $where .= " AND trang_thai_thanh_toan = ?";
    $params[] = $payment_filter;
    $types .= "s";
}
if ($search) {
    $where .= " AND (ma_don_hang LIKE ? OR ho_ten LIKE ? OR so_dien_thoai LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Đếm tổng
$count_sql = "SELECT COUNT(*) as total FROM don_hang WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Lấy danh sách
$sql = "SELECT * FROM don_hang WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Bộ lọc -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm mã đơn, tên, SĐT..." class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
        <select name="status" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
            <option value="">-- Trạng thái đơn --</option>
            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
            <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
            <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
        </select>
        <select name="payment" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
            <option value="">-- Thanh toán --</option>
            <option value="pending" <?php echo $payment_filter === 'pending' ? 'selected' : ''; ?>>Chưa thanh toán</option>
            <option value="paid" <?php echo $payment_filter === 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
            <option value="failed" <?php echo $payment_filter === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
        </select>
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-4 py-2 hover:bg-accent-600 transition">
            <i class="fas fa-search mr-2"></i>Lọc
        </button>
    </form>
</div>

<!-- Bảng đơn hàng -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Mã đơn</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Khách hàng</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Tổng tiền</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Thanh toán</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Ngày tạo</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap font-medium text-accent-500">
                    <?php echo htmlspecialchars($order['ma_don_hang']); ?>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-navy-900"><?php echo htmlspecialchars($order['ho_ten']); ?></div>
                    <div class="text-sm text-navy-500"><?php echo htmlspecialchars($order['so_dien_thoai']); ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                    <?php echo number_format($order['tong_tien']); ?>đ
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status" onchange="this.form.submit()" class="text-xs border-0 rounded-full px-3 py-1 font-medium
                            <?php echo match($order['trang_thai']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            }; ?>">
                            <option value="pending" <?php echo $order['trang_thai'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                            <option value="processing" <?php echo $order['trang_thai'] === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="completed" <?php echo $order['trang_thai'] === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                            <option value="cancelled" <?php echo $order['trang_thai'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </form>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="update_payment">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="payment_status" onchange="this.form.submit()" class="text-xs border-0 rounded-full px-3 py-1 font-medium
                            <?php echo match($order['trang_thai_thanh_toan']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'paid' => 'bg-green-100 text-green-700',
                                'failed' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            }; ?>">
                            <option value="pending" <?php echo $order['trang_thai_thanh_toan'] === 'pending' ? 'selected' : ''; ?>>Chưa TT</option>
                            <option value="paid" <?php echo $order['trang_thai_thanh_toan'] === 'paid' ? 'selected' : ''; ?>>Đã TT</option>
                            <option value="failed" <?php echo $order['trang_thai_thanh_toan'] === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
                        </select>
                    </form>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-navy-500">
                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="admin-order-detail.php?id=<?php echo $order['id']; ?>" class="text-accent-500 hover:text-accent-600 font-medium">
                        <i class="fas fa-eye mr-1"></i> Chi tiết
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <tr><td colspan="7" class="px-6 py-8 text-center text-navy-500">Không có đơn hàng nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&payment=<?php echo $payment_filter; ?>&search=<?php echo urlencode($search); ?>" 
           class="px-4 py-2 rounded-lg <?php echo $i === $page ? 'bg-accent-500 text-white' : 'bg-white text-navy-700 hover:bg-gray-100'; ?> transition">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
