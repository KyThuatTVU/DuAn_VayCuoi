<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Admin Dashboard';

// Lấy thống kê
$stats = [];

// Tổng số đơn hàng
$result = $conn->query("SELECT COUNT(*) as total FROM don_hang");
$stats['total_orders'] = $result->fetch_assoc()['total'];

// Tổng doanh thu
$result = $conn->query("SELECT SUM(tong_tien) as total FROM don_hang WHERE trang_thai_thanh_toan = 'paid'");
$stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Tổng người dùng
$result = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Tổng váy cưới
$result = $conn->query("SELECT COUNT(*) as total FROM vay_cuoi");
$stats['total_dresses'] = $result->fetch_assoc()['total'];

// Đơn hàng chờ xử lý
$result = $conn->query("SELECT COUNT(*) as total FROM don_hang WHERE trang_thai = 'pending'");
$stats['pending_orders'] = $result->fetch_assoc()['total'];

// Liên hệ mới
$result = $conn->query("SELECT COUNT(*) as total FROM lien_he WHERE status = 'new'");
$stats['new_contacts'] = $result->fetch_assoc()['total'];
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
<body class="bg-gray-100">
    
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-user-shield text-red-500 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">Admin Panel</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </span>
                    <a href="admin-logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Chào mừng, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
            <p class="text-gray-600 mt-2">Tổng quan hệ thống quản lý cửa hàng váy cưới</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Đơn hàng -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Tổng đơn hàng</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($stats['total_orders']); ?></p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-shopping-cart text-blue-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Doanh thu -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Tổng doanh thu</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($stats['total_revenue']); ?>đ</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Người dùng -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Người dùng</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <i class="fas fa-users text-purple-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Váy cưới -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-pink-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Váy cưới</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($stats['total_dresses']); ?></p>
                    </div>
                    <div class="bg-pink-100 rounded-full p-4">
                        <i class="fas fa-tshirt text-pink-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Đơn chờ -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Đơn chờ xử lý</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($stats['pending_orders']); ?></p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-4">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Liên hệ mới -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Liên hệ mới</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo number_format($stats['new_contacts']); ?></p>
                    </div>
                    <div class="bg-red-100 rounded-full p-4">
                        <i class="fas fa-envelope text-red-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Thao tác nhanh</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="admin-orders.php" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-shopping-cart text-blue-500 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Quản lý đơn hàng</span>
                </a>
                <a href="admin-dresses.php" class="flex flex-col items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                    <i class="fas fa-tshirt text-pink-500 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Quản lý váy cưới</span>
                </a>
                <a href="admin-users.php" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                    <i class="fas fa-users text-purple-500 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Quản lý người dùng</span>
                </a>
                <a href="admin-contacts.php" class="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-envelope text-red-500 text-3xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Quản lý liên hệ</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
