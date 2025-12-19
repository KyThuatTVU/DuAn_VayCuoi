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
    vc.hinh_anh_chinh,
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
$service_fee = $subtotal * 0.05;
$total = $subtotal + $service_fee;
$deposit = $total * 0.30;

// Kiểm tra giới hạn MoMo
$momo_limit_exceeded = $total > 50000000;

// Lấy thông tin địa chỉ đã lưu của user
$user_province = $user['tinh_thanh'] ?? '';
$user_district = $user['quan_huyen'] ?? '';
$user_ward = $user['phuong_xa'] ?? '';
$user_specific_address = $user['dia_chi_cu_the'] ?? '';

require_once 'includes/header.php';
?>

<style>
/* Custom styles cho checkout */
.checkout-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

.checkout-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.checkout-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.input-field {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #fafafa;
}

.input-field:focus {
    border-color: #ec4899;
    background: white;
    box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1);
    outline: none;
}

.select-field {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 20px;
    padding-right: 40px;
}

.order-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
    border-radius: 16px;
    border: 1px solid #fbcfe8;
}

.order-item-image {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}

.payment-method.selected {
    border-color: #ec4899;
    background: linear-gradient(135deg, #fdf2f8 0%, #ffffff 100%);
    box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1);
}

.payment-method.selected .payment-icon {
    transform: scale(1.05);
}

