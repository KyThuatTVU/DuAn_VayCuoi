// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
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
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
                backToTop.style.display = 'flex';
            } else {
                backToTop.classList.remove('show');
                backToTop.style.display = 'none';
            }
        });

        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
