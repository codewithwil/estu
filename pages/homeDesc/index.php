<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Description - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/estu/assets/css/toast.css">
    <link rel="stylesheet" href="/estu/assets/css/homeDesc/main.css">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <!-- Main Content -->
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
                    <h1 class="page-title">Home Description</h1>
                    <p class="page-subtitle">Kelola konten hero section dan deskripsi utama website.</p>
                </div>

                <!-- Preview Card -->
                <div class="preview-section">
                    <div class="section-header">
                        <h3><i class="fas fa-eye"></i> Live Preview</h3>
                        <span class="preview-badge">Hero Section</span>
                    </div>
                    <div class="hero-preview" id="heroPreview">
                        <div class="preview-content">
                            <p class="preview-label" id="previewLabel">Event Organizer Bali</p>
                            <h1 class="preview-title" id="previewTitle">ESTU</h1>
                            <div class="preview-subtitle">
                                <span class="preview-bold" id="previewBold">Designing Experiences, Not Just Events</span>
                                <span class="preview-light" id="previewLight">We turn ideas into memorable events through creative concepts and flawless execution.</span>
                            </div>
                            <a href="#" class="preview-btn">Explore <i class="fas fa-arrow-down"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="edit-section">
                    <div class="section-header">
                        <h3><i class="fas fa-edit"></i> Edit Konten</h3>
                    </div>
                    
                    <form id="homeForm" class="home-form">
                        <div class="form-grid">
                            <!-- Label Atas -->
                            <div class="form-group full-width">
                                <label>Label Atas (Small Text)</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" class="form-control" id="topLabel" value="Event Organizer Bali" maxlength="50">
                                </div>
                                <span class="char-count"><span id="labelCount">20</span>/50</span>
                            </div>

                            <!-- Main Title -->
                            <div class="form-group">
                                <label>Judul Utama</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-heading"></i>
                                    <input type="text" class="form-control" id="mainTitle" value="ESTU" maxlength="20">
                                </div>
                                <span class="char-count"><span id="titleCount">4</span>/20</span>
                            </div>

                            <!-- CTA Button Text -->
                            <div class="form-group">
                                <label>Teks Tombol CTA</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-mouse-pointer"></i>
                                    <input type="text" class="form-control" id="ctaText" value="Explore" maxlength="20">
                                </div>
                                <span class="char-count"><span id="ctaCount">7</span>/20</span>
                            </div>

                            <!-- Bold Subtitle -->
                            <div class="form-group full-width">
                                <label>Subjudul Bold</label>
                                <div class="input-wrapper textarea">
                                    <i class="fas fa-bold"></i>
                                    <textarea class="form-control" id="boldSubtitle" rows="2" maxlength="100">Designing Experiences, Not Just Events</textarea>
                                </div>
                                <span class="char-count"><span id="boldCount">42</span>/100</span>
                            </div>

                            <!-- Light Subtitle -->
                            <div class="form-group full-width">
                                <label>Subjudul Deskripsi</label>
                                <div class="input-wrapper textarea">
                                    <i class="fas fa-align-left"></i>
                                    <textarea class="form-control" id="lightSubtitle" rows="3" maxlength="200">We turn ideas into memorable events through creative concepts and flawless execution.</textarea>
                                </div>
                                <span class="char-count"><span id="lightCount">86</span>/200</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-icon" id="toastIcon">
            <i class="fas fa-check"></i>
        </div>
        <div class="toast-content">
            <h4 id="toastTitle">Berhasil</h4>
            <p id="toastMessage">Perubahan berhasil disimpan</p>
        </div>
    </div>

    <script src="/estu/assets/js/homeDesc/main.js"></script>
</body>
</html>