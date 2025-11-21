<?php
session_start();
require_once 'includes/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='vi'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Debug Google Login Session</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 20px; background: #f5f5f5; }";
echo ".box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo "h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }";
echo "h3 { color: #555; margin-top: 20px; }";
echo ".success { color: green; font-weight: bold; }";
echo ".error { color: red; font-weight: bold; }";
echo ".warning { color: orange; font-weight: bold; }";
echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; }";
echo "th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }";
echo "th { background: #4CAF50; color: white; }";
echo "tr:nth-child(even) { background: #f9f9f9; }";
echo ".avatar-preview { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #4CAF50; object-fit: cover; }";
echo "pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }";
echo ".info { background: #e3f2fd; padding: 10px; border-left: 4px solid #2196F3; margin: 10px 0; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h2>🔍 Debug Google Login - Session & Database</h2>";

// 1. Kiểm tra Session
echo "<div class='box'>";
echo "<h3>1️⃣ Thông tin Session hiện tại</h3>";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "<p class='success'>✅ User đã đăng nhập</p>";
    
    echo "<table>";
    echo "<tr><th>Session Key</th><th>Giá trị</th></tr>";
    
    $session_keys = ['user_id', 'user_name', 'user_email', 'user_avatar', 'logged_in'];
    foreach ($session_keys as $key) {
        $value = $_SESSION[$key] ?? '<span class="error">KHÔNG TỒN TẠI</span>';
        if ($key === 'user_avatar' && empty($value)) {
            $value = '<span class="error">TRỐNG</span>';
        }
        echo "<tr>";
        echo "<td><strong>$key</strong></td>";
        echo "<td>" . (is_string($value) ? htmlspecialchars($value) : $value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Hiển thị avatar từ session
    if (!empty($_SESSION['user_avatar'])) {
        echo "<h3>Avatar từ Session:</h3>";
        echo "<p><strong>URL:</strong> <code>" . htmlspecialchars($_SESSION['user_avatar']) . "</code></p>";
        echo "<img src='" . htmlspecialchars($_SESSION['user_avatar']) . "' class='avatar-preview' alt='Avatar'>";
        
        // Kiểm tra URL có phải từ Google không
        if (strpos($_SESSION['user_avatar'], 'googleusercontent.com') !== false) {
            echo "<p class='success'>✅ Đây là avatar từ Google</p>";
        } elseif (strpos($_SESSION['user_avatar'], 'uploads/') !== false) {
            echo "<p class='info'>ℹ️ Đây là avatar upload local</p>";
        }
    } else {
        echo "<p class='error'>❌ Session không có avatar</p>";
    }
    
} else {
    echo "<p class='error'>❌ User chưa đăng nhập</p>";
    echo "<p><a href='login.php'>→ Đi đến trang đăng nhập</a></p>";
}

echo "</div>";

// 2. Kiểm tra Database
if (isset($_SESSION['user_id'])) {
    echo "<div class='box'>";
    echo "<h3>2️⃣ Thông tin trong Database</h3>";
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, ho_ten, email, avt FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        echo "<table>";
        echo "<tr><th>Cột Database</th><th>Giá trị</th></tr>";
        foreach ($user as $key => $value) {
            $display_value = !empty($value) ? htmlspecialchars($value) : '<span class="error">TRỐNG</span>';
            echo "<tr>";
            echo "<td><strong>$key</strong></td>";
            echo "<td>$display_value</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Hiển thị avatar từ database
        if (!empty($user['avt'])) {
            echo "<h3>Avatar từ Database:</h3>";
            echo "<p><strong>URL:</strong> <code>" . htmlspecialchars($user['avt']) . "</code></p>";
            echo "<img src='" . htmlspecialchars($user['avt']) . "' class='avatar-preview' alt='Avatar DB'>";
            
            if (strpos($user['avt'], 'googleusercontent.com') !== false) {
                echo "<p class='success'>✅ Database có avatar từ Google</p>";
            }
        } else {
            echo "<p class='error'>❌ Database không có avatar (cột avt trống)</p>";
        }
    }
    
    $stmt->close();
    echo "</div>";
    
    // 3. So sánh Session vs Database
    echo "<div class='box'>";
    echo "<h3>3️⃣ So sánh Session vs Database</h3>";
    
    $session_avatar = $_SESSION['user_avatar'] ?? '';
    $db_avatar = $user['avt'] ?? '';
    
    echo "<table>";
    echo "<tr><th>Nguồn</th><th>Avatar URL</th><th>Trạng thái</th></tr>";
    echo "<tr>";
    echo "<td><strong>Session</strong></td>";
    echo "<td>" . (!empty($session_avatar) ? htmlspecialchars($session_avatar) : '<span class="error">TRỐNG</span>') . "</td>";
    echo "<td>" . (!empty($session_avatar) ? '<span class="success">✅ Có</span>' : '<span class="error">❌ Không</span>') . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><strong>Database</strong></td>";
    echo "<td>" . (!empty($db_avatar) ? htmlspecialchars($db_avatar) : '<span class="error">TRỐNG</span>') . "</td>";
    echo "<td>" . (!empty($db_avatar) ? '<span class="success">✅ Có</span>' : '<span class="error">❌ Không</span>') . "</td>";
    echo "</tr>";
    echo "</table>";
    
    if ($session_avatar === $db_avatar && !empty($session_avatar)) {
        echo "<p class='success'>✅ Session và Database KHỚP NHAU</p>";
    } elseif (empty($session_avatar) && !empty($db_avatar)) {
        echo "<p class='error'>❌ Database có avatar nhưng Session KHÔNG CÓ</p>";
        echo "<p class='warning'>→ Cần đăng xuất và đăng nhập lại để cập nhật session</p>";
    } elseif (!empty($session_avatar) && empty($db_avatar)) {
        echo "<p class='error'>❌ Session có avatar nhưng Database KHÔNG CÓ</p>";
        echo "<p class='warning'>→ Avatar không được lưu vào database khi đăng nhập</p>";
    } else {
        echo "<p class='error'>❌ CẢ HAI ĐỀU TRỐNG</p>";
    }
    
    echo "</div>";
}

// 4. Kiểm tra Header
echo "<div class='box'>";
echo "<h3>4️⃣ Test hiển thị như trong Header</h3>";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 8px;'>";
    echo "<p><strong>Code trong header.php:</strong></p>";
    echo "<pre>&lt;?php if (!empty(\$_SESSION['user_avatar'])): ?&gt;
    &lt;img src=\"&lt;?php echo htmlspecialchars(\$_SESSION['user_avatar']); ?&gt;\" alt=\"Avatar\"&gt;
&lt;?php else: ?&gt;
    &lt;!-- Icon mặc định --&gt;
&lt;?php endif; ?&gt;</pre>";
    
    echo "<p><strong>Kết quả:</strong></p>";
    if (!empty($_SESSION['user_avatar'])) {
        echo "<p class='success'>✅ Điều kiện TRUE - Sẽ hiển thị ảnh</p>";
        echo "<img src='" . htmlspecialchars($_SESSION['user_avatar']) . "' class='avatar-preview' alt='Avatar'>";
    } else {
        echo "<p class='error'>❌ Điều kiện FALSE - Sẽ hiển thị icon mặc định</p>";
        echo "<p>Lý do: \$_SESSION['user_avatar'] = " . var_export($_SESSION['user_avatar'] ?? null, true) . "</p>";
    }
    echo "</div>";
}

