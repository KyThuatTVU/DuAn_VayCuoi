<?php
session_start();
require_once 'includes/config.php';

// Lấy slug từ URL
$slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: blog.php');
    exit();
}

// Lấy thông tin bài viết
$sql = "SELECT t.*, a.full_name as author_name, km.title as promotion_title, km.description as promotion_description, km.type as promotion_type, km.value as promotion_value
        FROM tin_tuc_cuoi_hoi t 
        LEFT JOIN admin a ON t.admin_id = a.id 
        LEFT JOIN khuyen_mai km ON t.promotion_code = km.code AND km.start_at <= NOW() AND km.end_at >= NOW()
        WHERE t.slug = ? AND t.status = 'published'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: blog.php');
    exit();
}

$post = $result->fetch_assoc();
$page_title = $post['title'];

// Lấy bài viết liên quan
$related_sql = "SELECT * FROM tin_tuc_cuoi_hoi 
                WHERE status = 'published' AND id != ? 
                ORDER BY published_at DESC 
                LIMIT 3";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param("i", $post['id']);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 py-4">
    <div class="container mx-auto px-4">
        <nav class="flex text-sm text-gray-600">
            <a href="index.php" class="hover:text-pink-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <a href="blog.php" class="hover:text-pink-600 transition">Tin Tức</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 truncate"><?php echo htmlspecialchars($post['title']); ?></span>
        </nav>
    </div>
</div>

