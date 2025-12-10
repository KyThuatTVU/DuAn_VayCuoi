<?php
/**
 * Script thực thi migration: Thêm cột size vào bảng vay_cuoi
 */

require_once 'includes/config.php';

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <title>Migration: Thêm cột size</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        .success { color: #28a745; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
<div class='box'>
    <h2>🔧 Migration: Thêm cột size vào bảng vay_cuoi</h2>";

// Kiểm tra cột size đã tồn tại chưa
$check = $conn->query("SHOW COLUMNS FROM vay_cuoi LIKE 'size'");

if ($check->num_rows > 0) {
    echo "<div class='info'>✓ Cột 'size' đã tồn tại trong bảng vay_cuoi. Không cần migration.</div>";
} else {
    echo "<div class='info'>→ Đang thêm cột 'size' vào bảng vay_cuoi...</div>";
    
    // Đọc và thực thi file SQL
    $sql = file_get_contents(__DIR__ . '/sql-add-size-column.sql');
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✓ Đã thêm cột 'size' thành công!</div>";
        echo "<div class='info'><strong>Chi tiết:</strong><br>";
        echo "- Tên cột: <code>size</code><br>";
        echo "- Kiểu dữ liệu: <code>VARCHAR(100)</code><br>";
        echo "- Mô tả: Kích cỡ váy (S, M, L, XL hoặc số đo cụ thể)<br>";
        echo "- Vị trí: Sau cột <code>so_luong_ton</code></div>";
    } else {
        echo "<div class='error'>✗ Lỗi khi thêm cột: " . $conn->error . "</div>";
    }
}

// Hiển thị cấu trúc bảng hiện tại
echo "<h3>📋 Cấu trúc bảng vay_cuoi hiện tại:</h3>";
$columns = $conn->query("SHOW COLUMNS FROM vay_cuoi");
echo "<table border='1' cellpadding='8' style='width:100%; border-collapse: collapse;'>";
echo "<tr style='background:#f0f0f0;'><th>Tên cột</th><th>Kiểu dữ liệu</th><th>Null</th><th>Mặc định</th></tr>";
while ($col = $columns->fetch_assoc()) {
    echo "<tr>";
    echo "<td><strong>{$col['Field']}</strong></td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<div style='margin-top: 20px;'>";
echo "<a href='admin-dresses.php' style='display:inline-block; padding:10px 20px; background:#e91e63; color:white; text-decoration:none; border-radius:4px;'>← Quay lại quản lý váy cưới</a>";
echo "</div>";

echo "</div>
</body>
</html>";

$conn->close();
