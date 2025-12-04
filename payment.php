<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/settings-helper.php';
$page_title = 'Hướng Dẫn Thanh Toán';
require_once 'includes/header.php';

// Lấy thông tin ngân hàng từ cài đặt
$bank_name = getSetting($conn, 'bank_name', 'Vietcombank');
$bank_account = getSetting($conn, 'bank_account', '1234567890123');
$bank_holder = getSetting($conn, 'bank_holder', 'NGUYEN VAN A');
$bank_branch = getSetting($conn, 'bank_branch', 'TP. Hồ Chí Minh');
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Hướng Dẫn Thanh Toán</span>
        </nav>
    </div>
</div>

<!-- Payment Guide Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-6">
                    <i class="fas fa-credit-card text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Hướng Dẫn Thanh Toán</h1>
                <p class="text-lg text-gray-600">Các phương thức thanh toán an toàn và tiện lợi</p>
            </div>

            <!-- Payment Methods -->
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <!-- MoMo -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">MoMo</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Ví MoMo</h3>
                            <span class="text-green-600 text-sm font-medium">Phổ biến nhất</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Thanh toán nhanh chóng qua ứng dụng MoMo với mã QR hoặc số điện thoại.</p>
                    <div class="bg-pink-50 rounded-lg p-3">
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Xác nhận tức thì</p>
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Không mất phí giao dịch</p>
                    </div>
                </div>

                <!-- Bank Transfer -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-university text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Chuyển Khoản</h3>
                            <span class="text-blue-600 text-sm font-medium">Ngân hàng</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Chuyển khoản qua Internet Banking hoặc tại quầy giao dịch ngân hàng.</p>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Hỗ trợ tất cả ngân hàng</p>
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>An toàn, bảo mật</p>
                    </div>
                </div>

                <!-- VNPay -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">VNPay</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">VNPay QR</h3>
                            <span class="text-indigo-600 text-sm font-medium">Quét mã QR</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Thanh toán bằng cách quét mã QR qua ứng dụng ngân hàng hoặc ví điện tử.</p>
                    <div class="bg-indigo-50 rounded-lg p-3">
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Liên kết 40+ ngân hàng</p>
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Xác nhận tự động</p>
                    </div>
                </div>

                <!-- Cash -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Tiền Mặt</h3>
                            <span class="text-green-600 text-sm font-medium">Tại showroom</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Thanh toán trực tiếp bằng tiền mặt khi đến showroom thử váy hoặc nhận váy.</p>
                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Không cần tài khoản</p>
                        <p class="text-sm text-gray-700"><i class="fas fa-check text-green-500 mr-2"></i>Nhận biên lai ngay</p>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                
                <!-- Bank Info -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-university text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Thông Tin Chuyển Khoản</h2>
                    </div>
                    <div class="pl-14">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Ngân hàng</p>
                                    <p class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($bank_name); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Số tài khoản</p>
                                    <p class="font-bold text-gray-800 text-lg font-mono"><?php echo htmlspecialchars($bank_account); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Chủ tài khoản</p>
                                    <p class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($bank_holder); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Chi nhánh</p>
                                    <p class="font-bold text-gray-800 text-lg"><?php echo htmlspecialchars($bank_branch); ?></p>
                                </div>
                            </div>
                            <div class="border-t border-blue-200 pt-4">
                                <p class="text-gray-500 text-sm mb-1">Nội dung chuyển khoản</p>
                                <p class="font-bold text-pink-600 text-lg">[Mã đơn hàng] - [Số điện thoại]</p>
                                <p class="text-gray-600 text-sm mt-1">Ví dụ: DH123456 - 0901234567</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Steps -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-list-ol text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Các Bước Thanh Toán</h2>
                    </div>
                    <div class="pl-14">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-pink-500 text-white rounded-full flex items-center justify-center font-bold mr-4 flex-shrink-0">1</div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Chọn sản phẩm</h4>
                                    <p class="text-gray-600">Thêm váy cưới vào giỏ hàng và tiến hành đặt hàng</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-pink-500 text-white rounded-full flex items-center justify-center font-bold mr-4 flex-shrink-0">2</div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Điền thông tin</h4>
                                    <p class="text-gray-600">Nhập đầy đủ thông tin giao hàng và chọn ngày nhận váy</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-pink-500 text-white rounded-full flex items-center justify-center font-bold mr-4 flex-shrink-0">3</div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Chọn phương thức thanh toán</h4>
                                    <p class="text-gray-600">Chọn MoMo, chuyển khoản, VNPay hoặc thanh toán khi nhận hàng</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-pink-500 text-white rounded-full flex items-center justify-center font-bold mr-4 flex-shrink-0">4</div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Hoàn tất thanh toán</h4>
                                    <p class="text-gray-600">Thực hiện thanh toán theo hướng dẫn và chờ xác nhận</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold mr-4 flex-shrink-0">
                                    <i class="fas fa-check text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Nhận xác nhận</h4>
                                    <p class="text-gray-600">Nhận email/SMS xác nhận đơn hàng và theo dõi trạng thái</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-circle text-yellow-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Lưu Ý Quan Trọng</h2>
                    </div>
                    <div class="pl-14">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg p-4 space-y-3">
                            <p class="text-gray-700">
                                <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                Vui lòng ghi đúng nội dung chuyển khoản để đơn hàng được xác nhận nhanh chóng.
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                Đơn hàng sẽ được xác nhận trong vòng <strong>30 phút - 2 giờ</strong> sau khi thanh toán thành công.
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                Nếu thanh toán qua MoMo/VNPay, đơn hàng sẽ được xác nhận tự động.
                            </p>
                            <p class="text-gray-700">
                                <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                Giữ lại biên lai/ảnh chụp màn hình giao dịch để đối chiếu khi cần.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Refund Policy -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-undo text-green-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Hoàn Tiền</h2>
                    </div>
                    <div class="pl-14">
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Hoàn tiền qua cùng phương thức thanh toán ban đầu</li>
                            <li>Thời gian hoàn tiền: <strong>3-5 ngày làm việc</strong> đối với chuyển khoản</li>
                            <li>Hoàn tiền MoMo/VNPay: <strong>1-3 ngày làm việc</strong></li>
                            <li>Xem chi tiết chính sách hoàn tiền tại <a href="rental-policy.php" class="text-pink-600 hover:underline">Chính sách thuê váy</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-headset text-pink-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Cần Hỗ Trợ Thanh Toán?</h3>
                            <p class="text-gray-700 mb-4">
                                Nếu gặp vấn đề trong quá trình thanh toán, vui lòng liên hệ ngay:
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <a href="tel:0787972075" class="inline-flex items-center gap-2 bg-pink-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-700 transition">
                                    <i class="fas fa-phone"></i>
                                    078.797.2075
                                </a>
                                <a href="contact.php" class="inline-flex items-center gap-2 bg-white text-pink-600 px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition border border-pink-200">
                                    <i class="fas fa-envelope"></i>
                                    Gửi Tin Nhắn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
