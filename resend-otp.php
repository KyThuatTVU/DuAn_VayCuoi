<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/mail-helper.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// Kiểm tra có email trong session không
if (!isset($_SESSION['otp_email'])) {
    $_SESSION['errors'] = ['Phiên đăng ký đã hết hạn. Vui lòng đăng ký lại.'];
    redirect('register.php');
}

$email = $_SESSION['otp_email'];

// Kiểm tra rate limit (không cho gửi lại quá nhanh - tối thiểu 60 giây)
if (isset($_SESSION['otp_sent_time'])) {
    $time_diff = time() - $_SESSION['otp_sent_time'];
    if ($time_diff < 60) {
        $_SESSION['otp_errors'] = ['Vui lòng đợi ' . (60 - $time_diff) . ' giây trước khi gửi lại mã.'];
        redirect('verify-otp.php');
    }
}

// Lấy thông tin đăng ký từ OTP cũ
$stmt = $conn->prepare("SELECT * FROM otp_verification WHERE email = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$otp_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$otp_data) {
    unset($_SESSION['otp_email']);
    $_SESSION['errors'] = ['Không tìm thấy thông tin đăng ký. Vui lòng đăng ký lại.'];
    redirect('register.php');
}

// Kiểm tra số lần gửi lại (tối đa 5 lần)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM otp_verification WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$stmt->bind_param("s", $email);
$stmt->execute();
$count_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($count_result['count'] >= 5) {
    $_SESSION['otp_errors'] = ['Bạn đã yêu cầu gửi mã quá nhiều lần. Vui lòng thử lại sau 1 giờ.'];
    redirect('verify-otp.php');
}

// Xóa OTP cũ
$stmt = $conn->prepare("DELETE FROM otp_verification WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// Tạo mã OTP mới
$otp_code = generateOTP(6);

// Lưu OTP mới vào database - sử dụng DATE_ADD(NOW(), INTERVAL 5 MINUTE) để đảm bảo múi giờ nhất quán
try {
    $stmt = $conn->prepare("INSERT INTO otp_verification (email, otp_code, ho_ten, mat_khau, so_dien_thoai, dia_chi, avt, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
    $stmt->bind_param("sssssss", 
        $email, 
        $otp_code, 
        $otp_data['ho_ten'], 
        $otp_data['mat_khau'], 
        $otp_data['so_dien_thoai'], 
        $otp_data['dia_chi'], 
        $otp_data['avt']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Không thể lưu mã OTP mới");
    }
    $stmt->close();
    
    // Gửi email OTP mới
    $mail_result = sendOTPEmail($email, $otp_data['ho_ten'], $otp_code);
    
    if ($mail_result['success']) {
        $_SESSION['otp_sent_time'] = time();
        $_SESSION['success'] = "Mã OTP mới đã được gửi đến email của bạn.";
    } else {
        $_SESSION['otp_errors'] = ["Không thể gửi email. " . $mail_result['message']];
    }
    
    redirect('verify-otp.php');
} catch (Exception $e) {
    $_SESSION['otp_errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('verify-otp.php');
}
?>
