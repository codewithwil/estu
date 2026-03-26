let servicesData = {
    titleLine1: 'INTEGRATED EVENT',
    titleLine2: 'SOLUTIONS',
    sectionDesc: 'End-to-end event organizer Bali services ensuring every detail of your event is perfect.',
    services: [
        {
            id: 1,
            icon: 'fa-lightbulb',
            title: 'CONCEPT DEVELOPMENT',
            description: 'Designing unique concepts that reflect your brand identity with authentic Balinese cultural nuances.',
            isWide: true
        },
        {
            id: 2,
            icon: 'fa-tasks',
            title: 'EVENT MANAGEMENT',
            description: 'Precision logistics management, vendor coordination, and timeline control for seamless execution.',
            isWide: true
        },
        {
            id: 3,
            icon: 'fa-bullhorn',
            title: 'DIGITAL MARKETING',
            description: 'Strategic digital communication to maximize reach and engagement for your Bali events.',
            isWide: true
        },
        {
            id: 4,
            icon: 'fa-store',
            title: 'EXHIBITION DESIGN',
            description: 'Eye-catching and interactive booth designs that stand out at any trade show or exhibition.',
            isWide: false
        },
        {
            id: 5,
            icon: 'fa-video',
            title: 'MEDIA & LIVE STREAMING',
            description: 'Broadcast-quality video production and professional live streaming services for hybrid events.',
            isWide: false
        }
    ]
};

let nextId = 6;
const MAX_SERVICES = 6; 
const MAX_WIDE_CARDS = 3; 

const availableIcons = [
    'fa-lightbulb', 'fa-tasks', 'fa-bullhorn', 'fa-store', 'fa-video',
    'fa-calendar', 'fa-users', 'fa-camera', 'fa-microphone', 'fa-palette',
    'fa-gem', 'fa-star', 'fa-heart', 'fa-rocket', 'fa-cog',
    'fa-chart-line', 'fa-handshake', 'fa-gift', 'fa-music', 'fa-map-marker-alt',
    'fa-glass-cheers', 'fa-birthday-cake', 'fa-utensils', 'fa-wine-glass',
    'fa-cocktail', 'fa-beer', 'fa-coffee', 'fa-mug-hot', 'fa-lemon',
    'fa-briefcase', 'fa-building', 'fa-city', 'fa-landmark', 'fa-university',
    'fa-comments', 'fa-comment', 'fa-envelope', 'fa-paper-plane',
    'fa-laptop', 'fa-desktop', 'fa-mobile', 'fa-cloud',
    'fa-paint-brush', 'fa-pencil', 'fa-magic', 'fa-palette',
    'fa-music', 'fa-guitar', 'fa-drum', 'fa-ticket-alt',
    'fa-plane', 'fa-hotel', 'fa-map', 'fa-compass',
    'fa-shield-alt', 'fa-lock', 'fa-key',
    'fa-heartbeat', 'fa-hospital', 'fa-stethoscope',
    'fa-leaf', 'fa-tree', 'fa-sun', 'fa-moon',
    'fa-tools', 'fa-hammer', 'fa-hard-hat',
    'fa-graduation-cap', 'fa-book', 'fa-microscope',
    'fa-utensils', 'fa-pizza-slice', 'fa-coffee',
    'fa-running', 'fa-futbol', 'fa-dumbbell',
    'fa-handshake', 'fa-thumbs-up',
    'fa-star', 'fa-check-circle', 'fa-arrow-up'
];

document.addEventListener('DOMContentLoaded', () => {
    loadServicesData();
    setupEventListeners();
    initRichEditors();
    renderServicesForm();
    updatePreview();
    updateAddButtonState();
});

