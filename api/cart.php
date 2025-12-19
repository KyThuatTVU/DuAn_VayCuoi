<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng',
        'require_login' => true
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addToCart($conn, $user_id);
        break;
    
    case 'update':
        updateCart($conn, $user_id);
        break;
    
    case 'remove':
        removeFromCart($conn, $user_id);
        break;
    
    case 'get':
        getCart($conn, $user_id);
        break;
    
    case 'count':
        getCartCount($conn, $user_id);
        break;
    
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Action không hợp lệ'
        ]);
}

// Thêm váy vào giỏ hàng (cho thuê)
function addToCart($conn, $user_id) {
    $vay_id = intval($_POST['vay_id'] ?? 0);
    $so_luong = intval($_POST['so_luong'] ?? 1);
    $ngay_bat_dau_thue = $_POST['ngay_bat_dau_thue'] ?? null;
    $ngay_tra_vay = $_POST['ngay_tra_vay'] ?? null;
    $so_ngay_thue = intval($_POST['so_ngay_thue'] ?? 1);
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    
    if ($vay_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Váy cưới không hợp lệ'
        ]);
        return;
    }
    
    // Validate ngày thuê
    if (empty($ngay_bat_dau_thue)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng chọn ngày bắt đầu thuê'
        ]);
        return;
    }
    
    // Tính số ngày thuê nếu có ngày trả
    if (!empty($ngay_tra_vay)) {
        $start = new DateTime($ngay_bat_dau_thue);
        $end = new DateTime($ngay_tra_vay);
        $diff = $start->diff($end);
        $so_ngay_thue = max(1, $diff->days); // Tối thiểu 1 ngày
    }
    
    // Tính ngày trả nếu chưa có
    if (empty($ngay_tra_vay)) {
        $start = new DateTime($ngay_bat_dau_thue);
        $start->modify("+{$so_ngay_thue} days");
        $ngay_tra_vay = $start->format('Y-m-d');
    }
    
    // Kiểm tra váy có tồn tại không
    $check_sql = "SELECT id, ten_vay, gia_thue, so_luong_ton FROM vay_cuoi WHERE id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $vay_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Váy cưới không tồn tại'
        ]);
        return;
    }
    
    $vay = $result->fetch_assoc();
    
    // Kiểm tra số lượng váy còn có thể cho thuê
    if ($vay['so_luong_ton'] < $so_luong) {
        echo json_encode([
            'success' => false,
            'message' => 'Váy không đủ để cho thuê. Chỉ còn ' . $vay['so_luong_ton'] . ' váy có sẵn'
        ]);
        return;
    }
    
    // TODO: Kiểm tra váy có bị trùng lịch thuê không (nâng cao)
    // Cần check xem trong khoảng thời gian này váy đã được thuê chưa
    
    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $check_cart = "SELECT id, so_luong FROM gio_hang WHERE nguoi_dung_id = ? AND vay_id = ?";
    $stmt = $conn->prepare($check_cart);
    $stmt->bind_param("ii", $user_id, $vay_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    
    if ($cart_result->num_rows > 0) {
        // Váy đã có trong giỏ - Cập nhật thông tin thuê
        $cart_item = $cart_result->fetch_assoc();
        
        $update_sql = "UPDATE gio_hang SET 
                       so_luong = ?, 
                       ngay_bat_dau_thue = ?, 
                       ngay_tra_vay = ?,
                       so_ngay_thue = ?,
                       ghi_chu = ?
                       WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("issisi", $so_luong, $ngay_bat_dau_thue, $ngay_tra_vay, $so_ngay_thue, $ghi_chu, $cart_item['id']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã cập nhật thông tin thuê váy trong giỏ hàng',
                'product_name' => $vay['ten_vay'],
                'rental_days' => $so_ngay_thue
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi cập nhật giỏ hàng: ' . $conn->error
            ]);
        }
    } else {
        // Thêm mới vào giỏ hàng
        $insert_sql = "INSERT INTO gio_hang (nguoi_dung_id, vay_id, so_luong, ngay_bat_dau_thue, ngay_tra_vay, so_ngay_thue, ghi_chu) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiissis", $user_id, $vay_id, $so_luong, $ngay_bat_dau_thue, $ngay_tra_vay, $so_ngay_thue, $ghi_chu);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm váy vào giỏ hàng để thuê',
                'product_name' => $vay['ten_vay'],
                'rental_days' => $so_ngay_thue
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi thêm vào giỏ hàng: ' . $conn->error
            ]);
        }
    }
}

