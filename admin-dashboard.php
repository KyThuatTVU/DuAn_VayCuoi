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

// ========== THỐNG KÊ ==========
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

// Doanh thu theo tháng (12 tháng)
$monthly_data = [];
for ($i = 11; $i >= 0; $i--) {
    $m = date('n', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    
    $result = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as revenue, COUNT(*) as orders FROM don_hang 
        WHERE MONTH(created_at) = $m AND YEAR(created_at) = $y");
    $row = $result->fetch_assoc();
    
    $monthly_data[] = [
        'month' => $month_name,
        'revenue' => (float)$row['revenue'],
        'orders' => (int)$row['orders']
    ];
}

// Trạng thái đơn hàng
$order_stats = [];
$statuses = ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
foreach ($statuses as $key => $label) {
    $result = $conn->query("SELECT COUNT(*) as count FROM don_hang WHERE trang_thai = '$key'");
    $order_stats[$key] = (int)$result->fetch_assoc()['count'];
}
$total_order_stats = array_sum($order_stats);
$completed_percent = $total_order_stats > 0 ? round(($order_stats['completed'] / $total_order_stats) * 100) : 0;

// Đơn hàng gần đây
$recent_orders = $conn->query("SELECT * FROM don_hang ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Top váy cưới
$top_dresses = $conn->query("SELECT vc.ten_vay, COUNT(cthd.id) as rentals FROM vay_cuoi vc 
    LEFT JOIN chi_tiet_hoa_don cthd ON vc.id = cthd.vay_id 
    GROUP BY vc.id ORDER BY rentals DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <style>
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.1); border-left: 3px solid #ed8936; }
        .card { transition: all 0.3s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-56 bg-navy-900 fixed h-full">
            <!-- Profile -->
            <div class="p-4 text-center border-b border-navy-700">
                <div class="w-16 h-16 mx-auto bg-navy-700 rounded-full flex items-center justify-center mb-3 overflow-hidden">
                    <?php if (!empty($_SESSION['admin_avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['admin_avatar']); ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-user text-3xl text-accent-500"></i>
                    <?php endif; ?>
                </div>
                <h3 class="text-white font-semibold text-base truncate px-2"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></h3>
                <p class="text-navy-300 text-xs truncate px-2" title="<?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?>">
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
                <div class="border-t border-navy-700 mt-4 pt-4">
                    <a href="index.php" target="_blank" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded">
                        <i class="fas fa-external-link-alt w-5"></i> Xem website
                    </a>
                    <a href="admin-logout.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded mt-1">
                        <i class="fas fa-sign-out-alt w-5"></i> Đăng xuất
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-56">
            <!-- Header -->
            <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-navy-900">Dashboard</h1>
                    <p class="text-navy-500 text-sm">Chào mừng trở lại!</p>
                </div>
                <div class="flex items-center gap-4">
                    <form method="GET" class="flex items-center gap-2">
                        <select name="month" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $m == $selected_month ? 'selected' : ''; ?>>Tháng <?php echo $m; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $y == $selected_year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition">
                            <i class="fas fa-filter"></i>
                        </button>
                    </form>
                    <button class="relative text-navy-500 hover:text-navy-700">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if($pending_orders + $new_contacts > 0): ?>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?php echo $pending_orders + $new_contacts; ?></span>
                        <?php endif; ?>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Doanh thu -->
                    <div class="card bg-white rounded-2xl p-6 shadow-sm border-l-4 border-accent-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-navy-500 text-sm font-medium">Doanh thu</p>
                                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($total_revenue/1000000, 1); ?>M</p>
                            </div>
                            <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-accent-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn hàng -->
                    <div class="card bg-white rounded-2xl p-6 shadow-sm border-l-4 border-navy-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-navy-500 text-sm font-medium">Đơn hàng</p>
                                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($total_orders); ?></p>
                            </div>
                            <div class="w-12 h-12 bg-navy-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-navy-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Khách hàng -->
                    <div class="card bg-white rounded-2xl p-6 shadow-sm border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-navy-500 text-sm font-medium">Khách hàng</p>
                                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($total_users); ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Rating/Váy cưới -->
                    <div class="card bg-white rounded-2xl p-6 shadow-sm border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-navy-500 text-sm font-medium">Váy cưới</p>
                                <p class="text-3xl font-bold text-navy-900 mt-1"><?php echo number_format($total_dresses); ?></p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-star text-yellow-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Bar Chart -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-navy-900">Kết quả kinh doanh</h3>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-navy-600 rounded"></span> Doanh thu</span>
                                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-accent-500 rounded"></span> Đơn hàng</span>
                            </div>
                        </div>
                        <div style="height: 250px;">
                            <canvas id="resultChart"></canvas>
                        </div>
                    </div>

                    <!-- Doughnut Chart -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-navy-900 mb-4">Tỷ lệ hoàn thành</h3>
                        <div class="relative" style="height: 200px;">
                            <canvas id="completionChart"></canvas>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-navy-900"><?php echo $completed_percent; ?>%</p>
                                    <p class="text-navy-500 text-sm">Hoàn thành</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span> Hoàn thành</span>
                                <span class="font-medium"><?php echo $order_stats['completed']; ?></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2"><span class="w-2 h-2 bg-yellow-500 rounded-full"></span> Chờ xử lý</span>
                                <span class="font-medium"><?php echo $order_stats['pending']; ?></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2"><span class="w-2 h-2 bg-blue-500 rounded-full"></span> Đang xử lý</span>
                                <span class="font-medium"><?php echo $order_stats['processing']; ?></span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2"><span class="w-2 h-2 bg-red-500 rounded-full"></span> Đã hủy</span>
                                <span class="font-medium"><?php echo $order_stats['cancelled']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Area Chart -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-navy-900">Xu hướng doanh thu</h3>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-navy-400 rounded-full"></span> 2024</span>
                                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-accent-400 rounded-full"></span> 2025</span>
                            </div>
                        </div>
                        <div style="height: 200px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>

                    <!-- Calendar / Recent -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-navy-900">Đơn hàng gần đây</h3>
                            <a href="admin-orders.php" class="text-accent-500 text-sm hover:underline">Xem tất cả</a>
                        </div>
                        <div class="space-y-3">
                            <?php foreach ($recent_orders as $order): ?>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-10 h-10 bg-navy-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-receipt text-navy-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-navy-900 truncate"><?php echo htmlspecialchars($order['ho_ten']); ?></p>
                                    <p class="text-xs text-navy-500"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-accent-500"><?php echo number_format($order['tong_tien']/1000); ?>K</p>
                                    <?php
                                    $status_class = match($order['trang_thai']) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                    ?>
                                    <span class="text-xs px-2 py-0.5 rounded-full <?php echo $status_class; ?>">
                                        <?php echo match($order['trang_thai']) {
                                            'pending' => 'Chờ',
                                            'processing' => 'Xử lý',
                                            'completed' => 'Xong',
                                            'cancelled' => 'Hủy',
                                            default => $order['trang_thai']
                                        }; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($recent_orders)): ?>
                            <p class="text-center text-navy-500 py-4">Chưa có đơn hàng</p>
                            <?php endif; ?>
                        </div>
                        <a href="admin-orders.php" class="mt-4 block w-full text-center bg-accent-500 text-white py-2 rounded-lg hover:bg-accent-600 transition text-sm font-medium">
                            Xem ngay
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Charts Script -->
    <script>
        const monthlyData = <?php echo json_encode($monthly_data); ?>;
        const orderStats = <?php echo json_encode($order_stats); ?>;

        // Bar Chart - Result
        new Chart(document.getElementById('resultChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'Doanh thu (triệu)',
                        data: monthlyData.map(d => d.revenue / 1000000),
                        backgroundColor: '#334e68',
                        borderRadius: 4,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Đơn hàng',
                        data: monthlyData.map(d => d.orders),
                        backgroundColor: '#ed8936',
                        borderRadius: 4,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, beginAtZero: true }
                }
            }
        });

        // Doughnut Chart - Completion
        new Chart(document.getElementById('completionChart'), {
            type: 'doughnut',
            data: {
                labels: ['Hoàn thành', 'Chờ xử lý', 'Đang xử lý', 'Đã hủy'],
                datasets: [{
                    data: [orderStats.completed, orderStats.pending, orderStats.processing, orderStats.cancelled],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });

        // Area Chart - Trend
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'Doanh thu',
                        data: monthlyData.map(d => d.revenue / 1000000),
                        borderColor: '#627d98',
                        backgroundColor: 'rgba(98, 125, 152, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Đơn hàng',
                        data: monthlyData.map(d => d.orders * 0.5),
                        borderColor: '#ed8936',
                        backgroundColor: 'rgba(237, 137, 54, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
