<?php
/**
 * Script debug để kiểm tra avatar từ Google
 * Chạy file này để xem thông tin chi tiết
 */

session_start();
require_once 'includes/config.php';

echo "<h2>🔍 Debug Google Avatar</h2>";
echo "<hr>";

// 1. Kiểm tra session hiện tại
echo "<div style='background: #d1ecf1; border: 2px solid #17a2b8; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<h3 style='color: #0c5460; margin-top: 0;'>1️⃣ Session Hiện Tại</h3>";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "<p style='color: #0c5460;'><strong>✅ Đã đăng nhập</strong></p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; background: white; width: 100%;'>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    echo "<tr><td>user_id</td><td>" . ($_SESSION['user_id'] ?? 'N/A') . "</td></tr>";
    echo "<tr><td>user_name</td><td>" . htmlspecialchars($_SESSION['user_name'] ?? 'N/A') . "</td></tr>";
    echo "<tr><td>user_email</td><td>" . htmlspecialchars($_SESSION['user_email'] ?? 'N/A') . "</td></tr>";
    echo "<tr><td>user_avatar</td><td>";
    
    if (!empty($_SESSION['user_avatar'])) {
        echo "<img src='" . htmlspecialchars($_SESSION['user_avatar']) . "' width='50' height='50' style='border-radius: 50%; margin-right: 10px;'>";
        echo "<br><small>" . htmlspecialchars($_SESSION['user_avatar']) . "</small>";
    } else {
        echo "<span style='color: red;'>❌ KHÔNG CÓ AVATAR</span>";
    }
    
    echo "</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color: #856404;'><strong>⚠️ Chưa đăng nhập</strong></p>";
    echo "<p style='color: #856404;'>Vui lòng <a href='login.php'>đăng nhập</a> để kiểm tra.</p>";
}

echo "</div>";

// 2. Kiểm tra database
if (isset($_SESSION['user_id'])) {
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>2️⃣ Thông Tin Trong Database</h3>";
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, ho_ten, email, avt FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; background: white; width: 100%;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>ID</td><td>" . $user['id'] . "</td></tr>";
        echo "<tr><td>Họ tên</td><td>" . htmlspecialchars($user['ho_ten']) . "</td></tr>";
        echo "<tr><td>Email</td><td>" . htmlspecialchars($user['email']) . "</td></tr>";
        echo "<tr><td>Avatar (avt)</td><td>";
        
        if (!empty($user['avt'])) {
            echo "<img src='" . htmlspecialchars($user['avt']) . "' width='50' height='50' style='border-radius: 50%; margin-right: 10px;'>";
            echo "<br><small>" . htmlspecialchars($user['avt']) . "</small>";
            
            // Kiểm tra URL có hợp lệ không
            if (strpos($user['avt'], 'googleusercontent.com') !== false) {
                echo "<br><span style='color: green;'>✅ Avatar từ Google</span>";
            } else {
                echo "<br><span style='color: orange;'>⚠️ Avatar không phải từ Google</span>";
            }
        } else {
            echo "<span style='color: red;'>❌ KHÔNG CÓ AVATAR TRONG DATABASE</span>";
        }
        
        echo "</td></tr>";
        echo "</table>";
    }
    
    $stmt->close();
    echo "</div>";
}

// 3. So sánh Session vs Database
if (isset($_SESSION['user_id'])) {
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #856404; margin-top: 0;'>3️⃣ So Sánh Session vs Database</h3>";
    
    $session_avatar = $_SESSION['user_avatar'] ?? '';
    $db_avatar = $user['avt'] ?? '';
    
    if ($session_avatar === $db_avatar) {
        echo "<p style='color: #155724;'><strong>✅ KHỚP:</strong> Session và Database giống nhau</p>";
    } else {
        echo "<p style='color: #721c24;'><strong>❌ KHÔNG KHỚP:</strong></p>";
        echo "<ul style='color: #721c24;'>";
        echo "<li>Session: " . ($session_avatar ? htmlspecialchars($session_avatar) : 'EMPTY') . "</li>";
        echo "<li>Database: " . ($db_avatar ? htmlspecialchars($db_avatar) : 'EMPTY') . "</li>";
        echo "</ul>";
    }
    
    echo "</div>";
}

// 4. Hướng dẫn sửa lỗi
echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 8px;'>";
echo "<h3 style='color: #721c24; margin-top: 0;'>4️⃣ Hướng Dẫn Sửa Lỗi</h3>";

if (empty($_SESSION['user_avatar']) || empty($user['avt'] ?? '')) {
    echo "<p style='color: #721c24;'><strong>Vấn đề:</strong> Avatar không có hoặc không hiển thị</p>";
    echo "<p style='color: #721c24;'><strong>Giải pháp:</strong></p>";
    echo "<ol style='color: #721c24;'>";
    echo "<li><strong>Đăng xuất</strong> khỏi tài khoản hiện tại</li>";
    echo "<li><strong>Đăng nhập lại</strong> bằng Google</li>";
    echo "<li>Avatar sẽ tự động được cập nhật từ Google</li>";
    echo "<li>Quay lại trang này để kiểm tra</li>";
    echo "</ol>";
    
    echo "<p style='color: #721c24;'><strong>Hoặc:</strong></p>";
    echo "<ol style='color: #721c24;'>";
    echo "<li>Vào <a href='fix-user-avatars.php'>fix-user-avatars.php</a></li>";
    echo "<li>Xóa user hiện tại</li>";
    echo "<li>Đăng nhập lại bằng Google</li>";
    echo "</ol>";
} else {
    echo "<p style='color: #155724;'><strong>✅ Avatar đang hoạt động bình thường!</strong></p>";
}

echo "</div>";

// 5. Test URL avatar
if (!empty($user['avt'] ?? '')) {
    echo "<div style='background: #e7f3ff; border: 2px solid #2196F3; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
    echo "<h3 style='color: #0d47a1; margin-top: 0;'>5️⃣ Test Avatar URL</h3>";
    
    $avatar_url = $user['avt'];
    echo "<p style='color: #0d47a1;'><strong>URL:</strong> " . htmlspecialchars($avatar_url) . "</p>";
    
    // Test xem URL có load được không
    echo "<p style='color: #0d47a1;'><strong>Preview:</strong></p>";
    echo "<img src='" . htmlspecialchars($avatar_url) . "' width='100' height='100' style='border-radius: 50%; border: 3px solid #2196F3;' onerror='this.style.border=\"3px solid red\"; this.alt=\"❌ Không load được ảnh\";'>";
    
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
        width: 100%;
    }
    th {
        background: #f0f0f0;
        text-align: left;
    }
</style>
