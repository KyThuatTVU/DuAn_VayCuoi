<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$coupon_code = strtoupper(trim($data['coupon_code'] ?? ''));

if (empty($coupon_code)) {
    echo json_encode(['success' => false, 'message' => 'Mã khuyến mãi không hợp lệ']);
    exit;
}

// Lấy thông tin coupon kèm số lần đã sử dụng
$stmt = $conn->prepare("SELECT km.*, 
    COALESCE(usage_stats.used_count, 0) as used_count
    FROM khuyen_mai km
    LEFT JOIN (
        SELECT coupon_code, COUNT(*) as used_count 
        FROM user_coupon_usage 
        GROUP BY coupon_code
    ) usage_stats ON km.code = usage_stats.coupon_code
    WHERE km.code = ? AND km.start_at <= NOW() AND km.end_at >= NOW()");
$stmt->bind_param("s", $coupon_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Mã khuyến mãi không tồn tại hoặc đã hết hạn']);
    exit;
}

$coupon = $result->fetch_assoc();

// Kiểm tra giới hạn sử dụng (so sánh used_count với usage_limit)
if ($coupon['usage_limit'] !== null && $coupon['used_count'] >= $coupon['usage_limit']) {
    echo json_encode(['success' => false, 'message' => 'Mã khuyến mãi đã hết lượt sử dụng']);
    exit;
}

// Kiểm tra user đã sử dụng coupon này chưa
$user_usage_check = $conn->prepare("SELECT COUNT(*) as usage_count FROM user_coupon_usage WHERE user_id = ? AND coupon_code = ?");
$user_usage_check->bind_param("is", $user_id, $coupon_code);
$user_usage_check->execute();
$user_usage = $user_usage_check->get_result()->fetch_assoc();

if ($user_usage['usage_count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Bạn đã sử dụng mã khuyến mãi này rồi']);
    exit;
}

// Lấy giỏ hàng để tính tổng tiền
$cart_query = $conn->prepare("SELECT 
    SUM(vc.gia_thue * gh.so_luong * gh.so_ngay_thue) as subtotal
FROM gio_hang gh
JOIN vay_cuoi vc ON gh.vay_id = vc.id
WHERE gh.nguoi_dung_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result()->fetch_assoc();

$subtotal = $cart_result['subtotal'] ?? 0;

// Kiểm tra điều kiện áp dụng
if ($subtotal < $coupon['min_order_amount']) {
    echo json_encode([
        'success' => false, 
        'message' => 'Đơn hàng tối thiểu ' . number_format($coupon['min_order_amount'], 0, ',', '.') . ' VNĐ để áp dụng mã này'
    ]);
    exit;
}

// Tính giảm giá
if ($coupon['type'] === 'percent') {
    $discount_amount = $subtotal * ($coupon['value'] / 100);
} else {
    $discount_amount = min($coupon['value'], $subtotal); // Không vượt quá tổng tiền
}

$service_fee = ($subtotal - $discount_amount) * 0.05;
$total = $subtotal - $discount_amount + $service_fee;

echo json_encode([
    'success' => true,
    'coupon' => $coupon,
    'discount_amount' => $discount_amount,
    'discount_formatted' => number_format($discount_amount, 0, ',', '.') . ' VNĐ',
    'total_amount' => $total,
    'total_formatted' => number_format($total, 0, ',', '.') . ' VNĐ',
    'message' => 'Áp dụng mã khuyến mãi thành công!'
]);
?>