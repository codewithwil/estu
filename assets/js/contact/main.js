let contactData = {
    whyChoose: []
};

document.addEventListener('DOMContentLoaded', () => {
    loadContactData();
    setupEventListeners();
    setupRichEditor();
    renderWhyForm();
    updatePreview();
});

// Setup Rich Text Editor
function setupRichEditor() {
    document.querySelectorAll('.rte-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const command = btn.dataset.command;
            const editor = btn.closest('.rich-editor-container').querySelector('.rich-editor');
            
            editor.focus();
            document.execCommand(command, false, null);
            
            // Update active state
            if (command !== 'removeFormat') {
                btn.classList.toggle('active', document.queryCommandState(command));
            } else {
                btn.closest('.rich-editor-toolbar').querySelectorAll('.rte-btn').forEach(b => b.classList.remove('active'));
            }
            
            updatePreview();
        });
    });

    // Update toolbar state on selection change
    document.addEventListener('selectionchange', () => {
        document.querySelectorAll('.rte-btn').forEach(btn => {
            const command = btn.dataset.command;
            if (command !== 'removeFormat') {
                btn.classList.toggle('active', document.queryCommandState(command));
            }
        });
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

// ============================================
// WHY CHOOSE US - CRUD FUNCTIONS
// ============================================

// Render Why Choose form
function renderWhyForm() {
    const container = document.getElementById('whyFormList');
    if (!container) return;
    
    if (contactData.whyChoose.length === 0) {
        container.innerHTML = `
            <div class="empty-why-state">
                <i class="fas fa-star"></i>
                <p>Belum ada poin keunggulan. Klik "Tambah Poin" untuk menambahkan.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = contactData.whyChoose.map((item, index) => `
        <div class="why-form-item" draggable="true" data-index="${index}">
            <span class="why-number">${index + 1}</span>
            <div class="why-input-wrapper">
                <input type="text" 
                    value="${escapeHtml(item)}" 
                    oninput="updateWhyItem(${index}, this.value)"
                    placeholder="Masukkan poin keunggulan...">
            </div>
            <div class="why-actions">
                <button type="button" class="why-btn move" title="Drag to reorder">
                    <i class="fas fa-grip-vertical"></i>
                </button>
                <button type="button" class="why-btn delete" onclick="deleteWhyItem(${index})" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');

    setupDragAndDrop();
}

// Escape HTML untuk mencegah XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Tambah item baru
function addNewWhyItem() {
    contactData.whyChoose.push('New advantage point');
    renderWhyForm();
    updatePreview();
    
    // Focus ke input yang baru ditambahkan
    setTimeout(() => {
        const inputs = document.querySelectorAll('.why-input-wrapper input');
        const lastInput = inputs[inputs.length - 1];
        if (lastInput) {
            lastInput.focus();
            lastInput.select();
        }
    }, 10);
}

// Update item
function updateWhyItem(index, value) {
    contactData.whyChoose[index] = value;
    updatePreview();
}

// Hapus item
function deleteWhyItem(index) {
    if (confirm('Yakin ingin menghapus poin ini?')) {
        contactData.whyChoose.splice(index, 1);
        renderWhyForm();
        updatePreview();
    }
}

// Drag and Drop untuk reorder
function setupDragAndDrop() {
    const items = document.querySelectorAll('.why-form-item');
    let draggedItem = null;

    items.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedItem = null;
            
            // Update data array berdasarkan urutan DOM
            const newOrder = [];
            document.querySelectorAll('.why-form-item').forEach(el => {
                const index = parseInt(el.dataset.index);
                newOrder.push(contactData.whyChoose[index]);
            });
            contactData.whyChoose = newOrder;
            renderWhyForm();
            updatePreview();
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            return false;
        });

        item.addEventListener('dragenter', function(e) {
            if (this !== draggedItem) {
                this.style.borderColor = 'var(--blue)';
            }
        });

        item.addEventListener('dragleave', function() {
            this.style.borderColor = '';
        });

        item.addEventListener('drop', function(e) {
            e.stopPropagation();
            this.style.borderColor = '';
            
            if (draggedItem !== this) {
                const allItems = [...document.querySelectorAll('.why-form-item')];
                const draggedIdx = allItems.indexOf(draggedItem);
                const droppedIdx = allItems.indexOf(this);
                
                if (draggedIdx < droppedIdx) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }
            }
            return false;
        });
    });
}

