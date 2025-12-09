-- ===================================================================
-- CẬP NHẬT BẢNG NGUOI_DUNG ĐỂ HỖ TRỢ KHÓA TÀI KHOẢN KHI ĐĂNG NHẬP SAI 5 LẦN
-- Chạy script này để thêm các cột cần thiết
-- ===================================================================

-- Thêm cột đếm số lần đăng nhập thất bại
ALTER TABLE nguoi_dung 
ADD COLUMN IF NOT EXISTS login_attempts INT DEFAULT 0 COMMENT 'Số lần đăng nhập thất bại liên tiếp';

-- Thêm cột thời gian đăng nhập thất bại cuối cùng
ALTER TABLE nguoi_dung 
ADD COLUMN IF NOT EXISTS last_failed_login TIMESTAMP NULL COMMENT 'Thời gian đăng nhập thất bại cuối cùng';

-- Thêm cột thời gian khóa tài khoản
ALTER TABLE nguoi_dung 
ADD COLUMN IF NOT EXISTS locked_at TIMESTAMP NULL COMMENT 'Thời gian tài khoản bị khóa';

-- Thêm cột lý do khóa
ALTER TABLE nguoi_dung 
ADD COLUMN IF NOT EXISTS locked_reason VARCHAR(255) NULL COMMENT 'Lý do khóa tài khoản';

-- Thêm cột status nếu chưa có
ALTER TABLE nguoi_dung 
ADD COLUMN IF NOT EXISTS status ENUM('active', 'locked', 'disabled') DEFAULT 'active' COMMENT 'Trạng thái tài khoản';

-- ===================================================================
-- Tạo bảng log đăng nhập (tùy chọn - để theo dõi lịch sử)
-- ===================================================================
CREATE TABLE IF NOT EXISTS login_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT NULL,
    email VARCHAR(150) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    status ENUM('success', 'failed', 'locked') NOT NULL,
    failed_reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_nguoi_dung (nguoi_dung_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lịch sử đăng nhập';

-- ===================================================================
-- Tạo bảng thông báo admin (nếu chưa có)
-- ===================================================================
CREATE TABLE IF NOT EXISTS admin_notifications (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL COMMENT 'Loại thông báo: account_locked, new_order, etc.',
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    reference_id BIGINT NULL COMMENT 'ID tham chiếu (ví dụ: user_id)',
    reference_type VARCHAR(50) NULL COMMENT 'Loại tham chiếu (ví dụ: user)',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Thông báo cho admin';
