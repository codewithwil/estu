if (window.innerWidth > 768) {
  const cursorDot = document.querySelector(".cursor-dot");
  const cursorOutline = document.querySelector(".cursor-outline");
  let mouseX = 0,
    mouseY = 0,
    outlineX = 0,
    outlineY = 0;

  window.addEventListener("mousemove", (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
    cursorDot.style.left = mouseX + "px";
    cursorDot.style.top = mouseY + "px";
  });

  function animateCursor() {
    outlineX += (mouseX - outlineX) * 0.15;
    outlineY += (mouseY - outlineY) * 0.15;
    cursorOutline.style.left = outlineX + "px";
    cursorOutline.style.top = outlineY + "px";
    requestAnimationFrame(animateCursor);
  }
  animateCursor();

  document
    .querySelectorAll(
      "a, button, .service-card, .portfolio-item, input, textarea, .client-logo-item, .show-all-btn",
    )
    .forEach((el) => {
      el.addEventListener("mouseenter", () =>
        cursorOutline.classList.add("hover"),
      );
      el.addEventListener("mouseleave", () =>
        cursorOutline.classList.remove("hover"),
      );
    });
}

function toggleMobileMenu() {
  const menu = document.getElementById("mobileMenu");
  const isOpen = menu.style.transform === "translateX(0%)";
  menu.style.transform = isOpen ? "translateX(100%)" : "translateX(0%)";
}

const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("visible");

      entry.target.querySelectorAll(".counter").forEach((counter) => {
        if (!counter.classList.contains("counted")) {
          counter.classList.add("counted");
          const target = parseInt(counter.dataset.target);
          let current = 0;
          const timer = setInterval(() => {
            current += target / 125;
            if (current >= target) {
              counter.textContent = target + "+";
              clearInterval(timer);
            } else {
              counter.textContent = Math.floor(current);
            }
          }, 16);
        }
      });
    } else {
      entry.target.classList.remove("visible");
    }
  });
}, observerOptions);

document
  .querySelectorAll(".fade-up, .text-reveal")
  .forEach((el) => observer.observe(el));

const navbar = document.getElementById("navbar");
const scrollTop = document.getElementById("scrollTop");

window.addEventListener("scroll", () => {
  if (window.scrollY > 100) navbar.classList.add("scrolled");
  else navbar.classList.remove("scrolled");

  if (window.scrollY > window.innerHeight * 0.5)
    scrollTop.classList.add("visible");
  else scrollTop.classList.remove("visible");
});

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function showAllPortfolio() {
  alert(
    "Menampilkan semua karya! Dalam implementasi nyata, ini akan mengarahkan ke halaman portofolio lengkap.",
  );
}

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });
  });
});
