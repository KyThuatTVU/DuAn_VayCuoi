-- Thêm cột size vào bảng vay_cuoi
-- Cột size sẽ lưu thông tin kích cỡ váy cưới (S, M, L, XL, hoặc số đo cụ thể)

ALTER TABLE vay_cuoi 
ADD COLUMN size VARCHAR(100) NULL COMMENT 'Kích cỡ váy: S, M, L, XL hoặc số đo cụ thể' 
AFTER so_luong_ton;
