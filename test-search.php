<?php
require_once 'includes/config.php';

echo "=== KIỂM TRA DỮ LIỆU TÌM KIẾM ===\n\n";

// Kiểm tra tổng số váy
$result = $conn->query("SELECT COUNT(*) as total FROM vay_cuoi WHERE so_luong_ton > 0");
$total = $result->fetch_assoc()['total'];
echo "Tổng số váy có sẵn: $total\n\n";

// Tìm các váy có chứa "kim sa"
$search = "%kim sa%";
$stmt = $conn->prepare("SELECT id, ten_vay, mo_ta, phong_cach FROM vay_cuoi WHERE so_luong_ton > 0 AND (LOWER(ten_vay) LIKE LOWER(?) OR LOWER(mo_ta) LIKE LOWER(?))");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

echo "Tìm kiếm từ khóa 'kim sa':\n";
echo "Số kết quả: " . $result->num_rows . "\n\n";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "Tên: {$row['ten_vay']}\n";
        echo "Mô tả: " . substr($row['mo_ta'], 0, 100) . "...\n";
        echo "Phong cách: " . ($row['phong_cach'] ?? 'Không có') . "\n";
        echo "---\n";
    }
} else {
    echo "Không tìm thấy váy nào!\n\n";
    echo "Danh sách một số váy có sẵn:\n";
    $result = $conn->query("SELECT id, ten_vay FROM vay_cuoi WHERE so_luong_ton > 0 LIMIT 5");
    while($row = $result->fetch_assoc()) {
        echo "- {$row['ten_vay']}\n";
    }
}

$stmt->close();
$conn->close();
