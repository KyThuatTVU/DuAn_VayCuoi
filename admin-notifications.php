<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Thông Báo';
$page_subtitle = 'Quản lý thông báo hệ thống';

// Kiểm tra bảng
$check = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
$table_exists = $check && $check->num_rows > 0;

// Xử lý actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'mark_all_read' && $table_exists) {
        $conn->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
        $_SESSION['admin_success'] = 'Đã đánh dấu tất cả đã đọc!';
    }
    
    if ($action === 'delete_all_read' && $table_exists) {
        $conn->query("DELETE FROM admin_notifications WHERE is_read = 1");
        $_SESSION['admin_success'] = 'Đã xóa tất cả thông báo đã đọc!';
    }
    
    if ($action === 'delete' && $table_exists) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM admin_notifications WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Đã xóa thông báo!';
    }
    
    header('Location: admin-notifications.php');
    exit();
}

// Lấy thông báo
$notifications = [];
$unread_count = 0;
if ($table_exists) {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = 20;
    $offset = ($page - 1) * $per_page;
    
    $total = $conn->query("SELECT COUNT(*) as cnt FROM admin_notifications")->fetch_assoc()['cnt'];
    $total_pages = ceil($total / $per_page);
    
    $result = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    $unread_count = $conn->query("SELECT COUNT(*) as cnt FROM admin_notifications WHERE is_read = 0")->fetch_assoc()['cnt'];
}

// Helper function
function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff/60) . ' phút trước';
    if ($diff < 86400) return floor($diff/3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff/86400) . ' ngày trước';
    return date('d/m/Y H:i', strtotime($datetime));
}

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <span class="text-gray-500">Tổng: <strong class="text-gray-800"><?php echo $total ?? 0; ?></strong> thông báo</span>
        <?php if ($unread_count > 0): ?>
        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium"><?php echo $unread_count; ?> chưa đọc</span>
        <?php endif; ?>
    </div>
    <div class="flex items-center gap-2">
        <?php if ($unread_count > 0): ?>
        <form method="POST" class="inline">
            <input type="hidden" name="action" value="mark_all_read">
            <button type="submit" class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition text-sm">
                <i class="fas fa-check-double mr-1"></i> Đánh dấu tất cả đã đọc
            </button>
        </form>
        <?php endif; ?>
        <form method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa tất cả thông báo đã đọc?')">
            <input type="hidden" name="action" value="delete_all_read">
            <button type="submit" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                <i class="fas fa-trash mr-1"></i> Xóa đã đọc
            </button>
        </form>
    </div>
</div>

<!-- Notifications List -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <?php if (empty($notifications)): ?>
    <div class="p-12 text-center text-gray-500">
        <i class="fas fa-bell-slash text-5xl text-gray-300 mb-4"></i>
        <p class="text-lg">Chưa có thông báo nào</p>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-100">
        <?php foreach ($notifications as $notif): 
            $icon_class = 'fa-bell';
            $bg_class = 'bg-gray-100 text-gray-600';
            if ($notif['type'] === 'account_locked') {
                $icon_class = 'fa-lock';
                $bg_class = 'bg-red-100 text-red-600';
            } elseif ($notif['type'] === 'new_order') {
                $icon_class = 'fa-shopping-bag';
                $bg_class = 'bg-green-100 text-green-600';
            } elseif ($notif['type'] === 'new_contact') {
                $icon_class = 'fa-envelope';
                $bg_class = 'bg-blue-100 text-blue-600';
            } elseif ($notif['type'] === 'new_booking') {
                $icon_class = 'fa-calendar';
                $bg_class = 'bg-purple-100 text-purple-600';
            }
            
            $link = '#';
            if (isset($notif['reference_type']) && isset($notif['reference_id'])) {
                if ($notif['reference_type'] === 'user' && $notif['reference_id']) {
                    $link = 'admin-user-detail.php?id=' . $notif['reference_id'];
                } elseif ($notif['reference_type'] === 'order' && $notif['reference_id']) {
                    $link = 'admin-order-detail.php?id=' . $notif['reference_id'];
                }
            }
        ?>
        <div class="p-4 lg:p-5 hover:bg-gray-50 transition <?php echo $notif['is_read'] ? 'opacity-60' : 'bg-blue-50/30'; ?>">
            <div class="flex gap-4">
                <div class="w-12 h-12 rounded-full <?php echo $bg_class; ?> flex items-center justify-center flex-shrink-0">
                    <i class="fas <?php echo $icon_class; ?> text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900 <?php echo $notif['is_read'] ? '' : 'text-navy-900'; ?>">
                                <?php echo htmlspecialchars($notif['title']); ?>
                            </p>
                            <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($notif['content'] ?? ''); ?></p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span><i class="far fa-clock mr-1"></i><?php echo timeAgo($notif['created_at']); ?></span>
                                <span class="px-2 py-0.5 bg-gray-100 rounded-full"><?php echo htmlspecialchars($notif['type']); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <?php if (!$notif['is_read']): ?>
                            <span class="w-3 h-3 bg-accent-500 rounded-full" title="Chưa đọc"></span>
                            <?php endif; ?>
                            <?php if ($link !== '#'): ?>
                            <a href="<?php echo $link; ?>" class="text-accent-500 hover:text-accent-600 p-2 hover:bg-accent-50 rounded-lg" title="Xem chi tiết">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <?php endif; ?>
                            <form method="POST" class="inline" onsubmit="return confirm('Xóa thông báo này?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $notif['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if (isset($total_pages) && $total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" 
           class="px-4 py-2 rounded-lg <?php echo $i === $page ? 'bg-accent-500 text-white' : 'bg-white text-navy-700 hover:bg-gray-100'; ?> transition">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>

<?php include 'includes/admin-footer.php'; ?>
