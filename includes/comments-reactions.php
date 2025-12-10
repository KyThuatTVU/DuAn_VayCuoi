<!-- CSS cho Comments & Reactions -->
<style>
.comments-reactions-section {
    margin-top: 50px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Facebook-style Reactions */
.reaction-button-container {
    position: relative;
}

.reaction-main-btn {
    position: relative;
    z-index: 1;
}

.reaction-main-btn.active-like {
    color: #1877f2;
}

.reaction-main-btn.active-love {
    color: #f33e5b;
}

.reaction-main-btn.active-wow {
    color: #f7b125;
}

.reaction-main-btn.active-haha {
    color: #f7b125;
}

.reaction-main-btn.active-sad {
    color: #f7b125;
}

.reaction-main-btn.active-angry {
    color: #e9710f;
}

.reaction-popup {
    z-index: 1000;
    pointer-events: none;
}

.reaction-popup.show {
    display: block !important;
    opacity: 1 !important;
    pointer-events: all;
    animation: reactionPopup 0.2s ease-out;
}

@keyframes reactionPopup {
    from {
        transform: translate(-50%, 10px);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

.reaction-item {
    position: relative;
    padding: 8px;
    border-radius: 50%;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.reaction-item:hover {
    background: #f0f2f5;
}

.reaction-label {
    position: absolute;
    bottom: -25px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
}

.reaction-item:hover .reaction-label {
    opacity: 1;
}

/* Reaction Summary Icons */
#reactionIcons .reaction-summary-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 2px solid white;
    background: white;
}

.reaction-summary-icon.like {
    background: linear-gradient(135deg, #1877f2 0%, #0c63d4 100%);
}

.reaction-summary-icon.love {
    background: linear-gradient(135deg, #f33e5b 0%, #e11731 100%);
}

.reaction-summary-icon.wow {
    background: linear-gradient(135deg, #f7b125 0%, #f59e0b 100%);
}

.reaction-summary-icon.haha {
    background: linear-gradient(135deg, #f7b125 0%, #f59e0b 100%);
}

.reaction-summary-icon.sad {
    background: linear-gradient(135deg, #f7b125 0%, #f59e0b 100%);
}

.reaction-summary-icon.angry {
    background: linear-gradient(135deg, #e9710f 0%, #dc2626 100%);
}

/* Comments Section */
.comments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.comments-header h3 {
    font-size: 24px;
    color: #1f2937;
    font-weight: 700;
}

.comment-form {
    margin-bottom: 30px;
}

.comment-input-wrapper {
    position: relative;
}

.comment-textarea {
    width: 100%;
    min-height: 100px;
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    resize: vertical;
    transition: all 0.3s;
}

.comment-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.comment-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.login-prompt {
    padding: 20px;
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 30px;
}

.login-prompt p {
    margin-bottom: 15px;
    color: #92400e;
    font-weight: 600;
}

.btn-submit-comment {
    padding: 12px 30px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-submit-comment:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-submit-comment:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

/* Comment List */
.comments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.comment-item {
    padding: 20px;
    background: #f9fafb;
    border-radius: 10px;
    border-left: 4px solid #3b82f6;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.comment-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
}

.comment-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.comment-info {
    flex: 1;
}

.comment-author {
    font-weight: 700;
    color: #1f2937;
    font-size: 16px;
}

.comment-date {
    font-size: 13px;
    color: #6b7280;
}

.comment-content {
    margin-bottom: 12px;
    color: #374151;
    line-height: 1.6;
}

.comment-footer {
    display: flex;
    gap: 15px;
    align-items: center;
}

.comment-action-btn {
    background: none;
    border: none;
    color: #6b7280;
    font-size: 13px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
    transition: all 0.3s;
    font-weight: 600;
}

.comment-action-btn:hover {
    background: #e5e7eb;
    color: #3b82f6;
}

/* Replies - Minimal Style */
.comment-replies {
    margin-top: 12px;
    margin-left: 48px;
    padding-left: 16px;
    border-left: 2px solid #e5e7eb;
}

.comment-item.is-reply {
    background: transparent;
}

.reply-form {
    margin-top: 12px;
    margin-left: 48px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.reply-form textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    font-size: 13px;
    resize: none;
    background: white;
}

.reply-form textarea:focus {
    outline: none;
    border-color: #3b82f6;
}

.reply-to-indicator {
    padding: 6px 12px;
    background: #eff6ff;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 12px;
    color: #1e40af;
    font-weight: 500;
}

.owner-badge {
    display: inline-block;
    padding: 2px 6px;
    background: #dbeafe;
    color: #1e40af;
    font-size: 10px;
    border-radius: 4px;
    margin-left: 6px;
    font-weight: 600;
}

.author-badge {
    display: inline-block;
    padding: 2px 8px;
    background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
    color: white;
    font-size: 10px;
    border-radius: 10px;
    margin-left: 6px;
    font-weight: 600;
}

.comment-item.is-author {
    background: linear-gradient(135deg, #fdf2f8 0%, #f5f3ff 100%) !important;
    border-left: 3px solid #ec4899 !important;
    border-radius: 8px !important;
    padding: 16px !important;
}

.comment-avatar.author-avatar {
    background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%) !important;
}

.reply-count {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.reply-to-tag {
    color: #3b82f6;
    font-weight: 600;
    background: #eff6ff;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 4px;
}

.empty-comments {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

/* Animations */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Highlight new comment */
.comment-item.highlight {
    animation: highlightPulse 1s ease-out;
}

@keyframes highlightPulse {
    0%, 100% {
        background: #f9fafb;
    }
    50% {
        background: #dbeafe;
    }
}

@media (max-width: 768px) {
    .reactions-bar {
        flex-wrap: wrap;
    }
    
    .reaction-btn {
        flex: 1;
        min-width: calc(50% - 8px);
        justify-content: center;
    }
    
    .comment-replies {
        margin-left: 20px;
    }
    
    .reply-to-indicator {
        font-size: 13px;
    }
}
</style>

<!-- JavaScript cho Comments & Reactions -->
<script>
// Cấu hình
const COMMENTS_CONFIG = {
    type: '<?php echo $comments_type ?? "product"; ?>', // 'product' hoặc 'blog'
    itemId: <?php echo $item_id ?? 0; ?>,
    isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
    userId: <?php echo $_SESSION['user_id'] ?? 'null'; ?>
};

// Emoji cho reactions
const REACTION_EMOJIS = {
    like: '👍',
    love: '❤️',
    wow: '😮',
    haha: '😄',
    sad: '😢',
    angry: '😠'
};

// Load reactions
async function loadReactions() {
    try {
        const endpoint = COMMENTS_CONFIG.type === 'product' 
            ? 'api/reactions-products.php' 
            : 'api/reactions-blogs.php';
        
        const param = COMMENTS_CONFIG.type === 'product' ? 'vay_id' : 'bai_viet_id';
        const response = await fetch(`${endpoint}?action=get&${param}=${COMMENTS_CONFIG.itemId}`);
        const data = await response.json();
        
        if (data.success) {
            updateReactionsUI(data.reactions, data.user_reaction);
        }
    } catch (error) {
        console.error('Error loading reactions:', error);
    }
}

// Update reactions UI - Facebook style
function updateReactionsUI(reactions, userReaction) {
    const mainBtn = document.getElementById('mainReactionBtn');
    const mainIcon = document.getElementById('mainReactionIcon');
    const mainText = document.getElementById('mainReactionText');
    const reactionIcons = document.getElementById('reactionIcons');
    const reactionTotal = document.getElementById('reactionTotal');
    
    // Calculate total
    const total = Object.values(reactions).reduce((sum, count) => sum + count, 0);
    reactionTotal.textContent = total > 0 ? total : '0';
    
    // Update main button based on user reaction
    if (userReaction) {
        mainBtn.className = `reaction-main-btn w-full py-2 px-4 rounded-lg hover:bg-gray-100 transition-all duration-200 flex items-center justify-center gap-2 font-semibold active-${userReaction}`;
        
        const reactionData = {
            like: { icon: '👍', text: 'Thích', color: '#1877f2' },
            love: { icon: '❤️', text: 'Yêu thích', color: '#f33e5b' },
            wow: { icon: '😮', text: 'Wow', color: '#f7b125' },
            haha: { icon: '😄', text: 'Haha', color: '#f7b125' },
            sad: { icon: '😢', text: 'Buồn', color: '#f7b125' },
            angry: { icon: '😠', text: 'Phẫn nộ', color: '#e9710f' }
        };
        
        const data = reactionData[userReaction];
        mainIcon.textContent = data.icon;
        mainText.textContent = data.text;
    } else {
        mainBtn.className = 'reaction-main-btn w-full py-2 px-4 rounded-lg hover:bg-gray-100 transition-all duration-200 flex items-center justify-center gap-2 font-semibold text-gray-600';
        mainIcon.textContent = '👍';
        mainText.textContent = 'Thích';
    }
    
    // Update reaction summary icons
    reactionIcons.innerHTML = '';
    const sortedReactions = Object.entries(reactions)
        .filter(([_, count]) => count > 0)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 3);
    
    sortedReactions.forEach(([type, count]) => {
        const iconMap = {
            like: '👍',
            love: '❤️',
            wow: '😮',
            haha: '😄',
            sad: '😢',
            angry: '😠'
        };
        
        const icon = document.createElement('div');
        icon.className = `reaction-summary-icon ${type}`;
        icon.textContent = iconMap[type];
        reactionIcons.appendChild(icon);
    });
}

// Select reaction (Facebook style)
function selectReaction(type) {
    toggleReaction(type);
    hideReactionPopup();
}

// Show reaction popup on hover
let reactionHoverTimeout;
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.reaction-button-container');
    
    containers.forEach(container => {
        const popup = container.querySelector('.reaction-popup');
        
        container.addEventListener('mouseenter', function() {
            clearTimeout(reactionHoverTimeout);
            reactionHoverTimeout = setTimeout(() => {
                popup.classList.add('show');
            }, 500); // Delay 500ms như Facebook
        });
        
        container.addEventListener('mouseleave', function() {
            clearTimeout(reactionHoverTimeout);
            setTimeout(() => {
                if (!popup.matches(':hover')) {
                    popup.classList.remove('show');
                }
            }, 200);
        });
        
        popup.addEventListener('mouseleave', function() {
            popup.classList.remove('show');
        });
    });
});

function hideReactionPopup() {
    document.querySelectorAll('.reaction-popup').forEach(popup => {
        popup.classList.remove('show');
    });
}

// Toggle reaction
async function toggleReaction(type) {
    if (!COMMENTS_CONFIG.isLoggedIn) {
        showLoginAlert();
        return;
    }
    
    try {
        const endpoint = COMMENTS_CONFIG.type === 'product' 
            ? 'api/reactions-products.php' 
            : 'api/reactions-blogs.php';
        
        const param = COMMENTS_CONFIG.type === 'product' ? 'vay_id' : 'bai_viet_id';
        
        const formData = new FormData();
        formData.append('action', 'toggle');
        formData.append(param, COMMENTS_CONFIG.itemId);
        formData.append('loai_cam_xuc', type);
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadReactions();
        } else if (data.require_login) {
            showLoginAlert();
        }
    } catch (error) {
        console.error('Error toggling reaction:', error);
    }
}

// Load comments
async function loadComments() {
    try {
        const endpoint = COMMENTS_CONFIG.type === 'product' 
            ? 'api/comments-products.php' 
            : 'api/comments-blogs.php';
        
        const param = COMMENTS_CONFIG.type === 'product' ? 'vay_id' : 'bai_viet_id';
        const response = await fetch(`${endpoint}?action=get&${param}=${COMMENTS_CONFIG.itemId}`);
        const data = await response.json();
        
        if (data.success) {
            renderComments(data.comments);
        }
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

// Render comments
function renderComments(comments) {
    const container = document.getElementById('commentsList');
    if (!container) {
        console.warn('Comments container not found');
        return;
    }
    
    if (comments.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-400 text-sm">
                Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
            </div>
        `;
        const countEl = document.getElementById('totalCommentsCount');
        if (countEl) countEl.textContent = '0';
        return;
    }
    
    // Count total comments
    let totalCount = 0;
    comments.forEach(comment => {
        totalCount++;
        if (comment.replies) {
            totalCount += comment.replies.length;
        }
    });
    
    const countEl = document.getElementById('totalCommentsCount');
    if (countEl) {
        countEl.textContent = totalCount;
    } else {
        console.warn('totalCommentsCount element not found');
    }
    
    container.innerHTML = comments.map(comment => renderComment(comment)).join('');
}

// Render single comment
function renderComment(comment, isReply = false) {
    const isAdmin = comment.is_author || comment.is_admin_reply == 1;
    const isOwner = COMMENTS_CONFIG.userId === comment.nguoi_dung_id && !isAdmin;
    
    // Xác định tên hiển thị và avatar
    let displayName, avatar;
    if (isAdmin) {
        displayName = 'Admin';
        avatar = '<i class="fas fa-user-shield" style="font-size: 16px;"></i>';
    } else {
        displayName = comment.ho_ten;
        avatar = comment.avt 
            ? `<img src="${comment.avt}" alt="${comment.ho_ten}">` 
            : comment.ho_ten.charAt(0).toUpperCase();
    }
    
    // Nút xóa chỉ cho chủ bình luận (người dùng thường)
    const deleteBtn = isOwner 
        ? `<button class="comment-action-btn" onclick="deleteComment(${comment.id})">🗑️ Xóa</button>` 
        : '';
    
    // Hiển thị nút trả lời cho tất cả người dùng (kể cả chưa đăng nhập)
    const replyBtn = `<button class="comment-action-btn" onclick="showReplyForm(${comment.id}, '${escapeHtml(displayName)}')">💬 Trả lời</button>`;
    
    const replies = comment.replies && comment.replies.length > 0
        ? `<div class="comment-replies">${comment.replies.map(reply => renderComment(reply, true)).join('')}</div>`
        : '';
    
    // Badge: Admin = "Tác giả", Người dùng = "Bạn" (nếu là chính họ)
    let badge = '';
    if (isAdmin) {
        badge = '<span class="author-badge">Tác giả</span>';
    } else if (isOwner) {
        badge = '<span class="owner-badge">Bạn</span>';
    }
    
    // Hiển thị @tên_người nếu đang reply cho ai đó (không phải reply trực tiếp cho comment gốc)
    let replyToTag = '';
    if (isReply && comment.reply_to_name && comment.reply_to_id != comment.parent_id) {
        replyToTag = `<span class="reply-to-tag">@${escapeHtml(comment.reply_to_name)}</span> `;
    }
    
    // Style đặc biệt cho bình luận của Admin
    const adminClass = isAdmin ? 'is-author' : '';
    const avatarClass = isAdmin ? 'author-avatar' : '';
    
    return `
        <div class="comment-item ${isReply ? 'is-reply' : ''} ${adminClass}" id="comment-${comment.id}" data-comment-id="${comment.id}">
            <div class="comment-header">
                <div class="comment-avatar ${avatarClass}">${avatar}</div>
                <div class="comment-info">
                    <div class="comment-author">
                        ${displayName}
                        ${badge}
                    </div>
                    <div class="comment-date">${formatDate(comment.created_at)}</div>
                </div>
            </div>
            <div class="comment-content">${replyToTag}${escapeHtml(comment.noi_dung)}</div>
            <div class="comment-footer">
                ${replyBtn}
                ${deleteBtn}
                ${comment.replies && comment.replies.length > 0 ? `<span class="reply-count">${comment.replies.length} trả lời</span>` : ''}
            </div>
            <div id="replyForm${comment.id}"></div>
            ${replies}
        </div>
    `;
}

// Add comment
async function addComment(parentId = null) {
    const textareaId = parentId ? `replyTextarea${parentId}` : 'commentTextarea';
    const textarea = document.getElementById(textareaId);
    
    if (!textarea) {
        alert('Lỗi: Không tìm thấy ô nhập bình luận');
        return;
    }
    
    const content = textarea.value.trim();
    
    if (!content) {
        alert('Vui lòng nhập nội dung bình luận');
        return;
    }
    
    if (!COMMENTS_CONFIG.isLoggedIn) {
        showLoginAlert();
        return;
    }
    
    // Disable button để tránh spam
    const submitBtn = parentId 
        ? document.querySelector(`#replyForm${parentId} .btn-submit-comment`)
        : document.querySelector('.comment-form .btn-submit-comment');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    }
    
    try {
        const endpoint = COMMENTS_CONFIG.type === 'product' 
            ? 'api/comments-products.php' 
            : 'api/comments-blogs.php';
        
        const param = COMMENTS_CONFIG.type === 'product' ? 'vay_id' : 'bai_viet_id';
        
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append(param, COMMENTS_CONFIG.itemId);
        formData.append('noi_dung', content);
        if (parentId) formData.append('parent_id', parentId);
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            textarea.value = '';
            await loadComments();
            
            if (parentId) {
                closeReplyForm(parentId);
                // Scroll to new comment
                setTimeout(() => {
                    const commentElement = document.querySelector(`[data-comment-id="${parentId}"]`);
                    if (commentElement) {
                        commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 300);
            }
            
            // Show success message
            showNotification('✅ Đã gửi bình luận thành công!', 'success');
        } else if (data.require_login) {
            showLoginAlert();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error adding comment:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = parentId 
                ? '<i class="fas fa-paper-plane"></i> Gửi trả lời'
                : 'Gửi Bình Luận';
        }
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Delete comment
async function deleteComment(commentId) {
    if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;
    
    try {
        const endpoint = COMMENTS_CONFIG.type === 'product' 
            ? 'api/comments-products.php' 
            : 'api/comments-blogs.php';
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('comment_id', commentId);
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadComments();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
    }
}

// Show reply form
function showReplyForm(commentId, authorName = '') {
    // Kiểm tra đăng nhập trước
    if (!COMMENTS_CONFIG.isLoggedIn) {
        showLoginAlert();
        return;
    }
    
    const container = document.getElementById(`replyForm${commentId}`);
    
    // Toggle form
    if (container.innerHTML) {
        container.innerHTML = '';
        return;
    }
    
    // Scroll to form
    const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (commentElement) {
        commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    container.innerHTML = `
        <div class="reply-form">
            <div class="reply-to-indicator">
                <i class="fas fa-reply"></i> Trả lời <strong>${escapeHtml(authorName)}</strong>
            </div>
            <textarea id="replyTextarea${commentId}" class="comment-textarea" 
                      placeholder="Nhập câu trả lời của bạn..." 
                      autofocus></textarea>
            <div class="comment-actions" style="margin-top: 10px;">
                <button class="btn-submit-comment" onclick="addComment(${commentId})">
                    <i class="fas fa-paper-plane"></i> Gửi trả lời
                </button>
                <button class="comment-action-btn" onclick="closeReplyForm(${commentId})">
                    <i class="fas fa-times"></i> Hủy
                </button>
            </div>
        </div>
    `;
    
    // Focus vào textarea
    setTimeout(() => {
        document.getElementById(`replyTextarea${commentId}`)?.focus();
    }, 100);
}

// Close reply form
function closeReplyForm(commentId) {
    document.getElementById(`replyForm${commentId}`).innerHTML = '';
}

// Show login alert
function showLoginAlert() {
    alert('Vui lòng đăng nhập để sử dụng chức năng này!');
    window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'Vừa xong';
    if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
    if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
    if (diff < 604800) return Math.floor(diff / 86400) + ' ngày trước';
    
    return date.toLocaleDateString('vi-VN');
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Scroll đến comment từ URL hash (khi click từ thông báo)
function scrollToCommentFromHash() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#comment-')) {
        const commentId = hash.replace('#comment-', '');
        const commentElement = document.getElementById(`comment-${commentId}`);
        
        if (commentElement) {
            // Scroll đến comment
            setTimeout(() => {
                commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Highlight comment
                commentElement.classList.add('highlight-target-comment');
                
                // Xóa highlight sau 3 giây
                setTimeout(() => {
                    commentElement.classList.remove('highlight-target-comment');
                }, 3000);
            }, 500);
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadReactions();
    loadComments().then(() => {
        // Sau khi load comments xong, scroll đến comment từ hash
        scrollToCommentFromHash();
    });
});
</script>

<style>
/* Highlight animation for target comment */
@keyframes highlightFade {
    0% { background-color: #fef3c7; box-shadow: 0 0 0 4px #fbbf24; }
    70% { background-color: #fef3c7; box-shadow: 0 0 0 4px #fbbf24; }
    100% { background-color: transparent; box-shadow: none; }
}

.comment-item.highlight-target-comment {
    animation: highlightFade 3s ease-out forwards;
    border-radius: 8px !important;
    margin: -8px !important;
    padding: 24px 8px !important;
}

/* Override Comment Styles - Clean Minimal Design */
.comment-item {
    padding: 16px 0 !important;
    background: transparent !important;
    border-radius: 0 !important;
    border-left: none !important;
    border-bottom: 1px solid #f3f4f6 !important;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.comment-item:last-child {
    border-bottom: none !important;
}

.comment-header {
    display: flex !important;
    align-items: flex-start !important;
    gap: 12px !important;
    margin-bottom: 8px !important;
}

.comment-avatar {
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}

.comment-avatar img {
    width: 100% !important;
    height: 100% !important;
    border-radius: 50% !important;
    object-fit: cover !important;
}

.comment-info {
    flex: 1 !important;
    min-width: 0 !important;
}

.comment-author {
    font-weight: 600 !important;
    color: #111827 !important;
    font-size: 14px !important;
    display: inline-block !important;
    margin-right: 8px !important;
}

.comment-date {
    font-size: 12px !important;
    color: #9ca3af !important;
}

.comment-content {
    margin: 8px 0 8px 48px !important;
    color: #374151 !important;
    line-height: 1.5 !important;
    font-size: 14px !important;
}

.comment-footer {
    display: flex !important;
    gap: 16px !important;
    align-items: center !important;
    margin-left: 48px !important;
}

.comment-action-btn {
    background: none !important;
    border: none !important;
    color: #6b7280 !important;
    font-size: 12px !important;
    cursor: pointer !important;
    padding: 4px 8px !important;
    border-radius: 4px !important;
    transition: all 0.2s !important;
    font-weight: 500 !important;
}

.comment-action-btn:hover {
    background: #f3f4f6 !important;
    color: #3b82f6 !important;
}
</style>
