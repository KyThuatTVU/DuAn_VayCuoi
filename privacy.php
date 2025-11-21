<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Chính Sách Bảo Mật';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Chính Sách Bảo Mật</span>
        </nav>
    </div>
</div>

<!-- Privacy Policy Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-6">
                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Chính Sách Bảo Mật</h1>
                <p class="text-lg text-gray-600">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                
                <!-- Introduction -->
                <div class="mb-10">
                    <p class="text-gray-700 leading-relaxed text-lg">
                        Chào mừng bạn đến với <strong class="text-pink-600"><?php echo SITE_NAME; ?></strong>. 
                        Chúng tôi cam kết bảo vệ quyền riêng tư và thông tin cá nhân của bạn. 
                        Chính sách bảo mật này giải thích cách chúng tôi thu thập, sử dụng, lưu trữ và bảo vệ thông tin của bạn.
                    </p>
                </div>

                <!-- Section 1 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-database text-pink-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">1. Thông Tin Chúng Tôi Thu Thập</h2>
                    </div>
                    <div class="pl-14 space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">1.1. Thông tin cá nhân:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Họ tên, email, số điện thoại</li>
                                <li>Địa chỉ giao hàng và thanh toán</li>
                                <li>Thông tin tài khoản (username, mật khẩu đã mã hóa)</li>
                                <li>Ảnh đại diện (nếu bạn cung cấp)</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">1.2. Thông tin giao dịch:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Lịch sử đặt hàng và thuê váy cưới</li>
                                <li>Thông tin thanh toán (được mã hóa và bảo mật)</li>
                                <li>Lịch hẹn thử váy</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">1.3. Thông tin kỹ thuật:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                                <li>Địa chỉ IP, loại trình duyệt, thiết bị</li>
                                <li>Cookie và dữ liệu phiên làm việc</li>
                                <li>Thời gian truy cập và hành vi sử dụng website</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cogs text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">2. Cách Chúng Tôi Sử Dụng Thông Tin</h2>
                    </div>
                    <div class="pl-14">
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Xử lý đơn hàng và cung cấp dịch vụ thuê váy cưới</li>
                            <li>Liên hệ với bạn về đơn hàng, lịch hẹn và hỗ trợ khách hàng</li>
                            <li>Cải thiện trải nghiệm người dùng và tối ưu hóa website</li>
                            <li>Gửi thông báo về khuyến mãi, sản phẩm mới (nếu bạn đồng ý)</li>
                            <li>Phân tích dữ liệu để cải thiện dịch vụ</li>
                            <li>Bảo vệ chống gian lận và đảm bảo an ninh</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-lock text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">3. Bảo Mật Thông Tin</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700 mb-4">Chúng tôi áp dụng các biện pháp bảo mật nghiêm ngặt:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Mã hóa SSL/TLS cho tất cả dữ liệu truyền tải</li>
                            <li>Mật khẩu được mã hóa bằng thuật toán bcrypt</li>
                            <li>Hệ thống firewall và phần mềm chống virus</li>
                            <li>Giới hạn quyền truy cập dữ liệu chỉ cho nhân viên được ủy quyền</li>
                            <li>Sao lưu dữ liệu định kỳ</li>
                            <li>Tuân thủ các tiêu chuẩn bảo mật quốc tế</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 4 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-share-alt text-green-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">4. Chia Sẻ Thông Tin</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700 mb-4">Chúng tôi <strong>KHÔNG</strong> bán hoặc cho thuê thông tin cá nhân của bạn. Thông tin chỉ được chia sẻ trong các trường hợp:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Với đối tác vận chuyển để giao hàng</li>
                            <li>Với cổng thanh toán để xử lý giao dịch</li>
                            <li>Khi có yêu cầu từ cơ quan pháp luật</li>
                            <li>Với sự đồng ý rõ ràng của bạn</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 5 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cookie-bite text-yellow-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">5. Cookie và Công Nghệ Theo Dõi</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700 mb-4">Chúng tôi sử dụng cookie để:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li>Ghi nhớ thông tin đăng nhập của bạn</li>
                            <li>Lưu giỏ hàng và sở thích</li>
                            <li>Phân tích lưu lượng truy cập website</li>
                            <li>Cá nhân hóa nội dung và quảng cáo</li>
                        </ul>
                        <p class="text-gray-700 mt-4">Bạn có thể tắt cookie trong cài đặt trình duyệt, nhưng điều này có thể ảnh hưởng đến trải nghiệm sử dụng.</p>
                    </div>
                </div>

                <!-- Section 6 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user-shield text-red-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">6. Quyền Của Bạn</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700 mb-4">Bạn có quyền:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-3">
                            <li><strong>Truy cập:</strong> Xem thông tin cá nhân chúng tôi lưu trữ</li>
                            <li><strong>Chỉnh sửa:</strong> Cập nhật thông tin không chính xác</li>
                            <li><strong>Xóa:</strong> Yêu cầu xóa tài khoản và dữ liệu</li>
                            <li><strong>Từ chối:</strong> Không nhận email marketing</li>
                            <li><strong>Di chuyển:</strong> Xuất dữ liệu của bạn</li>
                            <li><strong>Khiếu nại:</strong> Liên hệ với chúng tôi nếu có vấn đề</li>
                        </ul>
                    </div>
                </div>

                <!-- Section 7 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-child text-indigo-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">7. Quyền Riêng Tư Của Trẻ Em</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700">
                            Dịch vụ của chúng tôi không dành cho người dưới 16 tuổi. 
                            Chúng tôi không cố ý thu thập thông tin từ trẻ em. 
                            Nếu bạn là phụ huynh và phát hiện con bạn đã cung cấp thông tin, vui lòng liên hệ với chúng tôi.
                        </p>
                    </div>
                </div>

                <!-- Section 8 -->
                <div class="mb-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-sync-alt text-teal-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">8. Thay Đổi Chính Sách</h2>
                    </div>
                    <div class="pl-14">
                        <p class="text-gray-700">
                            Chúng tôi có thể cập nhật chính sách này theo thời gian. 
                            Mọi thay đổi sẽ được thông báo trên website và qua email. 
                            Việc bạn tiếp tục sử dụng dịch vụ sau khi có thay đổi đồng nghĩa với việc bạn chấp nhận chính sách mới.
                        </p>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-8 mt-12">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-envelope text-pink-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Liên Hệ Với Chúng Tôi</h3>
                            <p class="text-gray-700 mb-4">
                                Nếu bạn có bất kỳ câu hỏi nào về chính sách bảo mật này, vui lòng liên hệ:
                            </p>
                            <div class="space-y-2 text-gray-700">
                                <p><i class="fas fa-building text-pink-600 mr-2"></i><strong><?php echo SITE_NAME; ?></strong></p>
                                <p><i class="fas fa-envelope text-pink-600 mr-2"></i>Email: <?php echo ADMIN_EMAIL; ?></p>
                                <p><i class="fas fa-phone text-pink-600 mr-2"></i>Hotline: 078.797.2075</p>
                                <p><i class="fas fa-map-marker-alt text-pink-600 mr-2"></i>Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
