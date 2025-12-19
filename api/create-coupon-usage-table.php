<?php
// File này chỉ chạy một lần để tạo bảng user_coupon_usage
require_once '../includes/config.php';

$sql = "CREATE TABLE IF NOT EXISTS user_coupon_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    coupon_code VARCHAR(50) NOT NULL,
    order_id INT NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES don_hang(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_coupon (user_id, coupon_code),
    INDEX idx_coupon_code (coupon_code),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Bảng user_coupon_usage đã được tạo thành công!";
} else {
    echo "Lỗi: " . $conn->error;
}

$conn->close();
?>
