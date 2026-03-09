const portfolioData = [
    {
        id: 1,
        title: 'Mass Wedding Event',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/wide/1.png',
        alt: 'Mass Wedding Event Wedding by ESTU Event Organizer Bali'
    },
    {
        id: 2,
        title: 'Fazzio Youth Bali Festival',
        category: 'Competition',
        type: 'competititon',
        image: 'assets/images/portofolio/wide/2.png',
        alt: 'Competition Fazzio Youth Bali Festival Event Bali Creative Summit by EO Bali'
    },
    {
        id: 3,
        title: 'Classy Sociaty',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/long/1.jpeg',
        alt: 'Classy Sociaty Event Organizer Bali'
    },
    {
        id: 4,
        title: 'Mass Wedding Event',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/long/2.png',
        alt: 'Bali Art Festival Cultural Event'
    },
    {
        id: 5,
        title: 'Employee Gathering',
        category: 'Gathering',
        type: 'gathering',
        image: 'assets/images/portofolio/wide/3.png',
        alt: 'Nusantara Luxury Product Launch EO Bali'
    },
    {
        id: 6,
        title: 'Touring',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/long/3.jpeg',
        alt: 'Touring Yamaha Bali'
    },
    {
        id: 7,
        title: 'Mass Wedding Event',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/long/4.png',
        alt: 'Exhibition Mass Wedding Event Bali'
    },
    {
        id: 8,
        title: 'Heavy-Duty Frame',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/wide/4.png',
        alt: 'Exhibition Heavy-Duty Frame EO Bali'
    },
    {
        id: 9,
        title: 'Heavy-Duty Frame',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/wide/5.png',
        alt: 'Heavy-Duty Frame Bali'
    },
    {
        id: 10,
        title: 'Heavy-Duty Frame',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/long/5.png',
        alt: 'Heavy-Duty Frame Event Organizer Bali'
    },
    {
        id: 11,
        title: 'Heavy-Duty Frame',
        category: 'Exhibition',
        type: 'exhibition',
        image: 'assets/images/portofolio/long/6.png',
        alt: 'Heavy-Duty Frame EO Bali'
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