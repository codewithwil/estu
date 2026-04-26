const availableIcons = [
    'fa-link', 'fa-tag', 'fa-bookmark', 'fa-star', 'fa-heart',
    'fa-globe', 'fa-cloud', 'fa-server', 'fa-database', 'fa-code',
    'fa-file-alt', 'fa-file-code', 'fa-image', 'fa-video', 'fa-music',
    'fa-shopping-cart', 'fa-store', 'fa-briefcase', 'fa-building', 'fa-landmark',
    'fa-university', 'fa-hospital', 'fa-plane', 'fa-ship', 'fa-car',
    'fa-bus', 'fa-bicycle', 'fa-map-marker-alt', 'fa-compass', 'fa-map',
    'fa-calendar', 'fa-clock', 'fa-bell', 'fa-envelope', 'fa-paper-plane',
    'fa-phone', 'fa-comments', 'fa-users', 'fa-user', 'fa-user-tie',
    'fa-graduation-cap', 'fa-book', 'fa-pencil-alt', 'fa-paint-brush', 'fa-palette',
    'fa-camera', 'fa-film', 'fa-gamepad', 'fa-trophy', 'fa-medal',
    'fa-award', 'fa-certificate', 'fa-flag', 'fa-shield-alt', 'fa-lock',
    'fa-key', 'fa-cog', 'fa-tools', 'fa-wrench', 'fa-hammer',
    'fa-chart-line', 'fa-chart-bar', 'fa-chart-pie', 'fa-percent', 'fa-dollar-sign',
    'fa-credit-card', 'fa-wallet', 'fa-piggy-bank', 'fa-hand-holding-usd', 'fa-donate',
    'fa-shopping-bag', 'fa-gift', 'fa-gem', 'fa-crown', 'fa-chess',
    'fa-puzzle-piece', 'fa-lightbulb', 'fa-brain', 'fa-atom', 'fa-flask',
    'fa-microscope', 'fa-dna', 'fa-rocket', 'fa-satellite', 'fa-robot',
    'fa-laptop', 'fa-desktop', 'fa-tablet-alt', 'fa-mobile-alt', 'fa-wifi',
    'fa-broadcast-tower', 'fa-signal', 'fa-rss', 'fa-plug', 'fa-battery-full',
    'fa-sun', 'fa-moon', 'fa-cloud-sun', 'fa-cloud-rain', 'fa-snowflake',
    'fa-fire', 'fa-water', 'fa-leaf', 'fa-tree', 'fa-seedling',
    'fa-paw', 'fa-fish', 'fa-dove', 'fa-horse', 'fa-dragon',
    'fa-bug', 'fa-spider', 'fa-otter', 'fa-hippo', 'fa-cat',
    'fa-dog', 'fa-kiwi-bird', 'fa-feather', 'fa-crow', 'fa-frog',
    'fa-coffee', 'fa-mug-hot', 'fa-glass-martini', 'fa-wine-glass', 'fa-beer',
    'fa-cocktail', 'fa-utensils', 'fa-pizza-slice', 'fa-hamburger', 'fa-ice-cream',
    'fa-cookie', 'fa-birthday-cake', 'fa-candy-cane', 'fa-apple-alt', 'fa-lemon',
    'fa-pepper-hot', 'fa-carrot', 'fa-fish', 'fa-bacon', 'fa-egg',
    'fa-cheese', 'fa-bread-slice', 'fa-running', 'fa-walking', 'fa-swimmer',
    'fa-biking', 'fa-skiing', 'fa-snowboarding', 'fa-skating', 'fa-futbol',
    'fa-basketball-ball', 'fa-baseball-ball', 'fa-volleyball-ball', 'fa-football-ball', 'fa-golf-ball',
    'fa-table-tennis', 'fa-dumbbell', 'fa-heartbeat', 'fa-first-aid', 'fa-stethoscope',
    'fa-pills', 'fa-syringe', 'fa-thermometer', 'fa-band-aid', 'fa-wheelchair',
    'fa-handshake', 'fa-thumbs-up', 'fa-thumbs-down', 'fa-smile', 'fa-frown',
    'fa-meh', 'fa-laugh', 'fa-angry', 'fa-surprise', 'fa-sad-tear',
    'fa-grin-stars', 'fa-kiss-wink-heart', 'fa-dizzy', 'fa-flushed', 'fa-grimace'
];

let selectedIcon    = 'fa-tag';
let selectedColor   = '#6366f1';
let currentView     = document.cookie.includes('linkManagerView=list') ? 'list' : 'grid';
let selectedLinkId  = null;
let deleteLinkId    = null;

