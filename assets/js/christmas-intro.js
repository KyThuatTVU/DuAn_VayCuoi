// Christmas Intro Animation
document.addEventListener('DOMContentLoaded', function() {
    // Check if intro has been shown before
    if (localStorage.getItem('christmasIntroShown') === 'true') {
        return;
    }

    // Only show on index page
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage !== 'index.php' && currentPage !== '') {
        return;
    }

    // Create intro HTML
    const introHTML = `
        <div class="christmas-intro" id="christmasIntro">
            <!-- Snowflakes -->
            ${createSnowflakes(30)}
            
            <!-- Christmas Scene -->
            <div class="christmas-scene">
                <!-- Santa Claus -->
                <div class="santa">
                    <div class="santa-body">🎅</div>
                </div>
                
                <!-- Reindeer -->
                <div class="reindeer">
                    <div class="reindeer-body">🦌</div>
                </div>
                
                <!-- Christmas Tree -->
                <div class="christmas-tree">
                    <div class="star">⭐</div>
                    <div class="tree-top">
                        <div class="light"></div>
                        <div class="light"></div>
                    </div>
                    <div class="tree-middle">
                        <div class="light"></div>
                        <div class="light"></div>
                    </div>
                    <div class="tree-bottom">
                        <div class="light"></div>
                    </div>
                    <div class="tree-trunk"></div>
                </div>
                
                <!-- Christmas Message -->
                <div class="christmas-message">
                    <h1>🎄 Chúc Mừng Giáng Sinh 🎄</h1>
                    <p>Merry Christmas & Happy New Year! 🎁</p>
                    <div class="loading-spinner">
                        <div class="spinner-dot"></div>
                        <div class="spinner-dot"></div>
                        <div class="spinner-dot"></div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Insert intro into body
    document.body.insertAdjacentHTML('afterbegin', introHTML);

    // Mark as shown immediately
    localStorage.setItem('christmasIntroShown', 'true');

    // Remove intro after animation
    setTimeout(function() {
        const intro = document.getElementById('christmasIntro');
        if (intro) {
            intro.remove();
        }
    }, 5500);
});

// Create snowflakes
function createSnowflakes(count) {
    let snowflakes = '';
    for (let i = 0; i < count; i++) {
        const left = Math.random() * 100;
        const animationDuration = 5 + Math.random() * 10;
        const animationDelay = Math.random() * 5;
        const fontSize = 0.5 + Math.random() * 1;
        
        snowflakes += `
            <div class="snowflake" style="
                left: ${left}%;
                animation-duration: ${animationDuration}s;
                animation-delay: ${animationDelay}s;
                font-size: ${fontSize}em;
            ">❄</div>
        `;
    }
    return snowflakes;
}

// Optional: Add jingle bells sound effect (uncomment if you want sound)
/*
function playChristmasSound() {
    const audio = new Audio('assets/sounds/jingle-bells.mp3');
    audio.volume = 0.3;
    audio.play().catch(e => console.log('Audio autoplay prevented'));
}
*/
