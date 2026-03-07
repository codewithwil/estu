const AnimationObserver = {
    observer: null,
    options: {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    },

    init() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.handleIntersect(entry.target);
                } else {
                    entry.target.classList.remove('visible');
                }
            });
        }, this.options);

        document.querySelectorAll('.fade-up, .text-reveal').forEach(el => {
            this.observer.observe(el);
        });
    },

    handleIntersect(element) {
        element.classList.add('visible');

        element.querySelectorAll('.counter').forEach(counter => {
            if (counter.classList.contains('counted')) return;
            
            counter.classList.add('counted');
            const target = parseInt(counter.dataset.target);
            this.animateCounter(counter, target);
        });
    },

    animateCounter(element, target) {
        let current = 0;
        const increment = target / 60;
        
        const update = () => {
            current += increment;
            if (current >= target) {
                element.textContent = target + (target < 100 ? '' : '+');
            } else {
                element.textContent = Math.floor(current);
                requestAnimationFrame(update);
            }
        };
        
        requestAnimationFrame(update);
    }
};

AnimationObserver.init();