.payment-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.payment-radio {
    width: 22px;
    height: 22px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.payment-method.selected .payment-radio {
    border-color: #ec4899;
    background: #ec4899;
}

.payment-method.selected .payment-radio::after {
    content: '';
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
}

.summary-row.total {
    border-top: 2px dashed #e5e7eb;
    margin-top: 12px;
    padding-top: 16px;
}

.btn-checkout {
    width: 100%;
    padding: 18px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(236, 72, 153, 0.35);
}

.btn-checkout:active {
    transform: translateY(0);
}

.voucher-input-group {
    display: flex;
    gap: 10px;
}

.voucher-input-group input {
    flex: 1;
}

.voucher-input-group button {
    white-space: nowrap;
}

/* Sticky sidebar */
.sticky-sidebar {
    position: sticky;
    top: 100px;
}

/* Progress steps */
.checkout-steps {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 32px;
}

.step {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.step.active {
    background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
    color: white;
}

.step.completed {
    background: #d1fae5;
    color: #059669;
}

.step.pending {
    background: #f3f4f6;
    color: #9ca3af;
}

.step-connector {
    width: 40px;
    height: 2px;
    background: #e5e7eb;
}

@media (max-width: 768px) {
    .checkout-steps {
        flex-wrap: wrap;
    }
    .step-connector {
        display: none;
    }
}
</style>

<section class="checkout-section py-8 md:py-12">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Progress Steps -->
        <div class="checkout-steps">
            <div class="step completed">
                <i class="fas fa-shopping-cart"></i>
                <span class="hidden sm:inline">Giỏ hàng</span>
            </div>
            <div class="step-connector"></div>
            <div class="step active">
                <i class="fas fa-credit-card"></i>
                <span class="hidden sm:inline">Thanh toán</span>
            </div>
            <div class="step-connector"></div>
            <div class="step pending">
                <i class="fas fa-check-circle"></i>
                <span class="hidden sm:inline">Hoàn tất</span>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Xác Nhận Đơn Hàng</h1>
            <p class="text-gray-500">Kiểm tra thông tin và hoàn tất thanh toán</p>
        </div>
        
        <?php if ($momo_limit_exceeded): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <div>
                <p class="font-semibold text-amber-800">Lưu ý về thanh toán</p>
                <p class="text-sm text-amber-700">Đơn hàng vượt 50 triệu VNĐ. MoMo không khả dụng, vui lòng chọn QR Code hoặc COD.</p>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="checkout-form" method="POST" action="api/create-order.php">
            <div class="grid lg:grid-cols-5 gap-6 lg:gap-8">
                
                <!-- Left Column - 3/5 -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Thông tin nhận hàng -->
                    <div class="checkout-card p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Thông Tin Nhận Váy</h2>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên *</label>
                                <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required
                                       class="input-field" placeholder="Nhập họ tên">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại *</label>
                                <input type="tel" name="so_dien_thoai" value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>" required
                                       class="input-field" placeholder="0901 234 567">
                            </div>
                        </div>
                        
                        <!-- Địa chỉ -->
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-pink-500"></i>
                                Địa chỉ giao hàng
                            </h3>
                            
                            <div class="grid md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Tỉnh/Thành phố *</label>
                                    <select name="tinh_thanh" id="province-select" required
                                            class="input-field select-field"
                                            <?php echo !empty($user_province) ? 'data-selected="' . htmlspecialchars($user_province) . '"' : ''; ?>>
                                        <option value="">Chọn tỉnh/thành</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Quận/Huyện *</label>
                                    <select name="quan_huyen" id="district-select" required
                                            class="input-field select-field"
                                            <?php echo !empty($user_district) ? 'data-selected="' . htmlspecialchars($user_district) . '"' : ''; ?>>
                                        <option value="">Chọn quận/huyện</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Phường/Xã *</label>
                                    <select name="phuong_xa" id="ward-select" required
                                            class="input-field select-field"
                                            <?php echo !empty($user_ward) ? 'data-selected="' . htmlspecialchars($user_ward) . '"' : ''; ?>>
                                        <option value="">Chọn phường/xã</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Địa chỉ cụ thể *</label>
                                <input type="text" name="dia_chi_cu_the" id="specific-address" 
                                       value="<?php echo htmlspecialchars($user_specific_address); ?>" 
                                       placeholder="Số nhà, tên đường, tòa nhà..."
                                       required class="input-field">
                            </div>
                            
                            <input type="hidden" name="dia_chi" id="full-address" value="">
                            
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hidden" id="address-preview">
                                <p class="text-xs text-gray-500 mb-1">📍 Địa chỉ đầy đủ:</p>
                                <p class="font-medium text-gray-800 text-sm" id="address-preview-text"></p>
                            </div>
                        </div>
                        
                        <!-- Ghi chú -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-600 mb-2">
                                <i class="fas fa-sticky-note text-gray-400 mr-1"></i>
                                Ghi chú đơn hàng
                            </label>
                            <textarea name="ghi_chu" rows="2" placeholder="Yêu cầu đặc biệt, thời gian nhận váy..."
                                      class="input-field resize-none"></textarea>
                        </div>
                    </div>
                    
                    <!-- Chi tiết đơn hàng -->
                    <div class="checkout-card p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-shopping-bag text-white"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Sản Phẩm Thuê</h2>
                            </div>
                            <span class="text-sm text-gray-500"><?php echo count($cart_items); ?> sản phẩm</span>
                        </div>
                        
                        <div class="space-y-4">
                            <?php foreach ($cart_items as $item): 
                                $size_display = '';
                                if (!empty($item['ghi_chu']) && preg_match('/Size:\s*([A-Z0-9]+)/i', $item['ghi_chu'], $matches)) {
                                    $size_display = $matches[1];
                                }
                                $image = !empty($item['hinh_anh_chinh']) ? $item['hinh_anh_chinh'] : 'images/vay1.jpg';
                            ?>
                            <div class="order-item">
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     alt="<?php echo htmlspecialchars($item['ten_vay']); ?>" 
                                     class="order-item-image"
                                     onerror="this.src='images/vay1.jpg'">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 truncate"><?php echo htmlspecialchars($item['ten_vay']); ?></h3>
                                    <div class="flex flex-wrap gap-2 mt-1 text-xs">
                                        <span class="px-2 py-1 bg-white rounded-full text-gray-600">
                                            <i class="fas fa-barcode mr-1"></i><?php echo htmlspecialchars($item['ma_vay']); ?>
                                        </span>
                                        <?php if (!empty($size_display)): ?>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">
                                            Size <?php echo htmlspecialchars($size_display); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                                        <i class="far fa-calendar-alt text-pink-500"></i>
                                        <span><?php echo date('d/m', strtotime($item['ngay_bat_dau_thue'])); ?></span>
                                        <i class="fas fa-arrow-right text-xs text-gray-400"></i>
                                        <span><?php echo date('d/m/Y', strtotime($item['ngay_tra_vay'])); ?></span>
                                        <span class="text-pink-600 font-medium">(<?php echo $item['so_ngay_thue']; ?> ngày)</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-pink-600"><?php echo formatPrice($item['tong_tien_thue']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo formatPrice($item['gia_thue']); ?>/ngày</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - 2/5 -->
                <div class="lg:col-span-2">
                    <div class="sticky-sidebar space-y-6">
                        
                        <!-- Voucher -->
                        <div class="checkout-card p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-ticket-alt text-pink-500"></i>
                                <h3 class="font-bold text-gray-800">Mã Giảm Giá</h3>
                            </div>
                            
                            <button type="button" id="select_voucher_btn" 
                                    class="w-full bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 text-white font-semibold py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 mb-3">
                                <i class="fas fa-tags"></i>
                                Chọn Voucher
                            </button>
                            
                            <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                                <div class="flex-1 h-px bg-gray-200"></div>
                                <span>hoặc nhập mã</span>
                                <div class="flex-1 h-px bg-gray-200"></div>
                            </div>
                            
                            <div class="voucher-input-group">
                                <input type="text" name="coupon_code" id="coupon_code" 
                                       placeholder="Nhập mã..."
                                       class="input-field uppercase text-sm">
                                <button type="button" id="apply_coupon" 
                                        class="px-4 py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-xl font-semibold text-sm transition-all">
                                    Áp dụng
                                </button>
                            </div>
                            <div id="coupon_message" class="text-sm text-center mt-2 hidden"></div>
                        </div>
                        
                        <!-- Phương thức thanh toán -->
                        <div class="checkout-card p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-wallet text-blue-500"></i>
                                <h3 class="font-bold text-gray-800">Thanh Toán</h3>
                            </div>
                            
                            <div class="space-y-3">
                                <!-- MoMo -->
                                <label class="payment-method <?php echo !$momo_limit_exceeded ? 'selected' : 'opacity-50'; ?>" data-method="momo">
                                    <input type="radio" name="payment_method" value="momo" <?php echo !$momo_limit_exceeded ? 'checked' : 'disabled'; ?> class="hidden">
                                    <div class="payment-icon bg-[#A50064] p-2">
                                        <svg viewBox="0 0 50 50" class="w-full h-full">
                                            <circle cx="15" cy="25" r="10" fill="none" stroke="#fff" stroke-width="3"/>
                                            <circle cx="15" cy="25" r="3" fill="#fff"/>
                                            <circle cx="35" cy="25" r="10" fill="none" stroke="#fff" stroke-width="3"/>
                                            <circle cx="35" cy="25" r="3" fill="#fff"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-800">Ví MoMo</span>
                                            <?php if (!$momo_limit_exceeded): ?>
                                            <span class="px-2 py-0.5 bg-pink-100 text-pink-600 text-xs font-semibold rounded-full">Phổ biến</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-gray-500">Thanh toán qua ví điện tử</p>
                                    </div>
                                    <div class="payment-radio"></div>
                                </label>
                                
                                <!-- QR Code / VNPay -->
                                <label class="payment-method <?php echo $momo_limit_exceeded ? 'selected' : ''; ?>" data-method="qr_code">
                                    <input type="radio" name="payment_method" value="qr_code" <?php echo $momo_limit_exceeded ? 'checked' : ''; ?> class="hidden">
                                    <div class="payment-icon bg-white border-2 border-gray-200 p-1">
                                        <svg viewBox="0 0 120 50" class="w-full h-full">
                                            <text x="5" y="35" font-family="Arial, sans-serif" font-weight="bold" font-size="28" fill="#004A9C">VN</text>
                                            <text x="55" y="35" font-family="Arial, sans-serif" font-weight="bold" font-size="28" fill="#E31837">PAY</text>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-bold text-gray-800">Quét mã QR</span>
                                        <p class="text-xs text-gray-500">Chuyển khoản ngân hàng</p>
                                    </div>
                                    <div class="payment-radio"></div>
                                </label>
                                
                                <!-- COD -->
                                <label class="payment-method" data-method="cod">
                                    <input type="radio" name="payment_method" value="cod" class="hidden">
                                    <div class="payment-icon bg-gradient-to-br from-green-500 to-green-600">
                                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-800">Thanh toán khi nhận</span>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-600 text-xs font-semibold rounded-full">COD</span>
                                        </div>
                                        <p class="text-xs text-gray-500">Trả tiền mặt khi nhận váy</p>
                                    </div>
                                    <div class="payment-radio"></div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Tổng tiền -->
                        <div class="checkout-card p-5">
                            <h3 class="font-bold text-gray-800 mb-4">Chi Tiết Thanh Toán</h3>
                            
                            <div class="space-y-1">
                                <div class="summary-row">
                                    <span class="text-gray-600">Tiền thuê váy</span>
                                    <span class="font-medium" id="subtotal_display"><?php echo formatPrice($subtotal); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="text-gray-600">Phí dịch vụ (5%)</span>
                                    <span class="font-medium" id="service_fee_display"><?php echo formatPrice($service_fee); ?></span>
                                </div>
                                <div class="summary-row hidden" id="discount_row">
                                    <span class="text-green-600">Giảm giá</span>
                                    <span class="font-medium text-green-600" id="discount_display">-0đ</span>
                                </div>
                                <div class="summary-row total">
                                    <span class="text-lg font-bold text-gray-800">Tổng cộng</span>
                                    <span class="text-2xl font-bold text-pink-600" id="total_display"><?php echo formatPrice($total); ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-amber-50 rounded-xl">
                                <div class="flex items-center gap-2 text-amber-700 text-sm">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Đặt cọc 30%: <strong><?php echo formatPrice($deposit); ?></strong></span>
                                </div>
                            </div>
                            
                            <button type="submit" id="submit-btn" 
                                    class="btn-checkout bg-gradient-to-r from-pink-500 to-pink-600 text-white mt-5">
                                <i class="fas fa-lock"></i>
                                <span id="btn-text">Thanh Toán Ngay</span>
                            </button>
                            
                            <a href="cart.php" class="block text-center mt-3 text-sm text-gray-500 hover:text-pink-600 transition-colors">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Quay lại giỏ hàng
                            </a>
                        </div>
                        
                        <!-- Trust badges -->
                        <div class="flex items-center justify-center gap-4 text-xs text-gray-400">
                            <span><i class="fas fa-shield-alt mr-1"></i>Bảo mật SSL</span>
                            <span><i class="fas fa-undo mr-1"></i>Hoàn tiền</span>
                            <span><i class="fas fa-headset mr-1"></i>Hỗ trợ 24/7</span>
                        </div>
                    </div>
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
        const response = await fetch('api/vietnam-address.php?action=provinces');
        const data = await response.json();
        if (data.success) {
            provincesData = data.data;
            provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành</option>';
            data.data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                option.dataset.name = province.name;
                provinceSelect.appendChild(option);
            });
            const savedProvince = provinceSelect.dataset.selected;
            if (savedProvince) {
                provinceSelect.value = savedProvince;
                await loadDistricts(savedProvince);
            }
        }
    } catch (error) {
        console.error('Error loading provinces:', error);
    }
}

