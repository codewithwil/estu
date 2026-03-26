document.querySelectorAll(".nav-item").forEach((item) => {
  item.addEventListener("click", function (e) {
    if (!this.classList.contains("active")) {
      document
        .querySelectorAll(".nav-item")
        .forEach((i) => i.classList.remove("active"));
      this.classList.add("active");
    }
  });
});

function toggleDropdown(element) {
  element.classList.toggle("active");

  const menu = element.nextElementSibling;
  menu.classList.toggle("show");
}

document.addEventListener("click", function (e) {
  const dropdowns = document.querySelectorAll(".nav-dropdown");
  dropdowns.forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");
    const menu = dropdown.querySelector(".dropdown-menu");

    if (!dropdown.contains(e.target)) {
      toggle.classList.remove("active");
      menu.classList.remove("show");
    }
  });
});

document.querySelectorAll(".nav-item").forEach((item) => {
  item.addEventListener("click", function (e) {
    if (!this.classList.contains("active")) {
      document
        .querySelectorAll(".nav-item")
        .forEach((i) => i.classList.remove("active"));
      document
        .querySelectorAll(".nav-subitem")
        .forEach((i) => i.classList.remove("active"));
      this.classList.add("active");
    }
  });
});
document.querySelectorAll(".nav-subitem").forEach((item) => {
  item.addEventListener("click", function (e) {
    document
      .querySelectorAll(".nav-item")
      .forEach((i) => i.classList.remove("active"));
    document
      .querySelectorAll(".nav-subitem")
      .forEach((i) => i.classList.remove("active"));
    this.classList.add("active");
  });
});

document.querySelectorAll(".action-btn").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.stopPropagation();
    const icon = this.querySelector("i");
    if (icon.classList.contains("fa-trash")) {
      if (confirm("Hapus konten ini?")) {
        this.closest("tr").remove();
      }
    }
  });
});