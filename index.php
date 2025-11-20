<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<!-- Promotional Banner -->
<section class="relative overflow-hidden bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 py-4">
    <div class="container mx-auto px-4">
        <!-- Banner chính với animation -->
        <div class="relative bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 rounded-3xl shadow-2xl overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
            </div>
            
            <div class="relative grid md:grid-cols-2 gap-8 items-center p-8 md:p-12">
                <!-- Left Content -->
                <div class="text-white space-y-6 z-10">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full border border-white/30">
                        <svg class="w-5 h-5 text-yellow-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-sm font-semibold">Ưu Đãi Đặc Biệt</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <h2 class="text-4xl md:text-5xl font-bold leading-tight">
                        Giảm Giá Lên Đến
                        <span class="block text-6xl md:text-7xl text-yellow-300 mt-2 animate-bounce">30%</span>
                    </h2>
                    
                    <!-- Description -->
                    <p class="text-lg md:text-xl text-white/90 leading-relaxed">
                        Cho tất cả các mẫu váy cưới cao cấp. Đặt lịch ngay hôm nay để nhận ưu đãi!
                    </p>
                    
                    <!-- Promo Code -->
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md border-2 border-white/30 rounded-2xl p-4 max-w-md">
                        <div class="flex-1">
                            <p class="text-sm text-white/80 mb-1">Mã giảm giá:</p>
                            <p class="text-2xl font-bold tracking-wider">WEDDING2024</p>
                        </div>
                        <button onclick="copyPromoCode()" class="bg-white text-pink-600 px-6 py-3 rounded-xl font-semibold hover:bg-pink-50 transition-all transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="products.php" class="inline-flex items-center gap-2 bg-white text-pink-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-pink-50 transition-all transform hover:scale-105 shadow-xl hover:shadow-2xl">
                            <span>Xem Bộ Sưu Tập</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="booking.php" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white border-2 border-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Đặt Lịch Thử</span>
                        </a>
                    </div>
                    
                    <!-- Countdown Timer -->
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-white/90">Ưu đãi kết thúc trong: <strong class="text-yellow-300">3 ngày 12 giờ</strong></span>
                    </div>
                </div>
                
                <!-- Right Image -->
                <div class="relative z-10 hidden md:block">
                    <div class="relative">
                        <!-- Decorative circles -->
                        <div class="absolute -top-4 -right-4 w-24 h-24 bg-yellow-300 rounded-full opacity-50 animate-ping"></div>
                        <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-white rounded-full opacity-20"></div>
                        
                        <!-- Main image -->
                        <div class="relative bg-white/10 backdrop-blur-sm rounded-3xl p-4 border-4 border-white/30 shadow-2xl transform hover:scale-105 transition-transform duration-300">
                            <img src="images/ad.png" alt="Váy cưới khuyến mãi" class="rounded-2xl w-full h-96 object-cover shadow-xl">
                            
                            <!-- Floating badge -->
                            <div class="absolute -top-6 -left-6 bg-gradient-to-br from-yellow-400 to-orange-500 text-white px-6 py-3 rounded-2xl shadow-2xl transform rotate-12 animate-bounce">
                                <p class="text-sm font-semibold">Giảm ngay</p>
                                <p class="text-3xl font-bold">30%</p>
                            </div>
                            
                            <!-- Stats badges -->
                            <div class="absolute -bottom-4 -right-4 bg-white rounded-2xl p-4 shadow-2xl">
                                <div class="flex items-center gap-2">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-pink-200 border-2 border-white"></div>
                                        <div class="w-8 h-8 rounded-full bg-purple-200 border-2 border-white"></div>
                                        <div class="w-8 h-8 rounded-full bg-blue-200 border-2 border-white"></div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-xs text-gray-500">Đã đặt hôm nay</p>
                                        <p class="text-sm font-bold text-gray-800">127+ khách hàng</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom features bar -->
            <div class="relative border-t border-white/20 bg-white/5 backdrop-blur-sm">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6">
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Miễn phí thử váy</p>
                            <p class="text-xs text-white/70">Tại showroom</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Giao hàng nhanh</p>
                            <p class="text-xs text-white/70">Trong 24h</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Bảo đảm chất lượng</p>
                            <p class="text-xs text-white/70">100% hài lòng</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Giá cả hợp lý</p>
                            <p class="text-xs text-white/70">Nhiều ưu đãi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copyPromoCode() {
    const code = 'WEDDING2024';
    navigator.clipboard.writeText(code).then(() => {
        // Show success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        btn.classList.add('bg-green-500', 'text-white');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-500', 'text-white');
        }, 2000);
    });
}
</script>