async function loadDistricts(provinceCode) {
    try {
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        const response = await fetch(`api/vietnam-address.php?action=districts&province_code=${provinceCode}`);
        const data = await response.json();
        if (data.success) {
            districtsData = data.data;
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            data.data.forEach(district => {
                const option = document.createElement('option');
                option.value = district.code;
                option.textContent = district.name;
                option.dataset.name = district.name;
                districtSelect.appendChild(option);
            });
            const savedDistrict = districtSelect.dataset.selected;
            if (savedDistrict) {
                districtSelect.value = savedDistrict;
                await loadWards(savedDistrict);
            }
        }
    } catch (error) {
        console.error('Error loading districts:', error);
    }
}

async function loadWards(districtCode) {
    try {
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        const response = await fetch(`api/vietnam-address.php?action=wards&district_code=${districtCode}`);
        const data = await response.json();
        if (data.success) {
            wardsData = data.data;
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            data.data.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.code;
                option.textContent = ward.name;
                option.dataset.name = ward.name;
                wardSelect.appendChild(option);
            });
            const savedWard = wardSelect.dataset.selected;
            if (savedWard) {
                wardSelect.value = savedWard;
                updateFullAddress();
            }
        }
    } catch (error) {
        console.error('Error loading wards:', error);
    }
}

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
    
    if (fullAddress) {
        addressPreview.classList.remove('hidden');
        addressPreviewText.textContent = fullAddress;
    } else {
        addressPreview.classList.add('hidden');
    }
}

