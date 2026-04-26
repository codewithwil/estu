// Global variables
let selectedItems = new Set();
let currentContext = null;
let confirmCallback = null;
let promptCallback = null;
let moveCallback = null;
let selectedTargetFolder = null;

document.addEventListener('DOMContentLoaded', () => {
    initEventListeners();
    initDragDrop();
    initSearch();
    initKeyboard();
    initNotifications();
    initItemListeners();
});

// ==================== EVENT LISTENERS ====================

function initEventListeners() {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
        document.getElementById('contextMenu')?.classList.remove('show');
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('show');
            }
        });
    });

    const promptInput = document.getElementById('promptInput');
    if (promptInput) {
        promptInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') executePrompt();
        });
    }

    const folderNameInput = document.getElementById('folderName');
    if (folderNameInput) {
        folderNameInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') saveFolder();
        });
    }
}

function initItemListeners() {
    document.querySelectorAll('.folder-item').forEach(item => {
        const folderId = item.dataset.folderId;

        item.addEventListener('click', (e) => {
            e.stopPropagation();
            selectItem(item, e);
        });

        item.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            window.location.href = `?folder=${folderId}`;
        });

        item.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();
            showContextMenu(e, 'folder', folderId);
        });
    });

    document.querySelectorAll('.file-item').forEach(item => {
        const fileId = item.dataset.fileId;
        const fileType = item.dataset.filetype;
        const filename = item.dataset.filename;
        const extension = item.dataset.extension;

        item.addEventListener('click', (e) => {
            e.stopPropagation();
            selectItem(item, e);
        });

        item.addEventListener('dblclick', (e) => {
            e.stopPropagation();
            downloadFile(fileId);
        });

        item.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();
            showContextMenu(e, 'file', fileId);
        });
    });
}

// ==================== FILE ACTIONS (Storage Only) ====================

function openFile(id, type, filename, extension) {
    // Storage only - no preview, just download
    downloadFile(id);
}

// ==================== SELECTION FUNCTIONS ====================

function selectItem(el, e) {
    if (e.ctrlKey || e.metaKey) {
        el.classList.toggle('selected');
        if (el.classList.contains('selected')) {
            selectedItems.add(el.dataset.id);
        } else {
            selectedItems.delete(el.dataset.id);
        }
    } else {
        document.querySelectorAll('.item.selected').forEach(i => i.classList.remove('selected'));
        selectedItems.clear();
        el.classList.add('selected');
        selectedItems.add(el.dataset.id);
    }

    updateSelectionBar();
}

function updateSelectionBar() {
    const bar = document.getElementById('selectionBar');
    const count = document.getElementById('selectionCount');

    if (selectedItems.size > 0) {
        bar?.classList.add('show');
        if (count) count.textContent = selectedItems.size;
    } else {
        bar?.classList.remove('show');
    }
}

// ==================== MODAL FUNCTIONS ====================

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        if (modalId === 'folderModal') {
            setTimeout(() => {
                const input = document.getElementById('folderName');
                if (input) {
                    input.value = '';
                    input.focus();
                }
            }, 100);
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

function showConfirm(options) {
    const { title, message, icon = 'warning', confirmText = 'Ya, Lanjutkan', danger = false, onConfirm } = options;

    const modal = document.getElementById('confirmModal');
    const iconEl = document.getElementById('confirmIcon');
    const titleEl = document.getElementById('confirmTitle');
    const messageEl = document.getElementById('confirmMessage');
    const btnEl = document.getElementById('confirmBtn');

    if (!modal) return;

    iconEl.className = 'modal-confirm-icon ' + icon;
    iconEl.innerHTML = icon === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' :
                       icon === 'danger' ? '<i class="fas fa-trash-alt"></i>' :
                       '<i class="fas fa-info-circle"></i>';

    titleEl.textContent = title;
    messageEl.textContent = message;
    btnEl.textContent = confirmText;
    btnEl.className = 'btn-confirm ' + (danger ? 'danger' : '');

    confirmCallback = onConfirm;

    showModal('confirmModal');
}

function executeConfirm() {
    closeModal('confirmModal');
    if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
    }
}

function showPrompt(options) {
    const { title, label, value = '', onConfirm } = options;

    const titleEl = document.getElementById('promptTitle');
    const labelEl = document.getElementById('promptLabel');
    const input = document.getElementById('promptInput');

    if (titleEl) titleEl.textContent = title;
    if (labelEl) labelEl.textContent = label;
    if (input) input.value = value;

    promptCallback = onConfirm;

    showModal('promptModal');

    setTimeout(() => input?.focus(), 100);
}

