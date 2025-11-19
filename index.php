<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>


<!-- Featured Categories -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Phong Cách Váy Cưới</h2>
            <p>Chọn phong cách phù hợp với cá tính của bạn</p>
        </div>
        <div class="categories-grid">
            <div class="category-card">
                <img src="images/vay1.jpg" alt="Váy công chúa">
                <div class="category-overlay">
                    <h3>Váy Công Chúa</h3>
                    <a href="products.php?cat=princess" class="btn-link">Xem Thêm →</a>
                </div>
            </div>
            <div class="category-card">
                <img src="images/vay2.jpg" alt="Váy đuôi cá">
                <div class="category-overlay">
                    <h3>Váy Đuôi Cá</h3>
                    <a href="products.php?cat=mermaid" class="btn-link">Xem Thêm →</a>
                </div>
            </div>
            <div class="category-card">
                <img src="images/vay3.jpg" alt="Váy chữ A">
                <div class="category-overlay">
                    <h3>Váy Chữ A</h3>
                    <a href="products.php?cat=aline" class="btn-link">Xem Thêm →</a>
                </div>
            </div>
            <div class="category-card">
                <img src="images/vay3.jpg" alt="Váy hiện đại">
                <div class="category-overlay">
                    <h3>Váy Hiện Đại</h3>
                    <a href="products.php?cat=modern" class="btn-link">Xem Thêm →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2>Váy Cưới Nổi Bật</h2>
            <p>Những mẫu váy được yêu thích nhất</p>
        </div>
        <div class="products-grid">
            <!-- Sản phẩm 1 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay1.jpg" alt="Váy Công Chúa Lộng Lẫy">
                    <div class="product-badge">Mới</div>
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Công Chúa Lộng Lẫy</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(45 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">5.500.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=1" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=1" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 2 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay2.jpg" alt="Váy Đuôi Cá Quyến Rũ">
                    <div class="product-badge">Hot</div>
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Đuôi Cá Quyến Rũ</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(38 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">6.200.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=2" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=2" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay3.jpg" alt="Váy Chữ A Thanh Lịch">
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Chữ A Thanh Lịch</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(32 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">4.800.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=3" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=3" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 4 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay4.jpg" alt="Váy Hiện Đại Tinh Tế">
                    <div class="product-badge">Mới</div>
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Hiện Đại Tinh Tế</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(28 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">5.000.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=4" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=4" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 5 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay5.jpg" alt="Váy Ren Cổ Điển">
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Ren Cổ Điển</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★☆</span>
                        <span class="reviews">(25 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">4.500.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=5" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=5" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 6 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay6.jpg" alt="Váy Xòe Lãng Mạn">
                    <div class="product-badge">Hot</div>
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Xòe Lãng Mạn</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(41 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">5.800.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=6" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=6" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 7 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay7.jpg" alt="Váy Tối Giản Sang Trọng">
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Tối Giản Sang Trọng</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(35 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">4.200.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=7" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=7" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 8 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="images/vay8.jpg" alt="Váy Dạ Hội Cao Cấp">
                    <div class="product-badge">Mới</div>
                    <div class="product-actions">
                        <button class="btn-icon" title="Yêu thích"><i class="icon-heart"></i></button>
                        <button class="btn-icon" title="Xem nhanh"><i class="icon-eye"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Váy Dạ Hội Cao Cấp</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews">(52 đánh giá)</span>
                    </div>
                    <div class="product-price">
                        <span class="price">7.500.000đ</span>
                        <span class="price-label">/ ngày thuê</span>
                    </div>
                    <div class="product-buttons">
                        <a href="product-detail.php?id=8" class="btn btn-outline">Xem Chi Tiết</a>
                        <a href="booking.php?id=8" class="btn btn-primary">Đặt Lịch Thử</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-40">
            <a href="products.php" class="btn btn-secondary">Xem Tất Cả Váy Cưới</a>
        </div>
    </div>
</section>

<!-- Promotion Banner -->
<section class="promo-banner">
    <div class="container">
        <div class="promo-content">
            <div class="promo-text">
                <span class="promo-label">Ưu Đãi Đặc Biệt</span>
                <h2>Giảm 20% Cho Đơn Hàng Đầu Tiên</h2>
                <p>Sử dụng mã: <strong>FIRSTLOVE</strong> khi thanh toán</p>
                <a href="products.php" class="btn btn-white">Thuê Ngay</a>
            </div>
            <div class="promo-image">
                <img src="images/banner.png" alt="Khuyến mãi">
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section class="services-section">
    <div class="container">
        <div class="services-grid">
            <div class="service-item">
                <div class="service-icon">
                    <i class="icon-dress"></i>
                </div>
                <h3>Thử Váy Miễn Phí</h3>
                <p>Đặt lịch thử váy tại showroom không mất phí</p>
            </div>
            <div class="service-item">
                <div class="service-icon">
                    <i class="icon-tailor"></i>
                </div>
                <h3>May Đo Theo Yêu Cầu</h3>
                <p>Chỉnh sửa váy vừa vặn với số đo của bạn</p>
            </div>
            <div class="service-item">
                <div class="service-icon">
                    <i class="icon-delivery"></i>
                </div>
                <h3>Giao Hàng Tận Nơi</h3>
                <p>Miễn phí giao hàng trong nội thành</p>
            </div>
            <div class="service-item">
                <div class="service-icon">
                    <i class="icon-support"></i>
                </div>
                <h3>Tư Vấn 24/7</h3>
                <p>Đội ngũ chuyên viên sẵn sàng hỗ trợ</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2>Khách Hàng Nói Gì Về Chúng Tôi</h2>
            <p>Những trải nghiệm thực tế từ các cô dâu</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-rating">★★★★★</div>
                <p class="testimonial-text">"Váy cưới tuyệt đẹp, chất lượng cao cấp. Nhân viên tư vấn rất nhiệt tình và chuyên nghiệp. Mình rất hài lòng với dịch vụ!"</p>
                <div class="testimonial-author">
                    <img src="assets/images/customer-1.jpg" alt="Nguyễn Thị An">
                    <div>
                        <h4>Nguyễn Thị An</h4>
                        <span>Cô dâu 2024</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">★★★★★</div>
                <p class="testimonial-text">"Showroom rất đẹp, nhiều mẫu váy đa dạng. Giá cả hợp lý, dịch vụ chỉnh sửa váy rất tốt. Chắc chắn sẽ giới thiệu cho bạn bè!"</p>
                <div class="testimonial-author">
                    <img src="assets/images/customer-2.jpg" alt="Trần Minh Châu">
                    <div>
                        <h4>Trần Minh Châu</h4>
                        <span>Cô dâu 2023</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">★★★★★</div>
                <p class="testimonial-text">"Mình đã thử nhiều nơi nhưng chỉ có ở đây mới tìm được chiếc váy ưng ý. Cảm ơn team đã giúp mình có một đám cưới hoàn hảo!"</p>
                <div class="testimonial-author">
                    <img src="assets/images/customer-3.jpg" alt="Lê Hương Giang">
                    <div>
                        <h4>Lê Hương Giang</h4>
                        <span>Cô dâu 2024</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section">
    <div class="container">
        <div class="section-header">
            <h2>Tin Tức & Cẩm Nang Cưới</h2>
            <p>Cập nhật xu hướng và mẹo hay cho ngày cưới</p>
        </div>
        <div class="blog-grid">
            <article class="blog-card">
                <div class="blog-image">
                    <img src="assets/images/blog-1.jpg" alt="Blog">
                    <span class="blog-date">15 Th11</span>
                </div>
                <div class="blog-content">
                    <span class="blog-category">Xu Hướng</span>
                    <h3>Top 10 Mẫu Váy Cưới Hot Nhất 2024</h3>
                    <p>Khám phá những xu hướng váy cưới được yêu thích nhất trong năm nay...</p>
                    <a href="blog-detail.php?id=1" class="blog-link">Đọc Thêm →</a>
                </div>
            </article>
            <article class="blog-card">
                <div class="blog-image">
                    <img src="assets/images/blog-2.jpg" alt="Blog">
                    <span class="blog-date">10 Th11</span>
                </div>
                <div class="blog-content">
                    <span class="blog-category">Cẩm Nang</span>
                    <h3>Cách Chọn Váy Cưới Phù Hợp Với Dáng Người</h3>
                    <p>Mỗi dáng người sẽ phù hợp với một kiểu váy khác nhau. Cùng tìm hiểu...</p>
                    <a href="blog-detail.php?id=2" class="blog-link">Đọc Thêm →</a>
                </div>
            </article>
            <article class="blog-card">
                <div class="blog-image">
                    <img src="assets/images/blog-3.jpg" alt="Blog">
                    <span class="blog-date">05 Th11</span>
                </div>
                <div class="blog-content">
                    <span class="blog-category">Mẹo Hay</span>
                    <h3>Checklist Chuẩn Bị Váy Cưới Cho Cô Dâu</h3>
                    <p>Những điều cần lưu ý khi thuê và sử dụng váy cưới để có ngày cưới hoàn hảo...</p>
                    <a href="blog-detail.php?id=3" class="blog-link">Đọc Thêm →</a>
                </div>
            </article>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
