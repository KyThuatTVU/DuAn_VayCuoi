<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/mail-helper.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('forgot-password.php');
}

// Kiểm tra có email trong session không
if (!isset($_SESSION['reset_email'])) {
    $_SESSION['errors'] = ['Phiên khôi phục đã hết hạn. Vui lòng thử lại.'];
    redirect('forgot-password.php');
}

$email = $_SESSION['reset_email'];

// Kiểm tra kết nối database
if (!$conn) {
    $_SESSION['errors'] = ['Lỗi kết nối database. Vui lòng thử lại sau.'];
    redirect('forgot-password.php');
}

// Kiểm tra rate limit (không cho gửi lại quá nhanh - tối thiểu 60 giây)
if (isset($_SESSION['reset_otp_sent_time'])) {
    $time_diff = time() - $_SESSION['reset_otp_sent_time'];
    if ($time_diff < 60) {
        $_SESSION['reset_errors'] = ['Vui lòng đợi ' . (60 - $time_diff) . ' giây trước khi gửi lại mã.'];
        redirect('reset-password.php');
    }
}

// Lấy thông tin user
$stmt = $conn->prepare("SELECT id, ho_ten FROM nguoi_dung WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    unset($_SESSION['reset_email']);
    $_SESSION['errors'] = ['Tài khoản không tồn tại. Vui lòng thử lại.'];
    redirect('forgot-password.php');
}

// Kiểm tra số lần gửi lại (tối đa 5 lần trong 1 giờ)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM password_reset WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$stmt->bind_param("s", $email);
$stmt->execute();
$count_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($count_result['count'] >= 5) {
    $_SESSION['reset_errors'] = ['Bạn đã yêu cầu gửi mã quá nhiều lần. Vui lòng thử lại sau 1 giờ.'];
    redirect('reset-password.php');
}

// Xóa OTP cũ
$stmt = $conn->prepare("DELETE FROM password_reset WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// Tạo mã OTP mới
$otp_code = generateOTP(6);

// Lưu OTP mới vào database
try {
    $stmt = $conn->prepare("INSERT INTO password_reset (email, otp_code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $stmt->bind_param("ss", $email, $otp_code);
    
    if (!$stmt->execute()) {
        throw new Exception("Không thể lưu mã OTP mới");
    }
    $stmt->close();
    
    // Gửi email OTP mới
    $mail_result = sendPasswordResetEmail($email, $user['ho_ten'], $otp_code);
    
    if ($mail_result['success']) {
        $_SESSION['reset_otp_sent_time'] = time();
        $_SESSION['success'] = "Mã OTP mới đã được gửi đến email của bạn.";
    } else {
        $_SESSION['reset_errors'] = ["Không thể gửi email. " . $mail_result['message']];
    }
    
    redirect('reset-password.php');
} catch (Exception $e) {
    $_SESSION['reset_errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('reset-password.php');
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
