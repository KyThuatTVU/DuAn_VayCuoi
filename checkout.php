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

// Lấy thông tin người dùng (bao gồm địa chỉ chi tiết)
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

// Kiểm tra giới hạn MoMo
$momo_limit_exceeded = $total > 50000000;

// Lấy thông tin địa chỉ đã lưu của user
$user_province = $user['tinh_thanh'] ?? '';
$user_district = $user['quan_huyen'] ?? '';
$user_ward = $user['phuong_xa'] ?? '';
$user_specific_address = $user['dia_chi_cu_the'] ?? '';

require_once 'includes/header.php';
?>

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">💳 Thanh Toán</h1>
        
        <?php if ($momo_limit_exceeded): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Lưu ý:</strong> Tổng đơn hàng vượt quá 50 triệu VNĐ. 
                        Phương thức thanh toán MoMo không khả dụng (giới hạn test: 50 triệu). 
                        Vui lòng chọn phương thức QR Code chuyển khoản.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
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
                    
                    <!-- Địa chỉ Việt Nam -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📍 Địa chỉ nhận váy</h3>
                        
                        <div class="grid md:grid-cols-3 gap-4">
                            <!-- Tỉnh/Thành phố -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tỉnh/Thành phố *</label>
                                <select name="tinh_thanh" id="province-select" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                        <?php echo !empty($user_province) ? 'data-selected="' . htmlspecialchars($user_province) . '"' : ''; ?>>
                                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                </select>
                            </div>
                            
                            <!-- Quận/Huyện -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Quận/Huyện *</label>
                                <select name="quan_huyen" id="district-select" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                        <?php echo !empty($user_district) ? 'data-selected="' . htmlspecialchars($user_district) . '"' : ''; ?>>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                            
                            <!-- Phường/Xã -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phường/Xã *</label>
                                <select name="phuong_xa" id="ward-select" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                        <?php echo !empty($user_ward) ? 'data-selected="' . htmlspecialchars($user_ward) . '"' : ''; ?>>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Địa chỉ cụ thể -->
                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ cụ thể (Số nhà, tên đường...) *</label>
                            <input type="text" name="dia_chi_cu_the" id="specific-address" 
                                   value="<?php echo htmlspecialchars($user_specific_address); ?>" 
                                   placeholder="Ví dụ: 123 Đường Nguyễn Văn A, Tòa nhà B, Tầng 5"
                                   required
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        </div>
                        
                        <!-- Địa chỉ đầy đủ (hidden, sẽ được tự động tạo) -->
                        <input type="hidden" name="dia_chi" id="full-address" value="">
                        
                        <!-- Hiển thị địa chỉ đầy đủ -->
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl" id="address-preview" style="display: none;">
                            <p class="text-sm text-gray-600 mb-1">📍 Địa chỉ đầy đủ:</p>
                            <p class="font-semibold text-gray-800" id="address-preview-text"></p>
                        </div>
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
                                <?php if (!empty($item['ghi_chu']) && strpos($item['ghi_chu'], 'Size:') === 0): ?>
                                <p class="text-sm text-blue-600 font-medium">
                                    👕 Size: <?php echo htmlspecialchars(explode('.', $item['ghi_chu'])[0]); ?>
                                </p>
                                <?php endif; ?>
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
            
            <!-- Mã khuyến mãi -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Mã Khuyến Mãi
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <input type="text" name="coupon_code" id="coupon_code" 
                                   placeholder="Nhập mã khuyến mãi (nếu có)"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200 transition-all">
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Mỗi khách hàng chỉ được sử dụng một mã khuyến mãi một lần
                            </p>
                        </div>
                        <button type="button" id="apply_coupon" 
                                class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Áp Dụng Mã
                        </button>
                        <div id="coupon_message" class="text-sm text-center hidden"></div>
                    </div>
                </div>
            </div>
            
            <!-- Tổng đơn hàng -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">💰 Tổng Đơn Hàng</h3>
                    
                    <div class="space-y-4 mb-6" id="order_summary">
                        <div class="flex justify-between text-gray-600">
                            <span>Tiền thuê váy:</span>
                            <span id="subtotal_display"><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí dịch vụ (5%):</span>
                            <span id="service_fee_display"><?php echo formatPrice($service_fee); ?></span>
                        </div>
                        <div id="discount_row" class="flex justify-between text-green-600 hidden">
                            <span>Giảm giá:</span>
                            <span id="discount_display">-<?php echo formatPrice(0); ?></span>
                        </div>
                        <div class="border-t pt-4 flex justify-between text-xl font-bold text-gray-800">
                            <span>Tổng cộng:</span>
                            <span id="total_display" class="text-pink-600"><?php echo formatPrice($total); ?></span>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Phương thức thanh toán
                        </h4>
                        
                        <!-- MoMo -->
                        <label class="payment-option flex items-center gap-4 p-4 bg-white rounded-xl mb-3 border-2 border-gray-100 hover:border-pink-300 hover:shadow-md transition-all <?php echo $momo_limit_exceeded ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'; ?>" data-method="momo">
                            <input type="radio" name="payment_method" value="momo" <?php echo !$momo_limit_exceeded ? 'checked' : 'disabled'; ?> class="hidden peer">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center shadow-lg shadow-pink-200">
                                <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-2h2v2zm0-4h-2V7h2v5z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">Ví MoMo</span>
                                    <span class="px-2 py-0.5 bg-pink-100 text-pink-600 text-xs font-semibold rounded-full">Phổ biến</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">Thanh toán nhanh qua ví điện tử</p>
                                <?php if ($momo_limit_exceeded): ?>
                                <p class="text-xs text-red-500 mt-1">⚠️ Vượt giới hạn 50 triệu VNĐ</p>
                                <?php endif; ?>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:border-pink-500 peer-checked:bg-pink-500 transition-all">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>
                        
                        <!-- QR Code VietQR -->
                        <label class="payment-option flex items-center gap-4 p-4 bg-white rounded-xl border-2 border-gray-100 hover:border-blue-300 hover:shadow-md cursor-pointer transition-all" data-method="qr_code">
                            <input type="radio" name="payment_method" value="qr_code" <?php echo $momo_limit_exceeded ? 'checked' : ''; ?> class="hidden peer">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-200">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">Quét mã QR (VietQR)</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">Chuyển khoản qua Vietcombank</p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>
                        
                        <!-- COD - Thanh toán khi nhận hàng -->
                        <label class="payment-option flex items-center gap-4 p-4 bg-white rounded-xl mt-3 border-2 border-gray-100 hover:border-green-300 hover:shadow-md cursor-pointer transition-all" data-method="cod">
                            <input type="radio" name="payment_method" value="cod" class="hidden peer">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg shadow-green-200">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">Thanh toán khi nhận hàng</span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-600 text-xs font-semibold rounded-full">COD</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">Thanh toán bằng tiền mặt khi nhận váy</p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:border-green-500 peer-checked:bg-green-500 transition-all">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>
                    </div>
                    
                    <style>
                        .payment-option:has(input:checked) {
                            border-color: transparent;
                            box-shadow: 0 0 0 2px var(--checked-color, #3b82f6);
                        }
                        .payment-option[data-method="momo"]:has(input:checked) {
                            --checked-color: #ec4899;
                            background: linear-gradient(to right, #fdf2f8, #ffffff);
                        }
                        .payment-option[data-method="qr_code"]:has(input:checked) {
                            --checked-color: #3b82f6;
                            background: linear-gradient(to right, #eff6ff, #ffffff);
                        }
                        .payment-option[data-method="cod"]:has(input:checked) {
                            --checked-color: #22c55e;
                            background: linear-gradient(to right, #f0fdf4, #ffffff);
                        }
                        .payment-option:has(input:checked) .w-6.h-6 {
                            border-color: transparent;
                        }
                        .payment-option:has(input:checked) .w-6.h-6 svg {
                            opacity: 1;
                        }
                        .payment-option[data-method="momo"]:has(input:checked) .w-6.h-6 {
                            background-color: #ec4899;
                        }
                        .payment-option[data-method="qr_code"]:has(input:checked) .w-6.h-6 {
                            background-color: #3b82f6;
                        }
                        .payment-option[data-method="cod"]:has(input:checked) .w-6.h-6 {
                            background-color: #22c55e;
                        }
                    </style>
                    
                    <div class="bg-yellow-50 rounded-xl p-4 mb-6 text-sm text-gray-700">
                        <p class="font-semibold mb-2">📋 Lưu ý:</p>
                        <ul class="space-y-1 text-xs">
                            <li>• Mã QR có hiệu lực 10 phút</li>
                            <li>• Thanh toán 30% đặt cọc</li>
                            <li>• 70% còn lại khi nhận váy</li>
                            <li>• Hoàn cọc sau khi trả váy</li>
                        </ul>
                    </div>
                    
                    <button type="submit" id="submit-btn" class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-pink-200 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span id="btn-text">Thanh Toán MoMo</span>
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
// Biến lưu trữ dữ liệu địa chỉ
let provincesData = [];
let districtsData = [];
let wardsData = [];

// Lấy các element
const provinceSelect = document.getElementById('province-select');
const districtSelect = document.getElementById('district-select');
const wardSelect = document.getElementById('ward-select');
const specificAddress = document.getElementById('specific-address');
const fullAddressInput = document.getElementById('full-address');
const addressPreview = document.getElementById('address-preview');
const addressPreviewText = document.getElementById('address-preview-text');

// Load danh sách tỉnh/thành phố
async function loadProvinces() {
    try {
        console.log('Loading provinces...');
        const response = await fetch('api/vietnam-address.php?action=provinces');
        const data = await response.json();
        console.log('Provinces response:', data);
        
        if (data.success) {
            provincesData = data.data;
            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
            
            data.data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                option.dataset.name = province.name;
                provinceSelect.appendChild(option);
            });
            
            // Nếu user đã có tỉnh được lưu, tự động chọn
            const savedProvince = provinceSelect.dataset.selected;
            if (savedProvince) {
                provinceSelect.value = savedProvince;
                await loadDistricts(savedProvince);
            }
        } else {
            console.error('API returned error:', data.message);
        }
    } catch (error) {
        console.error('Error loading provinces:', error);
    }
}

// Load danh sách quận/huyện
async function loadDistricts(provinceCode) {
    try {
        console.log('Loading districts for province:', provinceCode);
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        
        const response = await fetch(`api/vietnam-address.php?action=districts&province_code=${provinceCode}`);
        const data = await response.json();
        console.log('Districts response:', data);
        
        if (data.success) {
            districtsData = data.data;
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            
            if (data.data.length === 0) {
                districtSelect.innerHTML = '<option value="">Không có dữ liệu</option>';
                return;
            }
            
            data.data.forEach(district => {
                const option = document.createElement('option');
                option.value = district.code;
                option.textContent = district.name;
                option.dataset.name = district.name;
                districtSelect.appendChild(option);
            });
            
            // Nếu user đã có huyện được lưu, tự động chọn
            const savedDistrict = districtSelect.dataset.selected;
            if (savedDistrict) {
                districtSelect.value = savedDistrict;
                await loadWards(savedDistrict);
            }
        } else {
            console.error('API returned error:', data.message);
            districtSelect.innerHTML = '<option value="">Lỗi: ' + data.message + '</option>';
        }
    } catch (error) {
        console.error('Error loading districts:', error);
        districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
    }
}

// Load danh sách phường/xã
async function loadWards(districtCode) {
    try {
        console.log('Loading wards for district:', districtCode);
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        
        const response = await fetch(`api/vietnam-address.php?action=wards&district_code=${districtCode}`);
        const data = await response.json();
        console.log('Wards response:', data);
        
        if (data.success) {
            wardsData = data.data;
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            
            if (data.data.length === 0) {
                wardSelect.innerHTML = '<option value="">Không có dữ liệu</option>';
                return;
            }
            
            data.data.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.code;
                option.textContent = ward.name;
                option.dataset.name = ward.name;
                wardSelect.appendChild(option);
            });
            
            // Nếu user đã có xã được lưu, tự động chọn
            const savedWard = wardSelect.dataset.selected;
            if (savedWard) {
                wardSelect.value = savedWard;
                updateFullAddress();
            }
        } else {
            console.error('API returned error:', data.message);
            wardSelect.innerHTML = '<option value="">Lỗi: ' + data.message + '</option>';
        }
    } catch (error) {
        console.error('Error loading wards:', error);
        wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
    }
}

