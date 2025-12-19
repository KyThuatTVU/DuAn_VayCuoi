<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id'];

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

// Lấy danh sách voucher đang hoạt động
$voucher_query = $conn->prepare("SELECT 
    km.*,
    COALESCE(usage_stats.used_count, 0) as used_count,
    CASE WHEN user_usage.user_id IS NOT NULL THEN 1 ELSE 0 END as user_used
FROM khuyen_mai km
LEFT JOIN (
    SELECT coupon_code, COUNT(*) as used_count 
    FROM user_coupon_usage 
    GROUP BY coupon_code
) usage_stats ON km.code = usage_stats.coupon_code
LEFT JOIN user_coupon_usage user_usage ON km.code = user_usage.coupon_code AND user_usage.user_id = ?
WHERE km.start_at <= NOW() 
    AND km.end_at >= NOW()
ORDER BY 
    user_usage.user_id IS NULL DESC,
    km.min_order_amount <= ? DESC,
    km.value DESC,
    km.created_at DESC");

$voucher_query->bind_param("id", $user_id, $subtotal);
$voucher_query->execute();
$vouchers = $voucher_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Format dữ liệu
$formatted_vouchers = array_map(function($voucher) {
    return [
        'id' => $voucher['id'],
        'code' => $voucher['code'],
        'title' => $voucher['title'],
        'description' => $voucher['description'],
        'type' => $voucher['type'],
        'value' => floatval($voucher['value']),
        'min_order_amount' => floatval($voucher['min_order_amount']),
        'usage_limit' => $voucher['usage_limit'] ? intval($voucher['usage_limit']) : null,
        'used_count' => intval($voucher['used_count']),
        'user_used' => boolval($voucher['user_used']),
        'start_at' => $voucher['start_at'],
        'end_at' => $voucher['end_at']
    ];
}, $vouchers);

echo json_encode([
    'success' => true,
    'vouchers' => $formatted_vouchers,
    'subtotal' => $subtotal
]);
?>
