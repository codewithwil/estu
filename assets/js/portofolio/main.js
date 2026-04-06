let portfolios = [];
let portfolioToDelete = null;
let uploadedFile = null;

const portfolioPreviewGrid = document.getElementById("portfolioPreviewGrid");
const portfolioPreviewBadge = document.getElementById("portfolioPreviewBadge");

const API = {
  async getPortfolios() {
    const res = await fetch(BASE_URL + "/process/portofolio.php?action=get");
    const data = await res.json();
    return data;
  },
  
  async createPortfolio(data) {
    return fetch(BASE_URL + "/process/portofolio.php?action=create", {
      method: "POST",
      body: data,
    });
  },

  async updatePortfolio(data) {
    return fetch(BASE_URL + "/process/portofolio.php?action=update", {
      method: "POST",
      body: data,
    });
  },

  async deletePortfolio(id) {
    return fetch(BASE_URL + "/process/portofolio.php?action=delete", {
      method: "POST",
      body: new URLSearchParams({ id }),
    });
  },
};

// File upload preview dengan validasi landscape
document.getElementById("imageInput")?.addEventListener("change", (e) => {
  const file = e.target.files[0];
  
  if (!file) return;
  
  // Validasi ukuran file (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    showToast("error", "Error", "Ukuran gambar maksimal 5MB");
    e.target.value = '';
    return;
  }
  
  // Validasi tipe file
  if (!file.type.startsWith('image/')) {
    showToast("error", "Error", "File harus berupa gambar");
    e.target.value = '';
    return;
  }
  
  // Validasi orientasi landscape
  const img = new Image();
  img.onload = function() {
    URL.revokeObjectURL(this.src);
    
    if (this.height > this.width) {
      showToast("error", "Error", "Gambar harus landscape (lebar > tinggi). Gunakan gambar horizontal, bukan portrait/potret.");
      e.target.value = '';
      resetUploadPreview();
      return;
    }
    
    // Gambar valid - tampilkan preview
    uploadedFile = file;
    const preview = document.getElementById("previewImage");
    const uploadContent = document.getElementById("uploadContent");
    preview.src = URL.createObjectURL(file);
    preview.style.display = "block";
    if (uploadContent) uploadContent.style.display = "none";
    document.getElementById("uploadArea").classList.add("has-file");
  };
  
  img.onerror = function() {
    showToast("error", "Error", "Gagal membaca gambar");
    e.target.value = '';
  };
  
  img.src = URL.createObjectURL(file);
});

function resetUploadPreview() {
  const preview = document.getElementById("previewImage");
  const uploadContent = document.getElementById("uploadContent");
  const uploadArea = document.getElementById("uploadArea");
  
  if (preview) {
    preview.src = "";
    preview.style.display = "none";
  }
  if (uploadContent) uploadContent.style.display = "flex";
  if (uploadArea) uploadArea.classList.remove("has-file");
  
  uploadedFile = null;
}

async function loadPortfolios() {
  try {
    const res = await API.getPortfolios();
    portfolios = res.data || res;

    renderPortfolios();
    updateStats();
  } catch (error) {
    console.error("Error loading portfolios:", error);
    showToast("error", "Error", "Gagal memuat data portfolio");
  }
}

function updateStats() {
  const totalEl = document.getElementById("totalPortfolio");
  const monthEl = document.getElementById("thisMonthPortfolio");
  
  if (totalEl) totalEl.innerText = portfolios.length;
  
  // Count portfolios added this month
  const now = new Date();
  const thisMonth = portfolios.filter(p => {
    if (!p.created_at) return false;
    const date = new Date(p.created_at);
    return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
  }).length;
  
  if (monthEl) monthEl.innerText = thisMonth;
}

function getGridSizeClass(size) {
  const sizeMap = {
    'small': 'medium',
    'medium': 'medium',
    'large': 'wide',
    'wide': 'wide',
    'tall': 'medium'
  };
  return sizeMap[size] || 'medium';
}

function getImagePath(portfolio) {
  if (portfolio.filepath) {
    return portfolio.filepath;
  }
  if (portfolio.filename) {
    return `${ASSET_URL}/assets/images/portfolio/${portfolio.filename}`;
  }
  return `${ASSET_URL}/assets/images/placeholder.jpg`;
}

