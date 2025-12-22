// Voucher Popup - Show after Christmas intro
document.addEventListener('DOMContentLoaded', function() {
    // Only show on index page
    const currentPage = window.location.pathname.split('/').pop();
    console.log('Current page:', currentPage);
    
    if (currentPage !== 'index.php' && currentPage !== '') {
        console.log('Not on index page, skipping voucher popup');
        return;
    }

    console.log('On index page, will show voucher popup after 6 seconds');
    
    // Wait for Christmas intro to finish (5.5 seconds) + 0.5 second delay
    setTimeout(function() {
        showVoucherPopup();
    }, 6000);
});

function showVoucherPopup() {
    console.log('Fetching latest promotion...');
    
    // Fetch latest active promotion
    fetch(window.siteUrl + '/api/get-latest-promotion.php')
        .then(response => {
            console.log('API response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('API data:', data);
            if (data.success && data.promotion) {
                console.log('Creating voucher popup with promotion:', data.promotion);
                createVoucherPopup(data.promotion);
            } else {
                console.log('No active promotion found:', data.message);
            }
        })
        .catch((error) => {
            console.error('Error fetching promotion:', error);
        });
}

function createVoucherPopup(promotion) {
    // Create confetti elements
    let confettiHTML = '';
    for (let i = 0; i < 20; i++) {
        const left = Math.random() * 100;
        const delay = Math.random() * 3;
        confettiHTML += `<div class="confetti" style="left: ${left}%; animation-delay: ${delay}s;"></div>`;
    }

    // Format end date
    let validityText = '';
    if (promotion.end_date) {
        const endDate = new Date(promotion.end_date);
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        validityText = `Có hiệu lực đến: ${endDate.toLocaleDateString('vi-VN', options)}`;
    }

    const popupHTML = `
        <div class="voucher-popup-overlay" id="voucherPopup">
            <div class="voucher-popup">
                <!-- Decorative elements -->
                <div class="voucher-decoration"></div>
                <div class="voucher-decoration"></div>
                
                <!-- Confetti -->
                ${confettiHTML}
                
                <!-- Close button -->
                <button class="voucher-close-btn" onclick="closeVoucherPopup()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <!-- Content -->
                <div class="voucher-content">
                    <!-- Badge -->
                    <div class="voucher-badge">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>Ưu Đãi Đặc Biệt</span>
                    </div>
                    
                    <!-- Title -->
                    <h2 class="voucher-title">${escapeHtml(promotion.title)}</h2>
                    
                    <!-- Discount -->
                    <div class="voucher-discount">${escapeHtml(promotion.discount_value)}</div>
                    
                    <!-- Subtitle -->
                    ${promotion.subtitle ? `<p class="voucher-subtitle">${escapeHtml(promotion.subtitle)}</p>` : ''}
                    
                    <!-- Description -->
                    ${promotion.description ? `<p class="voucher-description">${escapeHtml(promotion.description)}</p>` : ''}
                    
                    <!-- Promo Code -->
                    ${promotion.promo_code ? `
                    <div class="voucher-code-box">
                        <div class="voucher-code-label">Mã giảm giá của bạn</div>
                        <div class="voucher-code-value" id="promoCode">${escapeHtml(promotion.promo_code)}</div>
                        <button class="voucher-copy-btn" onclick="copyVoucherCode('${escapeHtml(promotion.promo_code)}')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Sao chép mã
                        </button>
                    </div>
                    ` : ''}
                    
                    <!-- Validity -->
                    ${validityText ? `
                    <div class="voucher-validity">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>${validityText}</span>
                    </div>
                    ` : ''}
                    
                    <!-- Action Buttons -->
                    <div class="voucher-actions">
                        <button class="voucher-btn voucher-btn-primary" onclick="window.location.href='products.php'">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Mua sắm ngay
                        </button>
                        <button class="voucher-btn voucher-btn-secondary" onclick="closeVoucherPopup()">
                            Để sau
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Insert popup into body
    document.body.insertAdjacentHTML('beforeend', popupHTML);

    // Show popup with animation
    setTimeout(function() {
        document.getElementById('voucherPopup').classList.add('active');
    }, 100);
}

function closeVoucherPopup() {
    const popup = document.getElementById('voucherPopup');
    if (popup) {
        popup.classList.remove('active');
        setTimeout(function() {
            popup.remove();
        }, 300);
    }
}

function copyVoucherCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show success notification
        const notification = document.createElement('div');
        notification.className = 'copy-notification';
        notification.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Đã sao chép mã: <strong>${code}</strong></span>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }).catch(function(err) {
        console.error('Failed to copy code:', err);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close popup when clicking outside
document.addEventListener('click', function(e) {
    const popup = document.getElementById('voucherPopup');
    if (popup && e.target === popup) {
        closeVoucherPopup();
    }
});

// Close popup with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVoucherPopup();
    }
});
