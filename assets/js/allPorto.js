let portfolioData = [];
let currentFilter       = "all";
let currentModalIndex   = 0;
let filteredData        = [...portfolioData];
let isAnimating         = false;

const grid                  = document.getElementById("portfolioGrid");
const emptyState            = document.getElementById("emptyState");
const filterButtons         = document.querySelectorAll(".filter-btn");
const modalImageWrapper     = document.getElementById("modalImageWrapper");
const modalTextContent      = document.getElementById("modalTextContent");
const modalContentWrapper   = document.getElementById("modalContentWrapper");

  document.addEventListener("DOMContentLoaded", () => {
    initSecurity();
    fetchPortfolio();
    initCursor();
    initScrollEffects();
    initFilters();
    animateOnScroll();
  });
  
  function capitalize(str) {
    return str.replace(/\b\w/g, l => l.toUpperCase());
  }

  function normalize(str) {
    return str.toLowerCase().replace(/\s+/g, "");
  }

  async function fetchPortfolio() {
    try {
      const res = await fetch(BASE_URL + "/process/portofolio.php?action=get");
      const data = await res.json();

      portfolioData = data.map((item) => ({
        id: Number(item.id),
        title: item.title,
        category: item.category,
        type: item.category.toLowerCase().trim() === currentFilter,
        image: item.filepath,
        alt: item.title,
        description: item.description,
        date: item.year,
        location: item.location,
        guests: item.guests + " guests",
        services: item.services,
        tags: item.tags ? item.tags.split(",").map(t => t.trim()) : []
      }));
      
      generateTopFilters();
      renderPortfolio();
    } catch (err) {
      console.error("Fetch error:", err);
    }
  }
  
  function generateTopFilters() {
    const container = document.getElementById("filterContainer");
    const counts = {};

    portfolioData.forEach(item => {
      const cat = item.category.toLowerCase().trim();
      counts[cat] = (counts[cat] || 0) + 1;
    });

    // urutkan dari terbanyak
    const topCategories = Object.entries(counts)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 3); 

    // render button
    container.innerHTML = `
      <button class="filter-btn active" data-filter="all">All</button>
      ${topCategories.map(([cat]) => `
        <button class="filter-btn" data-filter="${cat}">
          ${capitalize(cat)}
        </button>
      `).join("")}
    `;

    initFilters(); 
  }

function initSecurity() {
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
}

function renderPortfolio() {
filteredData =
  currentFilter === "all"
    ? portfolioData
    : portfolioData.filter(
        (item) =>
          normalize(item.category) === normalize(currentFilter)
      );
  if (filteredData.length === 0) {
    grid.innerHTML = "";
    emptyState.classList.remove("hidden");
    return;
  }

  emptyState.classList.add("hidden");

  grid.innerHTML = filteredData
    .map(
      (item, index) => `
                <article class="portfolio-card fade-up" 
                         style="animation-delay: ${index * 0.1}s"
                         onclick="modal.open(${item.id})"
                         data-aos="fade-up">
                    <img src="${item.image}" 
                         alt="${item.alt}" 
                         loading="lazy">
                    <div class="portfolio-card-overlay">
                        <span class="portfolio-card-category">${item.category}</span>
                        <h3 class="portfolio-card-title">${item.title}</h3>
                    </div>
                </article>
            `,
    )
    .join("");

  setTimeout(() => {
    document.querySelectorAll(".fade-up").forEach((el) => {
      el.classList.add("visible");
    });
  }, 50);
}

function initFilters() {
  const buttons = document.querySelectorAll(".filter-btn");

  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      buttons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      currentFilter = normalize(btn.dataset.filter);

      grid.style.opacity = "0";
      grid.style.transform = "translateY(20px)";

      setTimeout(() => {
        renderPortfolio();
        grid.style.transition = "all 0.5s ease";
        grid.style.opacity = "1";
        grid.style.transform = "translateY(0)";
      }, 300);
    });
  });
}

