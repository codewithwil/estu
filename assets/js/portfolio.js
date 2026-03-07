const portfolioData = [
    {
        id: 1,
        title: 'THE ROYAL BALINESE',
        category: 'Traditional Wedding',
        type: 'wedding',
        image: 'assets/images/portofolio/wide/1.png',
        alt: 'Royal Balinese Wedding by ESTU Event Organizer Bali'
    },
    {
        id: 2,
        title: 'BALI CREATIVE SUMMIT',
        category: 'Conference',
        type: 'corporate',
        image: 'assets/images/portofolio/wide/2.png',
        alt: 'Bali Creative Summit by EO Bali'
    },
    {
        id: 3,
        title: 'SUNSET BEACH FEST',
        category: 'Music Festival',
        type: 'festival',
        image: 'assets/images/portofolio/long/1.png',
        alt: 'Sunset Beach Festival Event Organizer Bali'
    },
    {
        id: 4,
        title: 'BALI ART FESTIVAL',
        category: 'Cultural Event',
        type: 'cultural',
        image: 'assets/images/portofolio/long/2.png',
        alt: 'Bali Art Festival Cultural Event'
    },
    {
        id: 5,
        title: 'NUSANTARA LUXURY',
        category: 'Product Launch',
        type: 'corporate',
        image: 'assets/images/portofolio/wide/3.png',
        alt: 'Nusantara Luxury Product Launch EO Bali'
    },
    {
        id: 6,
        title: 'TROPICAL DREAMS',
        category: 'Garden Wedding',
        type: 'wedding',
        image: 'assets/images/portofolio/long/3.png',
        alt: 'Tropical Garden Wedding Bali'
    },
    {
        id: 7,
        title: 'CORPORATE GALA',
        category: 'Gala Dinner',
        type: 'corporate',
        image: 'assets/images/portofolio/long/4.png',
        alt: 'Corporate Gala Dinner Event Organizer Bali'
    },
    {
        id: 8,
        title: 'OCEAN BREEZE',
        category: 'Beach Party',
        type: 'festival',
        image: 'assets/images/portofolio/wide/4.png',
        alt: 'Ocean Breeze Beach Party EO Bali'
    },
    {
        id: 9,
        title: 'LEGACY OF BALI',
        category: 'Performance',
        type: 'cultural',
        image: 'assets/images/portofolio/wide/5.png',
        alt: 'Legacy of Bali Traditional Performance'
    },
    {
        id: 10,
        title: 'CLIFFSIDE VOWS',
        category: 'Intimate Wedding',
        type: 'wedding',
        image: 'assets/images/portofolio/long/5.png',
        alt: 'Cliffside Intimate Wedding Bali'
    },
    {
        id: 11,
        title: 'EXECUTIVE RETREAT',
        category: 'Business Meeting',
        type: 'corporate',
        image: 'assets/images/portofolio/long/6.png',
        alt: 'Executive Business Retreat EO Bali'
    }
];

const portfolio = {
    init() {
        this.render();
    },

    render() {
        const grid = document.getElementById('portfolioGrid');
        if (!grid) return;

        grid.innerHTML = portfolioData.map((item, index) => `
            <div class="portfolio-item fade-up ${index > 0 ? `delay-${Math.min(index, 5)}` : ''}" 
                 data-category="${item.type}"
                 onclick="portfolio.openDetail(${item.id})">
                <img src="${item.image}" alt="${item.alt}" loading="lazy">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">${item.category}</span>
                    <h3 class="portfolio-title">${item.title}</h3>
                </div>
            </div>
        `).join('');

        setTimeout(() => {
            document.querySelectorAll('#portfolioGrid .fade-up').forEach(el => {
                el.classList.add('visible');
            });
        }, 100);
    },

    openDetail(id) {
        const item = portfolioData.find(p => p.id === id);
        if (!item) return;
        
        console.log('Opening detail for:', item.title);
    },

    showAll() {
        window.location.href = 'portfolio.html';
    }
};

portfolio.init();