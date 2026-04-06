<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '../../../helper/route.php';
checkAuth();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/portofolio/main.css') ?>">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari portfolio..." id="searchInput">
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

            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Portfolio</h1>
                    <p class="page-subtitle">Kelola portfolio yang ditampilkan di website.</p>
                </div>

                <!-- Live Preview Section -->
                <div class="portfolio-preview-section" id="portfolioPreviewSection">
                    <div class="portfolio-preview-header-inline">
                        <h3><i class="fas fa-eye"></i> Live Preview</h3>
                        <span class="preview-badge" id="portfolioPreviewBadge">0 portfolio</span>
                    </div>
                    <div class="portfolio-preview-grid" id="portfolioPreviewGrid">
                        <!-- Portfolio preview dirender otomatis dari JS -->
                    </div>
                </div>

                <!-- Stats -->
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-icon-box">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-info">
                            <h4 id="totalPortfolio">0</h4>
                            <span>Total Portfolio</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon-box">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h4 id="thisMonthPortfolio">0</h4>
                            <span>Bulan Ini</span>
                        </div>
                    </div>
                </div>

                <!-- Add Button -->
                <div class="add-portfolio-section">
                    <button class="btn-add-portfolio" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Tambah Portfolio Baru
                    </button>
                </div>

                <!-- Portfolio Grid -->
                <div class="portfolio-grid" id="portfolioGrid">
                    <!-- Portfolio items will be rendered here -->
                </div>
            </div>
        </main>
    </div>

    <!-- Add Portfolio Modal -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-box modal-large">
            <div class="modal-header">
                <h3>Tambah Portfolio Baru</h3>
                <p>Upload gambar dan detail portfolio</p>
            </div>
            
            <form id="portfolioForm">
                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Gambar Portfolio *</label>
                        <div class="upload-area" id="uploadArea" onclick="document.getElementById('imageInput').click()">
                            <div class="upload-content" id="uploadContent">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">Klik untuk upload gambar</div>
                                <div class="upload-hint">PNG, JPG (max 5MB)</div>
                            </div>
                            <img src="" alt="Preview" class="preview-image" id="previewImage" style="display: none;">
                        </div>
                        <input type="file" id="imageInput" accept="image/*" style="display: none;" required>
                    </div>
                    
                    <div class="form-group form-group-half">
                        <div class="form-group">
                            <label>Judul Portfolio *</label>
                            <input type="text" class="form-control" id="projectTitle" placeholder="Contoh: Brand Identity PT Maju" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Kategori *</label>
                            <select class="form-control" id="projectCategory" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Branding">Branding</option>
                                <option value="Web Design">Web Design</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Photography">Photography</option>
                                <option value="Videography">Videography</option>
                                <option value="Social Media">Social Media</option>
                                <option value="Exhibition">Exhibition</option>
                                <option value="Event">Event</option>
                                <option value="Production">Production</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Client</label>
                            <input type="text" class="form-control" id="projectClient" placeholder="Nama klien">
                        </div>
                        
                        <div class="form-group">
                            <label>Lokasi</label>
                            <input type="text" class="form-control" id="projectLocation" placeholder="Lokasi Portfolio">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Tahun</label>
                        <input type="number" class="form-control" id="projectYear" placeholder="2024" min="2000" max="2030">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Services / Layanan</label>
                        <input type="text" class="form-control" id="projectServices" placeholder="Contoh: Photography, Videography, Editing">
                    </div>
                    <div class="form-group form-group-half">
                        <label>Jumlah Tamu</label>
                        <input type="text" class="form-control" id="projectGuests" placeholder="Contoh: 100 orang">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tags</label>
                    <input type="text" class="form-control" id="projectTags" placeholder="Contoh: corporate, outdoor, commercial (pisahkan dengan koma)">
                </div>
                
                <div class="form-group">
                    <label>Deskripsi Portfolio</label>
                    <textarea class="form-control" id="projectDescription" rows="3" placeholder="Deskripsi singkat tentang Portfolio ini..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Portfolio</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Portfolio Modal -->
    <div class="modal-overlay edit-modal" id="editModal">
        <div class="modal-box modal-large">
            <div class="portfolio-preview-header" id="editPreview">
                <img id="editPreviewImg" src="" alt="Project Image">
            </div>
                        
            <div class="modal-header" style="text-align: center;">
                <h3>Edit Portfolio</h3>
                <p>Perbarui informasi Portfolio</p>
            </div>
            
            <form id="editForm">
                <input type="hidden" id="editId">
                <input type="hidden" id="editFileId">
                
                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Judul Portfolio *</label>
                        <input type="text" class="form-control" id="editTitle" required>
                    </div>
                    
                    <div class="form-group form-group-half">
                        <label>Kategori *</label>
                        <select class="form-control" id="editCategory" required>
                            <option value="Branding">Branding</option>
                            <option value="Web Design">Web Design</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Photography">Photography</option>
                            <option value="Videography">Videography</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Exhibition">Exhibition</option>
                            <option value="Event">Event</option>
                            <option value="Production">Production</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Client</label>
                        <input type="text" class="form-control" id="editClient">
                    </div>
                    
                    <div class="form-group form-group-half">
                        <label>Lokasi</label>
                        <input type="text" class="form-control" id="editLocation">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Tahun</label>
                        <input type="number" class="form-control" id="editYear" min="2000" max="2030">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-half">
                        <label>Services / Layanan</label>
                        <input type="text" class="form-control" id="editServices" placeholder="Contoh: Photography, Videography, Editing">
                    </div>
                    <div class="form-group form-group-half">
                        <label>Jumlah Tamu</label>
                        <input type="text" class="form-control" id="editGuests" placeholder="Contoh: 100 orang">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tags</label>
                    <input type="text" class="form-control" id="editTags" placeholder="Contoh: corporate, outdoor, commercial">
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" id="editDescription" rows="3"></textarea>
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
            
            <h3>Hapus Portfolio?</h3>
            <p>Tindakan ini tidak dapat dibatalkan. Portfolio akan dihapus permanen dari portfolio.</p>
            
            <div class="delete-project-name" id="deleteProjectName">
                Project Name
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
            <p id="toastMessage">Portfolio berhasil ditambahkan</p>
        </div>
    </div>

    <script src="<?= asset('js/portofolio/main.js') ?>"></script>
</body>
</html>