<?php
session_start();
require_once 'includes/config.php';

// Lấy từ khóa tìm kiếm
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = $search_query ? "Tìm kiếm: $search_query" : 'Tìm kiếm';

// Xử lý sắp xếp
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$order_by = "v.created_at DESC";

switch($sort) {
    case 'price_asc':
        $order_by = "v.gia_thue ASC";
        break;
    case 'price_desc':
        $order_by = "v.gia_thue DESC";
        break;
    case 'name':
        $order_by = "v.ten_vay ASC";
        break;
    case 'newest':
    default:
        $order_by = "v.created_at DESC";
        break;
}

$products = [];
$total_products = 0;

if (!empty($search_query)) {
    // Kiểm tra bảng vay_cuoi_size có tồn tại không
    $size_table_exists = false;
    $check_table = $conn->query("SHOW TABLES LIKE 'vay_cuoi_size'");
    if ($check_table && $check_table->num_rows > 0) {
        $size_table_exists = true;
    }

    // Tìm kiếm theo tên váy, mô tả, phong cách (không phân biệt hoa thường)
    $search_param = "%$search_query%";
    
    if ($size_table_exists) {
        $sql = "SELECT v.*, 
                COALESCE(v.hinh_anh_chinh, (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1)) as anh_dai_dien,
                (SELECT COUNT(*) FROM hinh_anh_vay_cuoi WHERE vay_id = v.id) as so_luong_hinh,
                (SELECT GROUP_CONCAT(DISTINCT size ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL') SEPARATOR ',') 
                 FROM vay_cuoi_size WHERE vay_id = v.id) as sizes
                FROM vay_cuoi v 
                WHERE v.so_luong_ton > 0 
                AND (LOWER(v.ten_vay) LIKE LOWER(?) OR LOWER(v.mo_ta) LIKE LOWER(?) OR LOWER(v.phong_cach) LIKE LOWER(?))
                ORDER BY $order_by";
    } else {
        $sql = "SELECT v.*, 
                COALESCE(v.hinh_anh_chinh, (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1)) as anh_dai_dien,
                (SELECT COUNT(*) FROM hinh_anh_vay_cuoi WHERE vay_id = v.id) as so_luong_hinh,
                NULL as sizes
                FROM vay_cuoi v 
                WHERE v.so_luong_ton > 0 
                AND (LOWER(v.ten_vay) LIKE LOWER(?) OR LOWER(v.mo_ta) LIKE LOWER(?) OR LOWER(v.phong_cach) LIKE LOWER(?))
                ORDER BY $order_by";
    }
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = null;
    }
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Detect phong cách nếu chưa có
            if (empty($row['phong_cach'])) {
                $text = strtolower($row['ten_vay'] . ' ' . ($row['mo_ta'] ?? ''));
                if (strpos($text, 'công chúa') !== false || strpos($text, 'princess') !== false) {
                    $row['phong_cach'] = 'công chúa';
                } elseif (strpos($text, 'đuôi cá') !== false || strpos($text, 'mermaid') !== false) {
                    $row['phong_cach'] = 'đuôi cá';
                } elseif (strpos($text, 'chữ a') !== false || strpos($text, 'a-line') !== false) {
                    $row['phong_cach'] = 'chữ a';
                } elseif (strpos($text, 'hiện đại') !== false || strpos($text, 'modern') !== false) {
                    $row['phong_cach'] = 'hiện đại';
                }
            }
            $products[] = $row;
        }
    }
    if ($stmt) {
        $stmt->close();
    }
    $total_products = count($products);
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-slate-50 to-blue-50 py-4 border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center gap-2 text-sm">
            <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">Trang Chủ</a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-700 font-medium">Tìm kiếm</span>
        </div>
    </div>
</div>

