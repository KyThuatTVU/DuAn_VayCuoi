<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Về Chúng Tôi';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[400px] bg-gradient-to-r from-pink-100 to-purple-100 flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('images/vay1.jpg');"></div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-5xl font-bold text-gray-800 mb-4">Về Chúng Tôi</h1>
        <p class="text-xl text-gray-600">Hành trình mang đến vẻ đẹp hoàn hảo cho ngày trọng đại của bạn</p>
    </div>
</section>

<!-- Story Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <img src="images/vay2.jpg" alt="Váy Cưới Thiên Thần" class="rounded-2xl shadow-2xl">
            </div>
            <div>
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Câu Chuyện Của Chúng Tôi</h2>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    <span class="font-bold text-pink-600">Váy Cưới Thiên Thần</span> được thành lập vào năm 2014 với niềm đam mê mang đến những chiếc váy cưới đẹp nhất cho các cô dâu Việt Nam.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Với hơn <span class="font-bold text-pink-600">10 năm kinh nghiệm</span> trong ngành, chúng tôi tự hào đã đồng hành cùng hơn <span class="font-bold text-pink-600">5,000 cặp đôi</span> trong ngày trọng đại của họ.
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Mỗi chiếc váy cưới tại Váy Cưới Thiên Thần không chỉ là một sản phẩm, mà là một tác phẩm nghệ thuật được chăm chút tỉ mỉ, mang đến vẻ đẹp hoàn hảo và sự tự tin cho mỗi cô dâu.
                </p>
                <div class="flex gap-4">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-pink-600">10+</div>
                        <div class="text-gray-600 text-sm">Năm kinh nghiệm</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-pink-600">5000+</div>
                        <div class="text-gray-600 text-sm">Cặp đôi hạnh phúc</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-pink-600">500+</div>
                        <div class="text-gray-600 text-sm">Mẫu váy đa dạng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-16 bg-gradient-to-br from-pink-50 to-purple-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Sứ Mệnh & Tầm Nhìn</h2>
            <p class="text-gray-600">Những giá trị cốt lõi định hướng hành trình của chúng tôi</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-heart text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Sứ Mệnh</h3>
                <p class="text-gray-600 text-center leading-relaxed">
                    Mang đến những chiếc váy cưới đẹp nhất, giúp mỗi cô dâu tỏa sáng và tự tin nhất trong ngày trọng đại của mình.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-eye text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Tầm Nhìn</h3>
                <p class="text-gray-600 text-center leading-relaxed">
                    Trở thành thương hiệu váy cưới hàng đầu Việt Nam, được tin tưởng và yêu thích bởi hàng triệu cô dâu.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-gem text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Giá Trị</h3>
                <p class="text-gray-600 text-center leading-relaxed">
                    Chất lượng - Uy tín - Tận tâm. Chúng tôi cam kết mang đến dịch vụ tốt nhất với giá cả hợp lý nhất.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Tại Sao Chọn Chúng Tôi?</h2>
            <p class="text-gray-600">Những lý do khiến hàng nghìn cô dâu tin tưởng lựa chọn</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-6 rounded-xl hover:bg-pink-50 transition-colors">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-crown text-pink-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Bộ Sưu Tập Đa Dạng</h3>
                <p class="text-gray-600 text-sm">Hơn 500 mẫu váy cưới từ cổ điển đến hiện đại, phù hợp mọi phong cách</p>
            </div>
            <div class="text-center p-6 rounded-xl hover:bg-pink-50 transition-colors">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-star text-pink-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Chất Lượng Cao Cấp</h3>
                <p class="text-gray-600 text-sm">Váy được nhập khẩu và may đo bởi những thợ thủ công lành nghề</p>
            </div>
            <div class="text-center p-6 rounded-xl hover:bg-pink-50 transition-colors">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-hand-holding-heart text-pink-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Tư Vấn Tận Tâm</h3>
                <p class="text-gray-600 text-sm">Đội ngũ tư vấn chuyên nghiệp, nhiệt tình hỗ trợ 24/7</p>
            </div>
            <div class="text-center p-6 rounded-xl hover:bg-pink-50 transition-colors">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <i class="fas fa-tags text-pink-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Giá Cả Hợp Lý</h3>
                <p class="text-gray-600 text-sm">Chính sách giá minh bạch, nhiều ưu đãi hấp dẫn</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-16 bg-gradient-to-br from-pink-50 to-purple-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Đội Ngũ Của Chúng Tôi</h2>
            <p class="text-gray-600">Những con người tận tâm đằng sau mỗi chiếc váy cưới hoàn hảo</p>
        </div>
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl">
                    <img src="images/vay1.jpg" alt="Team" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Nguyễn Thị Lan</h3>
                <p class="text-pink-600 font-medium mb-2">Giám Đốc Sáng Lập</p>
                <p class="text-gray-600 text-sm">10 năm kinh nghiệm trong ngành thời trang cưới</p>
            </div>
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl">
                    <img src="images/vay2.jpg" alt="Team" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Trần Minh Anh</h3>
                <p class="text-pink-600 font-medium mb-2">Trưởng Phòng Thiết Kế</p>
                <p class="text-gray-600 text-sm">Chuyên gia thiết kế váy cưới cao cấp</p>
            </div>
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl">
                    <img src="images/vay3.jpg" alt="Team" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Lê Thị Hương</h3>
                <p class="text-pink-600 font-medium mb-2">Trưởng Phòng Tư Vấn</p>
                <p class="text-gray-600 text-sm">Tư vấn viên hàng đầu với hơn 3000 khách hàng</p>
            </div>
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl">
                    <img src="images/vay1.jpg" alt="Team" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Phạm Văn Hùng</h3>
                <p class="text-pink-600 font-medium mb-2">Giám Đốc Vận Hành</p>
                <p class="text-gray-600 text-sm">Đảm bảo chất lượng dịch vụ hoàn hảo</p>
            </div>
        </div>
    </div>
