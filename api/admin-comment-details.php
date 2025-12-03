<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Xử lý admin trả lời bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'admin_reply') {
        $comment_id = (int)($_POST['comment_id'] ?? 0);
        $type = $_POST['type'] ?? 'product';
        $noi_dung = trim($_POST['noi_dung'] ?? '');
        $admin_id = $_SESSION['admin_id'];
        
        if (empty($noi_dung)) {
            echo json_encode(['success' => false, 'message' => 'Nội dung không được để trống']);
            exit();
        }
        
        if ($type === 'product') {
            // Lấy vay_id từ bình luận gốc
            $parent = $conn->query("SELECT vay_id FROM binh_luan_san_pham WHERE id = $comment_id")->fetch_assoc();
            if (!$parent) {
                echo json_encode(['success' => false, 'message' => 'Bình luận không tồn tại']);
                exit();
            }
            
            $vay_id = $parent['vay_id'];
            $sql = "INSERT INTO binh_luan_san_pham (nguoi_dung_id, admin_id, is_admin_reply, vay_id, noi_dung, parent_id) VALUES (NULL, ?, 1, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisi", $admin_id, $vay_id, $noi_dung, $comment_id);
        } else {
            // Lấy bai_viet_id từ bình luận gốc
            $parent = $conn->query("SELECT bai_viet_id FROM binh_luan_bai_viet WHERE id = $comment_id")->fetch_assoc();
            if (!$parent) {
                echo json_encode(['success' => false, 'message' => 'Bình luận không tồn tại']);
                exit();
            }
            
            $bai_viet_id = $parent['bai_viet_id'];
            $sql = "INSERT INTO binh_luan_bai_viet (nguoi_dung_id, admin_id, is_admin_reply, bai_viet_id, noi_dung, parent_id) VALUES (NULL, ?, 1, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisi", $admin_id, $bai_viet_id, $noi_dung, $comment_id);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Đã trả lời bình luận thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $conn->error]);
        }
        exit();
    }
}

$comment_id = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'product';

if ($comment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit();
}

$html = '';

