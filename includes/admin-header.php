<?php
// Admin Header Component
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}
?>
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="admin-dashboard.php" class="flex items-center">
                    <i class="fas fa-user-shield text-red-500 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">Admin Panel</span>
                </a>
                <div class="hidden md:flex space-x-4">
                    <a href="admin-dashboard.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-dashboard.php' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                    <a href="admin-orders.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-orders.php' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-shopping-cart mr-1"></i>Đơn hàng
                    </a>
                    <a href="admin-dresses.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-dresses.php' ? 'bg-pink-100 text-pink-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-tshirt mr-1"></i>Váy cưới
                    </a>
                    <a href="admin-users.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-users.php' ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-users mr-1"></i>Người dùng
                    </a>
                    <a href="admin-contacts.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-contacts.php' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-envelope mr-1"></i>Liên hệ
                    </a>
                    <a href="admin-blogs.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-blogs.php' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-newspaper mr-1"></i>Tin tức
                    </a>
                    <a href="admin-bookings.php" class="px-3 py-2 rounded-md text-sm font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'admin-bookings.php' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i class="fas fa-calendar-alt mr-1"></i>Lịch hẹn
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700 hidden sm:block">
                    <i class="fas fa-user mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                </span>
                <a href="index.php" target="_blank" class="text-gray-500 hover:text-gray-700" title="Xem trang chủ">
                    <i class="fas fa-external-link-alt"></i>
                </a>
                <a href="admin-logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                </a>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="md:hidden border-t">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="admin-dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Dashboard</a>
            <a href="admin-orders.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Đơn hàng</a>
            <a href="admin-dresses.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Váy cưới</a>
            <a href="admin-users.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Người dùng</a>
            <a href="admin-contacts.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Liên hệ</a>
            <a href="admin-blogs.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Tin tức</a>
            <a href="admin-bookings.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100">Lịch hẹn</a>
        </div>
    </div>
</nav>
