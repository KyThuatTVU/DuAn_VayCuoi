-- Thêm dữ liệu mẫu cho bảng tin tức
USE cua_hang_vay_cuoi_db;

-- Xóa dữ liệu cũ nếu có
DELETE FROM tin_tuc_cuoi_hoi;

-- Thêm bài viết mẫu
INSERT INTO tin_tuc_cuoi_hoi (admin_id, title, slug, summary, content, cover_image, status, published_at) VALUES
(1, 'Top 10 Xu Hướng Váy Cưới 2024 Không Thể Bỏ Lỡ', 'top-10-xu-huong-vay-cuoi-2024', 
'Khám phá những xu hướng váy cưới hot nhất năm 2024 từ các sàn diễn thời trang lớn trên thế giới.', 
'Năm 2024 đánh dấu sự trở lại của những thiết kế váy cưới đầy ấn tượng và độc đáo. Từ phong cách tối giản thanh lịch đến những mẫu váy lộng lẫy với chi tiết cầu kỳ, các nhà thiết kế đã mang đến vô số lựa chọn cho các cô dâu.

1. Váy Cưới Tối Giản (Minimalist)
Phong cách tối giản vẫn giữ vững vị trí trong lòng các cô dâu hiện đại. Những đường nét đơn giản, chất liệu cao cấp và form dáng hoàn hảo tạo nên vẻ đẹp thanh lịch, sang trọng.

2. Váy Cưới Vintage
Sự kết hợp giữa nét cổ điển và hiện đại tạo nên phong cách vintage độc đáo. Ren, voan, và những chi tiết thêu tay tinh xảo là điểm nhấn của xu hướng này.

3. Váy Cưới Công Chúa
Mẫu váy bồng bềnh, lộng lẫy vẫn là lựa chọn hàng đầu cho những cô dâu yêu thích sự cổ tích và lãng mạn.

4. Váy Cưới Đuôi Cá
Thiết kế ôm sát tôn dáng, đuôi váy xòe rộng tạo hiệu ứng thị giác ấn tượng, phù hợp với cô dâu có vóc dáng cân đối.

5. Váy Cưới Bohemian
Phong cách tự do, gần gũi với thiên nhiên với chất liệu nhẹ nhàng, họa tiết hoa lá và kiểu dáng thoải mái.

Hãy đến với Váy Cưới Thiên Thần để được tư vấn và lựa chọn mẫu váy phù hợp nhất với phong cách của bạn!', 
'assets/images/blog-1.jpg', 'published', '2024-11-15 10:00:00'),

(1, 'Cẩm Nang Chọn Váy Cưới Phù Hợp Với Vóc Dáng', 'cam-nang-chon-vay-cuoi-phu-hop-voc-dang', 
'Hướng dẫn chi tiết cách chọn váy cưới phù hợp với từng dáng người để tôn lên vẻ đẹp tự nhiên.', 
'Việc chọn váy cưới phù hợp với vóc dáng là yếu tố quan trọng giúp cô dâu tự tin và rạng rỡ nhất trong ngày trọng đại.

DÁNG NGƯỜI CAO, GẦY
- Nên chọn: Váy có chi tiết xếp ly, bèo nhún để tạo khối
- Tránh: Váy quá đơn giản, ôm sát

DÁNG NGƯỜI THẤP, NHỎ
- Nên chọn: Váy chữ A, váy ngắn hoặc váy có đường cắt cao
- Tránh: Váy bồng quá nhiều tầng, váy có đuôi dài

DÁNG NGƯỜI ĐẦY ĐẶN
- Nên chọn: Váy chữ A, váy có cổ V để tạo cảm giác thon gọn
- Tránh: Váy ôm sát, váy có nhiều chi tiết rườm rà

DÁNG NGƯỜI CÂN ĐỐI
- Nên chọn: Hầu hết các kiểu váy đều phù hợp
- Có thể thử nghiệm nhiều phong cách khác nhau

Đến với Váy Cưới Thiên Thần, đội ngũ tư vấn chuyên nghiệp sẽ giúp bạn tìm được chiếc váy hoàn hảo!', 
'assets/images/blog-2.jpg', 'published', '2024-11-12 14:30:00'),

