<?php
require_once 'includes/config.php';

echo "Đang tạo bảng admin_notifications...\n";

$sql = file_get_contents('create-admin-notifications-table.sql');

if ($conn->query($sql)) {
    echo "✅ Tạo bảng admin_notifications thành công!\n";
} else {
    echo "❌ Lỗi: " . $conn->error . "\n";
}

$conn->close();
