# Hướng dẫn tích hợp MoMo Payment

## 📋 Thông tin tài khoản Test MoMo

Dự án đã được cấu hình sẵn với tài khoản test MoMo:

```env
MOMO_PARTNER_CODE=MOMOBKUN20180529
MOMO_ACCESS_KEY=klm05TvNBzhg7h7j
MOMO_SECRET_KEY=at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
```

## 🔧 Cấu hình

### 1. File .env
Các thông tin cấu hình MoMo đã được thêm vào file `.env`:

```env
# MoMo Configuration (Test)
MOMO_PARTNER_CODE=MOMOBKUN20180529
MOMO_ACCESS_KEY=klm05TvNBzhg7h7j
MOMO_SECRET_KEY=at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
MOMO_REDIRECT_URL=http://localhost/DuAn_CuaHangVayCuoiGradenHome/momo-return.php
MOMO_IPN_URL=http://localhost/DuAn_CuaHangVayCuoiGradenHome/api/momo-ipn.php
```

**Lưu ý**: Thông tin trên là tài khoản test công khai của MoMo, chỉ dùng cho môi trường development.

### 2. Cấu trúc file

```
project/
├── api/
│   ├── momo-create-payment.php  # Tạo thanh toán MoMo
│   └── momo-ipn.php             # Xử lý IPN callback từ MoMo
├── momo-return.php              # Trang kết quả thanh toán
├── checkout.php                 # Trang thanh toán (đã thêm MoMo)
└── .env                         # Cấu hình MoMo
```

## 🚀 Cách sử dụng

### 1. Thanh toán qua MoMo

1. Khách hàng chọn sản phẩm và thêm vào giỏ hàng
2. Tại trang checkout, chọn phương thức "Ví MoMo"
3. Nhấn "Thanh Toán MoMo"
4. Hệ thống tạo đơn hàng và chuyển đến trang thanh toán MoMo
5. Khách hàng chọn phương thức thanh toán:
   - **Ví MoMo**: Quét QR hoặc đăng nhập ví MoMo
   - **Thẻ ATM**: Nhập thông tin thẻ ATM nội địa
   - **Thẻ quốc tế**: Nhập thông tin thẻ Visa/Master/JCB
6. Hoàn tất thanh toán
7. MoMo chuyển về trang kết quả

### 2. Luồng xử lý

```
Checkout 
  ↓
Create Order (api/create-order.php)
  ↓
MoMo Payment (api/momo-create-payment.php)
  ↓ Lưu thanh_toan (status='initiated')
MoMo Gateway
  ↓
User thanh toán
  ↓
┌─────────────────┬─────────────────┐
│   Return URL    │    IPN URL      │
│ (momo-return)   │  (momo-ipn)     │
│  - Hiển thị KQ  │  - Webhook      │
│  - Cập nhật DB  │  - Cập nhật DB  │
└─────────────────┴─────────────────┘
         ↓
  Update thanh_toan.status = 'success'
  Update don_hang.trang_thai_thanh_toan = 'paid'
  Update don_hang.trang_thai = 'processing'
```

**Lưu ý quan trọng:**
- **Return URL**: Được gọi khi user quay về từ MoMo (luôn được gọi)
- **IPN URL**: Webhook từ MoMo (chỉ hoạt động với public URL)
- Trong localhost, chỉ có Return URL hoạt động → Database được cập nhật qua Return URL
- Trong production, cả 2 đều hoạt động → Có cơ chế check duplicate

## 📱 Test thanh toán MoMo

### Phương thức thanh toán:
Hệ thống sử dụng `requestType: payWithATM` cho phép:
- ✅ Thanh toán bằng **Ví MoMo**
- ✅ Thanh toán bằng **Thẻ ATM nội địa**
- ✅ Thanh toán bằng **Thẻ Visa/Master/JCB**

### Tài khoản test MoMo:

#### Ví MoMo Test:
- **Số điện thoại**: 0963181714
- **OTP**: Nhập bất kỳ 6 số
- **Mật khẩu**: Nhập bất kỳ

#### Thẻ ATM Test:
- **Số thẻ**: 9704 0000 0000 0018
- **Tên chủ thẻ**: NGUYEN VAN A
- **Ngày phát hành**: 03/07
- **OTP**: Nhập bất kỳ 6 số

#### Thẻ Visa/Master Test:
- **Số thẻ**: 5200 0000 0000 0000
- **Tên chủ thẻ**: NGUYEN VAN A
- **Ngày hết hạn**: 12/25
- **CVV**: 123

### Các trường hợp test:

1. **Thanh toán thành công**:
   - Chọn "Thanh toán thành công" trong trang test
   - resultCode = 0

2. **Thanh toán thất bại**:
   - Chọn "Thanh toán thất bại" trong trang test
   - resultCode != 0

3. **Hủy thanh toán**:
   - Click nút "Hủy" hoặc đóng trang
   - Người dùng quay về trang checkout

## 🔐 Bảo mật

### Xác thực chữ ký (Signature)

MoMo sử dụng HMAC SHA256 để xác thực:

