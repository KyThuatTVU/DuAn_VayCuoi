-- Thêm các cột để lưu trạng thái xác thực email trong bảng lien_he
ALTER TABLE lien_he 
ADD COLUMN email_is_valid TINYINT(1) DEFAULT 1 COMMENT 'Email có đúng format không',
ADD COLUMN email_is_real TINYINT(1) DEFAULT 1 COMMENT 'Email có thật không (kiểm tra DNS, MX record)',
ADD COLUMN email_validation_reason VARCHAR(255) DEFAULT NULL COMMENT 'Lý do kết quả xác thực',
ADD COLUMN email_validation_details TEXT DEFAULT NULL COMMENT 'Chi tiết xác thực (JSON)';
