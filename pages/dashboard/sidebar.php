<?php
$current = $_GET['url'] ?? 'dashboard';
?>

<link rel="stylesheet" href="/estu/assets/css/dashboard/sidebar.css">
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">ESTU</div>
    </div>

    <nav class="nav-menu">
        <div class="nav-section">
            <div class="nav-label">Menu Utama</div>

            <a href="/estu/dashboard" class="nav-item <?= $current == 'dashboard' ? 'active' : '' ?>">
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
                    <a href="/estu/homeDesc" class="nav-subitem">Home Description</a>
                    <a href="/estu/homeImage" class="nav-subitem">Home Image</a>
                    <a href="/estu/about" class="nav-subitem">About</a>
                    <a href="/estu/service" class="nav-subitem">Service</a>
                    <a href="/estu/client" class="nav-subitem">Client</a>
                    <a href="#" class="nav-subitem">Tambah Konten</a>
                    <a href="#" class="nav-subitem">Kategori</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">AD</div>
            <div class="user-info">
                <div class="user-name">
                    <?= $_SESSION['user_name'] ?? 'Guest'; ?>
                </div>
                <div class="user-role">Super Admin</div>
            </div>
        </div>
    </div>
</aside>

<script src="/estu/assets/js/dashboard/sidebar.js"></script>