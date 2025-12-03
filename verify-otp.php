<?php
session_start();
require_once 'includes/config.php';

$page_title = 'Xác Nhận OTP';

// Kiểm tra có email trong session không
if (!isset($_SESSION['otp_email'])) {
    $_SESSION['errors'] = ['Vui lòng đăng ký trước'];
    redirect('register.php');
}

$email = $_SESSION['otp_email'];

// Kiểm tra bảng otp_verification có tồn tại không
$table_check = $conn->query("SHOW TABLES LIKE 'otp_verification'");
if ($table_check->num_rows == 0) {
    // Tạo bảng nếu chưa có
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
}

// Lấy thông tin OTP từ database (bỏ điều kiện expires_at để debug)
$stmt = $conn->prepare("SELECT *, NOW() as server_time FROM otp_verification WHERE email = ? AND is_verified = 0 ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$otp_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Nếu không có OTP
if (!$otp_data) {
    unset($_SESSION['otp_email']);
    $_SESSION['errors'] = ['Mã OTP không tồn tại. Vui lòng đăng ký lại.'];
    redirect('register.php');
}

// Kiểm tra OTP đã hết hạn chưa
if (strtotime($otp_data['expires_at']) < strtotime($otp_data['server_time'])) {
    // OTP hết hạn - cho phép gửi lại
    $otp_expired = true;
} else {
    $otp_expired = false;
}

// Tính thời gian còn lại (sử dụng server time từ MySQL)
$expires_at = strtotime($otp_data['expires_at']);
$server_time = strtotime($otp_data['server_time']);
$remaining_seconds = max(0, $expires_at - $server_time);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-sky-50 to-cyan-50 min-h-screen">

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Xác Nhận Email</h2>
            <p class="mt-2 text-sm text-gray-600">
                Mã OTP đã được gửi đến<br>
                <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($email); ?></span>
            </p>
        </div>

        <!-- Alert Messages -->
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="rounded-lg bg-green-50 p-4 border border-green-200">';
            echo '<p class="text-sm text-green-800">' . htmlspecialchars($_SESSION['success']) . '</p>';
            echo '</div>';
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['otp_errors'])) {
            echo '<div class="rounded-lg bg-red-50 p-4 border border-red-200">';
            foreach ($_SESSION['otp_errors'] as $error) {
                echo '<p class="text-sm text-red-800">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
            unset($_SESSION['otp_errors']);
        }
        ?>

        <!-- Timer -->
        <div class="text-center">
            <?php if ($otp_expired): ?>
            <div id="timer" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-lg">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-red-700 font-medium">
                    ⚠️ Mã OTP đã hết hạn! Vui lòng gửi lại mã.
                </span>
            </div>
            <?php else: ?>
            <div id="timer" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 border border-orange-200 rounded-lg">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-orange-700 font-medium">
                    Mã hết hạn sau: <span id="countdown" class="font-bold"><?php echo gmdate("i:s", $remaining_seconds); ?></span>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <form action="process-verify-otp.php" method="POST" class="space-y-6">
                <!-- OTP Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4 text-center">
                        Nhập mã OTP 6 số
                    </label>
                    <div class="flex justify-center gap-2" id="otp-container">
                        <input type="text" name="otp1" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" autofocus>
                        <input type="text" name="otp2" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        <input type="text" name="otp3" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        <input type="text" name="otp4" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        <input type="text" name="otp5" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        <input type="text" name="otp6" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    </div>
                    <!-- Hidden field để gửi OTP đầy đủ -->
                    <input type="hidden" name="otp_code" id="otp_code">
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    id="verify-btn"
                    <?php echo $otp_expired ? 'disabled' : ''; ?>
                    class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Xác Nhận
                </button>
            </form>

            <!-- Resend OTP -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Không nhận được mã?</p>
                <form action="resend-otp.php" method="POST" class="inline">
                    <?php if ($otp_expired): ?>
                    <button 
                        type="submit" 
                        id="resend-btn"
                        class="text-blue-600 hover:text-blue-700 font-semibold"
                    >
                        Gửi lại mã OTP
                    </button>
                    <?php else: ?>
                    <button 
                        type="submit" 
                        id="resend-btn"
                        class="text-blue-600 hover:text-blue-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                    >
                        Gửi lại mã OTP (<span id="resend-timer">60</span>s)
                    </button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Back to Register -->
            <div class="mt-4 text-center">
                <a href="register.php" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Quay lại đăng ký
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// OTP Input handling
const otpInputs = document.querySelectorAll('.otp-input');
const otpCodeField = document.getElementById('otp_code');

otpInputs.forEach((input, index) => {
    // Chỉ cho phép nhập số
    input.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        
        if (e.target.value && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }
        
        updateOTPCode();
    });
    
    // Xử lý phím Backspace
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
            otpInputs[index - 1].focus();
        }
    });
    
    // Xử lý paste
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        
        pastedData.split('').forEach((char, i) => {
            if (otpInputs[i]) {
                otpInputs[i].value = char;
            }
        });
        
        if (pastedData.length > 0) {
            otpInputs[Math.min(pastedData.length, 5)].focus();
        }
        
        updateOTPCode();
    });
});

function updateOTPCode() {
    let otp = '';
    otpInputs.forEach(input => {
        otp += input.value;
    });
    otpCodeField.value = otp;
}

// Countdown timer
let remainingSeconds = <?php echo $remaining_seconds; ?>;
let otpExpired = <?php echo $otp_expired ? 'true' : 'false'; ?>;
const countdownEl = document.getElementById('countdown');
const timerEl = document.getElementById('timer');
const verifyBtn = document.getElementById('verify-btn');

if (!otpExpired && remainingSeconds > 0) {
    const countdownInterval = setInterval(() => {
        remainingSeconds--;
        
        if (remainingSeconds <= 0) {
            clearInterval(countdownInterval);
            countdownEl.textContent = '00:00';
            timerEl.classList.remove('bg-orange-50', 'border-orange-200');
            timerEl.classList.add('bg-red-50', 'border-red-200');
            timerEl.querySelector('svg').classList.remove('text-orange-500');
            timerEl.querySelector('svg').classList.add('text-red-500');
            timerEl.querySelector('span').classList.remove('text-orange-700');
            timerEl.querySelector('span').classList.add('text-red-700');
            timerEl.querySelector('span').innerHTML = '⚠️ Mã OTP đã hết hạn!';
            verifyBtn.disabled = true;
            // Enable resend button
            const resendBtn = document.getElementById('resend-btn');
            if (resendBtn) {
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Gửi lại mã OTP';
            }
        } else {
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            countdownEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }
    }, 1000);
}

// Resend timer (chỉ chạy khi OTP chưa hết hạn)
if (!otpExpired) {
    let resendSeconds = 60;
    const resendBtn = document.getElementById('resend-btn');
    const resendTimerEl = document.getElementById('resend-timer');

    if (resendBtn && resendTimerEl) {
        const resendInterval = setInterval(() => {
            resendSeconds--;
            resendTimerEl.textContent = resendSeconds;
            
            if (resendSeconds <= 0) {
                clearInterval(resendInterval);
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Gửi lại mã OTP';
            }
        }, 1000);
    }
}
</script>
</body>
</html>
