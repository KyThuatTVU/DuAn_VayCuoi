<?php
session_start();
require_once 'includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['redirect_after_login'] = 'account.php';
    redirect('login.php');
}

$page_title = 'Hồ Sơ Tài Khoản';

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
                    <a href="account.php" class="menu-item active">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Thông tin cá nhân
                    </a>
                    <a href="orders.php" class="menu-item">
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
                    <a href="change-password.php" class="menu-item">
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
                    <h2>Thông Tin Cá Nhân</h2>
                    <p>Quản lý thông tin hồ sơ của bạn</p>
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

                <form action="update-profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
                    <!-- Avatar Upload -->
                    <div class="avatar-upload-section">
                        <div class="current-avatar">
                            <?php if (!empty($user['avt'])): ?>
                                <img src="<?php echo htmlspecialchars($user['avt']); ?>" alt="Avatar" id="preview-avatar" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <div class="avatar-placeholder" id="preview-avatar">
                                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="avatar-upload-info">
                            <h4>Ảnh đại diện</h4>
                            <p>Chọn ảnh có kích thước tối đa 5MB</p>
                            <label for="avatar-input" class="btn-upload">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                Chọn ảnh
                            </label>
                            <input type="file" id="avatar-input" name="avt" accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="ho_ten">Họ và tên</label>
                            <input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="so_dien_thoai">Số điện thoại</label>
                            <input type="tel" id="so_dien_thoai" name="so_dien_thoai" value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>" pattern="[0-9]{10,11}">
                        </div>

                        <div class="form-group full-width">
                            <label for="dia_chi">Địa chỉ</label>
                            <textarea id="dia_chi" name="dia_chi" rows="3"><?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Lưu thay đổi
                        </button>
                        <button type="reset" class="btn btn-secondary">Hủy bỏ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Preview avatar
document.getElementById('avatar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview-avatar');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Avatar';
                preview.parentNode.replaceChild(img, preview);
            }
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
