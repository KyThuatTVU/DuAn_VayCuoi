# 🎫 Hệ Thống Voucher Khuyến Mãi

## 🚀 Cài Đặt Nhanh

### Bước 1: Tạo bảng database
Truy cập: `http://localhost/DuAn_CuaHangVayCuoiGradenHome/api/create-coupon-usage-table.php`

### Bước 2: Test hệ thống
Truy cập: `http://localhost/DuAn_CuaHangVayCuoiGradenHome/test-voucher-system.php`

### Bước 3: Tạo voucher mẫu
1. Đăng nhập admin
2. Vào **Admin > Khuyến Mãi**
3. Thêm voucher mới:
   - Mã: `WELCOME10`
   - Giảm: 10%
   - Đơn tối thiểu: 500,000 VNĐ
   - Giới hạn: 100 lượt

### Bước 4: Test trên trang thanh toán
1. Đăng nhập user
2. Thêm sản phẩm vào giỏ
3. Vào trang thanh toán
4. Click "Chọn Voucher Khuyến Mãi"
5. Chọn voucher và hoàn tất đơn hàng

## ✨ Tính Năng

### Cho Khách Hàng:
- ✅ Xem danh sách voucher có sẵn
- ✅ Lọc voucher theo điều kiện (đủ điều kiện, đã dùng, hết lượt)
- ✅ Áp dụng voucher tự động khi chọn
- ✅ Mỗi voucher chỉ dùng 1 lần/user
- ✅ Hiển thị số lượt còn lại

### Cho Admin:
- ✅ Tạo/sửa/xóa voucher
- ✅ Đặt giới hạn số lần sử dụng
- ✅ Xem thống kê sử dụng
- ✅ Theo dõi lịch sử áp dụng

## 📊 Cấu Trúc Database

### Bảng `khuyen_mai`
```sql
- id: INT PRIMARY KEY
- code: VARCHAR(50) - Mã voucher
- title: VARCHAR(255) - Tiêu đề
- description: TEXT - Mô tả
- type: ENUM('percent', 'fixed') - Loại giảm giá
- value: DECIMAL(10,2) - Giá trị giảm
- min_order_amount: DECIMAL(10,2) - Đơn tối thiểu
- usage_limit: INT NULL - Giới hạn lượt dùng
- start_at: DATETIME - Bắt đầu
- end_at: DATETIME - Kết thúc
```

### Bảng `user_coupon_usage` (Mới)
```sql
- id: INT PRIMARY KEY
- user_id: INT - ID người dùng
- coupon_code: VARCHAR(50) - Mã voucher
- order_id: INT - ID đơn hàng
- discount_amount: DECIMAL(10,2) - Số tiền giảm
- used_at: TIMESTAMP - Thời gian sử dụng
- UNIQUE(user_id, coupon_code) - Mỗi user chỉ dùng 1 lần
```

## 🔧 API Endpoints

### `GET api/get-available-vouchers.php`
Lấy danh sách voucher khả dụng cho user hiện tại

**Response:**
```json
{
  "success": true,
  "vouchers": [
    {
      "code": "WELCOME10",
      "title": "Giảm 10% cho đơn đầu",
      "type": "percent",
      "value": 10,
      "min_order_amount": 500000,
      "usage_limit": 100,
      "used_count": 5,
      "user_used": false
    }
  ]
}
```

### `POST api/apply-coupon.php`
Áp dụng mã voucher

**Request:**
```json
{
  "coupon_code": "WELCOME10"
}
```

**Response:**
```json
{
  "success": true,
  "discount_amount": 50000,
  "total_amount": 525000,
  "message": "Áp dụng mã khuyến mãi thành công!"
}
```

## 🎨 Giao Diện

### Modal Chọn Voucher
- Hiển thị đẹp mắt với gradient màu
- Animation mượt mà khi mở/đóng
- Phân loại voucher theo trạng thái
- Hiển thị đầy đủ thông tin (HSD, lượt còn lại, điều kiện)

### Trang Thanh Toán
- Nút "Chọn Voucher Khuyến Mãi" nổi bật
- Hoặc nhập mã thủ công
- Hiển thị giảm giá trong tổng đơn hàng
- Disable sau khi áp dụng thành công

## 🔒 Bảo Mật

- ✅ Kiểm tra đăng nhập trước khi xử lý
- ✅ Validate dữ liệu đầu vào
- ✅ Sử dụng prepared statements
- ✅ Transaction để đảm bảo tính toàn vẹn
- ✅ UNIQUE constraint ngăn dùng lại voucher

## 📝 Lưu Ý

1. **Mỗi user chỉ dùng 1 lần/voucher**: Được kiểm soát bởi UNIQUE KEY
2. **Trừ lượt tự động**: Khi đơn hàng được tạo thành công
3. **Rollback khi lỗi**: Transaction đảm bảo không mất dữ liệu
4. **Voucher không giới hạn**: Đặt `usage_limit = NULL`

## 🐛 Troubleshooting

### Lỗi: "Bảng user_coupon_usage không tồn tại"
→ Chạy `api/create-coupon-usage-table.php`

### Voucher không hiển thị
→ Kiểm tra thời gian hiệu lực (start_at, end_at)

### Không trừ lượt
→ Kiểm tra transaction có commit thành công

## 📞 Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
1. File `test-voucher-system.php` để debug
2. Console log trong trình duyệt
3. Error log của PHP/MySQL

---

**Phát triển bởi:** Kiro AI Assistant
**Ngày cập nhật:** 19/12/2024
