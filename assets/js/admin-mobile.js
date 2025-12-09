/**
 * Admin Mobile Menu Handler - Enhanced v2.0
 * Wedding Dress Website
 */

(function() {
    'use strict';
    
    // DOM Elements
    let sidebar = null;
    let toggle = null;
    let headerToggle = null;
    let overlay = null;
    let isInitialized = false;
    
    /**
     * Initialize admin mobile menu
     */
    function init() {
        if (isInitialized) return;
        
        sidebar = document.querySelector('aside.w-64, .admin-sidebar');
        toggle = document.getElementById('adminMobileToggle');
        headerToggle = document.getElementById('headerMenuToggle');
        overlay = document.getElementById('adminSidebarOverlay');
        
        if (!sidebar) return;
        
        // Create toggle button if not exists
        if (!toggle) {
            createToggleButton();
            toggle = document.getElementById('adminMobileToggle');
        }
        
        // Create overlay if not exists
        if (!overlay) {
            createOverlay();
            overlay = document.getElementById('adminSidebarOverlay');
        }
        
        bindEvents();
        isInitialized = true;
    }
    
    /**
     * Create mobile toggle button
     */
    function createToggleButton() {
        const btn = document.createElement('button');
        btn.className = 'admin-mobile-toggle';
        btn.id = 'adminMobileToggle';
        btn.setAttribute('aria-label', 'Toggle Menu');
        btn.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(btn);
    }
    
    /**
     * Create sidebar overlay
     */
    function createOverlay() {
        const div = document.createElement('div');
        div.className = 'admin-sidebar-overlay';
        div.id = 'adminSidebarOverlay';
        document.body.appendChild(div);
    }
    
    /**
     * Bind event listeners
     */
    function bindEvents() {
        // Toggle button click (floating button)
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
        }
        
        // Header menu toggle button
        if (headerToggle) {
            headerToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
        }
        
        // Overlay click
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }
        
        // Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
        
        // Close on window resize (if going to desktop)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 1024 && sidebar && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            }, 100);
        });
        
        // Close sidebar when clicking on a link (mobile)
        if (sidebar) {
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        // Small delay to allow navigation
                        setTimeout(closeSidebar, 100);
                    }
                });
            });
        }
        
        // Swipe to close sidebar
        if (sidebar) {
            let touchStartX = 0;
            let touchStartY = 0;
            
            sidebar.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }, { passive: true });
            
            sidebar.addEventListener('touchend', function(e) {
                const touchEndX = e.changedTouches[0].clientX;
                const touchEndY = e.changedTouches[0].clientY;
                const diffX = touchStartX - touchEndX;
                const diffY = Math.abs(touchStartY - touchEndY);
                
                // Swipe left to close (only if horizontal swipe)
                if (diffX > 50 && diffY < 50) {
                    closeSidebar();
                }
            }, { passive: true });
        }
        
        // Swipe from left edge to open
        let edgeTouchStartX = 0;
        document.addEventListener('touchstart', function(e) {
            edgeTouchStartX = e.touches[0].clientX;
        }, { passive: true });
        
        document.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            // If swipe starts from left edge (within 20px) and moves right
            if (edgeTouchStartX < 20 && touchEndX - edgeTouchStartX > 50 && sidebar && !sidebar.classList.contains('active')) {
                openSidebar();
            }
        }, { passive: true });
    }
    
    /**
     * Toggle sidebar
     */
    function toggleSidebar() {
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
    
    /**
     * Open sidebar
     */
    function openSidebar() {
        if (!sidebar || !overlay) return;
        
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        
        // Update toggle icon
        if (toggle) {
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        }
        
        // Focus first link for accessibility
        const firstLink = sidebar.querySelector('nav a');
        if (firstLink) {
            setTimeout(function() {
                firstLink.focus();
            }, 300);
        }
    }
    
    /**
     * Close sidebar
     */
    function closeSidebar() {
        if (!sidebar || !overlay) return;
        
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
        
        // Update toggle icon
        if (toggle) {
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
        
        // Return focus to toggle button
        if (toggle) {
            toggle.focus();
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Also try to initialize on window load (backup)
    window.addEventListener('load', init);
    
    // Expose functions globally if needed
    window.adminMobileMenu = {
        open: openSidebar,
        close: closeSidebar,
        toggle: toggleSidebar,
        init: init
    };
})();
