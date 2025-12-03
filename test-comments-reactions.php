<?php
/**
 * Test file cho chức năng Comments & Reactions
 * Truy cập: http://localhost/wedding-dress/test-comments-reactions.php
 */

session_start();
require_once 'includes/config.php';

// Giả lập đăng nhập (để test)
if (isset($_GET['login'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Nguyễn Thị An';
    $_SESSION['user_email'] = 'an.nguyen@example.com';
    header('Location: test-comments-reactions.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: test-comments-reactions.php');
    exit();
}

$page_title = 'Test Comments & Reactions';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { color: #333; margin-bottom: 10px; }
        .status { padding: 15px; background: #e3f2fd; border-left: 4px solid #2196f3; margin-bottom: 20px; border-radius: 5px; }
        .status.logged-in { background: #e8f5e9; border-color: #4caf50; }
        .test-section { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-section h2 { color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee; }
        .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #2563eb; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .info-box { background: #f0f9ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info-box h3 { color: #0369a1; margin-bottom: 10px; }
        .info-box ul { margin-left: 20px; }
        .info-box li { margin: 5px 0; }
        .test-product { background: #fafafa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .test-product h3 { color: #555; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🧪 Test Comments & Reactions System</h1>
            <p>Trang test chức năng bình luận và thả cảm xúc</p>
        </div>

        <!-- Status -->
        <div class="status <?php echo isset($_SESSION['user_id']) ? 'logged-in' : ''; ?>">
            <?php if(isset($_SESSION['user_id'])): ?>
                <strong>✅ Đã đăng nhập:</strong> <?php echo $_SESSION['user_name']; ?> (<?php echo $_SESSION['user_email']; ?>)
                <a href="?logout" class="btn btn-danger" style="float: right;">Đăng Xuất</a>
            <?php else: ?>
                <strong>⚠️ Chưa đăng nhập</strong>
                <a href="?login" class="btn" style="float: right;">Đăng Nhập Test</a>
            <?php endif; ?>
        </div>

        <!-- Instructions -->
        <div class="test-section">
            <h2>📋 Hướng Dẫn Test</h2>
            <div class="info-box">
                <h3>Các Bước Test:</h3>
                <ul>
                    <li><strong>Bước 1:</strong> Import file <code>database-comments-reactions.sql</code> vào database</li>
                    <li><strong>Bước 2:</strong> Click "Đăng Nhập Test" để giả lập đăng nhập</li>
                    <li><strong>Bước 3:</strong> Test các chức năng bên dưới</li>
                    <li><strong>Bước 4:</strong> Click "Đăng Xuất" để test trạng thái chưa đăng nhập</li>
                </ul>
            </div>

            <div class="info-box">
                <h3>Chức Năng Cần Test:</h3>
                <ul>
                    <li>✅ Hiển thị danh sách bình luận</li>
                    <li>✅ Thêm bình luận mới</li>
                    <li>✅ Trả lời bình luận (nested comments)</li>
                    <li>✅ Xóa bình luận của mình</li>
                    <li>✅ Thả cảm xúc (6 loại)</li>
                    <li>✅ Thay đổi cảm xúc</li>
                    <li>✅ Bỏ cảm xúc (click lại)</li>
                    <li>✅ Thông báo khi chưa đăng nhập</li>
                </ul>
            </div>
        </div>

        <!-- Test Product Comments -->
        <div class="test-section">
            <h2>🛍️ Test Bình Luận Sản Phẩm</h2>
            <div class="test-product">
                <h3>Váy Công Chúa Bồng Bềnh (ID: 1)</h3>
                <p>Giá: 5,000,000đ/ngày</p>
                <a href="product-detail.php?id=1" class="btn" target="_blank">Xem Trang Chi Tiết</a>
            </div>

            <div class="info-box">
                <h3>API Endpoints:</h3>
                <ul>
                    <li><strong>GET:</strong> <code>api/comments-products.php?action=get&vay_id=1</code></li>
                    <li><strong>POST:</strong> <code>api/comments-products.php</code> (action=add, vay_id, noi_dung)</li>
                    <li><strong>POST:</strong> <code>api/reactions-products.php</code> (action=toggle, vay_id, loai_cam_xuc)</li>
                </ul>
            </div>
        </div>

        <!-- Test Blog Comments -->
        <div class="test-section">
            <h2>📰 Test Bình Luận Bài Viết</h2>
            <div class="test-product">
                <h3>Xu Hướng Váy Cưới 2024 (ID: 1)</h3>
                <p>Bài viết về xu hướng váy cưới hot nhất năm 2024</p>
                <a href="blog-detail.php?slug=xu-huong-vay-cuoi-2024" class="btn" target="_blank">Xem Trang Chi Tiết</a>
            </div>

            <div class="info-box">
                <h3>API Endpoints:</h3>
                <ul>
                    <li><strong>GET:</strong> <code>api/comments-blogs.php?action=get&bai_viet_id=1</code></li>
                    <li><strong>POST:</strong> <code>api/comments-blogs.php</code> (action=add, bai_viet_id, noi_dung)</li>
                    <li><strong>POST:</strong> <code>api/reactions-blogs.php</code> (action=toggle, bai_viet_id, loai_cam_xuc)</li>
                </ul>
            </div>
        </div>

        <!-- Database Check -->
        <div class="test-section">
            <h2>🗄️ Kiểm Tra Database</h2>
            <?php
            $tables = [
                'binh_luan_san_pham' => 'Bình luận sản phẩm',
                'binh_luan_bai_viet' => 'Bình luận bài viết',
                'cam_xuc_san_pham' => 'Cảm xúc sản phẩm',
                'cam_xuc_bai_viet' => 'Cảm xúc bài viết'
            ];

            foreach ($tables as $table => $name) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result->num_rows > 0) {
                    $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
                    echo "<div class='info-box'>";
                    echo "<strong>✅ $name ($table):</strong> $count bản ghi";
                    echo "</div>";
                } else {
                    echo "<div class='info-box' style='background: #fee2e2; color: #991b1b;'>";
                    echo "<strong>❌ $name ($table):</strong> Chưa tạo bảng";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <!-- Quick Test Links -->
        <div class="test-section">
            <h2>🔗 Quick Test Links</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <a href="product-detail.php?id=1" class="btn" target="_blank">Sản phẩm #1</a>
                <a href="product-detail.php?id=2" class="btn" target="_blank">Sản phẩm #2</a>
                <a href="product-detail.php?id=3" class="btn" target="_blank">Sản phẩm #3</a>
                <a href="blog-detail.php?slug=xu-huong-vay-cuoi-2024" class="btn" target="_blank">Bài viết #1</a>
                <a href="api/comments-products.php?action=get&vay_id=1" class="btn" target="_blank">API Comments Product</a>
                <a href="api/reactions-products.php?action=get&vay_id=1" class="btn" target="_blank">API Reactions Product</a>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #666;">
            <p>💡 <strong>Tip:</strong> Mở Developer Console (F12) để xem API requests và responses</p>
            <p style="margin-top: 10px;">Developed by Kiro AI Assistant</p>
        </div>
    </div>
</body>
</html>
