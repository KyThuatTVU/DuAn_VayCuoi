-- SQL tạo bảng thông báo cho người dùng
-- Chạy file này nếu bảng thong_bao chưa tồn tại

CREATE TABLE IF NOT EXISTS thong_bao (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT NOT NULL COMMENT 'ID người nhận thông báo',
    loai VARCHAR(50) NOT NULL DEFAULT 'system' COMMENT 'Loại thông báo: admin_reply, comment_reply, comment_reaction, order_update, new_blog, promotion, system',
    tieu_de VARCHAR(255) NOT NULL COMMENT 'Tiêu đề thông báo',
    noi_dung TEXT NOT NULL COMMENT 'Nội dung chi tiết',
    link VARCHAR(500) NULL COMMENT 'Link đến trang liên quan',
    reference_id BIGINT NULL COMMENT 'ID tham chiếu (đơn hàng, bài viết, sản phẩm...)',
    reference_type VARCHAR(50) NULL COMMENT 'Loại tham chiếu: order, blog, product, comment_product, comment_blog...',
    da_doc TINYINT(1) DEFAULT 0 COMMENT '0 = chưa đọc, 1 = đã đọc',
    read_at DATETIME NULL COMMENT 'Thời gian đọc',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_nguoi_dung_id (nguoi_dung_id),
    INDEX idx_da_doc (da_doc),
    INDEX idx_loai (loai),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thông báo cho người dùng';