<!-- Search Page -->
<section class="py-12 bg-gradient-to-br from-white via-slate-50 to-blue-50">
    <div class="container mx-auto px-4">
        <!-- Search Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">
                <?php if ($search_query): ?>
                    Kết quả tìm kiếm: <span class="text-blue-600">"<?php echo htmlspecialchars($search_query); ?>"</span>
                <?php else: ?>
                    Tìm kiếm váy cưới
                <?php endif; ?>
            </h1>
            <p class="text-lg text-gray-600">
                <?php if ($search_query): ?>
                    Tìm thấy <strong class="text-blue-600"><?php echo $total_products; ?></strong> kết quả
                <?php else: ?>
                    Nhập từ khóa để tìm kiếm váy cưới
                <?php endif; ?>
            </p>
        </div>

        <?php if (!$search_query): ?>
        <!-- Search Form -->
        <div class="max-w-2xl mx-auto mb-12">
            <form action="search.php" method="GET" class="relative">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Nhập tên váy cưới, phong cách..." 
                    class="w-full px-6 py-4 pr-14 rounded-full border-2 border-gray-300 focus:border-blue-500 focus:outline-none text-lg shadow-sm"
                    value="<?php echo htmlspecialchars($search_query); ?>"
                    required
                >
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2.5 rounded-full transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($search_query && $total_products > 0): ?>
        <!-- Filter & Sort -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-gray-700 font-medium">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    Sắp xếp:
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="?q=<?php echo urlencode($search_query); ?>&sort=newest" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all <?php echo $sort == 'newest' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Mới nhất
                    </a>
                    <a href="?q=<?php echo urlencode($search_query); ?>&sort=price_asc" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all <?php echo $sort == 'price_asc' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Giá tăng
                    </a>
                    <a href="?q=<?php echo urlencode($search_query); ?>&sort=price_desc" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all <?php echo $sort == 'price_desc' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Giá giảm
                    </a>
                    <a href="?q=<?php echo urlencode($search_query); ?>&sort=name" 
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all <?php echo $sort == 'name' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Tên A-Z
                    </a>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                    <div class="product-image">
                        <?php if ($product['anh_dai_dien']): ?>
                            <img src="<?php echo htmlspecialchars($product['anh_dai_dien']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['ten_vay']); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($product['so_luong_ton'] < 3): ?>
                            <span class="badge-low-stock">Sắp hết hàng</span>
                        <?php endif; ?>
                        
                        <?php if ($product['so_luong_hinh'] > 1): ?>
                            <span class="badge-images">
                                <i class="fas fa-images"></i> <?php echo $product['so_luong_hinh']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['ten_vay']); ?></h3>
                        
                        <?php if (!empty($product['phong_cach'])): ?>
                            <div class="product-style">
                                <i class="fas fa-tag"></i>
                                <span><?php echo htmlspecialchars($product['phong_cach']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php 
                        $size_display = '';
                        if (!empty($product['size'])) {
                            $decoded = json_decode($product['size'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $active_sizes = [];
                                foreach ($decoded as $s) {
                                    if (!empty($s['active'])) $active_sizes[] = $s['name'];
                                }
                                $size_display = implode(', ', $active_sizes);
                            } else {
                                $size_display = $product['size'];
                            }
                        }
                        
                        if (!empty($size_display)): 
                        ?>
                            <div class="product-style">
                                <i class="fas fa-ruler-combined"></i>
                                <span>Size: <?php echo htmlspecialchars($size_display); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-footer">
                            <div class="product-price">
                                <span class="price-amount"><?php echo number_format($product['gia_thue'], 0, ',', '.'); ?>đ</span>
                                <span class="price-unit">/ngày</span>
                            </div>
                            <button class="product-btn">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif ($search_query && $total_products == 0): ?>
        <!-- No Results -->
        <div class="text-center py-16">
            <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Không tìm thấy kết quả</h3>
            <p class="text-gray-600 mb-8">Không có váy cưới nào phù hợp với từ khóa "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</p>
            
            <!-- Suggestions -->
            <div class="max-w-2xl mx-auto mb-8">
                <p class="text-sm text-gray-600 mb-4">Thử tìm kiếm với các từ khóa phổ biến:</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="search.php?q=váy+công+chúa" class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-full text-sm font-medium hover:border-blue-500 hover:text-blue-600 transition-all shadow-sm hover:shadow-md">
                        <i class="fas fa-search mr-2"></i>váy công chúa
                    </a>
                    <a href="search.php?q=váy+đuôi+cá" class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-full text-sm font-medium hover:border-blue-500 hover:text-blue-600 transition-all shadow-sm hover:shadow-md">
                        <i class="fas fa-search mr-2"></i>váy đuôi cá
                    </a>
                    <a href="search.php?q=váy+hiện+đại" class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-full text-sm font-medium hover:border-blue-500 hover:text-blue-600 transition-all shadow-sm hover:shadow-md">
                        <i class="fas fa-search mr-2"></i>váy hiện đại
                    </a>
                    <a href="search.php?q=váy+chữ+a" class="px-5 py-2.5 bg-white border-2 border-gray-200 rounded-full text-sm font-medium hover:border-blue-500 hover:text-blue-600 transition-all shadow-sm hover:shadow-md">
                        <i class="fas fa-search mr-2"></i>váy chữ a
                    </a>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="search.php" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-search"></i>
                    Tìm kiếm lại
                </a>
                <a href="products.php" class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white rounded-full transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-th"></i>
                    Xem tất cả sản phẩm
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
