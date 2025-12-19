// Snowfall Effect
(function() {
    'use strict';
    
    function createSnowfall() {
        // Check if container already exists
        if (document.getElementById('snowfall-container')) {
            return;
        }
        
        const container = document.createElement('div');
        container.id = 'snowfall-container';
        document.body.appendChild(container);
        
        const snowflakeCount = 150; // Number of snowflakes
        const snowflakeChars = ['❄', '❅', '❆', '✻', '✼', '❉'];
        
        function createSnowflake() {
            const snowflake = document.createElement('div');
            snowflake.className = 'snowflake';
            snowflake.textContent = snowflakeChars[Math.floor(Math.random() * snowflakeChars.length)];
            snowflake.style.left = Math.random() * 100 + '%';
            snowflake.style.animationDelay = Math.random() * 10 + 's';
            snowflake.style.opacity = Math.random() * 0.6 + 0.4;
            
            container.appendChild(snowflake);
            
            // Remove and recreate snowflake after animation
            const duration = parseFloat(getComputedStyle(snowflake).animationDuration) * 1000;
            setTimeout(() => {
                if (snowflake.parentNode) {
                    snowflake.remove();
                    createSnowflake();
                }
            }, duration + (parseFloat(snowflake.style.animationDelay) * 1000));
        }
        
        // Create initial snowflakes
        for (let i = 0; i < snowflakeCount; i++) {
            setTimeout(() => createSnowflake(), i * 200);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createSnowfall);
    } else {
        createSnowfall();
    }
})();
