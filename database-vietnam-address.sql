-- ===================================================================
-- CẬP NHẬT DATABASE CHO ĐỊA CHỈ VIỆT NAM
-- ===================================================================

-- Thêm các cột địa chỉ chi tiết vào bảng nguoi_dung
ALTER TABLE nguoi_dung 
ADD COLUMN tinh_thanh VARCHAR(100) NULL COMMENT 'Mã tỉnh/thành phố' AFTER dia_chi,
ADD COLUMN quan_huyen VARCHAR(100) NULL COMMENT 'Mã quận/huyện' AFTER tinh_thanh,
ADD COLUMN phuong_xa VARCHAR(100) NULL COMMENT 'Mã phường/xã' AFTER quan_huyen,
ADD COLUMN dia_chi_cu_the VARCHAR(500) NULL COMMENT 'Địa chỉ cụ thể (số nhà, đường...)' AFTER phuong_xa;

-- Thêm các cột địa chỉ chi tiết vào bảng don_hang
ALTER TABLE don_hang 
ADD COLUMN tinh_thanh VARCHAR(100) NULL COMMENT 'Mã tỉnh/thành phố' AFTER dia_chi,
ADD COLUMN quan_huyen VARCHAR(100) NULL COMMENT 'Mã quận/huyện' AFTER tinh_thanh,
ADD COLUMN phuong_xa VARCHAR(100) NULL COMMENT 'Mã phường/xã' AFTER quan_huyen,
ADD COLUMN dia_chi_cu_the VARCHAR(500) NULL COMMENT 'Địa chỉ cụ thể (số nhà, đường...)' AFTER phuong_xa;

-- Index để tăng tốc truy vấn
CREATE INDEX idx_nguoi_dung_tinh ON nguoi_dung(tinh_thanh);
CREATE INDEX idx_don_hang_tinh ON don_hang(tinh_thanh);
