<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/mail-helper.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$page_title = 'Quản Lý Liên Hệ';
$page_subtitle = 'Xem và phản hồi tin nhắn từ khách hàng';

// Xử lý cập nhật trạng thái và gửi email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE lien_he SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Cập nhật trạng thái thành công!';
    }
    
    if ($_POST['action'] === 'reply_email') {
        // Lấy thông tin liên hệ
        $stmt = $conn->prepare("SELECT * FROM lien_he WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $contact = $stmt->get_result()->fetch_assoc();
        
        if ($contact) {
            $reply_content = trim($_POST['reply_content'] ?? '');
            
            if (!empty($reply_content)) {
                // Gửi email phản hồi
                $result = sendContactReplyEmail(
                    $contact['email'],
                    $contact['name'],
                    $contact['subject'],
                    $contact['message'],
                    $reply_content
                );
                
                if ($result['success']) {
                    // Cập nhật trạng thái thành 'replied'
                    $stmt = $conn->prepare("UPDATE lien_he SET status = 'replied' WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    
                    $_SESSION['admin_success'] = 'Đã gửi email phản hồi thành công!';
                } else {
                    $_SESSION['admin_error'] = 'Gửi email thất bại: ' . $result['message'];
                }
            } else {
                $_SESSION['admin_error'] = 'Nội dung phản hồi không được để trống!';
            }
        }
    }
    
    if ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM lien_he WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['admin_success'] = 'Xóa liên hệ thành công!';
    }
    
    header('Location: admin-contacts.php');
    exit();
}

// Lấy danh sách
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = "1=1";
$params = [];
$types = "";

