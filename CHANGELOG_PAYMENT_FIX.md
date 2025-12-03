# Changelog - Sửa Logic Thanh Toán

## Vấn đề
- Đơn hàng đã thanh toán thành công vẫn hiển thị nút "Tiếp tục thanh toán"
- Không kiểm tra trạng thái giao dịch trong bảng `thanh_toan`

## Giải pháp

### 1. my-orders.php
**Cập nhật query:**
```sql
CASE 
    WHEN dh.trang_thai_thanh_toan = 'pending' 
    AND dh.trang_thai != 'cancelled'
    AND TIMESTAMPDIFF(MINUTE, dh.created_at, NOW()) < 10 
    AND (t.status IS NULL OR t.status NOT IN ('success', 'completed'))
    THEN 1
    ELSE 0
END as can_continue_payment
```

**Điều kiện hiển thị nút:**
- ✅ Chỉ hiển thị khi `trang_thai_thanh_toan = 'pending'`
- ✅ Đơn hàng chưa bị hủy
- ✅ Còn trong thời gian 10 phút
- ✅ Chưa có giao dịch thành công trong bảng `thanh_toan`

**Hiển thị theo trạng thái:**
- `paid`: "Đã thanh toán thành công" (màu xanh)
- `pending` + < 10 phút: "Tiếp tục thanh toán (còn X phút)"
- `pending` + >= 10 phút: "Đã hết hạn thanh toán"
- `failed`: "Thanh toán thất bại"

### 2. order-detail.php
**Cập nhật điều kiện:**
```php
<?php if ($order['trang_thai_thanh_toan'] === 'paid'): ?>
    <!-- Hiển thị đã thanh toán -->
<?php elseif ($order['trang_thai_thanh_toan'] === 'pending' 
    && $order['minutes_ago'] < 10 
    && $order['payment_status'] !== 'success'): ?>
    <!-- Hiển thị nút tiếp tục thanh toán -->
<?php endif; ?>
```

**Các trạng thái hiển thị:**
- `paid`: Box xanh "Đã thanh toán thành công"
- `pending` + < 10 phút + chưa có giao dịch: Nút "Tiếp tục thanh toán"
- `pending` + >= 10 phút: Box xám "Đã hết hạn"
- `failed`: Box đỏ "Thanh toán thất bại"

### 3. payment-qr.php
**Đã có validation:**
```php
// Kiểm tra đã thanh toán chưa
if ($order_data['trang_thai_thanh_toan'] === 'paid') {
    header('Location: order-success.php?order_id=' . $order_id);
    exit;
}
```

## Luồng thanh toán đúng

### Khi thanh toán thành công:
1. MoMo gọi IPN → `api/momo-ipn.php`
2. Cập nhật `thanh_toan.status = 'success'`
3. Cập nhật `don_hang.trang_thai_thanh_toan = 'paid'`
4. Cập nhật `don_hang.trang_thai = 'processing'`

### Khi người dùng vào my-orders.php:
1. Query kiểm tra cả `don_hang.trang_thai_thanh_toan` và `thanh_toan.status`
2. Nếu `paid` hoặc `thanh_toan.status = 'success'` → Không hiển thị nút
3. Nếu `pending` + chưa có giao dịch + < 10 phút → Hiển thị nút
4. Nếu `pending` + >= 10 phút → Hiển thị "Hết hạn"

## Kết quả
- ✅ Đơn hàng đã thanh toán KHÔNG còn nút "Tiếp tục thanh toán"
- ✅ Hiển thị rõ ràng trạng thái thanh toán
- ✅ Ngăn chặn thanh toán trùng lặp
- ✅ UX tốt hơn với các trạng thái rõ ràng

## Test Cases

### Case 1: Đơn hàng mới tạo (< 10 phút, chưa thanh toán)
- ✅ Hiển thị: "Tiếp tục thanh toán (còn X phút)"
- ✅ Click vào → Chuyển đến payment-qr.php

### Case 2: Đơn hàng đã thanh toán
- ✅ Hiển thị: "Đã thanh toán thành công" (màu xanh)
- ✅ KHÔNG có nút thanh toán

### Case 3: Đơn hàng hết hạn (>= 10 phút, chưa thanh toán)
- ✅ Hiển thị: "Đã hết hạn thanh toán"
- ✅ KHÔNG có nút thanh toán

### Case 4: Thanh toán thất bại
- ✅ Hiển thị: "Thanh toán thất bại"
- ✅ KHÔNG có nút thanh toán

### Case 5: Truy cập payment-qr.php với đơn đã thanh toán
- ✅ Redirect về order-success.php
- ✅ Không cho phép thanh toán lại
