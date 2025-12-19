# 🔧 Hướng Dẫn Sửa Lỗi Hiển Thị Ngày Trong Giỏ Hàng

## 🐛 Vấn Đề

Trong giỏ hàng, ngày thuê và ngày trả hiển thị sai:
- "4 giờ trước" thay vì "20/12/2024"
- "Vừa xong" thay vì "25/12/2024"
- "6 ngày trước" thay vì "13/12/2024"

## 🔍 Nguyên Nhân

Dữ liệu trong bảng `gio_hang` có ngày thuê trong **quá khứ**:
- User thêm sản phẩm vào giỏ từ lâu nhưng chưa thanh toán
- Dữ liệu test có ngày cũ
- Ngày thuê được set là ngày hiện tại khi thêm vào giỏ

## ✅ Giải Pháp

### Cách 1: Sử dụng Tool Tự Động (Khuyến nghị)

1. Truy cập: `http://localhost/DuAn_CuaHangVayCuoiGradenHome/fix-cart-dates.php`

2. Xem danh sách giỏ hàng có vấn đề (màu đỏ)

3. Click "Sửa Tất Cả" để tự động cập nhật tất cả ngày trong quá khứ

4. Kiểm tra lại giỏ hàng

### Cách 2: Sửa Thủ Công Từng Mục

1. Truy cập `fix-cart-dates.php`
2. Click "Sửa ngay" ở mỗi dòng có vấn đề
3. Ngày sẽ được cập nhật thành ngày mai

### Cách 3: Chạy SQL Trực Tiếp

Mở phpMyAdmin và chạy:

```sql
-- Cập nhật tất cả ngày trong quá khứ
UPDATE gio_hang 
SET ngay_bat_dau_thue = DATE_ADD(NOW(), INTERVAL 1 DAY),
    ngay_tra_vay = DATE_ADD(DATE_ADD(NOW(), INTERVAL 1 DAY), INTERVAL so_ngay_thue DAY)
WHERE ngay_bat_dau_thue < NOW();
```

## 🛡️ Phòng Ngừa

### 1. Validation Khi Thêm Vào Giỏ

Đảm bảo ngày thuê phải >= ngày mai:

```php
// Trong api/cart.php - hàm addToCart
$ngay_bat_dau_thue = $_POST['ngay_bat_dau_thue'] ?? null;

// Validate ngày không được trong quá khứ
if (strtotime($ngay_bat_dau_thue) < strtotime('tomorrow')) {
    echo json_encode([
        'success' => false,
        'message' => 'Ngày thuê phải từ ngày mai trở đi'
    ]);
    return;
}
```

### 2. Tự Động Xóa Giỏ Hàng Cũ

Tạo cronjob hoặc chạy định kỳ:

```sql
-- Xóa giỏ hàng có ngày thuê quá 7 ngày trong quá khứ
DELETE FROM gio_hang 
WHERE ngay_bat_dau_thue < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### 3. Hiển Thị Cảnh Báo

Thêm cảnh báo trong cart.php khi ngày thuê gần hết hạn:

```javascript
// Trong cart.php
function renderCartItems(items, total) {
    items.forEach(item => {
        const startDate = new Date(item.ngay_bat_dau_thue);
        const today = new Date();
        
        if (startDate < today) {
            // Hiển thị cảnh báo
            alert('Ngày thuê đã qua, vui lòng cập nhật lại!');
        }
    });
}
```

## 📊 Kiểm Tra Sau Khi Sửa

1. **Xem giỏ hàng**: Truy cập `cart.php`
   - Ngày thuê phải hiển thị đúng định dạng: "20/12/2024"
   - Không còn "4 giờ trước" hay "Vừa xong"

2. **Xem trang thanh toán**: Truy cập `checkout.php`
   - Ngày hiển thị đúng trong chi tiết đơn hàng

3. **Test thêm mới**: Thêm váy vào giỏ
   - Chọn ngày thuê là ngày mai
   - Kiểm tra hiển thị đúng

## 🔍 Debug

### Kiểm tra dữ liệu trong database:

```sql
SELECT 
    id,
    vay_id,
    ngay_bat_dau_thue,
    ngay_tra_vay,
    so_ngay_thue,
    DATEDIFF(ngay_bat_dau_thue, NOW()) as days_diff,
    created_at
FROM gio_hang
ORDER BY created_at DESC;
```

### Kiểm tra API response:

1. Mở DevTools (F12)
2. Tab Network
3. Reload trang cart.php
4. Xem request `api/cart.php?action=get`
5. Kiểm tra response JSON:

```json
{
  "success": true,
  "items": [
    {
      "ngay_bat_dau_thue": "2024-12-20",  // Phải là ngày tương lai
      "ngay_tra_vay": "2024-12-25"
    }
  ]
}
```

### Kiểm tra JavaScript:

Mở Console và chạy:

```javascript
// Test hàm formatDate
const testDate = "2024-12-20";
console.log(formatDate(testDate)); // Phải ra: "20/12/2024"
```

## 📝 Lưu Ý

1. **Không ảnh hưởng đến đơn hàng đã đặt**: Script chỉ sửa dữ liệu trong bảng `gio_hang`, không động vào `don_hang`

2. **Backup trước khi sửa**: Nếu lo lắng, export bảng `gio_hang` trước

3. **Chạy trong giờ thấp điểm**: Nếu có nhiều user đang online

4. **Thông báo user**: Nếu sửa giỏ hàng của user, nên gửi email thông báo

## 🎯 Kết Quả Mong Đợi

Sau khi sửa:
- ✅ Ngày thuê hiển thị: "20/12/2024" (định dạng dd/mm/yyyy)
- ✅ Ngày trả hiển thị: "25/12/2024"
- ✅ Không còn "4 giờ trước", "Vừa xong", "6 ngày trước"
- ✅ Tất cả ngày đều trong tương lai

## 🆘 Hỗ Trợ

Nếu vẫn gặp vấn đề:

1. Chạy `test-voucher-system.php` để kiểm tra tổng thể
2. Xem Console log trong trình duyệt (F12)
3. Kiểm tra PHP error log
4. Xem file `fix-cart-dates.php` để debug chi tiết

---

**Cập nhật:** 19/12/2024
**Tool:** fix-cart-dates.php
