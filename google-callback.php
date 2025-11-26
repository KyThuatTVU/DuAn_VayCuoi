<?php
session_start();
require_once 'includes/config.php';

// DEBUG: Ghi log để kiểm tra
$debug_log = "=== " . date('Y-m-d H:i:s') . " ===\n";
$debug_log .= "GET: " . print_r($_GET, true) . "\n";
$debug_log .= "SESSION before: " . print_r($_SESSION, true) . "\n";
file_put_contents('debug-google-log.txt', $debug_log, FILE_APPEND);

// Kiểm tra loại đăng nhập (admin hay user)
// Ưu tiên state từ URL (vì session có thể bị mất khi redirect qua Google)
$state = $_GET['state'] ?? '';
$is_admin_login = ($state === 'admin_login');

// Fallback: kiểm tra session nếu state không có
if (!$is_admin_login && isset($_SESSION['google_login_type'])) {
    $is_admin_login = ($_SESSION['google_login_type'] === 'admin');
}

// DEBUG: Ghi thêm log
file_put_contents('debug-google-log.txt', "state: $state, is_admin_login: " . ($is_admin_login ? 'true' : 'false') . "\n\n", FILE_APPEND);

// Xóa session login type sau khi sử dụng
unset($_SESSION['google_login_type']);

// Kiểm tra có code từ Google không
if (!isset($_GET['code'])) {
    if ($is_admin_login) {
        $_SESSION['admin_errors'] = ['Đăng nhập Google thất bại'];
        redirect('admin-login.php');
    }
    $_SESSION['errors'] = ['Đăng nhập Google thất bại'];
    redirect('login.php');
}

$code = $_GET['code'];

// Lấy thông tin từ .env
$client_id = getenv('GOOGLE_CLIENT_ID');
$client_secret = getenv('GOOGLE_CLIENT_SECRET');
$redirect_uri = getenv('GOOGLE_REDIRECT_URI');

// Đổi code lấy access token
$token_url = 'https://oauth2.googleapis.com/token';
$token_data = [
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$token_info = json_decode($response, true);

if (!isset($token_info['access_token'])) {
    if ($is_admin_login) {
        $_SESSION['admin_errors'] = ['Không thể lấy thông tin từ Google'];
        redirect('admin-login.php');
    }
    $_SESSION['errors'] = ['Không thể lấy thông tin từ Google'];
    redirect('login.php');
}

$access_token = $token_info['access_token'];

// Lấy thông tin user từ Google
$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $access_token;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_info_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user_info_response = curl_exec($ch);
curl_close($ch);

$user_info = json_decode($user_info_response, true);

if (!isset($user_info['email'])) {
    if ($is_admin_login) {
        $_SESSION['admin_errors'] = ['Không thể lấy thông tin email từ Google'];
        redirect('admin-login.php');
    }
    $_SESSION['errors'] = ['Không thể lấy thông tin email từ Google'];
    redirect('login.php');
}

// Thông tin từ Google
$google_id = $user_info['id'];
$email = $user_info['email'];
$ho_ten = $user_info['name'] ?? '';
$avatar_url = $user_info['picture'] ?? '';

// ========== XỬ LÝ ĐĂNG NHẬP ADMIN ==========
if ($is_admin_login) {
    // Kiểm tra email có trong bảng admin không
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Admin tồn tại - đăng nhập thành công
        $admin = $result->fetch_assoc();
        
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_avatar'] = $avatar_url; // Lưu avatar từ Google
        $_SESSION['admin_logged_in'] = true;
        
        $_SESSION['admin_success'] = "Đăng nhập thành công! Chào mừng " . $admin['full_name'];
        $stmt->close();
        redirect('admin-dashboard.php');
    } else {
        // Email không có quyền admin
        $_SESSION['admin_errors'] = ['Email ' . $email . ' không có quyền truy cập trang quản trị.'];
        $stmt->close();
        redirect('admin-login.php');
    }
}

// ========== XỬ LÝ ĐĂNG NHẬP USER THƯỜNG ==========
// Kiểm tra user đã tồn tại chưa
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User đã tồn tại - đăng nhập
    $user = $result->fetch_assoc();
    
    // Cập nhật avatar từ Google (luôn cập nhật để có avatar mới nhất)
    if (!empty($avatar_url)) {
        // Luôn cập nhật avatar từ Google vào database
        $update_stmt = $conn->prepare("UPDATE nguoi_dung SET avt = ? WHERE id = ?");
        $update_stmt->bind_param("si", $avatar_url, $user['id']);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Sử dụng avatar từ Google cho session
        $user['avt'] = $avatar_url;
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['ho_ten'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_avatar'] = $user['avt']; // Lấy trực tiếp từ $user['avt'] đã được cập nhật
    $_SESSION['logged_in'] = true;
    
    $_SESSION['success'] = "Đăng nhập thành công! Chào mừng " . $user['ho_ten'];
    redirect('index.php');
    
} else {
    // User chưa tồn tại - tạo mới
    $mat_khau = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT); // Random password
    
    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, mat_khau, avt) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $ho_ten, $email, $mat_khau, $avatar_url);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        
        // Set session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $ho_ten;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_avatar'] = $avatar_url;
        $_SESSION['logged_in'] = true;
        
        $_SESSION['success'] = "Đăng ký thành công! Chào mừng " . $ho_ten;
        redirect('index.php');
    } else {
        $_SESSION['errors'] = ['Không thể tạo tài khoản'];
        redirect('register.php');
    }
}

$stmt->close();
?>
