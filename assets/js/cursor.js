const Cursor = {
    dot: null,
    outline: null,
    mouseX: 0,
    mouseY: 0,
    outlineX: 0,
    outlineY: 0,
    isActive: false,

    init() {
        if (window.matchMedia('(pointer: coarse)').matches) return;
        
        this.dot = document.querySelector('.cursor-dot');
        this.outline = document.querySelector('.cursor-outline');
        
        if (!this.dot || !this.outline) return;

        this.isActive = true;
        this.bindEvents();
        this.animate();
    },

    bindEvents() {
        window.addEventListener('mousemove', (e) => {
            this.mouseX = e.clientX;
            this.mouseY = e.clientY;
            this.dot.style.left = `${this.mouseX}px`;
            this.dot.style.top = `${this.mouseY}px`;
        }, { passive: true });

        const selectors = 'a, button, .service-card, .portfolio-item, input, textarea, .client-logo-item, .show-all-btn';
        document.querySelectorAll(selectors).forEach(el => {
            el.addEventListener('mouseenter', () => this.outline.classList.add('hover'));
            el.addEventListener('mouseleave', () => this.outline.classList.remove('hover'));
        });
    },

    animate() {
        if (!this.isActive) return;
        
        this.outlineX += (this.mouseX - this.outlineX) * 0.15;
        this.outlineY += (this.mouseY - this.outlineY) * 0.15;
        
        this.outline.style.left = `${this.outlineX}px`;
        this.outline.style.top = `${this.outlineY}px`;
        
        requestAnimationFrame(() => this.animate());
    }
};

Cursor.init();