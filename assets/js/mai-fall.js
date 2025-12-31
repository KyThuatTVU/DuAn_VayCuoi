// Hiệu ứng hoa mai rơi
(function () {
    "use strict";

    // Tạo SVG hoa mai 5 cánh
    function createMaiSVG(size, colorType) {
        const colors = {
            yellow: { petal: "#FFD700", gradient: "#FFA500", center: "#8B4513", dot: "#FFE4B5" },
            pink: { petal: "#FFB6C1", gradient: "#FF69B4", center: "#8B0000", dot: "#FFE4E1" },
            white: { petal: "#FFFAF0", gradient: "#FFE4B5", center: "#DAA520", dot: "#FFFACD" },
            gold: { petal: "#FFD700", gradient: "#FF8C00", center: "#8B0000", dot: "#FFFACD" },
        };

        const c = colors[colorType] || colors.yellow;
        const id = Math.random().toString(36).substr(2, 9);

        return `
            <svg width="${size}" height="${size}" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="pg${id}" cx="30%" cy="30%">
                        <stop offset="0%" style="stop-color:${c.petal};stop-opacity:1" />
                        <stop offset="100%" style="stop-color:${c.gradient};stop-opacity:0.9" />
                    </radialGradient>
                    <radialGradient id="cg${id}" cx="40%" cy="40%">
                        <stop offset="0%" style="stop-color:${c.dot};stop-opacity:1" />
                        <stop offset="100%" style="stop-color:${c.center};stop-opacity:1" />
                    </radialGradient>
                </defs>
                <g>
                    <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#pg${id})" transform="rotate(0, 50, 50)"/>
                    <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#pg${id})" transform="rotate(72, 50, 50)"/>
                    <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#pg${id})" transform="rotate(144, 50, 50)"/>
                    <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#pg${id})" transform="rotate(216, 50, 50)"/>
                    <ellipse cx="50" cy="25" rx="12" ry="20" fill="url(#pg${id})" transform="rotate(288, 50, 50)"/>
                </g>
                <circle cx="50" cy="50" r="10" fill="url(#cg${id})"/>
                <circle cx="50" cy="44" r="2" fill="${c.dot}" opacity="0.9"/>
                <circle cx="55" cy="48" r="1.5" fill="${c.dot}" opacity="0.8"/>
                <circle cx="53" cy="54" r="1.5" fill="${c.dot}" opacity="0.8"/>
                <circle cx="47" cy="54" r="1.5" fill="${c.dot}" opacity="0.8"/>
                <circle cx="45" cy="48" r="1.5" fill="${c.dot}" opacity="0.8"/>
            </svg>
        `;
    }

    // Tạo SVG cánh hoa mai
    function createPetalSVG(size, colorType) {
        const colors = {
            yellow: { main: "#FFD700", gradient: "#FFA500" },
            pink: { main: "#FFB6C1", gradient: "#FF69B4" },
            white: { main: "#FFFAF0", gradient: "#FFE4B5" },
            gold: { main: "#FFD700", gradient: "#FF8C00" },
        };

        const c = colors[colorType] || colors.yellow;
        const id = Math.random().toString(36).substr(2, 9);

        return `
            <svg width="${size}" height="${size * 1.5}" viewBox="0 0 30 45" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="ptl${id}" cx="30%" cy="30%">
                        <stop offset="0%" style="stop-color:${c.main};stop-opacity:1" />
                        <stop offset="100%" style="stop-color:${c.gradient};stop-opacity:0.85" />
                    </radialGradient>
                </defs>
                <ellipse cx="15" cy="22" rx="10" ry="18" fill="url(#ptl${id})"/>
            </svg>
        `;
    }

    function createMaiFall() {
        if (document.getElementById("mai-fall-container")) {
            return;
        }

        const container = document.createElement("div");
        container.id = "mai-fall-container";
        document.body.appendChild(container);

        const colorTypes = ["yellow", "pink", "white", "gold", "yellow", "yellow"];
        const flowerCount = 25;
        const petalCount = 20;

        // Tạo hoa mai
        function createFlower() {
            const flower = document.createElement("div");
            const colorType = colorTypes[Math.floor(Math.random() * colorTypes.length)];
            const size = 25 + Math.random() * 30;
            const sparkle = Math.random() > 0.6 ? "sparkle" : "";

            flower.className = `mai-flower-fall ${sparkle}`;
            flower.innerHTML = createMaiSVG(size, colorType);
            flower.style.left = Math.random() * 100 + "%";
            flower.style.animationDuration = `${10 + Math.random() * 10}s, ${3 + Math.random() * 3}s`;
            flower.style.animationDelay = `${Math.random() * 8}s, ${Math.random() * 3}s`;

            container.appendChild(flower);

            const duration = parseFloat(flower.style.animationDuration) * 1000;
            const delay = parseFloat(flower.style.animationDelay) * 1000;
            setTimeout(() => {
                if (flower.parentNode) {
                    flower.remove();
                    createFlower();
                }
            }, duration + delay);
        }

        // Tạo cánh hoa
        function createPetal() {
            const petal = document.createElement("div");
            const colorType = colorTypes[Math.floor(Math.random() * colorTypes.length)];
            const size = 10 + Math.random() * 12;

            petal.className = "mai-petal-fall";
            petal.innerHTML = createPetalSVG(size, colorType);
            petal.style.left = Math.random() * 100 + "%";
            petal.style.animationDuration = `${7 + Math.random() * 7}s, ${2 + Math.random() * 2}s`;
            petal.style.animationDelay = `${Math.random() * 10}s, ${Math.random() * 2}s`;

            container.appendChild(petal);

            const duration = parseFloat(petal.style.animationDuration) * 1000;
            const delay = parseFloat(petal.style.animationDelay) * 1000;
            setTimeout(() => {
                if (petal.parentNode) {
                    petal.remove();
                    createPetal();
                }
            }, duration + delay);
        }

        // Khởi tạo
        for (let i = 0; i < flowerCount; i++) {
            setTimeout(() => createFlower(), i * 300);
        }
        for (let i = 0; i < petalCount; i++) {
            setTimeout(() => createPetal(), i * 400);
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", createMaiFall);
    } else {
        createMaiFall();
    }
})();