function initRichEditors() {
    document.querySelectorAll('.rte-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const command = btn.dataset.command;
            const editor = btn.closest('.rich-editor-container').querySelector('.rich-editor');
            
            editor.focus();
            document.execCommand(command, false, null);
            updateToolbarState(btn.closest('.rich-editor-toolbar'));
            updatePreview();
        });
    });

    document.addEventListener('selectionchange', () => {
        document.querySelectorAll('.rich-editor-toolbar').forEach(toolbar => {
            updateToolbarState(toolbar);
        });
    });

    document.querySelectorAll('.rich-editor').forEach(editor => {
        editor.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                const map = { b: 'bold', i: 'italic', u: 'underline' };
                if (map[e.key.toLowerCase()]) {
                    e.preventDefault();
                    document.execCommand(map[e.key.toLowerCase()], false, null);
                    updatePreview();
                }
            }
        });

        editor.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
            updatePreview();
        });
    });
}

function updateToolbarState(toolbar) {
    const buttons = toolbar.querySelectorAll('.rte-btn');
    buttons.forEach(btn => {
        const command = btn.dataset.command;
        if (command !== 'removeFormat') {
            btn.classList.toggle('active', document.queryCommandState(command));
        }
    });
}

function setupEventListeners() {
    const btn = document.getElementById('notificationBtn');
    const dropdown = document.getElementById('notificationDropdown');

    if (btn && dropdown) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }
}

function updateAddButtonState() {
    const addBtn = document.querySelector('.btn-add-service');
    if (!addBtn) return;
    
    const currentCount = servicesData.services.length;
    const isMaxed = currentCount >= MAX_SERVICES;
    
    addBtn.disabled = isMaxed;
    addBtn.style.opacity = isMaxed ? '0.5' : '1';
    addBtn.style.cursor = isMaxed ? 'not-allowed' : 'pointer';
    
    if (isMaxed) {
        addBtn.title = 'Maksimal 6 layanan';
    } else {
        addBtn.title = 'Tambah Layanan';
    }
}

function countWideCards() {
    return servicesData.services.filter(s => s.isWide).length;
}