<!-- Featured Categories -->
<section class="py-20 bg-gradient-to-br from-white via-pink-50 to-purple-50 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-pink-200 rounded-full opacity-20 blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-200 rounded-full opacity-20 blur-3xl translate-x-1/2 translate-y-1/2"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-16 space-y-4">
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-100 to-purple-100 px-6 py-2 rounded-full border border-pink-200">
                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-sm font-semibold text-pink-700">Bộ Sưu Tập</span>
            </div>
            <h2 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-pink-600 via-rose-600 to-purple-600 bg-clip-text text-transparent">
                Phong Cách Váy Cưới
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Chọn phong cách phù hợp với cá tính của bạn - Mỗi chiếc váy là một câu chuyện tình yêu
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Category 1: Váy Công Chúa -->
            <a href="products.php?cat=princess" class="group relative overflow-hidden rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                <div class="relative h-96 overflow-hidden">
                    <img src="images/vay1.jpg" alt="Váy công chúa" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <!-- Gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>
                    
                    <!-- Decorative corner -->
                    <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-br from-pink-400 to-rose-500 rounded-full flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="absolute bottom-0 left-0 right-0 p-6 transform transition-transform duration-300 group-hover:translate-y-0">
                        <div class="space-y-3">
                            <h3 class="text-3xl font-bold text-white">Váy Công Chúa</h3>
                            <p class="text-white/90 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Lộng lẫy, quyền quý như một nàng công chúa thực thụ
                            </p>
                            <div class="flex items-center gap-2 text-white font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                <span>Khám phá ngay</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Category 2: Váy Đuôi Cá -->
            <a href="products.php?cat=mermaid" class="group relative overflow-hidden rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                <div class="relative h-96 overflow-hidden">
                    <img src="images/vay2.jpg" alt="Váy đuôi cá" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>
                    
                    <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-full flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>
                    </div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 transform transition-transform duration-300 group-hover:translate-y-0">
                        <div class="space-y-3">
                            <h3 class="text-3xl font-bold text-white">Váy Đuôi Cá</h3>
                            <p class="text-white/90 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Quyến rũ, gợi cảm, tôn dáng hoàn hảo
                            </p>
                            <div class="flex items-center gap-2 text-white font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                <span>Khám phá ngay</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Category 3: Váy Chữ A -->
            <a href="products.php?cat=aline" class="group relative overflow-hidden rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                <div class="relative h-96 overflow-hidden">
                    <img src="images/vay3.jpg" alt="Váy chữ A" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>
                    
                    <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-br from-rose-400 to-pink-500 rounded-full flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 transform transition-transform duration-300 group-hover:translate-y-0">
                        <div class="space-y-3">
                            <h3 class="text-3xl font-bold text-white">Váy Chữ A</h3>
                            <p class="text-white/90 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Thanh lịch, duyên dáng, phù hợp mọi vóc dáng
                            </p>
                            <div class="flex items-center gap-2 text-white font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                <span>Khám phá ngay</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Category 4: Váy Hiện Đại -->
            <a href="products.php?cat=modern" class="group relative overflow-hidden rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                <div class="relative h-96 overflow-hidden">
                    <img src="images/vay4.jpg" alt="Váy hiện đại" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>
                    
                    <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6 transform transition-transform duration-300 group-hover:translate-y-0">
                        <div class="space-y-3">
                            <h3 class="text-3xl font-bold text-white">Váy Hiện Đại</h3>
                            <p class="text-white/90 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Tinh tế, tối giản, phong cách đương đại
                            </p>
                            <div class="flex items-center gap-2 text-white font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                <span>Khám phá ngay</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-16">
            <div class="inline-flex flex-col items-center gap-4 bg-white rounded-3xl p-8 shadow-xl border border-pink-100">
                <p class="text-gray-600 text-lg">Không tìm thấy phong cách phù hợp?</p>
                <a href="products.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-pink-600 hover:to-rose-600 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <span>Xem Tất Cả Bộ Sưu Tập</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Váy Cưới Nổi Bật</h2>
            <p class="text-gray-600 text-lg">Những mẫu váy được yêu thích nhất</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Sản phẩm 1 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay1.jpg" alt="Váy Công Chúa Lộng Lẫy" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg">
                        Mới
                    </div>
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors" title="Yêu thích">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors" title="Xem nhanh">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Công Chúa Lộng Lẫy</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <span class="text-gray-500 text-sm">(45 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">5.500.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=1" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">
                            Chi Tiết
                        </a>
                        <a href="booking.php?id=1" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">
                            Đặt Lịch
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 2 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay2.jpg" alt="Váy Đuôi Cá Quyến Rũ" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-red-500 to-orange-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg animate-pulse">
                        Hot
                    </div>
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors" title="Yêu thích">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors" title="Xem nhanh">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Đuôi Cá Quyến Rũ</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <span class="text-gray-500 text-sm">(38 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">6.200.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=2" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">
                            Chi Tiết
                        </a>
                        <a href="booking.php?id=2" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">
                            Đặt Lịch
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 3 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay3.jpg" alt="Váy Chữ A Thanh Lịch" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Chữ A Thanh Lịch</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
                        <span class="text-gray-500 text-sm">(32 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">4.800.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=3" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">Chi Tiết</a>
                        <a href="booking.php?id=3" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">Đặt Lịch</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 4 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay4.jpg" alt="Váy Hiện Đại Tinh Tế" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg">Mới</div>
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Hiện Đại Tinh Tế</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
                        <span class="text-gray-500 text-sm">(28 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">5.000.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=4" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">Chi Tiết</a>
                        <a href="booking.php?id=4" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">Đặt Lịch</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 5 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay5.jpg" alt="Váy Ren Cổ Điển" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Ren Cổ Điển</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg"><span>★</span><span>★</span><span>★</span><span>★</span><span class="text-gray-300">★</span></div>
                        <span class="text-gray-500 text-sm">(25 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">4.500.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=5" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">Chi Tiết</a>
                        <a href="booking.php?id=5" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">Đặt Lịch</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 6 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay6.jpg" alt="Váy Xòe Lãng Mạn" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-red-500 to-orange-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg animate-pulse">Hot</div>
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Xòe Lãng Mạn</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
                        <span class="text-gray-500 text-sm">(41 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">5.800.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=6" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">Chi Tiết</a>
                        <a href="booking.php?id=6" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">Đặt Lịch</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 7 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay7.jpg" alt="Váy Tối Giản Sang Trọng" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Tối Giản Sang Trọng</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
                        <span class="text-gray-500 text-sm">(35 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">4.200.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=7" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">Chi Tiết</a>
                        <a href="booking.php?id=7" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">Đặt Lịch</a>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 8 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="images/vay8.jpg" alt="Váy Dạ Hội Cao Cấp" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg">Mới</div>
                    <div class="absolute top-4 left-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button>
                        <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Váy Dạ Hội Cao Cấp</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex text-yellow-400 text-lg"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
                        <span class="text-gray-500 text-sm">(52 đánh giá)</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-pink-600">7.500.000đ</span>
                        <span class="text-gray-500 text-sm ml-2">/ ngày thuê</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="product-detail.php?id=8" class="flex-1 text-center px-4 py-2.5 border-2 border-pink-500 text-pink-600 rounded-lg font-semibold hover:bg-pink-50 transition-colors">Chi Tiết</a>
                        <a href="booking.php?id=8" class="flex-1 text-center px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-600 transition-all shadow-md">Đặt Lịch</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-12">
            <a href="products.php" class="inline-block px-8 py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full font-semibold text-lg hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Xem Tất Cả Váy Cưới →
            </a>
        </div>
    </div>
</section>

<!-- Promotion Banner -->
<section class="promo-banner">
    <div class="container">
        <div class="promo-content">
            <div class="promo-text">
                <span class="promo-label">Ưu Đãi Đặc Biệt</span>
                <h2>Giảm 20% Cho Đơn Hàng Đầu Tiên</h2>
                <p>Sử dụng mã: <strong>FIRSTLOVE</strong> khi thanh toán</p>
                <a href="products.php" class="btn btn-white">Thuê Ngay</a>
            </div>
            <div class="promo-image">
                <img src="images/banner.png" alt="Khuyến mãi">
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section class="py-16 bg-gradient-to-br from-pink-50 via-white to-purple-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Service 1: Thử Váy Miễn Phí -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-pink-100">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-pink-400 to-pink-600 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3 text-center">Thử Váy Miễn Phí</h3>
                <p class="text-gray-600 text-center leading-relaxed">Đặt lịch thử váy tại showroom không mất phí</p>
            </div>

            <!-- Service 2: May Đo Theo Yêu Cầu -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-purple-100">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3 text-center">May Đo Theo Yêu Cầu</h3>
                <p class="text-gray-600 text-center leading-relaxed">Chỉnh sửa váy vừa vặn với số đo của bạn</p>
            </div>

            <!-- Service 3: Giao Hàng Tận Nơi -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-blue-100">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3 text-center">Giao Hàng Tận Nơi</h3>
                <p class="text-gray-600 text-center leading-relaxed">Miễn phí giao hàng trong nội thành</p>
            </div>

            <!-- Service 4: Tư Vấn 24/7 -->
            <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-rose-100">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-rose-400 to-rose-600 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3 text-center">Tư Vấn 24/7</h3>
                <p class="text-gray-600 text-center leading-relaxed">Đội ngũ chuyên viên sẵn sàng hỗ trợ</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-gradient-to-b from-white to-pink-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Khách Hàng Nói Gì Về Chúng Tôi</h2>
            <p class="text-gray-600 text-lg">Những trải nghiệm thực tế từ các cô dâu</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-pink-100">
                <div class="flex items-center justify-center mb-4">
                    <div class="flex text-yellow-400 text-2xl">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                </div>
                <div class="relative mb-6">
                    <svg class="absolute -top-2 -left-2 w-8 h-8 text-pink-200 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <p class="text-gray-700 leading-relaxed italic pl-6">"Váy cưới tuyệt đẹp, chất lượng cao cấp. Nhân viên tư vấn rất nhiệt tình và chuyên nghiệp. Mình rất hài lòng với dịch vụ!"</p>
                </div>
                <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-pink-100">
                        NA
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">Nguyễn Thị An</h4>
                        <span class="text-gray-500 text-sm">Cô dâu 2024</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-purple-100">
                <div class="flex items-center justify-center mb-4">
                    <div class="flex text-yellow-400 text-2xl">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                </div>
                <div class="relative mb-6">
                    <svg class="absolute -top-2 -left-2 w-8 h-8 text-purple-200 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <p class="text-gray-700 leading-relaxed italic pl-6">"Showroom rất đẹp, nhiều mẫu váy đa dạng. Giá cả hợp lý, dịch vụ chỉnh sửa váy rất tốt. Chắc chắn sẽ giới thiệu cho bạn bè!"</p>
                </div>
                <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-purple-100">
                        TC
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">Trần Minh Châu</h4>
                        <span class="text-gray-500 text-sm">Cô dâu 2023</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-rose-100">
                <div class="flex items-center justify-center mb-4">
                    <div class="flex text-yellow-400 text-2xl">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                </div>
                <div class="relative mb-6">
                    <svg class="absolute -top-2 -left-2 w-8 h-8 text-rose-200 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <p class="text-gray-700 leading-relaxed italic pl-6">"Mình đã thử nhiều nơi nhưng chỉ có ở đây mới tìm được chiếc váy ưng ý. Cảm ơn team đã giúp mình có một đám cưới hoàn hảo!"</p>
                </div>
                <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-rose-100">
                        LG
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">Lê Hương Giang</h4>
                        <span class="text-gray-500 text-sm">Cô dâu 2024</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="py-20 bg-gradient-to-b from-pink-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Tin Tức & Cẩm Nang Cưới</h2>
            <p class="text-gray-600 text-lg">Cập nhật xu hướng và mẹo hay cho ngày cưới</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Blog 1 -->
            <article class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="relative overflow-hidden h-64">
                    <!-- Placeholder Image with Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-400 via-rose-400 to-purple-500 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <img src="images/cn1.jpg" alt="Blog" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.display='none'">
                    <div class="absolute top-4 right-4 bg-white rounded-full px-4 py-2 shadow-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">15 Th11</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-gradient-to-r from-pink-100 to-rose-100 text-pink-600 rounded-full text-sm font-semibold mb-4">
                        Xu Hướng
                    </span>
                    <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-pink-600 transition-colors">
                        Top 10 Mẫu Váy Cưới Hot Nhất 2024
                    </h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Khám phá những xu hướng váy cưới được yêu thích nhất trong năm nay...
                    </p>
                    <a href="blog-detail.php?id=1" class="inline-flex items-center gap-2 text-pink-600 font-semibold hover:gap-3 transition-all">
                        Đọc Thêm 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog 2 -->
            <article class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="relative overflow-hidden h-64">
                    <!-- Placeholder Image with Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400 via-pink-400 to-rose-500 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <img src="images/cn2.jpg"" alt="Blog" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.display='none'">
                    <div class="absolute top-4 right-4 bg-white rounded-full px-4 py-2 shadow-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">10 Th11</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-600 rounded-full text-sm font-semibold mb-4">
                        Cẩm Nang
                    </span>
                    <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-pink-600 transition-colors">
                        Cách Chọn Váy Cưới Phù Hợp Với Dáng Người
                    </h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Mỗi dáng người sẽ phù hợp với một kiểu váy khác nhau. Cùng tìm hiểu...
                    </p>
                    <a href="blog-detail.php?id=2" class="inline-flex items-center gap-2 text-pink-600 font-semibold hover:gap-3 transition-all">
                        Đọc Thêm 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Blog 3 -->
            <article class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="relative overflow-hidden h-64">
                    <!-- Placeholder Image with Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-400 via-pink-400 to-orange-400 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <img src="images/cn3.jpg"" alt="Blog" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.display='none'">
                    <div class="absolute top-4 right-4 bg-white rounded-full px-4 py-2 shadow-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">05 Th11</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <span class="inline-block px-3 py-1 bg-gradient-to-r from-orange-100 to-rose-100 text-orange-600 rounded-full text-sm font-semibold mb-4">
                        Mẹo Hay
                    </span>
                    <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-pink-600 transition-colors">
                        Checklist Chuẩn Bị Váy Cưới Cho Cô Dâu
                    </h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Những điều cần lưu ý khi thuê và sử dụng váy cưới để có ngày cưới hoàn hảo...
                    </p>
                    <a href="blog-detail.php?id=3" class="inline-flex items-center gap-2 text-pink-600 font-semibold hover:gap-3 transition-all">
                        Đọc Thêm 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
