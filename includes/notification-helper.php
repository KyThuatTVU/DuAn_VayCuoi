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
    try {
        // Validate user_id
        $user_id = (int)$user_id;
        if ($user_id <= 0) {
            error_log("[createNotification] Invalid user_id: $user_id");
            return false;
        }
        
        // Kiểm tra bảng có tồn tại không
        $check = $conn->query("SHOW TABLES LIKE 'thong_bao'");
        if (!$check || $check->num_rows === 0) {
            error_log("[createNotification] Table thong_bao not found, creating...");
            // Tự động tạo bảng nếu chưa có
            $create_sql = "CREATE TABLE IF NOT EXISTS thong_bao (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                nguoi_dung_id BIGINT NOT NULL,
                loai VARCHAR(50) NOT NULL DEFAULT 'system',
                tieu_de VARCHAR(255) NOT NULL,
                noi_dung TEXT NOT NULL,
                link VARCHAR(500) NULL,
                reference_id BIGINT NULL,
                reference_type VARCHAR(50) NULL,
                da_doc TINYINT(1) DEFAULT 0,
                read_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_nguoi_dung_id (nguoi_dung_id),
                INDEX idx_da_doc (da_doc)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            if (!$conn->query($create_sql)) {
                error_log("[createNotification] Failed to create table: " . $conn->error);
                return false;
            }
        }
        
        // Convert reference_id to int or null
        $reference_id = $reference_id !== null ? (int)$reference_id : null;
        
        // Sử dụng query trực tiếp thay vì prepared statement để tránh lỗi type
        $user_id_safe = (int)$user_id;
        $type_safe = $conn->real_escape_string($type);
        $title_safe = $conn->real_escape_string($title);
        $content_safe = $conn->real_escape_string($content);
        $link_safe = $link ? "'" . $conn->real_escape_string($link) . "'" : "NULL";
        $ref_id_safe = $reference_id !== null ? (int)$reference_id : "NULL";
        $ref_type_safe = $reference_type ? "'" . $conn->real_escape_string($reference_type) . "'" : "NULL";
        
        $sql = "INSERT INTO thong_bao (nguoi_dung_id, loai, tieu_de, noi_dung, link, reference_id, reference_type) 
                VALUES ($user_id_safe, '$type_safe', '$title_safe', '$content_safe', $link_safe, $ref_id_safe, $ref_type_safe)";
        
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("[createNotification] INSERT error: " . $conn->error . " | SQL: " . $sql);
        } else {
            error_log("[createNotification] SUCCESS - Inserted notification for user $user_id, type: $type");
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("[createNotification] Exception: " . $e->getMessage());
        return false;
    }
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
        // Tự tạo bảng nếu chưa có - dùng cấu trúc từ file SQL
        $conn->query("CREATE TABLE IF NOT EXISTS admin_notifications (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(255) NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_is_read (is_read),
            INDEX idx_type (type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    
    // Tạo link dựa trên reference_type và reference_id
    $link = null;
    if ($reference_id && $reference_type) {
        switch ($reference_type) {
            case 'order':
                $link = "admin-order-detail.php?id=$reference_id";
                break;
            case 'user':
                $link = "admin-user-detail.php?id=$reference_id";
                break;
            case 'contact':
                $link = "admin-contacts.php";
                break;
            case 'booking':
                $link = "admin-bookings.php";
                break;
            case 'payment':
                $link = "admin-payments.php";
                break;
            case 'product':
                $link = "product-detail.php?id=$reference_id#comments";
                break;
            case 'blog':
                $link = "blog-detail.php?id=$reference_id#comments";
                break;
        }
    }
    
    // Dùng tên cột 'message' thay vì 'content' để khớp với database
    $sql = "INSERT INTO admin_notifications (type, title, message, link) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    
    $stmt->bind_param("ssss", $type, $title, $content, $link);
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
 * Thông báo khi có bình luận mới
 */
function notifyNewComment($conn, $comment_type, $item_id, $item_name, $user_name, $comment_content) {
    $type_text = $comment_type === 'product' ? 'sản phẩm' : 'bài viết';
    $short_content = mb_strlen($comment_content) > 50 ? mb_substr($comment_content, 0, 50) . '...' : $comment_content;
    
    error_log("[notifyNewComment] Type: $comment_type, Item: $item_name, User: $user_name, Content: $short_content");
    
    return createAdminNotification(
        $conn,
        'new_comment',
        'Bình luận mới về "' . $item_name . '" (' . $type_text . ')',
        "$user_name: \"$short_content\"",
        $item_id,
        $comment_type
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

// ============================================================
// USER INTERACTION NOTIFICATIONS
// ============================================================

/**
 * Thông báo khi có người trả lời bình luận
 * @param mysqli $conn Database connection
 * @param int $owner_user_id ID người sở hữu bình luận gốc (người nhận thông báo)
 * @param int $replier_user_id ID người trả lời
 * @param string $replier_name Tên người trả lời
 * @param string $comment_type Loại: 'product' hoặc 'blog'
 * @param int $item_id ID sản phẩm hoặc bài viết
 * @param string $item_name Tên sản phẩm hoặc bài viết
 * @param string $reply_content Nội dung trả lời (rút gọn)
 * @param int|null $comment_id ID của comment reply (để scroll đến đúng vị trí)
 * @return bool
 */
function notifyCommentReply($conn, $owner_user_id, $replier_user_id, $replier_name, $comment_type, $item_id, $item_name, $reply_content = '', $comment_id = null) {
    // Log bắt đầu
    error_log("[notifyCommentReply] START - Owner: $owner_user_id, Replier: $replier_user_id, Type: $comment_type, CommentID: " . ($comment_id ?? 'NULL'));
    
    // Validate parameters
    $owner_user_id = (int)$owner_user_id;
    $replier_user_id = (int)$replier_user_id;
    $item_id = (int)$item_id;
    
    // Không gửi thông báo cho chính mình
    if ($owner_user_id <= 0 || $owner_user_id == $replier_user_id) {
        error_log("[notifyCommentReply] SKIP - Same user or invalid owner ($owner_user_id)");
        return true;
    }
    
    // Kiểm tra bảng thong_bao tồn tại
    $check = $conn->query("SHOW TABLES LIKE 'thong_bao'");
    if (!$check || $check->num_rows === 0) {
        error_log("[notifyCommentReply] Table thong_bao not found, creating...");
        // Tự động tạo bảng nếu chưa có
        $create_sql = "CREATE TABLE IF NOT EXISTS thong_bao (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            nguoi_dung_id BIGINT NOT NULL,
            loai VARCHAR(50) NOT NULL DEFAULT 'system',
            tieu_de VARCHAR(255) NOT NULL,
            noi_dung TEXT NOT NULL,
            link VARCHAR(500) NULL,
            reference_id BIGINT NULL,
            reference_type VARCHAR(50) NULL,
            da_doc TINYINT(1) DEFAULT 0,
            read_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_nguoi_dung_id (nguoi_dung_id),
            INDEX idx_da_doc (da_doc)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $conn->query($create_sql);
    }
    
    $type_text = $comment_type === 'product' ? 'sản phẩm' : 'bài viết';
    
    // Tạo link với anchor đến đúng comment
    $comment_anchor = $comment_id ? "comment-$comment_id" : "comments";
    $link = $comment_type === 'product' 
        ? "product-detail.php?id=$item_id#$comment_anchor" 
        : "blog-detail.php?id=$item_id#$comment_anchor";
    
    // Rút gọn nội dung
    $short_content = mb_strlen($reply_content) > 50 ? mb_substr($reply_content, 0, 50) . '...' : $reply_content;
    
    // Escape strings
    $replier_name = $conn->real_escape_string($replier_name);
    $item_name = $conn->real_escape_string($item_name);
    $short_content = $conn->real_escape_string($short_content);
    
    $result = createNotification(
        $conn,
        $owner_user_id,
        'comment_reply',
        "$replier_name đã trả lời bình luận của bạn",
        "\"$short_content\" - trong $type_text \"$item_name\"",
        $link,
        $item_id,
        'comment_' . $comment_type
    );
    
    error_log("[notifyCommentReply] createNotification result: " . ($result ? 'SUCCESS' : 'FAILED') . " - MySQL Error: " . $conn->error);
    
    return $result;
}

/**
 * Thông báo khi có người thả cảm xúc vào bình luận
 * @param mysqli $conn Database connection
 * @param int $owner_user_id ID người sở hữu bình luận (người nhận thông báo)
 * @param int $reactor_user_id ID người thả cảm xúc
 * @param string $reactor_name Tên người thả cảm xúc
 * @param string $reaction_type Loại cảm xúc: like, love, haha, wow, sad, angry
 * @param string $comment_type Loại: 'product' hoặc 'blog'
 * @param int $item_id ID sản phẩm hoặc bài viết
 * @param string $item_name Tên sản phẩm hoặc bài viết
 * @return bool
 */
function notifyCommentReaction($conn, $owner_user_id, $reactor_user_id, $reactor_name, $reaction_type, $comment_type, $item_id, $item_name) {
    // Không gửi thông báo cho chính mình
    if ($owner_user_id == $reactor_user_id) {
        return true;
    }
    
    $reaction_text = [
        'like' => '👍 thích',
        'love' => '❤️ yêu thích',
        'haha' => '😄 cười',
        'wow' => '😮 ngạc nhiên',
        'sad' => '😢 buồn',
        'angry' => '😠 tức giận'
    ];
    
    $emoji = $reaction_text[$reaction_type] ?? '👍 thích';
    $type_text = $comment_type === 'product' ? 'sản phẩm' : 'bài viết';
    $link = $comment_type === 'product' 
        ? "product-detail.php?id=$item_id#comments" 
        : "blog-detail.php?id=$item_id#comments";
    
    return createNotification(
        $conn,
        $owner_user_id,
        'comment_reaction',
        "$reactor_name đã $emoji bình luận của bạn",
        "Trong $type_text \"$item_name\"",
        $link,
        $item_id,
        'reaction_' . $comment_type
    );
}

/**
 * Thông báo khi có người thả cảm xúc vào sản phẩm/bài viết (cho admin hoặc chủ bài viết)
 * @param mysqli $conn Database connection
 * @param int $reactor_user_id ID người thả cảm xúc
 * @param string $reactor_name Tên người thả cảm xúc
 * @param string $reaction_type Loại cảm xúc
 * @param string $item_type Loại: 'product' hoặc 'blog'
 * @param int $item_id ID sản phẩm hoặc bài viết
 * @param string $item_name Tên sản phẩm hoặc bài viết
 * @return bool
 */
function notifyItemReaction($conn, $reactor_user_id, $reactor_name, $reaction_type, $item_type, $item_id, $item_name) {
    $reaction_text = [
        'like' => '👍 thích',
        'love' => '❤️ yêu thích',
        'haha' => '😄 cười',
        'wow' => '😮 ngạc nhiên',
        'sad' => '😢 buồn',
        'angry' => '😠 tức giận'
    ];
    
    $emoji = $reaction_text[$reaction_type] ?? '👍 thích';
    $type_text = $item_type === 'product' ? 'sản phẩm' : 'bài viết';
    
    // Tạo thông báo cho admin
    return createAdminNotification(
        $conn,
        'item_reaction',
        "$reactor_name đã $emoji $type_text",
        "\"$item_name\"",
        $item_id,
        $item_type
    );
}
?>
