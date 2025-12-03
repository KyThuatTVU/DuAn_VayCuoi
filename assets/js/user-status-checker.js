/**
 * User Status Checker
 * Kiểm tra trạng thái tài khoản người dùng định kỳ
 * Nếu bị khóa sẽ tự động đăng xuất
 */

(function() {
    'use strict';
    
    // Cấu hình
    const CHECK_INTERVAL = 5000; // Kiểm tra mỗi 5 giây
    const API_URL = 'api/check-user-status.php';
    
    let checkInterval = null;
    let isChecking = false;
    
    /**
     * Kiểm tra trạng thái user
     */
    async function checkUserStatus() {
        if (isChecking) return;
        isChecking = true;
        
        try {
            const response = await fetch(API_URL, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.force_logout) {
                // User bị khóa hoặc xóa - hiển thị thông báo và redirect
                stopChecking();
                showLockedModal(data.message, data.status);
            }
        } catch (error) {
            console.error('Error checking user status:', error);
        } finally {
            isChecking = false;
        }
    }
    
    /**
     * Hiển thị modal thông báo bị khóa
     */
    function showLockedModal(message, status) {
        // Tạo overlay
        const overlay = document.createElement('div');
        overlay.id = 'user-locked-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        `;
        
        // Icon dựa trên status
        let icon = '🔒';
        let iconColor = '#ef4444';
        if (status === 'disabled') {
            icon = '⛔';
            iconColor = '#6b7280';
        } else if (status === 'deleted') {
            icon = '❌';
            iconColor = '#dc2626';
        }
        
        // Tạo modal content
        const modal = document.createElement('div');
        modal.style.cssText = `
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease;
        `;
        
        modal.innerHTML = `
            <div style="font-size: 64px; margin-bottom: 20px;">${icon}</div>
            <h2 style="color: ${iconColor}; font-size: 24px; font-weight: bold; margin-bottom: 15px;">
                ${status === 'locked' ? 'Tài khoản bị khóa' : status === 'disabled' ? 'Tài khoản bị vô hiệu hóa' : 'Tài khoản không tồn tại'}
            </h2>
            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
                ${message}
            </p>
            <button onclick="window.location.href='login.php'" style="
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: white;
                border: none;
                padding: 14px 32px;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s, box-shadow 0.2s;
            " onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 10px 25px rgba(59, 130, 246, 0.4)';"
               onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                Đăng nhập lại
            </button>
        `;
        
        // Thêm CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideUp {
                from { 
                    opacity: 0;
                    transform: translateY(30px);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Disable scroll
        document.body.style.overflow = 'hidden';
    }
    
    /**
     * Bắt đầu kiểm tra định kỳ
     */
    function startChecking() {
        if (checkInterval) return;
        
        // Kiểm tra ngay lập tức
        checkUserStatus();
        
        // Sau đó kiểm tra định kỳ
        checkInterval = setInterval(checkUserStatus, CHECK_INTERVAL);
    }
    
    /**
     * Dừng kiểm tra
     */
    function stopChecking() {
        if (checkInterval) {
            clearInterval(checkInterval);
            checkInterval = null;
        }
    }
    
    // Khởi động khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startChecking);
    } else {
        startChecking();
    }
    
    // Dừng khi tab không active để tiết kiệm tài nguyên
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopChecking();
        } else {
            startChecking();
        }
    });
    
    // Export để có thể gọi từ bên ngoài
    window.UserStatusChecker = {
        check: checkUserStatus,
        start: startChecking,
        stop: stopChecking
    };
})();
