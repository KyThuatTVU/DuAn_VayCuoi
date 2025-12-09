<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/settings-helper.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Cài Đặt Hệ Thống';

// Lấy thống kê cho sidebar badges
$result = $conn->query("SELECT COUNT(*) as total FROM don_hang WHERE trang_thai = 'pending'");
$pending_orders = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM lien_he WHERE status = 'new'");
$new_contacts = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM dat_lich_thu_vay WHERE status = 'pending'");
$pending_bookings = $result->fetch_assoc()['total'];

// Lấy tất cả nhóm cài đặt
$groups = getSettingGroups($conn);

// Xử lý cập nhật cài đặt
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        $result = updateSettings($conn, $_POST['settings']);
        if ($result) {
            $success_message = 'Cập nhật cài đặt thành công!';
        } else {
            $error_message = 'Có lỗi xảy ra khi cập nhật cài đặt.';
        }
    }
}

// Tab hiện tại
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'contact';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }
    </style>
</head>
<body class="bg-gray-100 admin-page">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-navy-900 fixed h-full overflow-y-auto sidebar-scroll z-50">
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
                <p class="text-navy-300 text-xs break-all px-1 leading-relaxed"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'admin@vaycuoi.com'); ?></p>
            </div>

            <!-- Menu -->
            <nav class="p-4">
                <a href="admin-dashboard.php" class="sidebar-link flex items-center gap-3 px-4 py-3 text-navy-200 rounded">
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
                </a>
                <a href="admin-settings.php" class="sidebar-link active flex items-center gap-3 px-4 py-3 text-white rounded mt-1">
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
        <main class="flex-1 ml-64">
            <!-- Header -->
            <header class="bg-white shadow-sm px-6 max-sm:px-4 py-4 max-sm:py-3 flex items-center sticky top-0 z-40">
                <!-- Mobile Menu Toggle -->
                <button id="headerMenuToggle" class="hidden max-lg:flex p-2 rounded-lg hover:bg-gray-100 mr-3 items-center justify-center">
                    <i class="fas fa-bars text-navy-700 text-xl"></i>
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl max-sm:text-lg font-bold text-navy-900 truncate">Cài Đặt Hệ Thống</h1>
                    <p class="text-navy-500 text-sm max-sm:text-xs max-sm:hidden">Quản lý thông tin liên hệ, giờ làm việc và các cài đặt khác</p>
                </div>
            </header>

            <!-- Content -->
            <div class="p-6 max-sm:p-4">
                <!-- Alert Messages -->
                <?php if ($success_message): ?>
                <div class="mb-6 max-sm:mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 max-sm:p-3 rounded-r-lg flex items-center">
                    <i class="fas fa-check-circle mr-3 max-sm:mr-2 text-xl max-sm:text-base"></i>
                    <span class="max-sm:text-sm"><?php echo $success_message; ?></span>
                </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                <div class="mb-6 max-sm:mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 max-sm:p-3 rounded-r-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 max-sm:mr-2 text-xl max-sm:text-base"></i>
                    <span class="max-sm:text-sm"><?php echo $error_message; ?></span>
                </div>
                <?php endif; ?>

                <div class="flex flex-col lg:flex-row gap-6 max-sm:gap-4">
                    <!-- Settings Tabs -->
                    <div class="lg:w-64 flex-shrink-0">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="p-4 max-sm:p-3 bg-gradient-to-r from-accent-500 to-accent-600">
                                <h3 class="text-white font-semibold max-sm:text-sm">Danh mục cài đặt</h3>
                            </div>
                            <nav class="p-2 max-sm:flex max-sm:flex-row max-sm:overflow-x-auto max-sm:gap-2 max-sm:p-3 max-sm:-mx-0">
                                <?php foreach ($groups as $group): ?>
                                <a href="?tab=<?php echo $group; ?>" 
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition max-sm:whitespace-nowrap max-sm:flex-shrink-0 max-sm:px-3 max-sm:py-2 max-sm:text-sm max-sm:rounded-full <?php echo $current_tab === $group ? 'bg-accent-50 text-accent-700 font-medium' : 'text-gray-600 hover:bg-gray-50'; ?>">
                                    <i class="<?php echo getGroupIcon($group); ?> w-5 max-sm:w-4"></i>
                                    <span><?php echo getGroupLabel($group); ?></span>
                                </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                    </div>

                    <!-- Settings Form -->
                    <div class="flex-1 min-w-0">
                        <form method="POST" class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="p-6 max-sm:p-4 border-b bg-gradient-to-r from-gray-50 to-white">
                                <h2 class="text-xl max-sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <i class="<?php echo getGroupIcon($current_tab); ?> text-accent-500"></i>
                                    <?php echo getGroupLabel($current_tab); ?>
                                </h2>
                            </div>

                            <div class="p-6 max-sm:p-4 space-y-6 max-sm:space-y-4">
                                <?php 
                                $settings = getSettingsByGroup($conn, $current_tab);
                                foreach ($settings as $setting): 
                                ?>
                                <div class="group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 max-sm:text-xs">
                                        <?php echo htmlspecialchars($setting['setting_label']); ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] === 'textarea'): ?>
                                    <textarea 
                                        name="settings[<?php echo $setting['setting_key']; ?>]"
                                        rows="3"
                                        class="w-full px-4 py-3 max-sm:px-3 max-sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition text-base"
                                    ><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                    
                                    <?php else: ?>
                                    <div class="relative">
                                        <?php if ($setting['setting_type'] === 'email'): ?>
                                        <span class="absolute left-4 max-sm:left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <?php elseif ($setting['setting_type'] === 'phone'): ?>
                                        <span class="absolute left-4 max-sm:left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <?php elseif ($setting['setting_type'] === 'url'): ?>
                                        <span class="absolute left-4 max-sm:left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <?php endif; ?>
                                        
                                        <input 
                                            type="<?php echo $setting['setting_type'] === 'phone' ? 'tel' : $setting['setting_type']; ?>"
                                            name="settings[<?php echo $setting['setting_key']; ?>]"
                                            value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                            class="w-full px-4 py-3 max-sm:px-3 max-sm:py-2 <?php echo in_array($setting['setting_type'], ['email', 'phone', 'url']) ? 'pl-12 max-sm:pl-10' : ''; ?> border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition text-base"
                                        >
                                    </div>
                                    <?php endif; ?>
                                    
                                    <p class="mt-1 text-xs text-gray-500 max-sm:text-[10px]">
                                        Key: <code class="bg-gray-100 px-1 rounded"><?php echo $setting['setting_key']; ?></code>
                                    </p>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="px-6 py-4 max-sm:px-4 max-sm:py-3 bg-gray-50 border-t flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-0">
                                <p class="text-sm text-gray-500 text-center sm:text-left">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Thay đổi sẽ được áp dụng ngay trên toàn bộ website
                                </p>
                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i>
                                    Lưu Cài Đặt
                                </button>
                            </div>
                        </form>

                        <!-- Preview Section -->
                        <?php if ($current_tab === 'contact' || $current_tab === 'working'): ?>
                        <div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="p-4 max-sm:p-3 bg-gray-800 text-white">
                                <h3 class="font-semibold flex items-center gap-2 max-sm:text-sm">
                                    <i class="fas fa-eye"></i>
                                    Xem trước (Footer)
                                </h3>
                            </div>
                            <div class="p-6 max-sm:p-4 bg-gray-900 text-gray-300">
                                <h4 class="text-white font-bold mb-4 max-sm:mb-3 flex items-center gap-2 max-sm:text-sm">
                                    <div class="w-1 h-5 max-sm:h-4 bg-pink-500 rounded"></div>
                                    Liên Hệ
                                </h4>
                                <div class="space-y-3 max-sm:space-y-2 text-sm max-sm:text-xs">
                                    <p class="flex items-start gap-3 max-sm:gap-2">
                                        <i class="fas fa-map-marker-alt text-pink-500 mt-1 max-sm:mt-0.5"></i>
                                        <span class="break-words"><?php echo nl2br(htmlspecialchars(getSetting($conn, 'contact_address'))); ?></span>
                                    </p>
                                    <p class="flex items-center gap-3 max-sm:gap-2">
                                        <i class="fas fa-phone text-pink-500"></i>
                                        <span><?php echo htmlspecialchars(getSetting($conn, 'contact_phone')); ?></span>
                                    </p>
                                    <p class="flex items-center gap-3 max-sm:gap-2">
                                        <i class="fas fa-envelope text-pink-500"></i>
                                        <span class="break-all"><?php echo htmlspecialchars(getSetting($conn, 'contact_email')); ?></span>
                                    </p>
                                    <p class="flex items-start gap-3 max-sm:gap-2">
                                        <i class="fas fa-clock text-pink-500 mt-1 max-sm:mt-0.5"></i>
                                        <span>
                                            <?php echo htmlspecialchars(getSetting($conn, 'working_days')); ?><br>
                                            <?php echo htmlspecialchars(getSetting($conn, 'working_hours')); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Auto-save notification
    document.querySelector('form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang lưu...';
        btn.disabled = true;
    });
    </script>
</body>
</html>
