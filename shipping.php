<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/settings-helper.php';
$page_title = 'Giao Nhận & Hoàn Trả';
require_once 'includes/header.php';

// Lấy thông tin liên hệ từ cài đặt
$contact_address = getSetting($conn, 'contact_address', '123 Đường ABC, Quận XYZ, TP.HCM');
$contact_phone = getSetting($conn, 'contact_hotline', '078.797.2075');
$working_days = getSetting($conn, 'working_days', 'Thứ 2 - Chủ Nhật');
$working_hours = getSetting($conn, 'working_hours', '8:00 - 20:00');
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Giao Nhận & Hoàn Trả</span>
        </nav>
    </div>
</div>

<!-- Shipping Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-6">
                    <i class="fas fa-truck text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Giao Nhận & Hoàn Trả</h1>
                <p class="text-lg text-gray-600">Quy trình giao nhận và hoàn trả váy cưới</p>
            </div>

            <!-- Delivery Options -->
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <!-- Option 1 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-store text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Nhận Tại Showroom</h3>
                    <p class="text-gray-600 text-sm mb-3">Đến trực tiếp showroom để nhận váy</p>
                    <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">Miễn phí</span>
                </div>

                <!-- Option 2 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-motorcycle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Giao Nội Thành</h3>
                    <p class="text-gray-600 text-sm mb-3">Giao hàng trong TP.HCM</p>
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">50.000đ - 100.000đ</span>
                </div>

                <!-- Option 3 -->
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-violet-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-plane text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Giao Tỉnh</h3>
                    <p class="text-gray-600 text-sm mb-3">Giao hàng toàn quốc</p>
                    <span class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-medium">Theo bảng giá</span>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                
                <!-- Section 1: Delivery -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-shipping-fast text-pink-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">1. Giao Hàng</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">1.1. Khu vực giao hàng:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li><strong>Nội thành TP.HCM:</strong> Giao trong ngày hoặc hẹn giờ</li>
                                <li><strong>Ngoại thành TP.HCM:</strong> Giao trong 1-2 ngày</li>
                                <li><strong>Các tỉnh lân cận:</strong> Giao trong 2-3 ngày</li>
                                <li><strong>Các tỉnh xa:</strong> Giao trong 3-5 ngày</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">1.2. Phí giao hàng:</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-200 px-4 py-3 text-left">Khu vực</th>
                                            <th class="border border-gray-200 px-4 py-3 text-left">Phí ship</th>
                                            <th class="border border-gray-200 px-4 py-3 text-left">Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-gray-200 px-4 py-3">Quận 1, 3, 5, 10</td>
                                            <td class="border border-gray-200 px-4 py-3 text-green-600 font-semibold">50.000đ</td>
                                            <td class="border border-gray-200 px-4 py-3">2-4 giờ</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="border border-gray-200 px-4 py-3">Các quận khác TP.HCM</td>
                                            <td class="border border-gray-200 px-4 py-3 text-blue-600 font-semibold">70.000đ - 100.000đ</td>
                                            <td class="border border-gray-200 px-4 py-3">4-8 giờ</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-gray-200 px-4 py-3">Bình Dương, Đồng Nai</td>
                                            <td class="border border-gray-200 px-4 py-3 text-purple-600 font-semibold">150.000đ</td>
                                            <td class="border border-gray-200 px-4 py-3">1 ngày</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="border border-gray-200 px-4 py-3">Các tỉnh khác</td>
                                            <td class="border border-gray-200 px-4 py-3 text-orange-600 font-semibold">200.000đ - 400.000đ</td>
                                            <td class="border border-gray-200 px-4 py-3">2-5 ngày</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Receiving -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-box-open text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">2. Nhận Hàng</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Khi nhận váy, vui lòng:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Kiểm tra váy ngay trước mặt shipper</li>
                                <li>Đối chiếu với hình ảnh và mô tả đơn hàng</li>
                                <li>Kiểm tra đầy đủ phụ kiện đi kèm (nếu có)</li>
                                <li>Ký xác nhận nhận hàng</li>
                                <li>Thanh toán số tiền còn lại + tiền thế chân (nếu chưa thanh toán)</li>
                            </ul>
                        </div>
                        <p class="text-gray-700">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                            <strong>Lưu ý:</strong> Nếu phát hiện váy có vấn đề, vui lòng từ chối nhận và liên hệ ngay hotline để được hỗ trợ.
                        </p>
                    </div>
                </div>

                <!-- Section 3: Return -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-undo-alt text-green-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">3. Hoàn Trả Váy</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">3.1. Hình thức trả váy:</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-green-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-green-700 mb-2"><i class="fas fa-store mr-2"></i>Trả tại showroom</h4>
                                    <p class="text-gray-600 text-sm">Mang váy đến trực tiếp showroom trong giờ làm việc. Miễn phí.</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-blue-700 mb-2"><i class="fas fa-truck mr-2"></i>Nhân viên đến nhận</h4>
                                    <p class="text-gray-600 text-sm">Đặt lịch để nhân viên đến tận nơi nhận váy. Phí: 50.000đ - 100.000đ.</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">3.2. Quy định trả váy:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Trả váy đúng hạn theo hợp đồng thuê</li>
                                <li><strong>Không cần giặt váy</strong> trước khi trả</li>
                                <li>Trả đầy đủ phụ kiện đi kèm (nếu có)</li>
                                <li>Váy được kiểm tra tình trạng khi nhận lại</li>
                                <li>Hoàn trả tiền thế chân sau khi kiểm tra (nếu váy không hư hỏng)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Late Return -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-red-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">4. Trả Trễ Hạn</h2>
                    </div>
                    <div class="pl-14">
                        <div class="bg-red-50 border-l-4 border-red-400 rounded-r-lg p-4 space-y-3">
                            <p class="text-gray-700">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                Phí trả trễ: <strong class="text-red-600">200.000đ/ngày</strong>
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                Trả trễ quá 3 ngày không thông báo: <strong class="text-red-600">Mất tiền thế chân</strong>
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                Trả trễ quá 7 ngày: <strong class="text-red-600">Bồi thường 100% giá trị váy</strong>
                            </p>
                        </div>
                        <p class="text-gray-600 text-sm mt-4">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Nếu cần gia hạn, vui lòng liên hệ trước ít nhất 1 ngày để được hỗ trợ và tính phí gia hạn hợp lý.
                        </p>
                    </div>
                </div>

                <!-- Section 5: Care Instructions -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-heart text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">5. Hướng Dẫn Bảo Quản</h2>
                    </div>
                    <div class="pl-14">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-semibold text-green-700 mb-2"><i class="fas fa-check-circle mr-2"></i>Nên làm</h4>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li>• Treo váy trên móc áo chuyên dụng</li>
                                    <li>• Bảo quản nơi khô ráo, thoáng mát</li>
                                    <li>• Dùng túi bọc váy khi di chuyển</li>
                                    <li>• Cẩn thận với trang sức, móng tay</li>
                                </ul>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4">
                                <h4 class="font-semibold text-red-700 mb-2"><i class="fas fa-times-circle mr-2"></i>Không nên</h4>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li>• Gấp váy hoặc để váy bị nhăn</li>
                                    <li>• Để váy gần nguồn nhiệt, lửa</li>
                                    <li>• Ăn uống khi mặc váy</li>
                                    <li>• Tự ý giặt hoặc là váy</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-map-marker-alt text-pink-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Địa Chỉ Showroom</h3>
                            <div class="space-y-2 text-gray-700 mb-4">
                                <p><i class="fas fa-building text-pink-600 mr-2"></i><strong><?php echo SITE_NAME; ?></strong></p>
                                <p><i class="fas fa-map-marker-alt text-pink-600 mr-2"></i><?php echo htmlspecialchars($contact_address); ?></p>
                                <p><i class="fas fa-clock text-pink-600 mr-2"></i><?php echo htmlspecialchars($working_hours); ?> (<?php echo htmlspecialchars($working_days); ?>)</p>
                                <p><i class="fas fa-phone text-pink-600 mr-2"></i>Hotline: <strong><?php echo htmlspecialchars($contact_phone); ?></strong></p>
                            </div>
                            <a href="contact.php" class="inline-flex items-center gap-2 bg-pink-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-700 transition">
                                <i class="fas fa-directions"></i>
                                Xem Bản Đồ
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
