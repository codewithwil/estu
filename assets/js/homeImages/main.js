const MAX_IMAGES = 10;

let homeImages      = [];
let deleteId        = null;
let draggedElement  = null;
let draggedIndex    = null;

// GLOBAL VARIABLES - Deklarasikan di luar supaya accessible semua function
let imagesContainer, imageCounter, addImageBtn, uploadSection, fileInput, 
    searchInput, heroSliderLive, previewBadge, notificationBtn, 
    notificationDropdown, notificationDot, notificationList, markAllReadBtn;

const animations = [
    'zoomIn', 'zoomOut', 'panLeft', 'panRight', 'zoomRotate',
    'slowZoom', 'panUp', 'panDown', 'zoomBlur', 'kenBurns'
];

let notifications = [
  {
    id: 1,
    icon: "fa-image",
    iconColor: "blue",
    title: "Gambar Diupload",
    text: "10 gambar berhasil diupload ke Home Images",
    time: "10 menit yang lalu",
    unread: true,
  },
  {
    id: 2,
    icon: "fa-arrows-alt-v",
    iconColor: "green",
    title: "Urutan Diperbarui",
    text: "Urutan gambar home berhasil diubah",
    time: "1 jam yang lalu",
    unread: false,
  },
  {
    id: 3,
    icon: "fa-trophy",
    iconColor: "amber",
    title: "Gambar Pertama",
    text: "Hero Banner sekarang di posisi pertama",
    time: "2 jam yang lalu",
    unread: false,
  },
];

// TUNGGU DOM READY
document.addEventListener('DOMContentLoaded', function() {
    initApp();
});

function initApp() {
    // ASSIGN ELEMENTS KE GLOBAL VARIABLES
    imagesContainer   = document.getElementById("imagesContainer");
    imageCounter      = document.getElementById("imageCounter");
    addImageBtn       = document.getElementById("addImageBtn");
    uploadSection     = document.getElementById("uploadSection");
    fileInput         = document.getElementById("fileInput");
    searchInput       = document.getElementById("searchInput");
    heroSliderLive    = document.getElementById("heroSliderLive");
    previewBadge      = document.getElementById("previewBadge");
    notificationBtn   = document.getElementById("notificationBtn");
    notificationDropdown = document.getElementById("notificationDropdown");
    notificationDot   = document.getElementById("notificationDot");
    notificationList  = document.getElementById("notificationList");
    markAllReadBtn    = document.getElementById("markAllRead");

    console.log('InitApp - imagesContainer:', imagesContainer); // DEBUG

    // Setup event listeners
    setupEventListeners();
    
    // Load data
    renderNotifications();
    loadImages();
}

function setupEventListeners() {
    document.getElementById("deleteModal").addEventListener("click", function (e) {
        if (e.target === this) closeDeleteModal();
    });

    document.getElementById("modalClose").addEventListener("click", closeModal);

    document.getElementById("previewModal").addEventListener("click", function (e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeModal();
    });

    if (notificationBtn) {
        notificationBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle("show");
        });
    }

    document.addEventListener("click", function (e) {
        if (notificationDropdown && notificationBtn &&
            !notificationDropdown.contains(e.target) &&
            !notificationBtn.contains(e.target)
        ) {
            notificationDropdown.classList.remove("show");
        }
    });

    if (markAllReadBtn) {
        markAllReadBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            notifications.forEach((n) => (n.unread = false));
            renderNotifications();
        });
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const cards = document.querySelectorAll(".image-card");

            cards.forEach((card) => {
                const title = card.querySelector(".image-title").textContent.toLowerCase();
                const filename = card.querySelector(".image-meta span").textContent.toLowerCase();
                const type = card.querySelector(".image-meta span:last-child").textContent.toLowerCase();

                card.style.display =
                    title.includes(query) ||
                    filename.includes(query) ||
                    type.includes(query)
                        ? ""
                        : "none";
            });
        });
    }

    if (fileInput) {
        fileInput.addEventListener("change", async function (e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            const file = files[0];

            const formData = new FormData();
            formData.append("action", "create");
            formData.append("title", file.name);
            formData.append("image", file);

            try {
                const res = await fetch("/estu/process/homeImages.php", {
                    method: "POST",
                    body: formData,
                });

                const result = await res.json();

                if (result.success) {
                    await loadImages();
                    showToast("success", "Berhasil", "Gambar ditambahkan");
                }
            } catch {
                showToast("error", "Error", "Terjadi kesalahan");
            }

            fileInput.value = "";
        });
    }
}

