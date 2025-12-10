<?php
// Đảm bảo session được start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu admin đang xem preview website
// Khi có tham số admin_preview, hiển thị website như khách chưa đăng nhập
$is_admin_preview = isset($_GET['admin_preview']) && $_GET['admin_preview'] == '1' && isset($_SESSION['admin_logged_in']);

// Kiểm tra trạng thái user (nếu đã đăng nhập và không phải admin preview)
if (!$is_admin_preview) {
    require_once __DIR__ . '/check-user-status.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <meta name="description" content="Cửa hàng váy cưới cao cấp - Cho thuê váy cưới đẹp, giá tốt tại TP.HCM">
    <!-- Allow loading images from Google -->
    <meta http-equiv="Content-Security-Policy" content="img-src 'self' https://lh3.googleusercontent.com https://lh4.googleusercontent.com https://lh5.googleusercontent.com https://lh6.googleusercontent.com data: blob:;">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7ec8e3',
                        accent: '#5ab8d9',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/mobile-responsive.css">
    <!-- Mobile Enhancements -->
    <script src="assets/js/mobile-enhancements.js" defer></script>
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && !$is_admin_preview): ?>
    <!-- User Status Checker - Kiểm tra tài khoản bị khóa realtime -->
    <script src="assets/js/user-status-checker.js" defer></script>
    <?php endif; ?>
    <!-- Mobile viewport fix for iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#7ec8e3">
