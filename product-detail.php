<?php
session_start();
require_once 'includes/config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin váy từ database
$product = null;
$images = [];

if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM vay_cuoi WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product) {
        // Lấy ảnh gallery từ bảng hinh_anh_vay_cuoi
        $img_result = $conn->query("SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = $product_id ORDER BY is_primary DESC, sort_order ASC");
        while ($img = $img_result->fetch_assoc()) {
            $images[] = $img['url'];
        }
        
        // Nếu không có ảnh gallery, dùng ảnh chính từ bảng vay_cuoi
        if (empty($images) && !empty($product['hinh_anh_chinh'])) {
            $images = [$product['hinh_anh_chinh']];
        }
        
        // Nếu vẫn không có ảnh nào, dùng ảnh mặc định
        if (empty($images)) {
            $images = ['images/vay1.jpg'];
        }
    }
}

// Nếu không tìm thấy sản phẩm, redirect về trang products
if (!$product) {
    header('Location: products.php');
    exit();
}

// Lấy số lượng đánh giá (reactions) cho sản phẩm này
$review_count = 0;
$avg_rating = 5; // Mặc định 5 sao
$reaction_result = $conn->query("SELECT COUNT(*) as total FROM cam_xuc_san_pham WHERE vay_id = $product_id");
if ($reaction_result) {
    $review_count = (int)$reaction_result->fetch_assoc()['total'];
}

// Tính rating dựa trên reactions (love = 5, like = 4, wow = 4, haha = 3, sad = 2, angry = 1)
$rating_query = $conn->query("SELECT loai_cam_xuc, COUNT(*) as cnt FROM cam_xuc_san_pham WHERE vay_id = $product_id GROUP BY loai_cam_xuc");
if ($rating_query && $rating_query->num_rows > 0) {
    $rating_map = ['love' => 5, 'like' => 4, 'wow' => 4, 'haha' => 3, 'sad' => 2, 'angry' => 1];
    $total_score = 0;
    $total_count = 0;
    while ($r = $rating_query->fetch_assoc()) {
        $score = $rating_map[$r['loai_cam_xuc']] ?? 3;
        $total_score += $score * $r['cnt'];
        $total_count += $r['cnt'];
    }
    if ($total_count > 0) {
        $avg_rating = round($total_score / $total_count);
    }
}

// Lấy số bình luận
$comment_count = 0;
$comment_result = $conn->query("SELECT COUNT(*) as total FROM binh_luan_san_pham WHERE vay_id = $product_id AND parent_id IS NULL");
if ($comment_result) {
    $comment_count = (int)$comment_result->fetch_assoc()['total'];
}

// Chuẩn bị dữ liệu hiển thị
// Lấy size từ database hoặc tạo mảng mặc định
$sizes_arr = [];
if (!empty($product['size'])) {
    // Nếu có size trong DB (có thể là "S, M, L" hoặc "S" hoặc "85-65-90")
    $sizes_arr = array_map('trim', explode(',', $product['size']));
} else {
    // Mặc định nếu không có
    $sizes_arr = ['S', 'M', 'L', 'XL'];
}

$product_data = [
    'id' => $product['id'],
    'name' => $product['ten_vay'],
    'price' => $product['gia_thue'],
    'code' => $product['ma_vay'],
    'status' => $product['so_luong_ton'] > 0 ? 'Còn hàng' : 'Hết hàng',
    'stock' => $product['so_luong_ton'],
    'images' => $images,
    'description' => $product['mo_ta'] ?? 'Váy cưới cao cấp với thiết kế tinh tế, phù hợp cho ngày trọng đại của bạn.',
    'sizes' => $sizes_arr,
    'size_display' => !empty($product['size']) ? $product['size'] : 'Chưa cập nhật',
    'rating' => $avg_rating,
    'reviews' => $review_count + $comment_count
];

