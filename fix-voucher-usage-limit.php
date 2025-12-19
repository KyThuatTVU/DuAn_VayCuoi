<?php
/**
 * Script sửa lỗi usage_limit bị giảm sai
 * 
 * Vấn đề: Trước đây hệ thống giảm usage_limit mỗi khi có người dùng voucher
 * Giải pháp: Không giảm usage_limit nữa, chỉ dựa vào bảng user_coupon_usage để đếm
 * 
 * Script này giúp admin reset lại usage_limit về giá trị ban đầu
 */

session_start();
require_once 'includes/config.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo "<h1>⛔ Bạn cần đăng nhập admin để sử dụng công cụ này</h1>";
    echo "<a href='admin-login.php'>Đăng nhập Admin</a>";
    exit;
}

echo "<h1>🔧 Công Cụ Sửa Lỗi Usage Limit Voucher</h1>";
echo "<hr>";

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_single') {
        $voucher_id = intval($_POST['voucher_id']);
        $new_limit = intval($_POST['new_limit']);
        
        $stmt = $conn->prepare("UPDATE khuyen_mai SET usage_limit = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_limit, $voucher_id);
        
        if ($stmt->execute()) {
            echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>
                ✅ Đã cập nhật usage_limit thành {$new_limit} cho voucher ID {$voucher_id}
            </div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>
                ❌ Lỗi: " . $conn->error . "
            </div>";
        }
    }
}

// Hiển thị danh sách voucher
echo "<h2>📋 Danh Sách Voucher</h2>";
echo "<p><strong>Lưu ý:</strong> Hệ thống mới sẽ so sánh <code>used_count</code> (số lần đã dùng) với <code>usage_limit</code> (giới hạn).</p>";
echo "<p>Nếu <code>usage_limit</code> bị giảm sai trước đó, bạn có thể reset lại ở đây.</p>";

$vouchers = $conn->query("
    SELECT 
        km.*,
        COALESCE(usage_stats.used_count, 0) as used_count
    FROM khuyen_mai km
    LEFT JOIN (
        SELECT coupon_code, COUNT(*) as used_count 
        FROM user_coupon_usage 
        GROUP BY coupon_code
    ) usage_stats ON km.code = usage_stats.coupon_code
    ORDER BY km.id DESC
");

if ($vouchers && $vouchers->num_rows > 0) {
    echo "<table border='1' cellpadding='8' style='width: 100%; border-collapse: collapse; background: white;'>";
    echo "<tr style='background: #e91e63; color: white;'>
        <th>ID</th>
        <th>Mã</th>
        <th>Tiêu đề</th>
        <th>Giá trị</th>
        <th>Usage Limit<br>(Giới hạn)</th>
        <th>Used Count<br>(Đã dùng)</th>
        <th>Còn lại</th>
        <th>Trạng thái</th>
        <th>Cập nhật</th>
    </tr>";
    
    while ($v = $vouchers->fetch_assoc()) {
        $remaining = $v['usage_limit'] !== null ? ($v['usage_limit'] - $v['used_count']) : '∞';
        $status = '';
        $row_style = '';
        
        if ($v['usage_limit'] !== null) {
            if ($v['usage_limit'] <= 0) {
                $status = '<span style="color: red; font-weight: bold;">⚠️ Limit = 0 (Cần sửa!)</span>';
                $row_style = 'background: #fff3cd;';
            } elseif ($v['used_count'] >= $v['usage_limit']) {
                $status = '<span style="color: orange;">🔒 Hết lượt</span>';
            } else {
                $status = '<span style="color: green;">✅ Còn dùng được</span>';
            }
        } else {
            $status = '<span style="color: blue;">♾️ Không giới hạn</span>';
        }
        
        $value_display = $v['type'] === 'percent' 
            ? $v['value'] . '%' 
            : number_format($v['value']) . ' VNĐ';
        
        echo "<tr style='{$row_style}'>";
        echo "<td>{$v['id']}</td>";
        echo "<td><strong>{$v['code']}</strong></td>";
        echo "<td>{$v['title']}</td>";
        echo "<td>{$value_display}</td>";
        echo "<td style='text-align: center;'>" . ($v['usage_limit'] ?? 'NULL') . "</td>";
        echo "<td style='text-align: center;'>{$v['used_count']}</td>";
        echo "<td style='text-align: center;'>{$remaining}</td>";
        echo "<td>{$status}</td>";
        echo "<td>
            <form method='POST' style='display: flex; gap: 5px;'>
                <input type='hidden' name='action' value='update_single'>
                <input type='hidden' name='voucher_id' value='{$v['id']}'>
                <input type='number' name='new_limit' value='" . ($v['usage_limit'] ?? '') . "' 
                       placeholder='Số lượng' style='width: 80px; padding: 5px;'>
                <button type='submit' style='background: #e91e63; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;'>
                    Cập nhật
                </button>
            </form>
        </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Không có voucher nào.</p>";
}

echo "<hr>";
echo "<h2>📖 Giải thích</h2>";
echo "<ul>
    <li><strong>Usage Limit:</strong> Số lần tối đa voucher có thể được sử dụng (admin đặt)</li>
    <li><strong>Used Count:</strong> Số lần voucher đã được sử dụng (đếm từ bảng user_coupon_usage)</li>
    <li><strong>Còn lại:</strong> = Usage Limit - Used Count</li>
    <li><strong>Lỗi cũ:</strong> Hệ thống cũ giảm usage_limit mỗi khi có người dùng, dẫn đến usage_limit = 0</li>
    <li><strong>Cách sửa:</strong> Reset usage_limit về giá trị ban đầu (ví dụ: 3, 10, 100...)</li>
</ul>";

echo "<p><a href='admin-promotions.php' style='color: #e91e63;'>← Quay lại Quản lý Khuyến mãi</a></p>";

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
    h2 { color: #333; margin-top: 20px; }
    table { margin: 15px 0; }
    code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
</style>
