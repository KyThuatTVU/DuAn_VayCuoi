-- ===================================================================
-- BẢNG CÀI ĐẶT HỆ THỐNG (SETTINGS)
-- ===================================================================

USE cua_hang_vay_cuoi_db;

-- Bảng cài đặt hệ thống
CREATE TABLE IF NOT EXISTS cai_dat (
   id INT AUTO_INCREMENT PRIMARY KEY,
   setting_key VARCHAR(100) NOT NULL UNIQUE COMMENT 'Khóa cài đặt',
   setting_value TEXT COMMENT 'Giá trị cài đặt',
   setting_group VARCHAR(50) DEFAULT 'general' COMMENT 'Nhóm cài đặt',
   setting_label VARCHAR(255) COMMENT 'Nhãn hiển thị',
   setting_type ENUM('text','textarea','email','phone','url','number') DEFAULT 'text' COMMENT 'Loại input',
   sort_order INT DEFAULT 0 COMMENT 'Thứ tự hiển thị',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   INDEX idx_group (setting_group),
   INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cài đặt hệ thống';

-- Thêm dữ liệu mặc định cho cài đặt
INSERT INTO cai_dat (setting_key, setting_value, setting_group, setting_label, setting_type, sort_order) VALUES
-- Thông tin liên hệ
('contact_address', '123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'contact', 'Địa chỉ', 'textarea', 1),
('contact_phone', '0901 234 567', 'contact', 'Số điện thoại', 'phone', 2),
('contact_email', 'contact@vaycuoi.com', 'contact', 'Email', 'email', 3),
('contact_hotline', '078.797.2075', 'contact', 'Hotline', 'phone', 4),

-- Giờ làm việc
('working_days', 'Thứ 2 - Chủ Nhật', 'working', 'Ngày làm việc', 'text', 1),
('working_hours', '8:00 - 20:00', 'working', 'Giờ làm việc', 'text', 2),

-- Mạng xã hội
('social_facebook', '#', 'social', 'Facebook', 'url', 1),
('social_instagram', '#', 'social', 'Instagram', 'url', 2),
('social_youtube', '#', 'social', 'YouTube', 'url', 3),
('social_zalo', 'https://zalo.me/0787972075', 'social', 'Zalo', 'url', 4),

-- Thông tin ngân hàng
('bank_name', 'Vietcombank', 'bank', 'Tên ngân hàng', 'text', 1),
('bank_account', '1234567890123', 'bank', 'Số tài khoản', 'text', 2),
('bank_holder', 'NGUYEN VAN A', 'bank', 'Chủ tài khoản', 'text', 3),
('bank_branch', 'TP. Hồ Chí Minh', 'bank', 'Chi nhánh', 'text', 4),

-- Thông tin chung
('site_name', 'Váy Cưới Thiên Thần', 'general', 'Tên website', 'text', 1),
('site_description', 'Địa chỉ uy tín cho thuê váy cưới cao cấp tại TP.HCM với hơn 10 năm kinh nghiệm.', 'general', 'Mô tả website', 'textarea', 2),
('site_copyright', '© 2024 Váy Cưới Thiên Thần. All rights reserved.', 'general', 'Bản quyền', 'text', 3)

ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    updated_at = CURRENT_TIMESTAMP;
