let isNewImage = false;

document.addEventListener('DOMContentLoaded', () => {
    loadAboutData();
    setupEventListeners();
    initRichEditors();
});


function initRichEditors() {

    document.querySelectorAll('.rte-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            const command   = btn.dataset.command;
            const editor    = btn.closest('.rich-editor-container')
                              .querySelector('.rich-editor');

            editor.focus();
            document.execCommand(command, false, null);

            updateToolbarState(btn.closest('.rich-editor-toolbar'));
            updatePreview();
        });
    });

    document.addEventListener('selectionchange', () => {
        document.querySelectorAll('.rich-editor-toolbar')
            .forEach(updateToolbarState);
    });

    document.querySelectorAll('.rich-editor').forEach(editor => {
        editor.addEventListener('keydown', (e) => {
            if (!(e.ctrlKey || e.metaKey)) return;

            const key = e.key.toLowerCase();
            const map = {
                b: 'bold',
                i: 'italic',
                u: 'underline'
            };

            if (map[key]) {
                e.preventDefault();
                document.execCommand(map[key], false, null);
                updatePreview();
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
            const active = document.queryCommandState(command);
            btn.classList.toggle('active', active);
        }
    });
}

function setupEventListeners() {
    const btn       = document.getElementById('notificationBtn');
    const dropdown  = document.getElementById('notificationDropdown');

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
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    isNewImage = true;

    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowed.includes(file.type)) {
        return showToast('error', 'Error', 'Format file tidak didukung.');
    }

    if (file.size > 5 * 1024 * 1024) {
        return showToast('error', 'Error', 'Ukuran file terlalu besar.');
    }

    const reader = new FileReader();

    reader.onload = (e) => {
        const url = e.target.result;

        document.getElementById('uploadPreview').src                = url;
        document.getElementById('previewImage').src                 = url;
        document.getElementById('uploadPreview').style.display      = 'block';
        document.getElementById('uploadPlaceholder').style.display  = 'none';
        document.getElementById('btnRemoveImage').style.display     = 'flex';
    };

    reader.readAsDataURL(file);
}


function removeImage() {
    isNewImage = false;

    document.getElementById('uploadPreview').src                = '';
    document.getElementById('previewImage').src                 = '';
    document.getElementById('uploadPreview').style.display      = 'none';
    document.getElementById('uploadPlaceholder').style.display  = 'flex';
    document.getElementById('btnRemoveImage').style.display     = 'none';
    document.getElementById('aboutImageInput').value            = '';
}


function updatePreview() {
    const sectionLabel  = val('sectionLabel', 'About Us');
    const title1        = val('titleLine1', 'LOCAL SOUL');
    const title2        = val('titleLine2', 'GLOBAL TOUCH');
    const p1            = html('paragraph1');
    const p2            = html('paragraph2');
    const stat1         = getStat(1, 'Events Completed');
    const stat2         = getStat(2, 'Years Experience');
    const stat3         = getStat(3, 'Client Satisfaction');

    text('previewLabel', sectionLabel);
    text('previewTitle1', title1);
    text('previewTitle2', title2);

    htmlSet('previewParagraph1', p1);
    htmlSet('previewParagraph2', p2);

    setStat('previewStat1', 'previewStatLabel1', stat1);
    setStat('previewStat2', 'previewStatLabel2', stat2);
    setStat('previewStat3', 'previewStatLabel3', stat3);
}

async function saveAboutData() {

    const formData = new FormData();

    formData.append('sectionLabel', val('sectionLabel'));
    formData.append('titleLine1', val('titleLine1'));
    formData.append('titleLine2', val('titleLine2'));

    formData.append('paragraph1', html('paragraph1'));
    formData.append('paragraph2', html('paragraph2'));

    formData.append('stats', JSON.stringify([
        getStat(1),
        getStat(2),
        getStat(3)
    ]));

    const file = document.getElementById('aboutImageInput').files[0];
    if (file) formData.append('image', file);

    try {
        const res = await fetch('/estu/process/about.php?action=save', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();

        if (result.success) {
            showToast('success', 'Berhasil', 'Data berhasil disimpan');
        } else {
            showToast('error', 'Error', 'Gagal menyimpan');
        }

    } catch (err) {
        console.error(err);
        showToast('error', 'Error', 'Server error');
    }
}


async function loadAboutData() {
    try {
        const res = await fetch('/estu/process/about.php?action=get');
        const data = await res.json();

        setVal('sectionLabel', data.sectionLabel);
        setVal('titleLine1', data.titleLine1);
        setVal('titleLine2', data.titleLine2);

        htmlSet('paragraph1', data.paragraph1);
        htmlSet('paragraph2', data.paragraph2);

        fillStat(1, data.stats?.[0]);
        fillStat(2, data.stats?.[1]);
        fillStat(3, data.stats?.[2]);

        if (data.image && !isNewImage) {
            document.getElementById('previewImage').src = data.image;
            document.getElementById('uploadPreview').src = data.image;

            document.getElementById('uploadPreview').style.display = 'block';
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('btnRemoveImage').style.display = 'flex';
        }

        updatePreview();

    } catch (err) {
        console.error(err);
    }
}


const val = (id, def = '') => document.getElementById(id).value || def;
const setVal = (id, v = '') => document.getElementById(id).value = v || '';

const html = (id) => document.getElementById(id).innerHTML;
const htmlSet = (id, v = '') => document.getElementById(id).innerHTML = v || '';

const text = (id, v) => document.getElementById(id).textContent = v;

function getStat(i, defaultLabel = '') {
    return {
        number: val(`stat${i}Number`, '0'),
        suffix: val(`stat${i}Suffix`, ''),
        label: val(`stat${i}Label`, defaultLabel)
    };
}

function setStat(numId, labelId, stat) {
    text(numId, stat.number + stat.suffix);
    text(labelId, stat.label);
}

function fillStat(i, stat = {}) {
    setVal(`stat${i}Number`, stat.number);
    setVal(`stat${i}Suffix`, stat.suffix);
    setVal(`stat${i}Label`, stat.label);
}


function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');

    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;

    icon.className = 'toast-icon';
    icon.classList.add(type);

    icon.innerHTML = type === 'success'
        ? '<i class="fas fa-check"></i>'
        : '<i class="fas fa-times"></i>';

    toast.classList.add('show');

    setTimeout(() => toast.classList.remove('show'), 3000);
}