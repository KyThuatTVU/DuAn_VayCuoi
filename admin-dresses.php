<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Váy Cưới';
$page_subtitle = 'Thêm, sửa, xóa các mẫu váy cưới';

// Tạo thư mục upload nếu chưa có
$upload_dir = 'uploads/dresses/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Debug: Ghi log
    error_log("Admin Dresses POST: action=$action, POST=" . print_r($_POST, true));
    
    if ($action === 'add') {
        $ma_vay = trim($_POST['ma_vay']);
        $ten_vay = trim($_POST['ten_vay']);
        $mo_ta = trim($_POST['mo_ta']);
        $gia_thue = floatval($_POST['gia_thue']);
        $so_luong = intval($_POST['so_luong_ton']);
        
        $stmt = $conn->prepare("INSERT INTO vay_cuoi (ma_vay, ten_vay, mo_ta, gia_thue, so_luong_ton) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdi", $ma_vay, $ten_vay, $mo_ta, $gia_thue, $so_luong);
        
        if ($stmt->execute()) {
            $vay_id = $conn->insert_id;
            
            // Upload hình ảnh
            if (!empty($_FILES['images']['name'][0])) {
                $is_first = true;
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_name = time() . '_' . $key . '_' . basename($_FILES['images']['name'][$key]);
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($tmp_name, $file_path)) {
                            $url = $file_path;
                            $is_primary = $is_first ? 1 : 0;
                            $is_first = false;
                            
                            $img_stmt = $conn->prepare("INSERT INTO hinh_anh_vay_cuoi (vay_id, url, is_primary, sort_order) VALUES (?, ?, ?, ?)");
                            $img_stmt->bind_param("isii", $vay_id, $url, $is_primary, $key);
                            $img_stmt->execute();
                        }
                    }
                }
            }
            $_SESSION['admin_success'] = 'Thêm váy cưới thành công!';
        } else {
            $_SESSION['admin_error'] = 'Lỗi: Mã váy đã tồn tại!';
        }
    }
    
    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $ma_vay = trim($_POST['ma_vay']);
        $ten_vay = trim($_POST['ten_vay']);
        $mo_ta = trim($_POST['mo_ta']);
        $gia_thue = floatval($_POST['gia_thue']);
        $so_luong = intval($_POST['so_luong_ton']);
        
        $stmt = $conn->prepare("UPDATE vay_cuoi SET ma_vay=?, ten_vay=?, mo_ta=?, gia_thue=?, so_luong_ton=? WHERE id=?");
        $stmt->bind_param("sssdii", $ma_vay, $ten_vay, $mo_ta, $gia_thue, $so_luong, $id);
        $stmt->execute();
        
        // Upload hình ảnh mới nếu có
        if (!empty($_FILES['images']['name'][0])) {
            // Lấy sort_order cao nhất
            $max_order = $conn->query("SELECT MAX(sort_order) as max_order FROM hinh_anh_vay_cuoi WHERE vay_id = $id")->fetch_assoc()['max_order'] ?? 0;
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = time() . '_' . $key . '_' . basename($_FILES['images']['name'][$key]);
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $url = $file_path;
                        $sort = $max_order + $key + 1;
                        
                        $img_stmt = $conn->prepare("INSERT INTO hinh_anh_vay_cuoi (vay_id, url, is_primary, sort_order) VALUES (?, ?, 0, ?)");
                        $img_stmt->bind_param("isi", $id, $url, $sort);
                        $img_stmt->execute();
                    }
                }
            }
        }
        $_SESSION['admin_success'] = 'Cập nhật váy cưới thành công!';
    }
    
    if ($action === 'delete') {
        $id = intval($_POST['id']);
        // Xóa hình ảnh trước
        $images = $conn->query("SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = $id")->fetch_all(MYSQLI_ASSOC);
        foreach ($images as $img) {
            if (file_exists($img['url'])) unlink($img['url']);
        }
        $conn->query("DELETE FROM hinh_anh_vay_cuoi WHERE vay_id = $id");
        
        $stmt = $conn->prepare("DELETE FROM vay_cuoi WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Xóa váy cưới thành công!';
    }
    
    if ($action === 'delete_image') {
        $img_id = intval($_POST['image_id']);
        $result = $conn->query("SELECT url FROM hinh_anh_vay_cuoi WHERE id = $img_id");
        if ($row = $result->fetch_assoc()) {
            if (file_exists($row['url'])) unlink($row['url']);
            $conn->query("DELETE FROM hinh_anh_vay_cuoi WHERE id = $img_id");
        }
        $_SESSION['admin_success'] = 'Xóa hình ảnh thành công!';
    }
    
    if ($action === 'set_primary') {
        $img_id = intval($_POST['image_id']);
        $vay_id = intval($_POST['vay_id']);
        $conn->query("UPDATE hinh_anh_vay_cuoi SET is_primary = 0 WHERE vay_id = $vay_id");
        $conn->query("UPDATE hinh_anh_vay_cuoi SET is_primary = 1 WHERE id = $img_id");
        $_SESSION['admin_success'] = 'Đã đặt làm ảnh chính!';
    }
    
    header('Location: admin-dresses.php');
    exit();
}

