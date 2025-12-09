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

// ============================================================
// ADMIN NOTIFICATIONS
// ============================================================

/**
 * Tạo thông báo cho admin
 * @param mysqli $conn Database connection
 * @param string $type Loại: new_order, new_user, new_contact, new_booking, account_locked
 * @param string $title Tiêu đề
 * @param string $content Nội dung
 * @param int|null $reference_id ID tham chiếu
 * @param string|null $reference_type Loại: order, user, contact, booking
 * @return bool
 */
function createAdminNotification($conn, $type, $title, $content, $reference_id = null, $reference_type = null) {
    // Kiểm tra bảng có tồn tại không
    $check = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
    if (!$check || $check->num_rows === 0) {
        // Tự tạo bảng nếu chưa có
        $conn->query("CREATE TABLE IF NOT EXISTS admin_notifications (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            reference_id BIGINT NULL,
            reference_type VARCHAR(50) NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_is_read (is_read),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
    
    $sql = "INSERT INTO admin_notifications (type, title, content, reference_id, reference_type) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    
    $stmt->bind_param("sssis", $type, $title, $content, $reference_id, $reference_type);
    return $stmt->execute();
}

/**
 * Thông báo khi có đơn hàng mới
 */
function notifyNewOrder($conn, $order_id, $order_code, $customer_name, $total) {
    $total_formatted = number_format($total) . 'đ';
    return createAdminNotification(
        $conn,
        'new_order',
        'Đơn hàng mới #' . $order_code,
        "Khách hàng $customer_name vừa đặt đơn hàng mới với tổng giá trị $total_formatted",
        $order_id,
        'order'
    );
}

/**
 * Thông báo khi có khách hàng đăng ký mới
 */
function notifyNewUser($conn, $user_id, $user_name, $user_email) {
    return createAdminNotification(
        $conn,
        'new_user',
        'Khách hàng mới đăng ký',
        "Khách hàng $user_name ($user_email) vừa đăng ký tài khoản",
        $user_id,
        'user'
    );
}

/**
 * Thông báo khi có liên hệ mới
 */
function notifyNewContact($conn, $contact_id, $name, $subject) {
    return createAdminNotification(
        $conn,
        'new_contact',
        'Liên hệ mới từ ' . $name,
        "Chủ đề: $subject",
        $contact_id,
        'contact'
    );
}

/**
 * Thông báo khi có lịch hẹn mới
 */
function notifyNewBooking($conn, $booking_id, $name, $phone, $date, $time) {
    return createAdminNotification(
        $conn,
        'new_booking',
        'Lịch hẹn mới từ ' . $name,
        "Khách hàng $name ($phone) đặt lịch thử váy ngày $date lúc $time",
        $booking_id,
        'booking'
    );
}

/**
 * Thông báo khi tài khoản bị khóa do đăng nhập sai nhiều lần
 */
function notifyAccountLocked($conn, $user_id, $user_email, $reason) {
    return createAdminNotification(
        $conn,
        'account_locked',
        'Tài khoản bị khóa tự động',
        "Tài khoản $user_email đã bị khóa. Lý do: $reason",
        $user_id,
        'user'
    );
}

/**
 * Thông báo khi có thanh toán mới
 */
function notifyNewPayment($conn, $payment_id, $order_code, $amount, $method) {
    $amount_formatted = number_format($amount) . 'đ';
    $method_text = $method === 'momo' ? 'MoMo' : ($method === 'bank' ? 'Chuyển khoản' : $method);
    return createAdminNotification(
        $conn,
        'new_payment',
        'Thanh toán mới #' . $order_code,
        "Nhận thanh toán $amount_formatted qua $method_text",
        $payment_id,
        'payment'
    );
}
?>
