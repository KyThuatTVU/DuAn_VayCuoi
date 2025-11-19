<?php
session_start();
require_once 'includes/config.php';

// Dữ liệu sản phẩm mẫu
$products = [
    1 => [
        'name' => 'Váy Công Chúa Lộng Lẫy',
        'price' => 5500000,
        'rating' => 5,
        'reviews' => 45,
        'category' => 'Váy Công Chúa',
        'code' => 'VCC001',
        'status' => 'Còn hàng',
        'images' => ['images/vay1.jpg', 'images/vay2.jpg', 'images/vay3.jpg'],
        'description' => 'Váy cưới công chúa lộng lẫy với thiết kế váy xòe bồng bềnh, thân váy đính ren và đá lấp lánh. Phù hợp cho cô dâu muốn tỏa sáng như một nàng công chúa trong ngày trọng đại.',
        'features' => [
            'Chất liệu: Lụa cao cấp, ren Pháp',
            'Màu sắc: Trắng ngà',
            'Kiểu dáng: Công chúa xòe',
            'Đính kết: Đá pha lê Swarovski',
            'Độ dài: Kéo đuôi 2m'
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
        'badge' => 'Mới'
    ],
    2 => [
        'name' => 'Váy Đuôi Cá Quyến Rũ',
        'price' => 6200000,
        'rating' => 5,
        'reviews' => 38,
        'category' => 'Váy Đuôi Cá',
        'code' => 'VDC002',
        'status' => 'Còn hàng',
        'images' => ['images/vay2.jpg', 'images/vay1.jpg', 'images/vay4.jpg'],
        'description' => 'Váy cưới đuôi cá quyến rũ ôm sát thân hình, tôn dáng hoàn hảo. Thiết kế hiện đại với đường cắt tinh tế, phù hợp cho cô dâu có vóc dáng chuẩn.',
        'features' => [
            'Chất liệu: Satin cao cấp, ren hoa',
            'Màu sắc: Trắng tinh',
            'Kiểu dáng: Đuôi cá ôm body',
            'Đính kết: Đá pha lê và ngọc trai',
            'Độ dài: Kéo đuôi 1.5m'
        ],
        'sizes' => ['S', 'M', 'L'],
        'badge' => 'Hot'
    ],
    3 => [
        'name' => 'Váy Chữ A Thanh Lịch',
        'price' => 4800000,
        'rating' => 5,
        'reviews' => 32,
        'category' => 'Váy Chữ A',
        'code' => 'VCA003',
        'status' => 'Còn hàng',
        'images' => ['images/vay3.jpg', 'images/vay2.jpg', 'images/vay1.jpg'],
        'description' => 'Váy cưới chữ A thanh lịch với thiết kế đơn giản nhưng tinh tế. Phù hợp với mọi vóc dáng, tạo cảm giác nhẹ nhàng và thoải mái.',
        'features' => [
            'Chất liệu: Voan lụa, ren Ý',
            'Màu sắc: Trắng kem',
            'Kiểu dáng: Chữ A xòe nhẹ',
            'Đính kết: Thêu tay tinh xảo',
            'Độ dài: Kéo đuôi 1m'
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
        'badge' => ''
    ],
    4 => [
        'name' => 'Váy Hiện Đại Tinh Tế',
        'price' => 5000000,
        'rating' => 5,
        'reviews' => 28,
        'category' => 'Váy Hiện Đại',
        'code' => 'VHD004',
        'status' => 'Còn hàng',
        'images' => ['images/vay4.jpg', 'images/vay3.jpg', 'images/vay2.jpg'],
        'description' => 'Váy cưới hiện đại với thiết kế tối giản nhưng sang trọng. Đường nét sắc sảo, phù hợp cho cô dâu yêu thích phong cách hiện đại.',
        'features' => [
            'Chất liệu: Mikado cao cấp',
            'Màu sắc: Trắng ngọc trai',
            'Kiểu dáng: Suông hiện đại',
            'Đính kết: Đơn giản, tinh tế',
            'Độ dài: Dài chấm đất'
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
        'badge' => 'Mới'
    ],
    5 => [
        'name' => 'Váy Ren Cổ Điển',
        'price' => 4500000,
        'rating' => 4,
        'reviews' => 25,
        'category' => 'Váy Cổ Điển',
        'code' => 'VCD005',
        'status' => 'Còn hàng',
        'images' => ['images/vay1.jpg', 'images/vay4.jpg', 'images/vay3.jpg'],
        'description' => 'Váy cưới ren cổ điển với họa tiết ren tinh xảo. Mang đậm phong cách vintage, phù hợp cho đám cưới theo chủ đề cổ điển.',
        'features' => [
            'Chất liệu: Ren Pháp cao cấp',
            'Màu sắc: Trắng ngà vintage',
            'Kiểu dáng: Cổ điển xòe nhẹ',
            'Đính kết: Ren thêu tay',
            'Độ dài: Kéo đuôi 1.2m'
        ],
        'sizes' => ['S', 'M', 'L'],
        'badge' => ''
    ],
    6 => [
        'name' => 'Váy Xòe Lãng Mạn',
        'price' => 5800000,
        'rating' => 5,
        'reviews' => 41,
        'category' => 'Váy Xòe',
        'code' => 'VXL006',
        'status' => 'Còn hàng',
        'images' => ['images/vay2.jpg', 'images/vay1.jpg', 'images/vay4.jpg'],
        'description' => 'Váy cưới xòe lãng mạn với nhiều lớp voan mềm mại. Tạo cảm giác nhẹ nhàng, bay bổng như một nàng tiên.',
        'features' => [
            'Chất liệu: Voan lụa nhiều lớp',
            'Màu sắc: Trắng pastel',
            'Kiểu dáng: Xòe bồng bềnh',
            'Đính kết: Hoa ren 3D',
            'Độ dài: Kéo đuôi 1.8m'
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
        'badge' => 'Hot'
    ],
    7 => [
        'name' => 'Váy Tối Giản Sang Trọng',
        'price' => 4200000,
        'rating' => 5,
        'reviews' => 35,
        'category' => 'Váy Tối Giản',
        'code' => 'VTG007',
        'status' => 'Còn hàng',
        'images' => ['images/vay3.jpg', 'images/vay2.jpg', 'images/vay4.jpg'],
        'description' => 'Váy cưới tối giản sang trọng với thiết kế đơn giản nhưng đầy tinh tế. Phù hợp cho cô dâu yêu thích sự thanh lịch.',
        'features' => [
            'Chất liệu: Satin mềm mại',
            'Màu sắc: Trắng ivory',
            'Kiểu dáng: Suông tối giản',
            'Đính kết: Không đính kết',
            'Độ dài: Dài chấm đất'
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
        'badge' => ''
    ],
    8 => [
        'name' => 'Váy Dạ Hội Cao Cấp',
        'price' => 7500000,
        'rating' => 5,
        'reviews' => 52,
        'category' => 'Váy Dạ Hội',
        'code' => 'VDH008',
        'status' => 'Còn hàng',
        'images' => ['images/vay4.jpg', 'images/vay1.jpg', 'images/vay2.jpg'],
        'description' => 'Váy cưới dạ hội cao cấp với thiết kế xa hoa, lộng lẫy. Đính kết đá quý và pha lê cao cấp, phù hợp cho đám cưới sang trọng.',
        'features' => [
            'Chất liệu: Lụa tơ tằm, ren Pháp',
            'Màu sắc: Trắng champagne',
            'Kiểu dáng: Dạ hội xòe lớn',
            'Đính kết: Đá Swarovski cao cấp',
            'Độ dài: Kéo đuôi 2.5m'
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
        'badge' => 'Mới'
    ]
];

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$product = isset($products[$product_id]) ? $products[$product_id] : $products[1];

$page_title = $product['name'];
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

.product-gallery {
    position: sticky;
    top: 120px;
    height: fit-content;
}

.main-image {
    width: 100%;
    height: 500px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-images {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.thumbnail {
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
}

.thumbnail:hover,
.thumbnail.active {
    border-color: var(--primary-color);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info-section {
    padding: 20px 0;
}

.product-badge-detail {
    display: inline-block;
    background: var(--danger);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 15px;
}

.product-title {
    font-size: 36px;
    color: var(--text-dark);
    margin-bottom: 15px;
    font-weight: 700;
}

.product-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}

.product-rating-detail {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stars-large {
    color: #ffc107;
    font-size: 18px;
}

.product-code {
    color: var(--text-light);
    font-size: 14px;
}

.product-price-detail {
    font-size: 42px;
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 10px;
}

.price-note {
    color: var(--text-light);
    font-size: 15px;
    margin-bottom: 30px;
}

.product-description {
    line-height: 1.8;
    color: var(--text-dark);
    margin-bottom: 30px;
    font-size: 15px;
}

.product-features {
    background: var(--bg-light);
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.product-features h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: var(--text-dark);
}

.product-features ul {
    list-style: none;
}

.product-features li {
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-features li:last-child {
    border-bottom: none;
}

.product-features li::before {
    content: "✓";
    color: var(--primary-color);
    font-weight: bold;
    font-size: 18px;
}

.size-selector {
    margin-bottom: 30px;
}

.size-selector h3 {
    font-size: 16px;
    margin-bottom: 15px;
    color: var(--text-dark);
}

.size-options {
    display: flex;
    gap: 10px;
}

.size-option {
    width: 50px;
    height: 50px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.size-option:hover,
.size-option.selected {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.btn-large {
    flex: 1;
    padding: 16px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.product-status {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #d4edda;
    border-radius: 8px;
    color: #155724;
    font-weight: 600;
}

.related-products {
    margin-top: 60px;
}

@media (max-width: 768px) {
    .product-detail-container {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .product-gallery {
        position: static;
    }
    
    .main-image {
        height: 400px;
    }
}
</style>

<div class="product-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Trang chủ</a> / 
            <a href="products.php">Sản phẩm</a> / 
            <span><?php echo $product['name']; ?></span>
        </div>

        <!-- Product Detail -->
        <div class="product-detail-container">
            <!-- Gallery -->
            <div class="product-gallery">
                <div class="main-image" id="mainImage">
                    <img src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <div class="thumbnail-images">
                    <?php foreach($product['images'] as $index => $image): ?>
                    <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo $image; ?>', this)">
                        <img src="<?php echo $image; ?>" alt="Thumbnail">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <?php if($product['badge']): ?>
                <span class="product-badge-detail"><?php echo $product['badge']; ?></span>
                <?php endif; ?>
                
                <h1 class="product-title"><?php echo $product['name']; ?></h1>
                
                <div class="product-meta">
                    <div class="product-rating-detail">
                        <span class="stars-large">
                            <?php 
                            for($i = 0; $i < $product['rating']; $i++) echo '★';
                            for($i = $product['rating']; $i < 5; $i++) echo '☆';
                            ?>
                        </span>
                        <span>(<?php echo $product['reviews']; ?> đánh giá)</span>
                    </div>
                    <span class="product-code">Mã: <?php echo $product['code']; ?></span>
                </div>

                <div class="product-price-detail"><?php echo number_format($product['price']); ?>đ</div>
                <p class="price-note">* Giá thuê cho 1 ngày (chưa bao gồm phụ kiện)</p>

                <p class="product-description"><?php echo $product['description']; ?></p>

                <div class="product-features">
                    <h3>Thông Số Kỹ Thuật</h3>
                    <ul>
                        <?php foreach($product['features'] as $feature): ?>
                        <li><?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="size-selector">
                    <h3>Chọn Size</h3>
                    <div class="size-options">
                        <?php foreach($product['sizes'] as $size): ?>
                        <div class="size-option" onclick="selectSize(this)"><?php echo $size; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="booking.php?id=<?php echo $product_id; ?>" class="btn btn-primary btn-large">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Đặt Lịch Thử Váy
                    </a>
                    <button class="btn btn-outline btn-large" onclick="addToWishlist()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        Yêu Thích
                    </button>
                </div>

                <div class="product-status">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <?php echo $product['status']; ?>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="related-products">
            <div class="section-header">
                <h2>Sản Phẩm Liên Quan</h2>
                <p>Các mẫu váy tương tự bạn có thể quan tâm</p>
            </div>
            <div class="products-grid">
                <?php 
                $related_ids = [1, 2, 3, 4];
                foreach($related_ids as $id):
                    if($id == $product_id) continue;
                    $related = $products[$id];
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $related['images'][0]; ?>" alt="<?php echo $related['name']; ?>">
                        <?php if($related['badge']): ?>
                        <div class="product-badge"><?php echo $related['badge']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $related['name']; ?></h3>
                        <div class="product-rating">
                            <span class="stars">
                                <?php 
                                for($i = 0; $i < $related['rating']; $i++) echo '★';
                                for($i = $related['rating']; $i < 5; $i++) echo '☆';
                                ?>
                            </span>
                            <span class="reviews">(<?php echo $related['reviews']; ?> đánh giá)</span>
                        </div>
                        <div class="product-price">
                            <span class="price"><?php echo number_format($related['price']); ?>đ</span>
                            <span class="price-label">/ ngày thuê</span>
                        </div>
                        <div class="product-buttons">
                            <a href="product-detail.php?id=<?php echo $id; ?>" class="btn btn-outline">Xem Chi Tiết</a>
                            <a href="booking.php?id=<?php echo $id; ?>" class="btn btn-primary">Đặt Lịch Thử</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
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