provinceSelect.addEventListener('change', async function() {
    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
    if (this.value) await loadDistricts(this.value);
    updateFullAddress();
});

districtSelect.addEventListener('change', async function() {
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
    if (this.value) await loadWards(this.value);
    updateFullAddress();
});

wardSelect.addEventListener('change', updateFullAddress);
specificAddress.addEventListener('input', updateFullAddress);

document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
    if (specificAddress.value) setTimeout(updateFullAddress, 1000);
});

// Payment method selection
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        if (this.querySelector('input').disabled) return;
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input').checked = true;
        
        const btnText = document.getElementById('btn-text');
        const submitBtn = document.getElementById('submit-btn');
        const value = this.querySelector('input').value;
        
        submitBtn.className = 'btn-checkout text-white mt-5';
        if (value === 'momo') {
            btnText.textContent = 'Thanh Toán MoMo';
            submitBtn.classList.add('bg-gradient-to-r', 'from-pink-500', 'to-pink-600');
        } else if (value === 'qr_code') {
            btnText.textContent = 'Tạo Mã QR';
            submitBtn.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-blue-600');
        } else {
            btnText.textContent = 'Đặt Hàng (COD)';
            submitBtn.classList.add('bg-gradient-to-r', 'from-green-500', 'to-green-600');
        }
    });
});

