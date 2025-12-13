<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Admin Dashboard';

// Lấy tháng/năm
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// ========== THỐNG KÊ TỔNG QUAN ==========
$result = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as total FROM don_hang WHERE trang_thai_thanh_toan = 'paid'");
$total_revenue = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM don_hang");
$total_orders = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung");
$total_users = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM vay_cuoi");
$total_dresses = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM don_hang WHERE trang_thai = 'pending'");
$pending_orders = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM lien_he WHERE status = 'new'");
$new_contacts = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM dat_lich_thu_vay WHERE status = 'pending'");
$pending_bookings = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM khuyen_mai WHERE start_at <= NOW() AND end_at >= NOW()");
$active_promotions = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM khuyen_mai WHERE end_at < NOW()");
$expired_promotions = $result->fetch_assoc()['total'];

// Lấy thông báo admin
$admin_notifications = [];
$unread_notifications = 0;
$check_notif_table = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
if ($check_notif_table && $check_notif_table->num_rows > 0) {
    $notif_result = $conn->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 10");
    if ($notif_result) {
        while ($row = $notif_result->fetch_assoc()) {
            $admin_notifications[] = $row;
        }
    }
    $unread_result = $conn->query("SELECT COUNT(*) as cnt FROM admin_notifications WHERE is_read = 0");
    if ($unread_result) {
        $unread_notifications = $unread_result->fetch_assoc()['cnt'];
    }
}

