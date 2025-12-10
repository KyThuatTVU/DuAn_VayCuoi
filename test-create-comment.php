<?php
session_start();
require_once __DIR__ . '/includes/config.php';

// Giả lập đăng nhập user ID 6 (Thiên Vũ Đỗ)
$_SESSION['user_id'] = 6;

// Test tạo comment cho sản phẩm
echo "<h2>Test tạo comment và admin notification</h2>";

// Giả lập POST data
$_POST['action'] = 'add';
$_POST['vay_id'] = '1'; // ID sản phẩm
$_POST['noi_dung'] = 'Test comment từ admin để kiểm tra notification - ' . date('H:i:s');

// Include API để xử lý
echo "<h3>Gọi API comments-products.php</h3>";
include 'api/comments-products.php';

echo "<h3>Kiểm tra admin notifications sau khi tạo comment</h3>";

// Kiểm tra admin notifications
$result = $conn->query("SELECT id, type, title, message, link, created_at FROM admin_notifications WHERE type = 'new_comment' ORDER BY id DESC LIMIT 3");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>Message</th><th>Link</th><th>Created</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['type']}</td>";
        echo "<td>{$row['title']}</td>";
        echo "<td>{$row['message']}</td>";
        echo "<td><a href='{$row['link']}'>{$row['link']}</a></td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Không có admin notification nào được tạo!</p>";
}

$conn->close();