```php
$rawHash = "accessKey=" . $accessKey . 
           "&amount=" . $amount . 
           "&extraData=" . $extraData . 
           "&ipnUrl=" . $ipnUrl . 
           "&orderId=" . $orderId . 
           "&orderInfo=" . $orderInfo . 
           "&partnerCode=" . $partnerCode . 
           "&redirectUrl=" . $redirectUrl . 
           "&requestId=" . $requestId . 
           "&requestType=" . $requestType;

$signature = hash_hmac("sha256", $rawHash, $secretKey);
```

## 📊 Trạng thái đơn hàng

| resultCode | Ý nghĩa | Xử lý |
|------------|---------|-------|
| 0 | Thành công | Cập nhật đơn hàng thành `paid` |
| 9000 | Giao dịch đã được xác nhận thành công | Cập nhật đơn hàng thành `paid` |
| 10 | Invalid signature | Sai chữ ký |
| 20 | Bad format request | Format dữ liệu sai |
| 22 | Amount limit exceeded | Số tiền vượt giới hạn (10K-50M VNĐ) |
| Khác | Thất bại | Giữ nguyên trạng thái `pending` |

## 🐛 Debug

### Log files:
- `debug-momo-ipn.txt` - Log IPN callback từ MoMo
- `debug-momo-return.txt` - Log return URL

### Kiểm tra:
```bash
# Xem log IPN
type debug-momo-ipn.txt

# Xem log Return
type debug-momo-return.txt
```

## 🎯 Request Types

MoMo hỗ trợ nhiều loại request type:

| Request Type | Mô tả | Phương thức thanh toán |
|--------------|-------|------------------------|
| `captureWallet` | Chỉ ví MoMo | Quét QR hoặc đăng nhập ví MoMo |
| `payWithATM` | Đa phương thức | Ví MoMo + Thẻ ATM + Thẻ quốc tế |
| `payWithCC` | Chỉ thẻ quốc tế | Visa/Master/JCB |

**Dự án đang sử dụng**: `payWithATM` (cho phép khách hàng linh hoạt chọn phương thức)

## 📝 API Endpoints

### 1. Tạo thanh toán
```
POST /api/momo-create-payment.php
Content-Type: application/json

{
  "order_id": 123
}

Response:
{
  "success": true,
  "payUrl": "https://test-payment.momo.vn/..."
}
```

**Request Data gửi đến MoMo**:
```json
{
  "partnerCode": "MOMOBKUN20180529",
  "accessKey": "klm05TvNBzhg7h7j",
  "requestId": "MOMO_123_1234567890",
  "amount": "100000",
  "orderId": "MOMO_123_1234567890",
  "orderInfo": "Thanh toan don hang #DH123",
  "redirectUrl": "http://localhost/project/momo-return.php",
  "ipnUrl": "http://localhost/project/api/momo-ipn.php",
  "extraData": "",
  "requestType": "payWithATM",
  "signature": "...",
  "lang": "vi"
}
```

### 2. IPN Callback (Webhook)
```
POST /api/momo-ipn.php
Content-Type: application/x-www-form-urlencoded

partnerCode=MOMOBKUN20180529
orderId=MOMO_123_1234567890
requestId=MOMO_123_1234567890
amount=100000
...
signature=abc123...
```

### 3. Return URL
```
GET /momo-return.php?partnerCode=...&orderId=...&resultCode=0&...
```

## 🔗 Tài liệu tham khảo

- [MoMo Developer Portal](https://developers.momo.vn/)
- [API Documentation](https://developers.momo.vn/v3/docs/payment/api/wallet/onetime)
- [Test Environment](https://developers.momo.vn/v3/docs/payment/guide/test)

## ⚠️ Lưu ý

1. **Môi trường Test**: Đang sử dụng môi trường test của MoMo
2. **Giới hạn số tiền**:
   - Tối thiểu: **10,000 VNĐ**
   - Tối đa: **50,000,000 VNĐ** (môi trường test)
   - Production: Tối đa 100,000,000 VNĐ
3. **IPN URL**: Cần public URL để MoMo gọi callback (dùng ngrok cho localhost)
4. **Timeout**: Giao dịch có thời gian timeout 10 phút
5. **Signature**: Luôn xác thực signature từ MoMo để đảm bảo an toàn
6. **Amount**: Phải là integer string (VD: "100000"), KHÔNG được có dấu thập phân
7. **OrderInfo**: Nên dùng tiếng Việt không dấu để tránh lỗi encoding

## 🌐 Sử dụng ngrok cho IPN (Development)

```bash
# Cài đặt ngrok
# Download từ https://ngrok.com/download

# Chạy ngrok
ngrok http 80

# Cập nhật MOMO_IPN_URL trong .env
MOMO_IPN_URL=https://your-ngrok-url.ngrok.io/DuAn_CuaHangVayCuoiGradenHome/api/momo-ipn.php
```

## 📞 Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. Log files (debug-momo-*.txt)
2. Browser console
3. Network tab trong DevTools
4. MoMo Developer Portal
