const App = {
    async init() {
        this.initStarfield();
        this.initScrollEffects();
        this.initSmoothScroll();
        await this.renderClients();
        this.renderClients();
        this.initSecurity();
        this.renderServices();
    },

renderServices() {
    // 1. Ambil data dari window atau default
    const data = window.initialServicesData || {
        services: [],
        titleLine1: 'INTEGRATED EVENT',
        titleLine2: 'SOLUTIONS',
        sectionDesc: 'End-to-end event organizer Bali services...'
    };

    // Gunakan copy agar tidak merusak data asli saat reordering
    let services = [...(data.services || [])];
    const grid = document.querySelector('.services-grid');
    
    if (!grid || services.length === 0) return;

    // 2. Update Header Text
    const titleEl = grid.closest('section')?.querySelector('.section-title');
    if (titleEl) {
        titleEl.innerHTML = `${data.titleLine1 || 'INTEGRATED EVENT'}<br>${data.titleLine2 || 'SOLUTIONS'}`;
    }
    
    const descEl = grid.closest('section')?.querySelector('.section-desc');
    if (descEl) {
        descEl.innerHTML = data.sectionDesc || '';
    }

    // 3. Logika Perhitungan
    const totalServices = services.length;
    // Helper untuk cek wide (karena di DB/JS bisa boolean atau string '1')
    const isWide = (val) => val === true || val === 1 || val === '1';
    const wideCount = services.filter(s => isWide(s.isWide)).length;

    // 4. Logika Reordering (Sama dengan Preview/PHP)
    // Agar layout simetris secara visual di grid
    if (totalServices === 5 && wideCount === 1) {
        const wideIndex = services.findIndex(s => isWide(s.isWide));
        if (wideIndex !== -1 && wideIndex !== 3) {
            const [wideCard] = services.splice(wideIndex, 1);
            services.splice(3, 0, wideCard); // Pindahkan ke index 3 (posisi ke-4)
        }
    } 
    else if (totalServices === 5 && wideCount === 2) {
        // Taruh wide cards di urutan terakhir agar mengisi baris bawah
        services.sort((a, b) => isWide(a.isWide) - isWide(b.isWide));
    }

    // 5. Penentuan Grid Class (Sama persis dengan Preview)
    let gridClass = '';
    if (totalServices === 5) {
        if (wideCount === 2) gridClass = 'layout-5-2wide';
        else if (wideCount === 1) gridClass = 'layout-5-1wide';
        else if (wideCount === 3) gridClass = 'layout-5-3wide';
    } else if (totalServices === 4) {
        if (wideCount === 2) gridClass = 'layout-4-2wide';
        else if (wideCount === 1) gridClass = 'layout-4-1wide';
    } else if (totalServices === 6) {
        if (wideCount === 2) gridClass = 'layout-6-2wide';
        else if (wideCount === 3) gridClass = 'layout-6-3wide';
    }

    // 6. Apply Attributes & Classes
    grid.className = `services-grid ${gridClass}`;
    grid.setAttribute('data-count', totalServices);
    grid.setAttribute('data-wide', wideCount);

    // 7. Render HTML
    grid.innerHTML = services.map((service, index) => {
        const wideStatus = isWide(service.isWide);
        return `
            <article class="service-card ${wideStatus ? 'wide' : ''}" data-index="${index}">
                <div class="service-icon">
                    <i class="fas ${service.icon || 'fa-star'}"></i>
                </div>
                <h3>${service.title || 'Untitled'}</h3>
                <p>${service.description || ''}</p>
            </article>
        `;
    }).join('');
},

    // initSecurity() {
    //     document.addEventListener("contextmenu", e => e.preventDefault());
    //     document.addEventListener("selectstart", e => e.preventDefault());
    //     document.addEventListener("copy", e => e.preventDefault());
    //     document.addEventListener("dragstart", e => {
    //         if (e.target.tagName === "IMG") {
    //             e.preventDefault();
    //         }
    //     });
    //     document.addEventListener("keydown", function (e) {

    //         if (
    //             e.key === "F12" ||
    //             (e.ctrlKey && e.shiftKey && e.key === "I") ||
    //             (e.ctrlKey && e.shiftKey && e.key === "J") ||
    //             (e.ctrlKey && e.shiftKey && e.key === "C") ||
    //             (e.ctrlKey && e.key === "U") ||
    //             (e.ctrlKey && e.key === "S")
    //         ) {
    //             e.preventDefault();
    //         }

    //     });

    //         setInterval(() => {
    //             const threshold = 160;

    //             if (
    //                 window.outerWidth - window.innerWidth > threshold ||
    //                 window.outerHeight - window.innerHeight > threshold
    //             ) {
    //                 document.body.style.filter = "blur(10px)";
    //             } else {
    //                 document.body.style.filter = "";
    //             }
    //         }, 1000);
    // },

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

    async renderClients() {
            const grid = document.getElementById('clientsGrid');
            if (!grid) return;

            try {
                const response = await fetch("process/client.php?action=get");
                const clients = await response.json();

                if (!clients || clients.length === 0) {
                    grid.innerHTML = '<p>No clients found</p>';
                    return;
                }

                grid.innerHTML = clients.map(client => `
                    <div class="client-logo-item" title="${client.name}">
                        <img src="./assets/images/client/${client.logo}" alt="${client.name}" loading="lazy">
                    </div>
                `).join('');

            } catch (error) {
                console.error("Gagal mengambil data client:", error);
                grid.innerHTML = '<p>Failed to load clients.</p>';
            }
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
