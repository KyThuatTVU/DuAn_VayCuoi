-- =====================================================
-- CẬP NHẬT BẢNG VÁY CƯỚI - THÊM CỘT PHONG CÁCH VÀ SIZE
-- =====================================================
-- Chạy file này sau khi đã có database cua_hang_vay_cuoi_db

USE cua_hang_vay_cuoi_db;

-- =====================================================
-- BƯỚC 1: THÊM CỘT VÀO BẢNG VAY_CUOI
-- =====================================================

-- Thêm cột phong_cach (nếu chưa có, chạy lệnh này - nếu báo lỗi "Duplicate column" thì bỏ qua)
ALTER TABLE vay_cuoi 
ADD COLUMN phong_cach ENUM('công chúa', 'đuôi cá', 'chữ a', 'hiện đại', 'vintage', 'minimalist') 
DEFAULT NULL 
COMMENT 'Phong cách váy cưới' 
AFTER mo_ta;

-- Thêm cột mau_sac
ALTER TABLE vay_cuoi 
ADD COLUMN mau_sac VARCHAR(50) 
DEFAULT 'Trắng' 
COMMENT 'Màu sắc váy' 
AFTER phong_cach;

-- =====================================================
-- BƯỚC 2: TẠO BẢNG SIZE VÁY CƯỚI
-- =====================================================

CREATE TABLE IF NOT EXISTS vay_cuoi_size (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    vay_id BIGINT NOT NULL,
    size ENUM('XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL') NOT NULL,
    so_luong INT DEFAULT 1 COMMENT 'Số lượng váy theo size này',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vay_size (vay_id, size),
    FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng quản lý size của từng váy cưới';

-- =====================================================
-- BƯỚC 3: CẬP NHẬT DỮ LIỆU MẪU
-- =====================================================

-- Tắt Safe Update Mode tạm thời
SET SQL_SAFE_UPDATES = 0;

-- Cập nhật phong cách cho các váy hiện có dựa trên tên
UPDATE vay_cuoi SET phong_cach = 'công chúa' WHERE id = 1;
UPDATE vay_cuoi SET phong_cach = 'đuôi cá' WHERE id = 2;
UPDATE vay_cuoi SET phong_cach = 'chữ a' WHERE id = 3;

-- Hoặc cập nhật tự động dựa trên tên váy
UPDATE vay_cuoi SET phong_cach = 'công chúa' 
WHERE (LOWER(ten_vay) LIKE '%công chúa%' OR LOWER(ten_vay) LIKE '%princess%')
AND phong_cach IS NULL;

UPDATE vay_cuoi SET phong_cach = 'đuôi cá' 
WHERE (LOWER(ten_vay) LIKE '%đuôi cá%' OR LOWER(ten_vay) LIKE '%mermaid%')
AND phong_cach IS NULL;

UPDATE vay_cuoi SET phong_cach = 'chữ a' 
WHERE (LOWER(ten_vay) LIKE '%chữ a%' OR LOWER(ten_vay) LIKE '%a-line%' OR LOWER(ten_vay) LIKE '%tối giản%')
AND phong_cach IS NULL;

UPDATE vay_cuoi SET phong_cach = 'hiện đại' 
WHERE (LOWER(ten_vay) LIKE '%hiện đại%' OR LOWER(ten_vay) LIKE '%modern%')
AND phong_cach IS NULL;

-- Bật lại Safe Update Mode
SET SQL_SAFE_UPDATES = 1;

-- Thêm size mẫu cho các váy hiện có
INSERT IGNORE INTO vay_cuoi_size (vay_id, size, so_luong) VALUES
-- Váy 1: Công Chúa Bồng Bềnh
(1, 'S', 1),
(1, 'M', 2),
(1, 'L', 2),
-- Váy 2: Đuôi Cá Quyến Rũ
(2, 'S', 1),
(2, 'M', 1),
(2, 'L', 1),
-- Váy 3: Chữ A Tối Giản
(3, 'XS', 2),
(3, 'S', 3),
(3, 'M', 3),
(3, 'L', 2);

-- =====================================================
-- BƯỚC 4: KIỂM TRA KẾT QUẢ
-- =====================================================

-- Xem váy cưới với phong cách
SELECT id, ma_vay, ten_vay, phong_cach, mau_sac, gia_thue, so_luong_ton FROM vay_cuoi;

-- Xem size của từng váy
SELECT vc.ma_vay, vc.ten_vay, vcs.size, vcs.so_luong
FROM vay_cuoi vc
LEFT JOIN vay_cuoi_size vcs ON vc.id = vcs.vay_id
ORDER BY vc.id, FIELD(vcs.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL');

-- Xem váy với tất cả size (gộp thành 1 dòng)
SELECT vc.id, vc.ma_vay, vc.ten_vay, vc.phong_cach, vc.gia_thue,
       GROUP_CONCAT(vcs.size ORDER BY FIELD(vcs.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL') SEPARATOR ', ') as sizes
FROM vay_cuoi vc
LEFT JOIN vay_cuoi_size vcs ON vc.id = vcs.vay_id
GROUP BY vc.id;

-- =====================================================
-- HƯỚNG DẪN
-- =====================================================
-- 1. Mở MySQL Workbench hoặc phpMyAdmin
-- 2. Chạy từng phần một (BƯỚC 1, 2, 3, 4)
-- 3. Nếu BƯỚC 1 báo lỗi "Duplicate column name" thì bỏ qua (cột đã tồn tại)
-- 4. Sau khi chạy xong, bộ lọc trên trang products.php sẽ hoạt động