function renderServicesForm() {
    const container = document.getElementById('servicesFormList');
    container.innerHTML = '';

    const wideCount = countWideCards();

    servicesData.services.forEach((service, index) => {
        const item = document.createElement('div');
        item.className = 'service-form-item';
        
        item.innerHTML = `
            <div class="service-form-header">
                <span class="service-number">${index + 1}</span>
                <h4>${service.title || 'Layanan Baru'}</h4>
                <div class="service-actions">
                    <button type="button" class="btn-icon move-up" onclick="moveService(${service.id}, -1)" ${index === 0 ? 'disabled' : ''} title="Move Up">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button type="button" class="btn-icon move-down" onclick="moveService(${service.id}, 1)" ${index === servicesData.services.length - 1 ? 'disabled' : ''} title="Move Down">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                    <button type="button" class="btn-icon delete" onclick="deleteService(${service.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="service-form-grid">
                <div class="service-icon-picker">
                    <div class="service-icon-preview">
                        <i class="fas ${service.icon}"></i>
                    </div>
                    <select class="form-control icon-select" onchange="updateServiceIcon(${service.id}, this.value)">
                        ${availableIcons.map(icon => `
                            <option value="${icon}" ${service.icon === icon ? 'selected' : ''}>
                                ${icon.replace('fa-', '')}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="form-group">
                    <label>Judul Layanan</label>
                    <input type="text" class="form-control" value="${service.title}" 
                        oninput="updateServiceTitle(${service.id}, this.value)" placeholder="CONCEPT DEVELOPMENT">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" class="form-control" value="${service.description}" 
                        oninput="updateServiceDesc(${service.id}, this.value)" placeholder="Deskripsi layanan...">
                </div>
                <div class="wide-checkbox">
                    <input type="checkbox" id="wide-${service.id}" ${service.isWide ? 'checked' : ''} 
                        onchange="updateServiceWide(${service.id}, this.checked)">
                    <label for="wide-${service.id}">Wide Card</label>
                </div>
            </div>
        `;
        container.appendChild(item);
    });
    
    updateAddButtonState();
}

function addNewService() {
    if (servicesData.services.length >= MAX_SERVICES) {
        showToast('error', 'Error', `Maksimal ${MAX_SERVICES} layanan`);
        return;
    }
    
    const newService = {
        id: nextId++,
        icon: 'fa-star',
        title: '',
        description: '',
        isWide: false
    };
    
    servicesData.services.push(newService);
    
    renderServicesForm();
    updatePreview();
}

function deleteService(id) {
    if (servicesData.services.length <= 1) {
        showToast('error', 'Error', 'Minimal harus ada 1 layanan');
        return;
    }
    
    servicesData.services = servicesData.services.filter(s => s.id !== id);
    renderServicesForm();
    updatePreview();
}

function moveService(id, direction) {
    const index = servicesData.services.findIndex(s => s.id === id);
    if (index === -1) return;
    
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= servicesData.services.length) return;
    
    [servicesData.services[index], servicesData.services[newIndex]] = 
    [servicesData.services[newIndex], servicesData.services[index]];
    
    renderServicesForm();
    updatePreview();
}

function updateServiceIcon(id, value) {
    const service = servicesData.services.find(s => s.id === id);
    if (service) {
        service.icon = value;
        renderServicesForm();
        updatePreview();
    }
}

function updateServiceTitle(id, value) {
    const service = servicesData.services.find(s => s.id === id);
    if (service) {
        service.title = value;
        updatePreview();
    }
}

function updateServiceDesc(id, value) {
    const service = servicesData.services.find(s => s.id === id);
    if (service) {
        service.description = value;
        updatePreview();
    }
}

function updateServiceWide(id, checked) {
    const service = servicesData.services.find(s => s.id === id);
    if (!service) return;
    
    const currentWideCount = countWideCards();
    
    if (checked && currentWideCount >= MAX_WIDE_CARDS && !service.isWide) {
        showToast('error', 'Error', `Maksimal ${MAX_WIDE_CARDS} wide card`);
        return;
    }
    
    service.isWide = checked;
    
    renderServicesForm();
    updatePreview();
}

function updatePreview() {
    const title1 = document.getElementById('titleLine1').value || 'INTEGRATED EVENT';
    const title2 = document.getElementById('titleLine2').value || 'SOLUTIONS';
    document.getElementById('previewSectionTitle').innerHTML = `${title1}<br>${title2}`;
    
    document.getElementById('previewSectionDesc').innerHTML = 
        document.getElementById('sectionDesc').innerHTML;

    const grid = document.getElementById('servicesPreviewGrid');
    
    grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
    
    grid.innerHTML = servicesData.services.map(service => `
        <article class="preview-service-card ${service.isWide ? 'wide' : ''}">
            <div class="preview-service-icon">
                <i class="fas ${service.icon}"></i>
            </div>
            <h3 class="preview-service-title">${service.title || 'Untitled'}</h3>
            <p class="preview-service-desc">${service.description || ''}</p>
        </article>
    `).join('');
}

async function saveServicesData() {
    const data = {
        titleLine1: document.getElementById('titleLine1').value,
        titleLine2: document.getElementById('titleLine2').value,
        sectionDesc: document.getElementById('sectionDesc').innerHTML,
        services: servicesData.services
    };

    try {
        await new Promise(resolve => setTimeout(resolve, 500));
        showToast('success', 'Berhasil', 'Data Services berhasil disimpan');
        console.log('Saved data:', data);
    } catch (error) {
        showToast('error', 'Error', 'Gagal menyimpan data');
    }
}

async function loadServicesData() {
    try {
        document.getElementById('sectionDesc').innerHTML = servicesData.sectionDesc;
        console.log('Services data loaded');
    } catch (error) {
        console.error('Load error:', error);
    }
}

function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    
    icon.className = 'toast-icon ' + type;
    icon.innerHTML = type === 'success' 
        ? '<i class="fas fa-check"></i>' 
        : '<i class="fas fa-times"></i>';
    
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}