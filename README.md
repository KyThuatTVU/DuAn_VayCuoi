# Hệ Thống Quản Lý Cửa Hàng Váy Cưới

## Cấu Trúc Dự Án

``
wedding-dress/
├── assets/
│   ├── css/
│   │   ├── style.css          # CSS chính
│   │   └── responsive.css     # CSS responsive
│   ├── js/
│   │   └── main.js           # JavaScript chính
│   └── images/               # Thư mục hình ảnh
├── includes/
│   ├── config.php            # Cấu hình database
│   ├── header.php            # Header chung
│   └── footer.php            # Footer chung
├── index.php                 # Trang chủ
├── products.php              # Danh sách váy cưới
├── product-detail.php        # Chi tiết váy cưới
├── booking.php               # Đặt lịch thử váy
├── contact.php               # Liên hệ
├── blog.php                  # Tin tức
└── README.md
```

## Các Trang Đã Tạo

### 1. Trang Chủ (index.php)
- Hero slider
- Danh mục váy cưới
- Sản phẩm nổi bật
- Banner khuyến mãi
- Dịch vụ
- Đánh giá khách hàng
- Tin tức

### 2. Danh Sách Váy (products.php)
- Sidebar lọc sản phẩm
- Grid hiển thị váy
- Sắp xếp và phân trang

### 3. Chi Tiết Váy (product-detail.php)
- Gallery ảnh
- Thông tin chi tiết
- Chọn size, số lượng
- Tabs: Mô tả, Thông số, Đánh giá, Chính sách
- Sản phẩm liên quan

### 4. Đặt Lịch (booking.php)
- Form đặt lịch thử váy
- Thông tin showroom
- Lưu ý khi thử váy

### 5. Liên Hệ (contact.php)
- Form liên hệ
- Thông tin liên hệ
- Google Maps

### 6. Tin Tức (blog.php)
- Bài viết nổi bật
- Danh sách bài viết
- Phân trang

## Cài Đặt

### 1. Cấu Hình Database
- Import file SQL vào MySQL
- Cập nhật thông tin database trong `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cua_hang_vay_cuoi_db');
```

### 2. Cấu Hình Web Server
- Đặt project vào thư mục web root (htdocs/www)
- Truy cập: http://localhost/wedding-dress

### 3. Thêm Hình Ảnh
Tạo các file ảnh trong `assets/images/`:
- logo.png
- hero-1.jpg
- cat-princess.jpg, cat-mermaid.jpg, cat-aline.jpg, cat-modern.jpg
- dress-1.jpg đến dress-12.jpg
- blog-1.jpg đến blog-9.jpg
- customer-1.jpg, customer-2.jpg, customer-3.jpg
- payment-visa.png, payment-mastercard.png, payment-momo.png, payment-vnpay.png

## Màu Sắc Thiết Kế

- Primary: #d4a574 (Vàng gold)
- Secondary: #f5e6d3 (Kem)
- Accent: #c89b6d (Vàng đậm)
- Text Dark: #2c2c2c
- Text Light: #666
- Background: #faf8f5

## Tính Năng Chưa Hoàn Thiện

- Kết nối database thực tế
- Xử lý form (đăng ký, đăng nhập, đặt hàng)
- Giỏ hàng và thanh toán
- Chatbot
- Admin dashboard
- Upload ảnh
- Tìm kiếm nâng cao

## Ghi Chú

- Giao diện đã responsive cho mobile/tablet
- Sử dụng CSS Grid và Flexbox
- Chưa tích hợp icon font (cần thêm Font Awesome hoặc tương tự)
- Placeholder cho hình ảnh cần được thay thế bằng ảnh thật
