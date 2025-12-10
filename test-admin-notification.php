<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/notification-helper.php';

echo "<h2>Test notifyNewComment function</h2>";

// Test tạo admin notification cho comment
$result = notifyNewComment(
    $conn,
    'product',
    1,
    'Váy Cưới Công Chúa',
    'Test User',
    'Đây là nội dung comment test để kiểm tra admin notification'
);

echo "<p>Kết quả: " . ($result ? 'SUCCESS' : 'FAILED') . "</p>";

// Kiểm tra notification vừa tạo
$check = $conn->query("SELECT id, type, title, message, link FROM admin_notifications WHERE type = 'new_comment' ORDER BY id DESC LIMIT 1");
if ($check && $row = $check->fetch_assoc()) {
    echo "<h3>Notification vừa tạo:</h3>";
    echo "<ul>";
    echo "<li>ID: {$row['id']}</li>";
    echo "<li>Type: {$row['type']}</li>";
    echo "<li>Title: {$row['title']}</li>";
    echo "<li>Message: {$row['message']}</li>";
    echo "<li>Link: <a href='{$row['link']}'>{$row['link']}</a></li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>Không tìm thấy notification vừa tạo!</p>";
}

$conn->close();
