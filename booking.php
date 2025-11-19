<?php
session_start();
require_once 'includes/config.php';
$page_title = 'Đặt Lịch Thử Váy';
require_once 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Trang Chủ</a> / <span>Đặt Lịch Thử Váy</span>
    </div>
</div>

<section class="booking-section">
    <div class="container">
        <div class="section-header">
            <h1>Đặt Lịch Thử Váy</h1>
            <p>Vui lòng điền thông tin để đặt lịch hẹn thử váy tại showroom</p>
        </div>

        <div class="booking-layout">
            <div class="booking-form-container">
                <form class="booking-form" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và Tên <span class="required">*</span></label>
                            <input type="text" name="name" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="form-group">
                            <label>Số Điện Thoại <span class="required">*</span></label>
                            <input type="tel" name="phone" required placeholder="0901234567">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label>Váy Muốn Thử</label>
                            <select name="dress_id">
                                <option value="">-- Chọn váy --</option>
                                <option>Váy Công Chúa Bồng Bềnh</option>
                                <option>Váy Đuôi Cá Quyến Rũ</option>
                                <option>Váy Chữ A Tối Giản</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Ngày Hẹn <span class="required">*</span></label>
                            <input type="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label>Giờ Hẹn <span class="required">*</span></label>
                            <select name="time" required>
                                <option value="">-- Chọn giờ --</option>
                                <option>09:00</option>
                                <option>10:00</option>
                                <option>11:00</option>
                                <option>14:00</option>
                                <option>15:00</option>
                                <option>16:00</option>
                                <option>17:00</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Số Người Đi Cùng</label>
                        <input type="number" name="persons" value="1" min="1" max="5">
                    </div>

                    <div class="form-group">
                        <label>Ghi Chú</label>
                        <textarea name="note" rows="4" placeholder="Yêu cầu đặc biệt hoặc ghi chú thêm..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large btn-block">Đặt Lịch Ngay</button>
                </form>
            </div>

            <div class="booking-info">
                <div class="info-box">
                    <h3>Thông Tin Showroom</h3>
                    <div class="info-item">
                        <i class="icon-location"></i>
                        <div>
                            <strong>Địa Chỉ</strong>
                            <p>123 Đường Nguyễn Huệ, Quận 1, TP.HCM</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="icon-phone"></i>
                        <div>
                            <strong>Hotline</strong>
                            <p>0901 234 567</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="icon-clock"></i>
                        <div>
                            <strong>Giờ Làm Việc</strong>
                            <p>Thứ 2 - Chủ Nhật: 8:00 - 20:00</p>
                        </div>
                    </div>
                </div>

                <div class="info-box">
                    <h3>Lưu Ý Khi Thử Váy</h3>
                    <ul class="note-list">
                        <li>Đến đúng giờ hẹn để được phục vụ tốt nhất</li>
                        <li>Mang theo giày cao gót để thử váy chuẩn nhất</li>
                        <li>Có thể mang theo người thân để tư vấn</li>
                        <li>Thời gian thử váy: 60-90 phút</li>
                        <li>Miễn phí hoàn toàn, không mất phí</li>
                    </ul>
                </div>

                <div class="info-box highlight">
                    <h3>Ưu Đãi Đặc Biệt</h3>
                    <p>Đặt lịch hôm nay, nhận ngay voucher giảm 10% khi thuê váy!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.booking-section {
    padding: 60px 0;
}

.booking-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    margin-top: 40px;
}

.booking-form-container {
    background: var(--white);
    padding: 40px;
    border-radius: 8px;
    box-shadow: var(--shadow);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.required {
    color: var(--danger);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 15px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.info-box {
    background: var(--white);
    padding: 30px;
    border-radius: 8px;
    box-shadow: var(--shadow);
    margin-bottom: 20px;
}

.info-box h3 {
    font-size: 20px;
    margin-bottom: 20px;
    color: var(--text-dark);
}

.info-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.info-item i {
    font-size: 24px;
    color: var(--primary-color);
    margin-top: 5px;
}

.info-item strong {
    display: block;
    margin-bottom: 5px;
}

.info-item p {
    color: var(--text-light);
}

.note-list {
    list-style: none;
}

.note-list li {
    padding-left: 25px;
    margin-bottom: 12px;
    position: relative;
    color: var(--text-light);
}

.note-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: var(--success);
    font-weight: bold;
}

.info-box.highlight {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: var(--white);
}

.info-box.highlight h3 {
    color: var(--white);
}

@media (max-width: 768px) {
    .booking-layout {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .booking-form-container {
        padding: 25px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
