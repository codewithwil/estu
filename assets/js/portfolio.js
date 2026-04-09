const portfolio = {
    data: [],

    async init() {
        await this.fetchData();
        this.render();
    },

    async fetchData() {
        try {
            const res = await fetch(BASE_URL + "/process/portofolio.php?action=get");
            const json = await res.json();

            this.data = json; 
        } catch (err) {
            console.error("Failed fetch portfolio:", err);
        }
    },

    getImageUrl(item) {
        if (item.filepath && item.filepath.startsWith("/")) {
            return item.filepath; 
        }
        return `${ASSET_URL}/images/placeholder.jpg`;
    },

    render() {
        const grid = document.getElementById('portfolioGrid');
        if (!grid) return;

        grid.innerHTML = this.data.map((item, index) => `
            <div class="portfolio-item fade-up ${index > 0 ? `delay-${Math.min(index, 5)}` : ''}" 
                 data-category="${item.category}"
                 onclick="portfolio.openDetail(${item.id})">
                 
                <img src="${this.getImageUrl(item)}" alt="${item.title}" loading="lazy">
                
                <div class="portfolio-overlay">
                    <span class="portfolio-category">${item.category}</span>
                    <h3 class="portfolio-title">${item.title}</h3>
                </div>
            </div>
        `).join('');

        // trigger animation
        setTimeout(() => {
            document.querySelectorAll('#portfolioGrid .fade-up').forEach(el => {
                el.classList.add('visible');
            });
        }, 100);
    },

    openDetail(id) {
        const item = this.data.find(p => p.id == id);
        if (!item) return;

        console.log("DETAIL:", item);

        // nanti bisa bikin modal di sini
    },

    showAll() {
        window.location.href = 'portfolio.php';
    }
};

portfolio.init();