<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Liên Hệ';
require_once 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Trang Chủ</a> / <span>Liên Hệ</span>
    </div>
</div>

<section class="contact-section">
    <div class="container">
        <div class="section-header">
            <h1>Liên Hệ Với Chúng Tôi</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
        </div>

        <div class="contact-layout">
            <div class="contact-form-container">
                <h2>Gửi Tin Nhắn</h2>
                <form class="contact-form" method="POST">
                    <div class="form-group">
                        <label>Họ và Tên <span class="required">*</span></label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Số Điện Thoại</label>
                            <input type="tel" name="phone">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Chủ Đề <span class="required">*</span></label>
                        <select name="subject" required>
                            <option value="">-- Chọn chủ đề --</option>
                            <option>Hỏi về giá thuê váy</option>
                            <option>Đặt lịch thử váy</option>
                            <option>Khiếu nại dịch vụ</option>
                            <option>Góp ý, đề xuất</option>
                            <option>Khác</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nội Dung <span class="required">*</span></label>
                        <textarea name="message" rows="6" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large btn-block">Gửi Tin Nhắn</button>
                </form>
            </div>

            <div class="contact-info-container">
                <div class="contact-info-box">
                    <div class="contact-icon">
                        <i class="icon-location"></i>
                    </div>
                    <h3>Địa Chỉ Showroom</h3>
                    <p>123 Đường Nguyễn Huệ<br>Quận 1, TP. Hồ Chí Minh</p>
                </div>

                <div class="contact-info-box">
                    <div class="contact-icon">
                        <i class="icon-phone"></i>
                    </div>
                    <h3>Số Điện Thoại</h3>
                    <p>Hotline: 0901 234 567<br>Tel: (028) 3822 xxxx</p>
                </div>

                <div class="contact-info-box">
                    <div class="contact-icon">
                        <i class="icon-email"></i>
                    </div>
                    <h3>Email</h3>
                    <p>contact@vaycuoi.com<br>support@vaycuoi.com</p>
                </div>

                <div class="contact-info-box">
                    <div class="contact-icon">
                        <i class="icon-clock"></i>
                    </div>
                    <h3>Giờ Làm Việc</h3>
                    <p>Thứ 2 - Chủ Nhật<br>8:00 AM - 8:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4967!2d106.7!3d10.8!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ4JzAwLjAiTiAxMDbCsDQyJzAwLjAiRQ!5e0!3m2!1svi!2s!4v1234567890" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<style>
.contact-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    margin: 40px 0;
}

.contact-form-container {
    background: var(--white);
    padding: 40px;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

.contact-form-container h2 {
    font-size: 28px;
    margin-bottom: 30px;
}

.contact-info-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.contact-info-box {
    background: var(--white);
    padding: 30px;
    border-radius: 8px;
    box-shadow: var(--shadow);
    text-align: center;
    transition: all 0.3s;
}

.contact-info-box:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.contact-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    background: var(--secondary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: var(--primary-color);
}

.contact-info-box h3 {
    font-size: 18px;
    margin-bottom: 10px;
}

.contact-info-box p {
    color: var(--text-light);
    line-height: 1.8;
}

.map-container {
    margin-top: 40px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow);
}

@media (max-width: 768px) {
    .contact-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