// ============================================
// PREVIEW UPDATE
// ============================================

function updatePreview() {
    // Header
    const titleLine = document.getElementById('titleLine');
    
    if (document.getElementById('previewSectionTitle')) {
        document.getElementById('previewSectionTitle').textContent = 
            titleLine?.value || "LET'S COLLABORATE";
    }
    
    if (document.getElementById('previewSectionDesc')) {
        document.getElementById('previewSectionDesc').innerHTML = 
            getValue('sectionDesc') || 'Have an event idea? Contact our local Bali EO team directly via WhatsApp for quick response.';
    }

    // Contact Info
    const whatsAppNumber = document.getElementById('whatsAppNumber');
    const whatsAppNote = document.getElementById('whatsAppNote');
    const email = document.getElementById('email');
    
    if (document.getElementById('previewWhatsAppNumber')) {
        document.getElementById('previewWhatsAppNumber').textContent = 
            whatsAppNumber?.value || '+62 815 7200 0039';
    }
    
    if (document.getElementById('previewWhatsAppNote')) {
        document.getElementById('previewWhatsAppNote').textContent = 
            whatsAppNote?.value || 'Click to chat directly • Response in 1-2 hours';
    }
    
    if (document.getElementById('previewEmail')) {
        document.getElementById('previewEmail').textContent = 
            email?.value || 'estu.office.bali@gmail.com';
    }
    
    // HTML content (rich editor)
    if (document.getElementById('previewHours')) {
        document.getElementById('previewHours').innerHTML = 
            getValue('operatingHours') || 'Monday - Saturday<br>09:00 - 17:00 WITA (Bali Time)';
    }
    
    if (document.getElementById('previewLocation')) {
        document.getElementById('previewLocation').innerHTML = 
            getValue('location') || 'Jalan Raya Padang Luwih, Dalung, North Kuta,<br>Badung Regency, Bali 80117, Indonesia';
    }

    // Button text
    const btnPreview = document.querySelector('.btn-whatsapp-preview');
    const whatsAppButtonText = document.getElementById('whatsAppButtonText');
    if (btnPreview && whatsAppButtonText) {
        btnPreview.innerHTML = 
            `<i class="fab fa-whatsapp"></i> ${whatsAppButtonText.value || 'Chat WhatsApp Now'}`;
    }

    // Why Choose list
    const whyList = document.getElementById('previewWhyList');
    if (whyList) {
        whyList.innerHTML = contactData.whyChoose.map(item => `
            <li><i class="fas fa-check"></i> ${item || '-'}</li>
        `).join('');
    }

    // Quote
    const quoteEl = document.getElementById('previewWhyQuote');
    if (quoteEl) {
        quoteEl.innerHTML = getValue('whyQuote') || '"We believe fast communication is the key to successful events. Chat with our EO Bali team anytime!"';
    }

    // Footer
    const brandName = document.getElementById('brandName');
    const brandTagline = document.getElementById('brandTagline');
    const copyrightText = document.getElementById('copyrightText');
    
    const logoEl = document.querySelector('.preview-footer-brand .logo');
    const taglineEl = document.querySelector('.preview-footer-brand .tagline');
    const copyEl = document.querySelector('.preview-footer-copy');
    
    if (logoEl && brandName) {
        logoEl.textContent = brandName.value || 'ESTU';
    }
    
    if (taglineEl && brandTagline) {
        taglineEl.textContent = brandTagline.value || 'Event Organizer Bali';
    }
    
    if (copyEl && copyrightText) {
        copyEl.innerHTML = copyrightText.value || '&copy; 2024 ESTU. All rights reserved.';
    }
}