</head>
<body class="antialiased">
    <?php if ($is_admin_preview): ?>
    <!-- Admin Preview Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 px-4 text-center text-sm font-medium sticky top-0 z-[100]">
        <i class="fas fa-eye mr-2"></i>
        Bạn đang xem website ở chế độ Admin Preview (không hiển thị thông tin đăng nhập user)
        <a href="admin-dashboard.php" class="ml-4 underline hover:no-underline">← Quay lại Admin</a>
    </div>
    <?php endif; ?>
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-left">
                    <span><i class="icon-phone"></i> Hotline: 0901 234 567</span>
                    <span><i class="icon-email"></i> contact@vaycuoi.com</span>
                </div>
                <div class="top-bar-right">
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header sticky top-0 z-50">
        <div class="container mx-auto px-4 max-w-screen-2xl">
            <div class="flex items-center justify-between py-4 gap-6">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="index.php" class="block transition-transform hover:scale-105">
                        <img src="images/Green And White Illustrative Flower Shop Logo.png" alt="<?php echo SITE_NAME; ?>" class="h-16 w-auto">
                    </a>
                </div>

                <!-- Navigation -->
                <?php
                // Lấy tên file hiện tại để xác định trang active
                $current_page = basename($_SERVER['PHP_SELF']);
                
                // Helper function để check active state
                function isActive($pages) {
                    global $current_page;
                    $pages = is_array($pages) ? $pages : [$pages];
                    return in_array($current_page, $pages);
                }
                ?>
                <nav class="hidden lg:flex flex-1 justify-center">
                    <ul class="flex items-center gap-2 xl:gap-3">
                        <li>
                            <a href="index.php" class="inline-flex items-center gap-2 px-4 xl:px-5 py-3 rounded-xl font-semibold text-base xl:text-lg transition-all duration-300 <?php echo isActive('index.php') ? '!bg-gradient-to-r !from-primary !to-accent !text-white !shadow-lg scale-105' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary'; ?>">
                                <svg class="w-5 h-5 xl:w-6 xl:h-6 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <span class="whitespace-nowrap">Trang chủ</span>
                            </a>
                        </li>
                        <li class="relative group">
                            <a href="products.php" class="inline-flex items-center gap-2 px-4 xl:px-5 py-3 rounded-xl font-semibold text-base xl:text-lg transition-all duration-300 <?php echo isActive(['products.php', 'product-detail.php']) ? '!bg-gradient-to-r !from-primary !to-accent !text-white !shadow-lg scale-105' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary'; ?>">
                                <svg class="w-5 h-5 xl:w-6 xl:h-6 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                                <span class="whitespace-nowrap">Váy Cưới</span>
                            </a>
                            <ul class="absolute top-full left-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 min-w-[220px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 overflow-hidden z-50">
                                <li><a href="products.php?cat=princess" class="block px-5 py-3 text-base font-medium text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">Váy Công Chúa</a></li>
                                <li><a href="products.php?cat=mermaid" class="block px-5 py-3 text-base font-medium text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">Váy Đuôi Cá</a></li>
                                <li><a href="products.php?cat=aline" class="block px-5 py-3 text-base font-medium text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">Váy Chữ A</a></li>
                                <li><a href="products.php?cat=modern" class="block px-5 py-3 text-base font-medium text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">Váy Hiện Đại</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="booking.php" class="inline-flex items-center gap-2 px-4 xl:px-5 py-3 rounded-xl font-semibold text-base xl:text-lg transition-all duration-300 <?php echo isActive('booking.php') ? '!bg-gradient-to-r !from-primary !to-accent !text-white !shadow-lg scale-105' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary'; ?>">
                                <svg class="w-5 h-5 xl:w-6 xl:h-6 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                    <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                    <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span class="whitespace-nowrap">Đặt Lịch</span>
                            </a>
                        </li>
                        <li>
                            <a href="blog.php" class="inline-flex items-center gap-2 px-4 xl:px-5 py-3 rounded-xl font-semibold text-base xl:text-lg transition-all duration-300 <?php echo isActive('blog.php') ? '!bg-gradient-to-r !from-primary !to-accent !text-white !shadow-lg scale-105' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary'; ?>">
                                <svg class="w-5 h-5 xl:w-6 xl:h-6 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8" stroke="white" stroke-width="2"/>
                                    <line x1="16" y1="13" x2="8" y2="13" stroke="white" stroke-width="2"/>
                                    <line x1="16" y1="17" x2="8" y2="17" stroke="white" stroke-width="2"/>
                                </svg>
                                <span class="whitespace-nowrap">Tin Tức</span>
                            </a>
                        </li>
                        <li>
                            <a href="about.php" class="inline-flex items-center gap-2 px-4 xl:px-5 py-3 rounded-xl font-semibold text-base xl:text-lg transition-all duration-300 <?php echo isActive('about.php') ? '!bg-gradient-to-r !from-primary !to-accent !text-white !shadow-lg scale-105' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary'; ?>">
                                <svg class="w-5 h-5 xl:w-6 xl:h-6 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span class="whitespace-nowrap">Về Chúng Tôi</span>
                            </a>
                        </li>
                        <li>
                            <a href="contact.php" class="inline-flex items-center gap-2 px-4 xl:px-5 py-3 rounded-xl font-semibold text-base xl:text-lg transition-all duration-300 <?php echo isActive('contact.php') ? '!bg-gradient-to-r !from-primary !to-accent !text-white !shadow-lg scale-105' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary'; ?>">
                                <svg class="w-5 h-5 xl:w-6 xl:h-6 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <span class="whitespace-nowrap">Liên Hệ</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Header Actions -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <button class="hidden md:flex items-center justify-center w-11 h-11 rounded-full hover:bg-gray-100 transition-colors search-toggle" title="Tìm kiếm">
                        <svg class="w-6 h-6 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </button>
                    
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && !$is_admin_preview): ?>
                    <!-- Notification Bell -->
                    <div class="relative" id="notificationWrapper">
                        <button class="relative flex items-center justify-center w-11 h-11 rounded-full hover:bg-gray-100 transition-colors" id="notificationBtn" title="Thông báo">
                            <svg class="w-6 h-6 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1 hidden" id="notificationCount">0</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible transition-all duration-300 transform translate-y-2" id="notificationDropdown">
                            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-800 text-lg">Thông báo</h3>
                                <button class="text-sm text-primary hover:underline font-medium" id="markAllReadBtn">Đánh dấu đã đọc</button>
                            </div>
                            <div class="max-h-80 sm:max-h-96 overflow-y-auto" id="notificationList">
                                <div class="p-8 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                    </svg>
                                    <p>Đang tải thông báo...</p>
                                </div>
                            </div>
                            <div class="p-3 border-t border-gray-100 text-center">
                                <a href="notifications.php" class="text-sm text-primary hover:underline font-medium">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <a href="cart.php" class="relative flex items-center justify-center w-11 h-11 rounded-full hover:bg-gray-100 transition-colors" title="Giỏ hàng">
                        <svg class="w-6 h-6 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <span class="cart-count absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center hidden">0</span>
                    </a>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && !$is_admin_preview): ?>
                        <!-- User đã đăng nhập -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-primary/30 rounded-full hover:border-primary transition-all">
                                <?php if (!empty($_SESSION['user_avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" alt="Avatar" class="w-9 h-9 rounded-full object-cover border-2 border-primary" referrerpolicy="no-referrer" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-accent items-center justify-center" style="display:none;">
                                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <span class="hidden lg:block font-semibold text-gray-700 whitespace-nowrap"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <svg class="hidden lg:block w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                            <div class="absolute right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 min-w-[240px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 overflow-hidden z-50">
                                <a href="account.php" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span class="font-medium">Tài khoản của tôi</span>
                                </a>
                                <a href="booking.php" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <span class="font-medium">Đặt lịch của tôi</span>
                                </a>
                                <a href="my-orders.php" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 hover:text-primary transition-all">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                        <line x1="3" y1="6" x2="21" y2="6"/>
                                        <path d="M16 10a4 4 0 0 1-8 0"/>
                                    </svg>
                                    <span class="font-medium">Đơn hàng của tôi</span>
                                </a>
                                <div class="h-px bg-gray-200 my-2"></div>
                                <a href="logout.php" class="flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-red-50 transition-all">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    <span class="font-medium">Đăng xuất</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- User chưa đăng nhập -->
                        <div class="hidden md:flex items-center gap-3">
                            <a href="login.php" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-primary text-primary rounded-full font-semibold text-base hover:bg-primary hover:text-white transition-all transform hover:scale-105">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                                </svg>
                                <span class="whitespace-nowrap">Đăng Nhập</span>
                            </a>
                            <a href="register.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-accent text-white rounded-full font-semibold text-base hover:from-accent hover:to-primary transition-all transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <line x1="20" y1="8" x2="20" y2="14"/>
                                    <line x1="23" y1="11" x2="17" y2="11"/>
                                </svg>
                                <span class="whitespace-nowrap">Đăng Ký</span>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="lg:hidden flex flex-col gap-1.5 w-10 h-10 items-center justify-center rounded-lg hover:bg-gray-100 transition-colors mobile-menu-toggle">
                        <span class="w-6 h-0.5 bg-gray-700 transition-all"></span>
                        <span class="w-6 h-0.5 bg-gray-700 transition-all"></span>
                        <span class="w-6 h-0.5 bg-gray-700 transition-all"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <div class="container">
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Tìm kiếm váy cưới..." required>
                    <button type="submit"><i class="icon-search"></i> Tìm Kiếm</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay fixed inset-0 bg-black/50 z-40 opacity-0 invisible transition-all duration-300 lg:hidden" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu fixed top-0 right-0 h-full w-80 max-w-[85%] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 lg:hidden overflow-y-auto" id="mobileMenu">
        <div class="p-6">
            <!-- Close Button -->
            <button class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors mobile-menu-close">
                <svg class="w-6 h-6 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>

            <!-- Logo -->
            <div class="mb-8">
                <img src="images/Green And White Illustrative Flower Shop Logo.png" alt="<?php echo SITE_NAME; ?>" class="h-16 w-auto">
            </div>

            <!-- Mobile Navigation -->
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base transition-all <?php echo isActive('index.php') ? 'bg-gradient-to-r from-primary to-accent text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Trang chủ</span>
                </a>

                <!-- Products with Submenu -->
                <div class="mobile-submenu-wrapper">
                    <button class="flex items-center justify-between w-full px-4 py-3 rounded-xl font-semibold text-base text-gray-700 hover:bg-gray-100 transition-all mobile-submenu-toggle">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            <span>Váy Cưới</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="mobile-submenu hidden pl-12 pr-4 py-2 space-y-1">
                        <a href="products.php?cat=princess" class="block py-2 text-gray-600 hover:text-primary transition-colors">Váy Công Chúa</a>
                        <a href="products.php?cat=mermaid" class="block py-2 text-gray-600 hover:text-primary transition-colors">Váy Đuôi Cá</a>
                        <a href="products.php?cat=aline" class="block py-2 text-gray-600 hover:text-primary transition-colors">Váy Chữ A</a>
                        <a href="products.php?cat=modern" class="block py-2 text-gray-600 hover:text-primary transition-colors">Váy Hiện Đại</a>
                    </div>
                </div>

                <a href="booking.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base transition-all <?php echo isActive('booking.php') ? 'bg-gradient-to-r from-primary to-accent text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Đặt Lịch</span>
                </a>

                <a href="blog.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base transition-all <?php echo isActive('blog.php') ? 'bg-gradient-to-r from-primary to-accent text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8" stroke="white" stroke-width="2"/>
                    </svg>
                    <span>Tin Tức</span>
                </a>

                <a href="about.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base transition-all <?php echo isActive('about.php') ? 'bg-gradient-to-r from-primary to-accent text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                    <span>Về Chúng Tôi</span>
                </a>

                <a href="contact.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base transition-all <?php echo isActive('contact.php') ? 'bg-gradient-to-r from-primary to-accent text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>Liên Hệ</span>
                </a>
            </nav>

            <!-- Mobile Auth Buttons -->
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && !$is_admin_preview): ?>
            <div class="mt-6 space-y-2 pt-6 border-t border-gray-200">
                <a href="notifications.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base text-gray-700 hover:bg-gray-100 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span>Thông Báo</span>
                    <span class="mobile-notification-badge ml-auto bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 items-center justify-center px-1 hidden"></span>
                </a>
                <a href="account.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base text-gray-700 hover:bg-gray-100 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Tài Khoản</span>
                </a>
                <a href="my-orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base text-gray-700 hover:bg-gray-100 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span>Đơn Hàng</span>
                </a>
                <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-base text-red-600 hover:bg-red-50 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Đăng Xuất</span>
                </a>
            </div>
            <?php else: ?>
            <div class="mt-6 space-y-3 pt-6 border-t border-gray-200">
                <a href="login.php" class="flex items-center justify-center gap-2 w-full px-5 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary hover:text-white transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                    </svg>
                    <span>Đăng Nhập</span>
                </a>
                <a href="register.php" class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-gradient-to-r from-primary to-accent text-white rounded-xl font-semibold hover:from-accent hover:to-primary transition-all shadow-lg">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    <span>Đăng Ký</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Menu Script -->
    <script>
    // Immediate mobile menu functionality
    (function() {
        function initMobileMenu() {
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileMenuClose = document.querySelector('.mobile-menu-close');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

            if (!mobileMenuToggle || !mobileMenu || !mobileMenuOverlay) {
                console.error('Mobile menu elements not found');
                return;
            }

            function openMobileMenu() {
                mobileMenu.classList.remove('translate-x-full');
                mobileMenuOverlay.classList.remove('invisible', 'opacity-0');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                mobileMenu.classList.add('translate-x-full');
                mobileMenuOverlay.classList.add('invisible', 'opacity-0');
                document.body.style.overflow = '';
            }

            mobileMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openMobileMenu();
            });

            if (mobileMenuClose) {
                mobileMenuClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeMobileMenu();
                });
            }

            mobileMenuOverlay.addEventListener('click', closeMobileMenu);

            // Mobile Submenu Toggle
            const mobileSubmenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
            mobileSubmenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    const icon = this.querySelector('svg:last-child');
                    
                    if (submenu) {
                        submenu.classList.toggle('hidden');
                        if (icon) {
                            icon.classList.toggle('rotate-180');
                        }
                    }
                });
            });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobileMenu);
        } else {
            initMobileMenu();
        }
    })();
    </script>

    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && !$is_admin_preview): ?>
    <!-- Notification System -->
    <style>
    /* Notification Wrapper */
    #notificationWrapper {
        position: relative;
    }
    
    /* Notification Dropdown Base Styles */
    .notification-dropdown {
        position: absolute !important;
        right: 0 !important;
        top: 100% !important;
        margin-top: 0.5rem;
        width: 320px;
        max-width: calc(100vw - 2rem);
        z-index: 9999 !important;
    }
    
    .notification-dropdown.show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
    }
    
    .notification-item {
        transition: all 0.2s;
    }
    .notification-item:hover {
        background: #f9fafb;
    }
    .notification-item.unread {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
        border-left: 3px solid #3b82f6;
    }
    .notification-item.unread:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
    }
    .notification-badge {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    /* Mobile: Full-width bottom sheet */
    @media (max-width: 640px) {
        .notification-dropdown {
            position: fixed !important;
            top: auto !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            border-radius: 1rem 1rem 0 0 !important;
            max-height: 75vh !important;
            margin-top: 0;
            transform: translateY(100%) !important;
        }
        
        .notification-dropdown.show {
            transform: translateY(0) !important;
        }
        
        /* Add overlay for mobile */
        .notification-dropdown::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        
        .notification-dropdown.show::before {
            opacity: 1;
        }
    }
    
    /* Tablet and small desktop */
    @media (min-width: 641px) and (max-width: 1024px) {
        .notification-dropdown {
            width: 360px;
            right: 0 !important;
        }
    }
    
    /* Large desktop */
    @media (min-width: 1025px) {
        .notification-dropdown {
            width: 384px;
        }
    }
    </style>
    <script>
    (function() {
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const notificationCount = document.getElementById('notificationCount');
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        
        if (!notificationBtn) return;
        
        let isOpen = false;
        
        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;
            if (isOpen) {
                notificationDropdown.classList.add('show');
                loadNotifications();
            } else {
                notificationDropdown.classList.remove('show');
            }
        });
        
        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && e.target !== notificationBtn) {
                notificationDropdown.classList.remove('show');
                isOpen = false;
            }
        });
        
        // Load notifications
        async function loadNotifications() {
            try {
                const response = await fetch('api/notifications.php?action=get&limit=10');
                const data = await response.json();
                
                if (data.success) {
                    renderNotifications(data.notifications);
                    updateBadge(data.unread_count);
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }
        
        // Render notifications
        function renderNotifications(notifications) {
            if (notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <p>Chưa có thông báo nào</p>
                    </div>
                `;
                return;
            }
            
            notificationList.innerHTML = notifications.map(n => `
                <a href="${n.link || '#'}" class="notification-item block p-4 border-b border-gray-50 ${n.da_doc == 0 ? 'unread' : ''}" 
                   data-id="${n.id}" onclick="markNotificationRead(${n.id})">
                    <div class="flex gap-3">
                        <div class="text-2xl flex-shrink-0">${n.icon}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">${escapeHtml(n.tieu_de)}</p>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2">${escapeHtml(n.noi_dung)}</p>
                            <p class="text-gray-400 text-xs mt-2">${n.time_ago}</p>
                        </div>
                        ${n.da_doc == 0 ? '<div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                    </div>
                </a>
            `).join('');
        }
        
        // Update badge
        function updateBadge(count) {
            if (count > 0) {
                notificationCount.textContent = count > 99 ? '99+' : count;
                notificationCount.classList.remove('hidden');
                notificationCount.classList.add('flex');
            } else {
                notificationCount.classList.add('hidden');
                notificationCount.classList.remove('flex');
            }
        }
        
        // Mark as read
        window.markNotificationRead = async function(id) {
            try {
                const formData = new FormData();
                formData.append('action', 'mark_read');
                formData.append('notification_id', id);
                
                await fetch('api/notifications.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Update UI
                const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('unread');
                    const dot = item.querySelector('.bg-blue-500');
                    if (dot) dot.remove();
                }
                
                // Update count
                const currentCount = parseInt(notificationCount.textContent) || 0;
                if (currentCount > 0) {
                    updateBadge(currentCount - 1);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        };
        
        // Mark all as read
        markAllReadBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const formData = new FormData();
                formData.append('action', 'mark_all_read');
                
                const response = await fetch('api/notifications.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        const dot = item.querySelector('.bg-blue-500');
                        if (dot) dot.remove();
                    });
                    updateBadge(0);
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        });
        
        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Initial load count
        async function loadUnreadCount() {
            try {
                const response = await fetch('api/notifications.php?action=count_unread');
                const data = await response.json();
                if (data.success) {
                    updateBadge(data.unread_count);
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        }
        
        // Load count on page load
        loadUnreadCount();
        
        // Poll for new notifications every 60 seconds
        setInterval(loadUnreadCount, 60000);
    })();
    </script>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
