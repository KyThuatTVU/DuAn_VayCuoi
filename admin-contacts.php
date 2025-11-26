<?php
session_start();
require_once 'includes/config.php';

$page_title = 'Quản Lý Liên Hệ';
$page_subtitle = 'Xem và phản hồi tin nhắn từ khách hàng';

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE lien_he SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật trạng thái thành công!';
    }
    
    if ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM lien_he WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Xóa liên hệ thành công!';
    }
    
    header('Location: admin-contacts.php');
    exit();
}

// Lấy danh sách
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($status_filter) {
    $where .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($search) {
    $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$count_sql = "SELECT COUNT(*) as total FROM lien_he WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT * FROM lien_he WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Bộ lọc -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm tên, email, tiêu đề..." class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
        <select name="status" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>Mới</option>
            <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Đã trả lời</option>
            <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Đã đóng</option>
        </select>
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-4 py-2 hover:bg-accent-600 transition">
            <i class="fas fa-search mr-2"></i>Lọc
        </button>
    </form>
</div>

<!-- Danh sách liên hệ -->
<div class="space-y-4">
    <?php foreach ($contacts as $contact): ?>
    <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-semibold text-navy-900"><?php echo htmlspecialchars($contact['subject'] ?? 'Không có tiêu đề'); ?></h3>
                <div class="flex flex-wrap items-center gap-4 text-sm text-navy-500 mt-2">
                    <span><i class="fas fa-user mr-1 text-accent-500"></i><?php echo htmlspecialchars($contact['name']); ?></span>
                    <span><i class="fas fa-envelope mr-1 text-accent-500"></i><?php echo htmlspecialchars($contact['email']); ?></span>
                    <?php if ($contact['phone']): ?>
                    <span><i class="fas fa-phone mr-1 text-accent-500"></i><?php echo htmlspecialchars($contact['phone']); ?></span>
                    <?php endif; ?>
                    <span><i class="fas fa-clock mr-1 text-accent-500"></i><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></span>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                    <select name="status" onchange="this.form.submit()" class="text-sm border-0 rounded-full px-3 py-1 font-medium
                        <?php echo match($contact['status']) {
                            'new' => 'bg-red-100 text-red-700',
                            'replied' => 'bg-blue-100 text-blue-700',
                            'closed' => 'bg-gray-100 text-gray-700',
                            default => 'bg-gray-100 text-gray-700'
                        }; ?>">
                        <option value="new" <?php echo $contact['status'] === 'new' ? 'selected' : ''; ?>>Mới</option>
                        <option value="replied" <?php echo $contact['status'] === 'replied' ? 'selected' : ''; ?>>Đã trả lời</option>
                        <option value="closed" <?php echo $contact['status'] === 'closed' ? 'selected' : ''; ?>>Đã đóng</option>
                    </select>
                </form>
                <button onclick="deleteContact(<?php echo $contact['id']; ?>)" class="text-red-500 hover:text-red-600 p-2">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-navy-700 whitespace-pre-wrap"><?php echo htmlspecialchars($contact['message']); ?></p>
        </div>
        <?php if ($contact['image_path']): ?>
        <div class="mt-4">
            <img src="<?php echo htmlspecialchars($contact['image_path']); ?>" class="max-w-xs rounded-lg" alt="Ảnh đính kèm">
        </div>
        <?php endif; ?>
        <div class="mt-4">
            <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>?subject=Re: <?php echo htmlspecialchars($contact['subject'] ?? ''); ?>" 
               class="inline-flex items-center bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition text-sm">
                <i class="fas fa-reply mr-2"></i>Trả lời qua Email
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($contacts)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-navy-500">
        <i class="fas fa-inbox text-4xl mb-4 text-navy-300"></i>
        <p>Không có liên hệ nào</p>
    </div>
    <?php endif; ?>
</div>

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>" 
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
    function deleteContact(id) {
        if (confirm('Bạn có chắc muốn xóa liên hệ này?')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<?php include 'includes/admin-footer.php'; ?>
