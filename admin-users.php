<?php
session_start();
require_once 'includes/config.php';

$page_title = 'Quản Lý Khách Hàng';
$page_subtitle = 'Xem và quản lý thông tin khách hàng';

// Xử lý xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Xóa người dùng thành công!';
        header('Location: admin-users.php');
        exit();
    }
}

// Lấy danh sách
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (ho_ten LIKE ? OR email LIKE ? OR so_dien_thoai LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$count_sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT n.*, (SELECT COUNT(*) FROM don_hang WHERE nguoi_dung_id = n.id) as order_count,
        (SELECT SUM(tong_tien) FROM don_hang WHERE nguoi_dung_id = n.id AND trang_thai_thanh_toan = 'paid') as total_spent
        FROM nguoi_dung n WHERE $where ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Tìm kiếm -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm tên, email, SĐT..." class="flex-1 border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-6 py-2 hover:bg-accent-600 transition">
            <i class="fas fa-search mr-2"></i>Tìm
        </button>
    </form>
</div>

<!-- Bảng người dùng -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Khách hàng</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">SĐT</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Đơn hàng</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Tổng chi</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Ngày ĐK</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach ($users as $user): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-navy-100 overflow-hidden flex items-center justify-center">
                            <?php if (!empty($user['avt'])): ?>
                                <img src="<?php echo htmlspecialchars($user['avt']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user text-navy-400"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="font-medium text-navy-900"><?php echo htmlspecialchars($user['ho_ten']); ?></p>
                            <p class="text-xs text-navy-500">#<?php echo $user['id']; ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-navy-600"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="px-6 py-4 text-sm text-navy-600"><?php echo htmlspecialchars($user['so_dien_thoai'] ?? '-'); ?></td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium"><?php echo $user['order_count']; ?></span>
                </td>
                <td class="px-6 py-4 font-bold text-green-600">
                    <?php echo number_format($user['total_spent'] ?? 0); ?>đ
                </td>
                <td class="px-6 py-4 text-sm text-navy-500">
                    <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                </td>
                <td class="px-6 py-4 space-x-3">
                    <a href="admin-user-detail.php?id=<?php echo $user['id']; ?>" class="text-accent-500 hover:text-accent-600">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['ho_ten']); ?>')" class="text-red-500 hover:text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
            <tr><td colspan="7" class="px-6 py-8 text-center text-navy-500">Không có khách hàng nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
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
    function deleteUser(id, name) {
        if (confirm('Bạn có chắc muốn xóa người dùng "' + name + '"?\nTất cả đơn hàng liên quan sẽ bị ảnh hưởng!')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<?php include 'includes/admin-footer.php'; ?>