function executePrompt() {
    const input = document.getElementById('promptInput');
    const value = input?.value.trim();

    if (!value) {
        showToast('Nama tidak boleh kosong', 'error');
        return;
    }

    closeModal('promptModal');
    if (promptCallback) {
        promptCallback(value);
        promptCallback = null;
    }
}

function showMoveModal(itemName, itemId, itemType, onConfirm) {
    const moveItemName = document.getElementById('moveItemName');
    if (moveItemName) moveItemName.textContent = itemName;

    selectedTargetFolder = null;
    moveCallback = () => onConfirm(selectedTargetFolder);

    renderFolderTree();
    showModal('moveModal');
}

function renderFolderTree() {
    const container = document.getElementById('folderTree');
    if (!container) return;

    container.innerHTML = '<div class="modal-loading"><div class="spinner"></div><p>Memuat folder...</p></div>';

    fetch(BASE_URL + '/process/fileManager.php?path=folders/tree')
        .then(res => res.json())
        .then(response => {
            let folders = [];

            if (response.success && response.data) {
                folders = response.data;
            } else if (response.success && response.folders) {
                folders = response.folders;
            } else if (Array.isArray(response)) {
                folders = response;
            } else if (response.data && Array.isArray(response.data)) {
                folders = response.data;
            }

            if (folders.length === 0) {
                container.innerHTML = '<p style="color: var(--gray-500); text-align: center;">Tidak ada folder tujuan</p>';
                return;
            }

            let html = `
                <div class="tree-folder" onclick="selectTargetFolder(1, this)" data-id="1">
                    <i class="fas fa-folder"></i>
                    <span>📁 My Files (Root)</span>
                </div>
                <div class="tree-children" style="display: block;">
                    ${buildTreeHTML(folders, CURRENT_FOLDER)}
                </div>
            `;

            container.innerHTML = html;
            initTreeSelection();
        })
        .catch(err => {
            console.error('Error loading tree:', err);
            container.innerHTML = `
                <div class="tree-folder selected" onclick="selectTargetFolder(1, this)" data-id="1">
                    <i class="fas fa-folder"></i>
                    <span>📁 My Files (Root)</span>
                </div>
            `;
            selectedTargetFolder = 1;
        });
}

function buildTreeHTML(folders, excludeId, level = 0) {
    if (!Array.isArray(folders) || folders.length === 0) {
        return '';
    }

    let html = '';
    folders.forEach(folder => {
        if (folder.id == excludeId || folder.id == CURRENT_FOLDER) return;

        const padding = level * 24;
        const hasChildren = folder.children && folder.children.length > 0;

        html += `
            <div class="tree-folder" 
                 onclick="selectTargetFolder(${folder.id}, this)" 
                 data-id="${folder.id}" 
                 style="padding-left: ${16 + padding}px">
                <i class="fas fa-folder" style="color: ${hasChildren ? '#eab308' : '#6b7280'}"></i>
                <span>${escapeHtml(folder.name)}</span>
            </div>
        `;

        if (hasChildren) {
            html += `
                <div class="tree-children" style="display: block;">
                    ${buildTreeHTML(folder.children, excludeId, level + 1)}
                </div>
            `;
        }
    });
    return html;
}

function selectTargetFolder(id, el) {
    if (selectedTargetFolder === id) return;

    selectedTargetFolder = id;

    document.querySelectorAll('.tree-folder').forEach(f => {
        f.classList.remove('selected');
        f.style.background = '';
        f.style.borderColor = '';
    });

    el.classList.add('selected');
    el.style.background = 'rgba(59, 130, 246, 0.15)';
    el.style.borderColor = 'var(--blue)';

    const confirmBtn = document.querySelector('#moveModal .btn-confirm');
    if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.style.opacity = '1';
    }
}

function initTreeSelection() {
    if (!selectedTargetFolder) {
        const root = document.querySelector('.tree-folder[data-id="1"]');
        if (root) {
            selectTargetFolder(1, root);
        }
    }
}

function executeMove() {
    if (!selectedTargetFolder) {
        showToast('Pilih folder tujuan terlebih dahulu', 'error');
        return;
    }

    if (selectedTargetFolder == CURRENT_FOLDER) {
        showToast('File sudah berada di folder ini', 'error');
        return;
    }

    closeModal('moveModal');
    if (moveCallback) {
        moveCallback();
        moveCallback = null;
    }
}

function toggleDropdown(menuId) {
    const menu = document.getElementById(menuId);
    if (!menu) return;

    const isShown = menu.classList.contains('show');
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
    if (!isShown) menu.classList.add('show');
}

