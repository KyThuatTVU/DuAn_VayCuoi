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

// Thêm sản phẩm vào giỏ hàng
function addToCart($conn, $user_id) {
    $vay_id = intval($_POST['vay_id'] ?? 0);
    $so_luong = intval($_POST['so_luong'] ?? 1);
    $ngay_thue = $_POST['ngay_thue'] ?? null;
    $so_ngay_thue = intval($_POST['so_ngay_thue'] ?? 1);
    
    if ($vay_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không hợp lệ'
        ]);
        return;
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
    
    // Kiểm tra số lượng tồn kho
    if ($vay['so_luong_ton'] < $so_luong) {
        echo json_encode([
            'success' => false,
            'message' => 'Số lượng váy không đủ. Chỉ còn ' . $vay['so_luong_ton'] . ' váy'
        ]);
        return;
    }
    
    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $check_cart = "SELECT id, so_luong FROM gio_hang WHERE nguoi_dung_id = ? AND vay_id = ?";
    $stmt = $conn->prepare($check_cart);
    $stmt->bind_param("ii", $user_id, $vay_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    
    if ($cart_result->num_rows > 0) {
        // Cập nhật số lượng
        $cart_item = $cart_result->fetch_assoc();
        $new_quantity = $cart_item['so_luong'] + $so_luong;
        
        if ($new_quantity > $vay['so_luong_ton']) {
            echo json_encode([
                'success' => false,
                'message' => 'Số lượng vượt quá tồn kho'
            ]);
            return;
        }
        
        $update_sql = "UPDATE gio_hang SET so_luong = ?, so_ngay_thue = ?, ngay_thue = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iisi", $new_quantity, $so_ngay_thue, $ngay_thue, $cart_item['id']);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã cập nhật số lượng trong giỏ hàng',
                'product_name' => $vay['ten_vay']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi cập nhật giỏ hàng'
            ]);
        }
    } else {
        // Thêm mới vào giỏ hàng
        $insert_sql = "INSERT INTO gio_hang (nguoi_dung_id, vay_id, so_luong, ngay_thue, so_ngay_thue) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiisi", $user_id, $vay_id, $so_luong, $ngay_thue, $so_ngay_thue);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'product_name' => $vay['ten_vay']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi thêm vào giỏ hàng'
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

// Lấy danh sách giỏ hàng
function getCart($conn, $user_id) {
    $sql = "SELECT 
                gh.id as cart_id,
                gh.vay_id,
                vc.ten_vay,
                vc.ma_vay,
                vc.gia_thue,
                gh.so_luong,
                gh.ngay_thue,
                gh.so_ngay_thue,
                (vc.gia_thue * gh.so_luong * gh.so_ngay_thue) as tong_tien,
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
        $items[] = $row;
        $total += $row['tong_tien'];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => count($items)
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