if ($status_filter) {
    $where .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($search) {
    $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$count_sql = "SELECT COUNT(*) as total FROM lien_he WHERE $where";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$sql = "SELECT * FROM lien_he WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-layout.php';
?>

<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
    </div>
<?php endif; ?>

<!-- Bộ lọc -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
            placeholder="Tìm tên, email, tiêu đề..." class="border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 focus:border-transparent text-base">
        <select name="status" class="border border-gray-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-accent-500 text-base">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>Mới</option>
            <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Đã trả lời</option>
            <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Đã đóng</option>
        </select>
        <button type="submit" class="bg-accent-500 text-white rounded-lg px-4 py-2.5 hover:bg-accent-600 transition flex items-center justify-center sm:col-span-2 lg:col-span-1">
            <i class="fas fa-search mr-2"></i>Lọc
        </button>
    </form>
</div>

<!-- Danh sách liên hệ -->
<div class="space-y-4">
    <?php foreach ($contacts as $contact): ?>
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 hover:shadow-md transition">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-4">
            <div class="flex-1 min-w-0">
                <h3 class="text-base sm:text-lg font-semibold text-navy-900"><?php echo htmlspecialchars($contact['subject'] ?? 'Không có tiêu đề'); ?></h3>
                <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-sm text-navy-500 mt-2">
                    <span><i class="fas fa-user mr-1 text-accent-500"></i><?php echo htmlspecialchars($contact['name']); ?></span>
                    <span class="break-all"><i class="fas fa-envelope mr-1 text-accent-500"></i><?php echo htmlspecialchars($contact['email']); ?></span>
                    <?php if ($contact['phone']): ?>
                    <span><i class="fas fa-phone mr-1 text-accent-500"></i><?php echo htmlspecialchars($contact['phone']); ?></span>
                    <?php endif; ?>
                    <span><i class="fas fa-clock mr-1 text-accent-500"></i><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                    <select name="status" onchange="this.form.submit()" class="text-xs sm:text-sm border rounded-lg px-2 sm:px-3 py-1.5 font-medium
                        <?php echo match($contact['status']) {
                            'new' => 'bg-red-50 text-red-700 border-red-200',
                            'replied' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'closed' => 'bg-gray-50 text-gray-700 border-gray-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200'
                        }; ?>">
                        <option value="new" <?php echo $contact['status'] === 'new' ? 'selected' : ''; ?>>Mới</option>
                        <option value="replied" <?php echo $contact['status'] === 'replied' ? 'selected' : ''; ?>>Đã trả lời</option>
                        <option value="closed" <?php echo $contact['status'] === 'closed' ? 'selected' : ''; ?>>Đã đóng</option>
                    </select>
                </form>
                <button onclick="deleteContact(<?php echo $contact['id']; ?>)" class="text-red-500 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-navy-700 whitespace-pre-wrap"><?php echo htmlspecialchars($contact['message']); ?></p>
        </div>
        <?php if ($contact['image_path']): ?>
        <div class="mt-4">
            <img src="<?php echo htmlspecialchars($contact['image_path']); ?>" class="max-w-xs rounded-lg" alt="Ảnh đính kèm">
        </div>
        <?php endif; ?>
        <div class="mt-4 flex gap-2">
            <button 
                onclick="openReplyModal(this)" 
                data-id="<?php echo $contact['id']; ?>"
                data-name="<?php echo htmlspecialchars($contact['name']); ?>"
                data-email="<?php echo htmlspecialchars($contact['email']); ?>"
                data-subject="<?php echo htmlspecialchars($contact['subject'] ?? ''); ?>"
                class="inline-flex items-center bg-accent-500 text-white px-4 py-2 rounded-lg hover:bg-accent-600 transition text-sm">
                <i class="fas fa-reply mr-2"></i>Trả lời qua Email
            </button>
            <?php 
                $mailto_subject = $contact['subject'] ?? 'Liên hệ';
                $mailto_link = 'mailto:' . rawurlencode($contact['email']) . '?subject=' . rawurlencode('Re: ' . $mailto_subject);
            ?>
            <a href="<?php echo $mailto_link; ?>" 
               class="inline-flex items-center bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm"
               target="_blank">
                <i class="fas fa-external-link-alt mr-2"></i>Mở Email Client
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($contacts)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-navy-500">
        <i class="fas fa-inbox text-4xl mb-4 text-navy-300"></i>
        <p>Không có liên hệ nào</p>
    </div>
    <?php endif; ?>
</div>

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="flex space-x-2">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>" 
           class="px-4 py-2 rounded-lg <?php echo $i === $page ? 'bg-accent-500 text-white' : 'bg-white text-navy-700 hover:bg-gray-100'; ?> transition">
            <?php echo $i; ?>
        </a>
        <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>

<form id="deleteForm" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<!-- Reply Email Modal -->
<div id="replyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <form method="POST" id="replyForm">
            <input type="hidden" name="action" value="reply_email">
            <input type="hidden" name="id" id="replyContactId">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-accent-500 to-accent-600 p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-reply mr-3"></i>Trả lời Liên hệ
                    </h3>
                    <button type="button" onclick="closeReplyModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Thông tin khách hàng -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-navy-900 mb-2">Thông tin khách hàng:</h4>
                    <div class="text-sm space-y-1 text-navy-600">
                        <p><strong>Tên:</strong> <span id="replyCustomerName"></span></p>
                        <p><strong>Email:</strong> <span id="replyCustomerEmail"></span></p>
                        <p><strong>Chủ đề:</strong> <span id="replySubject"></span></p>
                    </div>
                </div>
                
                <!-- Nội dung phản hồi -->
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-2">
                        Nội dung phản hồi <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="reply_content" 
                        rows="8" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent transition resize-none"
                        placeholder="Nhập nội dung phản hồi cho khách hàng...&#10;&#10;Ví dụ:&#10;Xin chào Anh/Chị,&#10;&#10;Cảm ơn bạn đã liên hệ với chúng tôi. Về vấn đề bạn đề cập...&#10;&#10;Trân trọng,&#10;Váy Cưới Thiên Thần"
                    ></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-accent-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-accent-600 transition flex items-center justify-center"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>Gửi Email
                    </button>
                    <button 
                        type="button"
                        onclick="closeReplyModal()"
                        class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-semibold"
                    >
                        Hủy
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function deleteContact(id) {
        if (confirm('Bạn có chắc muốn xóa liên hệ này?')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
    
    function openReplyModal(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const email = button.getAttribute('data-email');
        const subject = button.getAttribute('data-subject');
        
        document.getElementById('replyContactId').value = id;
        document.getElementById('replyCustomerName').textContent = name;
        document.getElementById('replyCustomerEmail').textContent = email;
        document.getElementById('replySubject').textContent = subject || 'Không có chủ đề';
        document.getElementById('replyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeReplyModal() {
        document.getElementById('replyModal').classList.add('hidden');
        document.getElementById('replyForm').reset();
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('replyModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReplyModal();
        }
    });
</script>

<?php include 'includes/admin-footer.php'; ?>