// Apply coupon
document.getElementById('apply_coupon').addEventListener('click', function() {
    const couponCode = document.getElementById('coupon_code').value.trim().toUpperCase();
    const messageDiv = document.getElementById('coupon_message');
    const button = this;
    
    if (!couponCode) {
        messageDiv.className = 'text-sm text-center text-red-600 mt-2';
        messageDiv.textContent = 'Vui lòng nhập mã';
        messageDiv.classList.remove('hidden');
        return;
    }
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch('api/apply-coupon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ coupon_code: couponCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('discount_row').classList.remove('hidden');
            document.getElementById('discount_display').textContent = '-' + data.discount_formatted;
            document.getElementById('total_display').textContent = data.total_formatted;
            
            messageDiv.className = 'text-sm text-center text-green-600 mt-2';
            messageDiv.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Giảm ' + data.discount_formatted;
            
            document.getElementById('checkout-form').insertAdjacentHTML('beforeend', 
                '<input type="hidden" name="applied_coupon" value="' + couponCode + '">' +
                '<input type="hidden" name="discount_amount" value="' + data.discount_amount + '">'
            );
            
            document.getElementById('coupon_code').disabled = true;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('bg-pink-600', 'hover:bg-pink-700');
            button.classList.add('bg-green-600');
            
            document.getElementById('select_voucher_btn').disabled = true;
            document.getElementById('select_voucher_btn').classList.add('opacity-50');
        } else {
            messageDiv.className = 'text-sm text-center text-red-600 mt-2';
            messageDiv.innerHTML = '<i class="fas fa-times-circle mr-1"></i>' + data.message;
        }
        messageDiv.classList.remove('hidden');
    })
    .catch(error => {
        messageDiv.className = 'text-sm text-center text-red-600 mt-2';
        messageDiv.textContent = 'Có lỗi xảy ra';
        messageDiv.classList.remove('hidden');
    })
    .finally(() => {
        if (!button.disabled) {
            button.disabled = false;
            button.innerHTML = 'Áp dụng';
        }
    });
});

// Form submit
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!provinceSelect.value || !districtSelect.value || !wardSelect.value || !specificAddress.value.trim()) {
        alert('Vui lòng điền đầy đủ thông tin địa chỉ');
        return;
    }
    
    updateFullAddress();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submit-btn');
    const paymentMethod = formData.get('payment_method');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
    
    fetch('api/create-order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Không thể tạo đơn hàng');
        
        const orderId = data.order_id;
        
        if (paymentMethod === 'momo') {
            return fetch('api/momo-create-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => response.json())
            .then(momoData => {
                if (momoData.success && momoData.payUrl) {
                    window.location.href = momoData.payUrl;
                } else {
                    throw new Error(momoData.message || 'Không thể tạo thanh toán MoMo');
                }
            });
        } else if (paymentMethod === 'qr_code') {
            window.location.href = 'payment-qr.php?order_id=' + orderId;
        } else {
            window.location.href = 'order-success.php?order_id=' + orderId + '&method=cod';
        }
    })
    .catch(error => {
        alert('Có lỗi: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock"></i><span id="btn-text">Thanh Toán Ngay</span>';
    });
});