// Cập nhật địa chỉ đầy đủ
function updateFullAddress() {
    const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.dataset?.name || '';
    const districtName = districtSelect.options[districtSelect.selectedIndex]?.dataset?.name || '';
    const wardName = wardSelect.options[wardSelect.selectedIndex]?.dataset?.name || '';
    const specific = specificAddress.value.trim();
    
    let fullAddress = '';
    if (specific) fullAddress += specific;
    if (wardName) fullAddress += (fullAddress ? ', ' : '') + wardName;
    if (districtName) fullAddress += (fullAddress ? ', ' : '') + districtName;
    if (provinceName) fullAddress += (fullAddress ? ', ' : '') + provinceName;
    
    fullAddressInput.value = fullAddress;
    
    // Hiển thị preview
    if (fullAddress) {
        addressPreview.style.display = 'block';
        addressPreviewText.textContent = fullAddress;
    } else {
        addressPreview.style.display = 'none';
    }
}

// Event listeners
provinceSelect.addEventListener('change', async function() {
    const provinceCode = this.value;
    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    
    if (provinceCode) {
        await loadDistricts(provinceCode);
    }
    updateFullAddress();
});

districtSelect.addEventListener('change', async function() {
    const districtCode = this.value;
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    
    if (districtCode) {
        await loadWards(districtCode);
    }
    updateFullAddress();
});

