CREATE DATABASE IF NOT EXISTS cua_hang_vay_cuoi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE cua_hang_vay_cuoi_db;

-- ===================================================================
-- PHẦN 1: CÁC BẢNG CHÍNH (BẢNG CHA) - PHẢI TẠO TRƯỚC
-- ===================================================================

-- Bảng người dùng
CREATE TABLE nguoi_dung (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   ho_ten VARCHAR(255) NOT NULL,
   email VARCHAR(150) NOT NULL UNIQUE,
   mat_khau VARCHAR(255) NOT NULL,
   so_dien_thoai VARCHAR(30),
   dia_chi TEXT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng quản trị viên
CREATE TABLE admin (
   id INT AUTO_INCREMENT PRIMARY KEY,
   username VARCHAR(100) NOT NULL UNIQUE,
   password VARCHAR(255) NOT NULL,
   full_name VARCHAR(255),
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng váy cưới
CREATE TABLE vay_cuoi (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   ma_vay VARCHAR(50) UNIQUE NOT NULL,
   ten_vay VARCHAR(255) NOT NULL,
   mo_ta TEXT,
   gia_thue DECIMAL(12,2) NOT NULL,
   so_luong_ton INT DEFAULT 0,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE hinh_anh_vay_cuoi (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    vay_id BIGINT NOT NULL,
    url VARCHAR(1024) NOT NULL COMMENT 'URL hoặc đường dẫn tới file ảnh',
    alt_text VARCHAR(255) NULL COMMENT 'Văn bản thay thế cho ảnh (tốt cho SEO)',
    is_primary TINYINT(1) DEFAULT 0 COMMENT '1 = là ảnh đại diện, 0 = là ảnh phụ',
    sort_order INT DEFAULT 0 COMMENT 'Thứ tự hiển thị ảnh',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Bảng đơn hàng (đã cập nhật cho thanh toán QR)
CREATE TABLE don_hang (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   ma_don_hang VARCHAR(50) UNIQUE COMMENT 'Mã đơn hàng duy nhất',
   nguoi_dung_id BIGINT NULL,
   ho_ten VARCHAR(255) NOT NULL COMMENT 'Họ tên người nhận',
   so_dien_thoai VARCHAR(30) NOT NULL COMMENT 'Số điện thoại người nhận',
   dia_chi TEXT NOT NULL COMMENT 'Địa chỉ nhận váy',
   ghi_chu TEXT NULL COMMENT 'Ghi chú đơn hàng',
   tong_tien DECIMAL(14,2) NOT NULL,
   trang_thai ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
   phuong_thuc_thanh_toan VARCHAR(50) DEFAULT 'qr_code' COMMENT 'Phương thức thanh toán',
   trang_thai_thanh_toan ENUM('pending','paid','failed','expired') DEFAULT 'pending' COMMENT 'Trạng thái thanh toán',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
   INDEX idx_ma_don_hang (ma_don_hang),
   INDEX idx_trang_thai (trang_thai),
   INDEX idx_trang_thai_thanh_toan (trang_thai_thanh_toan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Đơn hàng thuê váy cưới';

-- Bảng hóa đơn
CREATE TABLE hoa_don (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   don_hang_id BIGINT UNIQUE NOT NULL,
   nguoi_dung_id BIGINT NULL,
   ma_hoa_don VARCHAR(100) UNIQUE,
   tong_thanh_toan DECIMAL(14,2) NOT NULL,
   status ENUM('unpaid','paid','partially_paid','cancelled') DEFAULT 'unpaid',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng khuyến mãi (đã thành công từ trước)
CREATE TABLE khuyen_mai (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   code VARCHAR(80) UNIQUE,
   title VARCHAR(255),
   description TEXT,
   type ENUM('percent','fixed') DEFAULT 'percent',
   value DECIMAL(10,2) NOT NULL,
   min_order_amount DECIMAL(12,2) DEFAULT 0.00,
   start_at DATETIME,
   end_at DATETIME,
   usage_limit INT DEFAULT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng thống kê (đã thành công từ trước)
CREATE TABLE thong_ke_hang_ngay (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   stat_date DATE NOT NULL UNIQUE,
   total_orders INT DEFAULT 0,
   total_revenue DECIMAL(14,2) DEFAULT 0.00,
   total_visitors INT DEFAULT 0,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ===================================================================
-- PHẦN 2: CÁC BẢNG PHỤ THUỘC (BẢNG CON) - ĐÃ SỬA LỖI
-- ===================================================================

CREATE TABLE chi_tiet_hoa_don (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   hoa_don_id BIGINT NOT NULL,
   vay_id BIGINT NULL,
   description TEXT,
   amount DECIMAL(12,2) NOT NULL,
   quantity INT DEFAULT 1,
   FOREIGN KEY (hoa_don_id) REFERENCES hoa_don(id) ON DELETE CASCADE,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE thanh_toan (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   hoa_don_id BIGINT NULL,
   don_hang_id BIGINT NULL,
   payment_gateway VARCHAR(100) NULL,
   transaction_id VARCHAR(255) NULL,
   amount DECIMAL(14,2) NOT NULL,
   status ENUM('initiated','success','failed','refunded') DEFAULT 'initiated',
   paid_at TIMESTAMP NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (hoa_don_id) REFERENCES hoa_don(id) ON DELETE SET NULL,
   FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE SET NULL,
   INDEX idx_tx (transaction_id) -- SỬA LỖI: Bỏ ngoặc đơn ở tên index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE khuyen_mai_vay (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   khuyen_mai_id BIGINT NOT NULL,
   vay_id BIGINT NOT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (khuyen_mai_id) REFERENCES khuyen_mai(id) ON DELETE CASCADE,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE CASCADE,
   UNIQUE KEY(khuyen_mai_id, vay_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE dat_lich_thu_vay (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   user_id BIGINT NULL,
   name VARCHAR(200),
   phone VARCHAR(30),
   email VARCHAR(150) NULL,
   vay_id BIGINT NULL,
   scheduled_date DATE NOT NULL,
   scheduled_time TIME NULL,
   number_of_persons INT DEFAULT 1,
   status ENUM('pending','confirmed','attended','cancelled') DEFAULT 'pending',
   note TEXT,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE SET NULL,
   INDEX idx_date_status (scheduled_date, status) -- SỬA LỖI: Bỏ ngoặc đơn ở tên index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE lien_he (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   user_id BIGINT NULL,
   name VARCHAR(200),
   email VARCHAR(150),
   phone VARCHAR(30),
   subject VARCHAR(255),
   message TEXT,
   image_path VARCHAR(300) NULL,
   status ENUM('new','replied','closed') DEFAULT 'new',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE lich_su_chatbot (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   user_id BIGINT NULL,
   session_id VARCHAR(255),
   message_from ENUM('user','bot'),
   message TEXT,
   metadata JSON NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
   INDEX idx_session (session_id) -- SỬA LỖI: Bỏ ngoặc đơn ở tên index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE du_lieu_tim_kiem (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   user_id BIGINT NULL,
   keyword VARCHAR(255) NOT NULL,
   results_count INT DEFAULT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
   INDEX idx_keyword (keyword) -- SỬA LỖI: Bỏ ngoặc đơn ở tên index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tin_tuc_cuoi_hoi (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   admin_id INT NULL,
   title VARCHAR(300) NOT NULL,
   slug VARCHAR(300) NOT NULL UNIQUE,
   summary TEXT,
   content LONGTEXT,
   cover_image VARCHAR(1024),
   published_at DATETIME NULL,
   status ENUM('draft','published','archived') DEFAULT 'draft',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL,
   INDEX idx_status_pub (status, published_at) -- SỬA LỖI: Bỏ ngoặc đơn ở tên index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE quang_cao (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   admin_id INT NULL,
   title VARCHAR(255),
   image_url VARCHAR(1024),
   link_url VARCHAR(1024),
   start_at DATETIME,
   end_at DATETIME,
   active TINYINT(1) DEFAULT 1,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL,
   INDEX idx_active_time (active, start_at, end_at) -- SỬA LỖI: Bỏ ngoặc đơn ở tên index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm người dùng
INSERT INTO nguoi_dung (id, ho_ten, email, mat_khau, so_dien_thoai, dia_chi) VALUES
(1, 'Nguyễn Thị An', 'an.nguyen@example.com', 'hashed_password_123', '0901234567', '123 Đường ABC, Quận 1, TP.HCM'),
(2, 'Trần Văn Bình', 'binh.tran@example.com', 'hashed_password_456', '0912345678', '456 Đường XYZ, Quận 3, TP.HCM');

-- Thêm admin
INSERT INTO admin (id, username, password, full_name) VALUES
(1, 'admin_manager', 'super_secret_password', 'Quản Trị Viên');

-- Thêm váy cưới
INSERT INTO vay_cuoi (id, ma_vay, ten_vay, mo_ta, gia_thue, so_luong_ton) VALUES
(1, 'VC001', 'Váy Công Chúa Bồng Bềnh', 'Váy cưới lộng lẫy với thiết kế công chúa, đính đá Swarovski.', 5000000.00, 5),
(2, 'VC002', 'Váy Đuôi Cá Quyến Rũ', 'Thiết kế đuôi cá tôn dáng, chất liệu ren cao cấp.', 4500000.00, 3),
(3, 'VC003', 'Váy Chữ A Tối Giản', 'Váy cưới phong cách minimalist, thanh lịch và sang trọng.', 3000000.00, 10);

-- Thêm đơn hàng mẫu cho người dùng 1 (với cấu trúc mới)
INSERT INTO don_hang (id, ma_don_hang, nguoi_dung_id, ho_ten, so_dien_thoai, dia_chi, ghi_chu, tong_tien, trang_thai, phuong_thuc_thanh_toan, trang_thai_thanh_toan) VALUES
(1, 'DH20231120001', 1, 'Nguyễn Thị An', '0901234567', '123 Đường ABC, Quận 1, TP.HCM', 'Giao hàng buổi sáng', 5000000.00, 'completed', 'qr_code', 'paid');

-- Thêm hóa đơn cho đơn hàng 1
INSERT INTO hoa_don (id, don_hang_id, nguoi_dung_id, ma_hoa_don, tong_thanh_toan, status) VALUES
(1, 1, 1, 'HD20230001', 5000000.00, 'paid');

-- Thêm chi tiết cho hóa đơn 1
INSERT INTO chi_tiet_hoa_don (hoa_don_id, vay_id, description, amount, quantity) VALUES
(1, 1, 'Thuê Váy Công Chúa Bồng Bềnh', 5000000.00, 1);

-- Thêm thanh toán cho hóa đơn 1
INSERT INTO thanh_toan (hoa_don_id, don_hang_id, payment_gateway, transaction_id, amount, status, paid_at) VALUES
(1, 1, 'VNPAY', 'VNP123456789XYZ', 5000000.00, 'success', NOW());

-- Thêm lịch hẹn thử váy cho người dùng 2
INSERT INTO dat_lich_thu_vay (user_id, name, phone, email, vay_id, scheduled_date, scheduled_time, status) VALUES
(2, 'Trần Văn Bình', '0912345678', 'binh.tran@example.com', 2, '2023-12-25', '14:30:00', 'confirmed');

-- Thêm liên hệ
INSERT INTO lien_he (name, email, phone, subject, message) VALUES
('Lê Thị Cúc', 'cuc.le@example.com', '0987654321', 'Hỏi về giá thuê váy', 'Xin chào, tôi muốn hỏi giá thuê mẫu váy VC003. Cảm ơn.');
INSERT INTO hinh_anh_vay_cuoi (vay_id, url, alt_text, is_primary, sort_order) VALUES
-- Ảnh cho Váy Công Chúa (id = 1)
(1, '/images/dresses/vc001-main.jpg', 'Váy công chúa bồng bềnh nhìn từ phía trước', 1, 0), -- Ảnh đại diện
(1, '/images/dresses/vc001-back.jpg', 'Váy công chúa bồng bềnh nhìn từ phía sau', 0, 1),
(1, '/images/dresses/vc001-detail.jpg', 'Chi tiết đá đính trên thân váy công chúa', 0, 2),

-- Ảnh cho Váy Đuôi Cá (id = 2)
(2, '/images/dresses/vc002-front.jpg', 'Váy đuôi cá quyến rũ dáng trước', 1, 0), -- Ảnh đại diện
(2, '/images/dresses/vc002-side.jpg', 'Váy đuôi cá quyến rũ nhìn từ bên cạnh', 0, 1);
-- Thêm tin tức
INSERT INTO tin_tuc_cuoi_hoi (admin_id, title, slug, summary, content, status, published_at) VALUES
(1, 'Xu Hướng Váy Cưới 2024', 'xu-huong-vay-cuoi-2024', 'Khám phá những xu hướng váy cưới hot nhất năm 2024.', 'Nội dung chi tiết về các xu hướng...', 'published', '2023-11-20 10:00:00');
-- 1. Xem tất cả người dùng
SELECT * FROM nguoi_dung;

-- 2. Xem tất cả quản trị viên
SELECT * FROM admin;

-- 3. Xem tất cả các mẫu váy cưới
SELECT * FROM vay_cuoi;

-- 4. Xem tất cả hình ảnh của váy cưới
SELECT * FROM hinh_anh_vay_cuoi;

-- 5. Xem tất cả đơn hàng
SELECT * FROM don_hang;

-- 6. Xem tất cả hóa đơn
SELECT * FROM hoa_don;

-- 7. Xem tất cả chi tiết hóa đơn
SELECT * FROM chi_tiet_hoa_don;

-- 8. Xem tất cả các giao dịch thanh toán
SELECT * FROM thanh_toan;

-- 9. Xem các lịch hẹn thử váy
SELECT * FROM dat_lich_thu_vay;

-- 10. Xem các tin nhắn liên hệ
SELECT * FROM lien_he;

-- 11. Xem các bài đăng tin tức
SELECT * FROM tin_tuc_cuoi_hoi;

-- 12. Xem các mã khuyến mãi
SELECT * FROM khuyen_mai;

-- ===================================================================
-- BẢNG GIỎ HÀNG (CART)
-- ===================================================================

-- Bảng giỏ hàng (cho thuê váy cưới)
CREATE TABLE gio_hang (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   nguoi_dung_id BIGINT NOT NULL,
   vay_id BIGINT NOT NULL,
   so_luong INT DEFAULT 1 COMMENT 'Số lượng váy thuê (thường là 1)',
   ngay_bat_dau_thue DATE NOT NULL COMMENT 'Ngày bắt đầu thuê váy',
   ngay_tra_vay DATE NOT NULL COMMENT 'Ngày trả váy',
   so_ngay_thue INT DEFAULT 1 COMMENT 'Số ngày thuê (tự động tính)',
   ghi_chu TEXT NULL COMMENT 'Ghi chú đặc biệt (size, yêu cầu sửa...)',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE CASCADE,
   UNIQUE KEY unique_user_dress (nguoi_dung_id, vay_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Giỏ hàng cho thuê váy cưới';

-- Index để tăng tốc truy vấn
CREATE INDEX idx_user_cart ON gio_hang(nguoi_dung_id);
CREATE INDEX idx_created ON gio_hang(created_at);

-- 13. Xem giỏ hàng của người dùng (cho thuê váy)
SELECT 
    gh.id,
    gh.nguoi_dung_id,
    nd.ho_ten,
    nd.email,
    nd.so_dien_thoai,
    vc.ma_vay,
    vc.ten_vay,
    vc.gia_thue as gia_thue_moi_ngay,
    gh.so_luong,
    gh.ngay_bat_dau_thue,
    gh.ngay_tra_vay,
    gh.so_ngay_thue,
    (vc.gia_thue * gh.so_luong * gh.so_ngay_thue) as tong_tien_thue,
    gh.ghi_chu,
    gh.created_at
FROM gio_hang gh
JOIN nguoi_dung nd ON gh.nguoi_dung_id = nd.id
JOIN vay_cuoi vc ON gh.vay_id = vc.id
ORDER BY gh.created_at DESC;