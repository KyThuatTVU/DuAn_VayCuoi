<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Chính Sách Thuê Váy';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Chính Sách Thuê Váy</span>
        </nav>
    </div>
</div>

<!-- Rental Policy Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-6">
                    <i class="fas fa-file-signature text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Chính Sách Thuê Váy</h1>
                <p class="text-lg text-gray-600">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                
                <!-- Introduction -->
                <div class="mb-10">
                    <p class="text-gray-700 leading-relaxed text-lg">
                        Chào mừng bạn đến với <strong class="text-pink-600"><?php echo SITE_NAME; ?></strong>. 
                        Dưới đây là các chính sách thuê váy cưới của chúng tôi. Vui lòng đọc kỹ để có trải nghiệm thuê váy tốt nhất.
                    </p>
                </div>

                <!-- Section 1 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-pink-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">1. Thời Gian Thuê</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div class="bg-pink-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Gói thuê tiêu chuẩn:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li><strong>3 ngày 2 đêm:</strong> Phù hợp cho đám cưới trong ngày</li>
                                <li><strong>5 ngày 4 đêm:</strong> Phù hợp cho đám cưới + chụp ảnh</li>
                                <li><strong>7 ngày 6 đêm:</strong> Gói trọn vẹn cho cả tuần cưới</li>
                            </ul>
                        </div>
                        <p class="text-gray-700">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Thời gian thuê được tính từ ngày nhận váy đến ngày trả váy. Nếu cần thuê thêm, vui lòng thông báo trước và sẽ tính phí 15% giá thuê/ngày.
                        </p>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-money-bill-wave text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">2. Đặt Cọc & Thanh Toán</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">2.1. Tiền đặt cọc:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Váy dưới 5 triệu: Đặt cọc <strong>30%</strong> giá thuê</li>
                                <li>Váy từ 5-10 triệu: Đặt cọc <strong>40%</strong> giá thuê</li>
                                <li>Váy trên 10 triệu: Đặt cọc <strong>50%</strong> giá thuê</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">2.2. Tiền thế chân:</h3>
                            <p class="text-gray-700 ml-4">
                                Ngoài tiền cọc, khách hàng cần đặt thêm tiền thế chân (từ 1-3 triệu tùy giá trị váy). 
                                Tiền thế chân sẽ được hoàn trả 100% khi trả váy đúng hạn và váy không bị hư hỏng.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">2.3. Thanh toán:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Thanh toán tiền cọc khi đặt váy</li>
                                <li>Thanh toán số tiền còn lại + tiền thế chân khi nhận váy</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">3. Đặt Lịch & Thử Váy</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Khách hàng nên đặt lịch thử váy trước <strong>1-2 tuần</strong> để được phục vụ tốt nhất</li>
                            <li>Mỗi buổi thử váy kéo dài <strong>1-2 giờ</strong>, được thử tối đa <strong>5 mẫu váy</strong></li>
                            <li>Miễn phí thử váy lần đầu, các lần sau tính phí 200.000đ/lần</li>
                            <li>Nên đặt váy trước ngày cưới <strong>ít nhất 2 tuần</strong> để đảm bảo có váy và thời gian sửa (nếu cần)</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 4 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cut text-green-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">4. Sửa Váy</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Miễn phí:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Bóp eo, nới eo trong phạm vi 5cm</li>
                                <li>Điều chỉnh độ dài váy (lên gấu, xuống gấu)</li>
                                <li>Sửa dây vai, điều chỉnh cúp ngực</li>
                            </ul>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Có phí (từ 200.000đ - 1.000.000đ):</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Thay đổi kiểu dáng váy</li>
                                <li>Thêm/bớt chi tiết trang trí</li>
                                <li>Sửa váy với thay đổi lớn (trên 5cm)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 5 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">5. Hủy Đơn & Hoàn Tiền</h2>
                    </div>
                    <div class="pl-14">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-200 px-4 py-3 text-left">Thời điểm hủy</th>
                                        <th class="border border-gray-200 px-4 py-3 text-left">Hoàn tiền cọc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-3">Trước 14 ngày</td>
                                        <td class="border border-gray-200 px-4 py-3 text-green-600 font-semibold">100%</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-200 px-4 py-3">7 - 14 ngày</td>
                                        <td class="border border-gray-200 px-4 py-3 text-yellow-600 font-semibold">70%</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-3">3 - 7 ngày</td>
                                        <td class="border border-gray-200 px-4 py-3 text-orange-600 font-semibold">50%</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="border border-gray-200 px-4 py-3">Dưới 3 ngày</td>
                                        <td class="border border-gray-200 px-4 py-3 text-red-600 font-semibold">0%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-gray-600 text-sm mt-4">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                            Thời điểm hủy được tính so với ngày nhận váy dự kiến.
                        </p>
                    </div>
                </div>

                <!-- Section 6 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-orange-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">6. Bồi Thường Hư Hỏng</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Không tính phí:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Bẩn nhẹ có thể giặt sạch</li>
                                <li>Sút chỉ, tuột nút nhỏ</li>
                                <li>Nhăn do vận chuyển</li>
                            </ul>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Bồi thường theo mức độ:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li><strong>Hư hỏng nhẹ</strong> (vết bẩn khó tẩy, rách nhỏ): 10-30% giá váy</li>
                                <li><strong>Hư hỏng trung bình</strong> (rách lớn, mất phụ kiện): 30-50% giá váy</li>
                                <li><strong>Hư hỏng nặng</strong> (không thể phục hồi): 70-100% giá váy</li>
                                <li><strong>Mất váy:</strong> 100% giá váy + phí xử lý</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 7 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-undo text-teal-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">7. Trả Váy Trễ Hạn</h2>
                    </div>
                    <div class="pl-14">
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Phí trả trễ: <strong>200.000đ/ngày</strong></li>
                            <li>Trả trễ quá 3 ngày mà không thông báo: Mất tiền thế chân</li>
                            <li>Trả trễ quá 7 ngày: Được coi là mất váy, phải bồi thường 100% giá trị</li>
                        </ul>
                        <p class="text-gray-600 text-sm mt-4">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Nếu cần gia hạn, vui lòng thông báo trước ít nhất 1 ngày để được hỗ trợ.
                        </p>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-8 mt-12">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-phone-alt text-pink-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Cần Hỗ Trợ Thêm?</h3>
                            <p class="text-gray-700 mb-4">
                                Nếu bạn có thắc mắc về chính sách thuê váy, vui lòng liên hệ:
                            </p>
                            <div class="space-y-2 text-gray-700">
                                <p><i class="fas fa-phone text-pink-600 mr-2"></i>Hotline: <strong>078.797.2075</strong></p>
                                <p><i class="fas fa-envelope text-pink-600 mr-2"></i>Email: <strong><?php echo ADMIN_EMAIL; ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
