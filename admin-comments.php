<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Bình Luận';
$page_subtitle = 'Xem và quản lý bình luận sản phẩm & bài viết';

// Xử lý xóa bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_product') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM binh_luan_san_pham WHERE id = $id");
        $_SESSION['admin_success'] = 'Đã xóa bình luận sản phẩm!';
    }
    
    if ($_POST['action'] === 'delete_blog') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM binh_luan_bai_viet WHERE id = $id");
        $_SESSION['admin_success'] = 'Đã xóa bình luận bài viết!';
    }
    
    header('Location: admin-comments.php');
    exit();
}

// Lấy thống kê
$stats = [
    'total_product_comments' => $conn->query("SELECT COUNT(*) as count FROM binh_luan_san_pham")->fetch_assoc()['count'],
    'total_blog_comments' => $conn->query("SELECT COUNT(*) as count FROM binh_luan_bai_viet")->fetch_assoc()['count'],
    'total_product_reactions' => $conn->query("SELECT COUNT(*) as count FROM cam_xuc_san_pham")->fetch_assoc()['count'],
    'total_blog_reactions' => $conn->query("SELECT COUNT(*) as count FROM cam_xuc_bai_viet")->fetch_assoc()['count'],
];

// Bộ lọc
$type = $_GET['type'] ?? 'product'; // product hoặc blog
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Lấy bình luận theo loại
if ($type === 'product') {
    $where = "1=1";
    if ($search) {
        $where .= " AND (nd.ho_ten LIKE '%$search%' OR bl.noi_dung LIKE '%$search%' OR vc.ten_vay LIKE '%$search%')";
    }
    
    $total = $conn->query("SELECT COUNT(*) as count FROM binh_luan_san_pham bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        JOIN vay_cuoi vc ON bl.vay_id = vc.id 
        WHERE $where")->fetch_assoc()['count'];
    
    $comments = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt, vc.ten_vay, vc.ma_vay,
        (SELECT COUNT(*) FROM binh_luan_san_pham WHERE parent_id = bl.id) as reply_count
        FROM binh_luan_san_pham bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        JOIN vay_cuoi vc ON bl.vay_id = vc.id 
        WHERE $where AND bl.parent_id IS NULL
        ORDER BY bl.created_at DESC 
        LIMIT $per_page OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
} else {
    $where = "1=1";
    if ($search) {
        $where .= " AND (nd.ho_ten LIKE '%$search%' OR bl.noi_dung LIKE '%$search%' OR t.title LIKE '%$search%')";
    }
    
    $total = $conn->query("SELECT COUNT(*) as count FROM binh_luan_bai_viet bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        JOIN tin_tuc_cuoi_hoi t ON bl.bai_viet_id = t.id 
        WHERE $where")->fetch_assoc()['count'];
    
    $comments = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt, t.title as ten_bai_viet, t.slug,
        (SELECT COUNT(*) FROM binh_luan_bai_viet WHERE parent_id = bl.id) as reply_count
        FROM binh_luan_bai_viet bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        JOIN tin_tuc_cuoi_hoi t ON bl.bai_viet_id = t.id 
        WHERE $where AND bl.parent_id IS NULL
        ORDER BY bl.created_at DESC 
        LIMIT $per_page OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
}

$total_pages = ceil($total / $per_page);

include 'includes/admin-layout.php';
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Bình luận sản phẩm</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_product_comments']); ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-comments text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Bình luận bài viết</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_blog_comments']); ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-comment-dots text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Cảm xúc sản phẩm</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_product_reactions']); ?></p>
            </div>
            <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-heart text-pink-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-navy-500 text-sm font-medium">Cảm xúc bài viết</p>
                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($stats['total_blog_reactions']); ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-thumbs-up text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<!-- Tabs & Filter -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Tabs -->
        <div class="flex gap-2">
            <a href="?type=product" class="px-4 py-2 rounded-lg font-medium transition <?php echo $type === 'product' ? 'bg-accent-500 text-white' : 'bg-gray-100 text-navy-700 hover:bg-gray-200'; ?>">
                <i class="fas fa-tshirt mr-2"></i>Sản phẩm (<?php echo $stats['total_product_comments']; ?>)
            </a>
            <a href="?type=blog" class="px-4 py-2 rounded-lg font-medium transition <?php echo $type === 'blog' ? 'bg-accent-500 text-white' : 'bg-gray-100 text-navy-700 hover:bg-gray-200'; ?>">
                <i class="fas fa-newspaper mr-2"></i>Bài viết (<?php echo $stats['total_blog_comments']; ?>)
            </a>
        </div>
        
        <!-- Search -->
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                placeholder="Tìm kiếm..." class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
            <button type="submit" class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<!-- Comments List -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-navy-500 uppercase tracking-wider">Người dùng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-navy-500 uppercase tracking-wider"><?php echo $type === 'product' ? 'Sản phẩm' : 'Bài viết'; ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-navy-500 uppercase tracking-wider">Nội dung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-navy-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-navy-500 uppercase tracking-wider">Trả lời</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-navy-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($comments as $comment): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold overflow-hidden">
                                <?php if ($comment['avt']): ?>
                                    <img src="<?php echo htmlspecialchars($comment['avt']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($comment['ho_ten'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-navy-900"><?php echo htmlspecialchars($comment['ho_ten']); ?></p>
                                <p class="text-xs text-navy-500"><?php echo htmlspecialchars($comment['email']); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <?php if ($type === 'product'): ?>
                                <p class="font-medium text-navy-900"><?php echo htmlspecialchars($comment['ten_vay']); ?></p>
                                <p class="text-xs text-navy-500">Mã: <?php echo htmlspecialchars($comment['ma_vay']); ?></p>
                                <a href="product-detail.php?id=<?php echo $comment['vay_id']; ?>" target="_blank" class="text-xs text-accent-500 hover:underline">
                                    <i class="fas fa-external-link-alt mr-1"></i>Xem
                                </a>
                            <?php else: ?>
                                <p class="font-medium text-navy-900 max-w-xs truncate"><?php echo htmlspecialchars($comment['ten_bai_viet']); ?></p>
                                <a href="blog-detail.php?slug=<?php echo $comment['slug']; ?>" target="_blank" class="text-xs text-accent-500 hover:underline">
                                    <i class="fas fa-external-link-alt mr-1"></i>Xem
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-navy-700 max-w-md line-clamp-2"><?php echo htmlspecialchars($comment['noi_dung']); ?></p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <p class="text-sm text-navy-700"><?php echo date('d/m/Y', strtotime($comment['created_at'])); ?></p>
                        <p class="text-xs text-navy-500"><?php echo date('H:i', strtotime($comment['created_at'])); ?></p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($comment['reply_count'] > 0): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-reply mr-1"></i><?php echo $comment['reply_count']; ?>
                            </span>
                        <?php else: ?>
                            <span class="text-xs text-navy-400">Chưa có</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="viewReplies(<?php echo $comment['id']; ?>, '<?php echo $type; ?>')" class="text-blue-600 hover:text-blue-900 mr-3" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteComment(<?php echo $comment['id']; ?>, '<?php echo $type; ?>')" class="text-red-600 hover:text-red-900" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($comments)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-navy-500">
                        <i class="fas fa-comments text-4xl mb-4 text-navy-300"></i>
                        <p>Chưa có bình luận nào</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&type=<?php echo $type; ?>&search=<?php echo urlencode($search); ?>" 
           class="px-4 py-2 rounded-lg <?php echo $i === $page ? 'bg-accent-500 text-white' : 'bg-white text-navy-700 hover:bg-gray-100'; ?> transition">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>

<!-- Modal xem chi tiết -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 my-8 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-navy-900">Chi tiết bình luận</h3>
                <button onclick="closeModal()" class="text-navy-400 hover:text-navy-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modalContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" class="hidden">
    <input type="hidden" name="action" id="deleteAction">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function viewReplies(commentId, type) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<p class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tải...</p>';
    
    // Load comment details via AJAX
    fetch(`api/admin-comment-details.php?id=${commentId}&type=${type}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = '<p class="text-center text-red-500">Lỗi tải dữ liệu</p>';
            }
        })
        .catch(err => {
            content.innerHTML = '<p class="text-center text-red-500">Lỗi kết nối</p>';
        });
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

function deleteComment(id, type) {
    if (confirm('Bạn có chắc muốn xóa bình luận này? Tất cả các trả lời cũng sẽ bị xóa.')) {
        document.getElementById('deleteAction').value = type === 'product' ? 'delete_product' : 'delete_blog';
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php include 'includes/admin-footer.php'; ?>
