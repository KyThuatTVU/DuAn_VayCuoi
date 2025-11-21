-- Cập nhật bảng admin để thêm cột email và các cột khác
-- Chạy file SQL này trong phpMyAdmin hoặc MySQL client

USE cua_hang_vay_cuoi_db;

-- Kiểm tra và thêm cột email nếu chưa có
ALTER TABLE admin 
ADD COLUMN IF NOT EXISTS email VARCHAR(150) NOT NULL UNIQUE AFTER username;

-- Kiểm tra và thêm cột role nếu chưa có
ALTER TABLE admin 
ADD COLUMN IF NOT EXISTS role ENUM('super_admin','admin','moderator') DEFAULT 'admin' AFTER full_name;

-- Kiểm tra và thêm cột status nếu chưa có
ALTER TABLE admin 
ADD COLUMN IF NOT EXISTS status ENUM('active','inactive') DEFAULT 'active' AFTER role;

-- Kiểm tra và thêm cột last_login nếu chưa có
ALTER TABLE admin 
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL AFTER status;

-- Cập nhật email cho admin hiện có (nếu có)
UPDATE admin SET email = CONCAT(username, '@admin.local') WHERE email IS NULL OR email = '';

-- Hiển thị cấu trúc bảng sau khi cập nhật
DESCRIBE admin;

-- Hiển thị dữ liệu
SELECT * FROM admin;
