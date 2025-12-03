<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/mail-helper.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// Lấy dữ liệu từ form
$ho_ten = sanitizeInput($_POST['ho_ten'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$mat_khau = $_POST['mat_khau'] ?? '';
$xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';
$so_dien_thoai = sanitizeInput($_POST['so_dien_thoai'] ?? '');
$dia_chi = sanitizeInput($_POST['dia_chi'] ?? '');

// Validate dữ liệu
$errors = [];

if (empty($ho_ten)) {
    $errors[] = "Vui lòng nhập họ tên";
} elseif (strlen($ho_ten) < 2) {
    $errors[] = "Họ tên phải có ít nhất 2 ký tự";
}

if (empty($email)) {
    $errors[] = "Vui lòng nhập email";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}

if (empty($mat_khau)) {
    $errors[] = "Vui lòng nhập mật khẩu";
} elseif (strlen($mat_khau) < 6) {
    $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
}

if ($mat_khau !== $xac_nhan_mat_khau) {
    $errors[] = "Mật khẩu xác nhận không khớp";
}

// Kiểm tra email đã tồn tại trong bảng nguoi_dung
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email đã được sử dụng";
    }
    $stmt->close();
}

// Xử lý upload avatar
$avt_path = null;
if (isset($_FILES['avt']) && $_FILES['avt']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/avatars/';
    
    // Tạo thư mục nếu chưa có
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['avt']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($file_extension, $allowed_extensions)) {
        if ($_FILES['avt']['size'] <= 5242880) { // 5MB
            $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['avt']['tmp_name'], $upload_path)) {
                $avt_path = $upload_path;
            } else {
                $errors[] = "Không thể upload ảnh đại diện";
            }
        } else {
            $errors[] = "Kích thước ảnh quá lớn (tối đa 5MB)";
        }
    } else {
        $errors[] = "Định dạng ảnh không hợp lệ (chỉ chấp nhận jpg, jpeg, png, gif)";
    }
}

// Nếu có lỗi, quay lại trang đăng ký
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = [
        'ho_ten' => $ho_ten,
        'email' => $email,
        'so_dien_thoai' => $so_dien_thoai,
        'dia_chi' => $dia_chi
    ];
    redirect('register.php');
}

// Tạo bảng OTP nếu chưa có
$conn->query("CREATE TABLE IF NOT EXISTS otp_verification (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    ho_ten VARCHAR(255) NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    so_dien_thoai VARCHAR(30) NULL,
    dia_chi TEXT NULL,
    avt VARCHAR(500) NULL,
    expires_at DATETIME NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Xóa các OTP cũ của email này
$stmt = $conn->prepare("DELETE FROM otp_verification WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// Xóa các OTP hết hạn
$conn->query("DELETE FROM otp_verification WHERE expires_at < NOW()");

// Tạo mã OTP mới
$otp_code = generateOTP(6);
$hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);

// Lưu OTP vào database - sử dụng DATE_ADD(NOW(), INTERVAL 5 MINUTE) để đảm bảo múi giờ nhất quán
try {
    $stmt = $conn->prepare("INSERT INTO otp_verification (email, otp_code, ho_ten, mat_khau, so_dien_thoai, dia_chi, avt, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
    $stmt->bind_param("sssssss", $email, $otp_code, $ho_ten, $hashed_password, $so_dien_thoai, $dia_chi, $avt_path);
    
    if (!$stmt->execute()) {
        throw new Exception("Không thể lưu mã OTP");
    }
    $stmt->close();
    
    // Gửi email OTP
    $mail_result = sendOTPEmail($email, $ho_ten, $otp_code);
    
    if ($mail_result['success']) {
        // Lưu email vào session để verify
        $_SESSION['otp_email'] = $email;
        $_SESSION['otp_sent_time'] = time();
        $_SESSION['success'] = "Mã OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.";
        redirect('verify-otp.php');
    } else {
        // Nếu gửi email thất bại, xóa OTP và báo lỗi
        $stmt = $conn->prepare("DELETE FROM otp_verification WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['errors'] = ["Không thể gửi email xác nhận. " . $mail_result['message']];
        $_SESSION['old_data'] = [
            'ho_ten' => $ho_ten,
            'email' => $email,
            'so_dien_thoai' => $so_dien_thoai,
            'dia_chi' => $dia_chi
        ];
        redirect('register.php');
    }
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
    $_SESSION['old_data'] = [
        'ho_ten' => $ho_ten,
        'email' => $email,
        'so_dien_thoai' => $so_dien_thoai,
        'dia_chi' => $dia_chi
    ];
    redirect('register.php');
}
?>
