<?php
require_once 'includes/config.php';

echo "=== Kiểm Tra Banner Promotions ===\n\n";

if (!$conn) {
    die("Không thể kết nối database\n");
}

// Lấy tất cả banner
$result = $conn->query("SELECT * FROM banner_promotions ORDER BY display_order ASC, created_at DESC");

if ($result && $result->num_rows > 0) {
    echo "Tìm thấy " . $result->num_rows . " banner:\n\n";

    while ($banner = $result->fetch_assoc()) {
        echo "--- Banner ID: {$banner['id']} ---\n";
        echo "Title: {$banner['title']}\n";
        echo "Subtitle: {$banner['subtitle']}\n";
        echo "Description: {$banner['description']}\n";
        echo "Discount Value: {$banner['discount_value']}\n";
        echo "Promo Code: " . ($banner['promo_code'] ?: 'Không có') . "\n";
        echo "Active: " . ($banner['is_active'] ? 'Có' : 'Không') . "\n";
        echo "Start Date: " . ($banner['start_date'] ?: 'Không giới hạn') . "\n";
        echo "End Date: " . ($banner['end_date'] ?: 'Không giới hạn') . "\n";
        echo "Display Order: {$banner['display_order']}\n";
        echo "Created: {$banner['created_at']}\n\n";
    }
} else {
    echo "Không tìm thấy banner nào\n";
}

// Kiểm tra banner active hiện tại
echo "=== Banner Active Hiện Tại ===\n";
$active_query = $conn->prepare("SELECT * FROM banner_promotions
    WHERE is_active = 1
    AND (start_date IS NULL OR start_date <= NOW())
    AND (end_date IS NULL OR end_date >= NOW())
    ORDER BY display_order ASC, created_at DESC
    LIMIT 1");

$active_query->execute();
$active_result = $active_query->get_result();

if ($active_result->num_rows > 0) {
    $active_banner = $active_result->fetch_assoc();
    echo "Banner đang hiển thị:\n";
    echo "Title: {$active_banner['title']}\n";
    echo "Discount: {$active_banner['discount_value']}\n";
    echo "Code: " . ($active_banner['promo_code'] ?: 'Không có') . "\n";
} else {
    echo "Không có banner active nào\n";
}

$conn->close();
?>