function createFolder() {
    const newMenu = document.getElementById('newMenu');
    if (newMenu) newMenu.classList.remove('show');
    showModal('folderModal');
}

async function saveFolder() {
    const nameInput = document.getElementById('folderName');
    const name = nameInput?.value.trim();

    if (!name) {
        showToast('Nama folder tidak boleh kosong', 'error');
        return;
    }

    try {
        const res = await fetch(BASE_URL + '/process/fileManager.php?path=folders/create', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({name, parent_id: CURRENT_FOLDER})
        });

        const data = await res.json();

        if (data.success) {
            showToast('Folder berhasil dibuat', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(data.error || 'Gagal membuat folder', 'error');
        }
    } catch (err) {
        console.error('Error:', err);
        showToast('Terjadi kesalahan server', 'error');
    }
}

function triggerUpload() {
    const newMenu = document.getElementById('newMenu');
    if (newMenu) newMenu.classList.remove('show');
    showModal('uploadModal');
    initUploadZone();
}

function initUploadZone() {
    const zone = document.getElementById('uploadZone');
    const input = document.getElementById('fileInput');

    if (!zone || !input) return;

    zone.ondragover = (e) => {
        e.preventDefault();
        zone.classList.add('drag-over');
    };

    zone.ondragleave = () => zone.classList.remove('drag-over');

    zone.ondrop = (e) => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        if (e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    };

    zone.onclick = (e) => {
        if (e.target === zone || e.target.closest('.upload-zone-icon') || e.target.tagName === 'H4' || e.target.tagName === 'P') {
            input.click();
        }
    };

    input.onchange = (e) => {
        if (e.target.files.length > 0) {
            handleFiles(e.target.files);
        }
    };
}

function handleFiles(files) {
    const fileArray = Array.from(files);
    fileArray.forEach(uploadFile);
}

async function uploadFile(file) {
    const list = document.getElementById('uploadList');
    if (!list) return;

    const div = document.createElement('div');
    div.className = 'upload-item';
    div.id = 'upload-' + Date.now();
    div.innerHTML = `
        <div class="upload-item-icon"><i class="fas fa-file"></i></div>
        <div class="upload-item-info">
            <div class="upload-item-name">${escapeHtml(file.name)}</div>
            <div class="upload-item-size">${formatFileSize(file.size)}</div>
            <div class="upload-item-progress"><div class="upload-item-progress-bar" style="width: 0%"></div></div>
        </div>
    `;
    list.appendChild(div);

    const formData = new FormData();
    formData.append('file', file);
    formData.append('folder_id', CURRENT_FOLDER);

    const xhr = new XMLHttpRequest();

    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const pct = Math.round((e.loaded / e.total) * 100);
            const progressBar = div.querySelector('.upload-item-progress-bar');
            if (progressBar) progressBar.style.width = pct + '%';
        }
    };

    xhr.onload = () => {
        if (xhr.status === 200) {
            try {
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                    div.style.opacity = '0.5';
                    showToast(`File ${file.name} berhasil diupload`, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    div.style.borderColor = 'var(--red)';
                    showToast(res.error || `Gagal upload ${file.name}`, 'error');
                }
            } catch (err) {
                showToast('Error parsing response', 'error');
            }
        } else {
            div.style.borderColor = 'var(--red)';
            showToast(`Upload failed: ${xhr.statusText}`, 'error');
        }
    };

    xhr.onerror = () => {
        div.style.borderColor = 'var(--red)';
        showToast('Network error during upload', 'error');
    };

    xhr.open('POST', BASE_URL + '/process/fileManager.php?path=files/upload');
    xhr.send(formData);
}

function setViewMode(mode) {
    document.cookie = `fileManagerView=${mode};path=/;max-age=31536000`;
    location.reload();
}

// ==================== CONTEXT MENU ====================

function showContextMenu(e, type, id) {
    e.preventDefault();
    e.stopPropagation();

    currentContext = {type, id};
    const menu = document.getElementById('contextMenu');

    if (!menu) return;

    let x = e.pageX;
    let y = e.pageY;

    const menuWidth = 200;
    const menuHeight = 250;

    if (x + menuWidth > window.innerWidth) {
        x = window.innerWidth - menuWidth - 10;
    }
    if (y + menuHeight > window.innerHeight) {
        y = window.innerHeight - menuHeight - 10;
    }

    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
    menu.classList.add('show');
}

