<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$page_title = 'Thông Báo';
$user_id = $_SESSION['user_id'];

// Kiểm tra bảng thông báo
$table_exists = $conn->query("SHOW TABLES LIKE 'thong_bao'")->num_rows > 0;

// Phân trang
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$notifications = [];
$total = 0;
$unread_count = 0;

if ($table_exists) {
    // Đếm tổng
    $total = $conn->query("SELECT COUNT(*) as c FROM thong_bao WHERE nguoi_dung_id = $user_id")->fetch_assoc()['c'];
    $unread_count = $conn->query("SELECT COUNT(*) as c FROM thong_bao WHERE nguoi_dung_id = $user_id AND da_doc = 0")->fetch_assoc()['c'];
    
    // Lấy thông báo
    $result = $conn->query("SELECT * FROM thong_bao WHERE nguoi_dung_id = $user_id ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$total_pages = ceil($total / $per_page);

// Helper function
function getIcon($type) {
    $icons = [
        'admin_reply' => ['icon' => '💬', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
        'comment_reply' => ['icon' => '💬', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'comment_reaction' => ['icon' => '❤️', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
        'order_update' => ['icon' => '📦', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
        'new_blog' => ['icon' => '📰', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
        'promotion' => ['icon' => '🎉', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
        'system' => ['icon' => '🔔', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600']
    ];
    return $icons[$type] ?? $icons['system'];
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    
    return date('d/m/Y H:i', $time);
}

include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Thông Báo</h1>
                    <p class="text-gray-500 mt-1">
                        <?php if ($unread_count > 0): ?>
                            Bạn có <span class="text-primary font-semibold"><?php echo $unread_count; ?></span> thông báo chưa đọc
                        <?php else: ?>
                            Tất cả thông báo đã được đọc
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($unread_count > 0): ?>
                <button onclick="markAllRead()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-accent transition font-medium">
                    <i class="fas fa-check-double mr-2"></i>Đánh dấu tất cả đã đọc
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <?php if (empty($notifications)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Chưa có thông báo</h3>
                <p class="text-gray-500">Các thông báo mới sẽ xuất hiện ở đây</p>
            </div>
            <?php else: ?>
            <div class="divide-y divide-gray-100" id="notificationsList">
                <?php foreach ($notifications as $n): 
                    $icon_data = getIcon($n['loai']);
                ?>
                <div class="notification-row p-4 hover:bg-gray-50 transition <?php echo $n['da_doc'] ? '' : 'bg-blue-50/50'; ?>" data-id="<?php echo $n['id']; ?>">
                    <a href="<?php echo $n['link'] ?: '#'; ?>" class="flex gap-4" onclick="markRead(<?php echo $n['id']; ?>)">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full <?php echo $icon_data['bg']; ?> flex items-center justify-center text-2xl">
                                <?php echo $icon_data['icon']; ?>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-semibold text-gray-800 <?php echo $n['da_doc'] ? '' : 'text-primary'; ?>">
                                    <?php echo htmlspecialchars($n['tieu_de']); ?>
                                </h4>
                                <?php if (!$n['da_doc']): ?>
                                <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2"><?php echo htmlspecialchars($n['noi_dung']); ?></p>
                            <p class="text-gray-400 text-xs mt-2"><?php echo timeAgo($n['created_at']); ?></p>
                        </div>
                    </a>
                    <div class="flex justify-end mt-2">
                        <button onclick="deleteNotification(<?php echo $n['id']; ?>)" class="text-xs text-red-500 hover:text-red-700 transition">
                            <i class="fas fa-trash mr-1"></i>Xóa
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="p-4 border-t border-gray-100 flex justify-center gap-2">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded-lg transition <?php echo $i === $page ? 'bg-primary text-white' : 'bg-gray-100 hover:bg-gray-200'; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function markRead(id) {
    const formData = new FormData();
    formData.append('action', 'mark_read');
    formData.append('notification_id', id);
    
    await fetch('api/notifications.php', {
        method: 'POST',
        body: formData
    });
}

async function markAllRead() {
    const formData = new FormData();
    formData.append('action', 'mark_all_read');
    
    const response = await fetch('api/notifications.php', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    
    if (data.success) {
        location.reload();
    }
}

async function deleteNotification(id) {
    if (!confirm('Bạn có chắc muốn xóa thông báo này?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('notification_id', id);
    
    const response = await fetch('api/notifications.php', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    
    if (data.success) {
        document.querySelector(`.notification-row[data-id="${id}"]`).remove();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
