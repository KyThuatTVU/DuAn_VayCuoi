<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/notification-helper.php';

// Số lần đăng nhập sai tối đa trước khi khóa tài khoản
define('MAX_LOGIN_ATTEMPTS', 5);

// Kiểm tra kết nối database
if (!$conn) {
    die("Lỗi: Không thể kết nối database");
}

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// Lấy dữ liệu từ form
$email = sanitizeInput($_POST['email'] ?? '');
$mat_khau = $_POST['mat_khau'] ?? '';
$remember = isset($_POST['remember']);

// Validate dữ liệu
$errors = [];

if (empty($email)) {
    $errors[] = "Vui lòng nhập email";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}

if (empty($mat_khau)) {
    $errors[] = "Vui lòng nhập mật khẩu";
}

// Nếu có lỗi, quay lại trang đăng nhập
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_email'] = $email;
    redirect('login.php');
}

/**
 * Ghi log đăng nhập
 */
function logLoginAttempt($conn, $user_id, $email, $status, $failed_reason = null) {
    // Kiểm tra bảng có tồn tại không
    $check = $conn->query("SHOW TABLES LIKE 'login_logs'");
    if (!$check || $check->num_rows === 0) {
        return false;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO login_logs (nguoi_dung_id, email, ip_address, user_agent, status, failed_reason) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $email, $ip, $user_agent, $status, $failed_reason);
    return $stmt->execute();
}

/**
 * Tăng số lần đăng nhập thất bại
 */
