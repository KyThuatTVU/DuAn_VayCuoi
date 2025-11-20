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
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">💰 Tổng Chi Phí Thuê</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Tiền thuê váy:</span>
                            <span id="subtotal">0đ</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí dịch vụ (5%):</span>
                            <span id="service-fee">0đ</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>Đặt cọc (30%):</span>
                            <span id="deposit-fee">0đ</span>
                        </div>
                        <div class="border-t pt-4 flex justify-between text-xl font-bold text-gray-800">
                            <span>Tổng thanh toán:</span>
                            <span id="total" class="text-pink-600">0đ</span>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-xl p-4 mb-4 text-sm text-gray-700">
                        <p class="font-semibold mb-2">📋 Lưu ý:</p>
                        <ul class="space-y-1 text-xs">
                            <li>• Thanh toán 30% đặt cọc khi đặt hàng</li>
                            <li>• Thanh toán 70% còn lại khi nhận váy</li>
                            <li>• Hoàn cọc sau khi trả váy nguyên vẹn</li>
                        </ul>
                    </div>
                    
                    <button onclick="checkout()" class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all">
                        <i class="fas fa-check-circle mr-2"></i>
                        Đặt Thuê Váy
                    </button>
                    
                    <a href="products.php" class="block text-center mt-4 text-pink-600 hover:text-pink-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Tiếp tục xem váy
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
                <p class="text-gray-600 mb-6">Bạn chưa chọn váy nào để thuê</p>
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
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex gap-6 items-start">
                <img src="assets/images/dress-${item.vay_id}.jpg" alt="${item.ten_vay}" 
                     onerror="this.src='images/vay1.jpg'"
                     class="w-32 h-32 object-cover rounded-xl flex-shrink-0">
                
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-1">${item.ten_vay}</h3>
                            <p class="text-gray-600 text-sm">Mã: ${item.ma_vay}</p>
                        </div>
                        <button onclick="removeItem(${item.cart_id})" 
                                class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-all"
                                title="Xóa khỏi giỏ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-4 mb-3">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">📅 Ngày thuê:</span>
                                <p class="font-bold text-gray-800">${formatDate(item.ngay_bat_dau_thue)}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">📅 Ngày trả:</span>
                                <p class="font-bold text-gray-800">${formatDate(item.ngay_tra_vay)}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">⏱️ Số ngày:</span>
                                <p class="font-bold text-blue-600">${item.so_ngay_thue} ngày</p>
                            </div>
                            <div>
                                <span class="text-gray-600">💰 Giá/ngày:</span>
                                <p class="font-bold text-blue-600">${formatPrice(item.gia_thue_moi_ngay)}</p>
                            </div>
                        </div>
                        ${item.ghi_chu ? `
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <span class="text-gray-600 text-sm">📝 Ghi chú:</span>
                            <p class="text-gray-800 text-sm mt-1">${item.ghi_chu}</p>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tổng tiền thuê:</span>
                        <span class="text-2xl font-bold text-pink-600">${formatPrice(item.tong_tien_thue)}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    updateSummary(total);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function updateSummary(total) {
    const serviceFee = total * 0.05; // 5% phí dịch vụ
    const depositFee = total * 0.30; // 30% đặt cọc
    const finalTotal = total + serviceFee;
    
    document.getElementById('subtotal').textContent = formatPrice(total);
    document.getElementById('service-fee').textContent = formatPrice(serviceFee);
    document.getElementById('deposit-fee').textContent = formatPrice(depositFee);
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
    // Kiểm tra giỏ hàng có sản phẩm không
    fetch('api/cart.php?action=count')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.count > 0) {
            window.location.href = 'checkout.php';
        } else {
            alert('Giỏ hàng trống. Vui lòng thêm sản phẩm!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.location.href = 'checkout.php';
    });
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
