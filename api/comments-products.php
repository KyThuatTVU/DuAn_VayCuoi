<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/notification-helper.php';

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

// Kiểm tra xem cột reply_to_id có tồn tại không
function hasReplyToColumn($conn, $table) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE 'reply_to_id'");
    return $result && $result->num_rows > 0;
}

// Lấy danh sách bình luận
if ($action === 'get') {
    $vay_id = (int)($_GET['vay_id'] ?? 0);
    
    if ($vay_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
        exit();
    }
    
    $hasReplyTo = hasReplyToColumn($conn, 'binh_luan_san_pham');
    
    $sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email, bl.is_admin_reply, bl.admin_id
            FROM binh_luan_san_pham bl
            LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
            WHERE bl.vay_id = ? AND bl.parent_id IS NULL
            ORDER BY bl.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vay_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Nếu là admin reply, đánh dấu is_author = true (frontend sẽ hiển thị "Admin")
        if ($row['is_admin_reply'] == 1) {
            $row['is_author'] = true;
        }
        
        // Lấy replies với thông tin người được reply
        if ($hasReplyTo) {
            $reply_sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email, bl.is_admin_reply, bl.admin_id, bl.reply_to_id,
                          reply_to.ho_ten as reply_to_name, reply_to_bl.is_admin_reply as reply_to_is_admin
                          FROM binh_luan_san_pham bl
                          LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
                          LEFT JOIN binh_luan_san_pham reply_to_bl ON bl.reply_to_id = reply_to_bl.id
                          LEFT JOIN nguoi_dung reply_to ON reply_to_bl.nguoi_dung_id = reply_to.id
                          WHERE bl.parent_id = ?
                          ORDER BY bl.created_at ASC";
        } else {
            $reply_sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email, bl.is_admin_reply, bl.admin_id
                          FROM binh_luan_san_pham bl
                          LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
                          WHERE bl.parent_id = ?
                          ORDER BY bl.created_at ASC";
        }
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
            // Xác định tên người được reply
            if ($hasReplyTo && isset($reply['reply_to_id']) && $reply['reply_to_id']) {
                if (isset($reply['reply_to_is_admin']) && $reply['reply_to_is_admin'] == 1) {
                    $reply['reply_to_name'] = 'Admin';
                }
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
    
    $vay_id = (int)($_POST['vay_id'] ?? 0);
    $noi_dung = trim($_POST['noi_dung'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $reply_to_id = $parent_id; // Lưu ID comment đang được reply (để hiển thị @tên)
    $user_id = $_SESSION['user_id'];
    
    // Debug log
    error_log("[COMMENTS-PRODUCTS] ADD - user_id: $user_id, vay_id: $vay_id, parent_id: " . ($parent_id ?? 'NULL') . ", reply_to_id: " . ($reply_to_id ?? 'NULL'));
    
    if ($vay_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
        exit();
    }
    
    if (empty($noi_dung)) {
        echo json_encode(['success' => false, 'message' => 'Nội dung bình luận không được để trống']);
        exit();
    }
    
    // Nếu reply vào một reply (nested reply), tìm root comment để tránh nested quá sâu
    if ($parent_id) {
        $check_parent = $conn->prepare("SELECT id, parent_id FROM binh_luan_san_pham WHERE id = ?");
        $check_parent->bind_param("i", $parent_id);
        $check_parent->execute();
        $parent_comment = $check_parent->get_result()->fetch_assoc();
        
        // Nếu parent comment cũng có parent_id (là reply), thì set parent_id về root comment
        // Nhưng giữ nguyên reply_to_id để biết đang trả lời ai
        if ($parent_comment && $parent_comment['parent_id']) {
            $parent_id = $parent_comment['parent_id'];
        }
    }
    
    // Kiểm tra cột reply_to_id có tồn tại không
    if (hasReplyToColumn($conn, 'binh_luan_san_pham')) {
        $sql = "INSERT INTO binh_luan_san_pham (nguoi_dung_id, vay_id, noi_dung, parent_id, reply_to_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisii", $user_id, $vay_id, $noi_dung, $parent_id, $reply_to_id);
    } else {
        $sql = "INSERT INTO binh_luan_san_pham (nguoi_dung_id, vay_id, noi_dung, parent_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $user_id, $vay_id, $noi_dung, $parent_id);
    }
    
    if ($stmt->execute()) {
        $comment_id = $stmt->insert_id;
        
        // Lấy thông tin bình luận vừa thêm
        $get_sql = "SELECT bl.*, nd.ho_ten, nd.avt, nd.email
                    FROM binh_luan_san_pham bl
                    JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
                    WHERE bl.id = ?";
        $get_stmt = $conn->prepare($get_sql);
        $get_stmt->bind_param("i", $comment_id);
        $get_stmt->execute();
        $comment = $get_stmt->get_result()->fetch_assoc();
        
        // Gửi thông báo cho người được trả lời (nếu có)
        if ($reply_to_id) {
            error_log("[COMMENTS-PRODUCTS] Attempting to send notification for reply_to_id: $reply_to_id");
            try {
                // Lấy thông tin comment gốc và sản phẩm
                // Kiểm tra xem cột is_admin_reply có tồn tại không
                $has_admin_reply_col = $conn->query("SHOW COLUMNS FROM binh_luan_san_pham LIKE 'is_admin_reply'");
                $select_admin_reply = ($has_admin_reply_col && $has_admin_reply_col->num_rows > 0) ? 'bl.is_admin_reply' : '0 as is_admin_reply';
                
                $original_sql = "SELECT bl.nguoi_dung_id, $select_admin_reply, v.ten_vay 
                                 FROM binh_luan_san_pham bl 
                                 JOIN vay_cuoi v ON bl.vay_id = v.id 
                                 WHERE bl.id = ?";
                $original_stmt = $conn->prepare($original_sql);
                if ($original_stmt) {
                    $original_stmt->bind_param("i", $reply_to_id);
                    $original_stmt->execute();
                    $original_comment = $original_stmt->get_result()->fetch_assoc();
                    
                    // Kiểm tra: có nguoi_dung_id (không phải admin reply) VÀ không phải chính mình
                    $is_admin_reply = isset($original_comment['is_admin_reply']) ? (int)$original_comment['is_admin_reply'] : 0;
                    $owner_user_id = isset($original_comment['nguoi_dung_id']) ? (int)$original_comment['nguoi_dung_id'] : 0;
                    
                    if ($original_comment && $owner_user_id > 0 && $is_admin_reply != 1 && $owner_user_id != $user_id) {
                        // Lấy tên người trả lời
                        $replier_name = $comment['ho_ten'] ?? 'Người dùng';
                        
                        // Gửi thông báo cho chủ bình luận gốc (truyền comment_id để scroll đến đúng vị trí)
                        $notify_result = notifyCommentReply(
                            $conn,
                            $owner_user_id,
                            $user_id,
                            $replier_name,
                            'product',
                            $vay_id,
                            $original_comment['ten_vay'] ?? 'Sản phẩm',
                            $noi_dung,
                            $comment_id  // ID của comment mới để scroll đến
                        );
                        // Log để debug
                        error_log("[COMMENT_REPLY] Product - Owner: $owner_user_id, Replier: $user_id, Name: $replier_name, CommentID: $comment_id, Result: " . ($notify_result ? 'SUCCESS' : 'FAILED'));
                    }
                }
            } catch (Exception $e) {
                // Log lỗi để debug
                error_log("Notification error in comments-products.php: " . $e->getMessage());
            }
        }
        
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
    $check_sql = "SELECT id FROM binh_luan_san_pham WHERE id = ? AND nguoi_dung_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $comment_id, $user_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này']);
        exit();
    }
    
    $sql = "DELETE FROM binh_luan_san_pham WHERE id = ?";
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
