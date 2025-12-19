<?php
/**
 * File test chức năng voucher
 * Chạy file này để kiểm tra hệ thống voucher hoạt động đúng
 */

session_start();
require_once 'includes/config.php';

echo "<h1>Test Hệ Thống Voucher</h1>";
echo "<hr>";

// 1. Kiểm tra bảng user_coupon_usage
echo "<h2>1. Kiểm tra bảng user_coupon_usage</h2>";
$check_table = $conn->query("SHOW TABLES LIKE 'user_coupon_usage'");
if ($check_table->num_rows > 0) {
    echo "✅ Bảng user_coupon_usage đã tồn tại<br>";
    
    // Kiểm tra cấu trúc
    $columns = $conn->query("DESCRIBE user_coupon_usage");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Cột</th><th>Kiểu dữ liệu</th><th>Null</th><th>Key</th></tr>";
    while ($col = $columns->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Bảng user_coupon_usage chưa tồn tại<br>";
    echo "<a href='api/create-coupon-usage-table.php' target='_blank'>Click để tạo bảng</a><br>";
}

echo "<hr>";

// 2. Kiểm tra bảng khuyen_mai
echo "<h2>2. Kiểm tra bảng khuyến mãi</h2>";
$promotions = $conn->query("SELECT * FROM khuyen_mai WHERE start_at <= NOW() AND end_at >= NOW()");
if ($promotions->num_rows > 0) {
    echo "✅ Có {$promotions->num_rows} voucher đang hoạt động<br><br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Mã</th><th>Tiêu đề</th><th>Loại</th><th>Giá trị</th><th>Đơn tối thiểu</th><th>Giới hạn</th><th>Đã dùng</th></tr>";
    
    while ($promo = $promotions->fetch_assoc()) {
        // Đếm số lần đã sử dụng
        $used_query = $conn->prepare("SELECT COUNT(*) as count FROM user_coupon_usage WHERE coupon_code = ?");
        $used_query->bind_param("s", $promo['code']);
        $used_query->execute();
        $used_count = $used_query->get_result()->fetch_assoc()['count'];
        
        echo "<tr>";
        echo "<td><strong>{$promo['code']}</strong></td>";
        echo "<td>{$promo['title']}</td>";
        echo "<td>" . ($promo['type'] === 'percent' ? 'Phần trăm' : 'Cố định') . "</td>";
        echo "<td>" . ($promo['type'] === 'percent' ? $promo['value'] . '%' : number_format($promo['value']) . ' VNĐ') . "</td>";
        echo "<td>" . number_format($promo['min_order_amount']) . " VNĐ</td>";
        echo "<td>" . ($promo['usage_limit'] ? $promo['usage_limit'] : 'Không giới hạn') . "</td>";
        echo "<td>{$used_count}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "⚠️ Không có voucher nào đang hoạt động<br>";
    echo "<a href='admin-promotions.php'>Đi đến trang quản lý khuyến mãi</a><br>";
}

echo "<hr>";

// 3. Test API get-available-vouchers
echo "<h2>3. Test API lấy danh sách voucher</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ Đã đăng nhập với user_id: {$_SESSION['user_id']}<br>";
    echo "<a href='api/get-available-vouchers.php' target='_blank'>Xem kết quả API</a><br>";
} else {
    echo "⚠️ Chưa đăng nhập. <a href='login.php'>Đăng nhập</a> để test API<br>";
}

echo "<hr>";

// 4. Test API apply-coupon
echo "<h2>4. Test áp dụng voucher</h2>";
if (isset($_SESSION['user_id'])) {
    // Kiểm tra giỏ hàng
    $cart_check = $conn->prepare("SELECT COUNT(*) as count FROM gio_hang WHERE nguoi_dung_id = ?");
    $cart_check->bind_param("i", $_SESSION['user_id']);
    $cart_check->execute();
    $cart_count = $cart_check->get_result()->fetch_assoc()['count'];
    
    if ($cart_count > 0) {
        echo "✅ Giỏ hàng có {$cart_count} sản phẩm<br>";
        echo "<a href='checkout.php'>Đi đến trang thanh toán để test</a><br>";
    } else {
        echo "⚠️ Giỏ hàng trống. <a href='products.php'>Thêm sản phẩm</a> để test<br>";
    }
} else {
    echo "⚠️ Chưa đăng nhập<br>";
}

echo "<hr>";

// 5. Thống kê sử dụng voucher
echo "<h2>5. Thống kê sử dụng voucher</h2>";
$stats = $conn->query("
    SELECT 
        coupon_code,
        COUNT(*) as usage_count,
        SUM(discount_amount) as total_discount,
        MIN(used_at) as first_used,
        MAX(used_at) as last_used
    FROM user_coupon_usage
    GROUP BY coupon_code
    ORDER BY usage_count DESC
");

if ($stats && $stats->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Mã voucher</th><th>Số lần dùng</th><th>Tổng giảm giá</th><th>Lần đầu</th><th>Lần cuối</th></tr>";
    while ($stat = $stats->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>{$stat['coupon_code']}</strong></td>";
        echo "<td>{$stat['usage_count']}</td>";
        echo "<td>" . number_format($stat['total_discount']) . " VNĐ</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($stat['first_used'])) . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($stat['last_used'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "ℹ️ Chưa có voucher nào được sử dụng<br>";
}

echo "<hr>";

// 6. Kiểm tra user đã dùng voucher nào
echo "<h2>6. Lịch sử sử dụng voucher của user hiện tại</h2>";
if (isset($_SESSION['user_id'])) {
    $user_history = $conn->prepare("
        SELECT ucu.*, dh.ma_don_hang, dh.tong_tien
        FROM user_coupon_usage ucu
        JOIN don_hang dh ON ucu.order_id = dh.id
        WHERE ucu.user_id = ?
        ORDER BY ucu.used_at DESC
    ");
    $user_history->bind_param("i", $_SESSION['user_id']);
    $user_history->execute();
    $history = $user_history->get_result();
    
    if ($history->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Mã voucher</th><th>Mã đơn hàng</th><th>Giảm giá</th><th>Tổng đơn</th><th>Thời gian</th></tr>";
        while ($h = $history->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>{$h['coupon_code']}</strong></td>";
            echo "<td>{$h['ma_don_hang']}</td>";
            echo "<td>" . number_format($h['discount_amount']) . " VNĐ</td>";
            echo "<td>" . number_format($h['tong_tien']) . " VNĐ</td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($h['used_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "ℹ️ Bạn chưa sử dụng voucher nào<br>";
    }
} else {
    echo "⚠️ Chưa đăng nhập<br>";
}

echo "<hr>";
echo "<h2>✅ Hoàn tất kiểm tra!</h2>";
echo "<p><a href='checkout.php'>Đi đến trang thanh toán</a> | <a href='admin-promotions.php'>Quản lý khuyến mãi</a></p>";

$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 { color: #e91e63; }
    h2 { color: #333; margin-top: 20px; }
    table {
        background: white;
        width: 100%;
        margin: 10px 0;
        border-collapse: collapse;
    }
    th {
        background: #e91e63;
        color: white;
        padding: 10px;
    }
    td { padding: 8px; }
    tr:nth-child(even) { background: #f9f9f9; }
    a {
        color: #e91e63;
        text-decoration: none;
        font-weight: bold;
    }
    a:hover { text-decoration: underline; }
</style>
