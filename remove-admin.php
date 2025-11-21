<?php
/**
 * Script xóa admin không mong muốn
 * Chạy file này MỘT LẦN để xóa admin
 * SAU ĐÓ XÓA FILE NÀY
 */

require_once 'includes/config.php';

// Email admin cần xóa
$email_to_remove = 'nhattruong.261097@gmail.com';

echo "<h2>🗑️ Xóa Admin Không Mong Muốn</h2>";
echo "<hr>";

try {
    // Kiểm tra admin có tồn tại không
    $stmt = $conn->prepare("SELECT id, username, email, full_name, role FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email_to_remove);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Tìm thấy admin:</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; background: white;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Tên</th><th>Vai trò</th></tr>";
        echo "<tr>";
        echo "<td>" . $admin['id'] . "</td>";
        echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($admin['email']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($admin['full_name']) . "</td>";
        echo "<td>" . $admin['role'] . "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
        
        $stmt->close();
        
        // Xóa admin
        $stmt = $conn->prepare("DELETE FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email_to_remove);
        
        if ($stmt->execute()) {
            echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='color: #155724; margin-top: 0;'>✅ Xóa Thành Công!</h3>";
            echo "<p style='color: #155724;'>Admin với email <strong>" . htmlspecialchars($email_to_remove) . "</strong> đã được xóa khỏi hệ thống.</p>";
            echo "<p style='color: #155724;'><strong>Lưu ý:</strong> Tài khoản này sẽ không thể đăng nhập vào admin nữa.</p>";
            echo "</div>";
            
            echo "<hr>";
            echo "<div style='background: #d1ecf1; border: 2px solid #17a2b8; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
            echo "<h3 style='color: #0c5460; margin-top: 0;'>📋 Danh Sách Admin Còn Lại:</h3>";
            
            $result = $conn->query("SELECT id, username, email, full_name, role, status FROM admin");
            
            if ($result->num_rows > 0) {
                echo "<table border='1' cellpadding='10' style='border-collapse: collapse; background: white; width: 100%;'>";
                echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Username</th><th>Email</th><th>Tên</th><th>Vai trò</th><th>Trạng thái</th></tr>";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                    echo "<td>" . $row['role'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p style='color: #0c5460;'>⚠️ Không còn admin nào trong hệ thống!</p>";
            }
            
            echo "</div>";
            
        } else {
            echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='color: #721c24;'>❌ Lỗi Khi Xóa</h3>";
            echo "<p style='color: #721c24;'>Không thể xóa admin. Lỗi: " . $conn->error . "</p>";
            echo "</div>";
        }
        
        $stmt->close();
        
    } else {
        echo "<div style='background: #d1ecf1; border: 2px solid #17a2b8; padding: 20px; border-radius: 8px;'>";
        echo "<h3 style='color: #0c5460; margin-top: 0;'>ℹ️ Không Tìm Thấy</h3>";
        echo "<p style='color: #0c5460;'>Admin với email <strong>" . htmlspecialchars($email_to_remove) . "</strong> không tồn tại trong hệ thống.</p>";
        echo "<p style='color: #0c5460;'>Có thể đã được xóa trước đó hoặc chưa từng được tạo.</p>";
        echo "</div>";
        
        $stmt->close();
    }
    
    echo "<hr>";
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
    echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ QUAN TRỌNG</h3>";
    echo "<p style='color: #856404; font-size: 16px;'><strong>XÓA FILE NÀY NGAY SAU KHI CHẠY XONG!</strong></p>";
    echo "<p style='color: #856404;'>File: <code>remove-admin.php</code></p>";
    echo "<p style='color: #856404;'>Lý do: Bảo mật - Không để file xóa admin công khai trên server.</p>";
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
        width: 100%;
    }
    code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
    }
</style>
