<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Khuyến Mãi';
$page_subtitle = 'Thêm, sửa, xóa mã khuyến mãi';

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $code = strtoupper(trim($_POST['code']));
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $type = $_POST['type'];
        $value = floatval($_POST['value']);
        $min_order_amount = floatval($_POST['min_order_amount']);
        $start_at = $_POST['start_at'];
        $end_at = $_POST['end_at'];
        $usage_limit = intval($_POST['usage_limit']) ?: null;

        // Kiểm tra mã đã tồn tại
        $check_code = $conn->prepare("SELECT id FROM khuyen_mai WHERE code = ?");
        $check_code->bind_param("s", $code);
        $check_code->execute();
        if ($check_code->get_result()->num_rows > 0) {
            $_SESSION['admin_error'] = 'Mã khuyến mãi đã tồn tại!';
        } else {
            $stmt = $conn->prepare("INSERT INTO khuyen_mai (code, title, description, type, value, min_order_amount, start_at, end_at, usage_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssddssi", $code, $title, $description, $type, $value, $min_order_amount, $start_at, $end_at, $usage_limit);
            if ($stmt->execute()) {
                $_SESSION['admin_success'] = 'Thêm mã khuyến mãi thành công!';
            } else {
                $_SESSION['admin_error'] = 'Lỗi khi thêm mã khuyến mãi: ' . $conn->error;
            }
        }
    }

    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $code = strtoupper(trim($_POST['code']));
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $type = $_POST['type'];
        $value = floatval($_POST['value']);
        $min_order_amount = floatval($_POST['min_order_amount']);
        $start_at = $_POST['start_at'];
        $end_at = $_POST['end_at'];
        $usage_limit = intval($_POST['usage_limit']) ?: null;

        // Kiểm tra mã đã tồn tại (trừ mã hiện tại)
        $check_code = $conn->prepare("SELECT id FROM khuyen_mai WHERE code = ? AND id != ?");
        $check_code->bind_param("si", $code, $id);
        $check_code->execute();
        if ($check_code->get_result()->num_rows > 0) {
            $_SESSION['admin_error'] = 'Mã khuyến mãi đã tồn tại!';
        } else {
            $stmt = $conn->prepare("UPDATE khuyen_mai SET code=?, title=?, description=?, type=?, value=?, min_order_amount=?, start_at=?, end_at=?, usage_limit=? WHERE id=?");
            $stmt->bind_param("ssssddssii", $code, $title, $description, $type, $value, $min_order_amount, $start_at, $end_at, $usage_limit, $id);
            if ($stmt->execute()) {
                $_SESSION['admin_success'] = 'Cập nhật mã khuyến mãi thành công!';
            } else {
                $_SESSION['admin_error'] = 'Lỗi khi cập nhật mã khuyến mãi: ' . $conn->error;
            }
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM khuyen_mai WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['admin_success'] = 'Xóa mã khuyến mãi thành công!';
        } else {
            $_SESSION['admin_error'] = 'Lỗi khi xóa mã khuyến mãi: ' . $conn->error;
        }
    }

    header('Location: admin-promotions.php');
    exit();
}

