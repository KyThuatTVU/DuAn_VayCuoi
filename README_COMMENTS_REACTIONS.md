# Hệ Thống Bình Luận và Cảm Xúc

## Tổng Quan
Hệ thống bình luận và thả cảm xúc cho sản phẩm và bài viết với các tính năng:
- ✅ Bình luận và trả lời bình luận (nested comments)
- ✅ Thả cảm xúc (6 loại: Like, Love, Wow, Haha, Sad, Angry)
- ✅ Yêu cầu đăng nhập để sử dụng
- ✅ Hiển thị thông báo khi chưa đăng nhập
- ✅ Xóa bình luận của chính mình
- ✅ Real-time update

## Cài Đặt

### 1. Tạo Database
Chạy file SQL để tạo các bảng cần thiết:
```bash
mysql -u root -p cua_hang_vay_cuoi_db < database-comments-reactions.sql
```

Hoặc import trực tiếp trong phpMyAdmin.

**Lưu ý:** File SQL sẽ tự động kiểm tra và chỉ thêm dữ liệu mẫu nếu đã có người dùng trong bảng `nguoi_dung`. Nếu chưa có người dùng, các bảng sẽ được tạo nhưng không có dữ liệu mẫu (điều này hoàn toàn bình thường).

### 2. Cấu Trúc Database

#### Bảng `binh_luan_san_pham`
- Lưu trữ bình luận cho sản phẩm
- Hỗ trợ nested comments (parent_id)

#### Bảng `binh_luan_bai_viet`
- Lưu trữ bình luận cho bài viết
- Hỗ trợ nested comments (parent_id)

#### Bảng `cam_xuc_san_pham`
- Lưu trữ cảm xúc cho sản phẩm
- Mỗi user chỉ có 1 cảm xúc cho 1 sản phẩm

#### Bảng `cam_xuc_bai_viet`
- Lưu trữ cảm xúc cho bài viết
- Mỗi user chỉ có 1 cảm xúc cho 1 bài viết

## Sử Dụng

### Tích Hợp Vào Trang Sản Phẩm
```php
<?php
// Trong product-detail.php
$comments_type = 'product';
$item_id = $product_id;
require_once 'includes/comments-reactions.php';
?>
```

### Tích Hợp Vào Trang Bài Viết
```php
<?php
// Trong blog-detail.php
$comments_type = 'blog';
$item_id = $post['id'];
require_once 'includes/comments-reactions.php';
?>
```

## API Endpoints

### Comments API

#### Lấy danh sách bình luận
```
GET api/comments-products.php?action=get&vay_id=1
GET api/comments-blogs.php?action=get&bai_viet_id=1
```

#### Thêm bình luận
```
POST api/comments-products.php
{
    action: 'add',
    vay_id: 1,
    noi_dung: 'Nội dung bình luận',
    parent_id: null // Optional, cho reply
}
```

#### Xóa bình luận
```
POST api/comments-products.php
{
    action: 'delete',
    comment_id: 1
}
```

### Reactions API

#### Lấy thống kê cảm xúc
```
GET api/reactions-products.php?action=get&vay_id=1
GET api/reactions-blogs.php?action=get&bai_viet_id=1
```

#### Toggle cảm xúc
```
POST api/reactions-products.php
{
    action: 'toggle',
    vay_id: 1,
    loai_cam_xuc: 'love'
}
```

## Luồng Hoạt Động

### 1. Người Dùng Chưa Đăng Nhập
- Hiển thị thanh bình luận và cảm xúc
- Khi click vào bất kỳ nút nào → Hiển thị thông báo yêu cầu đăng nhập
- Redirect đến trang login với URL quay lại

### 2. Người Dùng Đã Đăng Nhập
- Có thể bình luận, trả lời, xóa bình luận của mình
- Có thể thả cảm xúc, thay đổi cảm xúc
- Click lại cảm xúc đã chọn → Bỏ cảm xúc

## Tính Năng Chi Tiết

### Bình Luận
- ✅ Hiển thị avatar (từ Google hoặc chữ cái đầu)
- ✅ Hiển thị thời gian (relative time: "5 phút trước")
- ✅ Nested comments (trả lời bình luận)
- ✅ Người dùng có thể bình luận qua lại với nhau
- ✅ Hiển thị số lượng trả lời cho mỗi bình luận
- ✅ Badge "Bạn" cho bình luận của chính mình
- ✅ Xóa bình luận của chính mình
- ✅ Real-time update sau khi thêm/xóa
- ✅ Notification khi gửi bình luận thành công
- ✅ Auto-scroll đến bình luận mới
- ✅ Prevent spam với disable button khi đang gửi