(1, '5 Mẹo Chụp Ảnh Cưới Đẹp Lung Linh', '5-meo-chup-anh-cuoi-dep-lung-linh', 
'Những bí quyết giúp bạn có bộ ảnh cưới đẹp như mơ, lưu giữ khoảnh khắc hạnh phúc trọn vẹn.', 
'Ảnh cưới là kỷ niệm đẹp đẽ mà mọi cặp đôi đều muốn lưu giữ. Dưới đây là 5 mẹo giúp bạn có bộ ảnh cưới hoàn hảo:

1. CHỌN THỜI ĐIỂM CHỤP PHÙ HỢP
Ánh sáng tự nhiên vào buổi sáng sớm hoặc chiều tà là lý tưởng nhất. Tránh chụp vào giữa trưa khi ánh nắng quá gắt.

2. CHỌN ĐỊA ĐIỂM Ý NGHĨA
Địa điểm chụp nên có ý nghĩa đặc biệt với cặp đôi hoặc có khung cảnh đẹp, phù hợp với concept.

3. TRANG PHỤC HÀI HÒA
Váy cưới và vest cần hài hòa về màu sắc và phong cách. Chuẩn bị thêm 2-3 bộ trang phục để đa dạng hóa bộ ảnh.

4. TƯ THẾ TỰ NHIÊN
Đừng quá gò bó, hãy thể hiện tình cảm chân thật. Những khoảnh khắc tự nhiên thường đẹp nhất.

5. CHUẨN BỊ KỸ LƯỠNG
Ngủ đủ giấc, chăm sóc da trước ngày chụp, chuẩn bị đầy đủ phụ kiện và trang điểm phù hợp.

Thuê váy cưới tại Váy Cưới Thiên Thần để có những bộ váy đẹp nhất cho bộ ảnh cưới của bạn!', 
'assets/images/blog-3.jpg', 'published', '2024-11-10 09:00:00'),

(1, 'Bí Quyết Chọn Phụ Kiện Váy Cưới Hoàn Hảo', 'bi-quyet-chon-phu-kien-vay-cuoi-hoan-hao', 
'Phụ kiện đóng vai trò quan trọng trong việc hoàn thiện tổng thể trang phục cô dâu.', 
'Phụ kiện váy cưới không chỉ là điểm nhấn mà còn giúp hoàn thiện phong cách tổng thể của cô dâu.

VÒNG HOA CÔ DÂU
- Phù hợp với phong cách bohemian, vintage
- Chọn hoa tươi hoặc hoa giả chất lượng cao
- Màu sắc hài hòa với váy và hoa cưới

KHĂN VÂN
- Khăn voan dài tạo vẻ lãng mạn, cổ điển
- Khăn voan ngắn phù hợp với váy hiện đại
- Có thể đính ren, đá để tăng sự sang trọng

TRANG SỨC
- Không nên đeo quá nhiều trang sức
- Chọn trang sức phù hợp với cổ váy
- Ưu tiên trang sức có ý nghĩa đặc biệt

GIÀY CƯỚI
- Chọn giày thoải mái, phù hợp với chiều cao váy
- Màu sắc hài hòa với váy
- Nên mang thử trước để làm quen

CLUTCH CÔ DÂU
- Nhỏ gọn, tinh tế
- Đựng vừa điện thoại, son, giấy thấm dầu
- Màu sắc trang nhã

Ghé thăm Váy Cưới Thiên Thần để được tư vấn trọn gói về váy và phụ kiện!', 
'assets/images/blog-4.jpg', 'published', '2024-11-08 16:00:00'),

