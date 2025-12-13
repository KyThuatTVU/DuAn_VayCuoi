<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';

// Load settings helper
if (!function_exists('getSetting')) {
    require_once 'includes/settings-helper.php';
}
$contact_address = getSetting($conn, 'contact_address', '123 Đường Nguyễn Huệ, Quận 1, TP.HCM');
$contact_phone = getSetting($conn, 'contact_phone', '0901 234 567');
$working_hours = getSetting($conn, 'working_hours', '8:00 - 20:00');

$page_title = 'Đặt Lịch Thử Váy';

// Xử lý form submit
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $phone = sanitizeInput($_POST['phone']);
    $email = !empty($_POST['email']) ? sanitizeInput($_POST['email']) : NULL;
    $vay_id = !empty($_POST['vay_id']) ? intval($_POST['vay_id']) : NULL;
    $scheduled_date = sanitizeInput($_POST['scheduled_date']);
    $scheduled_time = sanitizeInput($_POST['scheduled_time']);
    $number_of_persons = intval($_POST['number_of_persons']);
    $note = !empty($_POST['note']) ? sanitizeInput($_POST['note']) : NULL;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    
    // Validate
    if (empty($name) || empty($phone) || empty($scheduled_date) || empty($scheduled_time)) {
        $error_message = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
    } else {
        // Insert vào database
        $stmt = $conn->prepare("INSERT INTO dat_lich_thu_vay (user_id, name, phone, email, vay_id, scheduled_date, scheduled_time, number_of_persons, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssissis", $user_id, $name, $phone, $email, $vay_id, $scheduled_date, $scheduled_time, $number_of_persons, $note);
        
        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            
            // Gửi thông báo cho admin
            notifyNewBooking($conn, $booking_id, $name, $phone, $scheduled_date, $scheduled_time);
            
            $success_message = 'Đặt lịch thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.';
        } else {
            $error_message = 'Có lỗi xảy ra. Vui lòng thử lại!';
        }
        $stmt->close();
    }
}

// Lấy danh sách váy cưới
$dresses = [];
$result = $conn->query("SELECT id, ma_vay, ten_vay FROM vay_cuoi WHERE so_luong_ton > 0 ORDER BY ten_vay");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dresses[] = $row;
    }
}

// Lấy thông tin user nếu đã đăng nhập
$user_info = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT ho_ten, email, so_dien_thoai FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gray-100 py-4">
    <div class="container mx-auto px-4">
        <div class="text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Đặt Lịch Thử Váy</span>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Đặt Lịch Thử Váy</h1>
            <p class="text-lg text-gray-600">Vui lòng điền thông tin để đặt lịch hẹn thử váy tại showroom</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($success_message): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span><?php echo $success_message; ?></span>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span><?php echo $error_message; ?></span>
        </div>
        <?php endif; ?>

        <!-- Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <!-- Form Column -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <form method="POST" class="space-y-6">
                        <!-- Row 1: Họ tên và Số điện thoại -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Họ và Tên <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    required 
                                    value="<?php echo $user_info ? htmlspecialchars($user_info['ho_ten']) : ''; ?>"
                                    placeholder="Nguyễn Văn A"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Số Điện Thoại <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    required 
                                    value="<?php echo $user_info ? htmlspecialchars($user_info['so_dien_thoai']) : ''; ?>"
                                    placeholder="0901234567"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                >
                            </div>
                        </div>

                        <!-- Row 2: Email và Váy muốn thử -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="<?php echo $user_info ? htmlspecialchars($user_info['email']) : ''; ?>"
                                    placeholder="email@example.com"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Váy Muốn Thử
                                </label>
                                <select 
                                    name="vay_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                >
                                    <option value="">-- Chọn váy --</option>
                                    <?php foreach ($dresses as $dress): ?>
                                    <option value="<?php echo $dress['id']; ?>">
                                        <?php echo htmlspecialchars($dress['ten_vay']); ?> (<?php echo htmlspecialchars($dress['ma_vay']); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Ngày hẹn và Giờ hẹn -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Ngày Hẹn <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="scheduled_date" 
                                    required 
                                    min="<?php echo date('Y-m-d'); ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Giờ Hẹn <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="scheduled_time" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                >
                                    <option value="">-- Chọn giờ --</option>
                                    <option value="09:00:00">09:00</option>
                                    <option value="10:00:00">10:00</option>
                                    <option value="11:00:00">11:00</option>
                                    <option value="14:00:00">14:00</option>
                                    <option value="15:00:00">15:00</option>
                                    <option value="16:00:00">16:00</option>
                                    <option value="17:00:00">17:00</option>
                                </select>
                            </div>
                        </div>

                        <!-- Số người đi cùng -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Số Người Đi Cùng
                            </label>
                            <input 
                                type="number" 
                                name="number_of_persons" 
                                value="1" 
                                min="1" 
                                max="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            >
                        </div>

                        <!-- Ghi chú -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Ghi Chú
                            </label>
                            <textarea 
                                name="note" 
                                rows="4" 
                                placeholder="Yêu cầu đặc biệt hoặc ghi chú thêm..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold py-4 px-6 rounded-lg hover:from-pink-600 hover:to-purple-700 transform hover:scale-105 transition duration-300 shadow-lg"
                        >
                            Đặt Lịch Ngay
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Column -->
            <div class="space-y-6">
                <!-- Thông tin Showroom -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Thông Tin Showroom</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-pink-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Địa Chỉ</p>
                                <p class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($contact_address)); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-pink-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Hotline</p>
                                <p class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($contact_phone)); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-pink-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Giờ Làm Việc</p>
                                <p class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($working_hours)); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lưu ý -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Lưu Ý Khi Thử Váy</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600 text-sm">Đến đúng giờ hẹn để được phục vụ tốt nhất</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600 text-sm">Mang theo giày cao gót để thử váy chuẩn nhất</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600 text-sm">Có thể mang theo người thân để tư vấn</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600 text-sm">Thời gian thử váy: 60-90 phút</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600 text-sm">Miễn phí hoàn toàn, không mất phí</span>
                        </li>
                    </ul>
                </div>

                <!-- Ưu đãi -->
                <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <h3 class="text-xl font-bold mb-3">Ưu Đãi Đặc Biệt</h3>
                    <p class="text-sm">Đặt lịch hôm nay, nhận ngay voucher giảm 10% khi thuê váy!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
