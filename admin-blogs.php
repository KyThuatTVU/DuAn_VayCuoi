<?php
session_start();
require_once 'includes/config.php';

$page_title = 'Quản Lý Tin Tức';
$page_subtitle = 'Thêm, sửa, xóa bài viết tin tức';

// Tạo thư mục upload
$upload_dir = 'uploads/blogs/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
        $summary = trim($_POST['summary']);
        $content = $_POST['content'];
        $status = $_POST['status'];
        $published_at = $status === 'published' ? date('Y-m-d H:i:s') : null;
        $cover_image = null;
        
        // Upload ảnh bìa
        if (!empty($_FILES['cover_image']['name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $file_name = time() . '_' . basename($_FILES['cover_image']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $file_path)) {
                $cover_image = $file_path;
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO tin_tuc_cuoi_hoi (admin_id, title, slug, summary, content, cover_image, status, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $_SESSION['admin_id'], $title, $slug, $summary, $content, $cover_image, $status, $published_at);
        if ($stmt->execute()) {
            $_SESSION['admin_success'] = 'Thêm bài viết thành công!';
        } else {
            $_SESSION['admin_error'] = 'Lỗi: Slug đã tồn tại!';
        }
    }
    
    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']);
        $summary = trim($_POST['summary']);
        $content = $_POST['content'];
        $status = $_POST['status'];
        
        // Upload ảnh bìa mới nếu có
        if (!empty($_FILES['cover_image']['name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            // Xóa ảnh cũ
            $old = $conn->query("SELECT cover_image FROM tin_tuc_cuoi_hoi WHERE id = $id")->fetch_assoc();
            if ($old['cover_image'] && file_exists($old['cover_image'])) {
                unlink($old['cover_image']);
            }
            
            $file_name = time() . '_' . basename($_FILES['cover_image']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $file_path)) {
                $conn->query("UPDATE tin_tuc_cuoi_hoi SET cover_image = '$file_path' WHERE id = $id");
            }
        }
        
        $stmt = $conn->prepare("UPDATE tin_tuc_cuoi_hoi SET title=?, slug=?, summary=?, content=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $slug, $summary, $content, $status, $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật bài viết thành công!';
    }
    
    if ($action === 'delete') {
        $id = intval($_POST['id']);
        // Xóa ảnh bìa
        $old = $conn->query("SELECT cover_image FROM tin_tuc_cuoi_hoi WHERE id = $id")->fetch_assoc();
        if ($old['cover_image'] && file_exists($old['cover_image'])) {
            unlink($old['cover_image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM tin_tuc_cuoi_hoi WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Xóa bài viết thành công!';
    }
    
    header('Location: admin-blogs.php');
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
    $where .= " AND (title LIKE ? OR summary LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$count_sql = "SELECT COUNT(*) as total FROM tin_tuc_cuoi_hoi WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT * FROM tin_tuc_cuoi_hoi WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$blogs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<!-- Header Actions -->
<div class="flex justify-between items-center mb-6">
    <div class="text-sm text-navy-500">Tổng: <span class="font-bold text-navy-900"><?php echo $total; ?></span> bài viết</div>
    <button onclick="openModal('add')" class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition">
        <i class="fas fa-plus mr-2"></i>Thêm bài viết
    </button>
</div>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
    </div>
<?php endif; ?>

<!-- Bộ lọc -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm tiêu đề, tóm tắt..." class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
        <select name="status" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Nháp</option>
            <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
            <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Lưu trữ</option>
        </select>
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-4 py-2 hover:bg-accent-600 transition">
            <i class="fas fa-search mr-2"></i>Lọc
        </button>
    </form>
</div>

<!-- Danh sách bài viết dạng card -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($blogs as $blog): ?>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
        <!-- Ảnh bìa -->
        <div class="h-40 bg-gray-100 relative">
            <?php if ($blog['cover_image']): ?>
                <img src="<?php echo htmlspecialchars($blog['cover_image']); ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-newspaper text-4xl text-gray-300"></i>
                </div>
            <?php endif; ?>
            <span class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-medium <?php echo match($blog['status']) {
                'draft' => 'bg-yellow-100 text-yellow-700',
                'published' => 'bg-green-100 text-green-700',
                'archived' => 'bg-gray-100 text-gray-700',
                default => 'bg-gray-100 text-gray-700'
            }; ?>">
                <?php echo match($blog['status']) {
                    'draft' => 'Nháp',
                    'published' => 'Đã xuất bản',
                    'archived' => 'Lưu trữ',
                    default => $blog['status']
                }; ?>
            </span>
        </div>
        <!-- Nội dung -->
        <div class="p-4">
            <h3 class="font-bold text-navy-900 mb-2 line-clamp-2"><?php echo htmlspecialchars($blog['title']); ?></h3>
            <p class="text-sm text-navy-500 mb-3 line-clamp-2"><?php echo htmlspecialchars($blog['summary'] ?? ''); ?></p>
            <div class="flex items-center justify-between text-xs text-navy-400">
                <span><i class="fas fa-clock mr-1"></i><?php echo date('d/m/Y', strtotime($blog['created_at'])); ?></span>
                <div class="flex gap-2">
                    <button onclick='openModal("edit", <?php echo json_encode($blog); ?>)' class="p-2 text-accent-500 hover:bg-accent-50 rounded-lg" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="blog-detail.php?slug=<?php echo $blog['slug']; ?>" target="_blank" class="p-2 text-green-500 hover:bg-green-50 rounded-lg" title="Xem">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button onclick="deleteBlog(<?php echo $blog['id']; ?>)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($blogs)): ?>
    <div class="col-span-3 bg-white rounded-2xl shadow-sm p-8 text-center text-navy-500">
        <i class="fas fa-newspaper text-4xl mb-4 text-navy-300"></i>
        <p>Không có bài viết nào</p>
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

<!-- Modal -->
<div id="blogModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-8">
        <div class="p-6">
            <h3 id="modalTitle" class="text-xl font-bold text-navy-900 mb-4">Thêm bài viết</h3>
            <form id="blogForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="blogId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Tiêu đề</label>
                        <input type="text" name="title" id="title" required class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Slug (URL)</label>
                        <input type="text" name="slug" id="slug" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500" placeholder="Tự động tạo nếu để trống">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Ảnh bìa</label>
                        <input type="file" name="cover_image" id="cover_image" accept="image/*" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                        <div id="currentImage" class="mt-2 hidden">
                            <img id="previewImage" src="" class="h-20 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Tóm tắt</label>
                        <textarea name="summary" id="summary" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Nội dung</label>
                        <textarea name="content" id="content" rows="8" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Trạng thái</label>
                        <select name="status" id="status" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                            <option value="draft">Nháp</option>
                            <option value="published">Xuất bản</option>
                            <option value="archived">Lưu trữ</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-100 transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-accent-500 text-white rounded-lg hover:bg-accent-600 transition">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
    function openModal(action, data = null) {
        document.getElementById('blogModal').classList.remove('hidden');
        document.getElementById('blogModal').classList.add('flex');
        document.getElementById('formAction').value = action;
        
        if (action === 'edit' && data) {
            document.getElementById('modalTitle').textContent = 'Sửa bài viết';
            document.getElementById('blogId').value = data.id;
            document.getElementById('title').value = data.title;
            document.getElementById('slug').value = data.slug;
            document.getElementById('summary').value = data.summary || '';
            document.getElementById('content').value = data.content || '';
            document.getElementById('status').value = data.status;
            
            if (data.cover_image) {
                document.getElementById('currentImage').classList.remove('hidden');
                document.getElementById('previewImage').src = data.cover_image;
            } else {
                document.getElementById('currentImage').classList.add('hidden');
            }
        } else {
            document.getElementById('modalTitle').textContent = 'Thêm bài viết';
            document.getElementById('blogForm').reset();
            document.getElementById('currentImage').classList.add('hidden');
        }
    }
    
    function closeModal() {
        document.getElementById('blogModal').classList.add('hidden');
        document.getElementById('blogModal').classList.remove('flex');
    }
    
    function deleteBlog(id) {
        if (confirm('Bạn có chắc muốn xóa bài viết này?')) {
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
