<?php
session_start();
require_once 'includes/config.php';

$page_title = 'Quên Mật Khẩu';

// Nếu đã đăng nhập, chuyển về trang chủ
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    redirect('index.php');
}
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
<body class="bg-gradient-to-br from-blue-50 via-sky-50 to-cyan-50 min-h-screen">

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="index.php" class="inline-block mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
            </a>
            <h2 class="text-3xl font-bold text-gray-900">Quên Mật Khẩu</h2>
            <p class="mt-2 text-sm text-gray-600">
                Nhập email đã đăng ký để nhận mã khôi phục
            </p>
        </div>

        <!-- Alert Messages -->
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="rounded-lg bg-green-50 p-4 border border-green-200">';
            echo '<div class="flex items-center gap-2">';
            echo '<i class="fas fa-check-circle text-green-500"></i>';
            echo '<p class="text-sm text-green-800">' . htmlspecialchars($_SESSION['success']) . '</p>';
            echo '</div></div>';
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['errors'])) {
            echo '<div class="rounded-lg bg-red-50 p-4 border border-red-200">';
            echo '<ul class="list-disc list-inside text-sm text-red-800">';
            foreach ($_SESSION['errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['errors']);
        }
        ?>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="process-forgot-password.php" method="POST" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email đã đăng ký
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            placeholder="Nhập email của bạn"
                            value="<?php echo htmlspecialchars($_SESSION['old_email'] ?? ''); ?>"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all"
                >
                    <i class="fas fa-paper-plane"></i>
                    Gửi Mã Khôi Phục
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="login.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại đăng nhập
                </a>
            </div>
        </div>

        <!-- Help Text -->
        <div class="text-center">
            <p class="text-sm text-gray-500">
                Chưa có tài khoản? 
                <a href="register.php" class="font-medium text-blue-600 hover:text-blue-700">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>

<?php unset($_SESSION['old_email']); ?>
</body>
</html>
