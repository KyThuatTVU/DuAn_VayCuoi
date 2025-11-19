<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Tin Tức & Cẩm Nang Cưới';
require_once 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Trang Chủ</a> / <span>Tin Tức</span>
    </div>
</div>

<section class="blog-page">
    <div class="container">
        <div class="section-header">
            <h1>Tin Tức & Cẩm Nang Cưới</h1>
            <p>Cập nhật xu hướng và mẹo hay cho ngày cưới hoàn hảo</p>
        </div>

        <!-- Featured Post -->
        <div class="featured-post">
            <div class="featured-image">
                <img src="assets/images/blog-featured.jpg" alt="Featured">
            </div>
            <div class="featured-content">
                <span class="blog-category">Xu Hướng</span>
                <h2>Top 10 Xu Hướng Váy Cưới 2024 Không Thể Bỏ Lỡ</h2>
                <p>Khám phá những xu hướng váy cưới hot nhất năm 2024 từ các sàn diễn thời trang lớn trên thế giới. Từ phong cách tối giản đến lộng lẫy, chúng tôi sẽ giúp bạn tìm được chiếc váy hoàn hảo...</p>
                <div class="post-meta">
                    <span><i class="icon-calendar"></i> 15 Tháng 11, 2024</span>
                    <span><i class="icon-user"></i> Admin</span>
                    <span><i class="icon-comment"></i> 24 bình luận</span>
                </div>
                <a href="blog-detail.php?id=1" class="btn btn-primary">Đọc Thêm</a>
            </div>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid">
            <?php for($i = 1; $i <= 9; $i++): ?>
            <article class="blog-card">
                <div class="blog-image">
                    <img src="assets/images/blog-<?php echo $i; ?>.jpg" alt="Blog">
                    <span class="blog-date"><?php echo rand(1, 30); ?> Th11</span>
                </div>
                <div class="blog-content">
                    <span class="blog-category">
                        <?php 
                        $cats = ['Xu Hướng', 'Cẩm Nang', 'Mẹo Hay', 'Phong Cách'];
                        echo $cats[array_rand($cats)];
                        ?>
                    </span>
                    <h3>Bài Viết Hướng Dẫn Chọn Váy Cưới Số <?php echo $i; ?></h3>
                    <p>Mô tả ngắn gọn về nội dung bài viết, giúp người đọc hiểu được chủ đề chính...</p>
                    <div class="blog-footer">
                        <span class="read-time"><i class="icon-clock"></i> 5 phút đọc</span>
                        <a href="blog-detail.php?id=<?php echo $i; ?>" class="blog-link">Đọc Thêm →</a>
                    </div>
                </div>
            </article>
            <?php endfor; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <a href="#" class="page-link disabled">«</a>
            <a href="#" class="page-link active">1</a>
            <a href="#" class="page-link">2</a>
            <a href="#" class="page-link">3</a>
            <a href="#" class="page-link">»</a>
        </div>
    </div>
</section>

<style>
.featured-post {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 40px;
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin: 40px 0 60px;
}

.featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-content {
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.featured-content h2 {
    font-size: 32px;
    margin: 15px 0 20px;
    line-height: 1.4;
}

.featured-content p {
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 20px;
}

.post-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    font-size: 14px;
    color: var(--text-light);
}

.blog-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
}

.read-time {
    font-size: 13px;
    color: var(--text-light);
}

@media (max-width: 768px) {
    .featured-post {
        grid-template-columns: 1fr;
    }
    
    .featured-content {
        padding: 25px;
    }
    
    .featured-content h2 {
        font-size: 24px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
