<?php
/**
 * Helper functions để tạo thông báo cho người dùng
 */

/**
 * Tạo thông báo mới
 * @param mysqli $conn Database connection
 * @param int $user_id ID người nhận
 * @param string $type Loại thông báo: admin_reply, order_update, new_blog, promotion, system
 * @param string $title Tiêu đề
 * @param string $content Nội dung
 * @param string|null $link Link đến trang liên quan
 * @param int|null $reference_id ID tham chiếu
 * @param string|null $reference_type Loại tham chiếu
 * @return bool
 */
function createNotification($conn, $user_id, $type, $title, $content, $link = null, $reference_id = null, $reference_type = null) {
    // Kiểm tra bảng có tồn tại không
    $check = $conn->query("SHOW TABLES LIKE 'thong_bao'");
    if (!$check || $check->num_rows === 0) {
        return false;
    }
    
    $sql = "INSERT INTO thong_bao (nguoi_dung_id, loai, tieu_de, noi_dung, link, reference_id, reference_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    
    $stmt->bind_param("issssss", $user_id, $type, $title, $content, $link, $reference_id, $reference_type);
    return $stmt->execute();
}

/**
 * Tạo thông báo khi admin trả lời comment
 */
function notifyAdminReply($conn, $user_id, $comment_type, $item_id, $item_name) {
    $type_text = $comment_type === 'product' ? 'sản phẩm' : 'bài viết';
    $link = $comment_type === 'product' 
        ? "product-detail.php?id=$item_id#comments" 
        : "blog-detail.php?id=$item_id#comments";
    
    return createNotification(
        $conn,
        $user_id,
        'admin_reply',
        'Admin đã trả lời bình luận của bạn',
        "Admin đã trả lời bình luận của bạn trong $type_text \"$item_name\"",
        $link,
        $item_id,
        'comment_' . $comment_type
    );
}

/**
 * Tạo thông báo khi cập nhật đơn hàng
 */
function notifyOrderUpdate($conn, $user_id, $order_id, $order_code, $new_status) {
    $status_text = [
        'cho_xac_nhan' => 'đang chờ xác nhận',
        'da_xac_nhan' => 'đã được xác nhận',
        'dang_chuan_bi' => 'đang được chuẩn bị',
        'dang_giao' => 'đang được giao',
        'da_giao' => 'đã giao thành công',
        'da_huy' => 'đã bị hủy',
        'hoan_thanh' => 'đã hoàn thành'
    ];
    
    $status = $status_text[$new_status] ?? $new_status;
    
    return createNotification(
        $conn,
        $user_id,
        'order_update',
        'Cập nhật đơn hàng #' . $order_code,
        "Đơn hàng #$order_code của bạn $status",
        "order-detail.php?id=$order_id",
        $order_id,
        'order'
    );
}

/**
 * Tạo thông báo bài viết mới cho tất cả user
 */
function notifyNewBlog($conn, $blog_id, $blog_title, $blog_slug) {
    // Kiểm tra bảng có tồn tại không
    $check = $conn->query("SHOW TABLES LIKE 'thong_bao'");
    if (!$check || $check->num_rows === 0) {
        return false;
    }
    
    // Lấy tất cả user
    $users = $conn->query("SELECT id FROM nguoi_dung");
    if (!$users) return false;
    
    $count = 0;
    while ($user = $users->fetch_assoc()) {
        $result = createNotification(
            $conn,
            $user['id'],
            'new_blog',
            'Bài viết mới: ' . $blog_title,
            "Chúng tôi vừa đăng bài viết mới \"$blog_title\". Xem ngay!",
            "blog-detail.php?slug=$blog_slug",
            $blog_id,
            'blog'
        );
        if ($result) $count++;
    }
    
    return $count;
}

/**
 * Tạo thông báo khuyến mãi
 */
function notifyPromotion($conn, $user_id, $title, $content, $link = null) {
    return createNotification(
        $conn,
        $user_id,
        'promotion',
        $title,
        $content,
        $link
    );
}
?>
