// Action buttons
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
