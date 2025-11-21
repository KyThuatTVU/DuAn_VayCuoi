<?php
/**
 * Script tạo tài khoản admin
 * Chạy file này một lần để tạo tài khoản admin
 * Sau đó XÓA file này để bảo mật
 */

require_once 'includes/config.php';

// Thông tin admin mặc định
$email = 'nguyenhuynhkithuat84tv@gmail.com';
$username = 'admin_kithuat';
$password = 'Admin@123456'; // Mật khẩu mặc định - NÊN ĐỔI SAU KHI ĐĂNG NHẬP
$full_name = 'Nguyễn Huỳnh Kỹ Thuật';
$role = 'super_admin';

echo "<div style='background: #dbeafe; border: 2px solid #3b82f6; padding: 20px; border-radius: 8px; max-width: 600px; margin-bottom: 20px;'>";
echo "<h3 style='color: #1e40af; margin-top: 0;'>ℹ️ Thông tin</h3>";
echo "<p style='color: #1e3a8a;'>Tài khoản admin mặc định: <strong>nguyenhuynhkithuat84tv@gmail.com</strong></p>";
echo "<p style='color: #1e3a8a;'>Tài khoản này có thể đăng nhập bằng:</p>";
echo "<ul style='color: #1e3a8a;'>";
echo "<li>Email + Mật khẩu</li>";
echo "<li>Google OAuth (nếu đã cấu hình)</li>";
echo "</ul>";
echo "</div>";

// Hash mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Kiểm tra xem email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "❌ Email admin đã tồn tại trong hệ thống!<br>";
        echo "Nếu muốn reset mật khẩu, vui lòng xóa admin cũ trong database trước.<br>";
        $stmt->close();
        exit();
    }
    $stmt->close();
    
    // Insert admin mới
    $stmt = $conn->prepare("INSERT INTO admin (username, email, password, full_name, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $role);
    
    if ($stmt->execute()) {
        echo "✅ Tạo tài khoản admin thành công!<br><br>";
        echo "<div style='background: #f0f9ff; border: 2px solid #0284c7; padding: 20px; border-radius: 8px; max-width: 600px;'>";
        echo "<h2 style='color: #0284c7; margin-top: 0;'>Thông tin đăng nhập Admin</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
        echo "<p><strong>Mật khẩu:</strong> " . htmlspecialchars($password) . "</p>";
        echo "<p><strong>Vai trò:</strong> " . htmlspecialchars($role) . "</p>";
        echo "<p><strong>Link đăng nhập:</strong> <a href='admin-login.php' style='color: #0284c7;'>admin-login.php</a></p>";
        echo "<hr style='margin: 20px 0;'>";
        echo "<p style='color: #dc2626; font-weight: bold;'>⚠️ QUAN TRỌNG:</p>";
        echo "<ol style='color: #dc2626;'>";
        echo "<li>Đăng nhập và ĐỔI MẬT KHẨU ngay lập tức</li>";
        echo "<li>XÓA file setup-admin.php này sau khi tạo xong để bảo mật</li>";
        echo "<li>KHÔNG chia sẻ thông tin đăng nhập với người khác</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "❌ Lỗi khi tạo admin: " . $stmt->error;
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}

$conn->close();
?>
