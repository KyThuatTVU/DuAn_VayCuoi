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
                                <button class="btn-add-cart" onclick="addToCart(<?php echo $i; ?>, 'Váy Cưới Cao Cấp <?php echo $i; ?>', <?php echo rand(3000000, 6000000); ?>)">
                                    <i class="fas fa-shopping-cart"></i>
                                    Thêm Giỏ Hàng
                                </button>
                                <a href="product-detail.php?id=<?php echo $i; ?>" class="btn btn-outline">Chi Tiết</a>
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

/* Add to Cart Button */
.btn-add-cart {
    background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    width: 100%;
    margin-bottom: 10px;
}

.btn-add-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(236, 72, 153, 0.3);
}

.btn-add-cart i {
    font-size: 16px;
}

.product-buttons {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.product-buttons .btn {
    width: 100%;
    text-align: center;
}
</style>

<!-- Cart Notification -->
<div id="cart-notification" style="display: none; position: fixed; top: 100px; right: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; animation: slideIn 0.3s ease;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <i class="fas fa-check-circle" style="font-size: 24px;"></i>
        <div>
            <div style="font-weight: bold; font-size: 16px;">Đã thêm vào giỏ hàng!</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 4px;" id="cart-product-name"></div>
        </div>
    </div>
</div>

<script>
// Shopping Cart Functions với Database
function addToCart(productId, productName, price) {
    // Gửi request đến API
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'add',
            vay_id: productId,
            so_luong: 1,
            so_ngay_thue: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng giỏ hàng
            updateCartCount();
            // Hiển thị thông báo
            showCartNotification(data.product_name || productName, 'success');
        } else {
            if (data.require_login) {
                // Yêu cầu đăng nhập
                showLoginModal();
            } else {
                // Hiển thị lỗi
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
        notification.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
    } else {
        notification.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
    }
    
    productNameEl.textContent = message;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.style.display = 'none';
            notification.style.animation = 'slideIn 0.3s ease';
        }, 300);
    }, 3000);
}

function showLoginModal() {
    // Hiển thị modal yêu cầu đăng nhập
    const modal = document.createElement('div');
    modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000;';
    modal.innerHTML = `
        <div style="background: white; padding: 40px; border-radius: 20px; max-width: 400px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <i class="fas fa-lock" style="font-size: 48px; color: #ec4899; margin-bottom: 20px;"></i>
            <h3 style="font-size: 24px; margin-bottom: 15px; color: #1f2937;">Yêu Cầu Đăng Nhập</h3>
            <p style="color: #6b7280; margin-bottom: 30px;">Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="login.php" style="background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Đăng Nhập
                </a>
                <button onclick="this.closest('div').parentElement.remove()" style="background: #e5e7eb; color: #374151; padding: 12px 30px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                    Đóng
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Click outside to close
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
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
