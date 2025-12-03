<?php
/**
 * Test SMTP Connection
 * Truy cập: http://localhost/DuAn_CuaHangVayCuoiGradenHome/test-smtp.php
 */

require_once 'includes/config.php';
require_once 'includes/mail-helper.php';

echo "<h1>Test SMTP Configuration</h1>";
echo "<style>body{font-family:Arial;max-width:800px;margin:20px auto;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;border-radius:5px;overflow-x:auto;}</style>";

// Hiển thị cấu hình
echo "<h2>1. Cấu hình SMTP</h2>";
echo "<pre>";
echo "SMTP_HOST: " . (getenv('SMTP_HOST') ?: 'Chưa cấu hình') . "\n";
echo "SMTP_PORT: " . (getenv('SMTP_PORT') ?: 'Chưa cấu hình') . "\n";
echo "SMTP_USER: " . (getenv('SMTP_USER') ?: 'Chưa cấu hình') . "\n";
echo "SMTP_PASS: " . (getenv('SMTP_PASS') ? '***' . substr(getenv('SMTP_PASS'), -4) : 'Chưa cấu hình') . "\n";
echo "SMTP_FROM_EMAIL: " . (getenv('SMTP_FROM_EMAIL') ?: 'Chưa cấu hình') . "\n";
echo "SMTP_FROM_NAME: " . (getenv('SMTP_FROM_NAME') ?: 'Chưa cấu hình') . "\n";
echo "</pre>";

// Kiểm tra bảng OTP
echo "<h2>2. Kiểm tra bảng otp_verification</h2>";
$table_check = $conn->query("SHOW TABLES LIKE 'otp_verification'");
if ($table_check->num_rows > 0) {
    echo "<p class='success'>✓ Bảng otp_verification tồn tại</p>";
    
    // Xem dữ liệu
    $result = $conn->query("SELECT id, email, otp_code, ho_ten, expires_at, is_verified, attempts, created_at FROM otp_verification ORDER BY created_at DESC LIMIT 5");
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr><th>ID</th><th>Email</th><th>OTP</th><th>Họ tên</th><th>Hết hạn</th><th>Verified</th><th>Attempts</th><th>Created</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $expired = strtotime($row['expires_at']) < time() ? ' (HẾT HẠN)' : '';
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td><strong>{$row['otp_code']}</strong></td>";
            echo "<td>{$row['ho_ten']}</td>";
            echo "<td>{$row['expires_at']}{$expired}</td>";
            echo "<td>{$row['is_verified']}</td>";
            echo "<td>{$row['attempts']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Chưa có dữ liệu OTP</p>";
    }
} else {
    echo "<p class='error'>✗ Bảng otp_verification chưa tồn tại</p>";
}

// Test gửi email
echo "<h2>3. Test gửi email</h2>";
if (isset($_GET['test_email'])) {
    $test_email = $_GET['test_email'];
    $test_otp = generateOTP(6);
    
    echo "<p>Đang gửi email đến: <strong>$test_email</strong></p>";
    echo "<p>Mã OTP test: <strong>$test_otp</strong></p>";
    
    $result = sendOTPEmail($test_email, 'Test User', $test_otp);
    
    if ($result['success']) {
        echo "<p class='success'>✓ " . $result['message'] . "</p>";
    } else {
        echo "<p class='error'>✗ " . $result['message'] . "</p>";
    }
} else {
    echo "<form method='GET'>";
    echo "<input type='email' name='test_email' placeholder='Nhập email để test' required style='padding:10px;width:300px;'>";
    echo "<button type='submit' style='padding:10px 20px;background:#007bff;color:white;border:none;cursor:pointer;'>Gửi email test</button>";
    echo "</form>";
}

// Server time
echo "<h2>4. Thời gian server</h2>";
echo "<pre>";
echo "PHP time(): " . date('Y-m-d H:i:s') . "\n";
$mysql_time = $conn->query("SELECT NOW() as now")->fetch_assoc();
echo "MySQL NOW(): " . $mysql_time['now'] . "\n";
echo "</pre>";

echo "<hr>";
echo "<p><a href='register.php'>← Quay lại đăng ký</a></p>";
?>
