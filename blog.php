<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Tin Tức & Cẩm Nang Cưới';

// Load settings helper
if (!function_exists('getSetting')) {
    require_once 'includes/settings-helper.php';
}

// Lấy thông tin liên hệ từ database
$contact_phone = getSetting($conn, 'contact_phone', "Hotline: 0901 234 567\nTel: (028) 3822 xxxx");
$contact_email = getSetting($conn, 'contact_email', "contact@vaycuoi.com\nsupport@vaycuoi.com");
$contact_address = getSetting($conn, 'contact_address', "123 Đường Nguyễn Huệ\nQuận 1, TP. Hồ Chí Minh");
$social_zalo = getSetting($conn, 'social_zalo', 'https://zalo.me/0901234567');

// Helper để lấy số điện thoại đầu tiên cho link tel:
preg_match('/(\d[\d\s\.\-\(\)]{8,})/', $contact_phone, $matches);
$phone_link = isset($matches[1]) ? preg_replace('/[^0-9]/', '', $matches[1]) : '';

// Helper để lấy email đầu tiên cho link mailto:
preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $contact_email, $matches);
$email_link = isset($matches[0]) ? $matches[0] : '';

// Lấy banner khuyến mãi active
$active_banner = null;
$banner_query = $conn->prepare("SELECT * FROM banner_promotions 
    WHERE is_active = 1 
    AND (start_date IS NULL OR start_date <= NOW()) 
    AND (end_date IS NULL OR end_date >= NOW())
    ORDER BY display_order ASC, created_at DESC 
    LIMIT 1");
$banner_query->execute();
$banner_result = $banner_query->get_result();
if ($banner_result->num_rows > 0) {
    $active_banner = $banner_result->fetch_assoc();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Lấy tổng số bài viết đã publish
$count_sql = "SELECT COUNT(*) as total FROM tin_tuc_cuoi_hoi WHERE status = 'published'";
$count_result = $conn->query($count_sql);
$total_posts = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $limit);

// Lấy bài viết nổi bật (bài mới nhất)
$featured_sql = "SELECT t.*, a.full_name as author_name 
                 FROM tin_tuc_cuoi_hoi t 
                 LEFT JOIN admin a ON t.admin_id = a.id 
                 WHERE t.status = 'published' 
                 ORDER BY t.published_at DESC 
                 LIMIT 1";
$featured_result = $conn->query($featured_sql);
$featured_post = $featured_result->fetch_assoc();

// Lấy danh sách bài viết
$posts_sql = "SELECT t.*, a.full_name as author_name 
              FROM tin_tuc_cuoi_hoi t 
              LEFT JOIN admin a ON t.admin_id = a.id 
              WHERE t.status = 'published' 
              ORDER BY t.published_at DESC 
              LIMIT $limit OFFSET $offset";
$posts_result = $conn->query($posts_sql);

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 py-4">
    <div class="container mx-auto px-4">
        <nav class="flex text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Tin Tức</span>
        </nav>
    </div>
</div>

<!-- Promotional Banner -->
<?php if ($active_banner): ?>
<section class="relative overflow-hidden bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 py-2">
    <div class="container mx-auto px-4">
        <!-- Banner chính với animation -->
        <div class="relative bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 rounded-2xl shadow-xl overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
            </div>
            
            <div class="relative grid md:grid-cols-2 gap-4 items-center p-4 md:p-6">
                <!-- Left Content -->
                <div class="text-white space-y-3 z-10">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full border border-white/30">
                        <svg class="w-5 h-5 text-yellow-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-sm font-semibold">Ưu Đãi Đặc Biệt</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <h2 class="text-2xl md:text-3xl font-bold leading-tight">
                        <?php echo htmlspecialchars($active_banner['title']); ?>
                        <span class="block text-4xl md:text-5xl text-purple-600 mt-1 animate-bounce"><?php echo htmlspecialchars($active_banner['discount_value']); ?></span>
                    </h2>
                    
                    <!-- Subtitle -->
                    <?php if ($active_banner['subtitle']): ?>
                    <p class="text-sm md:text-base text-white/90 leading-relaxed">
                        <?php echo htmlspecialchars($active_banner['subtitle']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Description -->
                    <?php if ($active_banner['description']): ?>
                    <p class="text-sm md:text-base text-white/90 leading-relaxed">
                        <?php echo htmlspecialchars($active_banner['description']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Promo Code -->
                    <?php if ($active_banner['promo_code']): ?>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/30 rounded-xl p-2 max-w-sm">
                        <div class="flex-1">
                            <p class="text-xs text-white/80">Mã giảm giá:</p>
                            <p class="text-lg font-bold tracking-wider"><?php echo htmlspecialchars($active_banner['promo_code']); ?></p>
                        </div>
                        <button onclick="copyPromoCode('<?php echo htmlspecialchars($active_banner['promo_code']); ?>')" class="bg-white text-pink-600 px-4 py-2 rounded-lg font-semibold hover:bg-pink-50 transition-all transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <a href="products.php" class="inline-flex items-center gap-1 bg-white text-pink-600 px-4 py-2 rounded-full font-semibold text-sm hover:bg-pink-50 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span>Xem Bộ Sưu Tập</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="booking.php" class="inline-flex items-center gap-1 bg-white/10 backdrop-blur-sm text-white border border-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-white/20 transition-all transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Đặt Lịch Ngay</span>
                        </a>
                    </div>
                    
                    <!-- Countdown Timer -->
                    <?php if ($active_banner['end_date']): ?>
                    <div class="flex items-center gap-1 text-xs">
                        <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-white/90">Ưu đãi kết thúc trong: <strong class="text-yellow-300" id="countdown-timer-blog">Đang tải...</strong></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right Content - Image/Animation -->
                <div class="relative z-10">
                    <div class="relative">
                        <!-- Floating elements -->
                        <div class="absolute -top-4 -left-4 w-8 h-8 bg-yellow-400 rounded-full animate-bounce opacity-80"></div>
                        <div class="absolute -bottom-4 -right-4 w-6 h-6 bg-pink-400 rounded-full animate-pulse opacity-60"></div>
                        <div class="absolute top-1/2 -right-6 w-4 h-4 bg-purple-400 rounded-full animate-ping opacity-70"></div>
                        
                        <!-- Main image placeholder -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-white/80 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <p class="text-white/90 font-semibold">Váy Cưới Đẳng Cấp</p>
                                <p class="text-white/70 text-sm">Thiết kế độc đáo</p>
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
<?php endif; ?>

<!-- Main Content -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Tin Tức & Cẩm Nang Cưới</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Cập nhật xu hướng và mẹo hay cho ngày cưới hoàn hảo</p>
        </div>

        <?php if ($featured_post): ?>
        <!-- Featured Post -->
        <div class="grid md:grid-cols-2 gap-8 bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl overflow-hidden shadow-lg mb-16 hover:shadow-xl transition-shadow duration-300">
            <div class="relative h-64 md:h-auto">
                <img src="<?php echo htmlspecialchars($featured_post['cover_image'] ?: 'assets/images/blog-default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($featured_post['title']); ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute top-4 left-4">
                    <span class="bg-pink-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                        Nổi Bật
                    </span>
                </div>
            </div>
            <div class="p-8 md:p-12 flex flex-col justify-center">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight hover:text-pink-600 transition">
                    <a href="blog-detail.php?slug=<?php echo htmlspecialchars($featured_post['slug']); ?>">
                        <?php echo htmlspecialchars($featured_post['title']); ?>
                    </a>
                </h2>
                <p class="text-gray-600 mb-6 line-clamp-3 leading-relaxed">
                    <?php echo htmlspecialchars($featured_post['summary'] ?: substr(strip_tags($featured_post['content']), 0, 200) . '...'); ?>
                </p>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo date('d/m/Y', strtotime($featured_post['published_at'])); ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <?php echo htmlspecialchars($featured_post['author_name'] ?: 'Admin'); ?>
                    </span>
                </div>
                <a href="blog-detail.php?slug=<?php echo htmlspecialchars($featured_post['slug']); ?>" 
                   class="inline-flex items-center gap-2 bg-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-pink-700 transition-colors w-fit">
                    Đọc Thêm
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Blog Grid -->
        <?php if ($posts_result->num_rows > 0): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php while($post = $posts_result->fetch_assoc()): ?>
            <article class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($post['cover_image'] ?: 'assets/images/blog-default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    <div class="absolute top-3 right-3 bg-white px-3 py-1 rounded-full text-sm font-semibold text-pink-600 shadow">
                        <?php echo date('d', strtotime($post['published_at'])); ?> Th<?php echo date('m', strtotime($post['published_at'])); ?>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 hover:text-pink-600 transition">
                        <a href="blog-detail.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3 text-sm leading-relaxed">
                        <?php echo htmlspecialchars($post['summary'] ?: substr(strip_tags($post['content']), 0, 120) . '...'); ?>
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            5 phút đọc
                        </span>
                        <a href="blog-detail.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" 
                           class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1 transition">
                            Đọc Thêm
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" 
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-pink-50 hover:border-pink-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="px-4 py-2 rounded-lg bg-pink-600 text-white font-semibold"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-pink-50 hover:border-pink-300 transition">
                        <?php echo $i; ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>" 
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-pink-50 hover:border-pink-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- No Posts -->
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Chưa có bài viết nào</h3>
            <p class="text-gray-600">Vui lòng quay lại sau để xem nội dung mới nhất</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function copyPromoCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Hiển thị thông báo copy thành công
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = 'Đã sao chép mã: ' + code;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    });
}

<?php if ($active_banner && $active_banner['end_date']): ?>
// Countdown timer
function updateCountdown() {
    const endDate = new Date('<?php echo $active_banner['end_date']; ?>').getTime();
    const now = new Date().getTime();
    const distance = endDate - now;
    
    if (distance > 0) {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        
        let countdownText = '';
        if (days > 0) countdownText += days + ' ngày ';
        if (hours > 0) countdownText += hours + ' giờ ';
        if (minutes > 0) countdownText += minutes + ' phút';
        
        document.getElementById('countdown-timer-blog').textContent = countdownText.trim();
    } else {
        document.getElementById('countdown-timer-blog').textContent = 'Đã kết thúc';
    }
}

// Update countdown every minute
updateCountdown();
setInterval(updateCountdown, 60000);
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
