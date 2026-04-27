let currentView     = document.cookie.includes('userManagerView=list') ? 'list' : 'grid';
let selectedUserId  = null;
let deleteUserId    = null;

function setViewMode(mode) {
    currentView = mode;
    document.cookie = `userManagerView=${mode}; path=/; max-age=31536000`;
    location.reload();
}

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

function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}

function openAddUserModal() {
    if (!IS_SUPERADMIN) { showToast('Hanya Superadmin yang bisa menambah user', 'error'); return; }
    document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-user-plus" style="margin-right: 8px; color: var(--blue);"></i>Tambah User';
    document.getElementById('userId').value = '';
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userPassword').value = '';
    document.getElementById('userRole').value = 'editor';
    document.getElementById('passwordGroup').style.display = 'block';
    openModal('userModal');
}

function editUser(id) {
    fetch(`${PROCESS_URL}?action=get&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) throw new Error(res.data?.error || 'Gagal memuat data');
            const data = res.data;
            
            document.getElementById('userModalTitle').innerHTML = '<i class="fas fa-edit" style="margin-right: 8px; color: var(--amber);"></i>Edit User';
            document.getElementById('userId').value = id;
            document.getElementById('userName').value = data.name || '';
            document.getElementById('userEmail').value = data.email || '';
            document.getElementById('userRole').value = data.role || 'editor';
            document.getElementById('passwordGroup').style.display = 'none';
            openModal('userModal');
        })
        .catch(err => showToast(err.message, 'error'));
}

function saveUser() {
    const id = document.getElementById('userId').value;
    const data = {
        name: document.getElementById('userName').value.trim(),
        email: document.getElementById('userEmail').value.trim(),
        role: document.getElementById('userRole').value
    };

    if (!data.name || !data.email) {
        showToast('Nama dan email wajib diisi!', 'error');
        return;
    }

    if (!id) {
        data.password = document.getElementById('userPassword').value;
        if (!data.password || data.password.length < 6) {
            showToast('Password minimal 6 karakter!', 'error');
            return;
        }
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
            showToast(id ? 'User berhasil diupdate!' : 'User berhasil dibuat!', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res.data?.error || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menyimpan user', 'error'));
}

function resetPassword(id) {
    if (!IS_SUPERADMIN) { showToast('Hanya Superadmin yang bisa reset password', 'error'); return; }
    document.getElementById('resetUserId').value = id;
    document.getElementById('newPassword').value = '';
    openModal('resetPasswordModal');
}

function executeResetPassword() {
    const id = document.getElementById('resetUserId').value;
    const password = document.getElementById('newPassword').value;
    
    if (!password || password.length < 6) {
        showToast('Password minimal 6 karakter!', 'error');
        return;
    }

    fetch(`${PROCESS_URL}?action=updatePassword`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, password })
    })
    .then(r => r.json())
    .then(res => {
        closeModal('resetPasswordModal');
        if (res.success) {
            showToast('Password berhasil diupdate!', 'success');
        } else {
            showToast(res.data?.error || 'Gagal update password', 'error');
        }
    })
    .catch(() => {
        closeModal('resetPasswordModal');
        showToast('Gagal update password', 'error');
    });
}

function confirmDeleteUser(id) {
    if (!IS_SUPERADMIN) { showToast('Hanya Superadmin yang bisa menghapus user', 'error'); return; }
    deleteUserId = id;
    openModal('confirmModal');
}

function executeDeleteUser() {
    if (!deleteUserId) return;
    
    fetch(`${PROCESS_URL}?action=delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: deleteUserId })
    })
    .then(r => r.json())
    .then(res => {
        closeModal('confirmModal');
        if (res.success) {
            showToast('User berhasil dihapus!', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(res.data?.error || 'Gagal menghapus user', 'error');
        }
    })
    .catch(() => {
        closeModal('confirmModal');
        showToast('Gagal menghapus user', 'error');
    });
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast';
    const icons = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' };
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

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(overlay.id); });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(m => closeModal(m.id));
    }
});