function renderPortfolioPreview() {
  if (!portfolioPreviewGrid) return;
  
  if (portfolioPreviewBadge) {
    portfolioPreviewBadge.textContent = `${portfolios.length} proyek`;
  }
  
  if (portfolios.length === 0) {
    portfolioPreviewGrid.innerHTML = `
      <div class="portfolio-preview-empty">
        <i class="fas fa-briefcase"></i>
        <p>Belum ada proyek. Tambahkan proyek untuk melihat preview.</p>
      </div>
    `;
    return;
  }
  
  // Show max 8 items in preview with masonry layout
  const previewItems = portfolios.slice(0, 8);
  
  portfolioPreviewGrid.innerHTML = previewItems.map((portfolio) => {
    const gridClass = getGridSizeClass(portfolio.grid_size);
    const imagePath = getImagePath(portfolio);
    return `
      <div class="portfolio-preview-item ${gridClass}" title="${portfolio.title}">
        <img src="${imagePath}" alt="${portfolio.title}" loading="lazy">
        <div class="portfolio-preview-overlay">
          <span class="preview-category">${portfolio.category}</span>
        </div>
      </div>
    `;
  }).join('');
}

function renderPortfolios() {
  const portfolioGrid = document.getElementById("portfolioGrid");
  
  renderPortfolioPreview();

  if (!portfolioGrid) return;

  if (!portfolios.length) {
    portfolioGrid.innerHTML = `
      <div class="empty-state">
        <i class="fas fa-briefcase"></i>
        <h3>Belum ada proyek</h3>
        <p>Tambahkan proyek portfolio untuk ditampilkan di website</p>
      </div>
    `;
    return;
  }

  portfolioGrid.innerHTML = portfolios
    .map((p) => {
      const imagePath = getImagePath(p);
      const hasMetadata = p.client || p.location || p.year || p.services || p.guests;
      
      return `
        <div class="portfolio-card" data-id="${p.id}">
          <div class="portfolio-actions">
            <button class="portfolio-action-btn edit-btn" data-id="${p.id}" title="Edit">
              <i class="fas fa-pen"></i>
            </button>
            <button class="portfolio-action-btn delete-btn delete" data-id="${p.id}" title="Hapus">
              <i class="fas fa-trash"></i>
            </button>
          </div>
          <div class="portfolio-image">
            <img src="${imagePath}" alt="${p.title}" loading="lazy">
            <span class="portfolio-category-badge">${p.category}</span>
          </div>
          <div class="portfolio-info">
            <h4 class="portfolio-title">${p.title}</h4>
            ${p.description ? `<p class="portfolio-description">${truncateText(p.description, 100)}</p>` : ''}
            ${hasMetadata ? `
              <div class="portfolio-meta">
                ${p.client ? `<span class="meta-item"><i class="fas fa-user"></i> ${p.client}</span>` : ''}
                ${p.location ? `<span class="meta-item"><i class="fas fa-map-marker-alt"></i> ${p.location}</span>` : ''}
                ${p.year ? `<span class="meta-item"><i class="fas fa-calendar"></i> ${p.year}</span>` : ''}
                ${p.guests ? `<span class="meta-item"><i class="fas fa-users"></i> ${p.guests}</span>` : ''}
              </div>
            ` : ''}
            ${p.services ? `
              <div class="portfolio-services">
                <i class="fas fa-tools"></i> ${p.services}
              </div>
            ` : ''}
            ${p.tags ? `
              <div class="portfolio-tags">
                ${p.tags.split(',').map(tag => `<span class="tag">${tag.trim()}</span>`).join('')}
              </div>
            ` : ''}
            <div class="portfolio-date">
              <i class="fas fa-clock"></i> ${formatDate(p.created_at)}
            </div>
          </div>
        </div>
      `;
    })
    .join("");
  
  // PASANG EVENT LISTENER SETELAH RENDER
  attachCardEventListeners();
}

// FUNGSI BARU: Pasang event listener ke tombol edit/delete
function attachCardEventListeners() {
  // Edit buttons
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const id = btn.getAttribute('data-id');
      openEditModal(id);
    });
  });
  
  // Delete buttons
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const id = btn.getAttribute('data-id');
      openDeleteModal(id);
    });
  });
}

function truncateText(text, maxLength) {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substr(0, maxLength) + '...';
}

function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
}