function renderHeroPreviewLive() {
    console.log('renderHeroPreviewLive called, heroSliderLive:', heroSliderLive);
    
    if (!heroSliderLive) {
        console.error('heroSliderLive not found!');
        return;
    }
    
    if (previewBadge) {
        previewBadge.textContent = `${homeImages.length} gambar`;
    }
    
    if (homeImages.length === 0) {
        heroSliderLive.innerHTML = `
            <div class="hero-preview-empty-inline">
                <i class="fas fa-images"></i>
                <p>Belum ada gambar. Upload gambar untuk melihat preview.</p>
            </div>
        `;
        return;
    }

    const slideDuration = 5;
    const totalDuration = homeImages.length * slideDuration;
    
    let slidesHTML = '';
    homeImages.forEach((img, index) => {
        const animation = animations[index % animations.length];
        const delay = index * slideDuration;
        
        slidesHTML += `
            <div class="hero-slide-live" 
                 style="background-image: url('${img.src}');
                        animation: ${animation} ${totalDuration}s infinite;
                        animation-delay: ${delay}s;">
            </div>
        `;
    });

    heroSliderLive.innerHTML = `
        <div class="hero-bg-live">
            ${slidesHTML}
            <div class="hero-overlay-live"></div>
        </div>
        <div class="hero-content-live">
            <p class="hero-subtitle-live">Slide 1 dari ${homeImages.length}</p>
        </div>
        <div class="hero-indicators-live">
            ${homeImages.map((_, i) => `
                <div class="hero-indicator-live ${i === 0 ? 'active' : ''}" data-index="${i}"></div>
            `).join('')}
        </div>
    `;

    startLiveIndicatorUpdate();
}

function startLiveIndicatorUpdate() {
    const indicators = document.querySelectorAll('.hero-indicator-live');
    const subtitle = document.querySelector('.hero-subtitle-live');
    if (!indicators.length) return;
    
    let currentIndex = 0;
    
    if (window.livePreviewInterval) {
        clearInterval(window.livePreviewInterval);
    }
    
    window.livePreviewInterval = setInterval(() => {
        if (!heroSliderLive) return;
        
        indicators.forEach((ind, i) => {
            ind.classList.toggle('active', i === currentIndex);
        });
        
        if (subtitle) {
            subtitle.textContent = `Slide ${currentIndex + 1} dari ${homeImages.length}`;
        }
        
        currentIndex = (currentIndex + 1) % homeImages.length;
    }, 5000);
}

async function loadImages() {
    try {
        const res = await fetch("/estu/process/homeImages.php?action=get");
        const data = await res.json();
        
        console.log('Raw data from API:', data);
        console.log('Data length:', data.length);
        
        homeImages = data.map((item) => ({
            id: item.id,
            src: window.location.origin + item.filepath,
            title: item.title || item.filename,
            filename: item.filename,
            size: formatFileSize(item.size || 0),
            dimensions: "-",
            uploadedAt: new Date(item.created_at).toLocaleDateString("id-ID"),
            type: "Slider",
        }));
        
        console.log('Mapped homeImages:', homeImages);
        console.log('homeImages length:', homeImages.length);

        renderImages();
    } catch (err) {
        console.error('Error loading images:', err);
    }
}

function renderNotifications() {
    if (!notificationList) return;
    
    const unreadCount = notifications.filter((n) => n.unread).length;
    if (notificationDot) {
        notificationDot.style.display = unreadCount > 0 ? "block" : "none";
    }

    notificationList.innerHTML = notifications
        .map(
            (notif) => `
                <div class="notification-item ${notif.unread ? "unread" : ""}" data-id="${notif.id}">
                    <div class="notification-icon ${notif.iconColor}">
                        <i class="fas ${notif.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notif.title}</div>
                        <div class="notification-text">${notif.text}</div>
                        <div class="notification-time">${notif.time}</div>
                    </div>
                </div>
            `,
        )
        .join("");

    document.querySelectorAll(".notification-item").forEach((item) => {
        item.addEventListener("click", function () {
            const id = parseInt(this.dataset.id);
            const notif = notifications.find((n) => n.id === id);
            if (notif) {
                notif.unread = false;
                renderNotifications();
            }
        });
    });
}

