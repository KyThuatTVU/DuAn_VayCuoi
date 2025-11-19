# HƯỚNG DẪN CẤU HÌNH GOOGLE OAUTH

## Bước 1: Tạo Google Cloud Project

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Đăng nhập bằng tài khoản Google
3. Click "Select a project" → "New Project"
4. Đặt tên project (VD: "Vay Cuoi Website")
5. Click "Create"

## Bước 2: Kích hoạt Google+ API

1. Trong project vừa tạo, vào menu bên trái
2. Chọn "APIs & Services" → "Library"
3. Tìm "Google+ API"
4. Click "Enable"

## Bước 3: Tạo OAuth 2.0 Credentials

1. Vào "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "OAuth client ID"
3. Nếu chưa có OAuth consent screen:
   - Click "Configure Consent Screen"
   - Chọn "External" → "Create"
   - Điền thông tin:
     - App name: Váy Cưới Thiên Thần
     - User support email: email của bạn
     - Developer contact: email của bạn
   - Click "Save and Continue"
   - Bỏ qua phần Scopes → "Save and Continue"
   - Bỏ qua phần Test users → "Save and Continue"

4. Quay lại "Credentials" → "Create Credentials" → "OAuth client ID"
5. Chọn "Application type": Web application
6. Đặt tên: "Vay Cuoi OAuth"
7. Thêm "Authorized redirect URIs":
   ```
   http://localhost/DuAn_CuaHangVayCuoiGradenHome/google-callback.php
   ```
   (Thay đổi đường dẫn cho phù hợp với project của bạn)

8. Click "Create"
9. Copy **Client ID** và **Client Secret**

## Bước 4: Cấu hình file .env

Mở file `.env` và cập nhật:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost/DuAn_CuaHangVayCuoiGradenHome/google-callback.php
```

**Lưu ý:** 
- Thay `your-client-id-here` bằng Client ID từ Google
- Thay `your-client-secret-here` bằng Client Secret từ Google
- Đảm bảo `GOOGLE_REDIRECT_URI` khớp với URI đã đăng ký trên Google Console

## Bước 5: Test đăng nhập

1. Truy cập trang đăng nhập: `login.php`
2. Click nút "Đăng nhập với Google"
3. Chọn tài khoản Google
4. Cho phép ứng dụng truy cập thông tin
5. Sẽ được redirect về trang chủ với thông báo đăng nhập thành công

## Cách hoạt động

1. **google-login.php**: Tạo URL đăng nhập Google và redirect
2. **google-callback.php**: Nhận code từ Google, đổi lấy access token, lấy thông tin user
3. Nếu email đã tồn tại → Đăng nhập
4. Nếu email chưa tồn tại → Tạo tài khoản mới và đăng nhập

## Lưu ý bảo mật

- **KHÔNG** commit file `.env` lên Git
- Giữ `Client Secret` bí mật
- Khi deploy lên production, cập nhật lại `GOOGLE_REDIRECT_URI` với domain thật
- Thêm domain production vào "Authorized redirect URIs" trên Google Console

## Troubleshooting

### Lỗi "redirect_uri_mismatch"
- Kiểm tra `GOOGLE_REDIRECT_URI` trong `.env` khớp với URI trên Google Console
- Đảm bảo không có khoảng trắng thừa
- URI phải giống y hệt (bao gồm http/https, port, path)

### Lỗi "invalid_client"
- Kiểm tra `GOOGLE_CLIENT_ID` và `GOOGLE_CLIENT_SECRET` đúng chưa
- Đảm bảo đã copy đầy đủ, không bị cắt

### Không lấy được thông tin user
- Kiểm tra Google+ API đã được enable chưa
- Kiểm tra scope trong `google-login.php` có đúng không

## Deploy lên Production

Khi deploy lên server thật:

1. Cập nhật `.env`:
```env
GOOGLE_REDIRECT_URI=https://yourdomain.com/google-callback.php
```

2. Thêm URI mới vào Google Console:
   - Vào "Credentials" → Edit OAuth client
   - Thêm `https://yourdomain.com/google-callback.php` vào "Authorized redirect URIs"
   - Save

3. Test lại chức năng đăng nhập
