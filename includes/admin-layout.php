<?php
// Admin Layout - Dùng chung cho tất cả trang admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Lấy số liệu thông báo
$pending_orders = 0;
$new_contacts = 0;
$pending_bookings = 0;
$unread_notifications = 0;

if ($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM don_hang WHERE trang_thai = 'pending'");
    if ($result) $pending_orders = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM lien_he WHERE status = 'new'");
    if ($result) $new_contacts = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM dat_lich_thu_vay WHERE status = 'pending'");
    if ($result) $pending_bookings = $result->fetch_assoc()['total'];
    
    // Lấy số thông báo chưa đọc
    $check_notif = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
    if ($check_notif && $check_notif->num_rows > 0) {
        $result = $conn->query("SELECT COUNT(*) as total FROM admin_notifications WHERE is_read = 0");
        if ($result) $unread_notifications = $result->fetch_assoc()['total'];
    }
}

// Xác định trang hiện tại
$current_file = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi" class="admin-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#102a43">
    <title><?php echo ($page_title ?? 'Admin') . ' - ' . SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 50: '#f0f4f8', 100: '#d9e2ec', 200: '#bcccdc', 300: '#9fb3c8', 400: '#829ab1', 500: '#627d98', 600: '#486581', 700: '#334e68', 800: '#243b53', 900: '#102a43' },
                        accent: { 400: '#f6ad55', 500: '#ed8936', 600: '#dd6b20' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/admin-responsive.css">
    <script src="assets/js/admin-mobile.js" defer></script>
    <style>
        /* Base admin styles */
        html.admin-page, body.admin-page { overflow-x: hidden; max-width: 100vw; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.1); border-left: 3px solid #ed8936; }
        .card { transition: all 0.3s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        /* Custom scrollbar for sidebar */
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        /* Prevent text overflow */
        .truncate-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        /* Table scroll indicator */
        .table-scroll-hint { position: relative; }
        .table-scroll-hint::after { content: ''; position: absolute; right: 0; top: 0; bottom: 0; width: 30px; background: linear-gradient(to left, rgba(255,255,255,1), transparent); pointer-events: none; }
    </style>
</head>
<body class="bg-gray-100 admin-page">
    <div class="flex min-h-screen max-w-full overflow-x-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-navy-900 fixed h-full overflow-y-auto overflow-x-hidden z-50 admin-sidebar">
            <!-- Profile -->
            <div class="p-4 text-center border-b border-navy-700">
                <div class="w-16 h-16 mx-auto bg-navy-700 rounded-full flex items-center justify-center mb-3 overflow-hidden">
                    <?php if (!empty($_SESSION['admin_avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['admin_avatar']); ?>" alt="Avatar" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <i class="fas fa-user text-3xl text-accent-500" style="display:none;"></i>
                    <?php else: ?>
                        <i class="fas fa-user text-3xl text-accent-500"></i>
                    <?php endif; ?>
                </div>
                <h3 class="text-white font-semibold text-base truncate px-1"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h3>
                <p class="text-navy-300 text-xs break-all px-1 leading-relaxed" title="<?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?>">
                    <?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'admin@vaycuoi.com'); ?>
                </p>
            </div>

            <!-- Menu -->
            <nav class="p-4">
                <a href="admin-dashboard.php" class="sidebar-link <?php echo $current_file == 'admin-dashboard.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-white rounded">
                    <i class="fas fa-home w-5"></i> Dashboard
                </a>
                <a href="admin-orders.php" class="sidebar-link <?php echo $current_file == 'admin-orders.php' || $current_file == 'admin-order-detail.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-shopping-cart w-5"></i> Đơn hàng
                    <?php if ($pending_orders > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $pending_orders; ?></span><?php endif; ?>
                </a>
                <a href="admin-dresses.php" class="sidebar-link <?php echo $current_file == 'admin-dresses.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-tshirt w-5"></i> Váy cưới
                </a>
                <a href="admin-users.php" class="sidebar-link <?php echo $current_file == 'admin-users.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-users w-5"></i> Khách hàng
                </a>
                <a href="admin-bookings.php" class="sidebar-link <?php echo $current_file == 'admin-bookings.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-calendar w-5"></i> Lịch hẹn
                    <?php if ($pending_bookings > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $pending_bookings; ?></span><?php endif; ?>
                </a>
                <a href="admin-promotions.php" class="sidebar-link <?php echo $current_file == 'admin-promotions.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-gift w-5"></i> Khuyến mãi
                </a>
                <a href="admin-contacts.php" class="sidebar-link <?php echo $current_file == 'admin-contacts.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-envelope w-5"></i> Liên hệ
                    <?php if ($new_contacts > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $new_contacts; ?></span><?php endif; ?>
                </a>
                <a href="admin-blogs.php" class="sidebar-link <?php echo $current_file == 'admin-blogs.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-newspaper w-5"></i> Tin tức
                </a>
                <a href="admin-comments.php" class="sidebar-link <?php echo $current_file == 'admin-comments.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-comments w-5"></i> Bình luận
                </a>
                <a href="admin-payments.php" class="sidebar-link <?php echo $current_file == 'admin-payments.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-credit-card w-5"></i> Thanh toán
                    <?php if (isset($payment_stats) && $payment_stats['total_pending'] > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $payment_stats['total_pending']; ?></span><?php endif; ?>
                </a>
                <a href="admin-notifications.php" class="sidebar-link <?php echo $current_file == 'admin-notifications.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-bell w-5"></i> Thông báo
                    <?php if ($unread_notifications > 0): ?><span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $unread_notifications; ?></span><?php endif; ?>
                </a>
                <a href="admin-settings.php" class="sidebar-link <?php echo $current_file == 'admin-settings.php' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-cog w-5"></i> Cài đặt
                </a>
                <div class="border-t border-navy-700 mt-4 pt-4">
                    <a href="index.php?admin_preview=1" target="_blank" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded">
                        <i class="fas fa-external-link-alt w-5"></i> Xem website
                    </a>
                    <a href="admin-logout.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                        <i class="fas fa-sign-out-alt w-5"></i> Đăng xuất
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 max-w-full overflow-x-hidden admin-main">
            <!-- Header -->
            <header class="bg-white shadow-sm px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <!-- Mobile menu toggle in header -->
                    <button class="lg:hidden flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0" id="headerMenuToggle" aria-label="Toggle Menu">
                        <i class="fas fa-bars text-navy-700"></i>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-navy-900 truncate"><?php echo $page_title ?? 'Admin'; ?></h1>
                        <p class="text-navy-500 text-xs sm:text-sm hidden sm:block truncate"><?php echo $page_subtitle ?? 'Quản lý hệ thống'; ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                    <a href="admin-notifications.php" class="relative text-navy-500 hover:text-navy-700 w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-bell text-base sm:text-lg lg:text-xl"></i>
                        <?php if ($unread_notifications > 0): ?>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium"><?php echo min(99, $unread_notifications); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full overflow-hidden bg-navy-200 flex-shrink-0">
                        <?php if (!empty($_SESSION['admin_avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['admin_avatar']); ?>" alt="Avatar" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full flex items-center justify-center" style="display:none;">
                                <i class="fas fa-user text-navy-500"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-user text-navy-500"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-3 sm:p-4 lg:p-6 max-w-full overflow-x-hidden">

    <!-- Admin Mobile Toggle Button -->
    <button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Admin Sidebar Overlay -->
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>
    
    <script>
    // Admin Mobile Menu Toggle - Enhanced
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('aside.w-64');
        const toggle = document.getElementById('adminMobileToggle');
        const headerToggle = document.getElementById('headerMenuToggle');
        const overlay = document.getElementById('adminSidebarOverlay');
        
        function openSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (toggle) {
                    const icon = toggle.querySelector('i');
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            }
        }
        
        function closeSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                if (toggle) {
                    const icon = toggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        }
        
        function toggleSidebar() {
            if (sidebar && sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
        
        if (toggle) {
            toggle.addEventListener('click', toggleSidebar);
        }
        
        if (headerToggle) {
            headerToggle.addEventListener('click', toggleSidebar);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
        
        // Close sidebar when clicking on a link (mobile)
        if (sidebar) {
            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        setTimeout(closeSidebar, 100);
                    }
                });
            });
        }
        
        // Handle resize - close sidebar when going to desktop
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 1024 && sidebar && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            }, 100);
        });
    });
    </script>
