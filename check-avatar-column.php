<?php
require_once 'includes/config.php';

echo "<h2>Kiểm tra cột avt trong bảng nguoi_dung</h2>";

// Kiểm tra cấu trúc bảng
$result = $conn->query("DESCRIBE nguoi_dung");

echo "<h3>Cấu trúc bảng nguoi_dung:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";

$has_avt_column = false;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
    
    if ($row['Field'] === 'avt') {
        $has_avt_column = true;
    }
}
echo "</table>";

if ($has_avt_column) {
    echo "<p style='color: green; font-weight: bold;'>✅ Cột 'avt' TỒN TẠI trong bảng</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Cột 'avt' KHÔNG TỒN TẠI trong bảng</p>";
    echo "<p>Cần chạy câu lệnh SQL sau để thêm cột:</p>";
    echo "<pre>ALTER TABLE nguoi_dung ADD COLUMN avt VARCHAR(255) NULL AFTER dia_chi;</pre>";
}

$conn->close();
?>
