// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    function openMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.remove('translate-x-full');
            mobileMenu.classList.add('active');
            // Force reflow
            void mobileMenu.offsetWidth;
        }
        if (mobileMenuOverlay) {
            mobileMenuOverlay.classList.remove('invisible', 'opacity-0');
            mobileMenuOverlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        
        // Toggle button animation
        if (mobileMenuToggle) {
            mobileMenuToggle.classList.add('active');
        }
    }

    function closeMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.add('translate-x-full');
            mobileMenu.classList.remove('active');
        }
        if (mobileMenuOverlay) {
            mobileMenuOverlay.classList.add('invisible', 'opacity-0');
            mobileMenuOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        
        // Toggle button animation
        if (mobileMenuToggle) {
            mobileMenuToggle.classList.remove('active');
        }
    }

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (mobileMenu && mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function(e) {
            e.preventDefault();
            closeMobileMenu();
        });
    }

    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }
    
    // Close mobile menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });
    
    // Close mobile menu on window resize (if going to desktop)
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth >= 1024 && mobileMenu && mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            }
        }, 100);
    });

    // Mobile Submenu Toggle
    const mobileSubmenuToggles = document.querySelectorAll('.mobile-submenu-toggle');
    console.log('Found submenu toggles:', mobileSubmenuToggles.length);
    
    mobileSubmenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            const icon = this.querySelector('svg:last-child');
            
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            }
        });
    });
    
    // Search Toggle
    const searchToggle = document.querySelector('.search-toggle');
    const searchBar = document.querySelector('.search-bar');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', function() {
            searchBar.classList.toggle('active');
        });
    }

    // Back to Top Button
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        // Throttle scroll event for better performance
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) return;
            scrollTimeout = setTimeout(function() {
                scrollTimeout = null;
                if (window.scrollY > 300) {
                    backToTop.classList.add('show');
                    backToTop.style.display = 'flex';
                } else {
                    backToTop.classList.remove('show');
                    backToTop.style.display = 'none';
                }
            }, 100);
        }, { passive: true });

        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // Mobile Filter Modal
    const mobileFilterBtn = document.getElementById('mobile-filter-btn');
    const mobileFilterModal = document.getElementById('mobile-filter-modal');
    const closeMobileFilter = document.getElementById('close-mobile-filter');
    
    if (mobileFilterBtn && mobileFilterModal) {
        mobileFilterBtn.addEventListener('click', function() {
            mobileFilterModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        
        if (closeMobileFilter) {
            closeMobileFilter.addEventListener('click', function() {
                mobileFilterModal.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }
        
        // Close on overlay click
        mobileFilterModal.addEventListener('click', function(e) {
            if (e.target === mobileFilterModal) {
                mobileFilterModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    }
    
    // Sync mobile and desktop filters
    const priceFilter = document.getElementById('price-filter');
    const mobilePriceFilter = document.getElementById('mobile-price-filter');
    const priceDisplay = document.getElementById('price-display');
    const mobilePriceDisplay = document.getElementById('mobile-price-display');
    
    function formatPriceDisplay(value) {
        return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
    }
    
    if (priceFilter && priceDisplay) {
        priceFilter.addEventListener('input', function() {
            priceDisplay.textContent = formatPriceDisplay(this.value);
            if (mobilePriceFilter) mobilePriceFilter.value = this.value;
            if (mobilePriceDisplay) mobilePriceDisplay.textContent = formatPriceDisplay(this.value);
        });
    }
    
    if (mobilePriceFilter && mobilePriceDisplay) {
        mobilePriceFilter.addEventListener('input', function() {
            mobilePriceDisplay.textContent = formatPriceDisplay(this.value);
            if (priceFilter) priceFilter.value = this.value;
            if (priceDisplay) priceDisplay.textContent = formatPriceDisplay(this.value);
        });
    }

    // Chatbot Toggle
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    if (chatbotToggle) {
        chatbotToggle.addEventListener('click', function() {
            const chatbotWidget = document.querySelector('.chatbot-widget');
            if (chatbotWidget) {
                chatbotWidget.classList.toggle('active');
            }
        });
    }

    // Hero Slider (Simple)
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');

    function showSlide(n) {
        if (slides.length === 0) return;
        slides.forEach(slide => slide.classList.remove('active'));
        currentSlide = (n + slides.length) % slides.length;
        if (slides[currentSlide]) {
            slides[currentSlide].classList.add('active');
        }
    }

    // Auto slide every 5 seconds
    if (slides.length > 0) {
        setInterval(() => showSlide(currentSlide + 1), 5000);
    }

    // Product Quick View (placeholder)
    const productActionBtns = document.querySelectorAll('.product-actions .btn-icon');
    productActionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.title || 'Action';
            console.log(action + ' - Chức năng đang phát triển!');
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Form validation helper
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ các trường bắt buộc!');
            }
        });
    });

    // Image lazy loading fallback
    const images = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }

    // Notification helper
    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-24 right-6 px-6 py-4 rounded-xl shadow-2xl z-50 animate-slide-in ${
            type === 'success' ? 'bg-gradient-to-r from-emerald-500 to-teal-600' : 
            type === 'error' ? 'bg-gradient-to-r from-red-500 to-rose-600' : 
            'bg-gradient-to-r from-blue-500 to-cyan-600'
        } text-white`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' : 
                      type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' :
                      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'}
                </svg>
                <span class="font-semibold">${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    };

    // Login modal helper
    window.showLoginModal = function() {
        if (confirm('Bạn cần đăng nhập để sử dụng tính năng này. Chuyển đến trang đăng nhập?')) {
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        }
    };

    console.log('Main.js loaded successfully');
});