const modal = {
  element: document.getElementById("portfolioModal"),

  open(id) {
    const item = portfolioData.find((p) => p.id === id);
    if (!item) return;

    currentModalIndex = filteredData.findIndex((p) => p.id === id);
    this.populate(item);

    // Reset animations
    modalImageWrapper.classList.remove("animating-out", "animating-in");
    modalImageWrapper.classList.add("visible");
    modalTextContent.classList.remove("animating-out", "animating-in");
    modalTextContent.classList.add("visible");
    modalContentWrapper.classList.remove("animating-out", "animating-in");
    modalContentWrapper.classList.add("visible");

    this.element.classList.add("active");
    document.body.style.overflow = "hidden";
    document.body.style.paddingRight = "15px";

    this.updateCounter();
  },

  populate(item) {
    // Preload image for smooth transition
    const img = new Image();
    img.src = item.image;

    document.getElementById("modalImage").src = item.image;
    document.getElementById("modalImage").alt = item.alt;
    document.getElementById("modalCategory").textContent = item.category;
    document.getElementById("modalTitle").textContent = item.title;
    document.getElementById("modalDescription").innerHTML =
      `<p>${item.description}</p>`;
    document.getElementById("modalDate").textContent = item.date;
    document.getElementById("modalLocation").textContent = item.location;
    document.getElementById("modalGuests").textContent = item.guests;
    document.getElementById("modalServices").textContent = item.services;

    const tagsContainer = document.getElementById("modalTags");
    tagsContainer.innerHTML = item.tags
      .map(
        (tag, i) =>
          `<span class="px-3 py-1 bg-white/10 rounded-full text-xs text-gray-400" style="animation-delay: ${i * 0.05}s">${tag}</span>`,
      )
      .join("");
  },

  close() {
    this.element.classList.remove("active");
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";

    // Reset animation classes
    setTimeout(() => {
      modalImageWrapper.classList.remove(
        "animating-out",
        "animating-in",
        "visible",
      );
      modalTextContent.classList.remove(
        "animating-out",
        "animating-in",
        "visible",
      );
      modalContentWrapper.classList.remove(
        "animating-out",
        "animating-in",
        "visible",
      );
    }, 400);
  },

  closeOnBackground(e) {
    if (e.target === this.element) this.close();
  },

  async navigate(direction) {
    if (isAnimating || filteredData.length === 0) return;
    isAnimating = true;

    const isNext = direction === "next";

    modalImageWrapper.classList.remove("visible");
    modalImageWrapper.classList.add(isNext ? "animating-out" : "animating-in");

    modalTextContent.classList.add("animating-out");
    modalContentWrapper.classList.remove("visible");
    modalContentWrapper.classList.add(
      isNext ? "animating-out" : "animating-in",
    );

    await this.wait(400);

    if (isNext) {
      currentModalIndex = (currentModalIndex + 1) % filteredData.length;
    } else {
      currentModalIndex =
        (currentModalIndex - 1 + filteredData.length) % filteredData.length;
    }

    this.populate(filteredData[currentModalIndex]);
    this.updateCounter();

    modalImageWrapper.classList.remove("animating-out", "animating-in");
    modalImageWrapper.classList.add(isNext ? "animating-in" : "animating-out");

    modalTextContent.classList.remove("animating-out");
    modalTextContent.classList.add("animating-in");

    modalContentWrapper.classList.remove("animating-out", "animating-in");
    modalContentWrapper.classList.add(
      isNext ? "animating-in" : "animating-out",
    );

    void modalImageWrapper.offsetWidth;

    requestAnimationFrame(() => {
      modalImageWrapper.classList.remove("animating-in", "animating-out");
      modalImageWrapper.classList.add("visible");

      modalTextContent.classList.remove("animating-in");
      modalTextContent.classList.add("visible");

      modalContentWrapper.classList.remove("animating-in", "animating-out");
      modalContentWrapper.classList.add("visible");

      isAnimating = false;
    });
  },

  next() {
    this.navigate("next");
  },

  prev() {
    this.navigate("prev");
  },

  updateCounter() {
    document.getElementById("modalCounter").textContent = currentModalIndex + 1;
    document.getElementById("modalTotal").textContent = filteredData.length;
  },

  wait(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
  },
};

document.addEventListener("keydown", (e) => {
  if (!document.getElementById("portfolioModal").classList.contains("active"))
    return;

  if (e.key === "Escape") modal.close();
  if (e.key === "ArrowRight") {
    e.preventDefault();
    modal.next();
  }
  if (e.key === "ArrowLeft") {
    e.preventDefault();
    modal.prev();
  }
});

let touchStartX = 0;
let touchEndX = 0;

document.getElementById("portfolioModal").addEventListener(
  "touchstart",
  (e) => {
    touchStartX = e.changedTouches[0].screenX;
  },
  { passive: true },
);

document.getElementById("portfolioModal").addEventListener(
  "touchend",
  (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  },
  { passive: true },
);

function handleSwipe() {
  const swipeThreshold = 50;
  const diff = touchStartX - touchEndX;

  if (Math.abs(diff) > swipeThreshold) {
    if (diff > 0) {
      modal.next();
    } else {
      modal.prev();
    }
  }
}

function initCursor() {
  if (window.matchMedia("(pointer: coarse)").matches) return;

  const dot = document.querySelector(".cursor-dot");
  const outline = document.querySelector(".cursor-outline");

  let mouseX = 0,
    mouseY = 0;
  let outlineX = 0,
    outlineY = 0;

  window.addEventListener(
    "mousemove",
    (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      dot.style.left = mouseX + "px";
      dot.style.top = mouseY + "px";
    },
    { passive: true },
  );

  function animate() {
    outlineX += (mouseX - outlineX) * 0.15;
    outlineY += (mouseY - outlineY) * 0.15;
    outline.style.left = outlineX + "px";
    outline.style.top = outlineY + "px";
    requestAnimationFrame(animate);
  }
  animate();

  const interactiveElements =
    "a, button, .portfolio-card, .filter-btn, .modal-close, .nav-btn";
  document.querySelectorAll(interactiveElements).forEach((el) => {
    el.addEventListener("mouseenter", () => outline.classList.add("hover"));
    el.addEventListener("mouseleave", () => outline.classList.remove("hover"));
  });
}

function initScrollEffects() {
  const navbar = document.getElementById("navbar");

  window.addEventListener(
    "scroll",
    () => {
      navbar.classList.toggle("scrolled", window.scrollY > 50);
    },
    { passive: true },
  );
}

function animateOnScroll() {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.1 },
  );

  document.querySelectorAll(".fade-up").forEach((el) => observer.observe(el));
}

const mobileMenu = {
  toggle() {
    const menu = document.getElementById("mobileMenu");
    menu.classList.toggle("active");
    document.body.style.overflow = menu.classList.contains("active")
      ? "hidden"
      : "";
  },
};

