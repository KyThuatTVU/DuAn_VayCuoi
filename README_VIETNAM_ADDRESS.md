# Hướng dẫn tích hợp API Địa chỉ Việt Nam

## 1. Cập nhật Database

Chạy file SQL để thêm các cột địa chỉ mới:

```sql
-- Chạy file database-vietnam-address.sql
SOURCE database-vietnam-address.sql;
```

Hoặc chạy trực tiếp:

```sql
-- Thêm cột vào bảng nguoi_dung
ALTER TABLE nguoi_dung 
ADD COLUMN tinh_thanh VARCHAR(100) NULL COMMENT 'Mã tỉnh/thành phố' AFTER dia_chi,
ADD COLUMN quan_huyen VARCHAR(100) NULL COMMENT 'Mã quận/huyện' AFTER tinh_thanh,
ADD COLUMN phuong_xa VARCHAR(100) NULL COMMENT 'Mã phường/xã' AFTER quan_huyen,
ADD COLUMN dia_chi_cu_the VARCHAR(500) NULL COMMENT 'Địa chỉ cụ thể' AFTER phuong_xa;

-- Thêm cột vào bảng don_hang
ALTER TABLE don_hang 
ADD COLUMN tinh_thanh VARCHAR(100) NULL COMMENT 'Mã tỉnh/thành phố' AFTER dia_chi,
ADD COLUMN quan_huyen VARCHAR(100) NULL COMMENT 'Mã quận/huyện' AFTER tinh_thanh,
ADD COLUMN phuong_xa VARCHAR(100) NULL COMMENT 'Mã phường/xã' AFTER quan_huyen,
ADD COLUMN dia_chi_cu_the VARCHAR(500) NULL COMMENT 'Địa chỉ cụ thể' AFTER phuong_xa;
```

## 2. API Endpoints

### Lấy danh sách tỉnh/thành phố
```
GET api/vietnam-address.php?action=provinces
```

### Lấy danh sách quận/huyện theo tỉnh
```
GET api/vietnam-address.php?action=districts&province_code=84
```

### Lấy danh sách phường/xã theo huyện
```
GET api/vietnam-address.php?action=wards&district_code=842
```

### Tìm kiếm tỉnh theo tên
```
GET api/vietnam-address.php?action=search&keyword=tra
```

### Lấy tên đầy đủ từ mã
```
GET api/vietnam-address.php?action=get_names&province_code=84&district_code=842&ward_code=29542
```

## 3. Logic chọn địa chỉ thông minh

Khi người dùng đã lưu địa chỉ trong tài khoản:

- **Đã có tỉnh**: Tự động chọn tỉnh, chỉ cần chọn huyện và xã
- **Đã có tỉnh + huyện**: Tự động chọn tỉnh và huyện, chỉ cần chọn xã
- **Đã có đầy đủ**: Tự động điền tất cả, người dùng chỉ cần xác nhận

## 4. Cấu trúc dữ liệu

### Bảng nguoi_dung
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| tinh_thanh | VARCHAR(100) | Mã tỉnh/thành phố (VD: 84) |
| quan_huyen | VARCHAR(100) | Mã quận/huyện (VD: 842) |
| phuong_xa | VARCHAR(100) | Mã phường/xã (VD: 29542) |
| dia_chi_cu_the | VARCHAR(500) | Số nhà, tên đường... |
| dia_chi | TEXT | Địa chỉ đầy đủ (tự động tạo) |

### Bảng don_hang
Tương tự bảng nguoi_dung

## 5. Danh sách 63 tỉnh thành

API hỗ trợ đầy đủ 63 tỉnh thành Việt Nam với mã theo chuẩn hành chính.

Dữ liệu quận/huyện và phường/xã hiện có cho các tỉnh:
- Hà Nội (01)
- TP. Hồ Chí Minh (79)
- Đà Nẵng (48)
- Cần Thơ (92)
- Trà Vinh (84)
- Hải Phòng (31)
- Bình Dương (74)
- Đồng Nai (75)

Để thêm dữ liệu cho các tỉnh khác, cập nhật mảng `$districts` và `$wards` trong file `api/vietnam-address.php`.