// Cập nhật giỏ hàng
function updateCart($conn, $user_id) {
    $cart_id = intval($_POST['cart_id'] ?? 0);
    $so_luong = intval($_POST['so_luong'] ?? 1);
    
    if ($cart_id <= 0 || $so_luong <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ'
        ]);
        return;
    }
    
    $sql = "UPDATE gio_hang SET so_luong = ? WHERE id = ? AND nguoi_dung_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $so_luong, $cart_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi cập nhật'
        ]);
    }
}

// Xóa sản phẩm khỏi giỏ hàng
function removeFromCart($conn, $user_id) {
    $cart_id = intval($_POST['cart_id'] ?? $_GET['cart_id'] ?? 0);
    
    if ($cart_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ'
        ]);
        return;
    }
    
    $sql = "DELETE FROM gio_hang WHERE id = ? AND nguoi_dung_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi xóa'
        ]);
    }
}

// Lấy danh sách giỏ hàng (cho thuê váy)
function getCart($conn, $user_id) {
    $sql = "SELECT 
                gh.id as cart_id,
                gh.vay_id,
                vc.ten_vay,
                vc.ma_vay,
                vc.size as size_raw,
                vc.gia_thue as gia_thue_moi_ngay,
                gh.so_luong,
                DATE_FORMAT(gh.ngay_bat_dau_thue, '%Y-%m-%d') as ngay_bat_dau_thue,
                DATE_FORMAT(gh.ngay_tra_vay, '%Y-%m-%d') as ngay_tra_vay,
                gh.so_ngay_thue,
                gh.ghi_chu,
                (vc.gia_thue * gh.so_luong * gh.so_ngay_thue) as tong_tien_thue,
                gh.created_at
            FROM gio_hang gh
            JOIN vay_cuoi vc ON gh.vay_id = vc.id
            WHERE gh.nguoi_dung_id = ?
            ORDER BY gh.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        // Đảm bảo ngày được format đúng (YYYY-MM-DD)
        // Nếu ngày trong quá khứ, cập nhật thành ngày mai
        $today = date('Y-m-d');
        if ($row['ngay_bat_dau_thue'] < $today) {
            $row['ngay_qua_han'] = true;
            $row['thong_bao'] = 'Ngày thuê đã qua, vui lòng cập nhật lại';
        }
        
        // Parse size từ JSON hoặc ghi chú
        $size_display = '';
        
        // Kiểm tra ghi chú có chứa size không (format: "Size: XL. ...")
        if (!empty($row['ghi_chu']) && preg_match('/Size:\s*([A-Z0-9]+)/i', $row['ghi_chu'], $matches)) {
            $size_display = $matches[1];
        }
        // Nếu không có trong ghi chú, parse từ size_raw (JSON)
        elseif (!empty($row['size_raw'])) {
            $decoded = json_decode($row['size_raw'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Lấy các size active
                $active_sizes = [];
                foreach ($decoded as $s) {
                    if (!empty($s['active']) && !empty($s['name'])) {
                        $active_sizes[] = $s['name'];
                    }
                }
                $size_display = implode(', ', $active_sizes);
            } else {
                // Legacy format (chuỗi đơn giản)
                $size_display = $row['size_raw'];
            }
        }
        
        $row['size'] = $size_display;
        unset($row['size_raw']); // Xóa raw data
        
        $items[] = $row;
        $total += $row['tong_tien_thue'];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => count($items),
        'type' => 'rental' // Đánh dấu đây là giỏ hàng cho thuê
    ]);
}

// Lấy số lượng sản phẩm trong giỏ hàng
function getCartCount($conn, $user_id) {
    $sql = "SELECT SUM(so_luong) as total FROM gio_hang WHERE nguoi_dung_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'count' => intval($row['total'] ?? 0)
    ]);
}
?>
