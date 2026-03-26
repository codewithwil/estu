<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Clients - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/estu/assets/css/toast.css">
    <link rel="stylesheet" href="/estu/assets/css/client/main.css">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari klien..." id="searchInput">
                </div>
                
                <div class="header-actions">
                    <div class="notification-wrapper">
                        <button class="icon-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-dot" id="notificationDot"></span>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <h4>Notifikasi</h4>
                                <button class="mark-all-read" id="markAllRead">Tandai sudah dibaca</button>
                            </div>
                            <div class="notification-list" id="notificationList"></div>
                            <div class="notification-footer">
                                <a href="#" class="view-all">Lihat Semua Notifikasi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Our Clients</h1>
                    <p class="page-subtitle">Kelola logo dan nama klien yang ditampilkan di website.</p>
                </div>

                <div class="clients-preview-section" id="clientsPreviewSection">
                    <div class="clients-preview-header-inline">
                        <h3><i class="fas fa-eye"></i> Live Preview</h3>
                        <span class="preview-badge" id="clientsPreviewBadge">0 klien</span>
                    </div>
                    <div class="clients-preview-grid" id="clientsPreviewGrid">
                        <!-- Clients preview dirender otomatis dari JS -->
                    </div>
                </div>
                <!-- Stats -->
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-icon-box">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="stat-info">
                            <h4 id="totalClients">8</h4>
                            <span>Total Klien</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon-box">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h4>3</h4>
                            <span>Bulan Ini</span>
                        </div>
                    </div>
                </div>

                <!-- Add Button -->
                <div class="add-client-section">
                    <button class="btn-add-client" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Tambah Klien Baru
                    </button>
                </div>

                <!-- Clients Grid -->
                <div class="clients-grid" id="clientsGrid">
                    <!-- Clients will be rendered here -->
                </div>
            </div>
        </main>
    </div>

    <!-- Add Client Modal -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tambah Klien Baru</h3>
                <p>Upload logo dan masukkan nama klien</p>
            </div>
            
            <form id="clientForm">
                <div class="form-group">
                    <label>Logo Klien</label>
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('logoInput').click()">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">Klik untuk upload logo</div>
                        <div class="upload-hint">SVG, PNG, atau JPG (max 2MB)</div>
                        <img src="" alt="Preview" class="preview-image" id="previewImage" style="display: none;">
                    </div>
                    <input type="file" id="logoInput" accept="image/*" style="display: none;">
                </div>
                
                <div class="form-group">
                    <label>Nama Klien</label>
                    <input type="text" class="form-control" id="clientName" placeholder="Contoh: PT Maju Jaya" required>
                </div>
                
                <div class="form-group">
                    <label>Client Since (Opsional)</label>
                    <input type="text" class="form-control" id="clientSince" placeholder="Contoh: 2020">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Klien</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Client Modal -->
    <div class="modal-overlay edit-modal" id="editModal">
        <div class="modal-box">
            <div class="client-preview" id="editPreview">
                <img id="editPreviewImg" src="" alt="Client Logo">
            </div>
                        
            <div class="modal-header" style="text-align: center;">
                <h3>Edit Klien</h3>
                <p>Perbarui informasi klien</p>
            </div>
            
            <form id="editForm">
                <input type="hidden" id="editId">
                
                <div class="form-group">
                    <label>Nama Klien</label>
                    <input type="text" class="form-control" id="editName" required>
                </div>
                
                <div class="form-group">
                    <label>Client Since</label>
                    <input type="text" class="form-control" id="editSince">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay delete-modal" id="deleteModal">
        <div class="modal-box">
            <div class="delete-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            
            <h3>Hapus Klien?</h3>
            <p>Tindakan ini tidak dapat dibatalkan. Klien akan dihapus permanen dari daftar.</p>
            
            <div class="delete-client-name" id="deleteClientName">
                PT Maju Bersama
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Batal</button>
                <button type="button" class="btn-delete-confirm" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <div class="toast-icon" id="toastIcon">
            <i class="fas fa-check"></i>
        </div>
        <div class="toast-content">
            <h4 id="toastTitle">Berhasil</h4>
            <p id="toastMessage">Klien berhasil ditambahkan</p>
        </div>
    </div>

    <script src="/estu/assets/js/client/main.js"></script>
</body>
</html>