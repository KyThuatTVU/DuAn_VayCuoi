<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Bộ Sưu Tập Váy Cưới';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Trang Chủ</a> / <span>Váy Cưới</span>
    </div>
</div>

<!-- Products Page -->
<section class="products-page">
    <div class="container">
        <div class="page-layout">
            <!-- Sidebar Filter -->
            <aside class="sidebar">
                <div class="filter-box">
                    <h3>Lọc Theo Giá</h3>
                    <div class="price-range">
                        <input type="range" min="0" max="10000000" step="500000">
                        <div class="price-values">
                            <span>0đ</span>
                            <span>10.000.000đ</span>
                        </div>
                    </div>
                </div>

                <div class="filter-box">
                    <h3>Phong Cách</h3>
                    <label><input type="checkbox"> Váy Công Chúa</label>
                    <label><input type="checkbox"> Váy Đuôi Cá</label>
                    <label><input type="checkbox"> Váy Chữ A</label>
                    <label><input type="checkbox"> Váy Hiện Đại</label>
                </div>

                <div class="filter-box">
                    <h3>Kích Thước</h3>
                    <label><input type="checkbox"> S</label>
                    <label><input type="checkbox"> M</label>
                    <label><input type="checkbox"> L</label>
                    <label><input type="checkbox"> XL</label>
                </div>

                <button class="btn btn-primary btn-block">Áp Dụng Lọc</button>
            </aside>

            <!-- Products Grid -->
            <div class="products-content">
                <div class="products-header">
                    <h1>Bộ Sưu Tập Váy Cưới</h1>
                    <div class="products-toolbar">
                        <span>Hiển thị 1-12 trong 48 sản phẩm</span>
                        <select class="sort-select">
                            <option>Mới nhất</option>
                            <option>Giá thấp đến cao</option>
                            <option>Giá cao đến thấp</option>
                            <option>Phổ biến nhất</option>
                        </select>
                    </div>
                </div>

                <div class="products-grid">
                    <?php for($i = 1; $i <= 12; $i++): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/dress-<?php echo $i; ?>.jpg" alt="Váy cưới">
                            <?php if($i % 3 == 0): ?>
                            <div class="product-badge">Sale</div>
                            <?php endif; ?>
                            <div class="product-actions">
                                <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                                <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>Váy Cưới Cao Cấp <?php echo $i; ?></h3>
                            <div class="product-rating">
                                <span class="stars">★★★★★</span>
                                <span class="reviews">(<?php echo rand(10, 50); ?> đánh giá)</span>
                            </div>
                            <div class="product-price">
                                <span class="price"><?php echo formatPrice(rand(3000000, 6000000)); ?></span>
                                <span class="price-label">/ ngày</span>
                            </div>
                            <div class="product-buttons">
                                <a href="product-detail.php?id=<?php echo $i; ?>" class="btn btn-outline">Chi Tiết</a>
                                <a href="booking.php?id=<?php echo $i; ?>" class="btn btn-primary">Đặt Lịch</a>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <a href="#" class="page-link disabled">«</a>
                    <a href="#" class="page-link active">1</a>
                    <a href="#" class="page-link">2</a>
                    <a href="#" class="page-link">3</a>
                    <a href="#" class="page-link">4</a>
                    <a href="#" class="page-link">»</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.breadcrumb {
    background: var(--bg-light);
    padding: 15px 0;
    font-size: 14px;
}

.breadcrumb a {
    color: var(--primary-color);
}

.page-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    margin-top: 40px;
}

.sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
}

.filter-box {
    background: var(--white);
    padding: 25px;
    border-radius: 8px;
    box-shadow: var(--shadow);
    margin-bottom: 20px;
}

.filter-box h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: var(--text-dark);
}

.filter-box label {
    display: block;
    margin-bottom: 10px;
    cursor: pointer;
}

.filter-box input[type="checkbox"] {
    margin-right: 8px;
}

.price-range input {
    width: 100%;
}

.price-values {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: var(--text-light);
    margin-top: 10px;
}

.btn-block {
    width: 100%;
}

.products-header {
    margin-bottom: 30px;
}

.products-header h1 {
    font-size: 32px;
    margin-bottom: 15px;
}

.products-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
}

.sort-select {
    padding: 8px 15px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    cursor: pointer;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 50px;
}

.page-link {
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--text-dark);
}

.page-link:hover,
.page-link.active {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.page-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}

@media (max-width: 768px) {
    .page-layout {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        position: static;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
