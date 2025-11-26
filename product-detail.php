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
        // Lấy ảnh phụ từ bảng hinh_anh_vay_cuoi
        $img_result = $conn->query("SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = $product_id ORDER BY sort_order ASC");
        while ($img = $img_result->fetch_assoc()) {
            $images[] = $img['url'];
        }
        
        // Thêm ảnh chính vào đầu danh sách nếu có
        if (!empty($product['hinh_anh_chinh'])) {
            array_unshift($images, $product['hinh_anh_chinh']);
        }
        
        // Nếu không có ảnh nào, dùng ảnh mặc định
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

// Chuẩn bị dữ liệu hiển thị
$product_data = [
    'id' => $product['id'],
    'name' => $product['ten_vay'],
    'price' => $product['gia_thue'],
    'code' => $product['ma_vay'],
    'status' => $product['so_luong_ton'] > 0 ? 'Còn hàng' : 'Hết hàng',
    'stock' => $product['so_luong_ton'],
    'images' => $images,
    'description' => $product['mo_ta'] ?? 'Váy cưới cao cấp với thiết kế tinh tế, phù hợp cho ngày trọng đại của bạn.',
    'sizes' => ['S', 'M', 'L', 'XL'],
    'rating' => 5,
    'reviews' => rand(20, 50)
];

// Lấy sản phẩm liên quan
$related_products = $conn->query("SELECT v.*, 
    COALESCE(v.hinh_anh_chinh, (SELECT url FROM hinh_anh_vay_cuoi WHERE vay_id = v.id LIMIT 1)) as anh_dai_dien
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

                <div class="size-selector">
                    <h3>Chọn Size</h3>
                    <div class="size-options">
                        <?php foreach($product_data['sizes'] as $size): ?>
                        <div class="size-option" onclick="selectSize(this)"><?php echo $size; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="booking.php?id=<?php echo $product_id; ?>" class="btn-large btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Đặt Lịch Thử Váy
                    </a>
                    <button class="btn-large btn-outline" onclick="addToWishlist()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        Yêu Thích
                    </button>
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
</script>

<?php require_once 'includes/footer.php'; ?>
