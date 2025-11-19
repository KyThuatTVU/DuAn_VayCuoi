// Search Toggle
document.querySelector('.search-toggle')?.addEventListener('click', function() {
    document.querySelector('.search-bar').classList.toggle('active');
});

// Mobile Menu Toggle
document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function() {
    document.querySelector('.main-nav').classList.toggle('active');
});

// Back to Top Button
const backToTop = document.querySelector('.back-to-top');
window.addEventListener('scroll', function() {
    if (window.scrollY > 300) {
        backToTop?.classList.add('show');
    } else {
        backToTop?.classList.remove('show');
    }
});

backToTop?.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Chatbot Toggle
document.querySelector('.chatbot-toggle')?.addEventListener('click', function() {
    alert('Chatbot sẽ được tích hợp sau!');
});

// Hero Slider (Simple)
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');

function showSlide(n) {
    slides.forEach(slide => slide.classList.remove('active'));
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide]?.classList.add('active');
}

// Auto slide every 5 seconds
if (slides.length > 0) {
    setInterval(() => showSlide(currentSlide + 1), 5000);
}

// Product Quick View (placeholder)
document.querySelectorAll('.product-actions .btn-icon').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const action = this.title;
        alert(action + ' - Chức năng đang phát triển!');
    });
});
