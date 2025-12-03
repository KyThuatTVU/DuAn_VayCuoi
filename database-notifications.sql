-- ===================================================================
-- DATABASE CHO HỆ THỐNG THÔNG BÁO NGƯỜI DÙNG
-- ===================================================================

USE cua_hang_vay_cuoi_db;

-- Bảng thông báo
CREATE TABLE IF NOT EXISTS thong_bao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT NOT NULL COMMENT 'ID người nhận thông báo',
    loai ENUM('admin_reply', 'order_update', 'new_blog', 'promotion', 'system') NOT NULL COMMENT 'Loại thông báo',
    tieu_de VARCHAR(255) NOT NULL COMMENT 'Tiêu đề thông báo',
    noi_dung TEXT NOT NULL COMMENT 'Nội dung thông báo',
    link VARCHAR(500) NULL COMMENT 'Link đến trang liên quan',
    da_doc TINYINT(1) DEFAULT 0 COMMENT '0 = chưa đọc, 1 = đã đọc',
    reference_id INT NULL COMMENT 'ID tham chiếu (comment_id, order_id, blog_id...)',
    reference_type VARCHAR(50) NULL COMMENT 'Loại tham chiếu (comment, order, blog...)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL COMMENT 'Thời gian đọc',
    
    INDEX idx_nguoi_dung (nguoi_dung_id),
    INDEX idx_da_doc (da_doc),
    INDEX idx_loai (loai),
    INDEX idx_created (created_at),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng theo dõi bài viết mới (để gửi thông báo cho tất cả user)
CREATE TABLE IF NOT EXISTS thong_bao_blog_sent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bai_viet_id INT NOT NULL,
    nguoi_dung_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_blog_user (bai_viet_id, nguoi_dung_id),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
