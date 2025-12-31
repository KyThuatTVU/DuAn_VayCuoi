// Tết 2026 Bính Ngọ Intro Animation
document.addEventListener('DOMContentLoaded', function() {
    // Check if intro has been shown in this session
    if (sessionStorage.getItem('tetIntroShown') === 'true') {
        return;
    }

    // Only show on index page
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage !== 'index.php' && currentPage !== '') {
        return;
    }

    // Create intro HTML
    const introHTML = `
        <div class="tet-intro" id="tetIntro">
            <!-- Hoa mai rơi đẹp với SVG -->
            ${createMaiFlowers(30)}
            
            <!-- Pháo hoa -->
            <div class="fireworks-container">
                <div class="firework firework-1"></div>
                <div class="firework firework-2"></div>
                <div class="firework firework-3"></div>
            </div>
            
            <!-- Tết Scene -->
            <div class="tet-scene">
                <!-- Con Ngựa - Bính Ngọ 2026 -->
                <div class="horse-container">
                    <div class="horse">🐴</div>
                    <div class="horse-shadow"></div>
                </div>
                
                <!-- Cành mai bên phải với SVG -->
                <div class="mai-branch">
                    <div class="mai-flower mai-1">${createMaiFlowerSVG(35, 'yellow')}</div>
                    <div class="mai-flower mai-2">${createMaiFlowerSVG(28, 'gold')}</div>
                    <div class="mai-flower mai-3">${createMaiFlowerSVG(32, 'yellow')}</div>
                    <div class="mai-flower mai-4">${createMaiFlowerSVG(25, 'pink')}</div>
                    <div class="mai-flower mai-5">${createMaiFlowerSVG(30, 'yellow')}</div>
                    <div class="mai-flower mai-6">${createMaiFlowerSVG(28, 'gold')}</div>
                    <div class="mai-flower mai-7">${createMaiFlowerSVG(22, 'yellow')}</div>
                </div>
                
                <!-- Cành mai bên trái với SVG -->
                <div class="mai-branch-left">
                    <div class="mai-flower mai-1">${createMaiFlowerSVG(30, 'gold')}</div>
                    <div class="mai-flower mai-2">${createMaiFlowerSVG(35, 'yellow')}</div>
                    <div class="mai-flower mai-3">${createMaiFlowerSVG(25, 'yellow')}</div>
                    <div class="mai-flower mai-4">${createMaiFlowerSVG(32, 'pink')}</div>
                    <div class="mai-flower mai-5">${createMaiFlowerSVG(28, 'yellow')}</div>
                    <div class="mai-flower mai-6">${createMaiFlowerSVG(30, 'gold')}</div>
                    <div class="mai-flower mai-7">${createMaiFlowerSVG(24, 'yellow')}</div>
                </div>
                
                <!-- Đèn lồng -->
                <div class="lantern lantern-left">🏮</div>
                <div class="lantern lantern-right">🏮</div>
                
                <!-- Tết Message -->
                <div class="tet-message">
                    <div class="tet-year">2026</div>
                    <h1>🧧 Chúc Mừng Năm Mới 🧧</h1>
                    <p class="tet-zodiac">Năm Bính Ngọ - Mã Đáo Thành Công</p>
                    <p class="tet-wish">Chúc Quý Khách An Khang Thịnh Vượng! 🎊</p>
                    <div class="loading-spinner">
                        <div class="spinner-dot"></div>
                        <div class="spinner-dot"></div>
                        <div class="spinner-dot"></div>
                    </div>
                </div>
            </div>
            
            <!-- Bánh chưng decoration -->
            <div class="banh-chung banh-left">🟩</div>
            <div class="banh-chung banh-right">🟩</div>
        </div>
    `;

    // Insert intro into body
    document.body.insertAdjacentHTML('afterbegin', introHTML);

    // Mark as shown in session
    sessionStorage.setItem('tetIntroShown', 'true');

    // Remove intro after animation
    setTimeout(function() {
        const intro = document.getElementById('tetIntro');
        if (intro) {
            intro.remove();
        }
    }, 5500);
});