// ============================================
// LOAD & SAVE
// ============================================
function fillForm() {
    const setValue = (id, value) => {
        const el = document.getElementById(id);
        if (!el) return;

        if (el.tagName === 'DIV' && el.contentEditable === 'true') {
            el.innerHTML = value || '';
        } else {
            el.value = value || '';
        }
    };

    setValue('titleLine', contactData.titleLine);
    setValue('sectionDesc', contactData.sectionDesc);
    setValue('whatsAppNumber', contactData.whatsAppNumber);
    setValue('whatsAppNote', contactData.whatsAppNote);
    setValue('email', contactData.email);
    setValue('operatingHours', contactData.operatingHours);
    setValue('location', contactData.location);
    setValue('whatsAppButtonText', contactData.whatsAppButtonText);
    setValue('whyQuote', contactData.whyQuote);
    setValue('brandName', contactData.brandName);
    setValue('brandTagline', contactData.brandTagline);
    setValue('copyrightText', contactData.copyrightText);
}
function getValue(id) {
    const el = document.getElementById(id);
    if (!el) return '';

    return (el.tagName === 'DIV' && el.contentEditable === 'true')
        ? el.innerHTML
        : el.value;
}

async function loadContactData() {
    try {
        const res = await fetch(BASE_URL + '/process/contact.php?action=get');
        const data = await res.json();

        if (!data) return;

        contactData = {
            titleLine: data.title_line,
            sectionDesc: data.section_desc,
            whatsAppNumber: data.whatsapp_number,
            whatsAppNote: data.whatsapp_note,
            email: data.email,
            operatingHours: data.operating_hours,
            location: data.location,
            whatsAppButtonText: data.whatsapp_button_text,
            whyChoose: data.why_choose || [],
            whyQuote: data.why_quote,
            brandName: data.brand_name,
            brandTagline: data.brand_tagline,
            copyrightText: data.copyright_text
        };

        fillForm();
        renderWhyForm();
        updatePreview();

    } catch (error) {
        console.error('Load error:', error);
    }
}

async function saveContactData() {
    const formData = new FormData();

    formData.append('id', 1);
    formData.append('titleLine', document.getElementById('titleLine').value);
    formData.append('sectionDesc', getValue('sectionDesc'));
    formData.append('whatsAppNumber', document.getElementById('whatsAppNumber').value);
    formData.append('whatsAppNote', document.getElementById('whatsAppNote').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('operatingHours', getValue('operatingHours'));
    formData.append('location', getValue('location'));
    formData.append('whatsAppButtonText', document.getElementById('whatsAppButtonText').value);
    formData.append('whyQuote', getValue('whyQuote'));
    formData.append('brandName', document.getElementById('brandName').value);
    formData.append('brandTagline', document.getElementById('brandTagline').value);
    formData.append('copyrightText', document.getElementById('copyrightText').value);
    formData.append('whyChoose', JSON.stringify(contactData.whyChoose));

    try {
        const res = await fetch(BASE_URL + 'process/contact.php?action=update', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();

        console.log(result); // DEBUG

        if (result.success) {
            showToast('success', 'Berhasil', 'Data berhasil disimpan 👍');
        } else {
            showToast('error', 'Gagal', 'Gagal menyimpan data');
        }

    } catch (error) {
        console.error(error);
        showToast('error', 'Error', 'Terjadi kesalahan server');
    }
}

function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    
    if (!toast) return;
    
    const titleEl = document.getElementById('toastTitle');
    const msgEl = document.getElementById('toastMessage');
    
    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;
    
    icon.className = 'toast-icon ' + type;
    icon.innerHTML = type === 'success' 
        ? '<i class="fas fa-check"></i>' 
        : '<i class="fas fa-times"></i>';
    
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}
