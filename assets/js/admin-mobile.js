/**
 * Admin Mobile Menu Handler
 * Wedding Dress Website
 */

(function() {
    'use strict';
    
    // DOM Elements
    let sidebar = null;
    let toggle = null;
    let overlay = null;
    
    /**
     * Initialize admin mobile menu
     */
    function init() {
        sidebar = document.querySelector('aside.w-64');
        toggle = document.getElementById('adminMobileToggle');
        overlay = document.getElementById('adminSidebarOverlay');
        
        if (!sidebar || !toggle || !overlay) {
            // Create toggle button if not exists
            if (!toggle && sidebar) {
                createToggleButton();
            }
            // Create overlay if not exists
            if (!overlay && sidebar) {
                createOverlay();
            }
            
            // Re-get elements
            toggle = document.getElementById('adminMobileToggle');
            overlay = document.getElementById('adminSidebarOverlay');
        }
        
        if (toggle && sidebar && overlay) {
            bindEvents();
        }
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
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
        
        // Header menu toggle button
        const headerToggle = document.getElementById('headerMenuToggle');
        if (headerToggle) {
            headerToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
        }
        
        // Overlay click
        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
        
        // Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
        
        // Close on window resize (if going to desktop)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 1024 && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            }, 100);
        });
        
        // Close sidebar when clicking on a link (mobile)
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    // Small delay to allow navigation
                    setTimeout(closeSidebar, 100);
                }
            });
        });
        
        // Swipe to close sidebar
        let touchStartX = 0;
        sidebar.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });
        
        sidebar.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const diff = touchStartX - touchEndX;
            
            // Swipe left to close
            if (diff > 50) {
                closeSidebar();
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
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Update toggle icon
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
        
        // Focus first link for accessibility
        const firstLink = sidebar.querySelector('a');
        if (firstLink) {
            firstLink.focus();
        }
    }
    
    /**
     * Close sidebar
     */
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Update toggle icon
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
        
        // Return focus to toggle button
        toggle.focus();
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose functions globally if needed
    window.adminMobileMenu = {
        open: openSidebar,
        close: closeSidebar,
        toggle: toggleSidebar
    };
})();
