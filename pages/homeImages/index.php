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
    <title>Home Images - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/homeImages/main.css') ?>">
</head>
<body>
    <div class="dashboard">
         <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari gambar..." id="searchInput">
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
                    <h1 class="page-title">Home Images</h1>
                    <p class="page-subtitle">Kelola gambar yang ditampilkan di halaman utama. Maksimal 10 gambar. Urutan pertama akan tampil paling awal di slider.</p>
                </div>

                <div class="hero-preview-section" id="heroPreviewSection">
                    <div class="hero-preview-header-inline">
                        <h3><i class="fas fa-eye"></i> Live Preview Hero Slider</h3>
                        <span class="preview-badge" id="previewBadge">0 gambar</span>
                    </div>
                    <div class="hero-slider-live" id="heroSliderLive"></div>
                </div>

                <div class="image-counter">
                    <div class="counter-info">
                        <div class="counter-badge" id="imageCounter">0 / 10</div>
                        <span class="counter-text">gambar di home slider</span>
                    </div>
                    <button class="btn-primary" id="addImageBtn" onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-plus"></i> Tambah Gambar
                    </button>
                    <input type="file" id="fileInput" accept="image/*" style="display: none;" multiple>
                </div>

                <div class="images-wrapper" id="imagesContainer"></div>

                <div class="upload-section" id="uploadSection" style="display: none;">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Drag & drop gambar di sini</div>
                    <div class="upload-hint">atau klik tombol di atas untuk memilih file</div>
                    <button class="btn-upload" onclick="document.getElementById('fileInput').click()">
                        Pilih File
                    </button>
                </div>
            </div>
        </main>
    </div>

    <div class="modal-overlay" id="previewModal">
        <button class="modal-close" id="modalClose">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-content">
            <img src="" alt="Preview" id="previewImage">
            <div class="modal-info">
                <h3 id="previewTitle">Image Title</h3>
                <p id="previewMeta">1920x1080 • 245 KB</p>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <div class="toast-icon" id="toastIcon">
            <i class="fas fa-check"></i>
        </div>
        <div class="toast-content">
            <h4 id="toastTitle">Berhasil</h4>
            <p id="toastMessage">Success</p>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content" style="max-width: 400px; background: #171717; padding: 24px;">
            <h3 style="margin-bottom: 10px;">Hapus Gambar</h3>
            <p id="deleteText" style="color:#a3a3a3; font-size:14px;">
                Yakin mau hapus?
            </p>

            <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button onclick="closeDeleteModal()" class="btn-upload">Batal</button>
                <button onclick="confirmDelete()" class="btn-primary" style="background:#dc2626; color:white;">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/homeImages/main.js') ?>"></script>
</body>
</html>