function renderImages() {
    console.log('renderImages called, imagesContainer:', imagesContainer);
    console.log('homeImages.length:', homeImages.length);
    
    if (!imagesContainer) {
        console.error('imagesContainer is null! Trying to get from DOM...');
        // Fallback: coba ambil langsung dari DOM
        imagesContainer = document.getElementById("imagesContainer");
        if (!imagesContainer) {
            console.error('imagesContainer still not found!');
            return;
        }
    }
    
    if (homeImages.length === 0) {
        imagesContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>Belum ada gambar</h3>
                <p>Tambahkan gambar untuk ditampilkan di halaman utama</p>
            </div>
        `;
        updateCounter();
        renderHeroPreviewLive();
        return;
    }

    imagesContainer.innerHTML = homeImages
        .map(
            (img, index) => `
                <div class="image-card" draggable="true" data-id="${img.id}" data-index="${index}">
                    <div class="drag-handle" title="Drag untuk mengurutkan">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <div class="order-number ${getOrderNumberClass(index)}">
                        ${index + 1}
                    </div>
                    <div class="image-preview" onclick="previewImage('${img.id}')">
                        <img src="${img.src}" alt="${img.title}" loading="lazy">
                        <div class="image-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                    <div class="image-info">
                        <div class="image-title">${img.title}</div>
                        <div class="image-meta">
                            <span><i class="fas fa-file-image"></i> ${img.filename}</span>
                            <span><i class="fas fa-weight-hanging"></i> ${img.size}</span>
                            <span><i class="fas fa-ruler-combined"></i> ${img.dimensions}</span>
                            <span><i class="fas fa-calendar"></i> ${img.uploadedAt}</span>
                            <span><i class="fas fa-tag"></i> ${img.type}</span>
                        </div>
                    </div>
                    <div class="position-badge ${getPositionBadgeClass(index)}">
                        ${getPositionText(index)}
                    </div>
                    <div class="reorder-controls">
                        <button class="reorder-btn move-up" ${index === 0 ? "disabled" : ""} onclick="moveImage(${index}, -1)">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                        <button class="reorder-btn move-down" ${index === homeImages.length - 1 ? "disabled" : ""} onclick="moveImage(${index}, 1)">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="image-actions">
                        <button class="action-btn" onclick="previewImage('${img.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn delete" onclick="deleteImage('${img.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `,
        )
        .join("");

    document.querySelectorAll(".image-card").forEach((card) => {
        card.addEventListener("dragstart", handleDragStart);
        card.addEventListener("dragover", handleDragOver);
        card.addEventListener("drop", handleDrop);
        card.addEventListener("dragend", handleDragEnd);
        card.addEventListener("dragenter", handleDragEnter);
        card.addEventListener("dragleave", handleDragLeave);
    });

    updateCounter();
    renderHeroPreviewLive();
}

function previewImage(id) {
    const img = homeImages.find((i) => i.id === id);
    if (!img) return;

    const modal = document.getElementById("previewModal");
    const previewImg = document.getElementById("previewImage");
    const previewTitle = document.getElementById("previewTitle");
    const previewMeta = document.getElementById("previewMeta");

    previewImg.src = img.src;
    previewTitle.textContent = img.title;
    previewMeta.textContent = `${img.dimensions} • ${img.size} • ${img.type}`;

    modal.classList.add("show");
}

function deleteImage(id) {
    const img = homeImages.find((i) => i.id === id);
    if (!img) return;

    deleteId = id;
    document.getElementById("deleteText").innerText = `Yakin mau hapus "${img.title}"?`;
    document.getElementById("deleteModal").classList.add("show");
}

