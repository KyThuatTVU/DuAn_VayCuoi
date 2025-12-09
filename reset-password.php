<?php
session_start();
require_once 'includes/config.php';

$page_title = 'Đặt Lại Mật Khẩu';

// Kiểm tra có email trong session không
if (!isset($_SESSION['reset_email'])) {
    $_SESSION['errors'] = ['Vui lòng nhập email trước'];
    redirect('forgot-password.php');
}

$email = $_SESSION['reset_email'];

// Kiểm tra kết nối database
if (!$conn) {
    $_SESSION['errors'] = ['Lỗi kết nối database. Vui lòng thử lại sau.'];
    redirect('forgot-password.php');
}

// Lấy thông tin OTP từ database
$stmt = $conn->prepare("SELECT *, NOW() as server_time FROM password_reset WHERE email = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$otp_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Nếu không có OTP
if (!$otp_data) {
    unset($_SESSION['reset_email']);
    $_SESSION['errors'] = ['Yêu cầu khôi phục không tồn tại hoặc đã hết hạn. Vui lòng thử lại.'];
    redirect('forgot-password.php');
}

// Kiểm tra OTP đã hết hạn chưa
$otp_expired = strtotime($otp_data['expires_at']) < strtotime($otp_data['server_time']);

// Tính thời gian còn lại
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 min-h-screen">

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-lock-open text-red-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Đặt Lại Mật Khẩu</h2>
            <p class="mt-2 text-sm text-gray-600">
                Mã OTP đã được gửi đến<br>
                <span class="font-semibold text-red-600"><?php echo htmlspecialchars($email); ?></span>
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
        
        if (isset($_SESSION['reset_errors'])) {
            echo '<div class="rounded-lg bg-red-50 p-4 border border-red-200">';
            echo '<ul class="list-disc list-inside text-sm text-red-800">';
            foreach ($_SESSION['reset_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['reset_errors']);
        }
        ?>

        <!-- OTP Expired Warning -->
        <?php if ($otp_expired): ?>
        <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                <p class="text-sm text-yellow-800">Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="process-reset-password.php" method="POST" class="space-y-6">
                <!-- OTP Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
                        Nhập mã OTP 6 số
                    </label>
                    <div class="flex justify-center gap-2">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <input 
                            type="text" 
                            name="otp<?php echo $i; ?>" 
                            maxlength="1"
                            pattern="[0-9]"
                            inputmode="numeric"
                            class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                            required
                            <?php echo $otp_expired ? 'disabled' : ''; ?>
                        >
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Countdown Timer -->
                    <?php if (!$otp_expired): ?>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-500">
                            Mã hết hạn sau: 
                            <span id="countdown" class="font-semibold text-red-600"></span>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- New Password -->
                <div>
                    <label for="mat_khau_moi" class="block text-sm font-medium text-gray-700 mb-2">
                        Mật khẩu mới
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="mat_khau_moi" 
                            name="mat_khau_moi" 
                            required
                            minlength="6"
                            placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                            class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            <?php echo $otp_expired ? 'disabled' : ''; ?>
                        >
                        <button type="button" onclick="togglePassword('mat_khau_moi')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="xac_nhan_mat_khau" class="block text-sm font-medium text-gray-700 mb-2">
                        Xác nhận mật khẩu
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="xac_nhan_mat_khau" 
                            name="xac_nhan_mat_khau" 
                            required
                            minlength="6"
                            placeholder="Nhập lại mật khẩu mới"
                            class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            <?php echo $otp_expired ? 'disabled' : ''; ?>
                        >
                        <button type="button" onclick="togglePassword('xac_nhan_mat_khau')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-red-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    <?php echo $otp_expired ? 'disabled' : ''; ?>
                >
                    <i class="fas fa-key"></i>
                    Đổi Mật Khẩu
                </button>
            </form>

            <!-- Resend OTP -->
            <div class="mt-6 text-center">
                <form action="resend-reset-otp.php" method="POST" class="inline">
                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium" id="resend-btn">
                        <i class="fas fa-redo-alt mr-1"></i>
                        Gửi lại mã OTP
                    </button>
                </form>
            </div>

            <!-- Back to Login -->
            <div class="mt-4 text-center">
                <a href="login.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// OTP Input Auto-focus
const otpInputs = document.querySelectorAll('.otp-input');
otpInputs.forEach((input, index) => {
    input.addEventListener('input', function(e) {
        // Chỉ cho phép số
        this.value = this.value.replace(/[^0-9]/g, '');
        
        if (this.value.length === 1 && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }
    });
    
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
            otpInputs[index - 1].focus();
        }
    });
    
    // Paste handler
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        
        for (let i = 0; i < pastedData.length && i < otpInputs.length; i++) {
            otpInputs[i].value = pastedData[i];
        }
        
        if (pastedData.length > 0) {
            const nextIndex = Math.min(pastedData.length, otpInputs.length - 1);
            otpInputs[nextIndex].focus();
        }
    });
});

// Focus first input on load
otpInputs[0]?.focus();

// Countdown Timer
<?php if (!$otp_expired): ?>
let remainingSeconds = <?php echo $remaining_seconds; ?>;
const countdownEl = document.getElementById('countdown');

function updateCountdown() {
    if (remainingSeconds <= 0) {
        countdownEl.textContent = 'Đã hết hạn';
        countdownEl.classList.add('text-gray-500');
        // Disable form
        otpInputs.forEach(input => input.disabled = true);
        document.querySelector('button[type="submit"]').disabled = true;
        return;
    }
    
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    remainingSeconds--;
    setTimeout(updateCountdown, 1000);
}
updateCountdown();
<?php endif; ?>

// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
