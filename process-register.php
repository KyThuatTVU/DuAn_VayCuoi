<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// Lấy dữ liệu từ form
$ho_ten = sanitizeInput($_POST['ho_ten'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$mat_khau = $_POST['mat_khau'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$so_dien_thoai = sanitizeInput($_POST['so_dien_thoai'] ?? '');
$dia_chi = sanitizeInput($_POST['dia_chi'] ?? '');

// Validate dữ liệu
$errors = [];

if (empty($ho_ten)) {
    $errors[] = "Vui lòng nhập họ tên";
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

if ($mat_khau !== $confirm_password) {
    $errors[] = "Mật khẩu xác nhận không khớp";
}

// Kiểm tra email đã tồn tại
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
        $errors[] = "Định dạng ảnh không hợp lệ";
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

// Mã hóa mật khẩu
$hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);

// Thêm người dùng vào database
try {
    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi, avt) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $ho_ten, $email, $hashed_password, $so_dien_thoai, $dia_chi, $avt_path);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
        $stmt->close();
        redirect('login.php');
    } else {
        throw new Exception("Không thể tạo tài khoản");
    }
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('register.php');
}
?>