wardSelect.addEventListener('change', function() {
    updateFullAddress();
});

specificAddress.addEventListener('input', function() {
    updateFullAddress();
});

// Khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
    
    // Nếu đã có địa chỉ cụ thể, cập nhật preview
    if (specificAddress.value) {
        setTimeout(updateFullAddress, 1000);
    }
});

// Cập nhật text nút khi thay đổi phương thức thanh toán
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const btnText = document.getElementById('btn-text');
        const submitBtn = document.getElementById('submit-btn');
        
        if (this.value === 'momo') {
            btnText.textContent = 'Thanh Toán MoMo';
            submitBtn.className = 'w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-pink-200 transition-all flex items-center justify-center gap-2';
        } else if (this.value === 'qr_code') {
            btnText.textContent = 'Tạo Mã QR Thanh Toán';
            submitBtn.className = 'w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-blue-200 transition-all flex items-center justify-center gap-2';
        } else if (this.value === 'cod') {
            btnText.textContent = 'Đặt Hàng (COD)';
            submitBtn.className = 'w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-green-200 transition-all flex items-center justify-center gap-2';
        }
    });
});

// Xử lý áp dụng mã khuyến mãi
document.getElementById('apply_coupon').addEventListener('click', function() {
    const couponCode = document.getElementById('coupon_code').value.trim().toUpperCase();
    const messageDiv = document.getElementById('coupon_message');
    const button = this;
    
    if (!couponCode) {
        messageDiv.className = 'text-sm text-center text-red-600';
        messageDiv.textContent = 'Vui lòng nhập mã khuyến mãi';
        messageDiv.classList.remove('hidden');
        return;
    }
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang kiểm tra...';
    
    fetch('api/apply-coupon.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ coupon_code: couponCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật hiển thị tổng tiền
            document.getElementById('discount_row').classList.remove('hidden');
            document.getElementById('discount_display').textContent = '-' + data.discount_formatted;
            document.getElementById('total_display').textContent = data.total_formatted;
            
            messageDiv.className = 'text-sm text-center text-green-600';
            messageDiv.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Áp dụng thành công! Giảm ' + data.discount_formatted + ' cho đơn hàng của bạn.';
            
            // Lưu thông tin coupon để submit
            document.getElementById('checkout-form').insertAdjacentHTML('beforeend', 
                '<input type="hidden" name="applied_coupon" value="' + couponCode + '">' +
                '<input type="hidden" name="discount_amount" value="' + data.discount_amount + '">'
            );
            
            // Disable input và button sau khi áp dụng thành công
            document.getElementById('coupon_code').disabled = true;
            document.getElementById('apply_coupon').disabled = true;
            document.getElementById('apply_coupon').innerHTML = '<i class="fas fa-check mr-2"></i>Đã áp dụng';
            document.getElementById('apply_coupon').classList.remove('bg-pink-600', 'hover:bg-pink-700');
            document.getElementById('apply_coupon').classList.add('bg-green-600', 'cursor-not-allowed');
        } else {
            document.getElementById('discount_row').classList.add('hidden');
            messageDiv.className = 'text-sm text-center text-red-600';
            messageDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>' + data.message;
            
            // Xóa coupon đã áp dụng nếu có
            const existingCoupon = document.querySelector('input[name="applied_coupon"]');
            const existingDiscount = document.querySelector('input[name="discount_amount"]');
            if (existingCoupon) existingCoupon.remove();
            if (existingDiscount) existingDiscount.remove();
        }
        messageDiv.classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.className = 'text-sm text-center text-red-600';
        messageDiv.textContent = 'Có lỗi xảy ra, vui lòng thử lại';
        messageDiv.classList.remove('hidden');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Áp Dụng Mã';
    });
});

