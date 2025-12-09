<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/mail-helper.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('forgot-password.php');
}

// Kiểm tra kết nối database
if (!$conn) {
    $_SESSION['errors'] = ['Lỗi kết nối database. Vui lòng thử lại sau.'];
    redirect('forgot-password.php');
}

// Lấy email từ form
$email = sanitizeInput($_POST['email'] ?? '');

// Validate email
$errors = [];

if (empty($email)) {
    $errors[] = "Vui lòng nhập email";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}

// Kiểm tra email có tồn tại trong hệ thống không
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id, ho_ten FROM nguoi_dung WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $errors[] = "Email chưa được đăng ký trong hệ thống";
    } else {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
}

// Nếu có lỗi, quay lại
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_email'] = $email;
    redirect('forgot-password.php');
}

// Kiểm tra rate limit (không cho gửi quá nhanh - tối thiểu 60 giây)
if (isset($_SESSION['reset_otp_sent_time'])) {
    $time_diff = time() - $_SESSION['reset_otp_sent_time'];
    if ($time_diff < 60) {
        $_SESSION['errors'] = ['Vui lòng đợi ' . (60 - $time_diff) . ' giây trước khi gửi lại mã.'];
        redirect('forgot-password.php');
    }
}

// Tạo bảng password_reset nếu chưa có
$conn->query("CREATE TABLE IF NOT EXISTS password_reset (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    is_used TINYINT(1) DEFAULT 0,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Xóa các OTP cũ của email này
$stmt = $conn->prepare("DELETE FROM password_reset WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// Xóa các OTP hết hạn
$conn->query("DELETE FROM password_reset WHERE expires_at < NOW()");

// Tạo mã OTP mới
$otp_code = generateOTP(6);

// Lưu OTP vào database
try {
    $stmt = $conn->prepare("INSERT INTO password_reset (email, otp_code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $stmt->bind_param("ss", $email, $otp_code);
    
    if (!$stmt->execute()) {
        throw new Exception("Không thể lưu mã OTP");
    }
    $stmt->close();
    
    // Gửi email OTP
    $mail_result = sendPasswordResetEmail($email, $user['ho_ten'], $otp_code);
    
    if ($mail_result['success']) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp_sent_time'] = time();
        $_SESSION['success'] = "Mã khôi phục đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.";
        redirect('reset-password.php');
    } else {
        // Nếu gửi email thất bại, xóa OTP và báo lỗi
        $stmt = $conn->prepare("DELETE FROM password_reset WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['errors'] = ["Không thể gửi email. " . $mail_result['message']];
        $_SESSION['old_email'] = $email;
        redirect('forgot-password.php');
    }
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
    $_SESSION['old_email'] = $email;
    redirect('forgot-password.php');
}

/**
 * Gửi email khôi phục mật khẩu
 */
function sendPasswordResetEmail($to_email, $to_name, $otp_code) {
    // Lấy cấu hình SMTP từ .env
    $smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $smtp_port = (int)(getenv('SMTP_PORT') ?: 587);
    $smtp_user = getenv('SMTP_USER') ?: '';
    $smtp_pass = getenv('SMTP_PASS') ?: '';
    $smtp_from_email = getenv('SMTP_FROM_EMAIL') ?: $smtp_user;
    $smtp_from_name = getenv('SMTP_FROM_NAME') ?: 'Váy Cưới Thiên Thần';
    
    // Kiểm tra cấu hình SMTP
    if (empty($smtp_user) || empty($smtp_pass)) {
        return ['success' => false, 'message' => 'Chưa cấu hình SMTP trong file .env'];
    }
    
    // Tạo nội dung email
    $subject = 'Khôi phục mật khẩu - ' . $smtp_from_name;
    $body = getPasswordResetEmailTemplate($to_name, $otp_code);
    
    // Gửi email bằng SMTP
    return sendEmailSMTP($smtp_host, $smtp_port, $smtp_user, $smtp_pass, $smtp_from_email, $smtp_from_name, $to_email, $to_name, $subject, $body);
}

/**
 * Template HTML cho email khôi phục mật khẩu
 */
function getPasswordResetEmailTemplate($name, $otp_code) {
    $site_name = getenv('SITE_NAME') ?: 'Váy Cưới Thiên Thần';
    
    return '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 10px 10px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px;">' . htmlspecialchars($site_name) . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 20px; color: #333333; font-size: 24px;">Khôi phục mật khẩu</h2>
                            <p style="margin: 0 0 20px; color: #666666; font-size: 16px; line-height: 1.6;">
                                Xin chào <strong>' . htmlspecialchars($name) . '</strong>,
                            </p>
                            <p style="margin: 0 0 30px; color: #666666; font-size: 16px; line-height: 1.6;">
                                Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn. Vui lòng sử dụng mã OTP bên dưới để đặt lại mật khẩu:
                            </p>
                            <div style="text-align: center; margin: 30px 0;">
                                <div style="display: inline-block; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 20px 40px; border-radius: 10px;">
                                    <span style="font-size: 36px; font-weight: bold; color: #ffffff; letter-spacing: 8px;">' . $otp_code . '</span>
                                </div>
                            </div>
                            <p style="margin: 30px 0 0; color: #999999; font-size: 14px; text-align: center;">
                                Mã có hiệu lực trong <strong>10 phút</strong>
                            </p>
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">
                            <p style="margin: 0; color: #999999; font-size: 13px; line-height: 1.6;">
                                ⚠️ Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 40px; background-color: #f8f9fa; border-radius: 0 0 10px 10px; text-align: center;">
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                © ' . date('Y') . ' ' . htmlspecialchars($site_name) . '. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}
?>