// Add Portfolio
document.getElementById("portfolioForm")?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const title = document.getElementById("projectTitle").value;
  const category = document.getElementById("projectCategory").value;
  const client = document.getElementById("projectClient").value;
  const location = document.getElementById("projectLocation").value;
  const year = document.getElementById("projectYear").value;
  const description = document.getElementById("projectDescription").value;
  const services = document.getElementById("projectServices").value;
  const tags = document.getElementById("projectTags").value;
  const guests = document.getElementById("projectGuests").value;

  if (!uploadedFile) {
    return showToast("error", "Error", "Gambar proyek wajib diupload");
  }

  const formData = new FormData();
  formData.append("title", title);
  formData.append("category", category);
  formData.append("client", client);
  formData.append("location", location);
  formData.append("year", year);
  formData.append("description", description);
  formData.append("services", services);
  formData.append("tags", tags);
  formData.append("guests", guests);
  formData.append("image", uploadedFile);

  try {
    const response = await API.createPortfolio(formData);
    const result = await response.json();
    
    if (result.success) {
      loadPortfolios();
      closeAddModal();
      resetAddForm();
      showToast("success", "Berhasil", "Proyek berhasil ditambahkan");
    } else {
      showToast("error", "Error", result.error || "Gagal menambahkan proyek");
    }
  } catch (error) {
    showToast("error", "Error", "Terjadi kesalahan koneksi");
  }
});

// Edit Portfolio
document.getElementById("editForm")?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new URLSearchParams({
    id: document.getElementById("editId").value,
    file_id: document.getElementById("editFileId").value,
    title: document.getElementById("editTitle").value,
    category: document.getElementById("editCategory").value,
    client: document.getElementById("editClient").value,
    location: document.getElementById("editLocation").value,
    year: document.getElementById("editYear").value,
    description: document.getElementById("editDescription").value,
    services: document.getElementById("editServices").value,
    tags: document.getElementById("editTags").value,
    guests: document.getElementById("editGuests").value,
  });

  try {
    const response = await API.updatePortfolio(formData);
    const result = await response.json();
    
    if (result.success) {
      loadPortfolios();
      closeEditModal();
      showToast("success", "Berhasil", "Proyek berhasil diupdate");
    } else {
      showToast("error", "Error", result.error || "Gagal mengupdate proyek");
    }
  } catch (error) {
    showToast("error", "Error", "Terjadi kesalahan koneksi");
  }
});

async function confirmDelete() {
  if (!portfolioToDelete) return;

  try {
    const response = await API.deletePortfolio(portfolioToDelete);
    const result = await response.json();
    
    if (result.success) {
      loadPortfolios();
      closeDeleteModal();
      showToast("success", "Terhapus", "Proyek berhasil dihapus");
    } else {
      showToast("error", "Error", result.error || "Gagal menghapus proyek");
    }
  } catch (error) {
    showToast("error", "Error", "Terjadi kesalahan koneksi");
  }
}

// Modal Functions
function openAddModal() {
  document.getElementById("addModal").classList.add("show");
  document.body.style.overflow = 'hidden';
}

function closeAddModal() {
  document.getElementById("addModal").classList.remove("show");
  document.body.style.overflow = '';
  resetAddForm();
}

function resetAddForm() {
  const form = document.getElementById("portfolioForm");
  if (form) form.reset();
  
  const preview = document.getElementById("previewImage");
  const uploadContent = document.getElementById("uploadContent");
  const uploadArea = document.getElementById("uploadArea");
  
  if (preview) {
    preview.src = "";
    preview.style.display = "none";
  }
  if (uploadContent) uploadContent.style.display = "flex";
  if (uploadArea) uploadArea.classList.remove("has-file");
  
  uploadedFile = null;
}

function openEditModal(id) {
  const p = portfolios.find((x) => x.id == id);
  if (!p) return;

  document.getElementById("editId").value = p.id;
  document.getElementById("editFileId").value = p.file_id || '';
  document.getElementById("editTitle").value = p.title || '';
  document.getElementById("editCategory").value = p.category || 'Branding';
  document.getElementById("editClient").value = p.client || '';
  document.getElementById("editLocation").value = p.location || '';
  document.getElementById("editYear").value = p.year || '';
  document.getElementById("editDescription").value = p.description || '';
  document.getElementById("editServices").value = p.services || '';
  document.getElementById("editTags").value = p.tags || '';
  document.getElementById("editGuests").value = p.guests || '';
  
  const editPreviewImg = document.getElementById("editPreviewImg");
  if (editPreviewImg) {
    editPreviewImg.src = getImagePath(p);
  }

  document.getElementById("editModal").classList.add("show");
  document.body.style.overflow = 'hidden';
}

function closeEditModal() {
  document.getElementById("editModal").classList.remove("show");
  document.body.style.overflow = '';
}

function openDeleteModal(id) {
  const p = portfolios.find((x) => x.id == id);
  if (!p) return;

  portfolioToDelete = id;
  document.getElementById("deleteProjectName").innerText = p.title;
  document.getElementById("deleteModal").classList.add("show");
  document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
  portfolioToDelete = null;
  document.getElementById("deleteModal").classList.remove("show");
  document.body.style.overflow = '';
}

