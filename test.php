<?php
// File test kết nối database
echo "<h1>Test Kết Nối Database</h1>";

// Thông tin kết nối
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cua_hang_vay_cuoi_db';

echo "<p><strong>Thông tin kết nối:</strong></p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>User: $user</li>";
echo "<li>Password: " . (empty($pass) ? '(trống)' : '***') . "</li>";
echo "<li>Database: $dbname</li>";
echo "</ul>";

// Test kết nối
try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    
    echo "<p style='color: green; font-weight: bold;'>✓ Kết nối database THÀNH CÔNG!</p>";
    
    // Test query
    $result = $conn->query("SELECT COUNT(*) as total FROM vay_cuoi");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Số lượng váy cưới trong database: <strong>" . $row['total'] . "</strong></p>";
    }
    
    $result = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Số lượng người dùng: <strong>" . $row['total'] . "</strong></p>";
    }
    
    echo "<hr>";
    echo "<p style='color: green;'>✓ Hệ thống hoạt động bình thường!</p>";
    echo "<p><a href='index.php' style='background: #d4a574; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Vào Trang Chủ</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ LỖI KẾT NỐI!</p>";
    echo "<p style='color: red;'>Chi tiết lỗi: " . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<p><strong>Cách khắc phục:</strong></p>";
    echo "<ol>";
    echo "<li>Kiểm tra MySQL đã start trong XAMPP chưa</li>";
    echo "<li>Kiểm tra database 'cua_hang_vay_cuoi_db' đã được tạo chưa</li>";
    echo "<li>Vào http://localhost/phpmyadmin để import file SQL</li>";
    echo "<li>Kiểm tra thông tin kết nối trong includes/config.php</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><strong>Thông tin PHP:</strong></p>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "</ul>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1 {
    color: #d4a574;
}
ul, ol {
    line-height: 1.8;
}
</style>
