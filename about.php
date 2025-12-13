<?php
session_start();
require_once 'includes/config.php';

// Load settings helper
if (!function_exists('getSetting')) {
    require_once 'includes/settings-helper.php';
}

// Lấy thông tin liên hệ từ database
$contact_phone = getSetting($conn, 'contact_phone', '078.797.2075');
$contact_email = getSetting($conn, 'contact_email', 'duyphongtv123@gmail.com');
$contact_address = getSetting($conn, 'contact_address', '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh');
$social_zalo = getSetting($conn, 'social_zalo', 'https://zalo.me/0787972075');

$page_title = 'Về Chúng Tôi';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[500px] bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('images/nen.jpg');"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <div class="inline-block mb-4 px-6 py-2 bg-amber-500/20 border border-amber-500/50 rounded-full">
            <span class="text-amber-400 font-semibold text-sm tracking-wider">SINCE 2014</span>
        </div>
        <h1 class="text-6xl font-bold text-white mb-6 tracking-tight">Về Chúng Tôi</h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">Hành trình mang đến vẻ đẹp hoàn hảo cho ngày trọng đại của bạn</p>
    </div>
</section>

<!-- Story Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="relative group">
                <div class="absolute -inset-4 bg-gradient-to-r from-amber-500/20 to-blue-500/20 rounded-3xl blur-2xl group-hover:blur-3xl transition-all"></div>
                <img src="images/gt1.jpg" alt="Váy Cưới Thiên Thần" class="relative rounded-2xl shadow-2xl">
            </div>
            <div>
                <div class="inline-block mb-4 px-4 py-1 bg-amber-100 rounded-full">
                    <span class="text-amber-700 font-semibold text-sm">OUR STORY</span>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-6">Câu Chuyện Của Chúng Tôi</h2>
                <p class="text-gray-600 mb-4 leading-relaxed text-lg">
                    <span class="font-bold text-slate-800">Váy Cưới Thiên Thần</span> được thành lập vào năm 2014 với niềm đam mê mang đến những chiếc váy cưới đẹp nhất cho các cô dâu Việt Nam.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed text-lg">
                    Với hơn <span class="font-bold text-blue-600">10 năm kinh nghiệm</span> trong ngành, chúng tôi tự hào đã đồng hành cùng hơn <span class="font-bold text-blue-600">5,000 cặp đôi</span> trong ngày trọng đại của họ.
                </p>
                <p class="text-gray-600 mb-8 leading-relaxed text-lg">
                    Mỗi chiếc váy cưới tại Váy Cưới Thiên Thần không chỉ là một sản phẩm, mà là một tác phẩm nghệ thuật được chăm chút tỉ mỉ, mang đến vẻ đẹp hoàn hảo và sự tự tin cho mỗi cô dâu.
                </p>
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                        <div class="text-4xl font-bold text-amber-600 mb-1">10+</div>
                        <div class="text-gray-700 text-sm font-medium">Năm kinh nghiệm</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                        <div class="text-4xl font-bold text-blue-600 mb-1">5000+</div>
                        <div class="text-gray-700 text-sm font-medium">Cặp đôi hạnh phúc</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                        <div class="text-4xl font-bold text-emerald-600 mb-1">500+</div>
                        <div class="text-gray-700 text-sm font-medium">Mẫu váy đa dạng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-20 bg-gradient-to-br from-pink-50 to-purple-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <div class="inline-block mb-4 px-4 py-1 bg-pink-100 rounded-full">
                <span class="text-pink-700 font-semibold text-sm">OUR VALUES</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Sứ Mệnh & Tầm Nhìn</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Những giá trị cốt lõi định hướng hành trình của chúng tôi</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Card 1: Sứ Mệnh -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-48 overflow-hidden">
                    <img src="images/sm1.jpg" alt="Sứ mệnh" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-pink-600/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Sứ Mệnh</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Mang đến những chiếc váy cưới đẹp nhất, giúp mỗi cô dâu tỏa sáng và tự tin nhất trong ngày trọng đại của mình.
                    </p>
                </div>
            </div>

            <!-- Card 2: Tầm Nhìn -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-48 overflow-hidden">
                    <img src="images/sm2.jpg" alt="Tầm nhìn" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-purple-600/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Tầm Nhìn</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Trở thành thương hiệu váy cưới hàng đầu Việt Nam, được tin tưởng và yêu thích bởi hàng triệu cô dâu.
                    </p>
                </div>
            </div>

            <!-- Card 3: Giá Trị -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-48 overflow-hidden">
                    <img src="images/sm3.jpg" alt="Giá trị" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-600/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Giá Trị Cốt Lõi</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Chất lượng - Uy tín - Tận tâm. Chúng tôi cam kết mang đến dịch vụ tốt nhất với giá cả hợp lý nhất.
                    </p>
                </div>
            </div>

            <!-- Card 4: Cam Kết -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-48 overflow-hidden">
                    <img src="images/sm4.jpg" alt="Cam kết" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-600/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Cam Kết</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        Đồng hành cùng bạn từ khâu tư vấn, thử váy đến ngày cưới, đảm bảo mọi chi tiết đều hoàn hảo nhất.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <div class="inline-block mb-4 px-4 py-1 bg-purple-100 rounded-full">
                <span class="text-purple-700 font-semibold text-sm">WHY US</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Tại Sao Chọn Chúng Tôi?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Những lý do khiến hàng nghìn cô dâu tin tưởng lựa chọn</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Card 1: Bộ Sưu Tập Đa Dạng -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-56 overflow-hidden">
                    <img src="images/ts1.jpg" alt="Bộ sưu tập đa dạng" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-pink-600/40 to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <div class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <span class="inline-block px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-pink-600">500+ Mẫu Váy</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Bộ Sưu Tập Đa Dạng</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Hơn 500 mẫu váy cưới từ cổ điển đến hiện đại, phù hợp mọi phong cách và vóc dáng</p>
                </div>
            </div>

            <!-- Card 2: Chất Lượng Cao Cấp -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-56 overflow-hidden">
                    <img src="images/ts2.jpg" alt="Chất lượng cao cấp" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-purple-600/40 to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <div class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <span class="inline-block px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-purple-600">Premium Quality</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Chất Lượng Cao Cấp</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Váy được nhập khẩu và may đo bởi những thợ thủ công lành nghề với chất liệu cao cấp</p>
                </div>
            </div>

            <!-- Card 3: Tư Vấn Tận Tâm -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-56 overflow-hidden">
                    <img src="images/ts3.webp" alt="Tư vấn tận tâm" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-600/40 to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <div class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <span class="inline-block px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-blue-600">24/7 Support</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Tư Vấn Tận Tâm</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Đội ngũ tư vấn chuyên nghiệp, nhiệt tình hỗ trợ 24/7 để bạn có trải nghiệm tốt nhất</p>
                </div>
            </div>

            <!-- Card 4: Giá Cả Hợp Lý -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="relative h-56 overflow-hidden">
                    <img src="images/ts4.jpg" alt="Giá cả hợp lý" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-600/40 to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <div class="w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <span class="inline-block px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-emerald-600">Best Price</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Giá Cả Hợp Lý</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Chính sách giá minh bạch, nhiều ưu đãi hấp dẫn và chương trình khuyến mãi đặc biệt</p>
                </div>
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
                <div class="relative mb-4 overflow-hidden rounded-2xl bg-white">
                    <img src="images/t. vu.jpg" alt="Team" class="w-full h-80 object-contain group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Nguyễn Huỳnh Kỹ Thuật</h3>
                <p class="text-pink-600 font-medium mb-2">Giám Đốc Sáng Lập</p>
                <p class="text-gray-600 text-sm">10 năm kinh nghiệm trong ngành thời trang cưới</p>
            </div>
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl bg-white">
                    <img src="images/T.VY.jpg" alt="Team" class="w-full h-80 object-contain group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Hứa Thị Thảo Vy</h3>
                <p class="text-pink-600 font-medium mb-2">Trưởng Phòng Thiết Kế</p>
                <p class="text-gray-600 text-sm">Chuyên gia thiết kế váy cưới cao cấp</p>
            </div>
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl bg-white">
                    <img src="images/truong.jpg" alt="Team" class="w-full h-80 object-contain group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Nguyễn Nhật Trường</h3>
                <p class="text-pink-600 font-medium mb-2">Trưởng Phòng Tư Vấn</p>
                <p class="text-gray-600 text-sm">Tư vấn viên hàng đầu với hơn 3000 khách hàng</p>
            </div>
            <div class="text-center group">
                <div class="relative mb-4 overflow-hidden rounded-2xl bg-white">
                    <img src="images/HTL.png" alt="Team" class="w-full h-80 object-contain group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1">Hoàng Thục Linh</h3>
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

