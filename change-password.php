<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['redirect_after_login'] = 'change-password.php';
    redirect('login.php');
}

$page_title = 'Đổi Mật Khẩu';

// Lấy thông tin user từ database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<link rel="stylesheet" href="assets/css/account.css">
<?php require_once 'includes/header.php'; ?>

<div class="account-container">
    <div class="container">
        <div class="account-layout">
            <!-- Sidebar -->
            <aside class="account-sidebar">
                <div class="user-profile-card">
                    <div class="profile-avatar">
                        <?php if (!empty($user['avt'])): ?>
                            <img src="<?php echo htmlspecialchars($user['avt']); ?>" alt="Avatar" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($user['ho_ten']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <nav class="account-menu">
                    <a href="account.php" class="menu-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Thông tin cá nhân
                    </a>
                    <a href="my-orders.php" class="menu-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        Đơn hàng của tôi
                    </a>
                    <a href="booking.php" class="menu-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Lịch đặt thử váy
                    </a>
                    <a href="change-password.php" class="menu-item active">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Đổi mật khẩu
                    </a>
                    <a href="logout.php" class="menu-item logout">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Đăng xuất
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="account-content">
                <div class="content-header">
                    <h2>Đổi Mật Khẩu</h2>
                    <p>Cập nhật mật khẩu của bạn để bảo mật tài khoản</p>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-error">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; unset($_SESSION['errors']); ?>
                    </div>
                <?php endif; ?>

                <form action="process-change-password.php" method="POST" class="profile-form">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="current_password">Mật khẩu hiện tại</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="new_password">Mật khẩu mới</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6">
                            <small style="color: #666;">Mật khẩu phải có ít nhất 6 ký tự</small>
                        </div>

                        <div class="form-group full-width">
                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            Đổi mật khẩu
                        </button>
                        <a href="account.php" class="btn btn-secondary">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