echo "</div>";

// 5. Hướng dẫn sửa lỗi
echo "<div class='box'>";
echo "<h3>5️⃣ Hướng dẫn sửa lỗi</h3>";

if (isset($_SESSION['user_id'])) {
    $has_session_avatar = !empty($_SESSION['user_avatar']);
    $has_db_avatar = !empty($user['avt'] ?? '');
    
    if (!$has_session_avatar && !$has_db_avatar) {
        echo "<div class='error' style='padding: 15px; background: #ffebee; border-radius: 5px;'>";
        echo "<p><strong>❌ Vấn đề: Không có avatar trong cả Session và Database</strong></p>";
        echo "<p><strong>Nguyên nhân có thể:</strong></p>";
        echo "<ol>";
        echo "<li>Google không trả về avatar (picture field)</li>";
        echo "<li>Code trong google-callback.php không lưu avatar</li>";
        echo "<li>Cột 'avt' không tồn tại trong bảng nguoi_dung</li>";
        echo "</ol>";
        echo "<p><strong>Giải pháp:</strong></p>";
        echo "<ol>";
        echo "<li>Kiểm tra file google-callback.php dòng lưu avatar</li>";
        echo "<li>Chạy file fix-avatar-database.php để kiểm tra cột avt</li>";
        echo "<li>Đăng xuất và đăng nhập lại bằng Google</li>";
        echo "</ol>";
        echo "</div>";
    } elseif (!$has_session_avatar && $has_db_avatar) {
        echo "<div class='warning' style='padding: 15px; background: #fff3e0; border-radius: 5px;'>";
        echo "<p><strong>⚠️ Vấn đề: Database có avatar nhưng Session không có</strong></p>";
        echo "<p><strong>Giải pháp đơn giản:</strong></p>";
        echo "<ol>";
        echo "<li><a href='logout.php'>Đăng xuất</a></li>";
        echo "<li><a href='login.php'>Đăng nhập lại bằng Google</a></li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='success' style='padding: 15px; background: #e8f5e9; border-radius: 5px;'>";
        echo "<p><strong>✅ Mọi thứ OK!</strong></p>";
        echo "<p>Nếu vẫn không hiển thị trên nav, kiểm tra:</p>";
        echo "<ol>";
        echo "<li>Cache trình duyệt (Ctrl+F5 để refresh)</li>";
        echo "<li>Content Security Policy trong header.php</li>";
        echo "<li>Console browser có lỗi load ảnh không</li>";
        echo "</ol>";
        echo "</div>";
    }
}

echo "</div>";

echo "<hr>";
echo "<p><a href='index.php'>← Về trang chủ</a> | ";
echo "<a href='logout.php'>Đăng xuất</a> | ";
echo "<a href='login.php'>Đăng nhập</a></p>";

$conn->close();

echo "</body>";
echo "</html>";
?>
