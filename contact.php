<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';
$page_title = 'Liên Hệ';

// Xử lý form submit
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validate
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Vui lòng điền đầy đủ các trường bắt buộc (Họ tên, Email, Chủ đề, Nội dung).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Địa chỉ email không hợp lệ.';
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error_message = 'Số điện thoại không hợp lệ (phải có 10-11 chữ số).';
    } else {
        // Xử lý upload ảnh
        $image_path = null;
        if (isset($_FILES['contact_image']) && $_FILES['contact_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/contacts/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_tmp = $_FILES['contact_image']['tmp_name'];
            $file_name = $_FILES['contact_image']['name'];
            $file_size = $_FILES['contact_image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Kiểm tra định dạng file
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_ext, $allowed_extensions)) {
                $error_message = 'Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG, GIF.';
            } elseif ($file_size > $max_file_size) {
                $error_message = 'Kích thước file không được vượt quá 5MB.';
            } else {
                // Tạo tên file unique
                $new_file_name = 'contact_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_path = $upload_path;
                } else {
                    $error_message = 'Có lỗi khi upload ảnh. Vui lòng thử lại.';
                }
            }
        }
        
        // Nếu không có lỗi upload, tiếp tục insert vào database
        if (empty($error_message)) {
            // Lấy user_id nếu đã đăng nhập
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            
            try {
                // Insert vào database với prepared statement
                if ($user_id) {
                    $stmt = $conn->prepare("INSERT INTO lien_he (user_id, name, email, phone, subject, message, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issssss", $user_id, $name, $email, $phone, $subject, $message, $image_path);
                } else {
                    $stmt = $conn->prepare("INSERT INTO lien_he (name, email, phone, subject, message, image_path) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $name, $email, $phone, $subject, $message, $image_path);
                }
                
                if ($stmt->execute()) {
                    $contact_id = $conn->insert_id;
                    
                    // Gửi thông báo cho admin
                    notifyNewContact($conn, $contact_id, $name, $subject);
                    
                    $success_message = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
                    // Reset form data sau khi gửi thành công
                    $_POST = array();
                    $name = $email = $phone = $subject = $message = '';
                } else {
                    $error_message = 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.';
                }
                $stmt->close();
            } catch (Exception $e) {
                $error_message = 'Lỗi hệ thống: ' . $e->getMessage();
            }
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Liên Hệ</span>
        </nav>
    </div>
</div>

<!-- Contact Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Liên Hệ Với Chúng Tôi</h1>
            <p class="text-lg text-gray-600">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
        </div>

        <!-- Contact Cards Component -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            <!-- Contact Card 1 - Hotline -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fas fa-phone-alt text-3xl text-primary"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Hotline</h3>
                <a href="tel:0787972075" class="text-primary text-lg font-semibold hover:underline">078.797.2075</a>
                <p class="text-gray-600 mt-2 text-sm">Hỗ trợ 24/7</p>
            </div>
            
            <!-- Contact Card 2 - Zalo -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fab fa-whatsapp text-3xl text-accent"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Zalo</h3>
                <a href="https://zalo.me/0787972075" class="text-accent text-lg font-semibold hover:underline" target="_blank">078.797.2075</a>
                <p class="text-gray-600 mt-2 text-sm">Chat nhanh</p>
            </div>
            
            <!-- Contact Card 3 - Email -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fas fa-envelope text-3xl text-secondary"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Email</h3>
                <a href="mailto:duyphongtv123@gmail.com" class="text-secondary text-lg font-semibold hover:underline break-all">duyphongtv123@gmail.com</a>
                <p class="text-gray-600 mt-2 text-sm">Hỗ trợ email</p>
            </div>
            
            <!-- Contact Card 4 - Địa chỉ -->
            <div class="bg-gradient-to-br from-red-50 to-orange-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fas fa-map-marker-alt text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Địa chỉ</h3>
                <p class="text-gray-700 text-sm leading-relaxed">123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
                <p class="text-gray-600 mt-2 text-sm">Ghé thăm</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg flex items-center">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span><?php echo $success_message; ?></span>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg flex items-center">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span><?php echo $error_message; ?></span>
        </div>
        <?php endif; ?>

        <!-- Contact Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Gửi Tin Nhắn</h2>
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <!-- Họ và Tên -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Họ và Tên <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                placeholder="Nhập họ và tên của bạn"
                            >
                        </div>

                        <!-- Email & Phone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                    placeholder="email@example.com"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Số Điện Thoại
                                </label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                                    placeholder="0901234567"
                                >
                            </div>
                        </div>

                        <!-- Chủ Đề -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Chủ Đề <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="subject" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            >
                                <option value="">-- Chọn chủ đề --</option>
                                <option <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Hỏi về giá thuê váy') ? 'selected' : ''; ?>>Hỏi về giá thuê váy</option>
                                <option <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Đặt lịch thử váy') ? 'selected' : ''; ?>>Đặt lịch thử váy</option>
                                <option <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Khiếu nại dịch vụ') ? 'selected' : ''; ?>>Khiếu nại dịch vụ</option>
                                <option <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Góp ý, đề xuất') ? 'selected' : ''; ?>>Góp ý, đề xuất</option>
                                <option <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>

                        <!-- Nội Dung -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nội Dung <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="message" 
                                rows="6" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition resize-none"
                                placeholder="Nhập nội dung tin nhắn của bạn..."
                            ><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <!-- Upload Ảnh -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Đính Kèm Ảnh (Tùy chọn)
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click để chọn ảnh</span> hoặc kéo thả</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF (MAX. 5MB)</p>
                                    </div>
                                    <input 
                                        type="file" 
                                        name="contact_image" 
                                        accept="image/jpeg,image/jpg,image/png,image/gif"
                                        class="hidden"
                                        onchange="displayFileName(this)"
                                    >
                                </label>
                            </div>
                            <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold py-4 px-6 rounded-lg hover:from-pink-600 hover:to-purple-700 transform hover:scale-[1.02] transition duration-200 shadow-lg"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Gửi Tin Nhắn
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="space-y-6">
                <!-- Địa Chỉ -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Địa Chỉ Showroom</h3>
                            <p class="text-gray-600 leading-relaxed">123 Đường Nguyễn Huệ<br>Quận 1, TP. Hồ Chí Minh</p>
                        </div>
                    </div>
                </div>

                <!-- Số Điện Thoại -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Số Điện Thoại</h3>
                            <p class="text-gray-600 leading-relaxed">Hotline: 0901 234 567<br>Tel: (028) 3822 xxxx</p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Email</h3>
                            <p class="text-gray-600 leading-relaxed">contact@vaycuoi.com<br>support@vaycuoi.com</p>
                        </div>
                    </div>
                </div>

                <!-- Giờ Làm Việc -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Giờ Làm Việc</h3>
                            <p class="text-gray-600 leading-relaxed">Thứ 2 - Chủ Nhật<br>8:00 AM - 8:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="mt-16 max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4967!2d106.7!3d10.8!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ4JzAwLjAiTiAxMDbCsDQyJzAwLjAiRQ!5e0!3m2!1svi!2s!4v1234567890" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                    class="w-full"
                ></iframe>
            </div>
        </div>
    </div>
</section>

<script>
function displayFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
        fileNameDisplay.innerHTML = `<span class="text-green-600"><i class="fas fa-check-circle"></i> Đã chọn: <strong>${fileName}</strong> (${fileSize} MB)</span>`;
    } else {
        fileNameDisplay.innerHTML = '';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