(1, 'Lịch Trình Chuẩn Bị Đám Cưới 6 Tháng', 'lich-trinh-chuan-bi-dam-cuoi-6-thang', 
'Kế hoạch chi tiết từng bước để chuẩn bị đám cưới hoàn hảo trong 6 tháng.', 
'Chuẩn bị đám cưới là một quá trình đòi hỏi sự tỉ mỉ và chu đáo. Dưới đây là lịch trình chi tiết:

6 THÁNG TRƯỚC
- Xác định ngân sách tổng thể
- Chọn địa điểm tổ chức
- Lập danh sách khách mời sơ bộ
- Đặt nhiếp ảnh gia, quay phim
- Bắt đầu tìm váy cưới

5 THÁNG TRƯỚC
- Đặt tiệc cưới, nhà hàng
- Chọn thiệp cưới
- Đặt xe hoa, xe đón dâu
- Thử váy cưới, chọn mẫu phù hợp

4 THÁNG TRƯỚC
- Đặt hoa cưới, trang trí
- Chọn nhẫn cưới
- Lên kế hoạch honeymoon
- Đặt cọc váy cưới

3 THÁNG TRƯỚC
- Gửi thiệp mời
- Chọn trang phục phù dâu, phù rể
- Đặt bánh cưới
- Chọn MC, ban nhạc

2 THÁNG TRƯỚC
- Thử váy lần cuối, chỉnh sửa nếu cần
- Xác nhận số lượng khách
- Chuẩn bị quà cảm ơn
- Làm đẹp da, tóc

1 THÁNG TRƯỚC
- Hoàn tất mọi chi tiết
- Tập lễ cưới
- Chuẩn bị hành lý honeymoon
- Nghỉ ngơi, thư giãn

Đặt váy cưới sớm tại Váy Cưới Thiên Thần để có nhiều thời gian chỉnh sửa hoàn hảo!', 
'assets/images/blog-5.jpg', 'published', '2024-11-05 11:00:00'),

(1, 'Xu Hướng Trang Điểm Cô Dâu 2024', 'xu-huong-trang-diem-co-dau-2024', 
'Những phong cách trang điểm cô dâu đang được yêu thích nhất năm 2024.', 
'Trang điểm cô dâu năm 2024 hướng đến sự tự nhiên, tươi tắn nhưng vẫn nổi bật và sang trọng.

TRANG ĐIỂM TỰ NHIÊN (NO MAKEUP MAKEUP)
- Da căng mịn, tự nhiên
- Môi hồng nhẹ nhàng
- Mắt nhấn nhá tinh tế
- Phù hợp với váy cưới tối giản

TRANG ĐIỂM VINTAGE
- Môi đỏ cam cổ điển
- Mắt mèo quyến rũ
- Má hồng rõ nét
- Phù hợp với váy vintage, retro

TRANG ĐIỂM GLAM
- Da căng bóng
- Mắt khói sang trọng
- Môi nude hoặc hồng đất
- Phù hợp với váy cưới lộng lẫy

TRANG ĐIỂM BOHEMIAN
- Da rám nắng tự nhiên
- Mắt nâu đất
- Môi hồng cam
- Phù hợp với váy boho

MẸO TRANG ĐIỂM BỀN MÀU
- Dùng primer trước khi trang điểm
- Chọn sản phẩm lâu trôi
- Phủ phấn kỹ
- Mang theo son để chạm lại

Kết hợp trang điểm hoàn hảo với váy cưới đẹp từ Váy Cưới Thiên Thần!', 
'assets/images/blog-6.jpg', 'published', '2024-11-03 13:30:00'),

(1, 'Cách Bảo Quản Váy Cưới Sau Đám Cưới', 'cach-bao-quan-vay-cuoi-sau-dam-cuoi', 
'Hướng dẫn chi tiết cách bảo quản váy cưới để lưu giữ kỷ niệm đẹp lâu dài.', 
'Váy cưới là món đồ đặc biệt cần được bảo quản cẩn thận để giữ được vẻ đẹp ban đầu.

NGAY SAU ĐÁM CƯỚI
- Kiểm tra vết bẩn, vết ố
- Không để váy trong túi nilon kín
- Treo váy ở nơi thoáng mát

VỆ SINH VÁY CƯỚI
- Mang đến tiệm giặt ủi chuyên nghiệp
- Không tự giặt tại nhà
- Thông báo các vết bẩn đặc biệt
- Chọn dịch vụ giặt váy cưới chuyên dụng

BẢO QUẢN DÀI HẠN
- Dùng hộp đựng váy cưới chuyên dụng
- Bọc váy bằng giấy không axit
- Tránh ánh nắng trực tiếp
- Bảo quản ở nơi khô ráo, thoáng mát
- Kiểm tra định kỳ 6 tháng/lần

LƯU Ý QUAN TRỌNG
- Không dùng móc nhựa
- Không để váy trong tủ kín
- Tránh tiếp xúc với hóa chất
- Không gấp váy

NẾU THUÊ VÁY
- Trả váy đúng hạn
- Kiểm tra kỹ trước khi trả
- Thông báo nếu có hư hỏng
- Giữ hóa đơn thuê váy

Thuê váy cưới tại Váy Cưới Thiên Thần - chúng tôi có dịch vụ vệ sinh và bảo quản váy chuyên nghiệp!', 
'assets/images/blog-7.jpg', 'published', '2024-11-01 10:30:00'),

