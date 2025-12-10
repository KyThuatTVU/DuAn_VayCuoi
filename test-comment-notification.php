<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/notification-helper.php';

// Lấy thông tin comment vừa tạo
$comment_id = 17;
$sql = "SELECT bl.*, nd.ho_ten, v.ten_vay
        FROM binh_luan_san_pham bl
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
        JOIN vay_cuoi v ON bl.vay_id = v.id
        WHERE bl.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$comment = $stmt->get_result()->fetch_assoc();

if ($comment) {
    echo "<h2>Test tạo admin notification cho comment ID: $comment_id</h2>";
    echo "<p>User: {$comment['ho_ten']}</p>";
    echo "<p>Product: {$comment['ten_vay']}</p>";
    echo "<p>Content: {$comment['noi_dung']}</p>";

    // Tạo admin notification
    $result = notifyNewComment(
        $conn,
        'product',
        $comment['vay_id'],
        $comment['ten_vay'],
        $comment['ho_ten'],
        $comment['noi_dung']
    );

    echo "<p>Notification result: " . ($result ? 'SUCCESS' : 'FAILED') . "</p>";
} else {
    echo "<p>Comment not found!</p>";
}

// Kiểm tra admin notifications
$result = $conn->query("SELECT id, type, title, message, link FROM admin_notifications WHERE type = 'new_comment' ORDER BY id DESC LIMIT 3");

if ($result && $result->num_rows > 0) {
    echo "<h3>Admin notifications:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>Message</th><th>Link</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['type']}</td>";
        echo "<td>{$row['title']}</td>";
        echo "<td>{$row['message']}</td>";
        echo "<td><a href='{$row['link']}'>{$row['link']}</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No admin notifications found!</p>";
}

$conn->close();