// Thống kê thanh toán
$payment_stats = $conn->query("SELECT 
    COUNT(*) as total_payments,
    SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as total_success_amount,
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as total_success,
    SUM(CASE WHEN status = 'initiated' THEN 1 ELSE 0 END) as total_pending
FROM thanh_toan")->fetch_assoc();

// ========== DỮ LIỆU BIỂU ĐỒ ==========

// 1. Doanh thu theo tháng (12 tháng gần nhất)
$monthly_data = [];
for ($i = 11; $i >= 0; $i--) {
    $m = date('n', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    
    $result = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as revenue, COUNT(*) as orders FROM don_hang 
        WHERE MONTH(created_at) = $m AND YEAR(created_at) = $y AND trang_thai_thanh_toan = 'paid'");
    $row = $result->fetch_assoc();
    
    $monthly_data[] = [
        'month' => $month_name,
        'month_year' => "T$m/$y",
        'revenue' => (float)$row['revenue'],
        'orders' => (int)$row['orders']
    ];
}

// 2. Trạng thái đơn hàng
$order_stats = [];
$statuses = ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
foreach ($statuses as $key => $label) {
    $result = $conn->query("SELECT COUNT(*) as count FROM don_hang WHERE trang_thai = '$key'");
    $order_stats[$key] = (int)$result->fetch_assoc()['count'];
}
$total_order_stats = array_sum($order_stats);
$completed_percent = $total_order_stats > 0 ? round(($order_stats['completed'] / $total_order_stats) * 100) : 0;

// 3. Doanh thu theo ngày trong tháng được chọn
$daily_revenue = [];
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);
for ($d = 1; $d <= $days_in_month; $d++) {
    $result = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as revenue FROM don_hang 
        WHERE DAY(created_at) = $d AND MONTH(created_at) = $selected_month AND YEAR(created_at) = $selected_year 
        AND trang_thai_thanh_toan = 'paid'");
    $daily_revenue[] = [
        'day' => $d,
        'revenue' => (float)$result->fetch_assoc()['revenue']
    ];
}

// 4. Phương thức thanh toán
$payment_methods = $conn->query("SELECT 
    phuong_thuc_thanh_toan as method,
    COUNT(*) as count,
    SUM(tong_tien) as total
FROM don_hang 
WHERE trang_thai_thanh_toan = 'paid'
GROUP BY phuong_thuc_thanh_toan")->fetch_all(MYSQLI_ASSOC);

// 5. Top váy cưới được thuê nhiều nhất
$top_dresses = $conn->query("SELECT 
    vc.id,
    vc.ten_vay, 
    vc.ma_vay,
    COUNT(cthd.id) as rentals,
    COALESCE(SUM(cthd.amount), 0) as total_revenue
FROM vay_cuoi vc 
LEFT JOIN chi_tiet_hoa_don cthd ON vc.id = cthd.vay_id 
GROUP BY vc.id 
ORDER BY rentals DESC 
LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// 6. Khách hàng mới theo tháng (6 tháng gần nhất)
$new_users_monthly = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('n', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    
    $result = $conn->query("SELECT COUNT(*) as count FROM nguoi_dung 
        WHERE MONTH(created_at) = $m AND YEAR(created_at) = $y");
    $new_users_monthly[] = [
        'month' => $month_name,
        'count' => (int)$result->fetch_assoc()['count']
    ];
}

// 7. Trạng thái thanh toán
$payment_status_stats = $conn->query("SELECT 
    trang_thai_thanh_toan as status,
    COUNT(*) as count
FROM don_hang 
GROUP BY trang_thai_thanh_toan")->fetch_all(MYSQLI_ASSOC);

// 8. Lịch hẹn theo trạng thái
$booking_stats = $conn->query("SELECT 
    status,
    COUNT(*) as count
FROM dat_lich_thu_vay 
GROUP BY status")->fetch_all(MYSQLI_ASSOC);

// 9. So sánh doanh thu năm nay vs năm trước
$current_year = date('Y');
$last_year = $current_year - 1;
$yearly_comparison = [];
for ($m = 1; $m <= 12; $m++) {
    $result_current = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as revenue FROM don_hang 
        WHERE MONTH(created_at) = $m AND YEAR(created_at) = $current_year AND trang_thai_thanh_toan = 'paid'");
    $result_last = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as revenue FROM don_hang 
        WHERE MONTH(created_at) = $m AND YEAR(created_at) = $last_year AND trang_thai_thanh_toan = 'paid'");
    
    $yearly_comparison[] = [
        'month' => "T$m",
        'current' => (float)$result_current->fetch_assoc()['revenue'],
        'last' => (float)$result_last->fetch_assoc()['revenue']
    ];
}

// 10. Đơn hàng gần đây
$recent_orders = $conn->query("SELECT * FROM don_hang ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// 11. Doanh thu hôm nay, tuần này, tháng này
$today_revenue = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as total FROM don_hang 
    WHERE DATE(created_at) = CURDATE() AND trang_thai_thanh_toan = 'paid'")->fetch_assoc()['total'];

$week_revenue = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as total FROM don_hang 
    WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND trang_thai_thanh_toan = 'paid'")->fetch_assoc()['total'];

$month_revenue = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as total FROM don_hang 
    WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND trang_thai_thanh_toan = 'paid'")->fetch_assoc()['total'];

// 12. Tăng trưởng so với tháng trước
$last_month_revenue = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as total FROM don_hang 
    WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
    AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
    AND trang_thai_thanh_toan = 'paid'")->fetch_assoc()['total'];
$growth_percent = $last_month_revenue > 0 ? round((($month_revenue - $last_month_revenue) / $last_month_revenue) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="vi" class="admin-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#102a43">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
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
        html.admin-page, body.admin-page { overflow-x: hidden; max-width: 100vw; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.1); border-left: 3px solid #ed8936; }
        .card { transition: all 0.3s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .stat-card { background: linear-gradient(135deg, #fff 0%, #f8fafc 100%); }
        /* Chart container improvements */
        .chart-container { 
            position: relative; 
            width: 100%;
            min-height: 280px;
        }
        .chart-container canvas {
            max-width: 100% !important;
        }
        .chart-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .chart-title {
            font-size: 1rem;
            font-weight: 700;
            color: #102a43;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .chart-title i {
            font-size: 1.1rem;
        }
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        /* Desktop chart sizes */
        @media (min-width: 1024px) {
            .chart-container { min-height: 320px; }
            .chart-container.chart-sm { min-height: 260px; }
            .chart-container.chart-doughnut { min-height: 200px; }
            .chart-card { padding: 1.75rem; }
            .chart-title { font-size: 1.125rem; }
        }
        /* Tablet chart responsive */
        @media (min-width: 768px) and (max-width: 1023px) {
            .chart-container { min-height: 280px; }
            .chart-container.chart-sm { min-height: 240px; }
            .chart-container.chart-doughnut { min-height: 180px; }
        }
        /* Mobile chart responsive */
        @media (max-width: 767.98px) {
            .chart-container { min-height: 200px; }
            .chart-container.chart-sm { min-height: 180px; }
            .chart-container.chart-doughnut { min-height: 150px; }
            .chart-card { padding: 0.875rem; border-radius: 0.75rem; }
            .chart-title { font-size: 0.875rem; margin-bottom: 0.625rem; }
            .chart-title i { font-size: 1rem; }
            .card.stat-card { padding: 0.75rem !important; }
            .stat-card .text-2xl { font-size: 1.125rem !important; }
            .stat-card .text-xl { font-size: 1rem !important; }
            .stat-card .w-12.h-12 { width: 2.25rem !important; height: 2.25rem !important; }
            .stat-card .w-10.h-10 { width: 2rem !important; height: 2rem !important; }
            /* Mobile grid adjustments */
            .grid { gap: 0.75rem !important; }
            /* Stats cards 2 columns on mobile */
            .grid.grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
            .grid.grid-cols-3 { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 479.98px) {
            .chart-container { min-height: 160px; }
            .chart-container.chart-sm { min-height: 150px; }
            .chart-container.chart-doughnut { min-height: 130px; }
            .chart-card { padding: 0.75rem; }
            .chart-title { font-size: 0.8125rem; }
            /* Smaller stat cards */
            .stat-card .text-2xl { font-size: 1rem !important; }
            .stat-card .text-xl { font-size: 0.875rem !important; }
        }
    </style>
</head>
<body class="bg-gray-100 admin-page">
    <div class="flex min-h-screen max-w-full overflow-x-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-navy-900 fixed h-full overflow-y-auto overflow-x-hidden sidebar-scroll z-50 admin-sidebar">
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
                <a href="admin-dashboard.php" class="sidebar-link active flex items-center gap-3 px-4 py-3 text-white rounded">
                    <i class="fas fa-home w-5"></i> Dashboard
                </a>
                <a href="admin-orders.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-shopping-cart w-5"></i> Đơn hàng
                    <?php if($pending_orders > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $pending_orders; ?></span><?php endif; ?>
                </a>
                <a href="admin-dresses.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-tshirt w-5"></i> Váy cưới
                </a>
                <a href="admin-users.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-users w-5"></i> Khách hàng
                </a>
                <a href="admin-bookings.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-calendar w-5"></i> Lịch hẹn
                    <?php if($pending_bookings > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $pending_bookings; ?></span><?php endif; ?>
                </a>
                <a href="admin-promotions.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-gift w-5"></i> Khuyến mãi
                </a>
                <a href="admin-banners.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-bullhorn w-5"></i> Banner Quảng Cáo
                </a>
                <a href="admin-contacts.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-envelope w-5"></i> Liên hệ
                    <?php if($new_contacts > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $new_contacts; ?></span><?php endif; ?>
                </a>
                <a href="admin-blogs.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-newspaper w-5"></i> Tin tức
                </a>
                <a href="admin-comments.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-comments w-5"></i> Bình luận
                </a>
                <a href="admin-payments.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-credit-card w-5"></i> Thanh toán
                    <?php if(isset($payment_stats['total_pending']) && $payment_stats['total_pending'] > 0): ?><span class="ml-auto bg-accent-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $payment_stats['total_pending']; ?></span><?php endif; ?>
                </a>
                <a href="admin-notifications.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                    <i class="fas fa-bell w-5"></i> Thông báo
                    <?php if($unread_notifications > 0): ?><span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $unread_notifications; ?></span><?php endif; ?>
                </a>
                <a href="admin-settings.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
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
                        <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-navy-900 truncate">Dashboard</h1>
                        <p class="text-navy-500 text-xs sm:text-sm hidden sm:block">Tổng quan kinh doanh</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                    <form method="GET" class="hidden md:flex items-center gap-2">
                        <select name="month" class="border border-gray-200 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm focus:ring-2 focus:ring-accent-500">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $m == $selected_month ? 'selected' : ''; ?>>T<?php echo $m; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" class="border border-gray-200 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm focus:ring-2 focus:ring-accent-500">
                            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $y == $selected_year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="bg-accent-500 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg hover:bg-accent-600 transition">
                            <i class="fas fa-filter"></i>
                        </button>
                    </form>
                    
                    <!-- Notification Dropdown -->
                    <div class="relative" id="notificationDropdown">
                        <button onclick="toggleNotificationDropdown(event)" class="relative text-navy-500 hover:text-navy-700 w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-bell text-base sm:text-lg lg:text-xl"></i>
                            <?php if($unread_notifications > 0): ?>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium"><?php echo min(99, $unread_notifications); ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Notification Dropdown Menu (Outside header for proper positioning) -->
            <div id="notificationMenu" class="hidden fixed bg-white rounded-xl shadow-2xl border border-gray-200 z-[9999] w-80" style="max-height: 500px;">
                <div class="p-3 border-b border-gray-100 flex items-center justify-between bg-gray-50 rounded-t-xl">
                    <h3 class="font-semibold text-gray-800 text-sm">Thông báo</h3>
                    <?php if($unread_notifications > 0): ?>
                    <button onclick="markAllAsRead()" class="text-xs text-accent-500 hover:text-accent-600 font-medium">Đánh dấu đã đọc</button>
                    <?php endif; ?>
                </div>
                <div class="overflow-y-auto" style="max-height: 400px;">
                    <?php if (empty($admin_notifications)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                        <p class="text-sm">Không có thông báo nào</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($admin_notifications as $notif): 
                        $icon_class = 'fa-bell';
                        $bg_class = 'bg-gray-100 text-gray-600';
                        if ($notif['type'] === 'account_locked') {
                            $icon_class = 'fa-lock';
                            $bg_class = 'bg-red-100 text-red-600';
                        } elseif ($notif['type'] === 'new_order') {
                            $icon_class = 'fa-shopping-bag';
                            $bg_class = 'bg-green-100 text-green-600';
                        } elseif ($notif['type'] === 'new_contact') {
                            $icon_class = 'fa-envelope';
                            $bg_class = 'bg-blue-100 text-blue-600';
                        } elseif ($notif['type'] === 'new_comment') {
                            $icon_class = 'fa-comment';
                            $bg_class = 'bg-purple-100 text-purple-600';
                        }
                        $time_ago = '';
                        $diff = time() - strtotime($notif['created_at']);
                        if ($diff < 60) $time_ago = 'Vừa xong';
                        elseif ($diff < 3600) $time_ago = floor($diff/60) . ' phút trước';
                        elseif ($diff < 86400) $time_ago = floor($diff/3600) . ' giờ trước';
                        else $time_ago = floor($diff/86400) . ' ngày trước';
                    ?>
                    <div class="p-3 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer <?php echo $notif['is_read'] ? 'opacity-60' : ''; ?>" onclick="handleNotificationClick(<?php echo $notif['id']; ?>, '<?php echo htmlspecialchars($notif['type'] ?? ''); ?>', '<?php echo htmlspecialchars($notif['link'] ?? ''); ?>')">
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full <?php echo $bg_class; ?> flex items-center justify-center flex-shrink-0">
                                <i class="fas <?php echo $icon_class; ?> text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm <?php echo $notif['is_read'] ? '' : 'font-semibold'; ?> truncate"><?php echo htmlspecialchars($notif['title'] ?? ''); ?></p>
                                <p class="text-gray-500 text-xs mt-0.5 line-clamp-2"><?php echo htmlspecialchars($notif['message'] ?? ''); ?></p>
                                <p class="text-gray-400 text-xs mt-1"><?php echo $time_ago; ?></p>
                            </div>
                            <?php if (!$notif['is_read']): ?>
                            <div class="w-2 h-2 bg-accent-500 rounded-full flex-shrink-0 mt-2"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($admin_notifications)): ?>
                <div class="p-3 border-t border-gray-100 text-center bg-white rounded-b-xl">
                    <a href="admin-notifications.php" class="text-sm text-accent-500 hover:text-accent-600 font-medium">Xem tất cả thông báo</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="p-3 sm:p-4 lg:p-6 space-y-3 sm:space-y-4 lg:space-y-6 max-w-full overflow-x-hidden">
                <!-- Stats Cards Row 1 - Doanh thu -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                    <!-- Hôm nay -->
                    <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-gray-500 text-xs lg:text-sm font-medium mb-1">Hôm nay</p>
                        <p class="text-xl lg:text-2xl font-bold text-gray-900"><?php echo number_format($today_revenue/1000000, 1); ?><span class="text-sm font-normal text-gray-400">M</span></p>
                        <p class="text-xs text-gray-400 mt-1">VNĐ</p>
                    </div>

                    <!-- Tuần này -->
                    <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-gray-500 text-xs lg:text-sm font-medium mb-1">Tuần này</p>
                        <p class="text-xl lg:text-2xl font-bold text-gray-900"><?php echo number_format($week_revenue/1000000, 1); ?><span class="text-sm font-normal text-gray-400">M</span></p>
                        <p class="text-xs text-gray-400 mt-1">VNĐ</p>
                    </div>

                    <!-- Tháng này -->
                    <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-gray-500 text-xs lg:text-sm font-medium mb-1">Tháng này</p>
                        <p class="text-xl lg:text-2xl font-bold text-gray-900"><?php echo number_format($month_revenue/1000000, 1); ?><span class="text-sm font-normal text-gray-400">M</span></p>
                        <p class="text-xs mt-1 <?php echo $growth_percent >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $growth_percent >= 0 ? '↑' : '↓'; ?> <?php echo abs($growth_percent); ?>% so với tháng trước
                        </p>
                    </div>

                    <!-- Tổng doanh thu -->
                    <div class="bg-white rounded-xl p-4 lg:p-5 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-gray-500 text-xs lg:text-sm font-medium mb-1">Tổng doanh thu</p>
                        <p class="text-xl lg:text-2xl font-bold text-green-600"><?php echo number_format($total_revenue/1000000, 1); ?><span class="text-sm font-normal text-green-400">M</span></p>
                        <p class="text-xs text-gray-400 mt-1">VNĐ</p>
                    </div>
                </div>

                <!-- Stats Cards Row 2 - Số lượng -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-4">
                    <a href="admin-orders.php" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-xs font-medium">Đơn hàng</p>
                                <p class="text-lg lg:text-xl font-bold text-gray-900 mt-1"><?php echo number_format($total_orders); ?></p>
                            </div>
                            <?php if($pending_orders > 0): ?>
                            <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full"><?php echo $pending_orders; ?> chờ</span>
                            <?php endif; ?>
                        </div>
                    </a>

                    <a href="admin-payments.php" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-xs font-medium">Thanh toán</p>
                                <p class="text-lg lg:text-xl font-bold text-gray-900 mt-1"><?php echo number_format($payment_stats['total_success']); ?></p>
                            </div>
                            <?php if(isset($payment_stats['total_pending']) && $payment_stats['total_pending'] > 0): ?>
                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full"><?php echo $payment_stats['total_pending']; ?> chờ</span>
                            <?php endif; ?>
                        </div>
                    </a>

                    <a href="admin-users.php" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all group">
                        <p class="text-gray-500 text-xs font-medium">Khách hàng</p>
                        <p class="text-lg lg:text-xl font-bold text-gray-900 mt-1"><?php echo number_format($total_users); ?></p>
                    </a>

                    <a href="admin-dresses.php" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all group">
                        <p class="text-gray-500 text-xs font-medium">Váy cưới</p>
                        <p class="text-lg lg:text-xl font-bold text-gray-900 mt-1"><?php echo number_format($total_dresses); ?></p>
                    </a>

                    <a href="admin-bookings.php" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-xs font-medium">Lịch hẹn</p>
                                <p class="text-lg lg:text-xl font-bold text-gray-900 mt-1"><?php echo number_format($pending_bookings); ?></p>
                            </div>
                            <?php if($pending_bookings > 0): ?>
                            <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full">chờ xử lý</span>
                            <?php endif; ?>
                        </div>
                    </a>

                    <a href="admin-promotions.php" class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-xs font-medium">Khuyến mãi</p>
                                <p class="text-lg lg:text-xl font-bold text-gray-900 mt-1"><?php echo number_format($active_promotions); ?></p>
                            </div>
                            <?php if($expired_promotions > 0): ?>
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full"><?php echo $expired_promotions; ?> hết hạn</span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>

                <!-- Charts Row 1: Doanh thu & Trạng thái đơn hàng -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-6">
                    <!-- Biểu đồ doanh thu 12 tháng -->
                    <div class="lg:col-span-2 chart-card">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3 sm:mb-4">
                            <h3 class="chart-title">
                                <i class="fas fa-chart-bar text-accent-500"></i>
                                <span>Doanh thu 12 tháng</span>
                            </h3>
                            <div class="flex items-center gap-3 sm:gap-4 text-[10px] sm:text-xs lg:text-sm">
                                <span class="flex items-center gap-1 sm:gap-2">
                                    <span class="w-2 h-2 sm:w-3 sm:h-3 bg-navy-600 rounded"></span>
                                    <span>Doanh thu</span>
                                </span>
                                <span class="flex items-center gap-1 sm:gap-2">
                                    <span class="w-2 h-2 sm:w-3 sm:h-3 bg-accent-500 rounded"></span>
                                    <span>Đơn hàng</span>
                                </span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <!-- Biểu đồ trạng thái đơn hàng -->
                    <div class="chart-card">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-pie text-accent-500"></i>
                            <span>Trạng thái đơn hàng</span>
                        </h3>
                        <div class="relative chart-container chart-doughnut flex items-center justify-center">
                            <canvas id="orderStatusChart"></canvas>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <p class="text-2xl lg:text-3xl font-bold text-navy-900"><?php echo $completed_percent; ?>%</p>
                                    <p class="text-navy-500 text-xs lg:text-sm">Hoàn thành</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <div class="flex items-center gap-1 sm:gap-2 text-[10px] sm:text-xs lg:text-sm p-1.5 sm:p-2 bg-green-50 rounded-md sm:rounded-lg">
                                <span class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full flex-shrink-0"></span>
                                <span class="text-navy-600 truncate">Hoàn thành</span>
                                <span class="ml-auto font-bold"><?php echo $order_stats['completed']; ?></span>
                            </div>
                            <div class="flex items-center gap-1 sm:gap-2 text-[10px] sm:text-xs lg:text-sm p-1.5 sm:p-2 bg-yellow-50 rounded-md sm:rounded-lg">
                                <span class="w-2 h-2 sm:w-3 sm:h-3 bg-yellow-500 rounded-full flex-shrink-0"></span>
                                <span class="text-navy-600 truncate">Chờ xử lý</span>
                                <span class="ml-auto font-bold"><?php echo $order_stats['pending']; ?></span>
                            </div>
                            <div class="flex items-center gap-1 sm:gap-2 text-[10px] sm:text-xs lg:text-sm p-1.5 sm:p-2 bg-blue-50 rounded-md sm:rounded-lg">
                                <span class="w-2 h-2 sm:w-3 sm:h-3 bg-blue-500 rounded-full flex-shrink-0"></span>
                                <span class="text-navy-600 truncate">Đang xử lý</span>
                                <span class="ml-auto font-bold"><?php echo $order_stats['processing']; ?></span>
                            </div>
                            <div class="flex items-center gap-1 sm:gap-2 text-[10px] sm:text-xs lg:text-sm p-1.5 sm:p-2 bg-red-50 rounded-md sm:rounded-lg">
                                <span class="w-2 h-2 sm:w-3 sm:h-3 bg-red-500 rounded-full flex-shrink-0"></span>
                                <span class="text-navy-600 truncate">Đã hủy</span>
                                <span class="ml-auto font-bold"><?php echo $order_stats['cancelled']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 2: Doanh thu theo ngày & So sánh năm -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-6">
                    <!-- Biểu đồ doanh thu theo ngày trong tháng -->
                    <div class="chart-card">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-2 mb-3 sm:mb-4">
                            <h3 class="chart-title">
                                <i class="fas fa-calendar-alt text-blue-500"></i>
                                <span class="truncate">Doanh thu T<?php echo $selected_month; ?>/<?php echo $selected_year; ?></span>
                            </h3>
                            <span class="text-[10px] sm:text-xs text-navy-500 bg-gray-100 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full hidden sm:inline-block">
                                Theo ngày
                            </span>
                        </div>
                        <div class="chart-container">
                            <canvas id="dailyRevenueChart"></canvas>
                        </div>
                    </div>

                    <!-- Biểu đồ so sánh năm -->
                    <div class="chart-card">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-2 mb-3 sm:mb-4">
                            <h3 class="chart-title">
                                <i class="fas fa-exchange-alt text-purple-500"></i>
                                <span class="truncate">So sánh năm</span>
                            </h3>
                            <div class="flex items-center gap-2 sm:gap-3 text-[10px] sm:text-xs">
                                <span class="flex items-center gap-1">
                                    <span class="w-2 h-2 sm:w-3 sm:h-3 bg-accent-500 rounded"></span>
                                    <span><?php echo $current_year; ?></span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="w-2 h-2 sm:w-3 sm:h-3 bg-navy-600 rounded"></span>
                                    <span><?php echo $last_year; ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="yearCompareChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 3: Top váy cưới & Phương thức thanh toán & Khách hàng mới -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6">
                    <!-- Top váy cưới -->
                    <div class="chart-card">
                        <h3 class="chart-title">
                            <i class="fas fa-crown text-yellow-500"></i>
                            <span class="truncate">Top váy được thuê</span>
                        </h3>
                        <div class="chart-container chart-sm">
                            <canvas id="topDressesChart"></canvas>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="chart-card">
                        <h3 class="chart-title">
                            <i class="fas fa-wallet text-green-500"></i>
                            <span class="truncate">Phương thức thanh toán</span>
                        </h3>
                        <div class="chart-container chart-sm flex items-center justify-center">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>

                    <!-- Khách hàng mới -->
                    <div class="chart-card sm:col-span-2 lg:col-span-1">
                        <h3 class="chart-title">
                            <i class="fas fa-user-plus text-blue-500"></i>
                            <span class="truncate">Khách hàng mới</span>
                        </h3>
                        <div class="chart-container chart-sm">
                            <canvas id="newUsersChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 4: Trạng thái thanh toán & Lịch hẹn -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-6">
                    <!-- Trạng thái thanh toán -->
                    <div class="chart-card">
                        <h3 class="chart-title">
                            <i class="fas fa-money-check-alt text-purple-500"></i>
                            <span class="truncate">Trạng thái thanh toán</span>
                        </h3>
                        <div class="chart-container chart-sm flex items-center justify-center">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Lịch hẹn theo trạng thái -->
                    <div class="chart-card">
                        <h3 class="chart-title">
                            <i class="fas fa-calendar-check text-teal-500"></i>
                            <span class="truncate">Lịch hẹn thử váy</span>
                        </h3>
                        <div class="chart-container chart-sm flex items-center justify-center">
                            <canvas id="bookingStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: Đơn hàng gần đây & Top váy chi tiết -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-6">
                    <!-- Đơn hàng gần đây -->
                    <div class="lg:col-span-2 chart-card">
                        <div class="flex items-center justify-between gap-2 mb-3 sm:mb-4">
                            <h3 class="chart-title">
                                <i class="fas fa-clock text-accent-500"></i>
                                <span>Đơn hàng gần đây</span>
                            </h3>
                            <a href="admin-orders.php" class="text-accent-500 text-xs sm:text-sm font-medium hover:text-accent-600 transition flex items-center gap-1 whitespace-nowrap">
                                <span>Xem tất cả</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                        
                        <!-- Mobile: Card View -->
                        <div class="md:hidden space-y-3">
                            <?php foreach ($recent_orders as $order): 
                                $status_class = match($order['trang_thai']) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $status_text = match($order['trang_thai']) {
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                    default => $order['trang_thai']
                                };
                            ?>
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-navy-900 truncate"><?php echo htmlspecialchars($order['ho_ten']); ?></p>
                                        <p class="text-xs text-navy-400 font-mono truncate"><?php echo htmlspecialchars($order['ma_don_hang'] ?? '#'.$order['id']); ?></p>
                                    </div>
                                    <span class="text-[10px] px-2 py-1 rounded-full font-medium flex-shrink-0 <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-navy-500"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                                    <span class="font-bold text-accent-500"><?php echo number_format($order['tong_tien']); ?>đ</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($recent_orders)): ?>
                            <div class="py-8 text-center">
                                <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                                <p class="text-navy-500 text-sm">Chưa có đơn hàng nào</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Desktop: Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-navy-500 text-sm border-b border-gray-100">
                                        <th class="pb-3 font-semibold">Mã đơn</th>
                                        <th class="pb-3 font-semibold">Khách hàng</th>
                                        <th class="pb-3 font-semibold text-right">Tổng tiền</th>
                                        <th class="pb-3 font-semibold text-center">Trạng thái</th>
                                        <th class="pb-3 font-semibold text-right">Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="py-3.5 text-sm font-medium text-navy-900">
                                            <code class="bg-gray-100 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars($order['ma_don_hang'] ?? '#'.$order['id']); ?></code>
                                        </td>
                                        <td class="py-3.5 text-sm text-navy-600 font-medium"><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                                        <td class="py-3.5 text-sm font-bold text-accent-500 text-right"><?php echo number_format($order['tong_tien']); ?>đ</td>
                                        <td class="py-3.5 text-center">
                                            <?php
                                            $status_class = match($order['trang_thai']) {
                                                'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                                'processing' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                                'completed' => 'bg-green-100 text-green-700 border border-green-200',
                                                'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                                                default => 'bg-gray-100 text-gray-700 border border-gray-200'
                                            };
                                            $status_text = match($order['trang_thai']) {
                                                'pending' => 'Chờ xử lý',
                                                'processing' => 'Đang xử lý',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy',
                                                default => $order['trang_thai']
                                            };
                                            ?>
                                            <span class="text-xs px-2 py-1 rounded-full font-medium <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td class="py-3.5 text-sm text-navy-500 text-right"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recent_orders)): ?>
                                    <tr>
                                        <td colspan="5" class="py-12 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="fas fa-inbox text-3xl text-gray-300"></i>
                                                <p class="text-navy-500">Chưa có đơn hàng nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top váy chi tiết -->
                    <div class="chart-card">
                        <h3 class="chart-title mb-3 sm:mb-4">
                            <i class="fas fa-star text-yellow-500"></i>
                            <span>Chi tiết top váy</span>
                        </h3>
                        <div class="space-y-2 sm:space-y-3">
                            <?php foreach ($top_dresses as $index => $dress): ?>
                            <div class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 bg-gradient-to-r from-gray-50 to-white rounded-lg sm:rounded-xl border border-gray-100 hover:border-accent-200 hover:shadow-sm transition-all group">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-accent-400 to-accent-600 rounded-lg flex items-center justify-center text-white font-bold text-xs sm:text-sm shadow-sm flex-shrink-0">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-navy-900 truncate"><?php echo htmlspecialchars($dress['ten_vay']); ?></p>
                                    <p class="text-[10px] sm:text-xs text-navy-400 font-mono"><?php echo htmlspecialchars($dress['ma_vay']); ?></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs sm:text-sm font-bold text-accent-500"><?php echo $dress['rentals']; ?> lượt</p>
                                    <p class="text-[10px] sm:text-xs text-navy-400"><?php echo number_format($dress['total_revenue']/1000); ?>K</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($top_dresses)): ?>
                            <div class="py-6 sm:py-8 text-center">
                                <i class="fas fa-tshirt text-2xl sm:text-3xl text-gray-300 mb-2"></i>
                                <p class="text-navy-500 text-xs sm:text-sm">Chưa có dữ liệu</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Charts Script -->
    <script>
        // Data from PHP
        const monthlyData = <?php echo json_encode($monthly_data); ?>;
        const orderStats = <?php echo json_encode($order_stats); ?>;
        const dailyRevenue = <?php echo json_encode($daily_revenue); ?>;
        const paymentMethods = <?php echo json_encode($payment_methods); ?>;
        const topDresses = <?php echo json_encode($top_dresses); ?>;
        const newUsersMonthly = <?php echo json_encode($new_users_monthly); ?>;
        const paymentStatusStats = <?php echo json_encode($payment_status_stats); ?>;
        const bookingStats = <?php echo json_encode($booking_stats); ?>;
        const yearlyComparison = <?php echo json_encode($yearly_comparison); ?>;

        // Chart defaults
        Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.animation.duration = 750;
        Chart.defaults.animation.easing = 'easeInOutQuart';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(16, 42, 67, 0.9)';
        Chart.defaults.plugins.tooltip.titleColor = '#fff';
        Chart.defaults.plugins.tooltip.bodyColor = '#e2e8f0';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.displayColors = true;
        Chart.defaults.plugins.tooltip.boxPadding = 4;

        // Color palette
        const colors = {
            primary: '#334e68',
            accent: '#ed8936',
            green: '#10b981',
            yellow: '#f59e0b',
            blue: '#3b82f6',
            red: '#ef4444',
            purple: '#8b5cf6',
            pink: '#ec4899',
            teal: '#14b8a6',
            indigo: '#6366f1'
        };
        
        // Gradient helpers
        function createGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        // 1. Revenue Chart (Bar + Line)
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
        revenueGradient.addColorStop(0, colors.primary);
        revenueGradient.addColorStop(1, 'rgba(51, 78, 104, 0.6)');
        
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'Doanh thu (triệu)',
                        data: monthlyData.map(d => d.revenue / 1000000),
                        backgroundColor: revenueGradient,
                        borderRadius: 8,
                        barPercentage: 0.65,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Đơn hàng',
                        data: monthlyData.map(d => d.orders),
                        backgroundColor: colors.accent,
                        borderRadius: 8,
                        barPercentage: 0.65,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.dataset.label === 'Doanh thu (triệu)') {
                                    return `Doanh thu: ${ctx.parsed.y.toFixed(1)}M VNĐ`;
                                }
                                return `Đơn hàng: ${ctx.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: { 
                        type: 'linear',
                        position: 'left',
                        grid: { color: 'rgba(0,0,0,0.05)' }, 
                        beginAtZero: true,
                        title: { display: true, text: 'Doanh thu (triệu)', font: { size: 11 } }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: { display: false },
                        beginAtZero: true,
                        title: { display: true, text: 'Đơn hàng', font: { size: 11 } }
                    }
                }
            }
        });

        // 2. Order Status Chart (Doughnut)
        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Hoàn thành', 'Chờ xử lý', 'Đang xử lý', 'Đã hủy'],
                datasets: [{
                    data: [orderStats.completed, orderStats.pending, orderStats.processing, orderStats.cancelled],
                    backgroundColor: [colors.green, colors.yellow, colors.blue, colors.red],
                    hoverBackgroundColor: ['#059669', '#d97706', '#2563eb', '#dc2626'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    cutout: '68%',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 3. Daily Revenue Chart (Area)
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyGradient = dailyCtx.createLinearGradient(0, 0, 0, 280);
        dailyGradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        dailyGradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');
        
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyRevenue.map(d => d.day),
                datasets: [{
                    label: 'Doanh thu',
                    data: dailyRevenue.map(d => d.revenue / 1000000),
                    borderColor: colors.blue,
                    backgroundColor: dailyGradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: colors.blue,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    borderWidth: 2.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (ctx) => `Ngày ${ctx[0].label}`,
                            label: (ctx) => `Doanh thu: ${ctx.parsed.y.toFixed(2)}M VNĐ`
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            maxTicksLimit: 15,
                            font: { size: 10 }
                        }
                    },
                    y: { 
                        grid: { color: 'rgba(0,0,0,0.05)' }, 
                        beginAtZero: true,
                        ticks: { 
                            callback: (val) => val.toFixed(1) + 'M',
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // 4. Year Comparison Chart (Line)
        const yearCtx = document.getElementById('yearCompareChart').getContext('2d');
        const yearGradient1 = yearCtx.createLinearGradient(0, 0, 0, 280);
        yearGradient1.addColorStop(0, 'rgba(237, 137, 54, 0.25)');
        yearGradient1.addColorStop(1, 'rgba(237, 137, 54, 0.02)');
        const yearGradient2 = yearCtx.createLinearGradient(0, 0, 0, 280);
        yearGradient2.addColorStop(0, 'rgba(51, 78, 104, 0.2)');
        yearGradient2.addColorStop(1, 'rgba(51, 78, 104, 0.02)');
        
        new Chart(yearCtx, {
            type: 'line',
            data: {
                labels: yearlyComparison.map(d => d.month),
                datasets: [
                    {
                        label: '<?php echo $current_year; ?>',
                        data: yearlyComparison.map(d => d.current / 1000000),
                        borderColor: colors.accent,
                        backgroundColor: yearGradient1,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.accent,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 2.5
                    },
                    {
                        label: '<?php echo $last_year; ?>',
                        data: yearlyComparison.map(d => d.last / 1000000),
                        borderColor: colors.primary,
                        backgroundColor: yearGradient2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 2.5,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y.toFixed(1)}M VNĐ`
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: { 
                        grid: { color: 'rgba(0,0,0,0.05)' }, 
                        beginAtZero: true,
                        ticks: { 
                            callback: (val) => val.toFixed(0) + 'M',
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // 5. Top Dresses Chart (Horizontal Bar)
        const topDressesColors = [
            'rgba(237, 137, 54, 0.85)', 
            'rgba(51, 78, 104, 0.85)', 
            'rgba(59, 130, 246, 0.85)', 
            'rgba(16, 185, 129, 0.85)', 
            'rgba(139, 92, 246, 0.85)'
        ];
        new Chart(document.getElementById('topDressesChart'), {
            type: 'bar',
            data: {
                labels: topDresses.map(d => d.ten_vay.length > 18 ? d.ten_vay.substring(0, 18) + '...' : d.ten_vay),
                datasets: [{
                    label: 'Lượt thuê',
                    data: topDresses.map(d => d.rentals),
                    backgroundColor: topDressesColors,
                    hoverBackgroundColor: [colors.accent, colors.primary, colors.blue, colors.green, colors.purple],
                    borderRadius: 8,
                    barThickness: 24
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.parsed.x} lượt thuê`
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { color: 'rgba(0,0,0,0.05)' }, 
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } }
                    },
                    y: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });

        // 6. Payment Methods Chart (Pie)
        const paymentLabels = {
            'qr_code': 'QR Code',
            'momo': 'MoMo',
            'bank_transfer': 'Chuyển khoản',
            'cash': 'Tiền mặt',
            'vnpay': 'VNPay'
        };
        const paymentColors = [colors.green, colors.pink, colors.blue, colors.yellow, colors.purple, colors.teal];
        new Chart(document.getElementById('paymentMethodChart'), {
            type: 'doughnut',
            data: {
                labels: paymentMethods.map(p => paymentLabels[p.method] || p.method),
                datasets: [{
                    data: paymentMethods.map(p => p.count),
                    backgroundColor: paymentColors.slice(0, paymentMethods.length),
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8,
                    cutout: '50%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true, 
                        position: 'bottom', 
                        labels: { 
                            boxWidth: 14, 
                            padding: 12, 
                            usePointStyle: true, 
                            pointStyle: 'circle',
                            font: { size: 11 }
                        } 
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = Math.round((ctx.parsed / total) * 100);
                                return `${ctx.label}: ${ctx.parsed} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });

        // 7. New Users Chart (Bar)
        const usersCtx = document.getElementById('newUsersChart').getContext('2d');
        const usersGradient = usersCtx.createLinearGradient(0, 0, 0, 250);
        usersGradient.addColorStop(0, colors.blue);
        usersGradient.addColorStop(1, 'rgba(59, 130, 246, 0.5)');
        
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: newUsersMonthly.map(d => d.month),
                datasets: [{
                    label: 'Khách hàng mới',
                    data: newUsersMonthly.map(d => d.count),
                    backgroundColor: usersGradient,
                    hoverBackgroundColor: colors.blue,
                    borderRadius: 8,
                    barPercentage: 0.7,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.parsed.y} khách hàng mới`
                        }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: { 
                        grid: { color: 'rgba(0,0,0,0.05)' }, 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, font: { size: 10 } }
                    }
                }
            }
        });

        // 8. Payment Status Chart (Doughnut)
        const paymentStatusLabels = {
            'pending': 'Chờ thanh toán',
            'paid': 'Đã thanh toán',
            'failed': 'Thất bại',
            'expired': 'Hết hạn'
        };
        const paymentStatusColors = {
            'pending': colors.yellow,
            'paid': colors.green,
            'failed': colors.red,
            'expired': colors.primary
        };
        new Chart(document.getElementById('paymentStatusChart'), {
            type: 'doughnut',
            data: {
                labels: paymentStatusStats.map(p => paymentStatusLabels[p.status] || p.status),
                datasets: [{
                    data: paymentStatusStats.map(p => p.count),
                    backgroundColor: paymentStatusStats.map(p => paymentStatusColors[p.status] || colors.primary),
                    hoverBackgroundColor: paymentStatusStats.map(p => {
                        const c = paymentStatusColors[p.status] || colors.primary;
                        return c;
                    }),
                    borderWidth: 3,
                    borderColor: '#fff',
                    cutout: '55%',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true, 
                        position: 'bottom', 
                        labels: { 
                            boxWidth: 14, 
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 }
                        } 
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = Math.round((ctx.parsed / total) * 100);
                                return `${ctx.label}: ${ctx.parsed} đơn (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });

        // 9. Booking Status Chart (Polar Area)
        const bookingStatusLabels = {
            'pending': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'attended': 'Đã đến',
            'cancelled': 'Đã hủy'
        };
        const bookingStatusColors = {
            'pending': colors.yellow,
            'confirmed': colors.blue,
            'attended': colors.green,
            'cancelled': colors.red
        };
        new Chart(document.getElementById('bookingStatusChart'), {
            type: 'polarArea',
            data: {
                labels: bookingStats.map(b => bookingStatusLabels[b.status] || b.status),
                datasets: [{
                    data: bookingStats.map(b => b.count),
                    backgroundColor: bookingStats.map(b => {
                        const color = bookingStatusColors[b.status] || colors.primary;
                        return color + 'cc';
                    }),
                    borderColor: bookingStats.map(b => bookingStatusColors[b.status] || colors.primary),
                    borderWidth: 2,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true, 
                        position: 'bottom', 
                        labels: { 
                            boxWidth: 14, 
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 }
                        } 
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${ctx.parsed.r} lịch hẹn`
                        }
                    }
                },
                scales: {
                    r: { 
                        beginAtZero: true, 
                        ticks: { 
                            stepSize: 1,
                            display: true,
                            font: { size: 10 },
                            backdropColor: 'transparent'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.08)'
                        },
                        pointLabels: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // ========== NOTIFICATION DROPDOWN ==========
        function toggleNotificationDropdown(event) {
            event.stopPropagation();
            const button = document.querySelector('#notificationDropdown button');
            const menu = document.getElementById('notificationMenu');
            const isHidden = menu.classList.contains('hidden');
            
            if (isHidden) {
                // Tính vị trí của button
                const rect = button.getBoundingClientRect();
                
                // Đặt vị trí dropdown
                menu.style.top = (rect.bottom + 8) + 'px';
                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left = 'auto';
                
                menu.classList.remove('hidden');
                
                // Kiểm tra nếu tràn bên phải
                setTimeout(() => {
                    const menuRect = menu.getBoundingClientRect();
                    if (menuRect.right > window.innerWidth - 10) {
                        menu.style.right = '10px';
                    }
                    if (menuRect.left < 10) {
                        menu.style.left = '10px';
                        menu.style.right = 'auto';
                    }
                }, 0);
            } else {
                menu.classList.add('hidden');
            }
        }
        
        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            const menu = document.getElementById('notificationMenu');
            if (dropdown && menu && !dropdown.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
        
        // Đóng khi scroll
        window.addEventListener('scroll', function() {
            const menu = document.getElementById('notificationMenu');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });
        
        // Xử lý click vào thông báo
        function handleNotificationClick(id, type, link) {
            // Đánh dấu đã đọc
            fetch('api/admin-notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=mark_read&id=' + id
            });
            
            // Chuyển hướng dựa trên link hoặc type
            if (link && link !== '') {
                window.location.href = link;
            } else if (type === 'account_locked') {
                window.location.href = 'admin-users.php';
            } else if (type === 'new_order') {
                window.location.href = 'admin-orders.php';
            } else if (type === 'new_contact') {
                window.location.href = 'admin-contacts.php';
            } else if (type === 'new_comment') {
                // Comment notifications should have link, but fallback to products page
                window.location.href = link || 'products.php';
            } else {
                window.location.href = 'admin-notifications.php';
            }
        }
        
        // Đánh dấu tất cả đã đọc
        function markAllAsRead() {
            fetch('api/admin-notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=mark_all_read'
            }).then(() => {
                location.reload();
            });
        }
    </script>
    
    <!-- Admin Mobile Toggle Button -->
    <button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Admin Sidebar Overlay -->
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>
</body>
</html>
