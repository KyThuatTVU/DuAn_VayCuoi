-- ===================================================================
-- BẢNG BÌNH LUẬN VÀ CẢM XÚC
-- ===================================================================

USE cua_hang_vay_cuoi_db;

-- Bảng bình luận sản phẩm
CREATE TABLE IF NOT EXISTS binh_luan_san_pham (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   nguoi_dung_id BIGINT NOT NULL,
   vay_id BIGINT NOT NULL,
   noi_dung TEXT NOT NULL,
   parent_id BIGINT NULL COMMENT 'ID bình luận cha (cho reply)',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE CASCADE,
   FOREIGN KEY (parent_id) REFERENCES binh_luan_san_pham(id) ON DELETE CASCADE,
   INDEX idx_vay_id (vay_id),
   INDEX idx_nguoi_dung_id (nguoi_dung_id),
   INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bình luận sản phẩm váy cưới';

-- Bảng bình luận bài viết
CREATE TABLE IF NOT EXISTS binh_luan_bai_viet (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   nguoi_dung_id BIGINT NOT NULL,
   bai_viet_id BIGINT NOT NULL,
   noi_dung TEXT NOT NULL,
   parent_id BIGINT NULL COMMENT 'ID bình luận cha (cho reply)',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
   FOREIGN KEY (bai_viet_id) REFERENCES tin_tuc_cuoi_hoi(id) ON DELETE CASCADE,
   FOREIGN KEY (parent_id) REFERENCES binh_luan_bai_viet(id) ON DELETE CASCADE,
   INDEX idx_bai_viet_id (bai_viet_id),
   INDEX idx_nguoi_dung_id (nguoi_dung_id),
   INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bình luận bài viết tin tức';

-- Bảng cảm xúc sản phẩm
CREATE TABLE IF NOT EXISTS cam_xuc_san_pham (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   nguoi_dung_id BIGINT NOT NULL,
   vay_id BIGINT NOT NULL,
   loai_cam_xuc ENUM('like', 'love', 'wow', 'haha', 'sad', 'angry') NOT NULL DEFAULT 'like',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE CASCADE,
   UNIQUE KEY unique_user_product_reaction (nguoi_dung_id, vay_id),
   INDEX idx_vay_id (vay_id),
   INDEX idx_loai_cam_xuc (loai_cam_xuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cảm xúc cho sản phẩm váy cưới';

-- Bảng cảm xúc bài viết
CREATE TABLE IF NOT EXISTS cam_xuc_bai_viet (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   nguoi_dung_id BIGINT NOT NULL,
   bai_viet_id BIGINT NOT NULL,
   loai_cam_xuc ENUM('like', 'love', 'wow', 'haha', 'sad', 'angry') NOT NULL DEFAULT 'like',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
   FOREIGN KEY (bai_viet_id) REFERENCES tin_tuc_cuoi_hoi(id) ON DELETE CASCADE,
   UNIQUE KEY unique_user_post_reaction (nguoi_dung_id, bai_viet_id),
   INDEX idx_bai_viet_id (bai_viet_id),
   INDEX idx_loai_cam_xuc (loai_cam_xuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cảm xúc cho bài viết tin tức';

-- Thêm dữ liệu mẫu (chỉ khi có người dùng trong database)
-- Kiểm tra và thêm bình luận sản phẩm
INSERT INTO binh_luan_san_pham (nguoi_dung_id, vay_id, noi_dung)
SELECT 1, 1, 'Váy này đẹp quá! Mình rất thích thiết kế công chúa.'
FROM nguoi_dung WHERE id = 1 LIMIT 1;

INSERT INTO binh_luan_san_pham (nguoi_dung_id, vay_id, noi_dung)
SELECT 2, 1, 'Chất liệu váy có tốt không ạ? Mình đang cân nhắc thuê.'
FROM nguoi_dung WHERE id = 2 LIMIT 1;

INSERT INTO binh_luan_san_pham (nguoi_dung_id, vay_id, noi_dung)
SELECT 1, 2, 'Váy đuôi cá này tôn dáng lắm, mình đã thuê và rất hài lòng!'
FROM nguoi_dung WHERE id = 1 LIMIT 1;

-- Thêm dữ liệu mẫu cho cảm xúc sản phẩm
INSERT INTO cam_xuc_san_pham (nguoi_dung_id, vay_id, loai_cam_xuc)
SELECT 1, 1, 'love'
FROM nguoi_dung WHERE id = 1 LIMIT 1;

INSERT INTO cam_xuc_san_pham (nguoi_dung_id, vay_id, loai_cam_xuc)
SELECT 2, 1, 'like'
FROM nguoi_dung WHERE id = 2 LIMIT 1;

INSERT INTO cam_xuc_san_pham (nguoi_dung_id, vay_id, loai_cam_xuc)
SELECT 1, 2, 'love'
FROM nguoi_dung WHERE id = 1 LIMIT 1;

INSERT INTO cam_xuc_san_pham (nguoi_dung_id, vay_id, loai_cam_xuc)
SELECT 2, 3, 'like'
FROM nguoi_dung WHERE id = 2 LIMIT 1;

-- Thêm dữ liệu mẫu cho bình luận bài viết
INSERT INTO binh_luan_bai_viet (nguoi_dung_id, bai_viet_id, noi_dung)
SELECT 1, 1, 'Bài viết rất hữu ích! Cảm ơn admin đã chia sẻ.'
FROM nguoi_dung WHERE id = 1 LIMIT 1;

INSERT INTO binh_luan_bai_viet (nguoi_dung_id, bai_viet_id, noi_dung)
SELECT 2, 1, 'Xu hướng váy cưới năm nay thật sự đẹp và hiện đại.'
FROM nguoi_dung WHERE id = 2 LIMIT 1;

-- Thêm dữ liệu mẫu cho cảm xúc bài viết
INSERT INTO cam_xuc_bai_viet (nguoi_dung_id, bai_viet_id, loai_cam_xuc)
SELECT 1, 1, 'love'
FROM nguoi_dung WHERE id = 1 LIMIT 1;

INSERT INTO cam_xuc_bai_viet (nguoi_dung_id, bai_viet_id, loai_cam_xuc)
SELECT 2, 1, 'like'
FROM nguoi_dung WHERE id = 2 LIMIT 1;
