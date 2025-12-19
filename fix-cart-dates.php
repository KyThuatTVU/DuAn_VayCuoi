<?php
/**
 * Script sửa lỗi ngày tháng trong giỏ hàng
 * Chạy file này để kiểm tra và sửa các ngày thuê trong quá khứ
 */

session_start();
require_once 'includes/config.php';

echo "<h1>Kiểm Tra và Sửa Ngày Tháng Trong Giỏ Hàng</h1>";
echo "<hr>";

// Debug: Kiểm tra kiểu dữ liệu của cột ngày
echo "<h2>🔍 Debug: Kiểu dữ liệu cột ngày</h2>";
$column_info = $conn->query("SHOW COLUMNS FROM gio_hang WHERE Field IN ('ngay_bat_dau_thue', 'ngay_tra_vay')");
if ($column_info && $column_info->num_rows > 0) {
    echo "<table border='1' cellpadding='5' style='margin-bottom: 20px;'>";
    echo "<tr style='background: #2196F3; color: white;'><th>Cột</th><th>Kiểu dữ liệu</th><th>Null</th><th>Default</th></tr>";
    while ($col = $column_info->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "<hr>";

// 1. Kiểm tra giỏ hàng có ngày trong quá khứ
echo "<h2>1. Kiểm tra giỏ hàng</h2>";
$check_query = "SELECT 
    gh.id,
    gh.nguoi_dung_id,
    nd.ho_ten,
    vc.ten_vay,
    gh.ngay_bat_dau_thue,
    gh.ngay_tra_vay,
    gh.so_ngay_thue,
    gh.created_at,
    DATEDIFF(gh.ngay_bat_dau_thue, NOW()) as days_diff
FROM gio_hang gh
JOIN nguoi_dung nd ON gh.nguoi_dung_id = nd.id
JOIN vay_cuoi vc ON gh.vay_id = vc.id
ORDER BY gh.created_at DESC";

$result = $conn->query($check_query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' style='width:100%; border-collapse: collapse;'>";
    echo "<tr style='background: #e91e63; color: white;'>";
    echo "<th>ID</th><th>User</th><th>Váy</th><th>Ngày thuê</th><th>Ngày trả</th><th>Số ngày</th><th>Trạng thái</th><th>Hành động</th>";
    echo "</tr>";
    
    $has_past_dates = false;
    
    while ($row = $result->fetch_assoc()) {
        $is_past = $row['days_diff'] < 0;
        $bg_color = $is_past ? '#ffebee' : '#f5f5f5';
        
        if ($is_past) $has_past_dates = true;
        
        echo "<tr style='background: {$bg_color};'>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['ho_ten']}</td>";
        echo "<td>{$row['ten_vay']}</td>";
        echo "<td>" . date('d/m/Y', strtotime($row['ngay_bat_dau_thue'])) . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($row['ngay_tra_vay'])) . "</td>";
        echo "<td>{$row['so_ngay_thue']} ngày</td>";
        
        if ($is_past) {
            echo "<td style='color: red; font-weight: bold;'>⚠️ Ngày trong quá khứ ({$row['days_diff']} ngày)</td>";
            echo "<td><a href='?fix={$row['id']}' style='color: #e91e63; font-weight: bold;'>Sửa ngay</a></td>";
        } else {
            echo "<td style='color: green;'>✅ OK</td>";
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    if (!$has_past_dates) {
        echo "<p style='color: green; font-weight: bold; margin-top: 20px;'>✅ Tất cả ngày thuê đều hợp lệ!</p>";
    }
} else {
    echo "<p>ℹ️ Không có sản phẩm nào trong giỏ hàng</p>";
}

echo "<hr>";

// 2. Xử lý sửa ngày
if (isset($_GET['fix'])) {
    $cart_id = intval($_GET['fix']);
    
    echo "<h2>2. Sửa ngày cho giỏ hàng ID: {$cart_id}</h2>";
    
    // Lấy thông tin giỏ hàng
    $get_cart = $conn->prepare("SELECT * FROM gio_hang WHERE id = ?");
    $get_cart->bind_param("i", $cart_id);
    $get_cart->execute();
    $cart = $get_cart->get_result()->fetch_assoc();
    
    if ($cart) {
        // Tính ngày mới (từ hôm nay)
        $new_start = date('Y-m-d', strtotime('+1 day')); // Ngày mai
        $new_end = date('Y-m-d', strtotime('+' . ($cart['so_ngay_thue'] + 1) . ' days'));
        
        // Cập nhật
        $update = $conn->prepare("UPDATE gio_hang SET ngay_bat_dau_thue = ?, ngay_tra_vay = ? WHERE id = ?");
        $update->bind_param("ssi", $new_start, $new_end, $cart_id);
        
        if ($update->execute()) {
            echo "<p style='color: green; font-weight: bold;'>✅ Đã sửa thành công!</p>";
            echo "<p>Ngày thuê mới: <strong>" . date('d/m/Y', strtotime($new_start)) . "</strong></p>";
            echo "<p>Ngày trả mới: <strong>" . date('d/m/Y', strtotime($new_end)) . "</strong></p>";
            echo "<p><a href='fix-cart-dates.php'>Quay lại kiểm tra</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Lỗi: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Không tìm thấy giỏ hàng</p>";
    }
    
    echo "<hr>";
}

// 3. Nút sửa tất cả
if (isset($_GET['fix_all'])) {
    echo "<h2>3. Sửa tất cả ngày trong quá khứ</h2>";
    
    $fix_query = "UPDATE gio_hang 
                  SET ngay_bat_dau_thue = DATE_ADD(NOW(), INTERVAL 1 DAY),
                      ngay_tra_vay = DATE_ADD(DATE_ADD(NOW(), INTERVAL 1 DAY), INTERVAL so_ngay_thue DAY)
                  WHERE ngay_bat_dau_thue < NOW()";
    
    if ($conn->query($fix_query)) {
        $affected = $conn->affected_rows;
        echo "<p style='color: green; font-weight: bold;'>✅ Đã sửa {$affected} mục trong giỏ hàng!</p>";
        echo "<p><a href='fix-cart-dates.php'>Quay lại kiểm tra</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Lỗi: " . $conn->error . "</p>";
    }
    
    echo "<hr>";
}

// 4. Hiển thị nút hành động
echo "<h2>Hành động</h2>";
echo "<div style='display: flex; gap: 10px; margin: 20px 0;'>";
echo "<a href='?fix_all=1' onclick='return confirm(\"Bạn có chắc muốn sửa tất cả ngày trong quá khứ?\")' style='background: #e91e63; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔧 Sửa Tất Cả</a>";
echo "<a href='cart.php' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🛒 Xem Giỏ Hàng</a>";
echo "<a href='fix-cart-dates.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔄 Làm Mới</a>";
echo "</div>";

// 5. Giải thích
echo "<hr>";
echo "<h2>ℹ️ Giải thích</h2>";
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Vấn đề:</strong> Giỏ hàng có ngày thuê trong quá khứ (ví dụ: 4 giờ trước, 6 ngày trước)</p>";
echo "<p><strong>Nguyên nhân:</strong> Dữ liệu test hoặc người dùng thêm vào giỏ từ lâu nhưng chưa thanh toán</p>";
echo "<p><strong>Giải pháp:</strong> Cập nhật ngày thuê thành ngày mai và tính lại ngày trả</p>";
echo "<p><strong>Lưu ý:</strong> Chỉ sửa dữ liệu trong giỏ hàng, không ảnh hưởng đến đơn hàng đã đặt</p>";
echo "</div>";

$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1400px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 { color: #e91e63; }
    h2 { color: #333; margin-top: 30px; }
    table {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    th {
        padding: 12px;
        text-align: left;
    }
    td {
        padding: 10px;
    }
    tr:hover {
        background: #f9f9f9 !important;
    }
    a {
        text-decoration: none;
    }
    a:hover {
        opacity: 0.8;
    }
</style>
