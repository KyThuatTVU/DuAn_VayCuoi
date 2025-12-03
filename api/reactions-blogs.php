<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng chức năng này!', 'require_login' => true]);
        exit();
    }
}

// Lấy thống kê cảm xúc
if ($action === 'get') {
    $bai_viet_id = (int)($_GET['bai_viet_id'] ?? 0);
    
    if ($bai_viet_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ']);
        exit();
    }
    
    // Đếm số lượng từng loại cảm xúc
    $sql = "SELECT loai_cam_xuc, COUNT(*) as count 
            FROM cam_xuc_bai_viet 
            WHERE bai_viet_id = ? 
            GROUP BY loai_cam_xuc";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bai_viet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reactions = [
        'like' => 0,
        'love' => 0,
        'wow' => 0,
        'haha' => 0,
        'sad' => 0,
        'angry' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        $reactions[$row['loai_cam_xuc']] = (int)$row['count'];
    }
    
    // Kiểm tra cảm xúc của user hiện tại
    $user_reaction = null;
    if (isset($_SESSION['user_id'])) {
        $user_sql = "SELECT loai_cam_xuc FROM cam_xuc_bai_viet WHERE bai_viet_id = ? AND nguoi_dung_id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("ii", $bai_viet_id, $_SESSION['user_id']);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($user_row = $user_result->fetch_assoc()) {
            $user_reaction = $user_row['loai_cam_xuc'];
        }
    }
    
    echo json_encode([
        'success' => true, 
        'reactions' => $reactions,
        'user_reaction' => $user_reaction,
        'total' => array_sum($reactions)
    ]);
    exit();
}

// Thêm/Cập nhật cảm xúc
if ($action === 'toggle') {
    checkLogin();
    
    $bai_viet_id = (int)($_POST['bai_viet_id'] ?? 0);
    $loai_cam_xuc = $_POST['loai_cam_xuc'] ?? 'like';
    $user_id = $_SESSION['user_id'];
    
    if ($bai_viet_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ']);
        exit();
    }
    
    $valid_reactions = ['like', 'love', 'wow', 'haha', 'sad', 'angry'];
    if (!in_array($loai_cam_xuc, $valid_reactions)) {
        echo json_encode(['success' => false, 'message' => 'Loại cảm xúc không hợp lệ']);
        exit();
    }
    
    // Kiểm tra xem đã có cảm xúc chưa
    $check_sql = "SELECT loai_cam_xuc FROM cam_xuc_bai_viet WHERE bai_viet_id = ? AND nguoi_dung_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $bai_viet_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $current = $check_result->fetch_assoc();
        
        // Nếu click vào cùng loại cảm xúc thì xóa
        if ($current['loai_cam_xuc'] === $loai_cam_xuc) {
            $delete_sql = "DELETE FROM cam_xuc_bai_viet WHERE bai_viet_id = ? AND nguoi_dung_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $bai_viet_id, $user_id);
            $delete_stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Đã bỏ cảm xúc', 'action' => 'removed']);
        } else {
            // Cập nhật sang loại cảm xúc mới
            $update_sql = "UPDATE cam_xuc_bai_viet SET loai_cam_xuc = ? WHERE bai_viet_id = ? AND nguoi_dung_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sii", $loai_cam_xuc, $bai_viet_id, $user_id);
            $update_stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Đã cập nhật cảm xúc', 'action' => 'updated']);
        }
    } else {
        // Thêm cảm xúc mới
        $insert_sql = "INSERT INTO cam_xuc_bai_viet (nguoi_dung_id, bai_viet_id, loai_cam_xuc) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iis", $user_id, $bai_viet_id, $loai_cam_xuc);
        $insert_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Đã thêm cảm xúc', 'action' => 'added']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
?>
