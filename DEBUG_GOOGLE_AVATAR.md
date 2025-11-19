# 🔍 HƯỚNG DẪN DEBUG AVATAR GOOGLE

## Vấn đề: Avatar từ Google không hiển thị sau khi đăng nhập

### Các bước kiểm tra:

## 1️⃣ Kiểm tra Session
Truy cập: `http://localhost/DuAn_CuaHangVayCuoiGradenHome/test-session.php`

Kiểm tra:
- ✅ `user_avatar` có giá trị không?
- ✅ URL avatar có đúng format không?
- ✅ Avatar trong DB có khớp với session không?

## 2️⃣ Kiểm tra Database

```sql
SELECT id, ho_ten, email, avt FROM nguoi_dung WHERE email = 'your-google-email@gmail.com';
```

Kiểm tra:
- ✅ Cột `avt` có chứa URL Google không?
- ✅ URL có dạng: `https://lh3.googleusercontent.com/...`

## 3️⃣ Kiểm tra Google OAuth Response

Trong file `google-callback.php`, thêm debug tạm thời:

```php
// Sau dòng: $user_info = json_decode($user_info_response, true);
// Thêm:
error_log("Google User Info: " . print_r($user_info, true));
error_log("Avatar URL: " . ($user_info['picture'] ?? 'NO PICTURE'));
```

Xem log trong: `php_error.log` hoặc console

## 4️⃣ Các nguyên nhân thường gặp:

### ❌ Avatar không được lưu vào DB
**Giải pháp:** Đã fix trong `google-callback.php` - luôn cập nhật avatar từ Google

### ❌ Session không được set đúng
**Giải pháp:** Đã fix - đảm bảo `$_SESSION['user_avatar']` luôn được set

### ❌ URL Google bị chặn bởi CSP (Content Security Policy)
**Giải pháp:** Thêm vào `<head>` của header.php:
```html
<meta http-equiv="Content-Security-Policy" content="img-src 'self' https://lh3.googleusercontent.com data:;">
```

### ❌ Avatar URL hết hạn
**Giải pháp:** Google avatar URLs thường không hết hạn, nhưng nếu có vấn đề, cần refresh lại

## 5️⃣ Test thủ công

### Test 1: Kiểm tra URL trực tiếp
Copy URL avatar từ session/DB và mở trực tiếp trong browser
- Nếu hiển thị → Vấn đề ở code hiển thị
- Nếu không hiển thị → Vấn đề ở URL

### Test 2: Kiểm tra HTML
View source trang web, tìm:
```html
<img src="https://lh3.googleusercontent.com/..." alt="Avatar" class="user-avatar">
```
- Nếu có → Vấn đề ở CSS
- Nếu không có → Vấn đề ở PHP logic

### Test 3: Kiểm tra Console
Mở Developer Tools (F12) → Console
- Xem có lỗi CORS không?
- Xem có lỗi 404 không?

## 6️⃣ Giải pháp đã áp dụng:

✅ **Cập nhật `google-callback.php`:**
- Luôn sử dụng avatar từ Google cho session
- Cập nhật DB nếu chưa có avatar hoặc không phải local file

✅ **Cập nhật CSS:**
- Thêm `display: block` và `background` cho `.user-avatar`

✅ **Tạo trang test:**
- `test-session.php` để debug session và avatar

## 7️⃣ Cách test sau khi fix:

1. **Đăng xuất** (nếu đang đăng nhập)
2. **Xóa session** trong browser (hoặc clear cookies)
3. **Đăng nhập lại bằng Google**
4. **Kiểm tra avatar** ở header
5. **Truy cập `test-session.php`** để xem chi tiết

## 8️⃣ Nếu vẫn không hiển thị:

### Kiểm tra quyền Google API:
1. Vào Google Cloud Console
2. Kiểm tra scope có `profile` không
3. Kiểm tra Google+ API đã enable chưa

### Kiểm tra response từ Google:
Thêm vào `google-callback.php`:
```php
file_put_contents('google_debug.txt', print_r($user_info, true));
```

Xem file `google_debug.txt` để biết Google trả về gì

## 9️⃣ Liên hệ hỗ trợ:

Nếu vẫn gặp vấn đề, cung cấp:
- Screenshot của `test-session.php`
- Nội dung `google_debug.txt`
- Screenshot console errors (F12)
- Thông tin browser đang dùng

---

## ✅ Checklist hoàn chỉnh:

- [ ] Google OAuth đã cấu hình đúng
- [ ] Scope có `profile` và `email`
- [ ] Database có cột `avt` kiểu VARCHAR(500)
- [ ] Session được start đúng cách
- [ ] CSS cho `.user-avatar` đã đúng
- [ ] URL avatar từ Google hợp lệ
- [ ] Không có lỗi CORS trong console
- [ ] Avatar hiển thị trong `test-session.php`
