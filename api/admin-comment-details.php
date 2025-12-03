<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
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
    
    // Replies
    if (!empty($replies)) {
        $html .= '<div class="ml-8">';
        $html .= '<h4 class="font-bold text-navy-900 mb-3"><i class="fas fa-reply mr-2"></i>Các trả lời (' . count($replies) . ')</h4>';
        foreach ($replies as $reply) {
            $reply_avatar = $reply['avt'] 
                ? '<img src="' . htmlspecialchars($reply['avt']) . '" class="w-full h-full object-cover">'
                : '<span class="text-lg">' . strtoupper(substr($reply['ho_ten'], 0, 1)) . '</span>';
            
            $html .= '<div class="bg-white rounded-lg p-4 mb-3 border-l-4 border-accent-500">';
            $html .= '<div class="flex items-start gap-3">';
            $html .= '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">' . $reply_avatar . '</div>';
            $html .= '<div class="flex-1">';
            $html .= '<div class="flex items-center justify-between mb-2">';
            $html .= '<div>';
            $html .= '<p class="font-semibold text-navy-900">' . htmlspecialchars($reply['ho_ten']) . '</p>';
            $html .= '<p class="text-xs text-navy-500">' . htmlspecialchars($reply['email']) . '</p>';
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
    
    if (!empty($replies)) {
        $html .= '<div class="ml-8">';
        $html .= '<h4 class="font-bold text-navy-900 mb-3"><i class="fas fa-reply mr-2"></i>Các trả lời (' . count($replies) . ')</h4>';
        foreach ($replies as $reply) {
            $reply_avatar = $reply['avt'] 
                ? '<img src="' . htmlspecialchars($reply['avt']) . '" class="w-full h-full object-cover">'
                : '<span class="text-lg">' . strtoupper(substr($reply['ho_ten'], 0, 1)) . '</span>';
            
            $html .= '<div class="bg-white rounded-lg p-4 mb-3 border-l-4 border-accent-500">';
            $html .= '<div class="flex items-start gap-3">';
            $html .= '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white font-bold overflow-hidden flex-shrink-0">' . $reply_avatar . '</div>';
            $html .= '<div class="flex-1">';
            $html .= '<div class="flex items-center justify-between mb-2">';
            $html .= '<div>';
            $html .= '<p class="font-semibold text-navy-900">' . htmlspecialchars($reply['ho_ten']) . '</p>';
            $html .= '<p class="text-xs text-navy-500">' . htmlspecialchars($reply['email']) . '</p>';
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
}

echo json_encode(['success' => true, 'html' => $html]);
?>
