# Hướng dẫn Quản lý Thanh toán Admin

## 📋 Tổng quan

Hệ thống quản lý thanh toán cho phép admin:
- Xem tất cả giao dịch thanh toán
- Lọc theo trạng thái, cổng thanh toán
- Xác nhận/Cập nhật trạng thái thanh toán
- Xem chi tiết từng giao dịch
- Thống kê doanh thu

## 🔧 Các trang đã tạo

### 1. admin-payments.php
Trang quản lý chính cho thanh toán:

**Tính năng:**
- ✅ Hiển thị danh sách tất cả giao dịch
- ✅ Thống kê tổng quan (tổng giao dịch, thành công, đang xử lý, thất bại)
- ✅ Bộ lọc theo:
  - Trạng thái (initiated, success, failed, refunded)
  - Cổng thanh toán (MoMo, QR Code)
  - Tìm kiếm (mã giao dịch, đơn hàng, tên khách hàng)
- ✅ Cập nhật trạng thái trực tiếp từ dropdown
- ✅ Xem chi tiết giao dịch (modal popup)
- ✅ Phân trang
- ✅ Link đến chi tiết đơn hàng

**Truy cập:** `admin-payments.php`

### 2. api/get-payment-detail.php
API lấy chi tiết giao dịch thanh toán:

**Input:**
```
GET /api/get-payment-detail.php?id=123
```

**Output:**
```json
{
  "success": true,
  "payment": {
    "id": 123,
    "transaction_id": "MOMO_456_1234567890",
    "don_hang_id": 456,
    "ma_don_hang": "DH20231201001",
    "ho_ten": "Nguyễn Văn A",
    "payment_gateway": "momo",
    "amount": 5000000,
    "status": "success",
    "created_at": "2023-12-01 10:30:00",
    "paid_at": "2023-12-01 10:35:00"
  }
}
```

### 3. admin-confirm-payment.php
Trang xác nhận thanh toán thủ công (đã có sẵn, đã kiểm tra tương thích):

**Tính năng:**
- Hiển thị đơn hàng chờ thanh toán
- Xác nhận thanh toán thủ công
- Auto refresh mỗi 30 giây

## 📊 Cấu trúc Database

### Bảng `thanh_toan`
```sql
CREATE TABLE thanh_toan (
   id BIGINT AUTO_INCREMENT PRIMARY KEY,
   hoa_don_id BIGINT NULL,
   don_hang_id BIGINT NULL,
   payment_gateway VARCHAR(100) NULL,
   transaction_id VARCHAR(255) NULL,
   amount DECIMAL(14,2) NOT NULL,
   status ENUM('initiated','success','failed','refunded') DEFAULT 'initiated',
   paid_at TIMESTAMP NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (hoa_don_id) REFERENCES hoa_don(id) ON DELETE SET NULL,
   FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE SET NULL,
   INDEX idx_tx (transaction_id)
);
```

### Các trạng thái thanh toán

| Status | Ý nghĩa | Màu hiển thị |
|--------|---------|--------------|
| `initiated` | Đang xử lý | Vàng |
| `success` | Thành công | Xanh lá |
| `failed` | Thất bại | Đỏ |
| `refunded` | Hoàn tiền | Tím |

## 🔄 Luồng xử lý thanh toán

### 1. Thanh toán MoMo
```
Khách hàng checkout 
  → api/create-order.php (tạo đơn hàng)
  → api/momo-create-payment.php (tạo thanh toán MoMo)
  → Lưu vào bảng thanh_toan với status='initiated'
  → Chuyển đến MoMo
  → Khách thanh toán
  → MoMo gọi IPN (api/momo-ipn.php)
  → Cập nhật thanh_toan.status='success'
  → Cập nhật don_hang.trang_thai_thanh_toan='paid'
  → Chuyển về momo-return.php
```

### 2. Thanh toán QR Code
```
Khách hàng checkout
  → api/create-order.php (tạo đơn hàng)
  → Lưu vào bảng thanh_toan với status='initiated'
  → Hiển thị QR Code
  → Khách chuyển khoản
  → Admin xác nhận thủ công (admin-confirm-payment.php)
  → Cập nhật thanh_toan.status='success'
  → Cập nhật don_hang.trang_thai_thanh_toan='paid'
```

## 🎯 Cách sử dụng

### Xem danh sách thanh toán
1. Đăng nhập admin
2. Click menu "Thanh toán" hoặc truy cập `admin-payments.php`
3. Xem danh sách tất cả giao dịch

### Lọc giao dịch
1. Chọn trạng thái từ dropdown
2. Chọn cổng thanh toán
3. Nhập từ khóa tìm kiếm
4. Click "Lọc"

### Cập nhật trạng thái
1. Tìm giao dịch cần cập nhật
2. Click vào dropdown trạng thái
3. Chọn trạng thái mới
4. Hệ thống tự động cập nhật

**Lưu ý:** Khi cập nhật sang `success`, đơn hàng sẽ tự động được đánh dấu là đã thanh toán.

### Xem chi tiết giao dịch
1. Click icon mắt (👁️) ở cột "Thao tác"
2. Xem thông tin chi tiết trong popup

### Xác nhận thanh toán thủ công
1. Truy cập `admin-confirm-payment.php`
2. Kiểm tra tài khoản ngân hàng
3. Click "Xác nhận" nếu đã nhận tiền

## 📈 Thống kê

Trang admin-payments.php hiển thị:
- **Tổng giao dịch**: Tổng số giao dịch trong hệ thống
- **Thành công**: Số giao dịch thành công + tổng tiền
- **Đang xử lý**: Số giao dịch đang chờ
- **Thất bại**: Số giao dịch thất bại

## 🔐 Bảo mật

- ✅ Kiểm tra đăng nhập admin
- ✅ Validate input
- ✅ Prepared statements (SQL injection prevention)
- ✅ XSS protection với htmlspecialchars()

## 🐛 Xử lý lỗi

### Giao dịch bị treo (initiated quá lâu)
1. Kiểm tra log MoMo: `debug-momo-ipn.txt`
2. Kiểm tra IPN URL có public không
3. Xác nhận thủ công nếu cần

### Không nhận được IPN từ MoMo
1. Kiểm tra MOMO_IPN_URL trong .env
2. Sử dụng ngrok để public localhost
3. Kiểm tra log: `debug-momo-ipn.txt`

### Đơn hàng đã thanh toán nhưng status vẫn pending
1. Vào admin-payments.php
2. Tìm giao dịch
3. Cập nhật status thành "success"
4. Đơn hàng sẽ tự động cập nhật

## 📱 Responsive

Trang admin-payments.php responsive trên:
- ✅ Desktop
- ✅ Tablet
- ✅ Mobile

## 🔗 Liên kết

- [README MoMo](README_MOMO.md) - Hướng dẫn cấu hình MoMo
- [Test MoMo](test-momo.php) - Kiểm tra cấu hình MoMo
- [Admin Dashboard](admin-dashboard.php) - Trang chủ admin
- [Quản lý đơn hàng](admin-orders.php) - Quản lý đơn hàng

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra file log: `debug-momo-ipn.txt`, `debug-momo-return.txt`
2. Chạy `test-momo.php` để kiểm tra cấu hình
3. Kiểm tra database bảng `thanh_toan`
4. Xem console browser (F12) để debug JavaScript
