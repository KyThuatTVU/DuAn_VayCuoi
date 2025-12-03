-- ===================================================================
-- BẢNG LƯU MÃ OTP XÁC NHẬN EMAIL
-- ===================================================================

CREATE TABLE IF NOT EXISTS otp_verification (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    ho_ten VARCHAR(255) NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    so_dien_thoai VARCHAR(30) NULL,
    dia_chi TEXT NULL,
    avt VARCHAR(500) NULL,
    expires_at DATETIME NOT NULL COMMENT 'Thời gian hết hạn OTP (5 phút)',
    is_verified TINYINT(1) DEFAULT 0 COMMENT '0 = chưa xác nhận, 1 = đã xác nhận',
    attempts INT DEFAULT 0 COMMENT 'Số lần nhập sai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_otp (otp_code),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lưu mã OTP xác nhận đăng ký';

-- Xóa các OTP hết hạn (có thể chạy bằng cron job)
-- DELETE FROM otp_verification WHERE expires_at < NOW();
