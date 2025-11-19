<?php
require_once 'includes/env.php';

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'cua_hang_vay_cuoi_db';

echo '<h2>🔧 Setup Database</h2>';
echo '<p>Host: ' . $db_host . '</p>';
echo '<p>Database: ' . $db_name . '</p>';
echo '<hr>';

try {
    // Kết nối MySQL
    echo '<p>📡 Đang kết nối MySQL...</p>';
    $conn = new mysqli($db_host, $db_user, $db_pass);
    
    if ($conn->connect_error) {
        throw new Exception('Kết nối thất bại: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    echo '<p style="color:green">✅ Kết nối MySQL thành công!</p>';
    
    // Tạo database
    echo '<p>📦 Đang tạo database...</p>';
    $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo '<p style="color:green">✅ Database đã sẵn sàng!</p>';
    } else {
        throw new Exception('Lỗi tạo database: ' . $conn->error);
    }
    
    // Chọn database
    $conn->select_db($db_name);
    
    // Tạo bảng nguoi_dung
    echo '<p>👤 Đang tạo bảng nguoi_dung...</p>';
    $sql = "CREATE TABLE IF NOT EXISTS `nguoi_dung` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `ho_ten` VARCHAR(255) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `mat_khau` VARCHAR(255) NOT NULL,
        `so_dien_thoai` VARCHAR(30),
        `dia_chi` TEXT,
        `avt` VARCHAR(255) NULL,
        `last_login` DATETIME NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo '<p style="color:green">✅ Bảng nguoi_dung đã được tạo!</p>';
    } else {
        throw new Exception('Lỗi tạo bảng: ' . $conn->error);
    }
    
    // Kiểm tra dữ liệu
    echo '<p>🔍 Kiểm tra dữ liệu...</p>';
    $result = $conn->query('SELECT COUNT(*) as total FROM nguoi_dung');
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        echo '<p>➕ Đang thêm tài khoản mẫu...</p>';
        $password = password_hash('123456', PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi) VALUES 
                ('Admin', 'admin@vaycuoi.com', '$password', '0901234567', 'TP. Hồ Chí Minh'),
                ('User Test', 'user@example.com', '$password', '0912345678', 'Hà Nội')";
        
        if ($conn->query($sql)) {
            echo '<p style="color:green">✅ Đã thêm 2 tài khoản mẫu!</p>';
            echo '<div style="background:#e3f4f9;padding:15px;border-radius:8px;margin:10px 0">';
            echo '<strong>📋 Tài khoản test:</strong><br>';
            echo 'Email: admin@vaycuoi.com<br>';
            echo 'Mật khẩu: 123456<br><br>';
            echo 'Email: user@example.com<br>';
            echo 'Mật khẩu: 123456';
            echo '</div>';
        }
    } else {
        echo '<p style="color:blue">ℹ️ Database đã có ' . $row['total'] . ' người dùng</p>';
    }
    
    // Tạo thư mục uploads
    echo '<p>📁 Đang tạo thư mục uploads...</p>';
    if (!file_exists('uploads/avatars')) {
        if (mkdir('uploads/avatars', 0777, true)) {
            echo '<p style="color:green">✅ Thư mục uploads đã được tạo!</p>';
        }
    } else {
        echo '<p style="color:blue">ℹ️ Thư mục uploads đã tồn tại</p>';
    }
    
    $conn->close();
    
    echo '<hr>';
    echo '<h3 style="color:green">🎉 HOÀN THÀNH!</h3>';
    echo '<p>Database đã được thiết lập thành công!</p>';
    echo '<p>';
    echo '<a href="register.php" style="background:#7ec8e3;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;display:inline-block;margin:5px">Đăng ký</a>';
    echo '<a href="login.php" style="background:#5ab8d9;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;display:inline-block;margin:5px">Đăng nhập</a>';
    echo '</p>';
    echo '<hr>';
    echo '<p style="color:red"><strong>⚠️ LƯU Ý:</strong> Sau khi setup xong, nên xóa file này để bảo mật!</p>';
    
} catch (Exception $e) {
    echo '<p style="color:red;background:#ffe5e5;padding:15px;border-radius:8px">';
    echo '<strong>❌ LỖI:</strong> ' . $e->getMessage();
    echo '</p>';
    echo '<p>Vui lòng kiểm tra:</p>';
    echo '<ul>';
    echo '<li>MySQL đã chạy chưa?</li>';
    echo '<li>Thông tin trong file .env có đúng không?</li>';
    echo '<li>User có quyền tạo database không?</li>';
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #e3f4f9 0%, #d0eef7 100%);
            line-height: 1.6;
        }
        h2, h3 { color: #2c3e50; }
        p { margin: 8px 0; }
        hr { border: none; border-top: 2px solid #7ec8e3; margin: 20px 0; }
    </style>
</head>
<body>
</body>
</html>
