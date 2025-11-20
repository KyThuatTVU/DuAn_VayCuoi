<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Thanh Toán';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$user_query = $conn->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

// Lấy giỏ hàng
$cart_query = $conn->prepare("SELECT 
    gh.*,
    vc.ten_vay,
    vc.ma_vay,
    vc.gia_thue,
    (vc.gia_thue * gh.so_luong * gh.so_ngay_thue) as tong_tien_thue
FROM gio_hang gh
JOIN vay_cuoi vc ON gh.vay_id = vc.id
WHERE gh.nguoi_dung_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_items = $cart_query->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Tính tổng tiền
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['tong_tien_thue'];
}
$service_fee = $subtotal * 0.05; // 5% phí dịch vụ
$total = $subtotal + $service_fee;

require_once 'includes/header.php';
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">💳 Thanh Toán</h1>
        
        <form id="checkout-form" method="POST" action="api/create-order.php" class="grid lg:grid-cols-3 gap-8">
            <!-- Thông tin giao hàng -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">📋 Thông Tin Nhận Váy</h2>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên *</label>
                            <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại *</label>
                            <input type="tel" name="so_dien_thoai" value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ nhận váy *</label>
                        <textarea name="dia_chi" rows="3" required
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"><?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú (tùy chọn)</label>
                        <textarea name="ghi_chu" rows="3" placeholder="Yêu cầu đặc biệt, thời gian nhận váy..."
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"></textarea>
                    </div>
                </div>
                
                <!-- Chi tiết đơn hàng -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">🛍️ Chi Tiết Đơn Hàng</h2>
                    
                    <div class="space-y-4">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                            <img src="images/vay1.jpg" alt="<?php echo htmlspecialchars($item['ten_vay']); ?>" 
                                 class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($item['ten_vay']); ?></h3>
                                <p class="text-sm text-gray-600">Mã: <?php echo htmlspecialchars($item['ma_vay']); ?></p>
                                <p class="text-sm text-gray-600">
                                    📅 <?php echo date('d/m/Y', strtotime($item['ngay_bat_dau_thue'])); ?> 
                                    → <?php echo date('d/m/Y', strtotime($item['ngay_tra_vay'])); ?>
                                    (<?php echo $item['so_ngay_thue']; ?> ngày)
                                </p>
                                <p class="text-blue-600 font-bold mt-1"><?php echo formatPrice($item['tong_tien_thue']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Tổng đơn hàng -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">💰 Tổng Đơn Hàng</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Tiền thuê váy:</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí dịch vụ (5%):</span>
                            <span><?php echo formatPrice($service_fee); ?></span>
                        </div>
                        <div class="border-t pt-4 flex justify-between text-xl font-bold text-gray-800">
                            <span>Tổng cộng:</span>
                            <span class="text-pink-600"><?php echo formatPrice($total); ?></span>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-xl p-4 mb-6">
                        <h4 class="font-bold text-gray-800 mb-2">💳 Phương thức thanh toán</h4>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="payment_method" value="qr_code" checked class="w-5 h-5 text-blue-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                <span class="font-semibold">Quét mã QR</span>
                            </div>
                        </label>
                        <p class="text-sm text-gray-600 mt-2 ml-8">Thanh toán qua VietQR - Vietcombank</p>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-xl p-4 mb-6 text-sm text-gray-700">
                        <p class="font-semibold mb-2">📋 Lưu ý:</p>
                        <ul class="space-y-1 text-xs">
                            <li>• Mã QR có hiệu lực 10 phút</li>
                            <li>• Thanh toán 30% đặt cọc</li>
                            <li>• 70% còn lại khi nhận váy</li>
                            <li>• Hoàn cọc sau khi trả váy</li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all">
                        <i class="fas fa-qrcode mr-2"></i>
                        Tạo Mã QR Thanh Toán
                    </button>
                    
                    <a href="cart.php" class="block text-center mt-4 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
    
    fetch('api/create-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Kiểm tra response trước
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        
        // Lấy text trước để debug
        return response.text().then(text => {
            console.log('Response text:', text);
            
            // Thử parse JSON
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was:', text);
                throw new Error('Response không phải JSON hợp lệ. Response: ' + text.substring(0, 100));
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        
        if (data.success) {
            // Chuyển đến trang thanh toán QR
            window.location.href = 'payment-qr.php?order_id=' + data.order_id;
        } else {
            alert('Lỗi: ' + data.message);
            if (data.debug) {
                console.error('Debug info:', data.debug);
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-qrcode mr-2"></i>Tạo Mã QR Thanh Toán';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-qrcode mr-2"></i>Tạo Mã QR Thanh Toán';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