// Lấy danh sách váy
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (ma_vay LIKE ? OR ten_vay LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$count_sql = "SELECT COUNT(*) as total FROM vay_cuoi WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT v.*, (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id AND is_primary = 1 LIMIT 1) as image,
        (SELECT COUNT(*) FROM hinh_anh_vay_cuoi WHERE vay_id = v.id) as image_count
        FROM vay_cuoi v WHERE $where ORDER BY v.created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$dresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<!-- Header Actions -->
<div class="flex justify-between items-center mb-6">
    <div class="text-sm text-navy-500">Tổng: <span class="font-bold text-navy-900"><?php echo $total; ?></span> mẫu váy</div>
    <button onclick="openModal('add')" class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition">
        <i class="fas fa-plus mr-2"></i>Thêm váy mới
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

<!-- Tìm kiếm -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm mã váy, tên váy..." class="flex-1 border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500 focus:border-transparent">
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-6 py-2 hover:bg-accent-600 transition">
            <i class="fas fa-search mr-2"></i>Tìm
        </button>
    </form>
</div>

<!-- Bảng váy cưới -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Váy cưới</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Mã váy</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Giá thuê</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Tồn kho</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Ảnh</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach ($dresses as $dress): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <?php if ($dress['image']): ?>
                            <img src="<?php echo htmlspecialchars($dress['image']); ?>" class="w-14 h-14 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="font-medium text-navy-900"><?php echo htmlspecialchars($dress['ten_vay']); ?></p>
                            <p class="text-xs text-navy-500 truncate max-w-xs"><?php echo htmlspecialchars(substr($dress['mo_ta'] ?? '', 0, 50)); ?>...</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 font-medium text-accent-500"><?php echo htmlspecialchars($dress['ma_vay']); ?></td>
                <td class="px-6 py-4 font-bold text-green-600"><?php echo number_format($dress['gia_thue']); ?>đ</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $dress['so_luong_ton'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo $dress['so_luong_ton']; ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                        <?php echo $dress['image_count']; ?> ảnh
                    </span>
                </td>
                <td class="px-6 py-4 space-x-2">
                    <button onclick="openImageModal(<?php echo $dress['id']; ?>)" class="text-blue-500 hover:text-blue-600" title="Quản lý ảnh">
                        <i class="fas fa-images"></i>
                    </button>
                    <button onclick='openModal("edit", <?php echo json_encode($dress); ?>)' class="text-accent-500 hover:text-accent-600" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteDress(<?php echo $dress['id']; ?>, '<?php echo htmlspecialchars($dress['ten_vay']); ?>')" class="text-red-500 hover:text-red-600" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($dresses)): ?>
            <tr><td colspan="6" class="px-6 py-8 text-center text-navy-500">Không có váy cưới nào</td></tr>
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

<!-- Modal Thêm/Sửa -->
<div id="dressModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 my-8">
        <div class="p-6">
            <h3 id="modalTitle" class="text-xl font-bold text-navy-900 mb-4">Thêm váy cưới</h3>
            <form id="dressForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="dressId">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1">Mã váy</label>
                            <input type="text" name="ma_vay" id="ma_vay" required class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1">Số lượng tồn</label>
                            <input type="number" name="so_luong_ton" id="so_luong_ton" required class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Tên váy</label>
                        <input type="text" name="ten_vay" id="ten_vay" required class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Giá thuê (VNĐ)</label>
                        <input type="number" name="gia_thue" id="gia_thue" required class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Mô tả</label>
                        <textarea name="mo_ta" id="mo_ta" rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Hình ảnh (có thể chọn nhiều)</label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-accent-500">
                        <p class="text-xs text-navy-500 mt-1">Ảnh đầu tiên sẽ là ảnh chính. Hỗ trợ: JPG, PNG, GIF</p>
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

<!-- Modal Quản lý ảnh -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 my-8">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-navy-900">Quản lý hình ảnh</h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="imageGallery" class="grid grid-cols-3 gap-4 mb-4">
                <!-- Images loaded via JS -->
            </div>
            <form method="POST" enctype="multipart/form-data" class="border-t pt-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="imageVayId">
                <input type="hidden" name="ma_vay" id="img_ma_vay">
                <input type="hidden" name="ten_vay" id="img_ten_vay">
                <input type="hidden" name="mo_ta" id="img_mo_ta">
                <input type="hidden" name="gia_thue" id="img_gia_thue">
                <input type="hidden" name="so_luong_ton" id="img_so_luong">
                <div class="flex gap-4">
                    <input type="file" name="images[]" multiple accept="image/*" class="flex-1 border border-gray-200 rounded-lg px-4 py-2">
                    <button type="submit" class="px-4 py-2 bg-accent-500 text-white rounded-lg hover:bg-accent-600 transition">
                        <i class="fas fa-upload mr-2"></i>Tải lên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<form id="imageActionForm" method="POST" class="hidden">
    <input type="hidden" name="action" id="imageAction">
    <input type="hidden" name="image_id" id="imageId">
    <input type="hidden" name="vay_id" id="actionVayId">
</form>

<script>
    let currentDressData = null;
    
    function openModal(action, data = null) {
        document.getElementById('dressModal').classList.remove('hidden');
        document.getElementById('dressModal').classList.add('flex');
        document.getElementById('formAction').value = action;
        
        if (action === 'edit' && data) {
            currentDressData = data;
            document.getElementById('modalTitle').textContent = 'Sửa váy cưới';
            document.getElementById('dressId').value = data.id;
            document.getElementById('ma_vay').value = data.ma_vay;
            document.getElementById('ten_vay').value = data.ten_vay;
            document.getElementById('mo_ta').value = data.mo_ta || '';
            document.getElementById('gia_thue').value = data.gia_thue;
            document.getElementById('so_luong_ton').value = data.so_luong_ton;
        } else {
            currentDressData = null;
            document.getElementById('modalTitle').textContent = 'Thêm váy cưới';
            document.getElementById('dressForm').reset();
        }
    }
    
    function closeModal() {
        document.getElementById('dressModal').classList.add('hidden');
        document.getElementById('dressModal').classList.remove('flex');
    }
    
    function openImageModal(vayId) {
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
        document.getElementById('imageVayId').value = vayId;
        
        // Load images via AJAX
        fetch('api/get-dress-images.php?vay_id=' + vayId)
            .then(r => r.json())
            .then(data => {
                let html = '';
                if (data.images && data.images.length > 0) {
                    data.images.forEach(img => {
                        html += `
                            <div class="relative group">
                                <img src="${img.url}" class="w-full h-32 object-cover rounded-lg ${img.is_primary == 1 ? 'ring-2 ring-accent-500' : ''}">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">
                                    ${img.is_primary != 1 ? `<button onclick="setPrimary(${img.id}, ${vayId})" class="p-2 bg-white rounded-full text-accent-500 hover:bg-accent-500 hover:text-white" title="Đặt làm ảnh chính"><i class="fas fa-star"></i></button>` : ''}
                                    <button onclick="deleteImage(${img.id})" class="p-2 bg-white rounded-full text-red-500 hover:bg-red-500 hover:text-white" title="Xóa"><i class="fas fa-trash"></i></button>
                                </div>
                                ${img.is_primary == 1 ? '<span class="absolute top-1 left-1 bg-accent-500 text-white text-xs px-2 py-0.5 rounded">Ảnh chính</span>' : ''}
                            </div>
                        `;
                    });
                    // Set hidden fields for upload form
                    document.getElementById('img_ma_vay').value = data.dress.ma_vay;
                    document.getElementById('img_ten_vay').value = data.dress.ten_vay;
                    document.getElementById('img_mo_ta').value = data.dress.mo_ta || '';
                    document.getElementById('img_gia_thue').value = data.dress.gia_thue;
                    document.getElementById('img_so_luong').value = data.dress.so_luong_ton;
                } else {
                    html = '<p class="col-span-3 text-center text-navy-500 py-4">Chưa có hình ảnh nào</p>';
                }
                document.getElementById('imageGallery').innerHTML = html;
            });
    }
    
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
    }
    
    function setPrimary(imgId, vayId) {
        document.getElementById('imageAction').value = 'set_primary';
        document.getElementById('imageId').value = imgId;
        document.getElementById('actionVayId').value = vayId;
        document.getElementById('imageActionForm').submit();
    }
    
    function deleteImage(imgId) {
        if (confirm('Bạn có chắc muốn xóa hình ảnh này?')) {
            document.getElementById('imageAction').value = 'delete_image';
            document.getElementById('imageId').value = imgId;
            document.getElementById('imageActionForm').submit();
        }
    }
    
    function deleteDress(id, name) {
        if (confirm('Bạn có chắc muốn xóa váy "' + name + '"?\nTất cả hình ảnh liên quan cũng sẽ bị xóa!')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<?php include 'includes/admin-footer.php'; ?>
