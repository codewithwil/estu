let clients         = [];
let clientToDelete  = null;
let uploadedFile    = null;

const clientsPreviewGrid = document.getElementById("clientsPreviewGrid");
const clientsPreviewBadge = document.getElementById("clientsPreviewBadge");

const API = {
  async getClients() {
    const res = await fetch(BASE_URL + "process/client.php?action=get");
    return await res.json();
  },

  async createClient(data) {
    return fetch(BASE_URL + "process/client.php?action=create", {
      method: "POST",
      body: data,
    });
  },

  async updateClient(data) {
    return fetch(BASE_URL + "process/client.php?action=update", {
      method: "POST",
      body: data,
    });
  },

  async deleteClient(id) {
    return fetch(BASE_URL + "process/client.php?action=delete", {
      method: "POST",
      body: new URLSearchParams({ id }),
    });
  },
};
document.getElementById("logoInput").addEventListener("change", (e) => {
  uploadedFile = e.target.files[0];

  const preview = document.getElementById("previewImage");
  preview.src = URL.createObjectURL(uploadedFile);
  preview.style.display = "block";
});

async function loadClients() {
  clients = await API.getClients();
  renderClients();
}

function renderClientsPreview() {
    console.log('renderClientsPreview called', clientsPreviewGrid, clients.length);
    
    if (!clientsPreviewGrid) {
        console.error('clientsPreviewGrid not found!');
        return;
    }
    
    if (clientsPreviewBadge) {
        clientsPreviewBadge.textContent = `${clients.length} klien`;
    }
    
    if (clients.length === 0) {
        clientsPreviewGrid.innerHTML = `
            <div class="clients-preview-empty">
                <i class="fas fa-handshake"></i>
                <p>Belum ada klien. Tambahkan klien untuk melihat preview.</p>
            </div>
        `;
        return;
    }
    
    clientsPreviewGrid.innerHTML = clients.map((client) => `
        <div class="client-preview-item" title="${client.name}">
            <img src="/estu/assets/images/client/${client.logo}" alt="${client.name}">
        </div>
    `).join('');
}


function renderClients() {
    const clientsGrid = document.getElementById("clientsGrid");
    const totalClients = document.getElementById("totalClients");

    if (totalClients) {
        totalClients.innerText = clients.length;
    }

    renderClientsPreview();

    if (!clients.length) {
        clientsGrid.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-handshake"></i>
                <h3>Belum ada klien</h3>
                <p>Tambahkan klien untuk ditampilkan di website</p>
            </div>
        `;
        return;
    }

    clientsGrid.innerHTML = clients
        .map(
            (c) => `
                <div class="client-card">
                    <div class="client-actions">
                        <button class="client-action-btn" onclick="openEditModal(${c.id})">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="client-action-btn delete" onclick="openDeleteModal(${c.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="client-logo">
                        <img src="/estu/assets/images/client/${c.logo}" alt="${c.name}">
                    </div>
                    <div class="client-name">${c.name}</div>
                    <div class="client-since">Since ${c.since || '-'}</div>
                </div>
            `,
        )
        .join("");
}


document.getElementById("clientForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const name = document.getElementById("clientName").value;
  const since = document.getElementById("clientSince").value;

  if (!uploadedFile) {
    return showToast("error", "Error", "Logo wajib");
  }

  const formData = new FormData();
  formData.append("name", name);
  formData.append("since", since);
  formData.append("logo", uploadedFile);

  await API.createClient(formData);

  loadClients();
  closeAddModal();
  showToast("success", "Berhasil", "Client ditambah");
});

document.getElementById("editForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new URLSearchParams({
    id: document.getElementById("editId").value,
    name: document.getElementById("editName").value,
    since: document.getElementById("editSince").value,
  });

  await API.updateClient(formData);

  loadClients();
  closeEditModal();
  showToast("success", "Berhasil", "Client diupdate");
});

async function confirmDelete() {
  if (!clientToDelete) return;

  await API.deleteClient(clientToDelete);

  loadClients();
  closeDeleteModal();

  showToast("success", "Terhapus", "Client dihapus");
}

function openAddModal() {
  document.getElementById("addModal").classList.add("show");
}
function closeAddModal() {
  document.getElementById("addModal").classList.remove("show");
}

function openEditModal(id) {
  const c = clients.find((x) => x.id == id);
  if (!c) return;

  document.getElementById("editId").value = c.id;
  document.getElementById("editName").value = c.name;
  document.getElementById("editSince").value = c.since;
  document.getElementById("editPreviewImg").src =
    "/estu/assets/images/client/" + c.logo;

  document.getElementById("editModal").classList.add("show");
}
function closeEditModal() {
  document.getElementById("editModal").classList.remove("show");
}

function openDeleteModal(id) {
  const c = clients.find((x) => x.id == id);
  if (!c) return;

  clientToDelete = id;

  document.getElementById("deleteClientName").innerText = c.name;
  document.getElementById("deleteModal").classList.add("show");
}

function closeDeleteModal() {
  clientToDelete = null;
  document.getElementById("deleteModal").classList.remove("show");
}

function showToast(type, title, msg) {
  const toast = document.getElementById("toast");
  const iconBox = document.getElementById("toastIcon");
  const titleEl = document.getElementById("toastTitle");
  const msgEl = document.getElementById("toastMessage");

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

loadClients();
