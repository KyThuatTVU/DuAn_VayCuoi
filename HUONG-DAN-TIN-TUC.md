# Hướng Dẫn Sử Dụng Trang Tin Tức

## 📋 Tổng Quan
Trang tin tức đã được làm lại hoàn toàn với:
- ✅ Kết nối database thực tế (bảng `tin_tuc_cuoi_hoi`)
- ✅ Giao diện TailwindCSS hiện đại, responsive
- ✅ Phân trang tự động
- ✅ Bài viết nổi bật
- ✅ Bài viết liên quan
- ✅ Chia sẻ mạng xã hội

## 🗄️ Cấu Trúc Database

### Bảng: tin_tuc_cuoi_hoi
```sql
- id: ID bài viết
- admin_id: ID admin đăng bài
- title: Tiêu đề bài viết
- slug: URL thân thiện (dùng để truy cập)
- summary: Tóm tắt ngắn
- content: Nội dung đầy đủ
- cover_image: Đường dẫn ảnh đại diện
- status: Trạng thái (draft/published/archived)
- published_at: Ngày xuất bản
- created_at: Ngày tạo
```

## 🚀 Cài Đặt

### Bước 1: Import dữ liệu mẫu
```bash
# Mở phpMyAdmin hoặc MySQL client
# Chạy file: them-du-lieu-tin-tuc.sql
```

Hoặc dùng command line:
```bash
mysql -u root -p cua_hang_vay_cuoi_db < them-du-lieu-tin-tuc.sql
```

### Bước 2: Kiểm tra kết nối database
File `.env` đã có cấu hình:
```
DB_HOST=localhost
DB_USER=root
DB_PASS=TVU@842004
DB_NAME=cua_hang_vay_cuoi_db
```

### Bước 3: Truy cập trang tin tức
```
http://localhost/DuAn_CuaHangVayCuoiGradenHome/blog.php
```

## 📁 Files Đã Tạo/Cập Nhật

1. **blog.php** - Trang danh sách tin tức
   - Hiển thị bài viết nổi bật
   - Grid 3 cột responsive
   - Phân trang tự động
   - Kết nối database thực

2. **blog-detail.php** - Trang chi tiết bài viết
   - Hiển thị nội dung đầy đủ
   - Thông tin tác giả, ngày đăng
   - Nút chia sẻ Facebook, Twitter
   - Bài viết liên quan
   - Breadcrumb navigation

3. **them-du-lieu-tin-tuc.sql** - File SQL
   - 9 bài viết mẫu đầy đủ
   - Nội dung chi tiết về váy cưới
   - Đã publish sẵn

## 🎨 Giao Diện TailwindCSS

### Màu sắc chính
- Pink: `#EC4899` (pink-600)
- Gray: Các tone từ 50-900
- White: `#FFFFFF`

### Responsive Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

### Components
- Cards với hover effects
- Gradient backgrounds
- Shadow transitions
- Rounded corners
- Icons SVG

## 📝 Cách Thêm Bài Viết Mới

### Qua SQL:
```sql
INSERT INTO tin_tuc_cuoi_hoi 
(admin_id, title, slug, summary, content, cover_image, status, published_at) 
VALUES
(1, 'Tiêu đề bài viết', 'tieu-de-bai-viet', 
'Tóm tắt ngắn...', 'Nội dung đầy đủ...', 
'assets/images/blog-10.jpg', 'published', NOW());
```

### Lưu ý:
- `slug` phải unique và không dấu
- `status` = 'published' để hiển thị
- `cover_image` nên có ảnh thật (hoặc dùng placeholder)

## 🖼️ Ảnh Đại Diện

### Đường dẫn mặc định:
```
assets/images/blog-default.jpg
```

### Kích thước khuyến nghị:
- Featured post: 1200x600px
- Blog card: 800x600px
- Tỷ lệ: 4:3 hoặc 16:9

### Tạo ảnh placeholder:
Bạn có thể dùng:
- https://placeholder.com/
- https://picsum.photos/
- Hoặc tạo ảnh thật trong folder `assets/images/`

## 🔧 Tùy Chỉnh

### Thay đổi số bài viết mỗi trang:
File: `blog.php`, dòng 7
```php
$limit = 9; // Thay đổi số này
```

### Thay đổi số bài viết liên quan:
File: `blog-detail.php`, dòng 28
```php
LIMIT 3 // Thay đổi số này
```

### Thêm category/tags:
Cần tạo thêm bảng và cập nhật query

## 🐛 Xử Lý Lỗi

### Lỗi: "Chưa có bài viết nào"
- Kiểm tra đã import SQL chưa
- Kiểm tra `status = 'published'`
- Kiểm tra kết nối database

### Lỗi: Ảnh không hiển thị
- Kiểm tra đường dẫn trong database
- Tạo folder `assets/images/` nếu chưa có
- Upload ảnh hoặc dùng placeholder

### Lỗi: 404 Not Found
- Kiểm tra file `blog-detail.php` đã tạo chưa
- Kiểm tra slug trong URL
- Kiểm tra `.htaccess` nếu dùng URL rewrite

## 📱 Tính Năng

### ✅ Đã Hoàn Thành
- [x] Kết nối database
- [x] Giao diện TailwindCSS
- [x] Responsive mobile
- [x] Phân trang
- [x] Bài viết nổi bật
- [x] Bài viết liên quan
- [x] Chia sẻ mạng xã hội
- [x] Breadcrumb
- [x] SEO friendly URLs

### 🔜 Có Thể Mở Rộng
- [ ] Tìm kiếm bài viết
- [ ] Lọc theo category
- [ ] Bình luận
- [ ] Lượt xem
- [ ] Tags
- [ ] Admin panel để quản lý

## 📞 Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
1. Database đã import đúng chưa
2. File config.php kết nối OK
3. TailwindCSS đã load trong header
4. Console browser có lỗi JS không

---

**Chúc bạn thành công! 🎉**
