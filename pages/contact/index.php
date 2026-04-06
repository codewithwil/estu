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
    <title>Contact Section - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/contact/main.css') ?>">
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
                            <div class="notification-footer">
                                <a href="#" class="view-all">Lihat Semua Notifikasi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Contact Section</h1>
                    <p class="page-subtitle">Kelola konten bagian Contact pada halaman utama.</p>
                </div>

                <!-- Live Preview -->
                <div class="contact-preview-section" id="contactPreviewSection">
                    <div class="contact-preview-header-inline">
                        <h3><i class="fas fa-eye"></i> Live Preview</h3>
                        <span class="preview-badge">Real-time</span>
                    </div>
                    <div class="contact-preview-content">
                        <!-- Header Preview -->
                        <div class="contact-preview-header">
                            <h2 class="preview-section-title" id="previewSectionTitle">LET'S COLLABORATE</h2>
                            <p class="preview-section-desc" id="previewSectionDesc">Have an event idea? Contact our local Bali EO team directly via WhatsApp for quick response.</p>
                        </div>
                        
                        <!-- Contact Grid Preview -->
                        <div class="contact-preview-grid">
                            <div class="contact-preview-info">
                                <div class="contact-preview-item">
                                    <span class="contact-preview-label">WhatsApp</span>
                                    <span class="contact-preview-value text-lg" id="previewWhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                        <span id="previewWhatsAppNumber">+62 815 7200 0039</span>
                                    </span>
                                    <p class="contact-preview-note" id="previewWhatsAppNote">Click to chat directly • Response in 1-2 hours</p>
                                </div>

                                <div class="contact-preview-item">
                                    <span class="contact-preview-label">Email</span>
                                    <span class="contact-preview-value" id="previewEmail">estu.office.bali@gmail.com</span>
                                </div>

                                <div class="contact-preview-item">
                                    <span class="contact-preview-label">Operating Hours</span>
                                    <span class="contact-preview-value" id="previewHours">
                                        Monday - Saturday<br>09:00 - 17:00 WITA (Bali Time)
                                    </span>
                                </div>

                                <div class="contact-preview-item">
                                    <span class="contact-preview-label">Location</span>
                                    <span class="contact-preview-value" id="previewLocation">
                                        Jalan Raya Padang Luwih, Dalung, North Kuta,<br>
                                        Badung Regency, Bali 80117, Indonesia
                                    </span>
                                </div>
                            </div>

                            <div class="contact-preview-why">
                                <h3>Why Choose ESTU?</h3>
                                <ul class="why-preview-list" id="previewWhyList">
                                    <li><i class="fas fa-check"></i> Local Bali EO with 8+ years experience</li>
                                    <li><i class="fas fa-check"></i> Response within 1-2 business hours</li>
                                    <li><i class="fas fa-check"></i> Free initial consultation</li>
                                    <li><i class="fas fa-check"></i> Custom packages for every budget</li>
                                    <li><i class="fas fa-check"></i> Site visit available across Bali</li>
                                </ul>
                                
                                <div class="why-preview-quote">
                                    <p id="previewWhyQuote">"We believe fast communication is the key to successful events. Chat with our EO Bali team anytime!"</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Preview -->
                        <div class="contact-preview-footer">
                            <div class="preview-footer-brand">
                                <span class="logo">ESTU</span>
                                <span class="tagline">Event Organizer Bali</span>
                            </div>
                            <div class="preview-footer-copy">&copy; 2024 ESTU. All rights reserved.</div>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="form-container">
                    
                    <!-- Section Header -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-heading"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Header Section</h3>
                                <p>Label, judul dan deskripsi section</p>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Judul</label>
                                    <input type="text" class="form-control" id="titleLine"  placeholder="Judul section" oninput="updatePreview()">
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label>Deskripsi</label>
                                <div class="rich-editor-container">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="rte-btn" data-command="bold" title="Bold (Ctrl+B)">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="italic" title="Italic (Ctrl+I)">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="removeFormat" title="Clear Format">
                                            <i class="fas fa-remove-format"></i>
                                        </button>
                                    </div>
                                    <div class="rich-editor" id="sectionDesc" contenteditable="true" oninput="updatePreview()">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-address-card"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Informasi Kontak</h3>
                                <p>WhatsApp, Email, Jam Operasional & Lokasi</p>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nomor WhatsApp</label>
                                    <input type="text" class="form-control" id="whatsAppNumber" oninput="updatePreview()">
                                    <small class="form-hint">Format: +62 xxx xxxx xxxx</small>
                                </div>
                                <div class="form-group">
                                    <label>WhatsApp Note</label>
                                    <input type="text" class="form-control" id="whatsAppNote" oninput="updatePreview()">
                                </div>
                            </div>

                            <div class="form-row mt-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" id="email" oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Jam Operasional</label>
                                    <div class="rich-editor-container">
                                        <div class="rich-editor-toolbar">
                                            <button type="button" class="rte-btn" data-command="bold" title="Bold">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="rte-btn" data-command="italic" title="Italic">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                        </div>
                                        <div class="rich-editor" id="operatingHours" contenteditable="true" oninput="updatePreview()">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label>Lokasi</label>
                                <div class="rich-editor-container">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="rte-btn" data-command="bold" title="Bold">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="italic" title="Italic">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                    </div>
                                    <div class="rich-editor" id="location" contenteditable="true" oninput="updatePreview()">
                                      
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <label>Text Tombol WhatsApp</label>
                                <input type="text" class="form-control" id="whatsAppButtonText" oninput="updatePreview()">
                            </div>
                        </div>
                    </div>

                    <!-- Why Choose Us - DENGAN TAMBAH/HAPUS -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Why Choose Us</h3>
                                <p>Kelola poin keunggulan (bisa tambah, edit, hapus)</p>
                            </div>
                            <button type="button" class="btn-add-why" onclick="addNewWhyItem()">
                                <i class="fas fa-plus"></i> Tambah Poin
                            </button>
                        </div>
                        <div class="form-card-body">
                            <div class="why-form-list" id="whyFormList">
                                <!-- Generated by JS -->
                            </div>
                            
                            <div class="form-group mt-4">
                                <label>Quote Footer</label>
                                <div class="rich-editor-container">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="rte-btn" data-command="bold" title="Bold">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" class="rte-btn" data-command="italic" title="Italic">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                    </div>
                                    <div class="rich-editor" id="whyQuote" contenteditable="true" oninput="updatePreview()">
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Settings -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="form-card-icon">
                                <i class="fas fa-copyright"></i>
                            </div>
                            <div class="form-card-title">
                                <h3>Footer</h3>
                                <p>Pengaturan footer section</p>
                            </div>
                        </div>
                        <div class="form-card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Brand Name</label>
                                    <input type="text" class="form-control" id="brandName" oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Tagline</label>
                                    <input type="text" class="form-control" id="brandTagline" oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Copyright Text</label>
                                    <input type="text" class="form-control" id="copyrightText" oninput="updatePreview()">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Simpan -->
                <div class="save-section">
                    <button class="btn-save-bottom" onclick="saveContactData()">
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
    <script src="<?= asset('js/contact/main.js') ?>"></script>
</body>
</html>