// Voucher modal
let availableVouchers = [];

document.getElementById('select_voucher_btn').addEventListener('click', async function() {
    const modal = document.getElementById('voucher_modal');
    const voucherList = document.getElementById('voucher_list');
    
    voucherList.innerHTML = '<div class="text-center py-12"><div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-pink-500 border-t-transparent"></div><p class="mt-4 text-gray-500">Đang tải...</p></div>';
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.add('show'), 10);
    
    try {
        const response = await fetch('api/get-available-vouchers.php');
        const data = await response.json();
        
        if (data.success && data.vouchers.length > 0) {
            availableVouchers = data.vouchers;
            renderVouchers(data.vouchers);
        } else {
            voucherList.innerHTML = '<div class="text-center py-12"><i class="fas fa-ticket-alt text-5xl text-gray-300 mb-4"></i><p class="text-gray-500">Không có voucher khả dụng</p></div>';
        }
    } catch (error) {
        voucherList.innerHTML = '<div class="text-center py-12 text-red-500"><i class="fas fa-exclamation-circle text-4xl mb-4"></i><p>Lỗi tải voucher</p></div>';
    }
});

function renderVouchers(vouchers) {
    const voucherList = document.getElementById('voucher_list');
    const subtotal = <?php echo $subtotal; ?>;
    
    voucherList.innerHTML = vouchers.map(voucher => {
        const canUse = subtotal >= voucher.min_order_amount && !voucher.user_used && 
                       (!voucher.usage_limit || voucher.used_count < voucher.usage_limit);
        const discountText = voucher.type === 'percent' ? `${voucher.value}%` : formatPrice(voucher.value);
        
        return `
            <div class="bg-white border-2 ${canUse ? 'border-pink-200 hover:border-pink-400' : 'border-gray-200 opacity-60'} rounded-xl p-4 transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono font-bold text-pink-600 bg-pink-50 px-2 py-1 rounded">${voucher.code}</span>
                            ${voucher.user_used ? '<span class="text-xs text-gray-500">Đã dùng</span>' : ''}
                        </div>
                        <h4 class="font-bold text-gray-800">${voucher.title}</h4>
                        <p class="text-xs text-gray-500 mt-1">${voucher.description}</p>
                        ${voucher.min_order_amount > 0 ? `<p class="text-xs text-gray-400 mt-1">Đơn tối thiểu ${formatPrice(voucher.min_order_amount)}</p>` : ''}
                    </div>
                    <div class="text-right ml-4">
                        <div class="text-2xl font-bold text-pink-600">-${discountText}</div>
                        <button type="button" onclick="selectVoucher('${voucher.code}')" 
                                ${canUse ? '' : 'disabled'}
                                class="mt-2 px-4 py-2 ${canUse ? 'bg-pink-600 hover:bg-pink-700' : 'bg-gray-300 cursor-not-allowed'} text-white rounded-lg text-sm font-semibold transition-all">
                            ${canUse ? 'Chọn' : 'Không khả dụng'}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function selectVoucher(code) {
    document.getElementById('coupon_code').value = code;
    closeVoucherModal();
    setTimeout(() => document.getElementById('apply_coupon').click(), 300);
}

function closeVoucherModal() {
    const modal = document.getElementById('voucher_modal');
    modal.classList.remove('show');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

// Đăng ký event listener sau khi DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('close_voucher_modal');
    const modal = document.getElementById('voucher_modal');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeVoucherModal);
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeVoucherModal();
        });
    }
});

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}
</script>

<!-- Modal Voucher -->
<div id="voucher_modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-hidden transform transition-all duration-300 scale-95" id="voucher_modal_content">
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-ticket-alt"></i>
                Chọn Voucher
            </h3>
            <button type="button" id="close_voucher_modal" class="text-white/80 hover:text-white p-2 hover:bg-white/20 rounded-lg transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 overflow-y-auto max-h-[60vh] space-y-3 bg-gray-50" id="voucher_list"></div>
    </div>
</div>

<style>
#voucher_modal.show { opacity: 1; }
#voucher_modal.show #voucher_modal_content { transform: scale(1); }
</style>

<?php require_once 'includes/footer.php'; ?>
