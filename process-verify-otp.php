<?php
session_start();
require_once 'includes/config.php';

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

// Lấy mã OTP từ form
$otp_code = '';
if (isset($_POST['otp_code']) && !empty($_POST['otp_code'])) {
    $otp_code = trim($_POST['otp_code']);
} else {
    // Ghép từ các ô input riêng lẻ
    for ($i = 1; $i <= 6; $i++) {
        $otp_code .= $_POST['otp' . $i] ?? '';
    }
}

// Validate OTP
if (empty($otp_code) || strlen($otp_code) !== 6 || !ctype_digit($otp_code)) {
    $_SESSION['otp_errors'] = ['Vui lòng nhập đầy đủ mã OTP 6 số'];
    redirect('verify-otp.php');
}

// Lấy thông tin OTP từ database
$stmt = $conn->prepare("SELECT * FROM otp_verification WHERE email = ? AND is_verified = 0 ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$otp_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Kiểm tra OTP có tồn tại không
if (!$otp_data) {
    unset($_SESSION['otp_email']);
    $_SESSION['errors'] = ['Không tìm thấy yêu cầu đăng ký. Vui lòng đăng ký lại.'];
    redirect('register.php');
}

// Kiểm tra OTP đã hết hạn chưa
if (strtotime($otp_data['expires_at']) < time()) {
    // Xóa OTP hết hạn
    $stmt = $conn->prepare("DELETE FROM otp_verification WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    unset($_SESSION['otp_email']);
    $_SESSION['errors'] = ['Mã OTP đã hết hạn. Vui lòng đăng ký lại.'];
    redirect('register.php');
}

// Kiểm tra số lần nhập sai (tối đa 5 lần)
if ($otp_data['attempts'] >= 5) {
    // Xóa OTP
    $stmt = $conn->prepare("DELETE FROM otp_verification WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    unset($_SESSION['otp_email']);
    $_SESSION['errors'] = ['Bạn đã nhập sai quá nhiều lần. Vui lòng đăng ký lại.'];
    redirect('register.php');
}

// Kiểm tra mã OTP
if ($otp_code !== $otp_data['otp_code']) {
    // Tăng số lần nhập sai
    $stmt = $conn->prepare("UPDATE otp_verification SET attempts = attempts + 1 WHERE id = ?");
    $stmt->bind_param("i", $otp_data['id']);
    $stmt->execute();
    $stmt->close();
    
    $remaining_attempts = 5 - ($otp_data['attempts'] + 1);
    $_SESSION['otp_errors'] = ["Mã OTP không đúng. Còn $remaining_attempts lần thử."];
    redirect('verify-otp.php');
}

// OTP đúng - Tạo tài khoản người dùng
try {
    // Kiểm tra email một lần nữa (tránh race condition)
    $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Email đã tồn tại
        $stmt = $conn->prepare("DELETE FROM otp_verification WHERE id = ?");
        $stmt->bind_param("i", $otp_data['id']);
        $stmt->execute();
        $stmt->close();
        
        unset($_SESSION['otp_email']);
        $_SESSION['errors'] = ['Email đã được sử dụng bởi tài khoản khác.'];
        redirect('register.php');
    }
    $stmt->close();
    
    // Thêm người dùng vào database
    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi, avt) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", 
        $otp_data['ho_ten'], 
        $otp_data['email'], 
        $otp_data['mat_khau'], 
        $otp_data['so_dien_thoai'], 
        $otp_data['dia_chi'], 
        $otp_data['avt']
    );
    
    if ($stmt->execute()) {
        // Đánh dấu OTP đã xác nhận
        $stmt2 = $conn->prepare("UPDATE otp_verification SET is_verified = 1 WHERE id = ?");
        $stmt2->bind_param("i", $otp_data['id']);
        $stmt2->execute();
        $stmt2->close();
        
        // Xóa các OTP cũ của email này
        $stmt3 = $conn->prepare("DELETE FROM otp_verification WHERE email = ?");
        $stmt3->bind_param("s", $email);
        $stmt3->execute();
        $stmt3->close();
        
        // Xóa session OTP
        unset($_SESSION['otp_email']);
        unset($_SESSION['otp_sent_time']);
        
        $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
        redirect('login.php');
    } else {
        throw new Exception("Không thể tạo tài khoản");
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['otp_errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('verify-otp.php');
}
?>