### Cảm Xúc
- ✅ 6 loại cảm xúc: 👍 Like, ❤️ Love, 😮 Wow, 😄 Haha, 😢 Sad, 😠 Angry
- ✅ Hiển thị số lượng từng loại
- ✅ Highlight cảm xúc đã chọn
- ✅ Toggle on/off khi click lại
- ✅ Chỉ được chọn 1 cảm xúc

## Bảo Mật

### Kiểm Tra Đăng Nhập
```php
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Vui lòng đăng nhập!',
            'require_login' => true
        ]);
        exit();
    }
}
```

### Kiểm Tra Quyền Sở Hữu
- Chỉ cho phép xóa bình luận của chính mình
- Kiểm tra user_id trong database

### SQL Injection Prevention
- Sử dụng Prepared Statements
- Validate input data

## Tùy Chỉnh

### Thay Đổi Màu Sắc
Chỉnh sửa trong `includes/comments-reactions.php`:
```css
.reaction-btn.active {
    border-color: #3b82f6; /* Màu viền khi active */
    background: #eff6ff;   /* Màu nền khi active */
    color: #3b82f6;        /* Màu chữ khi active */
}
```

### Thêm Loại Cảm Xúc Mới
1. Thêm vào ENUM trong database
2. Thêm vào `REACTION_EMOJIS` trong JavaScript
3. Thêm button trong HTML

## Troubleshooting

### Lỗi Foreign Key Constraint khi import SQL
**Lỗi:** `Cannot add or update a child row: a foreign key constraint fails`

**Nguyên nhân:** Chưa có người dùng trong bảng `nguoi_dung`

**Giải pháp:**
1. File SQL đã được cập nhật để tự động kiểm tra
2. Nếu chưa có người dùng, bảng sẽ được tạo nhưng không có dữ liệu mẫu
3. Bạn có thể đăng ký tài khoản mới hoặc thêm người dùng thủ công:
```sql
INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai) 
VALUES ('Test User', 'test@example.com', 'hashed_password', '0123456789');
```

### Bình luận không hiển thị
- Kiểm tra kết nối database
- Kiểm tra console browser (F12) xem có lỗi API không
- Kiểm tra session đã start chưa
- Kiểm tra bảng đã được tạo chưa

### Không thể thả cảm xúc
- Kiểm tra đã đăng nhập chưa
- Kiểm tra API endpoint có đúng không
- Kiểm tra UNIQUE constraint trong database
- Xem response từ API trong Network tab (F12)

### Lỗi 404 khi gọi API
- Kiểm tra đường dẫn file API (phải có thư mục `api/`)
- Kiểm tra .htaccess nếu có
- Kiểm tra file có tồn tại không

### Không hiển thị avatar
- Avatar lấy từ trường `avt` trong bảng `nguoi_dung`
- Nếu không có avatar, sẽ hiển thị chữ cái đầu của tên
- Kiểm tra đường dẫn ảnh có đúng không

## Demo Data
File SQL đã bao gồm dữ liệu mẫu:
- 3 bình luận sản phẩm
- 4 cảm xúc sản phẩm
- 2 bình luận bài viết
- 2 cảm xúc bài viết

## Quản Lý Admin

### Trang Quản Lý Bình Luận
Admin có thể quản lý tất cả bình luận tại: `admin-comments.php`

**Tính năng:**
- ✅ Xem danh sách bình luận sản phẩm và bài viết
- ✅ Thống kê tổng quan (số lượng bình luận, cảm xúc)
- ✅ Tìm kiếm bình luận theo tên, nội dung, sản phẩm/bài viết
- ✅ Xem chi tiết bình luận và các trả lời
- ✅ Xóa bình luận (bao gồm cả replies)
- ✅ Phân trang
- ✅ Lọc theo loại (sản phẩm/bài viết)

**Truy cập:**
1. Đăng nhập admin tại `admin-login.php`
2. Click menu "Bình luận" trong sidebar
3. Hoặc truy cập trực tiếp: `admin-comments.php`

### API Admin
- `api/admin-comment-details.php` - Lấy chi tiết bình luận và replies

## Yêu Cầu Hệ Thống
- PHP 7.4+
- MySQL 5.7+
- Session enabled
- JavaScript enabled (client-side)

## Files Đã Tạo
```
database-comments-reactions.sql          # SQL tạo bảng và dữ liệu mẫu
api/comments-products.php                # API bình luận sản phẩm
api/comments-blogs.php                   # API bình luận bài viết
api/reactions-products.php               # API cảm xúc sản phẩm
api/reactions-blogs.php                  # API cảm xúc bài viết
api/admin-comment-details.php            # API chi tiết bình luận (admin)
includes/comments-reactions.php          # Component UI & JavaScript
admin-comments.php                       # Trang quản lý admin
test-comments-reactions.php              # Trang test chức năng
README_COMMENTS_REACTIONS.md             # Tài liệu này
```

## Tác Giả
Phát triển bởi Kiro AI Assistant

## License
MIT License