async function contextAction(action) {
    if (!currentContext) return;
    const {type, id} = currentContext;

    const menu = document.getElementById('contextMenu');
    if (menu) menu.classList.remove('show');

    switch(action) {
        case 'download':
            if (type === 'file') {
                downloadFile(id);
            } else {
                window.open(BASE_URL + `/process/fileManager.php?path=folders/download&id=${id}`);
            }
            break;

        case 'rename':
            const el = document.querySelector(`[data-id="${id}"]`);
            const currentName = el?.querySelector('.item-name')?.textContent?.trim() || '';
            const displayName = type === 'file' ? currentName.replace(/\.[^/.]+$/, '') : currentName;

            showPrompt({
                title: type === 'folder' ? 'Rename Folder' : 'Rename File',
                label: 'Nama baru',
                value: displayName,
                onConfirm: async (newName) => {
                    try {
                        const res = await fetch(BASE_URL + `/process/fileManager.php?path=${type}s/rename`, {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({id, name: newName})
                        });
                        const data = await res.json();

                        if (data.success) {
                            showToast('Berhasil diubah nama', 'success');
                            location.reload();
                        } else {
                            showToast(data.error || 'Gagal mengubah nama', 'error');
                        }
                    } catch (err) {
                        showToast('Terjadi kesalahan', 'error');
                    }
                }
            });
            break;

        case 'move':
            const moveEl = document.querySelector(`[data-id="${id}"]`);
            const moveName = moveEl?.querySelector('.item-name')?.textContent?.trim() || 'item';

            showMoveModal(moveName, id, type, async (targetId) => {
                try {
                    const res = await fetch(BASE_URL + `/process/fileManager.php?path=${type}s/move`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({id, target_folder_id: targetId})
                    });
                    const data = await res.json();

                    if (data.success) {
                        showToast('Berhasil dipindahkan', 'success');
                        location.reload();
                    } else {
                        showToast(data.error || 'Gagal memindahkan', 'error');
                    }
                } catch (err) {
                    showToast('Terjadi kesalahan', 'error');
                }
            });
            break;

        case 'delete':
            const delEl = document.querySelector(`[data-id="${id}"]`);
            const delName = delEl?.querySelector('.item-name')?.textContent?.trim() || 'item';

            showConfirm({
                title: 'Hapus ' + (type === 'folder' ? 'Folder' : 'File'),
                message: `Apakah Anda yakin ingin menghapus "${delName}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'danger',
                confirmText: 'Ya, Hapus',
                danger: true,
                onConfirm: async () => {
                    try {
                        const res = await fetch(BASE_URL + `/process/fileManager.php?path=${type}s/delete`, {
                            method: 'DELETE',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({id})
                        });
                        const data = await res.json();

                        if (data.success) {
                            showToast('Berhasil dihapus', 'success');
                            location.reload();
                        } else {
                            showToast(data.error || 'Gagal menghapus', 'error');
                        }
                    } catch (err) {
                        showToast('Terjadi kesalahan', 'error');
                    }
                }
            });
            break;
    }
}

function downloadFile(id) {
    const downloadUrl = BASE_URL + '/process/fileManager.php?path=files/download&id=' + id;
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = downloadUrl;
    document.body.appendChild(iframe);

    setTimeout(() => {
        document.body.removeChild(iframe);
    }, 5000);

    showToast('Mengunduh file...', 'info');
}

function downloadSelected() {
    if (selectedItems.size === 0) return;

    showModal('downloadModal');
    let delay = 0;

    selectedItems.forEach(id => {
        setTimeout(() => {
            const el = document.querySelector(`[data-id="${id}"]`);
            if (el?.dataset.type === 'file') {
                downloadFile(id);
            }
        }, delay);
        delay += 500;
    });

    setTimeout(() => closeModal('downloadModal'), delay + 1000);
}

async function moveSelected() {
    if (selectedItems.size === 0) return;

    const firstId = Array.from(selectedItems)[0];
    const firstEl = document.querySelector(`[data-id="${firstId}"]`);
    const itemName = selectedItems.size === 1 ? 
        (firstEl?.querySelector('.item-name')?.textContent?.trim() || 'item') : 
        `${selectedItems.size} item`;

    showMoveModal(itemName, null, null, async (targetId) => {
        showModal('downloadModal');

        const items = Array.from(selectedItems);
        const elements = items.map(id => document.querySelector(`[data-id="${id}"]`));
        const types = elements.map(el => el?.dataset.type);

        try {
            const promises = items.map((id, index) => 
                fetch(BASE_URL + `/process/fileManager.php?path=${types[index]}s/move`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id, target_folder_id: targetId})
                })
            );

            await Promise.all(promises);
            showToast('Item berhasil dipindahkan', 'success');
            location.reload();
        } catch (err) {
            showToast('Terjadi kesalahan saat memindahkan', 'error');
        } finally {
            closeModal('downloadModal');
        }
    });
}

async function deleteSelected() {
    if (selectedItems.size === 0) return;

    showConfirm({
        title: 'Hapus Multiple Item',
        message: `Apakah Anda yakin ingin menghapus ${selectedItems.size} item? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'danger',
        confirmText: 'Ya, Hapus Semua',
        danger: true,
        onConfirm: async () => {
            const items = Array.from(selectedItems);
            const elements = items.map(id => document.querySelector(`[data-id="${id}"]`));
            const types = elements.map(el => el?.dataset.type);

            try {
                const promises = items.map((id, index) => 
                    fetch(BASE_URL + `/process/fileManager.php?path=${types[index]}s/delete`, {
                        method: 'DELETE',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({id})
                    })
                );

                await Promise.all(promises);
                showToast('Item dihapus', 'success');
                location.reload();
            } catch (err) {
                showToast('Terjadi kesalahan', 'error');
            }
        }
    });
}

