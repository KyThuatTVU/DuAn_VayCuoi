/**
 * Mobile Enhancements JavaScript
 * Wedding Dress Website - Enhanced v3.0
 */

(function() {
    'use strict';

    const config = {
        breakpoints: {
            mobile: 480,
            tablet: 768,
            desktop: 1024
        },
        swipeThreshold: 80,
        scrollThreshold: 50
    };

    let touchStartX = 0;
    let touchStartY = 0;
    let touchEndX = 0;
    let touchEndY = 0;


    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setup);
        } else {
            setup();
        }
    }

    function setup() {
        initMobileMenu();
        initTouchGestures();
        initScrollBehavior();
        handleOrientationChange();
        initPullToRefresh();
        initLazyLoading();
        updateCartBadge();
    }

    /**
     * Initialize Mobile Menu
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        const closeBtn = document.querySelector('.mobile-menu-close');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', toggleMobileMenu);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closeMobileMenu);
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeMobileMenu);
        }

        // Submenu toggles
        const submenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const wrapper = this.closest('.mobile-submenu-wrapper');
                const submenu = wrapper.querySelector('.mobile-submenu');
                const icon = this.querySelector('svg:last-child');
                
                if (submenu) {
                    submenu.classList.toggle('hidden');
                    if (icon) {
                        icon.style.transform = submenu.classList.contains('hidden') ? '' : 'rotate(180deg)';
                    }
                }
            });
        });
    }

    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        const toggle = document.querySelector('.mobile-menu-toggle');
        
        if (mobileMenu && overlay) {
            const isOpen = !mobileMenu.classList.contains('translate-x-full');
            
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
            
            if (toggle) {
                toggle.classList.toggle('active', !isOpen);
            }
        }
    }

    function openMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        
        if (mobileMenu) {
            mobileMenu.classList.remove('translate-x-full');
            mobileMenu.classList.add('active');
        }
        if (overlay) {
            overlay.classList.remove('invisible', 'opacity-0');
            overlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        const toggle = document.querySelector('.mobile-menu-toggle');
        
        if (mobileMenu) {
            mobileMenu.classList.add('translate-x-full');
            mobileMenu.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.add('invisible', 'opacity-0');
            overlay.classList.remove('active');
        }
        if (toggle) {
            toggle.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    /**
     * Update cart badge count
     */
    function updateCartBadge() {
        const badges = document.querySelectorAll('.cart-badge, .cart-count');
        
        fetch('api/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                badges.forEach(badge => {
                    if (data.success && data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                });
            })
            .catch(() => {
                badges.forEach(badge => {
                    badge.style.display = 'none';
                });
            });
    }

    /**
     * Initialize Touch Gestures
     */
    function initTouchGestures() {
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });
        
        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe();
        }, { passive: true });
    }

    function handleSwipe() {
        const diffX = touchEndX - touchStartX;
        const diffY = touchEndY - touchStartY;
        
        // Only handle horizontal swipes (ignore vertical scrolling)
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > config.swipeThreshold) {
            if (diffX > 0) {
                // Swipe right - close mobile menu if open
                const mobileMenu = document.getElementById('mobileMenu');
                if (mobileMenu && mobileMenu.classList.contains('active')) {
                    closeMobileMenu();
                }
            } else {
                // Swipe left - open mobile menu if at edge
                if (touchStartX > window.innerWidth - 30 && window.innerWidth < config.breakpoints.desktop) {
                    openMobileMenu();
                }
            }
        }
    }

    /**
     * Initialize Scroll Behavior
     */
    function initScrollBehavior() {
        // Mobile bottom nav đã bị xóa - không cần xử lý scroll behavior
    }

    /**
     * Handle Orientation Change
     */
    function handleOrientationChange() {
        let resizeTimeout;
        
        const handleResize = () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                // Close mobile menu on resize to desktop
                if (window.innerWidth >= config.breakpoints.desktop) {
                    closeMobileMenu();
                }
            }, 100);
        };
        
        window.addEventListener('resize', handleResize);
        window.addEventListener('orientationchange', handleResize);
    }

    /**
     * Pull to Refresh (Optional - for PWA feel)
     */
    function initPullToRefresh() {
        if (window.innerWidth >= config.breakpoints.tablet) return;
        
        let startY = 0;
        let pulling = false;
        
        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].pageY;
                pulling = true;
            }
        }, { passive: true });
        
        document.addEventListener('touchmove', (e) => {
            if (!pulling) return;
            
            const currentY = e.touches[0].pageY;
            const diff = currentY - startY;
            
            if (diff > 100 && window.scrollY === 0) {
                // Show pull indicator (optional)
            }
        }, { passive: true });
        
        document.addEventListener('touchend', () => {
            pulling = false;
        }, { passive: true });
    }

    /**
     * Lazy Loading for Images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Smooth scroll to element
     */
    function smoothScrollTo(element, offset = 80) {
        const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }

    /**
     * Show toast notification
     */
    function showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-24 left-4 right-4 mx-auto max-w-sm px-4 py-3 rounded-xl shadow-lg z-[9999] text-center font-medium transform transition-all duration-300 translate-y-full opacity-0`;
        
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-gray-900',
            info: 'bg-blue-500 text-white'
        };
        
        toast.classList.add(...(colors[type] || colors.info).split(' '));
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
        });
        
        // Remove after duration
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // Initialize
    init();

    // Expose functions globally
    window.mobileEnhancements = {
        updateCartBadge,
        openMobileMenu,
        closeMobileMenu,
        smoothScrollTo,
        showToast
    };

})();
