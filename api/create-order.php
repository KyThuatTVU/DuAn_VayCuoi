<?php
// Bắt lỗi PHP và chuyển thành JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Bắt đầu output buffering để tránh output không mong muốn
ob_start();

session_start();
require_once '../includes/config.php';
require_once '../includes/notification-helper.php';

// Xóa bất kỳ output nào trước đó
ob_clean();

header('Content-Type: application/json');

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode([
        'success' => false,
        'message' => 'PHP Error: ' . $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
});

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin từ form
$ho_ten = trim($_POST['ho_ten'] ?? '');
$so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
$dia_chi = trim($_POST['dia_chi'] ?? '');
$ghi_chu = trim($_POST['ghi_chu'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'qr_code';

// Thông tin địa chỉ chi tiết
$tinh_thanh = trim($_POST['tinh_thanh'] ?? '');
$quan_huyen = trim($_POST['quan_huyen'] ?? '');
$phuong_xa = trim($_POST['phuong_xa'] ?? '');
$dia_chi_cu_the = trim($_POST['dia_chi_cu_the'] ?? '');

// Validate
if (empty($ho_ten) || empty($so_dien_thoai) || empty($dia_chi)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Vui lòng điền đầy đủ thông tin',
        'missing' => [
            'ho_ten' => empty($ho_ten),
            'so_dien_thoai' => empty($so_dien_thoai),
            'dia_chi' => empty($dia_chi)
        ]
    ]);
    exit;
}