<!-- Article Content -->
<article class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Article Header -->
            <header class="mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                
                <div class="flex flex-wrap items-center gap-6 text-gray-600 mb-8">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span><?php echo htmlspecialchars($post['author_name'] ?: 'Admin'); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span><?php echo date('d/m/Y', strtotime($post['published_at'])); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>5 phút đọc</span>
                    </div>
                </div>

                <?php if ($post['summary']): ?>
                <div class="bg-pink-50 border-l-4 border-pink-600 p-6 rounded-r-lg mb-8">
                    <p class="text-lg text-gray-700 leading-relaxed">
                        <?php echo htmlspecialchars($post['summary']); ?>
                    </p>
                </div>
                <?php endif; ?>
            </header>

            <!-- Featured Image -->
            <?php if ($post['cover_image']): ?>
            <div class="mb-10 rounded-2xl overflow-hidden shadow-lg">
                <img src="<?php echo htmlspecialchars($post['cover_image']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                     class="w-full h-auto">
            </div>
            <?php endif; ?>

            <!-- Promotion Banner -->
            <?php if (!empty($post['promotion_code'])): ?>
            <div class="mb-8 bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-gift text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($post['promotion_title']); ?></h3>
                        <p class="mb-3 opacity-90"><?php echo htmlspecialchars($post['promotion_description']); ?></p>
                        <div class="flex items-center gap-4">
                            <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                                <span class="font-bold text-lg">
                                    <?php if ($post['promotion_type'] === 'percent'): ?>
                                        <?php echo $post['promotion_value']; ?>%
                                    <?php else: ?>
                                        <?php echo number_format($post['promotion_value'], 0, ',', '.'); ?> VNĐ
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                                <span class="font-semibold">Mã: <?php echo htmlspecialchars($post['promotion_code']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Article Body -->
            <div class="prose prose-lg max-w-none mb-12">
                <div class="text-gray-700 leading-relaxed space-y-4">
                    <?php echo nl2br($post['content']); ?>
                </div>
            </div>

            <!-- Share Buttons -->
            <div class="border-t border-b border-gray-200 py-6 mb-12">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <span class="text-gray-700 font-semibold">Chia sẻ bài viết:</span>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog-detail.php?slug=' . $post['slug']); ?>" 
                           target="_blank"
                           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/blog-detail.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                           target="_blank"
                           class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            Twitter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Related Posts -->
            <?php if ($related_result->num_rows > 0): ?>
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Bài Viết Liên Quan</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <?php while($related = $related_result->fetch_assoc()): ?>
                    <article class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                        <div class="relative h-40 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($related['cover_image'] ?: 'assets/images/blog-default.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                 class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 hover:text-pink-600 transition">
                                <a href="blog-detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                <?php echo htmlspecialchars($related['summary'] ?: substr(strip_tags($related['content']), 0, 80) . '...'); ?>
                            </p>
                            <a href="blog-detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" 
                               class="text-pink-600 hover:text-pink-700 font-semibold text-sm flex items-center gap-1">
                                Đọc Thêm
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Comments & Reactions Section -->
            <div class="max-w-4xl mx-auto mt-12 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <!-- Reaction Summary -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <div id="reactionSummary" class="flex items-center gap-2">
                            <div id="reactionIcons" class="flex -space-x-1"></div>
                            <span id="reactionTotal" class="text-gray-600 font-medium"></span>
                        </div>
                        <span class="text-gray-500"><span id="totalCommentsCount">0</span> bình luận</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="px-6 py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <!-- Like Button with Reactions -->
                        <div class="relative flex-1 reaction-button-container">
                            <button id="mainReactionBtn" class="reaction-main-btn w-full py-2.5 rounded-lg hover:bg-gray-50 transition-all flex items-center justify-center gap-2 font-medium text-gray-700 text-sm">
                                <span id="mainReactionIcon" class="text-lg">👍</span>
                                <span id="mainReactionText">Thích</span>
                            </button>
                            
                            <!-- Reactions Popup -->
                            <div class="reaction-popup absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-white rounded-full shadow-xl px-3 py-3 hidden opacity-0 transition-all duration-200">
                                <div class="flex items-center gap-2">
                                    <button class="reaction-item" data-reaction="like" onclick="selectReaction('like')" title="Thích">
                                        <span class="text-2xl hover:scale-125 transition-transform inline-block">👍</span>
                                    </button>
                                    <button class="reaction-item" data-reaction="love" onclick="selectReaction('love')" title="Yêu thích">
                                        <span class="text-2xl hover:scale-125 transition-transform inline-block">❤️</span>
                                    </button>
                                    <button class="reaction-item" data-reaction="wow" onclick="selectReaction('wow')" title="Wow">
                                        <span class="text-2xl hover:scale-125 transition-transform inline-block">😮</span>
                                    </button>
                                    <button class="reaction-item" data-reaction="haha" onclick="selectReaction('haha')" title="Haha">
                                        <span class="text-2xl hover:scale-125 transition-transform inline-block">😄</span>
                                    </button>
                                    <button class="reaction-item" data-reaction="sad" onclick="selectReaction('sad')" title="Buồn">
                                        <span class="text-2xl hover:scale-125 transition-transform inline-block">😢</span>
                                    </button>
                                    <button class="reaction-item" data-reaction="angry" onclick="selectReaction('angry')" title="Phẫn nộ">
                                        <span class="text-2xl hover:scale-125 transition-transform inline-block">😠</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comment Button -->
                        <button class="flex-1 py-2.5 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 font-medium text-gray-700 text-sm" onclick="document.getElementById('commentTextarea')?.focus()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Bình luận</span>
                        </button>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="px-4 pt-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tất cả bình luận</h3>
                </div>

                <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Comment Form -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm overflow-hidden flex-shrink-0">
                            <?php if(!empty($_SESSION['user_avatar'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <textarea id="commentTextarea" 
                                      class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-full focus:outline-none focus:bg-gray-100 resize-none text-sm" 
                                      rows="1"
                                      placeholder="Viết bình luận..."
                                      onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();addComment();}"
                                      oninput="this.style.height='auto';this.style.height=Math.max(36,this.scrollHeight)+'px'"></textarea>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Login Prompt -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <p class="text-gray-700 mb-3 text-sm">Đăng nhập để bình luận và thả cảm xúc</p>
                        <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                           class="inline-block px-5 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors font-medium text-sm">
                            Đăng nhập
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Comments List -->
                <div class="px-6 py-4">
                    <div class="comments-list space-y-4" id="commentsList">
                        <div class="text-center py-8 text-gray-400 text-sm">
                            Đang tải bình luận...
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- Back to Blog -->
            <div class="text-center" style="margin-top: 40px;">
                <a href="blog.php" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Quay Lại Trang Tin Tức
                </a>
            </div>
        </div>
    </div>
</article>

<?php 
// Set config for comments & reactions
$comments_type = 'blog';
$item_id = $post['id'];
require_once 'includes/comments-reactions.php';
require_once 'includes/footer.php'; 
?>
