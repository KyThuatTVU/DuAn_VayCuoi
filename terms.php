<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Điều Khoản Dịch Vụ';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Điều Khoản Dịch Vụ</span>
        </nav>
    </div>
</div>

<!-- Terms Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-6">
                    <i class="fas fa-file-contract text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Điều Khoản Dịch Vụ</h1>
                <p class="text-lg text-gray-600">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                
                <!-- Introduction -->
                <div class="mb-10">
                    <p class="text-gray-700 leading-relaxed text-lg">
                        Chào mừng bạn đến với <strong class="text-pink-600"><?php echo SITE_NAME; ?></strong>. 
                        Bằng việc truy cập và sử dụng website của chúng tôi, bạn đồng ý tuân thủ các điều khoản và điều kiện sau đây. 
                        Vui lòng đọc kỹ trước khi sử dụng dịch vụ.
                    </p>
                </div>

                <!-- Section 1 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-pink-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">1. Giới Thiệu Dịch Vụ</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700 mb-4">
                            <?php echo SITE_NAME; ?> cung cấp dịch vụ cho thuê váy cưới cao cấp với các tính năng:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 space-y-2">
                            <li>Xem và tìm kiếm các mẫu váy cưới</li>
                            <li>Đặt lịch thử váy tại showroom</li>
                            <li>Đặt hàng và thanh toán trực tuyến</li>
                            <li>Theo dõi đơn hàng và lịch sử giao dịch</li>
                            <li>Nhận tư vấn từ chuyên gia</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">2. Tài Khoản Người Dùng</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">2.1. Đăng ký tài khoản:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Bạn phải từ 16 tuổi trở lên để đăng ký</li>
                                <li>Thông tin cung cấp phải chính xác và đầy đủ</li>
                                <li>Bạn chịu trách nhiệm bảo mật mật khẩu</li>
                                <li>Không chia sẻ tài khoản cho người khác</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">2.2. Trách nhiệm:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Bạn chịu trách nhiệm về mọi hoạt động từ tài khoản của mình</li>
                                <li>Thông báo ngay cho chúng tôi nếu phát hiện truy cập trái phép</li>
                                <li>Cập nhật thông tin khi có thay đổi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="mb-10">
                    <div 