<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Bộ Sưu Tập Váy Cưới';

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

// Lấy danh sách váy cưới từ database
$sql = "SELECT v.*, 
        (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id AND is_primary = 1 LIMIT 1) as hinh_anh_chinh,
        (SELECT COUNT(*) FROM hinh_anh_vay_cuoi WHERE vay_id = v.id) as so_luong_hinh
        FROM vay_cuoi v 
        WHERE v.so_luong_ton > 0
        ORDER BY $order_by";

$result = $conn->query($sql);
$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Đếm tổng số sản phẩm
$total_products = count($products);

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
            <span class="text-gray-700 font-medium">Váy Cưới</span>
        </div>
    </div>
</div>

<!-- Products Page -->
<section class="py-12 bg-gradient-to-br from-white via-slate-50 to-blue-50">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-[280px_1fr] gap-8">
            <!-- Sidebar Filter -->
            <aside class="hidden lg:block space-y-6">
                <!-- Price Filter -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lọc Theo Giá
                    </h3>
                    <div class="space-y-3">
                        <input type="range" min="0" max="10000000" step="500000" class="w-full h-2 bg-blue-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <div class="flex justify-between text-sm text-gray-600 font-medium">
                            <span>0đ</span>
                            <span>10.000.000đ</span>
                        </div>
                    </div>
                </div>

                <!-- Style Filter -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Phong Cách
                    </h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-gray-700 group-hover:text-blue-600 transition-colors">Váy Công Chúa</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-gray-700 group-hover:text-blue-600 transition-colors">Váy Đuôi Cá</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-gray-700 group-hover:text-blue-600 transition-colors">Váy Chữ A</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-gray-700 group-hover:text-blue-600 transition-colors">Váy Hiện Đại</span>
                        </label>
                    </div>
                </div>

                <!-- Size Filter -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Kích Thước
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="font-semibold text-gray-700">S</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="font-semibold text-gray-700">M</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="font-semibold text-gray-700">L</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="font-semibold text-gray-700">XL</span>
                        </label>
                    </div>
                </div>

                <button class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-cyan-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Áp Dụng Lọc
                </button>
            </aside>

            <!-- Products Grid -->
            <div class="space-y-8">
                <!-- Header -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-4">
                        Bộ Sưu Tập Váy Cưới
                    </h1>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4 border-t border-gray-200">
                        <span class="text-gray-600 font-medium">
                            Hiển thị <span class="text-blue-600 font-bold"><?php echo min($total_products, 12); ?></span> trong 
                            <span class="text-blue-600 font-bold"><?php echo $total_products; ?></span> sản phẩm
                        </span>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                            </svg>
                            <select class="px-4 py-2 border-2 border-gray-200 rounded-xl font-medium text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all cursor-pointer" onchange="sortProducts(this.value)">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá thấp đến cao</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá cao đến thấp</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if (empty($products)): ?>
                        <div class="col-span-full text-center py-20">
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-700 mb-2">Chưa có sản phẩm</h3>
                            <p class="text-gray-500">Hiện tại chưa có váy cưới nào trong kho. Vui lòng quay lại sau!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($products as $product): ?>
                        <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                            <!-- Product Image -->
                            <div class="relative overflow-hidden aspect-[3/4]">
                                <?php 
                                $image_url = !empty($product['hinh_anh_chinh']) ? $product['hinh_anh_chinh'] : 'images/vay1.jpg';
                                ?>
                                <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                     alt="<?php echo htmlspecialchars($product['ten_vay']); ?>" 
                                     onerror="this.src='images/vay1.jpg'"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                
                                <!-- Badge -->
                                <?php if($product['so_luong_ton'] <= 2): ?>
                                <div class="absolute top-4 right-4 bg-gradient-to-r from-red-500 to-orange-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg animate-pulse">
                                    Sắp hết
                                </div>
                                <?php endif; ?>
                                
                                <!-- Quick Actions -->
                                <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-500 hover:text-white transition-colors" title="Yêu thích">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                    <button onclick="quickView(<?php echo $product['id']; ?>)" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-500 hover:text-white transition-colors" title="Xem nhanh">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-5 space-y-3">
                                <h3 class="text-lg font-bold text-gray-800 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    <?php echo htmlspecialchars($product['ten_vay']); ?>
                                </h3>
                                
                                <div class="text-sm text-gray-500 font-medium">
                                    Mã: <?php echo htmlspecialchars($product['ma_vay']); ?>
                                </div>
                                
                                <?php if(!empty($product['mo_ta'])): ?>
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    <?php echo htmlspecialchars(substr($product['mo_ta'], 0, 100)); ?>...
                                </p>
                                <?php endif; ?>
                                
                                <div class="flex items-center gap-2 text-sm font-semibold <?php echo $product['so_luong_ton'] > 5 ? 'text-emerald-600' : 'text-amber-600'; ?>">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Còn <?php echo $product['so_luong_ton']; ?> sản phẩm
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex items-baseline gap-2 mb-4">
                                        <span class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                            <?php echo formatPrice($product['gia_thue']); ?>
                                        </span>
                                        <span class="text-sm text-gray-500">/ ngày</span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button onclick="showRentalModal(<?php echo $product['id']; ?>, '<?php echo addslashes($product['ten_vay']); ?>', <?php echo $product['gia_thue']; ?>)" 
                                                class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-cyan-700 transition-all transform hover:scale-105 shadow-md flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Thuê
                                        </button>
                                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                                           class="px-4 py-3 border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center items-center gap-2 mt-12">
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-gray-300 text-gray-400 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold shadow-lg">1</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-gray-200 text-gray-700 font-semibold hover:border-blue-500 hover:text-blue-600 transition-colors">2</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-gray-200 text-gray-700 font-semibold hover:border-blue-500 hover:text-blue-600 transition-colors">3</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-gray-200 text-gray-700 font-semibold hover:border-blue-500 hover:text-blue-600 transition-colors">4</button>
                    <button class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-gray-200 text-gray-700 hover:border-blue-500 hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cart Notification -->