(1, 'Phong Cách Cưới Ngoài Trời - Xu Hướng Mới', 'phong-cach-cuoi-ngoai-troi-xu-huong-moi', 
'Đám cưới ngoài trời đang trở thành xu hướng được nhiều cặp đôi lựa chọn.', 
'Đám cưới ngoài trời mang đến không gian thoáng đãng, gần gũi thiên nhiên và nhiều góc chụp đẹp.

ƯU ĐIỂM
- Không gian rộng rãi, thoáng đãng
- Ánh sáng tự nhiên đẹp
- Chi phí linh hoạt
- Nhiều concept sáng tạo

ĐỊA ĐIỂM PHÙ HỢP
- Bãi biển
- Vườn cây
- Resort
- Sân vườn nhà hàng
- Đồi cỏ

CHỌN VÁY CƯỚI
- Chất liệu nhẹ, thoáng
- Tránh váy quá dài, lê đất
- Phong cách bohemian, romantic
- Màu sắc tươi sáng

CHUẨN BỊ
- Dự phòng thời tiết
- Chuẩn bị lều bạt
- Chọn giày phù hợp địa hình
- Trang điểm lâu trôi
- Chuẩn bị kem chống nắng

LƯU Ý
- Kiểm tra thời tiết trước 1 tuần
- Có phương án dự phòng
- Thông báo khách về địa điểm
- Chuẩn bị đầy đủ tiện nghi

Váy Cưới Thiên Thần có nhiều mẫu váy phù hợp với đám cưới ngoài trời!', 
'assets/images/blog-8.jpg', 'published', '2024-10-28 15:00:00'),

(1, 'Ngân Sách Đám Cưới - Phân Bổ Thông Minh', 'ngan-sach-dam-cuoi-phan-bo-thong-minh', 
'Hướng dẫn phân bổ ngân sách đám cưới hợp lý để có một đám cưới hoàn hảo.', 
'Lập ngân sách đám cưới chi tiết giúp bạn kiểm soát chi phí và tránh vượt quá khả năng tài chính.

PHÂN BỔ NGÂN SÁCH CHUẨN
- Địa điểm & Tiệc: 40-50%
- Trang phục cô dâu chú rể: 10-15%
- Nhiếp ảnh & Quay phim: 10-15%
- Trang trí & Hoa: 8-10%
- Thiệp mời & Quà cảm ơn: 3-5%
- Trang điểm & Làm tóc: 3-5%
- Nhạc & MC: 5-8%
- Dự phòng: 10%

TIẾT KIỆM THÔNG MINH
- Chọn ngày cưới không phải mùa cao điểm
- Thuê váy thay vì mua
- Tự làm một số chi tiết trang trí
- Chọn hoa theo mùa
- Giảm số lượng khách mời

ƯU TIÊN CHI TIÊU
1. Địa điểm và tiệc cưới
2. Nhiếp ảnh (kỷ niệm lâu dài)
3. Trang phục
4. Trang trí
5. Các hạng mục khác

MẸO HAY
- Lập bảng Excel chi tiết
- So sánh giá nhiều nhà cung cấp
- Đặt cọc sớm để có giá tốt
- Thương lượng gói combo
- Thanh toán đúng hạn

Thuê váy cưới tại Váy Cưới Thiên Thần giúp bạn tiết kiệm đáng kể chi phí trang phục!', 
'assets/images/blog-9.jpg', 'published', '2024-10-25 09:30:00');

-- Kiểm tra dữ liệu đã thêm
SELECT id, title, slug, status, published_at FROM tin_tuc_cuoi_hoi ORDER BY published_at DESC;
