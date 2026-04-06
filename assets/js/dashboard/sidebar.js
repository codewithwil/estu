// Toggle dropdown menu (Konten)
function toggleDropdown(element) {
    element.classList.toggle("active");
    element.nextElementSibling.classList.toggle("show");
}

// Toggle user dropdown (Profil)
function toggleUserDropdown(element) {
    element.classList.toggle("active");
    document.getElementById("userDropdown").classList.toggle("show");
}

// Close dropdowns when clicking outside
document.addEventListener("click", function (e) {
    // Close nav dropdowns
    document.querySelectorAll(".nav-dropdown").forEach((dropdown) => {
        if (!dropdown.contains(e.target)) {
            dropdown.querySelector(".dropdown-toggle")?.classList.remove("active");
            dropdown.querySelector(".dropdown-menu")?.classList.remove("show");
        }
    });
    
    // Close user dropdown
    const userCard = document.querySelector(".user-card");
    const userDropdown = document.getElementById("userDropdown");
    if (userCard && userDropdown && !userCard.contains(e.target)) {
        userCard.classList.remove("active");
        userDropdown.classList.remove("show");
    }
});

// Active state for nav items
document.querySelectorAll(".nav-item").forEach((item) => {
    item.addEventListener("click", function () {
        document.querySelectorAll(".nav-item, .nav-subitem").forEach((i) => i.classList.remove("active"));
        this.classList.add("active");
    });
});

// Active state for sub items
document.querySelectorAll(".nav-subitem").forEach((item) => {
    item.addEventListener("click", function () {
        document.querySelectorAll(".nav-item, .nav-subitem").forEach((i) => i.classList.remove("active"));
        this.classList.add("active");
    });
});

// Action buttons (delete confirmation)
document.querySelectorAll(".action-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
        e.stopPropagation();
        if (this.querySelector("i")?.classList.contains("fa-trash") && confirm("Hapus konten ini?")) {
            this.closest("tr")?.remove();
        }
    });
});