<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$user_id = intval($_GET['id'] ?? 0);
if (!$user_id) {
    header('Location: admin-users.php');
    exit();
}

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE nguoi_dung SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $user_id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật trạng thái thành công!';
    }
    
    header('Location: admin-user-detail.php?id=' . $user_id);
    exit();
}

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT *, COALESCE(status, 'active') as status FROM nguoi_dung WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: admin-users.php');
    exit();
}

// Lấy thống kê đơn hàng
$stats = $conn->query("SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN trang_thai = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN trang_thai = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN trang_thai = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
    SUM(CASE WHEN trang_thai_thanh_toan = 'paid' THEN tong_tien ELSE 0 END) as total_spent
    FROM don_hang WHERE nguoi_dung_id = $user_id")->fetch_assoc();

// Lấy danh sách đơn hàng gần đây
$orders = $conn->query("SELECT * FROM don_hang WHERE nguoi_dung_id = $user_id ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

// Lấy lịch hẹn thử váy
$bookings = $conn->query("SELECT d.*, v.ten_vay FROM dat_lich_thu_vay d LEFT JOIN vay_cuoi v ON d.vay_id = v.id WHERE d.user_id = $user_id ORDER BY d.scheduled_date DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Chi Tiết Khách Hàng';
$page_subtitle = $user['ho_ten'];

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Back button -->
<div class="mb-6">
    <a href="admin-users.php" class="inline-flex items-center text-navy-500 hover:text-navy-700">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách khách hàng
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Thông tin cá nhân -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 mx-auto rounded-full bg-navy-100 overflow-hidden flex items-center justify-center mb-4">
                    <?php if (!empty($user['avt'])): ?>
                        <img src="<?php echo htmlspecialchars($user['avt']); ?>" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                    <?php else: ?>
                        <i class="fas fa-user text-4xl text-navy-400"></i>
                    <?php endif; ?>
                </div>
                <h2 class="text-xl font-bold text-navy-900"><?php echo htmlspecialchars($user['ho_ten']); ?></h2>
                <p class="text-navy-500">#<?php echo $user['id']; ?></p>
                
                <!-- Trạng thái -->
                <form method="POST" class="mt-4">
                    <input type="hidden" name="action" value="update_status">
                    <select name="status" onchange="this.form.submit()" class="w-full text-center border-2 rounded-lg px-4 py-2 font-medium
                        <?php echo match($user['status']) {
                            'active' => 'border-green-300 bg-green-50 text-green-700',
                            'locked' => 'border-red-300 bg-red-50 text-red-700',
                            'disabled' => 'border-gray-300 bg-gray-50 text-gray-700',
                            default => 'border-green-300 bg-green-50 text-green-700'
                        }; ?>">
                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>✓ Hoạt động</option>
                        <option value="locked" <?php echo $user['status'] === 'locked' ? 'selected' : ''; ?>>🔒 Đã khóa</option>
                        <option value="disabled" <?php echo $user['status'] === 'disabled' ? 'selected' : ''; ?>>⛔ Vô hiệu hóa</option>
                    </select>
                </form>
            </div>
            
            <div class="space-y-4 border-t pt-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-envelope text-accent-500 w-5"></i>
                    <div>
                        <p class="text-xs text-navy-500">Email</p>
                        <p class="font-medium text-navy-900"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-phone text-accent-500 w-5"></i>
                    <div>
                        <p class="text-xs text-navy-500">Số điện thoại</p>
                        <p class="font-medium text-navy-900"><?php echo htmlspecialchars($user['so_dien_thoai'] ?? 'Chưa cập nhật'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-accent-500 w-5"></i>
                    <div>
                        <p class="text-xs text-navy-500">Địa chỉ</p>
                        <p class="font-medium text-navy-900"><?php echo htmlspecialchars($user['dia_chi'] ?? 'Chưa cập nhật'); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar text-accent-500 w-5"></i>
                    <div>
                        <p class="text-xs text-navy-500">Ngày đăng ký</p>
                        <p class="font-medium text-navy-900"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thống kê và đơn hàng -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Thống kê -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-accent-500"><?php echo $stats['total_orders'] ?? 0; ?></div>
                <div class="text-sm text-navy-500">Tổng đơn hàng</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-green-500"><?php echo $stats['completed_orders'] ?? 0; ?></div>
                <div class="text-sm text-navy-500">Hoàn thành</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-yellow-500"><?php echo $stats['pending_orders'] ?? 0; ?></div>
                <div class="text-sm text-navy-500">Đang xử lý</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-blue-500"><?php echo number_format($stats['total_spent'] ?? 0); ?>đ</div>
                <div class="text-sm text-navy-500">Tổng chi tiêu</div>
            </div>
        </div>
        
        <!-- Đơn hàng gần đây -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-navy-900 mb-4">
                <i class="fas fa-shopping-bag mr-2 text-accent-500"></i>Đơn hàng gần đây
            </h3>
            <?php if (!empty($orders)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Mã đơn</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Ngày</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Tổng tiền</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Thanh toán</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="admin-order-detail.php?id=<?php echo $order['id']; ?>" class="text-accent-500 hover:underline font-medium">
                                    <?php echo htmlspecialchars($order['ma_don_hang']); ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-navy-600"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                            <td class="px-4 py-3 font-bold text-green-600"><?php echo number_format($order['tong_tien']); ?>đ</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo match($order['trang_thai']) {
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
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo match($order['trang_thai_thanh_toan']) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                }; ?>">
                                    <?php echo match($order['trang_thai_thanh_toan']) {
                                        'pending' => 'Chưa TT',
                                        'paid' => 'Đã TT',
                                        'failed' => 'Thất bại',
                                        default => $order['trang_thai_thanh_toan']
                                    }; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-navy-500">
                <i class="fas fa-shopping-cart text-4xl mb-4 text-navy-300"></i>
                <p>Chưa có đơn hàng nào</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Lịch hẹn thử váy -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-navy-900 mb-4">
                <i class="fas fa-calendar-check mr-2 text-accent-500"></i>Lịch hẹn thử váy
            </h3>
            <?php if (!empty($bookings)): ?>
            <div class="space-y-3">
                <?php foreach ($bookings as $booking): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-navy-900"><?php echo htmlspecialchars($booking['ten_vay'] ?? 'Chưa chọn váy'); ?></p>
                        <p class="text-sm text-navy-500">
                            <i class="fas fa-calendar mr-1"></i><?php echo date('d/m/Y', strtotime($booking['scheduled_date'])); ?>
                            <?php if ($booking['scheduled_time']): ?>
                            <i class="fas fa-clock ml-2 mr-1"></i><?php echo date('H:i', strtotime($booking['scheduled_time'])); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo match($booking['status']) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-blue-100 text-blue-700',
                        'attended' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    }; ?>">
                        <?php echo match($booking['status']) {
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'attended' => 'Đã đến',
                            'cancelled' => 'Đã hủy',
                            default => $booking['status']
                        }; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-navy-500">
                <i class="fas fa-calendar-times text-4xl mb-4 text-navy-300"></i>
                <p>Chưa có lịch hẹn nào</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