<!-- Contact Cards Section -->
<section class="py-20 bg-gradient-to-br from-white via-pink-50 to-purple-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Liên Hệ Với Chúng Tôi</h2>
            <p class="text-gray-600 text-lg">Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
        </div>
        
        <!-- Contact Cards Component -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Contact Card 1 - Hotline -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fas fa-phone-alt text-3xl text-primary"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Hotline</h3>
                <a href="tel:<?php echo str_replace(['.', ' '], '', $contact_phone); ?>" class="text-primary text-lg font-semibold hover:underline"><?php echo htmlspecialchars($contact_phone); ?></a>
                <p class="text-gray-600 mt-2 text-sm">Hỗ trợ 24/7</p>
            </div>
            
            <!-- Contact Card 2 - Zalo -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fab fa-whatsapp text-3xl text-accent"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Zalo</h3>
                <a href="<?php echo htmlspecialchars($social_zalo); ?>" class="text-accent text-lg font-semibold hover:underline" target="_blank"><?php echo htmlspecialchars($contact_phone); ?></a>
                <p class="text-gray-600 mt-2 text-sm">Chat nhanh</p>
            </div>
            
            <!-- Contact Card 3 - Email -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fas fa-envelope text-3xl text-secondary"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Email</h3>
                <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="text-secondary text-lg font-semibold hover:underline break-all"><?php echo htmlspecialchars($contact_email); ?></a>
                <p class="text-gray-600 mt-2 text-sm">Hỗ trợ email</p>
            </div>
            
            <!-- Contact Card 4 - Địa chỉ -->
            <div class="bg-gradient-to-br from-red-50 to-orange-100 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md">
                    <i class="fas fa-map-marker-alt text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Địa chỉ</h3>
                <p class="text-gray-700 text-sm leading-relaxed"><?php echo htmlspecialchars($contact_address); ?></p>
                <p class="text-gray-600 mt-2 text-sm">Ghé thăm</p>
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
