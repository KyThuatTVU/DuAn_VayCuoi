<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => ''];

// Kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng chức năng này!', 'require_login' => true]);
        exit();
    }
}

// Lấy danh sách bình luận
if ($action === 'get') {
    $bai_viet_id = (int)($_GET['bai_viet_id'] ?? 0);
    
    if ($bai_viet_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ']);
        exit();
    }
    
    $sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email, bl.is_admin_reply, bl.admin_id
            FROM binh_luan_bai_viet bl
            LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
            WHERE bl.bai_viet_id = ? AND bl.parent_id IS NULL
            ORDER BY bl.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bai_viet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Nếu là admin reply, đánh dấu is_author = true (frontend sẽ hiển thị "Admin")
        if ($row['is_admin_reply'] == 1) {
            $row['is_author'] = true;
        }
        
        // Lấy replies
        $reply_sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email, bl.is_admin_reply, bl.admin_id
                      FROM binh_luan_bai_viet bl
                      LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
                      WHERE bl.parent_id = ?
                      ORDER BY bl.created_at ASC";
        $reply_stmt = $conn->prepare($reply_sql);
        $reply_stmt->bind_param("i", $row['id']);
        $reply_stmt->execute();
        $reply_result = $reply_stmt->get_result();
        
        $replies = [];
        while ($reply = $reply_result->fetch_assoc()) {
            // Nếu là admin reply, đánh dấu is_author = true (frontend sẽ hiển thị "Admin")
            if ($reply['is_admin_reply'] == 1) {
                $reply['is_author'] = true;
            }
            $replies[] = $reply;
        }
        
        $row['replies'] = $replies;
        $comments[] = $row;
    }
    
    echo json_encode(['success' => true, 'comments' => $comments]);
    exit();
}

// Thêm bình luận
if ($action === 'add') {
    checkLogin();
    
    $bai_viet_id = (int)($_POST['bai_viet_id'] ?? 0);
    $noi_dung = trim($_POST['noi_dung'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $user_id = $_SESSION['user_id'];
    
    if ($bai_viet_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID bài viết không hợp lệ']);
        exit();
    }
    
    if (empty($noi_dung)) {
        echo json_encode(['success' => false, 'message' => 'Nội dung bình luận không được để trống']);
        exit();
    }
    
    $sql = "INSERT INTO binh_luan_bai_viet (nguoi_dung_id, bai_viet_id, noi_dung, parent_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $user_id, $bai_viet_id, $noi_dung, $parent_id);
    
    if ($stmt->execute()) {
        $comment_id = $stmt->insert_id;
        
        // Lấy thông tin bình luận vừa thêm
        $get_sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email
                    FROM binh_luan_bai_viet bl
                    JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
                    WHERE bl.id = ?";
        $get_stmt = $conn->prepare($get_sql);
        $get_stmt->bind_param("i", $comment_id);
        $get_stmt->execute();
        $comment = $get_stmt->get_result()->fetch_assoc();
        
        echo json_encode(['success' => true, 'message' => 'Đã thêm bình luận thành công', 'comment' => $comment]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
    }
    exit();
}

// Xóa bình luận
if ($action === 'delete') {
    checkLogin();
    
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra quyền sở hữu
    $check_sql = "SELECT id FROM binh_luan_bai_viet WHERE id = ? AND nguoi_dung_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $comment_id, $user_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này']);
        exit();
    }
    
    $sql = "DELETE FROM binh_luan_bai_viet WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa bình luận']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
?>
