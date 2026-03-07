const App = {
    init() {
        this.initStarfield();
        this.initScrollEffects();
        this.initSmoothScroll();
        this.renderClients();
        this.initSecurity();
    },

    initSecurity() {
        document.addEventListener("contextmenu", e => e.preventDefault());
        document.addEventListener("selectstart", e => e.preventDefault());
        document.addEventListener("copy", e => e.preventDefault());
        document.addEventListener("dragstart", e => {
            if (e.target.tagName === "IMG") {
                e.preventDefault();
            }
        });
        document.addEventListener("keydown", function (e) {

            if (
                e.key === "F12" ||
                (e.ctrlKey && e.shiftKey && e.key === "I") ||
                (e.ctrlKey && e.shiftKey && e.key === "J") ||
                (e.ctrlKey && e.shiftKey && e.key === "C") ||
                (e.ctrlKey && e.key === "U") ||
                (e.ctrlKey && e.key === "S")
            ) {
                e.preventDefault();
            }

        });

            setInterval(() => {
                const threshold = 160;

                if (
                    window.outerWidth - window.innerWidth > threshold ||
                    window.outerHeight - window.innerHeight > threshold
                ) {
                    document.body.style.filter = "blur(10px)";
                } else {
                    document.body.style.filter = "";
                }
            }, 1000);
    },

    initStarfield() {
        const container = document.getElementById('starfield');
        if (!container) return;

        const createStar = (type, count, sizeRange, colors = ['']) => {
            for (let i = 0; i < count; i++) {
                const star = document.createElement('div');
                const colorClass = colors[Math.floor(Math.random() * colors.length)];
                star.className = `${type} ${colorClass}`;
                
                const size = Math.random() * (sizeRange[1] - sizeRange[0]) + sizeRange[0];
                star.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${Math.random() * 100}%;
                    top: ${Math.random() * 100}%;
                    --duration: ${Math.random() * 3 + 3}s;
                    animation-delay: ${Math.random() * 5}s;
                `;
                
                container.appendChild(star);
            }
        };

        createStar('star-small', 150, [1, 3]);
        createStar('star-medium', 80, [2, 5]);
        createStar('star-large', 25, [4, 8], ['', 'star-blue', 'star-yellow']);

        setInterval(() => {
            const shootingStar = document.createElement('div');
            shootingStar.className = 'shooting-star';
            shootingStar.style.cssText = `
                left: ${Math.random() * 50 + 50}%;
                top: ${Math.random() * 30}%;
                --duration: ${Math.random() * 1 + 1.5}s;
            `;
            container.appendChild(shootingStar);
            setTimeout(() => shootingStar.remove(), 3000);
        }, 4000);
    },

    initScrollEffects() {
        const navbar = document.getElementById('navbar');
        const scrollTop = document.getElementById('scrollTop');

        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY > 100;
            navbar?.classList.toggle('scrolled', scrolled);
            scrollTop?.classList.toggle('visible', window.scrollY > window.innerHeight * 0.5);
        }, { passive: true });
    },

    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    },

    renderClients() {
        const clients = [
            { name: 'Agung Automall', img: 'agung.png' },
            { name: 'Angker', img: 'angker.png' },
            { name: 'Astra International', img: 'astra.png' },
            { name: 'Auto 2000', img: 'auto.png' },
            { name: 'Bali Tourism', img: 'bali.png' },
            { name: 'Bali Beach Run', img: 'beachbali.png' },
            { name: 'Fitness First', img: 'fitness.png' },
            { name: 'Hardy\'s', img: 'hardy.png' },
            { name: 'ISS', img: 'iss.svg' },
            { name: 'Jasindo Insurance', img: 'jasindo.png' },
            { name: 'Indonesia MDG Awards', img: 'mdg.png' },
            { name: 'OJK', img: 'ojk.png' },
            { name: 'Pertamina', img: 'pertamina.png' },
            { name: 'Pemuda Pancasila', img: 'pp.png' },
            { name: 'Pucuk Harum', img: 'pucuk.png' },
            { name: 'Jasa Raharja', img: 'raharja.png' },
            { name: 'Suzuki', img: 'suzuki.png' },
            { name: 'Telkomsel', img: 'telkomsel.png' },
            { name: 'Tough Mudder', img: 'tough.png' },
            { name: 'Tri', img: 'tri.png' },
            { name: 'Yamaha', img: 'yamaha.png' }
        ];

        const grid = document.getElementById('clientsGrid');
        if (!grid) return;

        grid.innerHTML = clients.map(client => `
            <div class="client-logo-item" title="${client.name}">
                <img src="./assets/images/client/${client.img}" alt="${client.name}" loading="lazy">
            </div>
        `).join('');
    }
};

const mobileMenu = {
    toggle() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('active');
        document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
    }
};

const scrollManager = {
    toTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => App.init());
} else {
    App.init();
}
