<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('account.php');
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Lấy dữ liệu từ form
$ho_ten = sanitizeInput($_POST['ho_ten'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$so_dien_thoai = sanitizeInput($_POST['so_dien_thoai'] ?? '');
$dia_chi = sanitizeInput($_POST['dia_chi'] ?? '');

// Validate
if (empty($ho_ten)) {
    $errors[] = "Vui lòng nhập họ tên";
}

if (empty($email)) {
    $errors[] = "Vui lòng nhập email";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}

// Kiểm tra email đã tồn tại (trừ email của chính user)
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email đã được sử dụng bởi tài khoản khác";
    }
    $stmt->close();
}

// Xử lý upload avatar
$avt_path = null;
if (isset($_FILES['avt']) && $_FILES['avt']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/avatars/';
    
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
                // Xóa ảnh cũ nếu có
                $stmt = $conn->prepare("SELECT avt FROM nguoi_dung WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_user = $result->fetch_assoc();
                $stmt->close();
                
                if (!empty($old_user['avt']) && file_exists($old_user['avt'])) {
                    unlink($old_user['avt']);
                }
                
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

// Nếu có lỗi
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    redirect('account.php');
}

// Cập nhật thông tin
try {
    if ($avt_path) {
        // Cập nhật cả avatar
        $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten = ?, email = ?, so_dien_thoai = ?, dia_chi = ?, avt = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $ho_ten, $email, $so_dien_thoai, $dia_chi, $avt_path, $user_id);
    } else {
        // Chỉ cập nhật thông tin
        $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten = ?, email = ?, so_dien_thoai = ?, dia_chi = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $ho_ten, $email, $so_dien_thoai, $dia_chi, $user_id);
    }
    
    if ($stmt->execute()) {
        // Cập nhật session
        $_SESSION['user_name'] = $ho_ten;
        $_SESSION['user_email'] = $email;
        if ($avt_path) {
            $_SESSION['user_avatar'] = $avt_path;
        }
        
        $_SESSION['success'] = "Cập nhật thông tin thành công!";
    } else {
        throw new Exception("Không thể cập nhật thông tin");
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
}

redirect('account.php');
?>
