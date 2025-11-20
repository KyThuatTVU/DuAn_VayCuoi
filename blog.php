<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Tin Tức & Cẩm Nang Cưới';

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

<?php require_once 'includes/footer.php'; ?>