if ($type === 'product') {
    // Lấy bình luận chính
    $main = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt, vc.ten_vay, vc.ma_vay
        FROM binh_luan_san_pham bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        JOIN vay_cuoi vc ON bl.vay_id = vc.id 
        WHERE bl.id = $comment_id")->fetch_assoc();
    
    if (!$main) {
        echo json_encode(['success' => false, 'message' => 'Comment not found']);
        exit();
    }
    
    // Lấy replies
    $replies = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt
        FROM binh_luan_san_pham bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        WHERE bl.parent_id = $comment_id
        ORDER BY bl.created_at ASC")->fetch_all(MYSQLI_ASSOC);
    
    // Build HTML
    $avatar = $main['avt'] 
        ? '<img src="' . htmlspecialchars($main['avt']) . '" class="w-full h-full object-cover">'
        : '<span class="text-2xl">' . strtoupper(substr($main['ho_ten'], 0, 1)) . '</span>';
    
    $html .= '<div class="bg-gray-50 rounded-xl p-6 mb-4">';
    $html .= '<div class="flex items-start gap-4">';
    $html .= '<div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">' . $avatar . '</div>';
    $html .= '<div class="flex-1">';
    $html .= '<div class="flex items-center justify-between mb-2">';
    $html .= '<div>';
    $html .= '<p class="font-bold text-navy-900">' . htmlspecialchars($main['ho_ten']) . '</p>';
    $html .= '<p class="text-sm text-navy-500">' . htmlspecialchars($main['email']) . '</p>';
    $html .= '</div>';
    $html .= '<span class="text-sm text-navy-500">' . date('d/m/Y H:i', strtotime($main['created_at'])) . '</span>';
    $html .= '</div>';
    $html .= '<div class="mb-3">';
    $html .= '<p class="text-sm text-navy-600 mb-1"><strong>Sản phẩm:</strong> ' . htmlspecialchars($main['ten_vay']) . ' (' . htmlspecialchars($main['ma_vay']) . ')</p>';
    $html .= '<a href="product-detail.php?id=' . $main['vay_id'] . '" target="_blank" class="text-sm text-accent-500 hover:underline"><i class="fas fa-external-link-alt mr-1"></i>Xem sản phẩm</a>';
    $html .= '</div>';
    $html .= '<p class="text-navy-700 leading-relaxed">' . nl2br(htmlspecialchars($main['noi_dung'])) . '</p>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Lấy replies với thông tin admin
    $replies = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt, bl.is_admin_reply, bl.admin_id
        FROM binh_luan_san_pham bl 
        LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        WHERE bl.parent_id = $comment_id
        ORDER BY bl.created_at ASC")->fetch_all(MYSQLI_ASSOC);
    
    // Replies
    if (!empty($replies)) {
        $html .= '<div class="ml-8">';
        $html .= '<h4 class="font-bold text-navy-900 mb-3"><i class="fas fa-reply mr-2"></i>Các trả lời (' . count($replies) . ')</h4>';
        foreach ($replies as $reply) {
            $is_admin = $reply['is_admin_reply'] == 1;
            
            if ($is_admin) {
                $reply_avatar = '<i class="fas fa-user-shield text-lg"></i>';
                $reply_name = 'Tác giả';
                $reply_email = 'Admin';
                $border_color = 'border-pink-500';
                $bg_gradient = 'from-pink-500 to-red-500';
                $badge = '<span class="ml-2 px-2 py-0.5 bg-pink-100 text-pink-600 text-xs font-semibold rounded-full">Tác giả</span>';
            } else {
                $reply_avatar = $reply['avt'] 
                    ? '<img src="' . htmlspecialchars($reply['avt']) . '" class="w-full h-full object-cover">'
                    : '<span class="text-lg">' . strtoupper(substr($reply['ho_ten'], 0, 1)) . '</span>';
                $reply_name = htmlspecialchars($reply['ho_ten']);
                $reply_email = htmlspecialchars($reply['email']);
                $border_color = 'border-accent-500';
                $bg_gradient = 'from-green-400 to-blue-500';
                $badge = '';
            }
            
            $html .= '<div class="bg-white rounded-lg p-4 mb-3 border-l-4 ' . $border_color . '">';
            $html .= '<div class="flex items-start gap-3">';
            $html .= '<div class="w-12 h-12 rounded-full bg-gradient-to-br ' . $bg_gradient . ' flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">' . $reply_avatar . '</div>';
            $html .= '<div class="flex-1">';
            $html .= '<div class="flex items-center justify-between mb-2">';
            $html .= '<div>';
            $html .= '<p class="font-semibold text-navy-900">' . $reply_name . $badge . '</p>';
            $html .= '<p class="text-xs text-navy-500">' . $reply_email . '</p>';
            $html .= '</div>';
            $html .= '<span class="text-xs text-navy-500">' . date('d/m/Y H:i', strtotime($reply['created_at'])) . '</span>';
            $html .= '</div>';
            $html .= '<p class="text-sm text-navy-700">' . nl2br(htmlspecialchars($reply['noi_dung'])) . '</p>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
    }
    
    // Form trả lời của admin
    $html .= '<div class="mt-6 bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-4">';
    $html .= '<h4 class="font-bold text-navy-900 mb-3"><i class="fas fa-pen mr-2"></i>Trả lời với tư cách Tác giả</h4>';
    $html .= '<form id="adminReplyForm" onsubmit="submitAdminReply(event, ' . $comment_id . ', \'product\')">';
    $html .= '<textarea name="noi_dung" id="adminReplyContent" rows="3" class="w-full border border-gray-200 rounded-lg p-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="Nhập nội dung trả lời..."></textarea>';
    $html .= '<div class="mt-3 flex justify-end">';
    $html .= '<button type="submit" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition"><i class="fas fa-paper-plane mr-2"></i>Gửi trả lời</button>';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</div>';
    
} else {
    // Blog comments
    $main = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt, t.title, t.slug
        FROM binh_luan_bai_viet bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        JOIN tin_tuc_cuoi_hoi t ON bl.bai_viet_id = t.id 
        WHERE bl.id = $comment_id")->fetch_assoc();
    
    if (!$main) {
        echo json_encode(['success' => false, 'message' => 'Comment not found']);
        exit();
    }
    
    $replies = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt
        FROM binh_luan_bai_viet bl 
        JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        WHERE bl.parent_id = $comment_id
        ORDER BY bl.created_at ASC")->fetch_all(MYSQLI_ASSOC);
    
    $avatar = $main['avt'] 
        ? '<img src="' . htmlspecialchars($main['avt']) . '" class="w-full h-full object-cover">'
        : '<span class="text-2xl">' . strtoupper(substr($main['ho_ten'], 0, 1)) . '</span>';
    
    $html .= '<div class="bg-gray-50 rounded-xl p-6 mb-4">';
    $html .= '<div class="flex items-start gap-4">';
    $html .= '<div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">' . $avatar . '</div>';
    $html .= '<div class="flex-1">';
    $html .= '<div class="flex items-center justify-between mb-2">';
    $html .= '<div>';
    $html .= '<p class="font-bold text-navy-900">' . htmlspecialchars($main['ho_ten']) . '</p>';
    $html .= '<p class="text-sm text-navy-500">' . htmlspecialchars($main['email']) . '</p>';
    $html .= '</div>';
    $html .= '<span class="text-sm text-navy-500">' . date('d/m/Y H:i', strtotime($main['created_at'])) . '</span>';
    $html .= '</div>';
    $html .= '<div class="mb-3">';
    $html .= '<p class="text-sm text-navy-600 mb-1"><strong>Bài viết:</strong> ' . htmlspecialchars($main['title']) . '</p>';
    $html .= '<a href="blog-detail.php?slug=' . $main['slug'] . '" target="_blank" class="text-sm text-accent-500 hover:underline"><i class="fas fa-external-link-alt mr-1"></i>Xem bài viết</a>';
    $html .= '</div>';
    $html .= '<p class="text-navy-700 leading-relaxed">' . nl2br(htmlspecialchars($main['noi_dung'])) . '</p>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Lấy replies với thông tin admin
    $replies = $conn->query("SELECT bl.*, nd.ho_ten, nd.email, nd.avt, bl.is_admin_reply, bl.admin_id
        FROM binh_luan_bai_viet bl 
        LEFT JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id 
        WHERE bl.parent_id = $comment_id
        ORDER BY bl.created_at ASC")->fetch_all(MYSQLI_ASSOC);
    
    if (!empty($replies)) {
        $html .= '<div class="ml-8">';
        $html .= '<h4 class="font-bold text-navy-900 mb-3"><i class="fas fa-reply mr-2"></i>Các trả lời (' . count($replies) . ')</h4>';
        foreach ($replies as $reply) {
            $is_admin = $reply['is_admin_reply'] == 1;
            
            if ($is_admin) {
                $reply_avatar = '<i class="fas fa-user-shield text-lg"></i>';
                $reply_name = 'Tác giả';
                $reply_email = 'Admin';
                $border_color = 'border-pink-500';
                $bg_gradient = 'from-pink-500 to-red-500';
                $badge = '<span class="ml-2 px-2 py-0.5 bg-pink-100 text-pink-600 text-xs font-semibold rounded-full">Tác giả</span>';
            } else {
                $reply_avatar = $reply['avt'] 
                    ? '<img src="' . htmlspecialchars($reply['avt']) . '" class="w-full h-full object-cover">'
                    : '<span class="text-lg">' . strtoupper(substr($reply['ho_ten'], 0, 1)) . '</span>';
                $reply_name = htmlspecialchars($reply['ho_ten']);
                $reply_email = htmlspecialchars($reply['email']);
                $border_color = 'border-accent-500';
                $bg_gradient = 'from-green-400 to-blue-500';
                $badge = '';
            }
            
            $html .= '<div class="bg-white rounded-lg p-4 mb-3 border-l-4 ' . $border_color . '">';
            $html .= '<div class="flex items-start gap-3">';
            $html .= '<div class="w-12 h-12 rounded-full bg-gradient-to-br ' . $bg_gradient . ' flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">' . $reply_avatar . '</div>';
            $html .= '<div class="flex-1">';
            $html .= '<div class="flex items-center justify-between mb-2">';
            $html .= '<div>';
            $html .= '<p class="font-semibold text-navy-900">' . $reply_name . $badge . '</p>';
            $html .= '<p class="text-xs text-navy-500">' . $reply_email . '</p>';
            $html .= '</div>';
            $html .= '<span class="text-xs text-navy-500">' . date('d/m/Y H:i', strtotime($reply['created_at'])) . '</span>';
            $html .= '</div>';
            $html .= '<p class="text-sm text-navy-700">' . nl2br(htmlspecialchars($reply['noi_dung'])) . '</p>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
    }
    
    // Form trả lời của admin
    $html .= '<div class="mt-6 bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-4">';
    $html .= '<h4 class="font-bold text-navy-900 mb-3"><i class="fas fa-pen mr-2"></i>Trả lời với tư cách Tác giả</h4>';
    $html .= '<form id="adminReplyForm" onsubmit="submitAdminReply(event, ' . $comment_id . ', \'blog\')">';
    $html .= '<textarea name="noi_dung" id="adminReplyContent" rows="3" class="w-full border border-gray-200 rounded-lg p-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="Nhập nội dung trả lời..."></textarea>';
    $html .= '<div class="mt-3 flex justify-end">';
    $html .= '<button type="submit" class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition"><i class="fas fa-paper-plane mr-2"></i>Gửi trả lời</button>';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</div>';
}

echo json_encode(['success' => true, 'html' => $html]);
?>
