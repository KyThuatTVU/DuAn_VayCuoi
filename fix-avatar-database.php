<?php
/**
 * File này sửa lỗi: Đăng nhập Google không hiển thị ảnh người dùng
 * Vấn đề: Bảng nguoi_dung thiếu cột 'avt' để lưu avatar
 * Giải pháp: Thêm cột 'avt' vào bảng nguoi_dung
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='vi'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Sửa lỗi Avatar Database</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }";
echo "h2 { color: #333; }";
echo ".success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }";
echo ".error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }";
echo ".info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; }";
echo "table { border-collapse: collapse; width: 100%; margin: 20px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }";
echo "th { background-color: #4CAF50; color: white; }";
echo "tr:nth-child(even) { background-color: #f2f2f2; }";
echo "pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h2>🔧 Sửa lỗi Avatar Database</h2>";

// Bước 1: Kiểm tra cột avt có tồn tại không
echo "<h3>Bước 1: Kiểm tra cấu trúc bảng hiện tại</h3>";

$result = $conn->query("DESCRIBE nguoi_dung");
$columns = [];
$has_avt = false;
$has_last_login = false;

while ($row = $result->fetch_assoc()) {
    $columns[] = $row;
    if ($row['Field'] === 'avt') {
        $has_avt = true;
    }
    if ($row['Field'] === 'last_login') {
        $has_last_login = true;
    }
}

echo "<table>";
echo "<tr><th>Cột</th><th>Kiểu dữ liệu</th><th>Null</th><th>Mặc định</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($col['Field']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
    echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
    echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Bước 2: Thêm cột nếu chưa có
echo "<h3>Bước 2: Thêm cột thiếu</h3>";

$updates = [];

if (!$has_avt) {
    echo "<div class='info'>⚠️ Cột 'avt' chưa tồn tại. Đang thêm...</div>";
    try {
        $sql = "ALTER TABLE nguoi_dung ADD COLUMN avt VARCHAR(255) NULL COMMENT 'URL hoặc đường dẫn ảnh đại diện' AFTER dia_chi";
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Đã thêm cột 'avt' thành công!</div>";
            $updates[] = "Thêm cột 'avt'";
        } else {
            echo "<div class='error'>❌ Lỗi khi thêm cột 'avt': " . $conn->error . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Exception: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='success'>✅ Cột 'avt' đã tồn tại</div>";
}

if (!$has_last_login) {
    echo "<div class='info'>⚠️ Cột 'last_login' chưa tồn tại. Đang thêm...</div>";
    try {
        $sql = "ALTER TABLE nguoi_dung ADD COLUMN last_login DATETIME NULL COMMENT 'Lần đăng nhập cuối' AFTER avt";
        if ($conn->query($sql)) {
            echo "<div class='success'>✅ Đã thêm cột 'last_login' thành công!</div>";
            $updates[] = "Thêm cột 'last_login'";
        } else {
            echo "<div class='error'>❌ Lỗi khi thêm cột 'last_login': " . $conn->error . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Exception: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='success'>✅ Cột 'last_login' đã tồn tại</div>";
}

// Bước 3: Hiển thị cấu trúc bảng sau khi cập nhật
echo "<h3>Bước 3: Cấu trúc bảng sau khi cập nhật</h3>";

$result = $conn->query("DESCRIBE nguoi_dung");
echo "<table>";
echo "<tr><th>Cột</th><th>Kiểu dữ liệu</th><th>Null</th><th>Mặc định</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    $highlight = ($row['Field'] === 'avt' || $row['Field'] === 'last_login') ? "style='background-color: #ffffcc;'" : "";
    echo "<tr $highlight>";
    echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($row['Extra'] ?? '') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Bước 4: Kiểm tra dữ liệu người dùng hiện có
echo "<h3>Bước 4: Kiểm tra dữ liệu người dùng</h3>";

$result = $conn->query("SELECT id, ho_ten, email, avt FROM nguoi_dung LIMIT 10");
$user_count = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung")->fetch_assoc()['total'];

echo "<p>Tổng số người dùng: <strong>$user_count</strong></p>";

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Họ tên</th><th>Email</th><th>Avatar</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ho_ten']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>";
        if (!empty($row['avt'])) {
            if (strpos($row['avt'], 'http') === 0) {
                echo "<img src='" . htmlspecialchars($row['avt']) . "' width='40' height='40' style='border-radius: 50%;'> ";
            }
            echo "<small>" . htmlspecialchars(substr($row['avt'], 0, 50)) . "...</small>";
        } else {
            echo "<span style='color: #999;'>Chưa có</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>Chưa có người dùng nào trong hệ thống</div>";
}

// Tóm tắt
echo "<h3>📋 Tóm tắt</h3>";
if (count($updates) > 0) {
    echo "<div class='success'>";
    echo "<strong>Đã thực hiện các cập nhật:</strong><ul>";
    foreach ($updates as $update) {
        echo "<li>$update</li>";
    }
    echo "</ul>";
    echo "<p><strong>✅ Database đã được cập nhật thành công!</strong></p>";
    echo "<p>Bây giờ bạn có thể đăng nhập bằng Google và avatar sẽ được hiển thị.</p>";
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<p><strong>✅ Database đã có đầy đủ các cột cần thiết!</strong></p>";
    echo "<p>Nếu vẫn không hiển thị avatar, hãy kiểm tra:</p>";
    echo "<ul>";
    echo "<li>Session có lưu đúng avatar không (kiểm tra file test-session.php)</li>";
    echo "<li>Header có hiển thị đúng avatar không (kiểm tra includes/header.php)</li>";
    echo "<li>Content Security Policy có cho phép load ảnh từ Google không</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='login.php'>← Quay lại trang đăng nhập</a> | ";
echo "<a href='test-session.php'>Kiểm tra Session</a> | ";
echo "<a href='test-avatar.php'>Test Avatar</a></p>";

$conn->close();

echo "</body>";
echo "</html>";
?>
