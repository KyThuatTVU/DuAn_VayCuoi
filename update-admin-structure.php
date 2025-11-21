<?php
/**
 * Script tự động cập nhật cấu trúc bảng admin
 * Chạy file này MỘT LẦN để thêm các cột cần thiết
 * Sau đó XÓA file này
 */

require_once 'includes/config.php';

echo "<h2>🔧 Cập Nhật Cấu Trúc Bảng Admin</h2>";
echo "<hr>";

try {
    // 1. Kiểm tra bảng admin có tồn tại không
    $result = $conn->query("SHOW TABLES LIKE 'admin'");
    if ($result->num_rows === 0) {
        echo "❌ Bảng 'admin' không tồn tại. Vui lòng import file SQL trước.<br>";
        exit();
    }
    echo "✅ Bảng 'admin' đã tồn tại<br><br>";

    // 2. Lấy cấu trúc hiện tại
    echo "<strong>Cấu trúc bảng hiện tại:</strong><br>";
    $result = $conn->query("DESCRIBE admin");
    $existing_columns = [];
    while ($row = $result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
    echo "<br>";

    // 3. Thêm cột email nếu chưa có
    if (!in_array('email', $existing_columns)) {
        echo "➕ Thêm cột 'email'...<br>";
        $conn->query("ALTER TABLE admin ADD COLUMN email VARCHAR(150) NULL AFTER username");
        echo "✅ Đã thêm cột 'email'<br><br>";
    } else {
        echo "✅ Cột 'email' đã tồn tại<br><br>";
    }

    // 4. Thêm cột role nếu chưa có
    if (!in_array('role', $existing_columns)) {
        echo "➕ Thêm cột 'role'...<br>";
        $conn->query("ALTER TABLE admin ADD COLUMN role ENUM('super_admin','admin','moderator') DEFAULT 'admin' AFTER full_name");
        echo "✅ Đã thêm cột 'role'<br><br>";
    } else {
        echo "✅ Cột 'role' đã tồn tại<br><br>";
    }

    // 5. Thêm cột status nếu chưa có
    if (!in_array('status', $existing_columns)) {
        echo "➕ Thêm cột 'status'...<br>";
        $conn->query("ALTER TABLE admin ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER role");
        echo "✅ Đã thêm cột 'status'<br><br>";
    } else {
        echo "✅ Cột 'status' đã tồn tại<br><br>";
    }

    // 6. Thêm cột last_login nếu chưa có
    if (!in_array('last_login', $existing_columns)) {
        echo "➕ Thêm cột 'last_login'...<br>";
        $conn->query("ALTER TABLE admin ADD COLUMN last_login TIMESTAMP NULL AFTER status");
        echo "✅ Đã thêm cột 'last_login'<br><br>";
    } else {
        echo "✅ Cột 'last_login' đã tồn tại<br><br>";
    }

    // 7. Cập nhật email cho admin hiện có (nếu email NULL)
    echo "🔄 Cập nhật email cho admin hiện có...<br>";
    $result = $conn->query("SELECT id, username, email FROM admin WHERE email IS NULL OR email = ''");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temp_email = $row['username'] . '@admin.local';
            $stmt = $conn->prepare("UPDATE admin SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $temp_email, $row['id']);
            $stmt->execute();
            echo "- Admin ID {$row['id']}: email = {$temp_email}<br>";
        }
        echo "✅ Đã cập nhật email<br><br>";
    } else {
        echo "✅ Tất cả admin đã có email<br><br>";
    }

    // 8. Thêm UNIQUE constraint cho email (nếu chưa có)
    echo "🔒 Thêm UNIQUE constraint cho email...<br>";
    $result = $conn->query("SHOW INDEXES FROM admin WHERE Column_name = 'email'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE admin ADD UNIQUE KEY unique_email (email)");
        echo "✅ Đã thêm UNIQUE constraint<br><br>";
    } else {
        echo "✅ UNIQUE constraint đã tồn tại<br><br>";
    }

    // 9. Hiển thị cấu trúc mới
    echo "<hr>";
    echo "<h3>📋 Cấu Trúc Bảng Sau Khi Cập Nhật:</h3>";
    $result = $conn->query("DESCRIBE admin");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";

    // 10. Hiển thị dữ liệu admin
    echo "<h3>👥 Danh Sách Admin:</h3>";
    $result = $conn->query("SELECT id, username, email, full_name, role, status FROM admin");
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Role</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['full_name'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "<p>Chưa có admin nào trong hệ thống.</p>";
    }

    echo "<hr>";
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Cập Nhật Thành Công!</h3>";
    echo "<p style='color: #155724;'>Bảng 'admin' đã được cập nhật với đầy đủ các cột cần thiết.</p>";
    echo "<p style='color: #155724;'><strong>Bước tiếp theo:</strong></p>";
    echo "<ol style='color: #155724;'>";
    echo "<li>Bây giờ bạn có thể đăng nhập admin bằng Google</li>";
    echo "<li><strong>XÓA file này (update-admin-structure.php) để bảo mật</strong></li>";
    echo "<li>Truy cập: <a href='admin-login.php'>admin-login.php</a></li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 8px;'>";
    echo "<h3 style='color: #721c24;'>❌ Lỗi</h3>";
    echo "<p style='color: #721c24;'>" . $e->getMessage() . "</p>";
    echo "</div>";
}

$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    table {
        background: white;
        width: 100%;
    }
    th {
        text-align: left;
    }
</style>
