<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Lịch Hẹn';
$page_subtitle = 'Xem và quản lý lịch hẹn thử váy';

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE dat_lich_thu_vay SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật trạng thái thành công!';
    }
    
    if ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM dat_lich_thu_vay WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Xóa lịch hẹn thành công!';
    }
    
    header('Location: admin-bookings.php');
    exit();
}

// Lấy danh sách
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($status_filter) {
    $where .= " AND d.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($date_filter) {
    $where .= " AND d.scheduled_date = ?";
    $params[] = $date_filter;
    $types .= "s";
}
if ($search) {
    $where .= " AND (d.name LIKE ? OR d.phone LIKE ? OR d.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$count_sql = "SELECT COUNT(*) as total FROM dat_lich_thu_vay d WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT d.*, v.ten_vay, v.ma_vay 
        FROM dat_lich_thu_vay d 
        LEFT JOIN vay_cuoi v ON d.vay_id = v.id 
        WHERE $where ORDER BY d.scheduled_date DESC, d.scheduled_time DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Bộ lọc -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm tên, SĐT, email..." class="border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 focus:border-transparent text-base">
        <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>" 
            class="border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
        <select name="status" class="border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
            <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
            <option value="attended" <?php echo $status_filter === 'attended' ? 'selected' : ''; ?>>Đã đến</option>
            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
        </select>
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-4 py-2.5 hover:bg-accent-600 transition flex items-center justify-center">
            <i class="fas fa-search mr-2"></i>Lọc
        </button>
    </form>
</div>

<!-- Bảng lịch hẹn -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <!-- Mobile: Card view -->
    <div class="booking-mobile-view block lg:hidden">
        <?php foreach ($bookings as $booking): ?>
        <div class="booking-card p-4 border-b border-gray-100 last:border-b-0 <?php echo $booking['scheduled_date'] < date('Y-m-d') && $booking['status'] === 'pending' ? 'bg-red-50' : ''; ?>">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="font-semibold text-navy-900"><?php echo htmlspecialchars($booking['name']); ?></p>
                    <p class="text-sm text-navy-500"><i class="fas fa-phone mr-1 text-accent-500"></i><?php echo htmlspecialchars($booking['phone']); ?></p>
                </div>
                <div class="text-right">
                    <p class="font-bold <?php echo $booking['scheduled_date'] === date('Y-m-d') ? 'text-green-600' : 'text-navy-900'; ?>">
                        <?php echo date('d/m/Y', strtotime($booking['scheduled_date'])); ?>
                    </p>
                    <p class="text-sm text-navy-600"><?php echo $booking['scheduled_time'] ? date('H:i', strtotime($booking['scheduled_time'])) : '-'; ?></p>
                </div>
            </div>
            <?php if ($booking['ten_vay']): ?>
            <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                <p class="text-sm"><span class="font-medium text-accent-500"><?php echo htmlspecialchars($booking['ma_vay']); ?></span> - <?php echo htmlspecialchars($booking['ten_vay']); ?></p>
            </div>
            <?php endif; ?>
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="px-2 py-1 bg-navy-100 text-navy-700 rounded-full text-xs"><?php echo $booking['number_of_persons']; ?> người</span>
                <form method="POST" class="flex-1 max-w-[150px]">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs border rounded-lg px-2 py-1.5 font-medium
                        <?php echo match($booking['status']) {
                            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'attended' => 'bg-green-50 text-green-700 border-green-200',
                            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200'
                        }; ?>">
                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="attended" <?php echo $booking['status'] === 'attended' ? 'selected' : ''; ?>>Đã đến</option>
                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </form>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button onclick="showNote('<?php echo htmlspecialchars($booking['note'] ?? 'Không có ghi chú'); ?>')" class="p-2 text-accent-500 hover:bg-accent-50 rounded-lg" title="Ghi chú">
                    <i class="fas fa-sticky-note"></i>
                </button>
                <button onclick="deleteBooking(<?php echo $booking['id']; ?>)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($bookings)): ?>
        <div class="p-8 text-center text-navy-500">Không có lịch hẹn nào</div>
        <?php endif; ?>
    </div>
    
    <!-- Desktop: Table view -->
    <div class="booking-table-view hidden lg:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Khách hàng</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Váy muốn thử</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Ngày hẹn</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Giờ</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Số người</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach ($bookings as $booking): ?>
            <tr class="hover:bg-gray-50 transition <?php echo $booking['scheduled_date'] < date('Y-m-d') && $booking['status'] === 'pending' ? 'bg-red-50' : ''; ?>">
                <td class="px-6 py-4">
                    <div class="font-medium text-navy-900"><?php echo htmlspecialchars($booking['name']); ?></div>
                    <div class="text-sm text-navy-500">
                        <i class="fas fa-phone mr-1 text-accent-500"></i><?php echo htmlspecialchars($booking['phone']); ?>
                    </div>
                    <?php if ($booking['email']): ?>
                    <div class="text-sm text-navy-500">
                        <i class="fas fa-envelope mr-1 text-accent-500"></i><?php echo htmlspecialchars($booking['email']); ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                    <?php if ($booking['ten_vay']): ?>
                    <div class="text-sm font-medium text-accent-500"><?php echo htmlspecialchars($booking['ma_vay']); ?></div>
                    <div class="text-sm text-navy-500"><?php echo htmlspecialchars($booking['ten_vay']); ?></div>
                    <?php else: ?>
                    <span class="text-navy-400">Chưa chọn</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="<?php echo $booking['scheduled_date'] === date('Y-m-d') ? 'text-green-600 font-bold' : 'text-navy-900'; ?>">
                        <?php echo date('d/m/Y', strtotime($booking['scheduled_date'])); ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-navy-600">
                    <?php echo $booking['scheduled_time'] ? date('H:i', strtotime($booking['scheduled_time'])) : '-'; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="px-2 py-1 bg-navy-100 text-navy-700 rounded-full text-sm"><?php echo $booking['number_of_persons']; ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                        <select name="status" onchange="this.form.submit()" class="text-xs border-0 rounded-full px-3 py-1 font-medium
                            <?php echo match($booking['status']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed' => 'bg-blue-100 text-blue-700',
                                'attended' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            }; ?>">
                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="attended" <?php echo $booking['status'] === 'attended' ? 'selected' : ''; ?>>Đã đến</option>
                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </form>
                </td>
                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                    <button onclick="showNote('<?php echo htmlspecialchars($booking['note'] ?? 'Không có ghi chú'); ?>')" class="text-accent-500 hover:text-accent-600" title="Xem ghi chú">
                        <i class="fas fa-sticky-note"></i>
                    </button>
                    <button onclick="deleteBooking(<?php echo $booking['id']; ?>)" class="text-red-500 hover:text-red-600" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($bookings)): ?>
            <tr><td colspan="7" class="px-6 py-8 text-center text-navy-500">Không có lịch hẹn nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div><!-- End booking-table-view -->
</div><!-- End wrapper -->

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&date=<?php echo $date_filter; ?>&search=<?php echo urlencode($search); ?>" 
           class="px-4 py-2 rounded-lg <?php echo $i === $page ? 'bg-accent-500 text-white' : 'bg-white text-navy-700 hover:bg-gray-100'; ?> transition">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>

<form id="deleteForm" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
    function deleteBooking(id) {
        if (confirm('Bạn có chắc muốn xóa lịch hẹn này?')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
    
    function showNote(note) {
        alert('Ghi chú: ' + note);
    }
</script>

<?php include 'includes/admin-footer.php'; ?>
