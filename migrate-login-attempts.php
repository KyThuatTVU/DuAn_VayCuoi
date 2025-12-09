<?php
/**
 * Script để tự động thêm các cột cần thiết cho tính năng khóa tài khoản
 * Chạy file này một lần để cập nhật database
 */

require_once 'includes/config.php';

echo "<h2>Migration: Thêm tính năng khóa tài khoản khi đăng nhập sai</h2>";
echo "<hr>";

$success = 0;
$errors = [];

// 1. Thêm cột login_attempts
echo "<p>1. Thêm cột login_attempts... ";
try {
    $check = $conn->query("SHOW COLUMNS FROM nguoi_dung LIKE 'login_attempts'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE nguoi_dung ADD COLUMN login_attempts INT DEFAULT 0 COMMENT 'Số lần đăng nhập thất bại liên tiếp'");
        echo "<span style='color:green'>✓ Đã thêm</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

// 2. Thêm cột last_failed_login
echo "<p>2. Thêm cột last_failed_login... ";
try {
    $check = $conn->query("SHOW COLUMNS FROM nguoi_dung LIKE 'last_failed_login'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE nguoi_dung ADD COLUMN last_failed_login TIMESTAMP NULL COMMENT 'Thời gian đăng nhập thất bại cuối cùng'");
        echo "<span style='color:green'>✓ Đã thêm</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

// 3. Thêm cột locked_at
echo "<p>3. Thêm cột locked_at... ";
try {
    $check = $conn->query("SHOW COLUMNS FROM nguoi_dung LIKE 'locked_at'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE nguoi_dung ADD COLUMN locked_at TIMESTAMP NULL COMMENT 'Thời gian tài khoản bị khóa'");
        echo "<span style='color:green'>✓ Đã thêm</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

// 4. Thêm cột locked_reason
echo "<p>4. Thêm cột locked_reason... ";
try {
    $check = $conn->query("SHOW COLUMNS FROM nguoi_dung LIKE 'locked_reason'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE nguoi_dung ADD COLUMN locked_reason VARCHAR(255) NULL COMMENT 'Lý do khóa tài khoản'");
        echo "<span style='color:green'>✓ Đã thêm</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

// 5. Thêm cột status nếu chưa có
echo "<p>5. Thêm cột status... ";
try {
    $check = $conn->query("SHOW COLUMNS FROM nguoi_dung LIKE 'status'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE nguoi_dung ADD COLUMN status ENUM('active', 'locked', 'disabled') DEFAULT 'active' COMMENT 'Trạng thái tài khoản'");
        echo "<span style='color:green'>✓ Đã thêm</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

// 6. Tạo bảng login_logs
echo "<p>6. Tạo bảng login_logs... ";
try {
    $check = $conn->query("SHOW TABLES LIKE 'login_logs'");
    if ($check->num_rows == 0) {
        $sql = "CREATE TABLE login_logs (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            nguoi_dung_id BIGINT NULL,
            email VARCHAR(150) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            status ENUM('success', 'failed', 'locked') NOT NULL,
            failed_reason VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_nguoi_dung (nguoi_dung_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lịch sử đăng nhập'";
        $conn->query($sql);
        echo "<span style='color:green'>✓ Đã tạo</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

// 7. Tạo bảng admin_notifications
echo "<p>7. Tạo bảng admin_notifications... ";
try {
    $check = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
    if ($check->num_rows == 0) {
        $sql = "CREATE TABLE admin_notifications (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL COMMENT 'Loại thông báo: account_locked, new_order, etc.',
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            reference_id BIGINT NULL COMMENT 'ID tham chiếu (ví dụ: user_id)',
            reference_type VARCHAR(50) NULL COMMENT 'Loại tham chiếu (ví dụ: user)',
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL,
            INDEX idx_type (type),
            INDEX idx_is_read (is_read),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Thông báo cho admin'";
        $conn->query($sql);
        echo "<span style='color:green'>✓ Đã tạo</span>";
        $success++;
    } else {
        echo "<span style='color:blue'>Đã tồn tại</span>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Lỗi: " . $e->getMessage() . "</span>";
    $errors[] = $e->getMessage();
}
echo "</p>";

echo "<hr>";
echo "<h3>Kết quả:</h3>";
if (empty($errors)) {
    echo "<p style='color:green; font-size:18px;'>✓ Migration hoàn tất thành công!</p>";
    echo "<p>Đã thêm/kiểm tra $success thành phần.</p>";
} else {
    echo "<p style='color:red;'>Có " . count($errors) . " lỗi xảy ra:</p>";
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li>$err</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<h3>Tính năng mới:</h3>";
echo "<ul>";
echo "<li>Người dùng nhập sai mật khẩu 5 lần liên tiếp sẽ bị khóa tài khoản tự động</li>";
echo "<li>Admin sẽ nhận được thông báo khi có tài khoản bị khóa</li>";
echo "<li>Người dùng sẽ được thông báo số lần thử còn lại (khi còn 3 lần trở xuống)</li>";
echo "<li>Admin có thể mở khóa tài khoản từ trang Quản lý Khách hàng</li>";
echo "<li>Đăng nhập thành công sẽ reset số lần sai về 0</li>";
echo "</ul>";

echo "<p><a href='admin-users.php'>← Quay lại Quản lý Khách hàng</a></p>";
?>
