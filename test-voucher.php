<?php
require_once 'includes/config.php';

echo "<h1>Test Voucher Popup</h1>";

// Check if khuyen_mai table exists
$check_table = $conn->query("SHOW TABLES LIKE 'khuyen_mai'");
if ($check_table->num_rows > 0) {
    echo "<p style='color: green;'>✓ Bảng 'khuyen_mai' tồn tại</p>";
    
    // Show table structure
    echo "<h2>Cấu trúc bảng:</h2>";
    $columns = $conn->query("DESCRIBE khuyen_mai");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($col = $columns->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Get all promotions
    $all_promos = $conn->query("SELECT * FROM khuyen_mai ORDER BY created_at DESC");
    echo "<h2>Tất cả mã khuyến mãi (" . $all_promos->num_rows . "):</h2>";
    
    if ($all_promos->num_rows > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Code</th><th>Title</th><th>Type</th><th>Value</th><th>Start</th><th>End</th><th>Usage</th></tr>";
        while ($row = $all_promos->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td><strong>" . $row['code'] . "</strong></td>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['type'] . "</td>";
            echo "<td>" . $row['value'] . "</td>";
            echo "<td>" . ($row['start_at'] ?? 'NULL') . "</td>";
            echo "<td>" . ($row['end_at'] ?? 'NULL') . "</td>";
            echo "<td>" . ($row['used_count'] ?? 0) . "/" . ($row['usage_limit'] ?? '∞') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ Chưa có mã khuyến mãi nào</p>";
    }
    
    // Test API query (simple version first)
    echo "<h2>Test API Query:</h2>";
    $query = $conn->prepare("
        SELECT *
        FROM khuyen_mai 
        WHERE (start_at IS NULL OR start_at <= NOW()) 
        AND (end_at IS NULL OR end_at >= NOW())
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $promotion = $result->fetch_assoc();
        echo "<p style='color: green;'>✓ Tìm thấy mã khuyến mãi active:</p>";
        echo "<pre>" . print_r($promotion, true) . "</pre>";
        
        // Format for display
        if ($promotion['type'] === 'percentage') {
            $discount_value = $promotion['value'] . '% OFF';
        } else {
            $discount_value = number_format($promotion['value'], 0, ',', '.') . 'đ OFF';
        }
        echo "<p><strong>Hiển thị:</strong> " . $discount_value . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Không tìm thấy mã khuyến mãi active</p>";
        echo "<p>Lý do có thể:</p>";
        echo "<ul>";
        echo "<li>Chưa có mã khuyến mãi nào</li>";
        echo "<li>Mã đã hết hạn (end_at < NOW())</li>";
        echo "<li>Mã chưa bắt đầu (start_at > NOW())</li>";
        echo "</ul>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Bảng 'khuyen_mai' không tồn tại</p>";
}

echo "<hr>";
echo "<h2>Test API Endpoint:</h2>";
echo "<p><a href='api/get-latest-promotion.php' target='_blank'>Mở API: api/get-latest-promotion.php</a></p>";

echo "<hr>";
echo "<p><a href='admin-promotions.php'>→ Đi tới trang Quản lý Khuyến mãi</a></p>";
echo "<p><a href='index.php'>→ Về trang chủ (test popup)</a></p>";
?>
