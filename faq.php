<?php
session_start();
require_once 'includes/config.php';

// Load settings helper
if (!function_exists('getSetting')) {
    require_once 'includes/settings-helper.php';
}

// Lấy thông tin liên hệ từ database
$contact_phone = getSetting($conn, 'contact_phone', "Hotline: 0901 234 567\nTel: (028) 3822 xxxx");
$social_zalo = getSetting($conn, 'social_zalo', 'https://zalo.me/0901234567');

// Helper để lấy số điện thoại đầu tiên cho link tel:
preg_match('/(\d[\d\s\.\-\(\)]{8,})/', $contact_phone, $matches);
$phone_link = isset($matches[1]) ? preg_replace('/[^0-9]/', '', $matches[1]) : '';

$page_title = 'Câu Hỏi Thường Gặp';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-pink-600 font-medium">Câu Hỏi Thường Gặp</span>
        </nav>
    </div>
</div>

<!-- FAQ Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-6">
                    <i class="fas fa-question-circle text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Câu Hỏi Thường Gặp</h1>
                <p class="text-lg text-gray-600">Giải đáp những thắc mắc phổ biến của khách hàng</p>
            </div>

            <!-- FAQ Accordion -->
            <div class="space-y-4">
                <!-- Category 1: Thuê Váy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tshirt text-pink-600"></i>
                        </div>
                        Về Thuê Váy Cưới
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Thời gian thuê váy cưới là bao lâu?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Thời gian thuê váy cưới tiêu chuẩn là <strong>3 ngày 2 đêm</strong>. Nếu bạn cần thuê lâu hơn, vui lòng liên hệ với chúng tôi để được báo giá phù hợp. Chúng tôi cũng có gói thuê theo tuần với mức giá ưu đãi.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Tôi có thể thử váy trước khi thuê không?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Có, bạn hoàn toàn có thể đặt lịch thử váy tại showroom của chúng tôi. Vui lòng đặt lịch trước qua website hoặc hotline để được phục vụ tốt nhất. Mỗi buổi thử váy kéo dài khoảng 1-2 giờ.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Váy có được giặt sạch trước khi giao không?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Tất cả váy cưới đều được giặt hấp chuyên nghiệp, kiểm tra kỹ lưỡng và đóng gói cẩn thận trước khi giao đến tay khách hàng. Chúng tôi cam kết váy luôn trong tình trạng sạch sẽ và hoàn hảo nhất.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Nếu váy không vừa thì sao?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Chúng tôi cung cấp dịch vụ sửa váy miễn phí cho các điều chỉnh nhỏ (bóp eo, nới rộng trong phạm vi cho phép). Đối với các thay đổi lớn hơn, sẽ có phí phát sinh tùy theo mức độ. Vì vậy, chúng tôi khuyến khích bạn thử váy trước khi quyết định.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 2: Đặt Hàng & Thanh Toán -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-purple-600"></i>
                        </div>
                        Đặt Hàng & Thanh Toán
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Tôi cần đặt cọc bao nhiêu?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Tiền đặt cọc thường là <strong>30-50%</strong> giá trị đơn hàng, tùy thuộc vào loại váy và thời gian thuê. Số tiền còn lại sẽ thanh toán khi nhận váy. Đặt cọc giúp đảm bảo váy được giữ cho bạn.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Có những hình thức thanh toán nào?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Chúng tôi chấp nhận nhiều hình thức thanh toán: Tiền mặt, Chuyển khoản ngân hàng, Ví MoMo, VNPay, và các thẻ Visa/Mastercard. Bạn có thể chọn phương thức phù hợp nhất khi thanh toán.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Tôi có thể hủy đơn hàng không?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Bạn có thể hủy đơn hàng trước <strong>7 ngày</strong> so với ngày nhận váy và được hoàn lại 100% tiền cọc. Hủy trong vòng 3-7 ngày sẽ được hoàn 50%. Hủy dưới 3 ngày sẽ không được hoàn tiền cọc.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 3: Giao Nhận -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-truck text-blue-600"></i>
                        </div>
                        Giao Nhận & Hoàn Trả
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Có giao váy tận nơi không?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Có, chúng tôi cung cấp dịch vụ giao váy tận nơi trong nội thành TP.HCM với phí ship từ 50.000đ - 100.000đ tùy khu vực. Đối với các tỉnh thành khác, chúng tôi gửi qua dịch vụ chuyển phát nhanh.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Trả váy như thế nào?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Bạn có thể trả váy trực tiếp tại showroom hoặc đặt lịch để nhân viên đến nhận. Váy cần được trả đúng hạn, nếu trả trễ sẽ tính phí phạt 200.000đ/ngày. Bạn không cần giặt váy trước khi trả.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Nếu váy bị hư hỏng thì sao?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Các hư hỏng nhỏ như sút chỉ, bẩn nhẹ sẽ được chúng tôi xử lý miễn phí. Đối với hư hỏng nặng (rách, cháy, mất phụ kiện), bạn sẽ phải bồi thường theo mức độ thiệt hại. Vui lòng bảo quản váy cẩn thận trong thời gian sử dụng.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 4: Khác -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ellipsis-h text-green-600"></i>
                        </div>
                        Câu Hỏi Khác
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Có cho thuê phụ kiện đi kèm không?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Có, chúng tôi cho thuê đầy đủ phụ kiện: khăn voan, vương miện, găng tay, hoa cưới, giày cưới... Khi thuê váy, bạn sẽ được tư vấn phụ kiện phù hợp với mức giá ưu đãi.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Có dịch vụ may váy theo yêu cầu không?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Có, ngoài dịch vụ cho thuê, chúng tôi còn nhận may váy cưới theo thiết kế riêng. Thời gian may từ 2-4 tuần tùy độ phức tạp. Vui lòng liên hệ để được tư vấn chi tiết.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button class="faq-toggle w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                                <span class="font-semibold text-gray-800">Showroom mở cửa giờ nào?</span>
                                <i class="fas fa-chevron-down text-pink-500 transition-transform"></i>
                            </button>
                            <div class="faq-content hidden px-6 pb-4">
                                <p class="text-gray-600 leading-relaxed">
                                    Showroom mở cửa từ <strong>8:00 - 20:00</strong> tất cả các ngày trong tuần, kể cả ngày lễ. Để được phục vụ tốt nhất, bạn nên đặt lịch hẹn trước qua hotline hoặc website.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-8 mt-12">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                            <i class="fas fa-headset text-pink-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Không Tìm Thấy Câu Trả Lời?</h3>
                        <p class="text-gray-700 mb-4">
                            Nếu bạn có câu hỏi khác, đừng ngần ngại liên hệ với chúng tôi:
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="contact.php" class="inline-flex items-center gap-2 bg-pink-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-700 transition">
                                <i class="fas fa-envelope"></i>
                                Liên Hệ Ngay
                            </a>
                            <a href="tel:<?php echo $phone_link; ?>" class="inline-flex items-center gap-2 bg-white text-pink-600 px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition border border-pink-200">
                                <i class="fas fa-phone"></i>
                                <?php echo nl2br(htmlspecialchars($contact_phone)); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.faq-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('i');
            
            // Close all other FAQs
            toggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    otherToggle.nextElementSibling.classList.add('hidden');
                    otherToggle.querySelector('i').classList.remove('rotate-180');
                }
            });
            
            // Toggle current FAQ
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