// Lọc và phân trang
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (code LIKE ? OR title LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($status_filter) {
    if ($status_filter === 'active') {
        $where .= " AND start_at <= NOW() AND end_at >= NOW()";
    } elseif ($status_filter === 'expired') {
        $where .= " AND end_at < NOW()";
    } elseif ($status_filter === 'upcoming') {
        $where .= " AND start_at > NOW()";
    }
}

// Đếm tổng
$count_sql = "SELECT COUNT(*) as total FROM khuyen_mai WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Lấy danh sách
$sql = "SELECT km.*,
    CASE
        WHEN km.start_at > NOW() THEN 'upcoming'
        WHEN km.end_at < NOW() THEN 'expired'
        ELSE 'active'
    END as status,
    COALESCE(usage_stats.used_count, 0) as used_count
FROM khuyen_mai km
LEFT JOIN (
    SELECT coupon_code, COUNT(*) as used_count 
    FROM user_coupon_usage 
    GROUP BY coupon_code
) usage_stats ON km.code = usage_stats.coupon_code
WHERE $where ORDER BY km.created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$promotions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-500"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-green-700"><?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-500"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700"><?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800"><?php echo $page_title; ?></h1>
        <p class="text-gray-600"><?php echo $page_subtitle; ?></p>
    </div>
    <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">
        <i class="fas fa-plus mr-2"></i>Thêm Mã Khuyến Mãi
    </button>
</div>

<!-- Bộ lọc -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm mã, tiêu đề..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Tất cả trạng thái</option>
            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Đang hoạt động</option>
            <option value="upcoming" <?php echo $status_filter === 'upcoming' ? 'selected' : ''; ?>>Sắp diễn ra</option>
            <option value="expired" <?php echo $status_filter === 'expired' ? 'selected' : ''; ?>>Đã hết hạn</option>
        </select>
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i>Lọc
        </button>
    </form>
</div>

<!-- Danh sách khuyến mãi -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giảm giá</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đã sử dụng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($promotions as $promo): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-bold text-blue-600"><?php echo htmlspecialchars($promo['code']); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($promo['title']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($promo['description'], 0, 50)); ?>...</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <?php if ($promo['type'] === 'percent'): ?>
                                <?php echo $promo['value']; ?>%
                            <?php else: ?>
                                <?php echo number_format($promo['value'], 0, ',', '.'); ?> VNĐ
                            <?php endif; ?>
                        </div>
                        <?php if ($promo['min_order_amount'] > 0): ?>
                        <div class="text-xs text-gray-500">Đơn tối thiểu: <?php echo number_format($promo['min_order_amount'], 0, ',', '.'); ?> VNĐ</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>Từ: <?php echo date('d/m/Y H:i', strtotime($promo['start_at'])); ?></div>
                        <div>Đến: <?php echo date('d/m/Y H:i', strtotime($promo['end_at'])); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div><?php echo $promo['used_count']; ?> lần</div>
                        <?php if ($promo['usage_limit']): ?>
                        <div class="text-xs text-gray-500">Giới hạn: <?php echo $promo['usage_limit']; ?> lần</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($promo['status'] === 'active'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Đang hoạt động
                            </span>
                        <?php elseif ($promo['status'] === 'upcoming'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Sắp diễn ra
                            </span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Đã hết hạn
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="openEditModal(<?php echo $promo['id']; ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <button onclick="deletePromotion(<?php echo $promo['id']; ?>, '<?php echo htmlspecialchars($promo['code']); ?>')" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages > 1): ?>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Trước</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sau</a>
            <?php endif; ?>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Hiển thị <span class="font-medium"><?php echo ($page-1)*$per_page + 1; ?></span> đến <span class="font-medium"><?php echo min($page*$per_page, $total); ?></span> trong tổng số <span class="font-medium"><?php echo $total; ?></span> kết quả
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <?php if ($page > 1): ?>
                    <a href="?page=1&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Đầu</a>
                    <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Trước</a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Sau</a>
                    <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Cuối</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm/Sửa -->
<div id="promotionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Thêm Mã Khuyến Mãi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="promotionForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="promotionId">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã khuyến mãi *</label>
                        <input type="text" name="code" id="code" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="SUMMER2024">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                        <input type="text" name="title" id="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Giảm giá mùa hè">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Mô tả chi tiết về khuyến mãi..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá *</label>
                        <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="percent">Phần trăm (%)</option>
                            <option value="fixed">Cố định (VNĐ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị giảm *</label>
                        <input type="number" name="value" id="value" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="10">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đơn hàng tối thiểu (VNĐ)</label>
                        <input type="number" name="min_order_amount" id="min_order_amount" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="500000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới hạn sử dụng</label>
                        <input type="number" name="usage_limit" id="usage_limit" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="100">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian bắt đầu *</label>
                        <input type="datetime-local" name="start_at" id="start_at" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian kết thúc *</label>
                        <input type="datetime-local" name="end_at" id="end_at" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Mã Khuyến Mãi';
    document.getElementById('formAction').value = 'add';
    document.getElementById('promotionId').value = '';
    document.getElementById('promotionForm').reset();
    document.getElementById('promotionModal').classList.remove('hidden');

    // Set default datetime
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('start_at').value = now.toISOString().slice(0, 16);
    document.getElementById('end_at').value = tomorrow.toISOString().slice(0, 16);
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Sửa Mã Khuyến Mãi';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('promotionId').value = id;

    // Fetch promotion data
    fetch(`api/get-promotion.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const promo = data.promotion;
                document.getElementById('code').value = promo.code;
                document.getElementById('title').value = promo.title;
                document.getElementById('description').value = promo.description;
                document.getElementById('type').value = promo.type;
                document.getElementById('value').value = promo.value;
                document.getElementById('min_order_amount').value = promo.min_order_amount;
                document.getElementById('usage_limit').value = promo.usage_limit;
                document.getElementById('start_at').value = promo.start_at.slice(0, 16);
                document.getElementById('end_at').value = promo.end_at.slice(0, 16);
                document.getElementById('promotionModal').classList.remove('hidden');
            }
        });
}

function closeModal() {
    document.getElementById('promotionModal').classList.add('hidden');
}

function deletePromotion(id, code) {
    if (confirm(`Bạn có chắc muốn xóa mã khuyến mãi "${code}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('promotionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include 'includes/admin-footer.php'; ?>