// Tạo SVG hoa mai đẹp với 5 cánh
function createMaiFlowerSVG(size, colorType) {
    const colors = {
        yellow: {
            petal: '#FFD700',
            petalGradient: '#FFA500',
            center: '#8B4513',
            centerDot: '#FFE4B5'
        },
        pink: {
            petal: '#FFB6C1',
            petalGradient: '#FF69B4',
            center: '#8B0000',
            centerDot: '#FFE4E1'
        },
        white: {
            petal: '#FFFAF0',
            petalGradient: '#FFE4B5',
            center: '#DAA520',
            centerDot: '#FFFACD'
        },
        gold: {
            petal: '#FFD700',
            petalGradient: '#FF8C00',
            center: '#8B0000',
            centerDot: '#FFFACD'
        }
    };
    
    const c = colors[colorType] || colors.yellow;
    const id = Math.random().toString(36).substr(2, 9);
    
    return `
        <svg width="${size}" height="${size}" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="petalGrad${id}" cx="30%" cy="30%">
                    <stop offset="0%" style="stop-color:${c.petal};stop-opacity:1" />
                    <stop offset="100%" style="stop-color:${c.petalGradient};stop-opacity:0.9" />
                </radialGradient>
                <radialGradient id="centerGrad${id}" cx="40%" cy="40%">
                    <stop offset="0%" style="stop-color:${c.centerDot};stop-opacity:1" />
                    <stop offset="100%" style="stop-color:${c.center};stop-opacity:1" />
                </radialGradient>
                <filter id="glow${id}" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                    <feMerge>
                        <feMergeNode in="coloredBlur"/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
            
            <!-- 5 cánh hoa mai -->
            <g filter="url(#glow${id})">
                <!-- Cánh 1 - trên -->
                <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#petalGrad${id})" transform="rotate(0, 50, 50)"/>
                <!-- Cánh 2 -->
                <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#petalGrad${id})" transform="rotate(72, 50, 50)"/>
                <!-- Cánh 3 -->
                <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#petalGrad${id})" transform="rotate(144, 50, 50)"/>
                <!-- Cánh 4 -->
                <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#petalGrad${id})" transform="rotate(216, 50, 50)"/>
                <!-- Cánh 5 -->
                <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#petalGrad${id})" transform="rotate(288, 50, 50)"/>
            </g>
            
            <!-- Nhụy hoa ở giữa -->
            <circle cx="50" cy="50" r="10" fill="url(#centerGrad${id})"/>
            
            <!-- Các chấm nhụy nhỏ -->
            <circle cx="50" cy="44" r="2" fill="${c.centerDot}" opacity="0.9"/>
            <circle cx="55" cy="48" r="1.5" fill="${c.centerDot}" opacity="0.8"/>
            <circle cx="53" cy="54" r="1.5" fill="${c.centerDot}" opacity="0.8"/>
            <circle cx="47" cy="54" r="1.5" fill="${c.centerDot}" opacity="0.8"/>
            <circle cx="45" cy="48" r="1.5" fill="${c.centerDot}" opacity="0.8"/>
        </svg>
    `;
}

// Tạo SVG cánh hoa mai rơi riêng lẻ
function createPetalSVG(size, colorType) {
    const colors = {
        yellow: { main: '#FFD700', gradient: '#FFA500' },
        pink: { main: '#FFB6C1', gradient: '#FF69B4' },
        white: { main: '#FFFAF0', gradient: '#FFE4B5' },
        gold: { main: '#FFD700', gradient: '#FF8C00' }
    };
    
    const c = colors[colorType] || colors.yellow;
    const id = Math.random().toString(36).substr(2, 9);
    
    return `
        <svg width="${size}" height="${size * 1.5}" viewBox="0 0 30 45" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="petalGrad${id}" cx="30%" cy="30%">
                    <stop offset="0%" style="stop-color:${c.main};stop-opacity:1" />
                    <stop offset="100%" style="stop-color:${c.gradient};stop-opacity:0.85" />
                </radialGradient>
            </defs>
            <ellipse cx="15" cy="22" rx="10" ry="18" fill="url(#petalGrad${id})" 
                     style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2))"/>
        </svg>
    `;
}

// Create mai flowers falling with beautiful SVG
function createMaiFlowers(count) {
    let flowers = '';
    const colorTypes = ['yellow', 'pink', 'white', 'gold', 'yellow', 'yellow']; // Nhiều hoa vàng hơn
    
    // Tạo hoa mai đầy đủ
    for (let i = 0; i < count; i++) {
        const left = Math.random() * 100;
        const fallDuration = 8 + Math.random() * 8;
        const swayDuration = 3 + Math.random() * 3;
        const spinDuration = 10 + Math.random() * 15;
        const animationDelay = Math.random() * 5;
        const size = 30 + Math.random() * 35;
        const colorType = colorTypes[Math.floor(Math.random() * colorTypes.length)];
        const sparkle = Math.random() > 0.6 ? 'sparkle' : '';
        
        flowers += `
            <div class="falling-flower ${sparkle}" style="
                left: ${left}%;
                animation: flowerFall ${fallDuration}s linear infinite, 
                           flowerSway ${swayDuration}s ease-in-out infinite,
                           flowerSpin ${spinDuration}s linear infinite;
                animation-delay: ${animationDelay}s;
            ">${createMaiFlowerSVG(size, colorType)}</div>
        `;
    }
    
    // Thêm cánh hoa mai rơi riêng lẻ
    const petalCount = Math.floor(count * 0.6);
    for (let i = 0; i < petalCount; i++) {
        const left = Math.random() * 100;
        const fallDuration = 6 + Math.random() * 6;
        const driftDuration = 2 + Math.random() * 2;
        const animationDelay = Math.random() * 6;
        const size = 12 + Math.random() * 15;
        const colorType = colorTypes[Math.floor(Math.random() * colorTypes.length)];
        
        flowers += `
            <div class="falling-petal" style="
                left: ${left}%;
                animation: petalFall ${fallDuration}s linear infinite, 
                           petalDrift ${driftDuration}s ease-in-out infinite;
                animation-delay: ${animationDelay}s;
            ">${createPetalSVG(size, colorType)}</div>
        `;
    }
    
    return flowers;
}
