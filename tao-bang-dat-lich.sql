-- Tạo bảng đặt lịch thử váy
CREATE TABLE IF NOT EXISTS dat_lich_thu_vay (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   user_id BIGINT NULL COMMENT 'ID người dùng (nếu đã đăng nhập)',
   name VARCHAR(200) NOT NULL COMMENT 'Họ tên người đặt lịch',
   phone VARCHAR(30) NOT NULL COMMENT 'Số điện thoại liên hệ',
   email VARCHAR(150) NULL COMMENT 'Email liên hệ',
   vay_id BIGINT NULL COMMENT 'ID váy muốn thử',
   scheduled_date DATE NOT NULL COMMENT 'Ngày hẹn thử váy',
   scheduled_time TIME NULL COMMENT 'Giờ hẹn thử váy',
   number_of_persons INT DEFAULT 1 COMMENT 'Số người đi cùng',
   status ENUM('pending','confirmed','attended','cancelled') DEFAULT 'pending' COMMENT 'Trạng thái lịch hẹn',
   note TEXT NULL COMMENT 'Ghi chú thêm',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
   FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
   FOREIGN KEY (vay_id) REFERENCES vay_cuoi(id) ON DELETE SET NULL,
   INDEX idx_date_status (scheduled_date, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng đặt lịch thử váy';

-- Thêm dữ liệu mẫu
INSERT INTO dat_lich_thu_vay (user_id, name, phone, email, vay_id, scheduled_date, scheduled_time, number_of_persons, status, note) VALUES
(1, 'Nguyễn Thị An', '0901234567', 'an.nguyen@example.com', 1, '2025-11-25', '10:00:00', 2, 'confirmed', 'Muốn thử thêm size khác'),
(2, 'Trần Văn Bình', '0912345678', 'binh.tran@example.com', 2, '2025-11-26', '14:30:00', 1, 'pending', NULL),
(NULL, 'Lê Thị Cúc', '0987654321', 'cuc.le@example.com', 3, '2025-11-27', '09:00:00', 3, 'pending', 'Đi cùng gia đình');
