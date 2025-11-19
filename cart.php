<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Giỏ Hàng';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

require_once 'includes/header.php';
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Giỏ Hàng Của Bạn</h1>
        
        <div id="cart-container" class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div id="cart-items" class="space-y-4">
                    <!-- Loading -->
                    <div class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-pink-500"></i>
                        <p class="mt-4 text-gray-600">Đang tải giỏ hàng...</p>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Tổng Đơn Hàng</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Tạm tính:</span>
                            <span id="subtotal">0đ</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí dịch vụ:</span>
                            <span id="service-fee">0đ</span>
                        </div>
                        <div class="border-t pt-4 flex justify-between text-xl font-bold text-gray-800">
                            <span>Tổng cộng:</span>
                            <span id="total" class="text-pink-600">0đ</span>
                        </div>
                    </div>
                    
                    <button onclick="checkout()" class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all">
                        <i class="fas fa-check-circle mr-2"></i>
                        Tiến Hành Đặt Hàng
                    </button>
                    
                    <a href="products.php" class="block text-center mt-4 text-pink-600 hover:text-pink-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Load cart items
function loadCart() {
    fetch('api/cart.php?action=get')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayCart(data.items, data.total);
        } else {
            showError('Không thể tải giỏ hàng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Có lỗi xảy ra');
    });
}

function displayCart(items, total) {
    const container = document.getElementById('cart-items');
    
    if (items.length === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Giỏ hàng trống</h3>
                <p class="text-gray-600 mb-6">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="products.php" class="inline-block bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Khám phá váy cưới
                </a>
            </div>
        `;
        updateSummary(0);
        return;
    }
    
    container.innerHTML = items.map(item => `
        <div class="bg-white rounded-2xl shadow-lg p-6 flex gap-6 items-center hover:shadow-xl transition-shadow">
            <img src="assets/images/dress-${item.vay_id}.jpg" alt="${item.ten_vay}" class="w-32 h-32 object-cover rounded-xl">
            
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800 mb-2">${item.ten_vay}</h3>
                <p class="text-gray-600 text-sm mb-2">Mã: ${item.ma_vay}</p>
                <p class="text-pink-600 font-bold text-lg">${formatPrice(item.gia_thue)}/ngày</p>
                
                <div class="flex gap-4 mt-3 text-sm text-gray-600">
                    <span><i class="fas fa-calendar mr-1"></i> ${item.so_ngay_thue} ngày</span>
                    ${item.ngay_thue ? `<span><i class="fas fa-clock mr-1"></i> ${item.ngay_thue}</span>` : ''}
                </div>
            </div>
            
            <div class="text-center">
                <div class="flex items-center gap-3 mb-3">
                    <button onclick="updateQuantity(${item.cart_id}, ${item.so_luong - 1})" class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 transition-colors">
                        <i class="fas fa-minus text-sm"></i>
                    </button>
                    <span class="text-xl font-bold w-12">${item.so_luong}</span>
                    <button onclick="updateQuantity(${item.cart_id}, ${item.so_luong + 1})" class="w-8 h-8 bg-pink-500 text-white rounded-full hover:bg-pink-600 transition-colors">
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </div>
                <p class="text-xl font-bold text-gray-800 mb-3">${formatPrice(item.tong_tien)}</p>
                <button onclick="removeItem(${item.cart_id})" class="text-red-500 hover:text-red-700 text-sm">
                    <i class="fas fa-trash mr-1"></i> Xóa
                </button>
            </div>
        </div>
    `).join('');
    
    updateSummary(total);
}

function updateSummary(total) {
    const serviceFee = total * 0.05; // 5% phí dịch vụ
    const finalTotal = total + serviceFee;
    
    document.getElementById('subtotal').textContent = formatPrice(total);
    document.getElementById('service-fee').textContent = formatPrice(serviceFee);
    document.getElementById('total').textContent = formatPrice(finalTotal);
}

function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) {
        removeItem(cartId);
        return;
    }
    
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update',
            cart_id: cartId,
            so_luong: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCart();
            updateCartCount();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function removeItem(cartId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'remove',
            cart_id: cartId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCart();
            updateCartCount();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function checkout() {
    window.location.href = 'checkout.php';
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

function showError(message) {
    document.getElementById('cart-items').innerHTML = `
        <div class="bg-red-50 border border-red-200 rounded-2xl p-8 text-center">
            <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
            <p class="text-red-700">${message}</p>
        </div>
    `;
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', loadCart);
</script>

<?php require_once 'includes/footer.php'; ?>