async function confirmDelete() {
    if (!deleteId) return;

    const formData = new FormData();
    formData.append("action", "delete");
    formData.append("id", deleteId);

    try {
        const res = await fetch("/estu/process/homeImages.php", {
            method: "POST",
            body: formData,
        });

        const result = await res.json();

        if (result.success) {
            await loadImages();
            showToast("success", "Terhapus", "Gambar dihapus");
        }
    } catch (err) {
        showToast("error", "Error", "Terjadi kesalahan");
    }

    closeDeleteModal();
}

function closeDeleteModal() {
    document.getElementById("deleteModal").classList.remove("show");
    deleteId = null;
}

function handleDragStart(e) {
    draggedElement = this;
    draggedIndex = parseInt(this.dataset.index);
    this.classList.add("dragging");
    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.setData("text/html", this.innerHTML);
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDragEnter(e) {
    e.preventDefault();
    this.classList.add("drag-over");
}

function handleDragLeave() {
    this.classList.remove("drag-over");
}

function handleDrop(e) {
    e.preventDefault();
    this.classList.remove("drag-over");

    const dropIndex = parseInt(this.dataset.index);

    if (draggedIndex !== dropIndex) {
        const item = homeImages.splice(draggedIndex, 1)[0];
        homeImages.splice(dropIndex, 0, item);

        renderImages();
        saveOrderToServer();

        addNotification(
            "fa-arrows-alt-v",
            "green",
            "Urutan Diperbarui",
            `"${item.title}" dipindahkan ke posisi ${dropIndex + 1}`,
            "Baru saja",
        );
    }
}

function handleDragEnd() {
    this.classList.remove("dragging");
    document.querySelectorAll(".image-card").forEach((card) => {
        card.classList.remove("drag-over");
    });
}

function moveImage(index, direction) {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= homeImages.length) return;

    const temp = homeImages[index];
    homeImages[index] = homeImages[newIndex];
    homeImages[newIndex] = temp;

    renderImages();
    saveOrderToServer();

    const notifText = direction < 0 ? "naik" : "turun";

    addNotification(
        "fa-arrows-alt-v",
        "green",
        "Posisi Diubah",
        `"${temp.title}" ${notifText} ke posisi ${newIndex + 1}`,
        "Baru saja",
    );
}

async function saveOrderToServer() {
    const orders = {};

    homeImages.forEach((img, index) => {
        orders[img.id] = index + 1;
    });

    try {
        const res = await fetch("/estu/process/homeImages.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "reorder",
                orders: orders,
            }),
        });

        const result = await res.json();

        if (result.success) {
            showToast("success", "Berhasil", "Gambar diurutkan");
        }
    } catch {
        showToast("error", "Error", "Terjadi kesalahan");
    }
}

function updateCounter() {
    if (!imageCounter) return;
    
    const count = homeImages.length;
    imageCounter.textContent = `${count} / ${MAX_IMAGES}`;

    if (count >= MAX_IMAGES) {
        imageCounter.classList.add("full");
        addImageBtn.disabled = true;
        addImageBtn.innerHTML = '<i class="fas fa-lock"></i> Maksimal (10)';
        uploadSection.style.display = "none";
    } else {
        imageCounter.classList.remove("full");
        addImageBtn.disabled = false;
        addImageBtn.innerHTML = '<i class="fas fa-plus"></i> Tambah Gambar';
        uploadSection.style.display = "block";
    }
}

function getOrderNumberClass(i) {
    return ["first", "second", "third"][i] || "";
}

function getPositionBadgeClass(i) {
    return ["first", "second", "third"][i] || "regular";
}

function getPositionText(i) {
    if (i === 0) return "Pertama (Hero)";
    if (i === 1) return "Kedua";
    if (i === 2) return "Ketiga";
    return `Posisi ${i + 1}`;
}

function formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
}

function closeModal() {
    document.getElementById("previewModal").classList.remove("show");
}

function addNotification(icon, iconColor, title, text, time) {
    const newNotif = {
        id: Date.now(), icon, iconColor, title, text, time, unread: true,
    };

    notifications.unshift(newNotif);
    renderNotifications();

    if (notificationDot) {
        notificationDot.style.animation = "none";
        setTimeout(() => {
            notificationDot.style.animation = "pulse 2s infinite";
        }, 10);
    }
}

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