// Toast Notification
function showToast(type, title, msg) {
  const toast = document.getElementById("toast");
  const iconBox = document.getElementById("toastIcon");
  const titleEl = document.getElementById("toastTitle");
  const msgEl = document.getElementById("toastMessage");

  if (!toast || !iconBox || !titleEl || !msgEl) return;

  titleEl.innerText = title;
  msgEl.innerText = msg;

  iconBox.className = "toast-icon";

  if (type === "success") {
    iconBox.classList.add("success");
    iconBox.innerHTML = '<i class="fas fa-check"></i>';
  } else {
    iconBox.classList.add("error");
    iconBox.innerHTML = '<i class="fas fa-times"></i>';
  }

  toast.classList.add("show");

  setTimeout(() => {
    toast.classList.remove("show");
  }, 3000);
}

// Search functionality
document.getElementById("searchInput")?.addEventListener("input", (e) => {
  const searchTerm = e.target.value.toLowerCase();
  const filtered = portfolios.filter(p => 
    (p.title && p.title.toLowerCase().includes(searchTerm)) ||
    (p.category && p.category.toLowerCase().includes(searchTerm)) ||
    (p.client && p.client.toLowerCase().includes(searchTerm)) ||
    (p.location && p.location.toLowerCase().includes(searchTerm)) ||
    (p.tags && p.tags.toLowerCase().includes(searchTerm))
  );
  
  const portfolioGrid = document.getElementById("portfolioGrid");
  if (portfolioGrid) {
    if (!filtered.length) {
      portfolioGrid.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-search"></i>
          <h3>Tidak ada hasil</h3>
          <p>Tidak ditemukan portfolio dengan kata kunci "${e.target.value}"</p>
        </div>
      `;
    } else {
      portfolioGrid.innerHTML = filtered
        .map((p) => {
          const imagePath = getImagePath(p);
          const hasMetadata = p.client || p.location || p.year || p.services;
          
          return `
            <div class="portfolio-card" data-id="${p.id}">
              <div class="portfolio-actions">
                <button class="portfolio-action-btn edit-btn" data-id="${p.id}" title="Edit">
                  <i class="fas fa-pen"></i>
                </button>
                <button class="portfolio-action-btn delete-btn delete" data-id="${p.id}" title="Hapus">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
              <div class="portfolio-image">
                <img src="${imagePath}" alt="${p.title}" loading="lazy">
                <span class="portfolio-category-badge">${p.category}</span>
              </div>
              <div class="portfolio-info">
                <h4 class="portfolio-title">${p.title}</h4>
                ${p.description ? `<p class="portfolio-description">${truncateText(p.description, 100)}</p>` : ''}
                ${hasMetadata ? `
                  <div class="portfolio-meta">
                    ${p.client ? `<span class="meta-item"><i class="fas fa-user"></i> ${p.client}</span>` : ''}
                    ${p.location ? `<span class="meta-item"><i class="fas fa-map-marker-alt"></i> ${p.location}</span>` : ''}
                    ${p.year ? `<span class="meta-item"><i class="fas fa-calendar"></i> ${p.year}</span>` : ''}
                  </div>
                ` : ''}
                ${p.services ? `
                  <div class="portfolio-services">
                    <i class="fas fa-tools"></i> ${p.services}
                  </div>
                ` : ''}
                ${p.tags ? `
                  <div class="portfolio-tags">
                    ${p.tags.split(',').map(tag => `<span class="tag">${tag.trim()}</span>`).join('')}
                  </div>
                ` : ''}
                <div class="portfolio-date">
                  <i class="fas fa-clock"></i> ${formatDate(p.created_at)}
                </div>
              </div>
            </div>
          `;
        })
        .join("");
      
      // PASANG EVENT LISTENER JUGA UNTUK HASIL SEARCH
      attachCardEventListeners();
    }
  }
});

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      if (overlay.id === 'addModal') closeAddModal();
      if (overlay.id === 'editModal') closeEditModal();
      if (overlay.id === 'deleteModal') closeDeleteModal();
    }
  });
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeAddModal();
    closeEditModal();
    closeDeleteModal();
  }
});

// EVENT DELEGATION - Solusi utama untuk tombol edit/delete
document.addEventListener('click', function(e) {
  const editBtn = e.target.closest('.edit-btn');
  if (editBtn) {
    e.stopPropagation();
    openEditModal(editBtn.getAttribute('data-id'));
    return;
  }
  
  const deleteBtn = e.target.closest('.delete-btn');
  if (deleteBtn) {
    e.stopPropagation();
    openDeleteModal(deleteBtn.getAttribute('data-id'));
    return;
  }
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  loadPortfolios();
});