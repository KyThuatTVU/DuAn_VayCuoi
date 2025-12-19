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

// Kiểm tra và thêm cột hinh_anh_chinh nếu chưa có
$check_column = $conn->query("SHOW COLUMNS FROM vay_cuoi LIKE 'hinh_anh_chinh'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE vay_cuoi ADD COLUMN hinh_anh_chinh VARCHAR(500) NULL AFTER so_luong_ton");
}

// Kiểm tra và thêm cột size nếu chưa có
$check_size = $conn->query("SHOW COLUMNS FROM vay_cuoi LIKE 'size'");
if ($check_size->num_rows == 0) {
    $conn->query("ALTER TABLE vay_cuoi ADD COLUMN size VARCHAR(100) NULL COMMENT 'Kích cỡ váy' AFTER so_luong_ton");
}

// Xử lý thêm/sửa/xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $ma_vay = trim($_POST['ma_vay']);
        $ten_vay = trim($_POST['ten_vay']);
        $mo_ta = trim($_POST['mo_ta']);
        $gia_thue = floatval($_POST['gia_thue']);
        $so_luong = intval($_POST['so_luong_ton']);
        $size = trim($_POST['size'] ?? '');
        $hinh_anh_chinh = '';
        
        // Upload ảnh chính
        if (!empty($_FILES['main_image']['name']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $file_name = time() . '_main_' . basename($_FILES['main_image']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $file_path)) {
                $hinh_anh_chinh = $file_path;
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO vay_cuoi (ma_vay, ten_vay, mo_ta, gia_thue, so_luong_ton, size, hinh_anh_chinh) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdisg", $ma_vay, $ten_vay, $mo_ta, $gia_thue, $so_luong, $size, $hinh_anh_chinh);
        
        if ($stmt->execute()) {
            $vay_id = $conn->insert_id;
            
            // Upload ảnh phụ vào bảng hinh_anh_vay_cuoi
            if (!empty($_FILES['gallery_images']['name'][0])) {
                foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_name = time() . '_' . $key . '_' . basename($_FILES['gallery_images']['name'][$key]);
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($tmp_name, $file_path)) {
                            $img_stmt = $conn->prepare("INSERT INTO hinh_anh_vay_cuoi (vay_id, url, is_primary, sort_order) VALUES (?, ?, 0, ?)");
                            $img_stmt->bind_param("isi", $vay_id, $file_path, $key);
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
        $size = trim($_POST['size'] ?? '');
        
        // Upload ảnh chính mới nếu có
        if (!empty($_FILES['main_image']['name']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            // Xóa ảnh chính cũ
            $old_img = $conn->query("SELECT hinh_anh_chinh FROM vay_cuoi WHERE id = $id")->fetch_assoc();
            if (!empty($old_img['hinh_anh_chinh']) && file_exists($old_img['hinh_anh_chinh'])) {
                unlink($old_img['hinh_anh_chinh']);
            }
            
            $file_name = time() . '_main_' . basename($_FILES['main_image']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare("UPDATE vay_cuoi SET ma_vay=?, ten_vay=?, mo_ta=?, gia_thue=?, so_luong_ton=?, size=?, hinh_anh_chinh=? WHERE id=?");
                $stmt->bind_param("sssdissi", $ma_vay, $ten_vay, $mo_ta, $gia_thue, $so_luong, $size, $file_path, $id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE vay_cuoi SET ma_vay=?, ten_vay=?, mo_ta=?, gia_thue=?, so_luong_ton=?, size=? WHERE id=?");
            $stmt->bind_param("sssdisi", $ma_vay, $ten_vay, $mo_ta, $gia_thue, $so_luong, $size, $id);
        }
        $stmt->execute();
        
        // Upload ảnh phụ mới nếu có
        if (!empty($_FILES['gallery_images']['name'][0])) {
            $max_order = $conn->query("SELECT MAX(sort_order) as max_order FROM hinh_anh_vay_cuoi WHERE vay_id = $id")->fetch_assoc()['max_order'] ?? 0;
            
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = time() . '_' . $key . '_' . basename($_FILES['gallery_images']['name'][$key]);
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $sort = $max_order + $key + 1;
                        $img_stmt = $conn->prepare("INSERT INTO hinh_anh_vay_cuoi (vay_id, url, is_primary, sort_order) VALUES (?, ?, 0, ?)");
                        $img_stmt->bind_param("isi", $id, $file_path, $sort);
                        $img_stmt->execute();
                    }
                }
            }
        }
        $_SESSION['admin_success'] = 'Cập nhật váy cưới thành công!';
    }
    
    if ($action === 'delete') {
        $id = intval($_POST['id']);
        // Xóa ảnh chính
        $dress = $conn->query("SELECT hinh_anh_chinh FROM vay_cuoi WHERE id = $id")->fetch_assoc();
        if (!empty($dress['hinh_anh_chinh']) && file_exists($dress['hinh_anh_chinh'])) {
            unlink($dress['hinh_anh_chinh']);
        }
        // Xóa ảnh phụ
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
    
    if ($action === 'delete_main_image') {
        $vay_id = intval($_POST['vay_id']);
        $dress = $conn->query("SELECT hinh_anh_chinh FROM vay_cuoi WHERE id = $vay_id")->fetch_assoc();
        if (!empty($dress['hinh_anh_chinh']) && file_exists($dress['hinh_anh_chinh'])) {
            unlink($dress['hinh_anh_chinh']);
        }
        $conn->query("UPDATE vay_cuoi SET hinh_anh_chinh = NULL WHERE id = $vay_id");
        $_SESSION['admin_success'] = 'Đã xóa ảnh chính!';
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

$sql = "SELECT v.*, 
        COALESCE(v.hinh_anh_chinh, (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id LIMIT 1)) as image,
        (SELECT COUNT(*) FROM hinh_anh_vay_cuoi WHERE vay_id = v.id) as gallery_count
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
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <div class="text-sm text-navy-500">Tổng: <span class="font-bold text-navy-900"><?php echo $total; ?></span> mẫu váy</div>
    <button onclick="openModal('add')" class="bg-accent-500 text-white px-4 py-2.5 rounded-lg hover:bg-accent-600 transition w-full sm:w-auto flex items-center justify-center">
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
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm mã váy, tên váy..." class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 focus:border-transparent text-base">
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-6 py-2.5 hover:bg-accent-600 transition flex items-center justify-center">
            <i class="fas fa-search mr-2"></i>Tìm
        </button>
    </form>
</div>

<!-- Bảng váy cưới -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <!-- Mobile: Hiển thị dạng card -->
    <div class="dress-mobile-view block md:hidden">
        <?php foreach ($dresses as $dress): ?>
        <div class="dress-card p-4 border-b border-gray-100 last:border-b-0">
            <div class="flex items-start gap-3 mb-3">
                <?php if ($dress['image']): ?>
                    <img src="<?php echo htmlspecialchars($dress['image']); ?>" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                <?php else: ?>
                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-navy-900 text-base"><?php echo htmlspecialchars($dress['ten_vay']); ?></p>
                    <p class="text-sm text-accent-500 font-medium mt-0.5"><?php echo htmlspecialchars($dress['ma_vay']); ?></p>
                    <p class="text-xs text-navy-500 mt-1 line-clamp-2"><?php echo htmlspecialchars(substr($dress['mo_ta'] ?? '', 0, 60)); ?>...</p>
                </div>
            </div>
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="flex flex-col gap-1">
                    <span class="text-lg font-bold text-green-600"><?php echo number_format($dress['gia_thue']); ?>đ</span>
                    <?php 
                    if (!empty($dress['size'])):
                        $mobile_sizes = [];
                        $decoded_mobile = json_decode($dress['size'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_mobile)) {
                            foreach ($decoded_mobile as $s) {
                                if (!empty($s['active'])) {
                                    $mobile_sizes[] = htmlspecialchars($s['name']);
                                }
                            }
                        } else {
                            $mobile_sizes[] = htmlspecialchars($dress['size']);
                        }
                        if (!empty($mobile_sizes)):
                    ?>
                    <span class="text-xs text-navy-600"><i class="fas fa-ruler-combined mr-1"></i><?php echo implode(', ', $mobile_sizes); ?></span>
                    <?php 
                        endif;
                    endif; 
                    ?>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo $dress['so_luong_ton'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    Tồn: <?php echo $dress['so_luong_ton']; ?>
                </span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <div class="flex flex-wrap gap-1 text-xs">
                    <span class="px-2 py-0.5 rounded <?php echo !empty($dress['hinh_anh_chinh']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                        <?php echo !empty($dress['hinh_anh_chinh']) ? '✓ Ảnh chính' : '✗ Chưa có'; ?>
                    </span>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700"><?php echo $dress['gallery_count']; ?> ảnh</span>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openImageModal(<?php echo $dress['id']; ?>)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg" title="Quản lý ảnh">
                        <i class="fas fa-images"></i>
                    </button>
                    <button onclick='openModal("edit", <?php echo json_encode($dress); ?>)' class="p-2 text-accent-500 hover:bg-accent-50 rounded-lg" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteDress(<?php echo $dress['id']; ?>, '<?php echo htmlspecialchars($dress['ten_vay']); ?>')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($dresses)): ?>
        <div class="p-8 text-center text-navy-500">Không có váy cưới nào</div>
        <?php endif; ?>
    </div>
    
    <!-- Desktop: Hiển thị dạng bảng -->
    <div class="dress-table-view hidden md:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Váy cưới</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Mã váy</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Giá thuê</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-navy-600 uppercase">Size</th>
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
                <td class="px-6 py-4 text-navy-700">
                    <?php 
                    $sizes_display = [];
                    if (!empty($dress['size'])) {
                        $decoded = json_decode($dress['size'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            foreach ($decoded as $s) {
                                if (!empty($s['active'])) {
                                    $sizes_display[] = htmlspecialchars($s['name']);
                                }
                            }
                        } else {
                            // Legacy format
                            $sizes_display[] = htmlspecialchars($dress['size']);
                        }
                    }
                    
                    if (!empty($sizes_display)): 
                    ?>
                        <i class="fas fa-ruler-combined text-navy-400 mr-1"></i><?php echo implode(', ', $sizes_display); ?>
                    <?php else: ?>
                        <span class="text-gray-400 italic">-</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $dress['so_luong_ton'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo $dress['so_luong_ton']; ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <span class="px-2 py-0.5 rounded text-xs font-medium <?php echo !empty($dress['hinh_anh_chinh']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                            <?php echo !empty($dress['hinh_anh_chinh']) ? '✓ Ảnh chính' : '✗ Chưa có ảnh chính'; ?>
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                            <?php echo $dress['gallery_count']; ?> ảnh phụ
                        </span>
                    </div>
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
            <tr><td colspan="7" class="px-6 py-8 text-center text-navy-500">Không có váy cưới nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div><!-- End dress-table-view -->
</div><!-- End wrapper -->

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
<div id="dressModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-auto my-auto max-h-[90vh] overflow-y-auto">
        <div class="p-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg sm:text-xl font-bold text-navy-900">Thêm váy cưới</h3>
                <button type="button" onclick="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="dressForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="dressId">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1">Mã váy</label>
                            <input type="text" name="ma_vay" id="ma_vay" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-navy-700 mb-1">Số lượng tồn</label>
                            <input type="number" name="so_luong_ton" id="so_luong_ton" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Tên váy</label>
                        <input type="text" name="ten_vay" id="ten_vay" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Giá thuê (VNĐ)</label>
                        <input type="number" name="gia_thue" id="gia_thue" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1"><i class="fas fa-ruler-combined mr-1"></i>Kích cỡ (Size)</label>
                        <div class="flex gap-2 mb-2">
                             <button type="button" onclick="addStandardSizes()" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-2 py-1.5 rounded transition border border-blue-200">
                                + Size Chuẩn (S, M, L, XL)
                            </button>
                             <button type="button" onclick="clearSizes()" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1.5 rounded transition border border-red-200">
                                Xóa hết
                            </button>
                        </div>
                        <div id="sizeContainer" class="space-y-2 mb-2 max-h-40 overflow-y-auto p-2 border border-gray-100 rounded-lg bg-gray-50">
                            <!-- Size items will be here -->
                        </div>
                        <button type="button" onclick="addSizeItem()" class="text-sm text-accent-500 hover:text-accent-600 font-medium flex items-center gap-1">
                            <i class="fas fa-plus-circle"></i> Thêm size tùy chỉnh
                        </button>
                        <input type="hidden" name="size" id="sizeInput">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Mô tả</label>
                        <textarea name="mo_ta" id="mo_ta" rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>Ảnh chính
                        </label>
                        <input type="file" name="main_image" id="main_image" accept="image/*" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent-500 text-sm">
                        <p class="text-xs text-navy-500 mt-1">Ảnh đại diện chính của váy cưới</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">
                            <i class="fas fa-images text-blue-500 mr-1"></i>Ảnh phụ (nhiều ảnh)
                        </label>
                        <input type="file" name="gallery_images[]" id="gallery_images" multiple accept="image/*" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent-500 text-sm">
                        <p class="text-xs text-navy-500 mt-1">Hỗ trợ: JPG, PNG, GIF</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="w-full sm:w-auto px-4 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-100 transition text-center">Hủy</button>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-accent-500 text-white rounded-lg hover:bg-accent-600 transition text-center">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Quản lý ảnh -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-auto my-auto max-h-[90vh] overflow-y-auto">
        <div class="p-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg sm:text-xl font-bold text-navy-900">Quản lý hình ảnh</h3>
                <button onclick="closeImageModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Ảnh chính -->
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                <h4 class="font-semibold text-navy-800 mb-3 text-sm sm:text-base"><i class="fas fa-star text-yellow-500 mr-2"></i>Ảnh chính</h4>
                <div id="mainImageContainer" class="mb-3">
                    <!-- Main image loaded via JS -->
                </div>
                <form method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="mainImageVayId">
                    <input type="hidden" name="ma_vay" id="main_ma_vay">
                    <input type="hidden" name="ten_vay" id="main_ten_vay">
                    <input type="hidden" name="mo_ta" id="main_mo_ta">
                    <input type="hidden" name="gia_thue" id="main_gia_thue">
                    <input type="hidden" name="so_luong_ton" id="main_so_luong">
                    <input type="file" name="main_image" accept="image/*" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-sm whitespace-nowrap">
                        <i class="fas fa-upload mr-1"></i>Cập nhật
                    </button>
                </form>
            </div>
            
            <!-- Ảnh phụ -->
            <div class="p-3 sm:p-4 bg-blue-50 rounded-xl border border-blue-200">
                <h4 class="font-semibold text-navy-800 mb-3 text-sm sm:text-base"><i class="fas fa-images text-blue-500 mr-2"></i>Ảnh phụ</h4>
                <div id="imageGallery" class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-3 mb-4">
                    <!-- Gallery images loaded via JS -->
                </div>
                <form method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="imageVayId">
                    <input type="hidden" name="ma_vay" id="img_ma_vay">
                    <input type="hidden" name="ten_vay" id="img_ten_vay">
                    <input type="hidden" name="mo_ta" id="img_mo_ta">
                    <input type="hidden" name="gia_thue" id="img_gia_thue">
                    <input type="hidden" name="so_luong_ton" id="img_so_luong">
                    <input type="file" name="gallery_images[]" multiple accept="image/*" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm whitespace-nowrap">
                        <i class="fas fa-upload mr-1"></i>Thêm ảnh
                    </button>
                </form>
            </div>
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
            renderSizes(data.size);
        } else {
            currentDressData = null;
            document.getElementById('modalTitle').textContent = 'Thêm váy cưới';
            document.getElementById('dressForm').reset();
            renderSizes(''); // Reset sizes
        }
    }

    function renderSizes(sizeData) {
        const container = document.getElementById('sizeContainer');
        container.innerHTML = '';
        
        let sizes = [];
        try {
            sizes = JSON.parse(sizeData);
        } catch (e) {
            // Fallback for old format "S, M, L"
            if (sizeData && typeof sizeData === 'string') {
                sizes = sizeData.split(',').map(s => ({ name: s.trim(), active: true }));
            }
        }
        
        if (!Array.isArray(sizes)) sizes = [];
        
        sizes.forEach(s => addSizeItem(s.name, s.active));
    }

    function addStandardSizes() {
        const standards = ['S', 'M', 'L', 'XL'];
        // Check existing to avoid duplicates
        const existing = Array.from(document.querySelectorAll('.size-name')).map(i => i.value);
        standards.forEach(s => {
            if (!existing.includes(s)) {
                addSizeItem(s, true);
            }
        });
    }

    function clearSizes() {
        if(confirm('Bạn có chắc muốn xóa hết các size?')) {
            document.getElementById('sizeContainer').innerHTML = '';
        }
    }

    function addSizeItem(name = '', active = true) {
        const container = document.getElementById('sizeContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 size-item mb-2';
        div.innerHTML = `
            <input type="text" class="size-name border border-gray-200 rounded px-2 py-1.5 text-sm w-24 focus:ring-2 focus:ring-accent-500" placeholder="Tên size" value="${name}">
            <label class="flex items-center gap-1 text-sm text-gray-600 cursor-pointer select-none bg-white border border-gray-200 rounded px-2 py-1.5 hover:bg-gray-50">
                <input type="checkbox" class="size-active rounded text-accent-500 focus:ring-accent-500" ${active ? 'checked' : ''}>
                <span>Hiện</span>
            </label>
            <button type="button" onclick="this.closest('.size-item').remove()" class="text-red-400 hover:text-red-600 p-1.5 rounded hover:bg-red-50 transition">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    }

    // On submit
    document.getElementById('dressForm').addEventListener('submit', function(e) {
        const items = document.querySelectorAll('.size-item');
        const sizes = [];
        items.forEach(item => {
            const name = item.querySelector('.size-name').value.trim();
            if (name) {
                sizes.push({
                    name: name,
                    active: item.querySelector('.size-active').checked
                });
            }
        });
        document.getElementById('sizeInput').value = JSON.stringify(sizes);
    });
    
    function closeModal() {
        document.getElementById('dressModal').classList.add('hidden');
        document.getElementById('dressModal').classList.remove('flex');
    }
    
    function openImageModal(vayId) {
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
        document.getElementById('imageVayId').value = vayId;
        document.getElementById('mainImageVayId').value = vayId;
        
        // Load images via AJAX
        fetch('api/get-dress-images.php?vay_id=' + vayId)
            .then(r => r.json())
            .then(data => {
                // Set hidden fields for both forms
                const dress = data.dress;
                ['img_', 'main_'].forEach(prefix => {
                    document.getElementById(prefix + 'ma_vay').value = dress.ma_vay;
                    document.getElementById(prefix + 'ten_vay').value = dress.ten_vay;
                    document.getElementById(prefix + 'mo_ta').value = dress.mo_ta || '';
                    document.getElementById(prefix + 'gia_thue').value = dress.gia_thue;
                    document.getElementById(prefix + 'so_luong').value = dress.so_luong_ton;
                });
                
                // Hiển thị ảnh chính
                let mainHtml = '';
                if (dress.hinh_anh_chinh) {
                    mainHtml = `
                        <div class="relative inline-block">
                            <img src="${dress.hinh_anh_chinh}" class="w-40 h-40 object-cover rounded-lg ring-2 ring-yellow-400">
                            <button onclick="deleteMainImage(${vayId})" class="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 shadow" title="Xóa ảnh chính">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    `;
                } else {
                    mainHtml = '<p class="text-gray-500 text-sm italic">Chưa có ảnh chính. Vui lòng upload ảnh đại diện cho sản phẩm.</p>';
                }
                document.getElementById('mainImageContainer').innerHTML = mainHtml;
                
                // Hiển thị ảnh phụ (gallery)
                let galleryHtml = '';
                if (data.images && data.images.length > 0) {
                    data.images.forEach(img => {
                        galleryHtml += `
                            <div class="relative group">
                                <img src="${img.url}" class="w-full h-24 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                                    <button onclick="deleteImage(${img.id})" class="p-2 bg-white rounded-full text-red-500 hover:bg-red-500 hover:text-white" title="Xóa">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    galleryHtml = '<p class="col-span-4 text-center text-gray-500 py-4 text-sm">Chưa có ảnh phụ nào</p>';
                }
                document.getElementById('imageGallery').innerHTML = galleryHtml;
            });
    }
    
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
    }
    
    function deleteImage(imgId) {
        if (confirm('Bạn có chắc muốn xóa hình ảnh này?')) {
            document.getElementById('imageAction').value = 'delete_image';
            document.getElementById('imageId').value = imgId;
            document.getElementById('imageActionForm').submit();
        }
    }
    
    function deleteMainImage(vayId) {
        if (confirm('Bạn có chắc muốn xóa ảnh chính?')) {
            document.getElementById('imageAction').value = 'delete_main_image';
            document.getElementById('actionVayId').value = vayId;
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