<div id="cart-notification" class="hidden fixed top-24 right-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-6 rounded-2xl shadow-2xl z-50 animate-slide-in">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <div class="font-bold text-lg">Đã thêm vào giỏ hàng!</div>
            <div class="text-sm opacity-90 mt-1" id="cart-product-name"></div>
        </div>
    </div>
</div>

<script>
// Format giá tiền
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(price);
}

// Hiển thị modal chọn ngày thuê váy
function showRentalModal(productId, productName, pricePerDay) {
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[10000] backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-3xl p-8 max-w-lg w-full mx-4 shadow-2xl transform animate-scale-in">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">📅 Chọn Ngày Thuê Váy</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            <div class="mb-4 p-4 bg-blue-50 rounded-xl">
                <p class="font-semibold text-gray-800">${productName}</p>
                <p class="text-blue-600 font-bold text-lg mt-1">${formatPrice(pricePerDay)}/ngày</p>
            </div>
            
            <form id="rental-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày bắt đầu thuê *</label>
                    <input type="date" id="start-date" min="${today}" value="${today}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày trả váy *</label>
                    <input type="date" id="end-date" min="${tomorrow}" value="${tomorrow}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                </div>
                
                <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Số ngày thuê:</span>
                        <span id="rental-days" class="text-2xl font-bold text-blue-600">1 ngày</span>
                    </div>
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-blue-200">
                        <span class="text-gray-700 font-medium">Tổng tiền:</span>
                        <span id="total-price" class="text-2xl font-bold text-blue-600">${formatPrice(pricePerDay)}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú (size, yêu cầu đặc biệt...)</label>
                    <textarea id="note" rows="3" placeholder="VD: Size M, cần sửa ngắn váy..."
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"></textarea>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="this.closest('.fixed').remove()" 
                            class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-300 transition-all">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-bold hover:from-blue-700 hover:to-cyan-700 transition-all transform hover:scale-105 shadow-lg">
                        Thêm Vào Giỏ
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tính số ngày và tổng tiền
    const startInput = modal.querySelector('#start-date');
    const endInput = modal.querySelector('#end-date');
    const daysSpan = modal.querySelector('#rental-days');
    const totalSpan = modal.querySelector('#total-price');
    
    function calculateRental() {
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        const total = pricePerDay * days;
        
        daysSpan.textContent = days + ' ngày';
        totalSpan.textContent = formatPrice(total);
        
        // Cập nhật min date cho end date
        const minEnd = new Date(start);
        minEnd.setDate(minEnd.getDate() + 1);
        endInput.min = minEnd.toISOString().split('T')[0];
    }
    
    startInput.addEventListener('change', calculateRental);
    endInput.addEventListener('change', calculateRental);
    
    // Xử lý submit form
    modal.querySelector('#rental-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const startDate = startInput.value;
        const endDate = endInput.value;
        const note = modal.querySelector('#note').value;
        const start = new Date(startDate);
        const end = new Date(endDate);
        const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        
        addToCart(productId, productName, startDate, endDate, days, note);
        modal.remove();
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Thêm váy vào giỏ hàng (cho thuê)
function addToCart(productId, productName, startDate, endDate, days, note) {
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'add',
            vay_id: productId,
            so_luong: 1,
            ngay_bat_dau_thue: startDate,
            ngay_tra_vay: endDate,
            so_ngay_thue: days,
            ghi_chu: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showCartNotification(`Đã thêm "${productName}" vào giỏ (${days} ngày)`, 'success');
        } else {
            if (data.require_login) {
                showLoginModal();
            } else {
                showCartNotification(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCartNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
    });
}

function updateCartCount() {
    // Lấy số lượng từ server
    fetch('api/cart.php?action=count')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = data.count;
                if (data.count > 0) {
                    cartBadge.style.display = 'block';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function showCartNotification(message, type = 'success') {
    const notification = document.getElementById('cart-notification');
    const productNameEl = document.getElementById('cart-product-name');
    
    // Thay đổi màu sắc theo loại thông báo
    if (type === 'error') {
        notification.className = 'fixed top-24 right-6 bg-gradient-to-r from-red-500 to-rose-600 text-white p-6 rounded-2xl shadow-2xl z-50 animate-slide-in';
    } else {
        notification.className = 'fixed top-24 right-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-6 rounded-2xl shadow-2xl z-50 animate-slide-in';
    }
    
    productNameEl.textContent = message;
    notification.classList.remove('hidden');
    
    setTimeout(() => {
        notification.classList.add('hidden');
    }, 3000);
}

function showLoginModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[10000] backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-3xl p-10 max-w-md text-center shadow-2xl transform animate-scale-in">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Yêu Cầu Đăng Nhập</h3>
            <p class="text-gray-600 mb-8">Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng</p>
            <div class="flex gap-3 justify-center">
                <a href="login.php" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-8 py-3 rounded-xl font-bold hover:from-blue-700 hover:to-cyan-700 transition-all transform hover:scale-105 shadow-lg">
                    Đăng Nhập
                </a>
                <button onclick="this.closest('.fixed').remove()" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-300 transition-all">
                    Đóng
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

// Sort products function
function sortProducts(sortBy) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortBy);
    window.location.href = url.toString();
}

// Quick view function
function quickView(productId) {
    // Redirect to product detail page
    window.location.href = 'product-detail.php?id=' + productId;
}
</script>

<style>
@keyframes slide-in {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes scale-in {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>

<?php require_once 'includes/footer.php'; ?>
