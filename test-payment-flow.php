<?php
require_once 'includes/config.php';

echo "<h1>Test Payment Flow</h1>";
echo "<style>
    body { font-family: Arial; max-width: 1200px; margin: 20px auto; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f5f5f5; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
</style>";

// Lấy order_id từ URL hoặc lấy đơn hàng mới nhất
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    // Lấy 5 đơn hàng mới nhất
    $orders = $conn->query("SELECT id, ma_don_hang, trang_thai_thanh_toan, tong_tien, created_at 
                            FROM don_hang 
                            ORDER BY created_at DESC 
                            LIMIT 5");
    
    echo "<h2>Chọn đơn hàng để test:</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Mã đơn hàng</th><th>Trạng thái TT</th><th>Tổng tiền</th><th>Ngày tạo</th><th>Action</th></tr>";
    
    while ($order = $orders->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $order['id'] . "</td>";
        echo "<td>" . $order['ma_don_hang'] . "</td>";
        echo "<td>" . $order['trang_thai_thanh_toan'] . "</td>";
        echo "<td>" . number_format($order['tong_tien']) . "</td>";
        echo "<td>" . $order['created_at'] . "</td>";
        echo "<td><a href='?order_id=" . $order['id'] . "'>Test</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    exit;
}

// Kiểm tra đơn hàng
echo "<h2>Đơn hàng #$order_id</h2>";

$order = $conn->query("SELECT * FROM don_hang WHERE id = $order_id")->fetch_assoc();

if (!$order) {
    echo "<p class='error'>Không tìm thấy đơn hàng</p>";
    exit;
}

echo "<h3>Thông tin đơn hàng:</h3>";
echo "<table>";
echo "<tr><th>Trường</th><th>Giá trị</th></tr>";
echo "<tr><td>Mã đơn hàng</td><td>" . $order['ma_don_hang'] . "</td></tr>";
echo "<tr><td>Tổng tiền</td><td>" . number_format($order['tong_tien']) . " VNĐ</td></tr>";
echo "<tr><td>Trạng thái đơn</td><td>" . $order['trang_thai'] . "</td></tr>";
echo "<tr><td>Trạng thái thanh toán</td><td><strong>" . $order['trang_thai_thanh_toan'] . "</strong></td></tr>";
echo "<tr><td>Ngày tạo</td><td>" . $order['created_at'] . "</td></tr>";
echo "</table>";

// Kiểm tra giao dịch thanh toán
echo "<h3>Giao dịch thanh toán:</h3>";
$payments = $conn->query("SELECT * FROM thanh_toan WHERE don_hang_id = $order_id ORDER BY created_at DESC");

if ($payments->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Gateway</th><th>Transaction ID</th><th>Amount</th><th>Status</th><th>Created</th><th>Paid At</th></tr>";
    
    while ($payment = $payments->fetch_assoc()) {
        $status_class = match($payment['status']) {
            'success' => 'success',
            'failed' => 'error',
            'initiated' => 'warning',
            default => ''
        };
        
        echo "<tr>";
        echo "<td>" . $payment['id'] . "</td>";
        echo "<td>" . $payment['payment_gateway'] . "</td>";
        echo "<td>" . substr($payment['transaction_id'], 0, 30) . "...</td>";
        echo "<td>" . number_format($payment['amount']) . "</td>";
        echo "<td class='$status_class'><strong>" . $payment['status'] . "</strong></td>";
        echo "<td>" . $payment['created_at'] . "</td>";
        echo "<td>" . ($payment['paid_at'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>Chưa có giao dịch thanh toán nào</p>";
}

// Kiểm tra logic
echo "<h3>Kiểm tra logic:</h3>";
echo "<ul>";

// Check 1: Trạng thái đơn hàng vs giao dịch
if ($order['trang_thai_thanh_toan'] === 'paid') {
    $success_payment = $conn->query("SELECT * FROM thanh_toan WHERE don_hang_id = $order_id AND status = 'success'")->fetch_assoc();
    if ($success_payment) {
        echo "<li class='success'>✓ Đơn hàng đã thanh toán và có giao dịch thành công</li>";
    } else {
        echo "<li class='error'>✗ Đơn hàng đã thanh toán nhưng KHÔNG có giao dịch thành công trong DB</li>";
    }
} else {
    echo "<li class='warning'>⚠ Đơn hàng chưa thanh toán (trạng thái: " . $order['trang_thai_thanh_toan'] . ")</li>";
}

// Check 2: Có giao dịch success nhưng đơn hàng chưa paid
$success_payment = $conn->query("SELECT * FROM thanh_toan WHERE don_hang_id = $order_id AND status = 'success'")->fetch_assoc();
if ($success_payment && $order['trang_thai_thanh_toan'] !== 'paid') {
    echo "<li class='error'>✗ CÓ giao dịch thành công nhưng đơn hàng CHƯA được đánh dấu là paid!</li>";
    echo "<li><a href='?order_id=$order_id&fix=1'>Click để sửa</a></li>";
}

// Check 3: Thời gian
$minutes_ago = round((time() - strtotime($order['created_at'])) / 60);
if ($order['trang_thai_thanh_toan'] === 'pending' && $minutes_ago < 10) {
    echo "<li class='success'>✓ Đơn hàng còn trong thời gian thanh toán ($minutes_ago phút, còn " . (10 - $minutes_ago) . " phút)</li>";
} elseif ($order['trang_thai_thanh_toan'] === 'pending' && $minutes_ago >= 10) {
    echo "<li class='warning'>⚠ Đơn hàng đã hết hạn thanh toán ($minutes_ago phút)</li>";
}

echo "</ul>";

// Fix nếu có tham số
if (isset($_GET['fix']) && $success_payment && $order['trang_thai_thanh_toan'] !== 'paid') {
    echo "<h3>Đang sửa...</h3>";
    $conn->query("UPDATE don_hang SET trang_thai_thanh_toan = 'paid', trang_thai = 'processing', updated_at = NOW() WHERE id = $order_id");
    echo "<p class='success'>✓ Đã cập nhật đơn hàng thành 'paid'</p>";
    echo "<p><a href='?order_id=$order_id'>Refresh</a></p>";
}

echo "<hr>";
echo "<p><a href='?'>← Chọn đơn hàng khác</a> | <a href='my-orders.php'>Xem đơn hàng</a> | <a href='admin-payments.php'>Admin Payments</a></p>";
?>
