-- ===================================================================
-- CẬP NHẬT DATABASE CHO ADMIN TRẢ LỜI BÌNH LUẬN
-- ===================================================================

USE cua_hang_vay_cuoi_db;

-- Thêm cột admin_id vào bảng bình luận sản phẩm
ALTER TABLE binh_luan_san_pham 
ADD COLUMN admin_id INT NULL COMMENT 'ID admin nếu là admin trả lời' AFTER nguoi_dung_id,
ADD COLUMN is_admin_reply TINYINT(1) DEFAULT 0 COMMENT '1 = bình luận của admin' AFTER admin_id,
ADD FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL;

-- Thêm cột admin_id vào bảng bình luận bài viết
ALTER TABLE binh_luan_bai_viet 
ADD COLUMN admin_id INT NULL COMMENT 'ID admin nếu là admin trả lời' AFTER nguoi_dung_id,
ADD COLUMN is_admin_reply TINYINT(1) DEFAULT 0 COMMENT '1 = bình luận của admin' AFTER admin_id,
ADD FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL;

-- Cập nhật constraint để cho phép nguoi_dung_id NULL khi admin trả lời
ALTER TABLE binh_luan_san_pham MODIFY COLUMN nguoi_dung_id BIGINT NULL;
ALTER TABLE binh_luan_bai_viet MODIFY COLUMN nguoi_dung_id BIGINT NULL;

-- Thêm cột reply_to_id để lưu ID comment đang được reply (hiển thị @tên)
ALTER TABLE binh_luan_san_pham 
ADD COLUMN reply_to_id INT NULL COMMENT 'ID bình luận đang được trả lời (để hiển thị @tên)' AFTER parent_id;

ALTER TABLE binh_luan_bai_viet 
ADD COLUMN reply_to_id INT NULL COMMENT 'ID bình luận đang được trả lời (để hiển thị @tên)' AFTER parent_id;
