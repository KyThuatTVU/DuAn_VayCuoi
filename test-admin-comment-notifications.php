<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/notification-helper.php';

// Test tạo comment và admin notification
echo "<h2>Test Admin Notifications for Comments</h2>";

// Giả lập tạo comment mới cho product
echo "<h3>1. Tạo comment cho sản phẩm</h3>";
$result1 = notifyNewComment(
    $conn,
    'product',
    1, // product ID
    'Váy Cưới Công Chúa',
    'Test User',
    'Đây là comment test từ user để kiểm tra admin notification'
);
echo "<p>Product comment notification: " . ($result1 ? 'SUCCESS' : 'FAILED') . "</p>";

// Giả lập tạo comment mới cho blog
echo "<h3>2. Tạo comment cho blog</h3>";
$result2 = notifyNewComment(
    $conn,
    'blog',
    6, // blog ID
    'Trang trí cổng đám cưới',
    'Test User 2',
    'Comment test cho blog để xem admin có nhận được không'
);
echo "<p>Blog comment notification: " . ($result2 ? 'SUCCESS' : 'FAILED') . "</p>";

// Hiển thị admin notifications gần đây
echo "<h3>3. Admin notifications gần đây</h3>";
$result = $conn->query("SELECT id, type, title, message, link, created_at FROM admin_notifications ORDER BY id DESC LIMIT 5");

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

$conn->close();