// ==================== DRAG & DROP ====================

function initDragDrop() {
    document.querySelectorAll('.item').forEach(item => {
        item.draggable = true;

        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', JSON.stringify({
                type: item.dataset.type,
                id: item.dataset.id
            }));
            item.classList.add('dragging');
        });

        item.addEventListener('dragend', () => {
            item.classList.remove('dragging');
        });
    });

    document.querySelectorAll('.folder-item').forEach(folder => {
        folder.addEventListener('dragover', (e) => {
            e.preventDefault();
            folder.classList.add('selected');
        });

        folder.addEventListener('dragleave', () => {
            folder.classList.remove('selected');
        });

        folder.addEventListener('drop', async (e) => {
            e.preventDefault();
            folder.classList.remove('selected');

            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                if (data.id === folder.dataset.id) return;

                const res = await fetch(BASE_URL + `/process/fileManager.php?path=${data.type}s/move`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        id: data.id,
                        target_folder_id: folder.dataset.id
                    })
                });

                const result = await res.json();
                if (result.success) {
                    showToast('Berhasil dipindahkan', 'success');
                    location.reload();
                }
            } catch (err) {
                console.error('Drag drop error:', err);
            }
        });
    });

    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        dropZone.addEventListener('dragover', (e) => e.preventDefault());

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFiles(files);
            }
        });
    }
}

// ==================== SEARCH & KEYBOARD ====================

function initSearch() {
    const search = document.getElementById('searchInput');
    if (!search) return;

    let debounceTimer;
    search.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const term = e.target.value.toLowerCase().trim();

            document.querySelectorAll('.item').forEach(item => {
                const name = item.querySelector('.item-name')?.textContent?.toLowerCase() || '';
                if (term === '' || name.includes(term)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }, 300);
    });
}

function initKeyboard() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.item.selected').forEach(i => i.classList.remove('selected'));
            selectedItems.clear();
            updateSelectionBar();

            document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
            document.getElementById('contextMenu')?.classList.remove('show');
        }

        if (e.key === 'Delete' && selectedItems.size > 0) {
            deleteSelected();
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            document.querySelectorAll('.item').forEach(item => {
                item.classList.add('selected');
                selectedItems.add(item.dataset.id);
            });
            updateSelectionBar();
        }
    });
}

// ==================== NOTIFICATIONS ====================

function initNotifications() {
    const btn = document.getElementById('notificationBtn');
    const dropdown = document.getElementById('notificationDropdown');

    if (!btn || !dropdown) return;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    const markReadBtn = document.getElementById('markAllRead');
    if (markReadBtn) {
        markReadBtn.addEventListener('click', () => {
            const dot = document.getElementById('notificationDot');
            if (dot) dot.style.display = 'none';
        });
    }
}

// ==================== UTILITY FUNCTIONS ====================

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast';

    const icons = {
        success: 'check-circle',
        error: 'times-circle',
        info: 'info-circle'
    };

    const colors = {
        success: 'var(--green)',
        error: 'var(--red)',
        info: 'var(--blue)'
    };

    toast.innerHTML = `
        <div class="toast-icon ${type}" style="background: ${colors[type]}15; color: ${colors[type]}">
            <i class="fas fa-${icons[type]}"></i>
        </div>
        <div class="toast-content">
            <h4>${type === 'success' ? 'Berhasil' : type === 'error' ? 'Error' : 'Info'}</h4>
            <p>${message}</p>
        </div>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}