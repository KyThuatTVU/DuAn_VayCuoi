# Hướng Dẫn Cập Nhật Chức Năng Voucher Khuyến Mãi

## Các thay đổi đã thực hiện:

### 1. Giao diện trang thanh toán (checkout.php)
- ✅ Thêm nút "Chọn Voucher Khuyến Mãi" với giao diện đẹp mắt
- ✅ Thêm modal hiển thị danh sách voucher có sẵn
- ✅ Hiển thị trạng thái voucher (đã dùng, hết lượt, không đủ điều kiện)
- ✅ Cho phép chọn voucher từ danh sách hoặc nhập mã thủ công
- ✅ Hiển thị số lượt sử dụng còn lại của mỗi voucher

### 2. API mới
- ✅ `api/get-available-vouchers.php` - Lấy danh sách voucher khả dụng cho user
- ✅ `api/create-coupon-usage-table.php` - Tạo bảng lưu lịch sử sử dụng voucher

### 3. Logic xử lý
- ✅ Kiểm tra user đã sử dụng voucher chưa (mỗi user chỉ dùng 1 lần/voucher)
- ✅ Trừ lượt sử dụng khi áp dụng voucher thành công
- ✅ Lưu lịch sử sử dụng vào bảng `user_coupon_usage`
- ✅ Hiển thị voucher theo thứ tự ưu tiên (chưa dùng > đủ điều kiện > giá trị cao)

## Cách cài đặt:

### Bước 1: Tạo bảng user_coupon_usage
Truy cập URL sau trong trình duyệt (chỉ cần chạy 1 lần):
```
http://localhost/DuAn_CuaHangVayCuoiGradenHome/api/create-coupon-usage-table.php
```

Hoặc chạy SQL sau trong phpMyAdmin:
```sql
CREATE TABLE IF NOT EXISTS user_coupon_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    coupon_code VARCHAR(50) NOT NULL,
    order_id INT NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES don_hang(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_coupon (user_id, coupon_code),
    INDEX idx_coupon_code (coupon_code),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Bước 2: Kiểm tra bảng khuyen_mai
Đảm bảo bảng `khuyen_mai` có các cột sau:
- `id` - INT PRIMARY KEY
- `code` - VARCHAR(50) - Mã voucher
- `title` - VARCHAR(255) - Tiêu đề
- `description` - TEXT - Mô tả
- `type` - ENUM('percent', 'fixed') - Loại giảm giá
- `value` - DECIMAL(10,2) - Giá trị giảm
- `min_order_amount` - DECIMAL(10,2) - Đơn hàng tối thiểu
- `usage_limit` - INT NULL - Giới hạn số lần sử dụng (NULL = không giới hạn)
- `start_at` - DATETIME - Thời gian bắt đầu
- `end_at` - DATETIME - Thời gian kết thúc
- `created_at` - TIMESTAMP

### Bước 3: Test chức năng

1. **Tạo voucher mới** trong trang Admin > Khuyến Mãi:
   - Mã: SUMMER2024
   - Tiêu đề: Giảm giá mùa hè
   - Loại: Phần trăm
   - Giá trị: 10
   - Đơn tối thiểu: 500000
   - Giới hạn: 100 lượt
   - Thời gian: Từ hôm nay đến 1 tháng sau

2. **Test trên trang thanh toán**:
   - Thêm sản phẩm vào giỏ hàng
   - Vào trang thanh toán
   - Click "Chọn Voucher Khuyến Mãi"
   - Chọn voucher từ danh sách
   - Kiểm tra giảm giá được áp dụng
   - Hoàn tất đơn hàng

3. **Kiểm tra sau khi đặt hàng**:
   - Voucher đã được đánh dấu "Đã sử dụng"
   - Số lượt sử dụng giảm đi 1
   - User không thể dùng lại voucher đó

## Tính năng chính:

### 1. Modal chọn voucher
- Hiển thị tất cả voucher đang hoạt động
- Phân loại theo trạng thái:
  - ✅ Có thể sử dụng (màu xanh)
  - ⚠️ Không đủ điều kiện (màu vàng)
  - ❌ Đã sử dụng/Hết lượt (màu xám)

### 2. Kiểm tra điều kiện
- Đơn hàng phải đạt giá trị tối thiểu
- User chưa sử dụng voucher đó
- Voucher còn lượt sử dụng (nếu có giới hạn)
- Voucher trong thời gian hiệu lực

### 3. Trừ lượt tự động
- Khi đơn hàng được tạo thành công
- Giảm `usage_limit` đi 1 (nếu có)
- Lưu vào bảng `user_coupon_usage`

## Lưu ý:

1. **Mỗi user chỉ dùng 1 lần/voucher**: Được kiểm soát bởi UNIQUE KEY trong bảng `user_coupon_usage`

2. **Voucher không giới hạn**: Nếu `usage_limit` = NULL, voucher có thể dùng không giới hạn (nhưng mỗi user vẫn chỉ 1 lần)

3. **Rollback khi lỗi**: Nếu thanh toán thất bại, transaction sẽ rollback và không trừ lượt

4. **Bảo mật**: API kiểm tra đăng nhập và validate dữ liệu trước khi xử lý

## Troubleshooting:

### Lỗi: "Bảng user_coupon_usage không tồn tại"
- Chạy lại file `api/create-coupon-usage-table.php`

### Lỗi: "Duplicate entry for key 'unique_user_coupon'"
- User đã sử dụng voucher này rồi (đây là hành vi mong muốn)

### Voucher không hiển thị trong modal
- Kiểm tra voucher có trong thời gian hiệu lực không
- Kiểm tra `start_at <= NOW() AND end_at >= NOW()`

### Không trừ lượt sử dụng
- Kiểm tra transaction có commit thành công không
- Xem log trong `api/create-order.php`