document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate địa chỉ
    if (!provinceSelect.value || !districtSelect.value || !wardSelect.value || !specificAddress.value.trim()) {
        alert('Vui lòng điền đầy đủ thông tin địa chỉ');
        return;
    }
    
    // Cập nhật địa chỉ đầy đủ trước khi submit
    updateFullAddress();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const paymentMethod = formData.get('payment_method');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
    
    // Bước 1: Tạo đơn hàng
    fetch('api/create-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Create order response:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Response không hợp lệ: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Không thể tạo đơn hàng');
        }
        
        const orderId = data.order_id;
        
        // Bước 2: Xử lý theo phương thức thanh toán
        if (paymentMethod === 'momo') {
            // Tạo URL thanh toán MoMo
            return fetch('api/momo-create-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => response.json())
            .then(momoData => {
                if (momoData.success && momoData.payUrl) {
                    // Chuyển hướng đến MoMo
                    window.location.href = momoData.payUrl;
                } else {
                    throw new Error(momoData.message || 'Không thể tạo thanh toán MoMo');
                }
            });
        } else if (paymentMethod === 'qr_code') {
            // Chuyển đến trang QR Code
            window.location.href = 'payment-qr.php?order_id=' + orderId;
        } else if (paymentMethod === 'cod') {
            // COD - Chuyển đến trang thành công
            window.location.href = 'order-success.php?order_id=' + orderId + '&method=cod';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra: ' + error.message);
        submitBtn.disabled = false;
        
        const currentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
        if (currentMethod === 'momo') {
            submitBtn.innerHTML = icon + '<span id="btn-text">Thanh Toán MoMo</span>';
        } else if (currentMethod === 'qr_code') {
            submitBtn.innerHTML = icon + '<span id="btn-text">Tạo Mã QR Thanh Toán</span>';
        } else {
            submitBtn.innerHTML = icon + '<span id="btn-text">Đặt Hàng (COD)</span>';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