</section>

<!-- Achievements -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Thành Tựu & Giải Thưởng</h2>
            <p class="text-gray-600">Những cột mốc đáng tự hào trong hành trình phát triển</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 text-center">
                <i class="fas fa-trophy text-5xl text-pink-600 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Top 10</h3>
                <p class="text-gray-600 text-sm">Thương hiệu váy cưới uy tín nhất TP.HCM 2023</p>
            </div>
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 text-center">
                <i class="fas fa-award text-5xl text-pink-600 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Giải Vàng</h3>
                <p class="text-gray-600 text-sm">Dịch vụ khách hàng xuất sắc 2022</p>
            </div>
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 text-center">
                <i class="fas fa-medal text-5xl text-pink-600 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">5 Sao</h3>
                <p class="text-gray-600 text-sm">Đánh giá trung bình từ 5000+ khách hàng</p>
            </div>
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 text-center">
                <i class="fas fa-certificate text-5xl text-pink-600 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Chứng Nhận</h3>
                <p class="text-gray-600 text-sm">ISO 9001:2015 về chất lượng dịch vụ</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-pink-500 to-purple-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-4">Sẵn Sàng Tìm Chiếc Váy Cưới Hoàn Hảo?</h2>
        <p class="text-xl mb-8 opacity-90">Hãy để chúng tôi đồng hành cùng bạn trong ngày trọng đại</p>
        <div class="flex gap-4 justify-center flex-wrap">
            <a href="products.php" class="bg-white text-pink-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition-colors inline-flex items-center gap-2">
                <i class="fas fa-shopping-bag"></i>
                Xem Bộ Sưu Tập
            </a>
            <a href="booking.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white hover:text-pink-600 transition-colors inline-flex items-center gap-2">
                <i class="fas fa-calendar-check"></i>
                Đặt Lịch Thử Váy
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
