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
    <title>About Section - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/about/main.css') ?>">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari..." id="searchInput">
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
                        </div>
                    </div>
                </div>
            </header>

            <div class="content">
                <!-- Page Header (tanpa tombol simpan) -->
                <div class="page-header">
                    <div class="page-header-left">
                        <h1 class="page-title">About Section</h1>
                        <p class="page-subtitle">Kelola konten bagian About Us pada halaman utama.</p>
                    </div>
                </div>

                <!-- Live Preview -->
                <div class="about-preview-section" id="aboutPreviewSection">
                    <div class="about-preview-header-inline">
                        <h3><i class="fas fa-eye"></i> Live Preview</h3>
                        <span class="preview-badge">Real-time</span>
                    </div>
                    <div class="about-preview-content" id="aboutPreviewContent">
                        <div class="about-preview-grid">
                            <div class="about-preview-image-wrapper">
                                <div class="about-preview-image">
                                    <img src="" alt="About Image" id="previewImage">
                                </div>
                            </div>
                            <div class="about-preview-text">
                                <span class="preview-label" id="previewLabel">About Us</span>
                                <h2 class="preview-title">
                                    <span id="previewTitle1">LOCAL SOUL</span><br>
                                    <span class="text-muted" id="previewTitle2">GLOBAL TOUCH</span>
                                </h2>
                                <div class="preview-line"></div>
                                <p class="preview-paragraph" id="previewParagraph1"></p>
                                <p class="preview-paragraph-secondary" id="previewParagraph2"></p>
                                <div class="preview-stats">
                                    <div class="preview-stat-item">
                                        <div class="preview-stat-number" id="previewStat1">0</div>
                                        <div class="preview-stat-label" id="previewStatLabel1">Events Completed</div>
                                    </div>
                                    <div class="preview-stat-item">
                                        <div class="preview-stat-number" id="previewStat2">0</div>
                                        <div class="preview-stat-label" id="previewStatLabel2">Years Experience</div>
                                    </div>
                                    <div class="preview-stat-item">
                                        <div class="preview-stat-number" id="previewStat3">100%</div>
                                        <div class="preview-stat-label" id="previewStatLabel3">Client Satisfaction</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="form-container">

                    <!-- Image Section -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Gambar About</h3>
                                <p>Upload gambar untuk section About</p>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="image-upload-wrapper" id="imageUploadWrapper" onclick="document.getElementById('aboutImageInput').click()">
                                <div class="upload-area" id="uploadArea">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Klik untuk upload gambar</span>
                                        <small>PNG, JPG, WebP (max 5MB)</small>
                                    </div>
                                    <img src="" alt="Preview" class="upload-preview" id="uploadPreview" style="display: none;">
                                </div>
                                <button type="button" class="btn-remove-image" id="btnRemoveImage" style="display: none;" onclick="event.stopPropagation(); removeImage()">
                                    <i class="fas fa-trash"></i> Hapus Gambar
                                </button>
                            </div>
                            <input type="file" id="aboutImageInput" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Konten Utama</h3>
                                <p>Label, judul dan deskripsi</p>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Section Label</label>
                                    <input type="text" class="form-control" id="sectionLabel" value="About Us" placeholder="Contoh: About Us" oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Judul Baris 1</label>
                                    <input type="text" class="form-control" id="titleLine1" value="LOCAL SOUL" placeholder="Baris pertama judul" oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Judul Baris 2</label>
                                    <input type="text" class="form-control" id="titleLine2" value="GLOBAL TOUCH" placeholder="Baris kedua judul" oninput="updatePreview()">
                                </div>
                            </div>

                           <!-- Paragraf 1 (Utama) -->
                            <div class="form-group mt-4">
                                <label>Paragraf 1 (Utama)</label>
                                <div class="rich-editor-container">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="rte-btn" data-command="bold" title="Bold (Ctrl+B)">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="italic" title="Italic (Ctrl+I)">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="underline" title="Underline (Ctrl+U)">
                                            <i class="fas fa-underline"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="removeFormat" title="Clear Format">
                                            <i class="fas fa-remove-format"></i>
                                        </button>
                                    </div>
                                    <div class="rich-editor" id="paragraph1" contenteditable="true" oninput="updatePreview()">
                                        ESTU is a <strong>local Bali event organizer</strong> dedicated to amplifying the islands cultural richness in contemporary formats. As a professional <strong>EO Bali</strong> company, we don't just provide services—we curate experiences.
                                    </div>
                                </div>
                                <small class="form-hint">Select text lalu klik tombol di atas untuk formatting</small>
                            </div>

                            <!-- Paragraf 2 (Sekunder) -->
                            <div class="form-group mt-4">
                                <label>Paragraf 2 (Sekunder)</label>
                                <div class="rich-editor-container">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="rte-btn" data-command="bold" title="Bold (Ctrl+B)">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="italic" title="Italic (Ctrl+I)">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="underline" title="Underline (Ctrl+U)">
                                            <i class="fas fa-underline"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="removeFormat" title="Clear Format">
                                            <i class="fas fa-remove-format"></i>
                                        </button>
                                    </div>
                                    <div class="rich-editor" id="paragraph2" contenteditable="true" oninput="updatePreview()">
                                        Whether you're planning a destination wedding, corporate retreat, or cultural festival, our team of local experts ensures every detail reflects authentic Balinese hospitality combined with international standards.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Statistik</h3>
                                <p>Kelola 3 statistik yang ditampilkan</p>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="stats-form-row">
                                <!-- Stat 1 -->
                                <div class="stat-form-box">
                                    <div class="stat-form-header">
                                        <span class="stat-badge">1</span>
                                        <h4>Statistik Pertama</h4>
                                    </div>
                                    <div class="form-group">
                                        <label>Angka</label>
                                        <input type="text" class="form-control" id="stat1Number" value="150" oninput="updatePreview()">
                                    </div>
                                    <div class="form-group">
                                        <label>Suffix</label>
                                        <select class="form-control" id="stat1Suffix" onchange="updatePreview()">
                                            <option value="">-</option>
                                            <option value="+" selected>+</option>
                                            <option value="%">%</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Label</label>
                                        <input type="text" class="form-control" id="stat1Label" value="Events Completed" oninput="updatePreview()">
                                    </div>
                                </div>

                                <!-- Stat 2 -->
                                <div class="stat-form-box">
                                    <div class="stat-form-header">
                                        <span class="stat-badge">2</span>
                                        <h4>Statistik Kedua</h4>
                                    </div>
                                    <div class="form-group">
                                        <label>Angka</label>
                                        <input type="text" class="form-control" id="stat2Number" value="8" oninput="updatePreview()">
                                    </div>
                                    <div class="form-group">
                                        <label>Suffix</label>
                                        <select class="form-control" id="stat2Suffix" onchange="updatePreview()">
                                            <option value="" selected>-</option>
                                            <option value="+">+</option>
                                            <option value="%">%</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Label</label>
                                        <input type="text" class="form-control" id="stat2Label" value="Years Experience" oninput="updatePreview()">
                                    </div>
                                </div>

                                <!-- Stat 3 -->
                                <div class="stat-form-box">
                                    <div class="stat-form-header">
                                        <span class="stat-badge">3</span>
                                        <h4>Statistik Ketiga</h4>
                                    </div>
                                    <div class="form-group">
                                        <label>Angka</label>
                                        <input type="text" class="form-control" id="stat3Number" value="100" oninput="updatePreview()">
                                    </div>
                                    <div class="form-group">
                                        <label>Suffix</label>
                                        <select class="form-control" id="stat3Suffix" onchange="updatePreview()">
                                            <option value="">-</option>
                                            <option value="+">+</option>
                                            <option value="%" selected>%</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Label</label>
                                        <input type="text" class="form-control" id="stat3Label" value="Client Satisfaction" oninput="updatePreview()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Simpan Perubahan - DI BAWAH -->
                <div class="save-section">
                    <button class="btn-save-bottom" onclick="saveAboutData()">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </div>
        </main>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast">
        <div class="toast-icon" id="toastIcon">
            <i class="fas fa-check"></i>
        </div>
        <div class="toast-content">
            <h4 id="toastTitle">Berhasil</h4>
            <p id="toastMessage">Data berhasil disimpan</p>
        </div>
    </div>
    
    <script>
        const BASE_URL = "<?= base_url() ?>";
    </script>
    <script src="<?= asset('js/about/main.js') ?>"></script>
</body>
</html>