function incrementLoginAttempts($conn, $user_id) {
    $stmt = $conn->prepare("UPDATE nguoi_dung SET login_attempts = COALESCE(login_attempts, 0) + 1, last_failed_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

/**
 * Reset số lần đăng nhập thất bại
 */
function resetLoginAttempts($conn, $user_id) {
    $stmt = $conn->prepare("UPDATE nguoi_dung SET login_attempts = 0, last_failed_login = NULL WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

/**
 * Khóa tài khoản
 */
function lockAccount($conn, $user_id, $reason) {
    $stmt = $conn->prepare("UPDATE nguoi_dung SET status = 'locked', locked_at = NOW(), locked_reason = ?, login_attempts = 0 WHERE id = ?");
    $stmt->bind_param("si", $reason, $user_id);
    return $stmt->execute();
}

/**
 * Tạo thông báo cho admin khi tài khoản bị khóa
 */
function notifyAdminAccountLockedLogin($conn, $user_id, $email, $ho_ten) {
    $reason = "Đăng nhập sai mật khẩu " . MAX_LOGIN_ATTEMPTS . " lần liên tiếp";
    return notifyAccountLocked($conn, $user_id, $email, $reason);
}

/**
 * Lấy số lần đăng nhập thất bại hiện tại
 */
function getLoginAttempts($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COALESCE(login_attempts, 0) as attempts FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return (int)$row['attempts'];
    }
    return 0;
}

// Kiểm tra thông tin đăng nhập
try {
    // Kiểm tra xem các cột mới có tồn tại không (tương thích ngược)
    $columns_exist = true;
    $check_columns = $conn->query("SHOW COLUMNS FROM nguoi_dung LIKE 'login_attempts'");
    if (!$check_columns || $check_columns->num_rows === 0) {
        $columns_exist = false;
    }
    
    // Query phù hợp với cấu trúc bảng
    if ($columns_exist) {
        $stmt = $conn->prepare("SELECT id, ho_ten, email, mat_khau, avt, COALESCE(status, 'active') as status, COALESCE(login_attempts, 0) as login_attempts, locked_at, locked_reason FROM nguoi_dung WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT id, ho_ten, email, mat_khau, avt, COALESCE(status, 'active') as status, 0 as login_attempts, NULL as locked_at, NULL as locked_reason FROM nguoi_dung WHERE email = ?");
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Email không tồn tại - ghi log
        logLoginAttempt($conn, null, $email, 'failed', 'Email không tồn tại');
        
        $_SESSION['errors'] = ["Email hoặc mật khẩu không đúng"];
        $_SESSION['old_email'] = $email;
        $stmt->close();
        redirect('login.php');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Kiểm tra trạng thái tài khoản trước
    if ($user['status'] === 'locked') {
        logLoginAttempt($conn, $user['id'], $email, 'locked', 'Tài khoản đang bị khóa');
        
        $locked_time = $user['locked_at'] ? date('d/m/Y H:i', strtotime($user['locked_at'])) : '';
        $locked_msg = "Tài khoản của bạn đã bị khóa";
        if ($user['locked_reason']) {
            $locked_msg .= " do: " . $user['locked_reason'];
        }
        if ($locked_time) {
            $locked_msg .= " (từ $locked_time)";
        }
        $locked_msg .= ". Vui lòng liên hệ quản trị viên qua hotline hoặc email để được hỗ trợ mở khóa.";
        
        $_SESSION['errors'] = [$locked_msg];
        $_SESSION['old_email'] = $email;
        redirect('login.php');
    }
    
    if ($user['status'] === 'disabled') {
        logLoginAttempt($conn, $user['id'], $email, 'failed', 'Tài khoản bị vô hiệu hóa');
        
        $_SESSION['errors'] = ["Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên để biết thêm chi tiết."];
        $_SESSION['old_email'] = $email;
        redirect('login.php');
    }
    
    // Kiểm tra mật khẩu
    if (!password_verify($mat_khau, $user['mat_khau'])) {
        // Mật khẩu sai
        if ($columns_exist) {
            incrementLoginAttempts($conn, $user['id']);
            $current_attempts = getLoginAttempts($conn, $user['id']);
            $remaining_attempts = MAX_LOGIN_ATTEMPTS - $current_attempts;
            
            // Log đăng nhập thất bại
            logLoginAttempt($conn, $user['id'], $email, 'failed', "Mật khẩu sai (lần $current_attempts/" . MAX_LOGIN_ATTEMPTS . ")");
            
            // Kiểm tra nếu đã đạt giới hạn
            if ($current_attempts >= MAX_LOGIN_ATTEMPTS) {
                // Khóa tài khoản
                $lock_reason = "Đăng nhập sai mật khẩu " . MAX_LOGIN_ATTEMPTS . " lần liên tiếp";
                lockAccount($conn, $user['id'], $lock_reason);
                
                // Thông báo cho admin
                notifyAdminAccountLockedLogin($conn, $user['id'], $user['email'], $user['ho_ten']);
                
                // Log khóa tài khoản
                logLoginAttempt($conn, $user['id'], $email, 'locked', $lock_reason);
                
                $_SESSION['errors'] = [
                    "Tài khoản của bạn đã bị khóa do nhập sai mật khẩu " . MAX_LOGIN_ATTEMPTS . " lần liên tiếp.",
                    "Vui lòng liên hệ quản trị viên qua hotline hoặc email để được hỗ trợ mở khóa."
                ];
                $_SESSION['old_email'] = $email;
                redirect('login.php');
            } else {
                // Còn cơ hội thử lại
                $error_msg = "Email hoặc mật khẩu không đúng.";
                if ($remaining_attempts <= 3) {
                    $error_msg .= " Bạn còn $remaining_attempts lần thử trước khi tài khoản bị khóa.";
                }
                $_SESSION['errors'] = [$error_msg];
                $_SESSION['old_email'] = $email;
                redirect('login.php');
            }
        } else {
            // Không có cột login_attempts, chỉ báo lỗi thông thường
            $_SESSION['errors'] = ["Email hoặc mật khẩu không đúng"];
            $_SESSION['old_email'] = $email;
            redirect('login.php');
        }
    }
    
    // Đăng nhập thành công - reset số lần thất bại
    if ($columns_exist) {
        resetLoginAttempts($conn, $user['id']);
    }
    
    // Log đăng nhập thành công
    logLoginAttempt($conn, $user['id'], $email, 'success', null);
    
    // Đăng nhập thành công
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['ho_ten'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_avatar'] = $user['avt'] ?? '';
    $_SESSION['logged_in'] = true;
    
    // Xử lý remember me
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
    }
    
    $_SESSION['success'] = "Đăng nhập thành công! Chào mừng " . $user['ho_ten'];
    
    // Redirect về trang trước đó hoặc trang chủ
    $redirect_url = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);
    redirect($redirect_url);
    
} catch (Exception $e) {
    $_SESSION['errors'] = ["Lỗi: " . $e->getMessage()];
    redirect('login.php');
}
?>
