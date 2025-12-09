<?php
session_start();
require_once 'includes/config.php';

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

// Lấy mã OTP từ form
$otp_code = '';
for ($i = 1; $i <= 6; $i++) {
    $otp_code .= $_POST['otp' . $i] ?? '';
}

// Lấy mật khẩu mới
$mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
$xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';

// Validate
$errors = [];

if (empty($otp_code) || strlen($otp_code) !== 6 || !ctype_digit($otp_code)) {
    $errors[] = 'Vui lòng nhập đầy đủ mã OTP 6 số';
}

if (empty($mat_khau_moi)) {
    $errors[] = 'Vui lòng nhập mật khẩu mới';
} elseif (strlen($mat_khau_moi) < 6) {
    $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
}

if ($mat_khau_moi !== $xac_nhan_mat_khau) {
    $errors[] = 'Mật khẩu xác nhận không khớp';
}

if (!empty($errors)) {
    $_SESSION['reset_errors'] = $errors;
    redirect('reset-password.php');
}

// Lấy thông tin OTP từ database
$stmt = $conn->prepare("SELECT * FROM password_reset WHERE email = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$otp_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Kiểm tra OTP có tồn tại không
if (!$otp_data) {
    unset($_SESSION['reset_email']);
    $_SESSION['errors'] = ['Không tìm thấy yêu cầu khôi phục. Vui lòng thử lại.'];
    redirect('forgot-password.php');
}

// Kiểm tra OTP đã hết hạn chưa
if (strtotime($otp_data['expires_at']) < time()) {
    $stmt = $conn->prepare("DELETE FROM password_reset WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    unset($_SESSION['reset_email']);
    $_SESSION['errors'] = ['Mã OTP đã hết hạn. Vui lòng thử lại.'];
    redirect('forgot-password.php');
}

// Kiểm tra số lần nhập sai (tối đa 5 lần)
if ($otp_data['attempts'] >= 5) {
    $stmt = $conn->prepare("DELETE FROM password_reset WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    unset($_SESSION['reset_email']);
    $_SESSION['errors'] = ['Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại.'];
    redirect('forgot-password.php');
}

// Kiểm tra mã OTP
if ($otp_code !== $otp_data['otp_code']) {
    // Tăng số lần nhập sai
    $stmt = $conn->prepare("UPDATE password_reset SET attempts = attempts + 1 WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    $remaining_attempts = 5 - ($otp_data['attempts'] + 1);
    $_SESSION['reset_errors'] = ["Mã OTP không đúng. Còn $remaining_attempts lần thử."];
    redirect('reset-password.php');
}

// OTP đúng - Cập nhật mật khẩu
try {
    // Hash mật khẩu mới
    $hashed_password = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
    
    // Cập nhật mật khẩu trong database
    $stmt = $conn->prepare("UPDATE nguoi_dung SET mat_khau = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    
    if (!$stmt->execute()) {
        throw new Exception("Không thể cập nhật mật khẩu");
    }
    $stmt->close();
    
    // Đánh dấu OTP đã sử dụng
    $stmt = $conn->prepare("UPDATE password_reset SET is_used = 1 WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    // Xóa session reset
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_otp_sent_time']);
    
    // Thông báo thành công
    $_SESSION['success'] = 'Mật khẩu đã được đổi thành công! Vui lòng đăng nhập.';
    redirect('login.php');
    
} catch (Exception $e) {
    $_SESSION['reset_errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('reset-password.php');
}
?>
