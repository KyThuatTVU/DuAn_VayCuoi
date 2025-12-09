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
        .chart-container { position: relative; }
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        /* Mobile chart responsive */
        @media (max-width: 767.98px) {
            .chart-container { height: 180px !important; }
            .card.stat-card { padding: 0.875rem !important; }
            .stat-card .text-2xl { font-size: 1.125rem !important; }
            .stat-card .text-xl { font-size: 1rem !important; }
            .stat-card .w-12.h-12 { width: 2.25rem !important; height: 2.25rem !important; }
            .stat-card .w-10.h-10 { width: 2rem !important; height: 2rem !important; }
        }
        @media (max-width: 479.98px) {
            .chart-container { height: 150px !important; }
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
                    <button class="relative text-navy-500 hover:text-navy-700 w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bell text-base sm:text-lg lg:text-xl"></i>
                        <?php if($pending_orders + $new_contacts > 0): ?>
                        <span class="absolute top-0 right-0 sm:top-0 sm:right-0 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center text-[10px]"><?php echo min(99, $pending_orders + $new_contacts); ?></span>
                        <?php endif; ?>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="p-3 sm:p-4 lg:p-6 space-y-4 sm:space-y-6 max-w-full overflow-x-hidden">
                <!-- Stats Cards Row 1 -->
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                    <!-- Doanh thu hôm nay -->
                    <div class="card stat-card rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 shadow-sm border-l-4 border-green-500">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-sm font-medium">Hôm nay</p>
                                <p class="text-2xl font-bold text-navy-900 mt-1"><?php echo number_format($today_revenue/1000000, 1); ?>M</p>
                                <p class="text-xs text-green-600 mt-1"><i class="fas fa-calendar-day mr-1"></i>VNĐ</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-sun text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Doanh thu tuần -->
                    <div class="card stat-card rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 shadow-sm border-l-4 border-blue-500">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-xs sm:text-sm font-medium truncate">Tuần này</p>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-navy-900 mt-0.5 sm:mt-1"><?php echo number_format($week_revenue/1000000, 1); ?>M</p>
                                <p class="text-[10px] sm:text-xs text-blue-600 mt-0.5 sm:mt-1 hidden sm:block"><i class="fas fa-calendar-week mr-1"></i>VNĐ</p>
                            </div>
                            <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-week text-blue-500 text-sm sm:text-base lg:text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Doanh thu tháng -->
                    <div class="card stat-card rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 shadow-sm border-l-4 border-accent-500">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-xs sm:text-sm font-medium truncate">Tháng này</p>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-navy-900 mt-0.5 sm:mt-1"><?php echo number_format($month_revenue/1000000, 1); ?>M</p>
                                <p class="text-[10px] sm:text-xs <?php echo $growth_percent >= 0 ? 'text-green-600' : 'text-red-600'; ?> mt-0.5 sm:mt-1 truncate">
                                    <i class="fas fa-<?php echo $growth_percent >= 0 ? 'arrow-up' : 'arrow-down'; ?> mr-0.5 sm:mr-1"></i>
                                    <span class="hidden sm:inline"><?php echo abs($growth_percent); ?>% vs tháng trước</span>
                                    <span class="sm:hidden"><?php echo abs($growth_percent); ?>%</span>
                                </p>
                            </div>
                            <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-accent-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-accent-500 text-sm sm:text-base lg:text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tổng doanh thu -->
                    <div class="card stat-card rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 shadow-sm border-l-4 border-purple-500">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-xs sm:text-sm font-medium truncate">Tổng doanh thu</p>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-navy-900 mt-0.5 sm:mt-1"><?php echo number_format($total_revenue/1000000, 1); ?>M</p>
                                <p class="text-[10px] sm:text-xs text-purple-600 mt-0.5 sm:mt-1 hidden sm:block"><i class="fas fa-coins mr-1"></i>VNĐ</p>
                            </div>
                            <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-dollar-sign text-purple-500 text-sm sm:text-base lg:text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards Row 2 -->
                <!-- Stats Cards Row 2 -->
                <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-5 gap-2 sm:gap-3 lg:gap-6">
                    <div class="card stat-card rounded-lg sm:rounded-xl lg:rounded-2xl p-2 sm:p-3 lg:p-5 shadow-sm border-l-2 sm:border-l-4 border-navy-600">
                        <div class="flex items-center justify-between gap-1 sm:gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-[10px] sm:text-xs font-medium truncate">Đơn hàng</p>
                                <p class="text-sm sm:text-base lg:text-xl font-bold text-navy-900"><?php echo number_format($total_orders); ?></p>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 bg-navy-100 rounded-md sm:rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shopping-bag text-navy-600 text-xs sm:text-sm lg:text-base"></i>
                            </div>
                        </div>
                    </div>

                    <a href="admin-payments.php" class="card stat-card rounded-lg sm:rounded-xl lg:rounded-2xl p-2 sm:p-3 lg:p-5 shadow-sm border-l-2 sm:border-l-4 border-green-500 hover:shadow-lg">
                        <div class="flex items-center justify-between gap-1 sm:gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-[10px] sm:text-xs font-medium truncate">Thanh toán</p>
                                <p class="text-sm sm:text-base lg:text-xl font-bold text-navy-900"><?php echo number_format($payment_stats['total_success']); ?></p>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 bg-green-100 rounded-md sm:rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-credit-card text-green-500 text-xs sm:text-sm lg:text-base"></i>
                            </div>
                        </div>
                    </a>

                    <div class="card stat-card rounded-lg sm:rounded-xl lg:rounded-2xl p-2 sm:p-3 lg:p-5 shadow-sm border-l-2 sm:border-l-4 border-blue-500">
                        <div class="flex items-center justify-between gap-1 sm:gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-[10px] sm:text-xs font-medium truncate">Khách hàng</p>
                                <p class="text-sm sm:text-base lg:text-xl font-bold text-navy-900"><?php echo number_format($total_users); ?></p>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 bg-blue-100 rounded-md sm:rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users text-blue-500 text-xs sm:text-sm lg:text-base"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card stat-card rounded-lg sm:rounded-xl lg:rounded-2xl p-2 sm:p-3 lg:p-5 shadow-sm border-l-2 sm:border-l-4 border-pink-500">
                        <div class="flex items-center justify-between gap-1 sm:gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-[10px] sm:text-xs font-medium truncate">Váy cưới</p>
                                <p class="text-sm sm:text-base lg:text-xl font-bold text-navy-900"><?php echo number_format($total_dresses); ?></p>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 bg-pink-100 rounded-md sm:rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tshirt text-pink-500 text-xs sm:text-sm lg:text-base"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card stat-card rounded-lg sm:rounded-xl lg:rounded-2xl p-2 sm:p-3 lg:p-5 shadow-sm border-l-2 sm:border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between gap-1 sm:gap-2">
                            <div class="min-w-0">
                                <p class="text-navy-500 text-[10px] sm:text-xs font-medium truncate">Lịch hẹn</p>
                                <p class="text-sm sm:text-base lg:text-xl font-bold text-navy-900"><?php echo number_format($pending_bookings); ?></p>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 bg-yellow-100 rounded-md sm:rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-check text-yellow-500 text-xs sm:text-sm lg:text-base"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1: Doanh thu & Trạng thái đơn hàng -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                    <!-- Biểu đồ doanh thu 12 tháng -->
                    <div class="lg:col-span-2 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3 sm:mb-4">
                            <h3 class="text-sm sm:text-base lg:text-lg font-bold text-navy-900">
                                <i class="fas fa-chart-bar text-accent-500 mr-1 sm:mr-2"></i>Doanh thu 12 tháng
                            </h3>
                            <div class="flex items-center gap-2 sm:gap-4 text-[10px] sm:text-xs lg:text-sm">
                                <span class="flex items-center gap-1 sm:gap-2"><span class="w-2 h-2 sm:w-3 sm:h-3 bg-navy-600 rounded"></span> <span class="hidden xs:inline">Doanh thu</span><span class="xs:hidden">DT</span></span>
                                <span class="flex items-center gap-1 sm:gap-2"><span class="w-2 h-2 sm:w-3 sm:h-3 bg-accent-500 rounded"></span> <span class="hidden xs:inline">Đơn hàng</span><span class="xs:hidden">ĐH</span></span>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 200px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <!-- Biểu đồ trạng thái đơn hàng -->
                    <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 shadow-sm">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-navy-900 mb-3 sm:mb-4">
                            <i class="fas fa-chart-pie text-accent-500 mr-1 sm:mr-2"></i>Trạng thái đơn hàng
                        </h3>
                        <div class="relative chart-container" style="height: 150px;">
                            <canvas id="orderStatusChart"></canvas>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-navy-900"><?php echo $completed_percent; ?>%</p>
                                    <p class="text-navy-500 text-[10px] sm:text-xs lg:text-sm">Hoàn thành</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 grid grid-cols-2 gap-1.5 sm:gap-2">
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Biểu đồ doanh thu theo ngày trong tháng -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-calendar-alt text-accent-500 mr-2"></i>Doanh thu tháng <?php echo $selected_month; ?>/<?php echo $selected_year; ?>
                        </h3>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="dailyRevenueChart"></canvas>
                        </div>
                    </div>

                    <!-- Biểu đồ so sánh năm -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-exchange-alt text-accent-500 mr-2"></i>So sánh <?php echo $current_year; ?> vs <?php echo $last_year; ?>
                        </h3>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="yearCompareChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 3: Top váy cưới & Phương thức thanh toán & Khách hàng mới -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Top váy cưới -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-crown text-yellow-500 mr-2"></i>Top váy được thuê
                        </h3>
                        <div class="chart-container" style="height: 220px;">
                            <canvas id="topDressesChart"></canvas>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-wallet text-green-500 mr-2"></i>Phương thức thanh toán
                        </h3>
                        <div class="chart-container" style="height: 220px;">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>

                    <!-- Khách hàng mới -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-user-plus text-blue-500 mr-2"></i>Khách hàng mới
                        </h3>
                        <div class="chart-container" style="height: 220px;">
                            <canvas id="newUsersChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 4: Trạng thái thanh toán & Lịch hẹn -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Trạng thái thanh toán -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-money-check-alt text-purple-500 mr-2"></i>Trạng thái thanh toán
                        </h3>
                        <div class="chart-container" style="height: 220px;">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Lịch hẹn theo trạng thái -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-calendar-check text-teal-500 mr-2"></i>Lịch hẹn thử váy
                        </h3>
                        <div class="chart-container" style="height: 220px;">
                            <canvas id="bookingStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: Đơn hàng gần đây & Top váy chi tiết -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Đơn hàng gần đây -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-navy-900">
                                <i class="fas fa-clock text-accent-500 mr-2"></i>Đơn hàng gần đây
                            </h3>
                            <a href="admin-orders.php" class="text-accent-500 text-sm hover:underline">Xem tất cả →</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-navy-500 text-sm border-b">
                                        <th class="pb-3 font-medium">Mã đơn</th>
                                        <th class="pb-3 font-medium">Khách hàng</th>
                                        <th class="pb-3 font-medium">Tổng tiền</th>
                                        <th class="pb-3 font-medium">Trạng thái</th>
                                        <th class="pb-3 font-medium">Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 text-sm font-medium text-navy-900"><?php echo htmlspecialchars($order['ma_don_hang'] ?? '#'.$order['id']); ?></td>
                                        <td class="py-3 text-sm text-navy-600"><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                                        <td class="py-3 text-sm font-bold text-accent-500"><?php echo number_format($order['tong_tien']); ?>đ</td>
                                        <td class="py-3">
                                            <?php
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
                                            <span class="text-xs px-2 py-1 rounded-full <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td class="py-3 text-sm text-navy-500"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recent_orders)): ?>
                                    <tr><td colspan="5" class="py-8 text-center text-navy-500">Chưa có đơn hàng nào</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top váy chi tiết -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>Chi tiết top váy
                        </h3>
                        <div class="space-y-3">
                            <?php foreach ($top_dresses as $index => $dress): ?>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                <div class="w-8 h-8 bg-gradient-to-br from-accent-400 to-accent-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-navy-900 truncate"><?php echo htmlspecialchars($dress['ten_vay']); ?></p>
                                    <p class="text-xs text-navy-500"><?php echo htmlspecialchars($dress['ma_vay']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-accent-500"><?php echo $dress['rentals']; ?> lượt</p>
                                    <p class="text-xs text-navy-500"><?php echo number_format($dress['total_revenue']/1000); ?>K</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($top_dresses)): ?>
                            <p class="text-center text-navy-500 py-4">Chưa có dữ liệu</p>
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
        Chart.defaults.plugins.legend.display = false;

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

        // 1. Revenue Chart (Bar + Line)
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'Doanh thu (triệu)',
                        data: monthlyData.map(d => d.revenue / 1000000),
                        backgroundColor: colors.primary,
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Đơn hàng',
                        data: monthlyData.map(d => d.orders),
                        backgroundColor: colors.accent,
                        borderRadius: 6,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, beginAtZero: true }
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
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 3. Daily Revenue Chart (Area)
        new Chart(document.getElementById('dailyRevenueChart'), {
            type: 'line',
            data: {
                labels: dailyRevenue.map(d => d.day),
                datasets: [{
                    label: 'Doanh thu',
                    data: dailyRevenue.map(d => d.revenue / 1000000),
                    borderColor: colors.accent,
                    backgroundColor: 'rgba(237, 137, 54, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.parsed.y.toFixed(1)}M VNĐ`
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, beginAtZero: true }
                }
            }
        });

        // 4. Year Comparison Chart (Line)
        new Chart(document.getElementById('yearCompareChart'), {
            type: 'line',
            data: {
                labels: yearlyComparison.map(d => d.month),
                datasets: [
                    {
                        label: '<?php echo $current_year; ?>',
                        data: yearlyComparison.map(d => d.current / 1000000),
                        borderColor: colors.accent,
                        backgroundColor: 'rgba(237, 137, 54, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: '<?php echo $last_year; ?>',
                        data: yearlyComparison.map(d => d.last / 1000000),
                        borderColor: colors.primary,
                        backgroundColor: 'rgba(51, 78, 104, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, beginAtZero: true }
                }
            }
        });

        // 5. Top Dresses Chart (Horizontal Bar)
        new Chart(document.getElementById('topDressesChart'), {
            type: 'bar',
            data: {
                labels: topDresses.map(d => d.ten_vay.length > 15 ? d.ten_vay.substring(0, 15) + '...' : d.ten_vay),
                datasets: [{
                    label: 'Lượt thuê',
                    data: topDresses.map(d => d.rentals),
                    backgroundColor: [colors.accent, colors.primary, colors.blue, colors.green, colors.purple],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: '#f0f0f0' }, beginAtZero: true },
                    y: { grid: { display: false } }
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
        new Chart(document.getElementById('paymentMethodChart'), {
            type: 'pie',
            data: {
                labels: paymentMethods.map(p => paymentLabels[p.method] || p.method),
                datasets: [{
                    data: paymentMethods.map(p => p.count),
                    backgroundColor: [colors.green, colors.pink, colors.blue, colors.yellow, colors.purple],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 10 } }
                }
            }
        });

        // 7. New Users Chart (Bar)
        new Chart(document.getElementById('newUsersChart'), {
            type: 'bar',
            data: {
                labels: newUsersMonthly.map(d => d.month),
                datasets: [{
                    label: 'Khách hàng mới',
                    data: newUsersMonthly.map(d => d.count),
                    backgroundColor: colors.blue,
                    borderRadius: 6,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, beginAtZero: true, ticks: { stepSize: 1 } }
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
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 10 } }
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
                    backgroundColor: bookingStats.map(b => bookingStatusColors[b.status] || colors.primary).map(c => c + '99'),
                    borderColor: bookingStats.map(b => bookingStatusColors[b.status] || colors.primary),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 10 } }
                },
                scales: {
                    r: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
    
    <!-- Admin Mobile Toggle Button -->
    <button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Admin Sidebar Overlay -->
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>
</body>
</html>