// Lấy sản phẩm liên quan
$related_products = $conn->query("SELECT v.*, 
    (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) as anh_dai_dien
    FROM vay_cuoi v WHERE v.id != $product_id AND v.so_luong_ton > 0 ORDER BY RAND() LIMIT 4")->fetch_all(MYSQLI_ASSOC);

$page_title = $product_data['name'];
require_once 'includes/header.php';
?>

<style>
.product-detail-page {
    padding: 40px 0;
    background: #f8f9fa;
}
.product-detail-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}
.product-gallery { position: sticky; top: 120px; height: fit-content; }
.main-image { width: 100%; height: 500px; border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
.main-image img { width: 100%; height: 100%; object-fit: cover; }
.thumbnail-images { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.thumbnail { height: 100px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 3px solid transparent; transition: all 0.3s; }
.thumbnail:hover, .thumbnail.active { border-color: #3b82f6; }
.thumbnail img { width: 100%; height: 100%; object-fit: cover; }
.product-info-section { padding: 20px 0; }
.product-badge-detail { display: inline-block; background: #ef4444; color: white; padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 15px; }
.product-title { font-size: 32px; color: #1f2937; margin-bottom: 15px; font-weight: 700; }
.product-meta { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; }
.product-rating-detail { display: flex; align-items: center; gap: 8px; }
.stars-large { color: #ffc107; font-size: 18px; }
.product-code { color: #6b7280; font-size: 14px; }
.product-price-detail { font-size: 36px; color: #3b82f6; font-weight: 700; margin-bottom: 10px; }
.price-note { color: #6b7280; font-size: 14px; margin-bottom: 30px; }
.product-description { line-height: 1.8; color: #374151; margin-bottom: 30px; font-size: 15px; }
.size-selector { margin-bottom: 30px; }
.size-selector h3 { font-size: 16px; margin-bottom: 15px; color: #1f2937; }
.size-options { display: flex; gap: 10px; }
.size-option { width: 50px; height: 50px; border: 2px solid #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: 600; transition: all 0.3s; }
.size-option:hover, .size-option.selected { border-color: #3b82f6; background: #3b82f6; color: white; }
.action-buttons { display: flex; gap: 15px; margin-bottom: 30px; }
.btn-large { flex: 1; padding: 16px 30px; font-size: 16px; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; }
.btn-primary { background: #3b82f6; color: white; border: none; }
.btn-primary:hover { background: #2563eb; }
.btn-outline { background: white; color: #3b82f6; border: 2px solid #3b82f6; }
.btn-outline:hover { background: #3b82f6; color: white; }
.product-status { display: flex; align-items: center; gap: 10px; padding: 15px; background: #d1fae5; border-radius: 8px; color: #065f46; font-weight: 600; }
.product-status.out-of-stock { background: #fee2e2; color: #991b1b; }
.related-products { margin-top: 60px; }
.section-header { text-align: center; margin-bottom: 40px; }
.section-header h2 { font-size: 28px; color: #1f2937; margin-bottom: 10px; }
.section-header p { color: #6b7280; }
.products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
.product-card .product-image { height: 250px; overflow: hidden; }
.product-card .product-image img { width: 100%; height: 100%; object-fit: cover; }
.product-card .product-info { padding: 20px; }
.product-card h3 { font-size: 16px; color: #1f2937; margin-bottom: 10px; }
.product-card .product-price { font-size: 20px; color: #3b82f6; font-weight: 700; margin-bottom: 15px; }
.product-card .product-buttons { display: flex; gap: 10px; }
.product-card .btn { flex: 1; padding: 10px; font-size: 13px; text-align: center; border-radius: 6px; text-decoration: none; }
@media (max-width: 768px) {
    .product-detail-container { grid-template-columns: 1fr; padding: 20px; }
    .product-gallery { position: static; }
    .main-image { height: 350px; }
    .products-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Modal animations */
@keyframes scale-in {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>

<div class="product-detail-page">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <!-- Breadcrumb -->
        <div style="margin-bottom: 20px; font-size: 14px; color: #6b7280;">
            <a href="index.php" style="color: #3b82f6; text-decoration: none;">Trang chủ</a> / 
            <a href="products.php" style="color: #3b82f6; text-decoration: none;">Sản phẩm</a> / 
            <span><?php echo htmlspecialchars($product_data['name']); ?></span>
        </div>

        <!-- Product Detail -->
        <div class="product-detail-container">
            <!-- Gallery -->
            <div class="product-gallery">
                <div class="main-image" id="mainImage">
                    <img src="<?php echo htmlspecialchars($product_data['images'][0]); ?>" alt="<?php echo htmlspecialchars($product_data['name']); ?>" onerror="this.src='images/vay1.jpg'">
                </div>
                <div class="thumbnail-images">
                    <?php foreach($product_data['images'] as $index => $image): ?>
                    <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo htmlspecialchars($image); ?>', this)">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Thumbnail" onerror="this.src='images/vay1.jpg'">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <?php if($product_data['stock'] <= 2 && $product_data['stock'] > 0): ?>
                <span class="product-badge-detail">Sắp hết</span>
                <?php endif; ?>
                
                <h1 class="product-title"><?php echo htmlspecialchars($product_data['name']); ?></h1>
                
                <div class="product-meta">
                    <div class="product-rating-detail">
                        <span class="stars-large">
                            <?php 
                            for($i = 0; $i < $product_data['rating']; $i++) echo '★';
                            for($i = $product_data['rating']; $i < 5; $i++) echo '☆';
                            ?>
                        </span>
                        <span>(<?php echo $product_data['reviews']; ?> đánh giá)</span>
                    </div>
                    <span class="product-code">Mã: <?php echo htmlspecialchars($product_data['code']); ?></span>
                </div>

                <div class="product-price-detail"><?php echo number_format($product_data['price']); ?>đ</div>
                <p class="price-note">* Giá thuê cho 1 ngày (chưa bao gồm phụ kiện)</p>

                <p class="product-description"><?php echo nl2br(htmlspecialchars($product_data['description'])); ?></p>

                <?php if (!empty($product_data['size_display']) && $product_data['size_display'] != 'Chưa cập nhật'): ?>
                <div class="mb-4 flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <strong>Kích cỡ:</strong> 
                    <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($product_data['size_display']); ?></span>
                </div>
                <?php endif; ?>

                <div class="size-selector">
                    <h3>Chọn Size</h3>
                    <div class="size-options">
                        <?php foreach($product_data['sizes'] as $size): ?>
                        <div class="size-option" onclick="selectSize(this)"><?php echo $size; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-large btn-primary" onclick="showRentalModal(<?php echo $product_id; ?>, '<?php echo addslashes($product_data['name']); ?>', <?php echo $product_data['price']; ?>)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Thuê Ngay
                    </button>
                    <a href="booking.php?id=<?php echo $product_id; ?>" class="btn-large btn-outline">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Đặt Lịch Thử Váy
                    </a>
                </div>

                <div class="product-status <?php echo $product_data['stock'] <= 0 ? 'out-of-stock' : ''; ?>">
                    <?php if($product_data['stock'] > 0): ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <?php echo $product_data['status']; ?> (Còn <?php echo $product_data['stock']; ?> sản phẩm)
                    <?php else: ?>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Hết hàng
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comments & Reactions Section -->
        <div class="max-w-4xl mx-auto mt-12 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <!-- Reaction Summary -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <div id="reactionSummary" class="flex items-center gap-2">
                        <div id="reactionIcons" class="flex -space-x-1"></div>
                        <span id="reactionTotal" class="text-gray-600 font-medium"></span>
                    </div>
                    <span class="text-gray-500"><span id="totalCommentsCount">0</span> bình luận</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="px-6 py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <!-- Like Button with Reactions -->
                    <div class="relative flex-1 reaction-button-container">
                        <button id="mainReactionBtn" class="reaction-main-btn w-full py-2.5 rounded-lg hover:bg-gray-50 transition-all flex items-center justify-center gap-2 font-medium text-gray-700 text-sm">
                            <span id="mainReactionIcon" class="text-lg">👍</span>
                            <span id="mainReactionText">Thích</span>
                        </button>
                        
                        <!-- Reactions Popup -->
                        <div class="reaction-popup absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-white rounded-full shadow-xl px-3 py-3 hidden opacity-0 transition-all duration-200">
                            <div class="flex items-center gap-2">
                                <button class="reaction-item" data-reaction="like" onclick="selectReaction('like')" title="Thích">
                                    <span class="text-2xl hover:scale-125 transition-transform inline-block">👍</span>
                                </button>
                                <button class="reaction-item" data-reaction="love" onclick="selectReaction('love')" title="Yêu thích">
                                    <span class="text-2xl hover:scale-125 transition-transform inline-block">❤️</span>
                                </button>
                                <button class="reaction-item" data-reaction="wow" onclick="selectReaction('wow')" title="Wow">
                                    <span class="text-2xl hover:scale-125 transition-transform inline-block">😮</span>
                                </button>
                                <button class="reaction-item" data-reaction="haha" onclick="selectReaction('haha')" title="Haha">
                                    <span class="text-2xl hover:scale-125 transition-transform inline-block">😄</span>
                                </button>
                                <button class="reaction-item" data-reaction="sad" onclick="selectReaction('sad')" title="Buồn">
                                    <span class="text-2xl hover:scale-125 transition-transform inline-block">😢</span>
                                </button>
                                <button class="reaction-item" data-reaction="angry" onclick="selectReaction('angry')" title="Phẫn nộ">
                                    <span class="text-2xl hover:scale-125 transition-transform inline-block">😠</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comment Button -->
                    <button class="flex-1 py-2.5 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 font-medium text-gray-700 text-sm" onclick="document.getElementById('commentTextarea')?.focus()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Bình luận</span>
                    </button>
                </div>
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Comment Form -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm overflow-hidden flex-shrink-0">
                        <?php if(!empty($_SESSION['user_avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <textarea id="commentTextarea" 
                                  class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-full focus:outline-none focus:bg-gray-100 resize-none text-sm" 
                                  rows="1"
                                  placeholder="Viết bình luận..."
                                  onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();addComment();}"
                                  oninput="this.style.height='auto';this.style.height=Math.max(36,this.scrollHeight)+'px'"></textarea>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Login Prompt -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-gray-700 mb-3 text-sm">Đăng nhập để bình luận và thả cảm xúc</p>
                    <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                       class="inline-block px-5 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors font-medium text-sm">
                        Đăng nhập
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Comments List -->
            <div class="px-6 py-4">
                <div class="comments-list space-y-4" id="commentsList">
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Đang tải bình luận...
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if(!empty($related_products)): ?>
        <div class="related-products">
            <div class="section-header">
                <h2>Sản Phẩm Liên Quan</h2>
                <p>Các mẫu váy tương tự bạn có thể quan tâm</p>
            </div>
            <div class="products-grid">
                <?php foreach($related_products as $related): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($related['anh_dai_dien'] ?? 'images/vay1.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($related['ten_vay']); ?>"
                             onerror="this.src='images/vay1.jpg'">
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($related['ten_vay']); ?></h3>
                        <div class="product-price"><?php echo number_format($related['gia_thue']); ?>đ/ngày</div>
                        <div class="product-buttons">
                            <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline">Chi Tiết</a>
                            <a href="booking.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">Đặt Lịch</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function changeImage(imageSrc, element) {
    document.querySelector('#mainImage img').src = imageSrc;
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    element.classList.add('active');
}

function selectSize(element) {
    document.querySelectorAll('.size-option').forEach(size => size.classList.remove('selected'));
    element.classList.add('selected');
}

function addToWishlist() {
    alert('Đã thêm vào danh sách yêu thích!');
}

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
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl transform animate-scale-in">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">📅 Chọn Ngày Thuê Váy</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            
            <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                <p class="font-semibold text-gray-800 text-sm">${productName}</p>
                <p class="text-blue-600 font-bold text-base mt-1">${formatPrice(pricePerDay)}/ngày</p>
            </div>
            
            <form id="rental-form" class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Chọn Size *</label>
                    <select id="size-select" required
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm">
                        <option value="">-- Chọn Size --</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ngày bắt đầu thuê *</label>
                    <input type="date" id="start-date" min="${today}" value="${today}" required
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ngày trả váy *</label>
                    <input type="date" id="end-date" min="${tomorrow}" value="${tomorrow}" required
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm">
                </div>
                
                <div class="p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium text-sm">Số ngày thuê:</span>
                        <span id="rental-days" class="text-lg font-bold text-blue-600">1 ngày</span>
                    </div>
                    <div class="flex justify-between items-center mt-1 pt-1 border-t border-blue-200">
                        <span class="text-gray-700 font-medium text-sm">Tổng tiền:</span>
                        <span id="total-price" class="text-lg font-bold text-blue-600">${formatPrice(pricePerDay)}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ghi chú (yêu cầu đặc biệt...)</label>
                    <textarea id="note" rows="2" placeholder="VD: Cần sửa ngắn váy, màu khác..."
                              class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm"></textarea>
                </div>
                
                <div class="flex gap-2 pt-3">
                    <button type="button" onclick="this.closest('.fixed').remove()" 
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition-all text-sm">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-700 transition-all transform hover:scale-105 shadow-lg text-sm">
                        Thêm Vào Giỏ
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Tính toán ngày thuê và tổng tiền
    const startInput = modal.querySelector('#start-date');
    const endInput = modal.querySelector('#end-date');
    const daysSpan = modal.querySelector('#rental-days');
    const totalPriceSpan = modal.querySelector('#total-price');
    
    function calculateRental() {
        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        const totalPrice = days * pricePerDay;
        
        daysSpan.textContent = days + ' ngày';
        totalPriceSpan.textContent = formatPrice(totalPrice);
    }
    
    startInput.addEventListener('change', calculateRental);
    endInput.addEventListener('change', calculateRental);
    
    // Xử lý submit form
    modal.querySelector('#rental-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const size = modal.querySelector('#size-select').value;
        const startDate = startInput.value;
        const endDate = endInput.value;
        const note = modal.querySelector('#note').value;
        const start = new Date(startDate);
        const end = new Date(endDate);
        const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        
        addToCart(productId, productName, startDate, endDate, days, note, size);
        modal.remove();
    });
}

// Thêm vào giỏ hàng
function addToCart(productId, productName, startDate, endDate, days, note, size) {
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
            ghi_chu: `Size: ${size}. ${note}`
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

// Cập nhật số lượng giỏ hàng
function updateCartCount() {
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
    });
}

// Hiển thị thông báo giỏ hàng
function showCartNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-24 right-6 p-6 rounded-2xl shadow-2xl z-50 animate-slide-in ${
        type === 'success' 
            ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white' 
            : 'bg-gradient-to-r from-red-500 to-pink-600 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'
                    }"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-lg">${
                    type === 'success' ? 'Đã thêm vào giỏ hàng!' : 'Có lỗi xảy ra!'
                }</div>
                <div class="text-sm opacity-90 mt-1">${message}</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

// Hiển thị modal đăng nhập
function showLoginModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-[10000] backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform animate-scale-in">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Đăng nhập để tiếp tục</h3>
                <p class="text-gray-600 mb-6">Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng</p>
                <div class="space-y-3">
                    <a href="login.php?redirect=${encodeURIComponent(window.location.href)}" 
                       class="block w-full bg-blue-600 text-white py-3 px-6 rounded-xl font-bold hover:bg-blue-700 transition-all">
                        Đăng nhập
                    </a>
                    <a href="register.php" 
                       class="block w-full bg-gray-200 text-gray-700 py-3 px-6 rounded-xl font-bold hover:bg-gray-300 transition-all">
                        Tạo tài khoản mới
                    </a>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.remove(), 10000); // Tự động đóng sau 10 giây
}
</script>

<?php 
// Set config for comments & reactions
$comments_type = 'product';
$item_id = $product_id;
require_once 'includes/comments-reactions.php';
require_once 'includes/footer.php'; 
?>
