<meta timestamp="2026-04-08 09:20" />

<?php
$current = $_GET['url'] ?? 'dashboard';
require_once __DIR__ . '../../../helper/route.php';
?>

<link rel="stylesheet" href="<?= asset('css/dashboard/sidebar.css') ?>">
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">ESTU</div>
    </div>

    <nav class="nav-menu">
        <div class="nav-section">
            <div class="nav-label">Menu Utama</div>

            <a href="<?= url('dashboard') ?>" class="nav-item <?= $current == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-dropdown">
                <div class="dropdown-toggle" onclick="toggleDropdown(this)">
                    <i class="fas fa-file-alt"></i>
                    <span>Konten</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </div>

                <div class="dropdown-menu">
                    <a href="<?= url('homeDesc') ?>" class="nav-subitem">Home Description</a>
                    <a href="<?= url('homeImage') ?>" class="nav-subitem">Home Image</a>
                    <a href="<?= url('about') ?>" class="nav-subitem">About</a>
                    <a href="<?= url('service') ?>" class="nav-subitem">Service</a>
                    <a href="<?= url('portofolio') ?>" class="nav-subitem">Portofolio</a>
                    <a href="<?= url('client') ?>" class="nav-subitem">Client</a>
                    <a href="<?= url('contact') ?>" class="nav-subitem">Contact</a>
                </div>
            </div>

            <!-- NEW: Management File Dropdown -->
            <div class="nav-dropdown">
                <div class="dropdown-toggle" onclick="toggleDropdown(this)">
                    <i class="fas fa-folder-open"></i>
                    <span>Management File</span>
                    <span class="badge badge-new">NEW</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </div>

                <div class="dropdown-menu">
                    <a href="<?= url('fileManager') ?>" class="nav-subitem">
                        <i class="fas fa-list"></i> Semua File
                    </a>
                    <a href="<?= url('file-manager?type=word') ?>" class="nav-subitem">
                        <i class="fas fa-file-word"></i> Dokumen Word
                    </a>
                    <a href="<?= url('file-manager?type=excel') ?>" class="nav-subitem">
                        <i class="fas fa-file-excel"></i> Spreadsheet Excel
                    </a>
                    <a href="<?= url('file-manager?type=powerpoint') ?>" class="nav-subitem">
                        <i class="fas fa-file-powerpoint"></i> Presentasi PPT
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= url('file-manager?action=upload') ?>" class="nav-subitem nav-subitem-primary">
                        <i class="fas fa-cloud-upload-alt"></i> Upload File
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card" onclick="toggleUserDropdown(this)">
            <div class="user-avatar">AD</div>
            <div class="user-info">
                <div class="user-name">
                    <?= $_SESSION['user_name'] ?? 'Guest'; ?>
                </div>
                <div class="user-role">Super Admin</div>
            </div>
            <i class="fas fa-chevron-up dropdown-arrow"></i>
        </div>
        
        <!-- Dropdown Logout -->
        <div class="user-dropdown" id="userDropdown">
            <a href="<?= url('profile') ?>" class="user-dropdown-item">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
            <a href="<?= url('settings') ?>" class="user-dropdown-item">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            <div class="user-dropdown-divider"></div>
            <a href="<?= url('logout') ?>" class="user-dropdown-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<script src="<?= asset('js/dashboard/sidebar.js') ?>"></script>