function openIconPicker() {
    const dropdown = document.getElementById('iconPickerDropdown');
    const trigger = document.querySelector('.icon-picker-trigger');
    
    dropdown.classList.toggle('show');
    trigger.classList.toggle('active');
    
    if (dropdown.classList.contains('show')) {
        renderIconPicker();
        setTimeout(() => document.getElementById('iconSearch').focus(), 100);
    }
}
function renderIconPicker(filter = '') {
    const grid = document.getElementById('iconPickerGrid');
    const icons = filter 
        ? availableIcons.filter(i => i.includes(filter.toLowerCase()))
        : availableIcons;
    
    grid.innerHTML = icons.map(icon => `
        <div class="icon-picker-item ${icon === selectedIcon ? 'selected' : ''}" 
             onclick="selectIcon('${icon}')" 
             title="${icon}">
            <i class="fas ${icon}"></i>
        </div>
    `).join('');
}

function filterIcons(query) {
    renderIconPicker(query);
}

function selectIcon(icon) {
    selectedIcon = icon;
    document.getElementById('categoryIcon').value = icon;
    document.getElementById('categoryIconPreview').innerHTML = `<i class="fas ${icon}"></i>`;
    document.getElementById('categoryIconText').textContent = icon;
    
    document.querySelectorAll('.icon-picker-item').forEach(item => {
        item.classList.toggle('selected', item.title === icon);
    });
    
    setTimeout(() => {
        document.getElementById('iconPickerDropdown').classList.remove('show');
        document.querySelector('.icon-picker-trigger').classList.remove('active');
    }, 150);
}
function selectColor(btn, color) {
    selectedColor = color;
    document.getElementById('categoryColor').value = color;
    
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
}

function updateCustomColor(color) {
    selectedColor = color;
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
}

document.addEventListener('click', (e) => {
    const picker = document.querySelector('.icon-picker-dropdown');
    const trigger = document.querySelector('.icon-picker-trigger');
    if (picker && trigger && !picker.contains(e.target) && !trigger.contains(e.target)) {
        picker.classList.remove('show');
        trigger.classList.remove('active');
    }
});

function setViewMode(mode) {
    currentView = mode;
    document.cookie = `linkManagerView=${mode}; path=/; max-age=31536000`;
    location.reload();
}

// ========================================
// DROPDOWN TOGGLE (sama persis FM)
// ========================================

function toggleDropdown(menuId) {
    const menu = document.getElementById(menuId);
    const allMenus = document.querySelectorAll('.dropdown-menu');
    
    allMenus.forEach(m => {
        if (m.id !== menuId) m.classList.remove('show');
    });
    
    menu.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
    }
});

// ========================================
// SEARCH
// ========================================

document.getElementById('searchInput')?.addEventListener('input', debounce(function() {
    const url = new URL(window.location);
    if (this.value.trim()) url.searchParams.set('search', this.value.trim());
    else url.searchParams.delete('search');
    window.location = url;
}, 500));

function debounce(fn, ms) {
    let t;
    return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), ms);
    };
}

// ========================================
// CONTEXT MENU
// ========================================

const contextMenu = document.getElementById('contextMenu');
let contextLinkId = null;

document.querySelectorAll('.link-item').forEach(item => {
    item.addEventListener('contextmenu', e => {
        e.preventDefault();
        contextLinkId = item.dataset.id;
        contextMenu.classList.add('show');
        contextMenu.style.left = `${Math.min(e.clientX, window.innerWidth - 220)}px`;
        contextMenu.style.top = `${Math.min(e.clientY, window.innerHeight - 200)}px`;
    });
});

document.addEventListener('click', () => {
    contextMenu.classList.remove('show');
});

function contextAction(action) {
    if (!contextLinkId) return;
    switch(action) {
        case 'open': openLink(contextLinkId); break;
        case 'copy': copyLinkById(contextLinkId); break;
        case 'edit': editLink(contextLinkId); break;
        case 'delete': confirmDelete(contextLinkId); break;
    }
}

// ========================================
// LINK ACTIONS
// ========================================

function openLink(id) {
    const item = document.querySelector(`.link-item[data-id="${id}"]`);
    const url = item?.querySelector('a[href^="http"]')?.href;
    if (url) {
        trackClick(id);
        window.open(url, '_blank');
    }
}