// Wrap toàn bộ trong try-catch
try {
    // Kiểm tra kết nối database
    if (!$conn || $conn->connect_error) {
        throw new Exception('Không thể kết nối database: ' . ($conn->connect_error ?? 'Unknown error'));
    }
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Lấy giỏ hàng
    $cart_query = $conn->prepare("SELECT 
        gh.*,
        vc.ten_vay,
        vc.ma_vay,
        vc.gia_thue,
        (vc.gia_thue * gh.so_luong * gh.so_ngay_thue) as tong_tien_thue
    FROM gio_hang gh
    JOIN vay_cuoi vc ON gh.vay_id = vc.id
    WHERE gh.nguoi_dung_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_items = $cart_query->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (empty($cart_items)) {
        throw new Exception('Giỏ hàng trống');
    }
    
    // Tính tổng tiền
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['tong_tien_thue'];
    }
    $service_fee = $subtotal * 0.05;
    $total = $subtotal + $service_fee;
    
    // Tạo mã đơn hàng
    $ma_don_hang = 'DH' . date('YmdHis') . rand(100, 999);
    
    // Tạo đơn hàng với đầy đủ thông tin (bao gồm địa chỉ chi tiết)
    $insert_order = $conn->prepare("INSERT INTO don_hang 
        (ma_don_hang, nguoi_dung_id, ho_ten, so_dien_thoai, dia_chi, tinh_thanh, quan_huyen, phuong_xa, dia_chi_cu_the, ghi_chu, 
         tong_tien, trang_thai, phuong_thuc_thanh_toan, trang_thai_thanh_toan, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, 'pending', NOW())");
    
    if (!$insert_order) {
        throw new Exception('Lỗi prepare order: ' . $conn->error);
    }
    
    $insert_order->bind_param("sissssssssds", 
        $ma_don_hang,      // s - string
        $user_id,          // i - integer
        $ho_ten,           // s - string
        $so_dien_thoai,    // s - string
        $dia_chi,          // s - string
        $tinh_thanh,       // s - string
        $quan_huyen,       // s - string
        $phuong_xa,        // s - string
        $dia_chi_cu_the,   // s - string
        $ghi_chu,          // s - string
        $total,            // d - double
        $payment_method    // s - string
    );
    
    if (!$insert_order->execute()) {
        throw new Exception('Lỗi insert order: ' . $insert_order->error);
    }
    
    $order_id = $conn->insert_id;
    
    // Tạo hóa đơn
    $ma_hoa_don = 'HD' . date('YmdHis') . rand(100, 999);
    $insert_invoice = $conn->prepare("INSERT INTO hoa_don 
        (don_hang_id, nguoi_dung_id, ma_hoa_don, tong_thanh_toan, status, created_at) 
        VALUES (?, ?, ?, ?, 'unpaid', NOW())");
    
    if (!$insert_invoice) {
        throw new Exception('Lỗi prepare invoice: ' . $conn->error);
    }
    
    $insert_invoice->bind_param("iisd", $order_id, $user_id, $ma_hoa_don, $total);
    
    if (!$insert_invoice->execute()) {
        throw new Exception('Lỗi insert invoice: ' . $insert_invoice->error);
    }
    
    $invoice_id = $conn->insert_id; // Không có dấu ngoặc!
    
    // Thêm chi tiết hóa đơn
    foreach ($cart_items as $item) {
        $insert_detail = $conn->prepare("INSERT INTO chi_tiet_hoa_don 
            (hoa_don_id, vay_id, description, amount, quantity) 
            VALUES (?, ?, ?, ?, ?)");
        
        $description = $item['ten_vay'] . ' - Thuê ' . $item['so_ngay_thue'] . ' ngày (' . 
                      date('d/m/Y', strtotime($item['ngay_bat_dau_thue'])) . ' - ' . 
                      date('d/m/Y', strtotime($item['ngay_tra_vay'])) . ')';
        
        $insert_detail->bind_param("iisdi", 
            $invoice_id, 
            $item['vay_id'], 
            $description, 
            $item['tong_tien_thue'], 
            $item['so_luong']
        );
        $insert_detail->execute();
    }
    
    // Tạo mã giao dịch cho thanh toán
    $ma_giao_dich = 'TT' . date('YmdHis') . rand(1000, 9999);
    $noi_dung = $ma_don_hang . ' ' . $ho_ten;
    $thoi_gian_het_han = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Lưu thông tin thanh toán
    $insert_payment = $conn->prepare("INSERT INTO thanh_toan 
        (hoa_don_id, don_hang_id, payment_gateway, transaction_id, amount, status, created_at) 
        VALUES (?, ?, 'qr_code', ?, ?, 'initiated', NOW())");
    $insert_payment->bind_param("iisd", $invoice_id, $order_id, $ma_giao_dich, $total);
    $insert_payment->execute();
    
    // Xóa giỏ hàng
    $delete_cart = $conn->prepare("DELETE FROM gio_hang WHERE nguoi_dung_id = ?");
    $delete_cart->bind_param("i", $user_id);
    $delete_cart->execute();
    
    // Commit transaction
    $conn->commit();
    
    // Lưu thông tin vào session
    $_SESSION['order_info'] = [
        'order_id' => $order_id,
        'ma_don_hang' => $ma_don_hang,
        'ma_giao_dich' => $ma_giao_dich,
        'total' => $total,
        'noi_dung' => $noi_dung,
        'thoi_gian_het_han' => $thoi_gian_het_han,
        'ho_ten' => $ho_ten,
        'so_dien_thoai' => $so_dien_thoai,
        'dia_chi' => $dia_chi
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Tạo đơn hàng thành công',
        'order_id' => $order_id,
        'ma_don_hang' => $ma_don_hang
    ]);
    
    // Gửi thông báo cho admin
    notifyNewOrder($conn, $order_id, $ma_don_hang, $ho_ten, $total);
    
} catch (Exception $e) {
    if ($conn) {
        $conn->rollback();
    }
    
    // Log lỗi chi tiết
    error_log('Create Order Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage(),
        'error_code' => $e->getCode(),
        'debug' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ]);
} catch (Error $e) {
    // Bắt cả PHP Error
    echo json_encode([
        'success' => false,
        'message' => 'PHP Error: ' . $e->getMessage(),
        'debug' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ]);
}

if ($conn) {
    $conn->close();
}

// Đảm bảo không có output nào khác
exit;
?>
