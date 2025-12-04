/**
 * Mobile Enhancements JavaScript
 * Wedding Dress Website
 */

(function() {
    'use strict';

    const config = {
        breakpoints: {
            tablet: 768,
            desktop: 1024
        },
        swipeThreshold: 100
    };

    let touchStartX = 0;
    let touchEndX = 0;

    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setup);
        } else {
            setup();
        }
    }

    function setup() {
        createMobileBottomNav();
        initTouchGestures();
        handleOrientationChange();
    }

    /**
     * Create Mobile Bottom Navigation
     */
    function createMobileBottomNav() {
        if (window.innerWidth >= config.breakpoints.tablet) return;
        if (document.querySelector('.mobile-bottom-nav')) return;

        const currentPage = window.location.pathname.split('/').pop() || 'index.php';
        
        const nav = document.createElement('nav');
        nav.className = 'mobile-bottom-nav';
        nav.innerHTML = `
            <div class="nav-items">
                <a href="index.php" class="nav-item ${currentPage === 'index.php' || currentPage === '' ? 'active' : ''}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Trang chủ</span>
                </a>
                <a href="products.php" class="nav-item ${currentPage === 'products.php' || currentPage === 'product-detail.php' ? 'active' : ''}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span>Váy cưới</span>
                </a>
                <a href="cart.php" class="nav-item ${currentPage === 'cart.php' ? 'active' : ''}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span>Giỏ hàng</span>
                    <span class="badge cart-badge" style="display: none;">0</span>
                </a>
                <a href="account.php" class="nav-item ${currentPage === 'account.php' || currentPage === 'login.php' ? 'active' : ''}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Tài khoản</span>
                </a>
            </div>
        `;
        
        document.body.appendChild(nav);
        updateCartBadge();
    }

    /**
     * Update cart badge count
     */
    function updateCartBadge() {
        const badge = document.querySelector('.mobile-bottom-nav .cart-badge');
        if (!badge) return;
        
        fetch('api/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(() => {
                badge.style.display = 'none';
            });
    }

    /**
     * Initialize Touch Gestures
     */
    function initTouchGestures() {
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }

    function handleSwipe() {
        const diffX = touchEndX - touchStartX;
        
        if (diffX > config.swipeThreshold) {
            // Swipe right - close mobile menu
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileMenu && mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            }
        }
    }

    function closeMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        
        if (mobileMenu) {
            mobileMenu.classList.add('translate-x-full');
            mobileMenu.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.add('invisible', 'opacity-0');
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    /**
     * Handle Orientation Change
     */
    function handleOrientationChange() {
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                const bottomNav = document.querySelector('.mobile-bottom-nav');
                if (bottomNav) {
                    bottomNav.style.display = window.innerWidth >= config.breakpoints.tablet ? 'none' : 'block';
                }
            }, 100);
        });
    }

    // Initialize
    init();

    // Expose functions globally
    window.mobileEnhancements = {
        updateCartBadge
    };

})();