function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link disalin ke clipboard!', 'success');
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        showToast('Link disalin ke clipboard!', 'success');
    });
}

function copyLinkById(id) {
    const item = document.querySelector(`.link-item[data-id="${id}"]`);
    const url = item?.querySelector('a[href^="http"]')?.href;
    if (url) copyLink(url);
}

function trackClick(id) {
    fetch(`${PROCESS_URL}?action=click&id=${id}`, { method: 'GET' })
        .catch(() => {});
}

// ========================================
// MODAL FUNCTIONS (show/hide sama FM)
// ========================================

function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}

function openAddLinkModal() {
    document.getElementById('linkModalTitle').innerHTML = '<i class="fas fa-plus-circle" style="margin-right: 8px; color: var(--blue);"></i>Tambah Link Baru';
    document.getElementById('linkId').value = '';
    document.getElementById('linkTitle').value = '';
    document.getElementById('linkUrl').value = '';
    document.getElementById('linkCategory').value = '';
    document.getElementById('linkDescription').value = '';
    openModal('linkModal');
}

function openCategoryModal() {
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryIcon').value = 'fa-tag';
    document.getElementById('categoryColor').value = '#6366f1';
    openModal('categoryModal');
}

function editLink(id) {
    fetch(`${PROCESS_URL}?action=get&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) throw new Error(res.data?.error || 'Gagal memuat data');
            const data = res.data;
            
            document.getElementById('linkModalTitle').innerHTML = '<i class="fas fa-edit" style="margin-right: 8px; color: var(--amber);"></i>Edit Link';
            document.getElementById('linkId').value = id;
            document.getElementById('linkTitle').value = data.title || '';
            document.getElementById('linkUrl').value = data.url || '';
            document.getElementById('linkCategory').value = data.category_id || '';
            document.getElementById('linkDescription').value = data.description || '';
            openModal('linkModal');
        })
        .catch(err => {
            showToast(err.message, 'error');
        });
}

// ========================================
// SAVE LINK (via process)
// ========================================

function saveLink() {
    const id = document.getElementById('linkId').value;
    const data = {
        title: document.getElementById('linkTitle').value.trim(),
        url: document.getElementById('linkUrl').value.trim(),
        category_id: document.getElementById('linkCategory').value,
        description: document.getElementById('linkDescription').value.trim()
    };

    if (!data.title || !data.url) {
        showToast('Judul dan URL wajib diisi!', 'error');
        return;
    }

    if (!isValidUrl(data.url)) {
        showToast('URL tidak valid!', 'error');
        return;
    }

    const action = id ? 'update' : 'create';
    if (id) data.id = id;

    fetch(`${PROCESS_URL}?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast(id ? 'Link berhasil diupdate!' : 'Link berhasil ditambahkan!', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res.data?.error || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menyimpan link', 'error'));
}

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// ========================================
// SAVE CATEGORY (via process)
// ========================================

function saveCategory() {
    const data = {
        name: document.getElementById('categoryName').value.trim(),
        icon: selectedIcon,
        color: selectedColor
    };

    if (!data.name) {
        showToast('Nama kategori wajib diisi!', 'error');
        return;
    }

    fetch(`${PROCESS_URL}?action=createCategory`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast('Kategori berhasil dibuat!', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res.data?.error || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menyimpan kategori', 'error'));
}

// ========================================
// DELETE
// ========================================

function confirmDelete(id) {
    deleteLinkId = id;
    openModal('confirmModal');
}

function executeDelete() {
    if (!deleteLinkId) return;
    
    fetch(`${PROCESS_URL}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: deleteLinkId })
    })
    .then(r => r.json())
    .then(res => {
        closeModal('confirmModal');
        if (res.success) {
            showToast('Link berhasil dihapus!', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res.data?.error || 'Gagal menghapus link', 'error');
        }
    })
    .catch(() => {
        closeModal('confirmModal');
        showToast('Gagal menghapus link', 'error');
    });
}

// ========================================
// TOAST (sama persis FM)
// ========================================

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast';
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        info: 'info-circle'
    };
    
    toast.innerHTML = `
        <div class="toast-icon ${type}">
            <i class="fas fa-${icons[type] || 'info-circle'}"></i>
        </div>
        <div class="toast-content">
            <h4>${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Error!' : 'Info'}</h4>
            <p>${message}</p>
        </div>
    `;
    
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'none';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%) scale(0.9)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ========================================
// EVENT LISTENERS
// ========================================

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeModal(overlay.id);
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(m => closeModal(m.id));
    }
});