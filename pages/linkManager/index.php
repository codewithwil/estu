<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '../../../helper/route.php';
require_once __DIR__ . '/../../functions/linkManager.php';
checkAuth();

$current = 'linkManager';
$action = $_GET['action'] ?? 'list';
$categories = getLinkCategories();
$links = getAllLinks();
$currentCategory = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

if ($currentCategory !== 'all') {
    $links = array_filter($links, fn($l) => $l['category_id'] == $currentCategory);
}
if ($search) {
    $links = array_filter($links, fn($l) => 
        stripos($l['title'], $search) !== false || 
        stripos($l['url'], $search) !== false
    );
}

$viewMode = $_COOKIE['linkManagerView'] ?? 'grid';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Link - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/linkManager/main.css') ?>">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari link..." id="searchInput" value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="header-actions">
                    <button class="icon-btn" id="notificationBtn" title="Notifikasi">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot" id="notificationDot" style="display: none;"></span>
                    </button>
                </div>
            </header>

            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Manajemen Link</h1>
                    <p class="page-subtitle">Kelola dan organisir link eksternal Anda</p>
                </div>

                <div class="fm-top-bar">
                    <!-- Category Filter sebagai Breadcrumb-style -->
                    <nav class="fm-breadcrumb" id="categoryBreadcrumb">
                        <a href="?category=all" class="<?= $currentCategory == 'all' ? 'active' : '' ?>">
                            <i class="fas fa-layer-group"></i> Semua
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <i class="fas fa-chevron-right separator"></i>
                            <a href="?category=<?= $cat['id'] ?>" class="<?= $currentCategory == $cat['id'] ? 'active' : '' ?>">
                                <i class="fas <?= $cat['icon'] ?? 'fa-tag' ?>"></i> <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <div class="fm-actions" id="mainActions">
                        <div class="view-toggle">
                            <button class="<?= $viewMode==='list'?'active':'' ?>" onclick="setViewMode('list')" title="List View">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="<?= $viewMode==='grid'?'active':'' ?>" onclick="setViewMode('grid')" title="Grid View">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>

                        <div class="dropdown">
                            <button class="btn-primary" onclick="toggleDropdown('newMenu')">
                                <i class="fas fa-plus"></i> Baru
                            </button>
                            <div class="dropdown-menu" id="newMenu">
                                <button type="button" onclick="openAddLinkModal()" class="dropdown-item">
                                    <i class="fas fa-link" style="color: var(--blue);"></i> 
                                    <span>Tambah Link</span>
                                </button>
                                <div class="dropdown-divider"></div>
                                <button type="button" onclick="openCategoryModal()" class="dropdown-item">
                                    <i class="fas fa-folder" style="color: #eab308;"></i> 
                                    <span>Kategori Baru</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fm-container" id="linkManagerContainer">
                    <div class="fm-header">
                        <div class="fm-header-info">
                            <div class="fm-header-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="fm-header-text">
                                <h3><?= $currentCategory == 'all' ? 'Semua Link' : htmlspecialchars(getCategoryName($currentCategory, $categories)) ?></h3>
                                <p><?= count($links) ?> link tersimpan</p>
                            </div>
                        </div>
                    </div>

                    <div class="fm-body" id="dropZone">
                        <?php if (empty($links)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <h3>Belum ada link</h3>
                                <p>Tambahkan link eksternal untuk mengorganisir resource Anda</p>
                                <div style="display: flex; gap: 12px;">
                                    <button class="btn-primary" onclick="openAddLinkModal()">
                                        <i class="fas fa-plus"></i> Tambah Link
                                    </button>
                                    <button class="btn-secondary" onclick="openCategoryModal()">
                                        <i class="fas fa-folder"></i> Kategori Baru
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>

                            <div class="fm-section">
                                <div class="fm-section-header">
                                    <span class="fm-section-title">Link</span>
                                    <span class="fm-section-count"><?= count($links) ?></span>
                                </div>
                                <div class="items-<?= $viewMode ?>">
                                    <?php foreach ($links as $link): 
                                        $favicon = getFavicon($link['url']);
                                        $domain = parse_url($link['url'], PHP_URL_HOST) ?? $link['url'];
                                        $catColor = $link['category_color'] ?? '#6366f1';
                                    ?>
                                    <div class="item link-item" 
                                        data-id="<?= $link['id'] ?>" 
                                        data-type="link"
                                        data-category="<?= $link['category_id'] ?>">

                                        <div class="item-icon link-icon">
                                            <?php if ($favicon): ?>
                                                <img src="<?= $favicon ?>" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <i class="fas fa-link" style="display: none;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-link"></i>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($viewMode === 'list'): ?>
                                        <div class="item-info">
                                            <span class="item-name"><?= htmlspecialchars($link['title']) ?></span>
                                            <span class="item-meta">
                                                <i class="fas fa-globe"></i> <?= htmlspecialchars($domain) ?>
                                                <span class="link-badge" style="background: <?= $catColor ?>20; color: <?= $catColor ?>">
                                                    <?= htmlspecialchars($link['category_name'] ?? 'Umum') ?>
                                                </span>
                                            </span>
                                            <span class="item-date">
                                                <?= date('d M Y', strtotime($link['created_at'])) ?>
                                                <?php if ($link['click_count'] > 0): ?>
                                                    <span class="click-count"><i class="fas fa-mouse-pointer"></i> <?= $link['click_count'] ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="item-actions-list">
                                            <a href="<?= $link['url'] ?>" target="_blank" class="btn-icon-only" title="Buka Link" onclick="trackClick(<?= $link['id'] ?>)">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button class="btn-icon-only" onclick="copyLink('<?= $link['url'] ?>')" title="Copy URL">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="btn-icon-only" onclick="editLink(<?= $link['id'] ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                        <?php else: ?>
                                        <div class="item-name" title="<?= htmlspecialchars($link['title']) ?>">
                                            <?= htmlspecialchars($link['title']) ?>
                                        </div>
                                        <div class="item-meta">
                                            <i class="fas fa-globe"></i> <?= htmlspecialchars($domain) ?>
                                        </div>
                                        <div class="link-badge" style="background: <?= $catColor ?>20; color: <?= $catColor ?>">
                                            <?= htmlspecialchars($link['category_name'] ?? 'Umum') ?>
                                        </div>
                                        <div class="item-footer">
                                            <span class="item-date"><?= date('d M Y', strtotime($link['created_at'])) ?></span>
                                            <div class="item-actions-grid">
                                                <a href="<?= $link['url'] ?>" target="_blank" class="btn-icon-only" title="Buka" onclick="trackClick(<?= $link['id'] ?>)">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <button class="btn-icon-only" onclick="copyLink('<?= $link['url'] ?>')" title="Copy">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Context Menu -->
    <div class="context-menu" id="contextMenu">
        <div class="context-item open" onclick="contextAction('open')"><i class="fas fa-external-link-alt"></i> Buka Link</div>
        <div class="context-item copy" onclick="contextAction('copy')"><i class="fas fa-copy"></i> Copy URL</div>
        <div class="context-divider"></div>
        <div class="context-item rename" onclick="contextAction('edit')"><i class="fas fa-edit"></i> Edit</div>
        <div class="context-item delete" onclick="contextAction('delete')"><i class="fas fa-trash"></i> Hapus</div>
    </div>

    <!-- Add/Edit Link Modal -->
    <div class="modal-overlay" id="linkModal">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h3 id="linkModalTitle"><i class="fas fa-plus-circle" style="margin-right: 8px; color: var(--blue);"></i>Tambah Link Baru</h3>
                <button class="btn-close" onclick="closeModal('linkModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="linkId">
                <div class="form-group">
                    <label>Judul Link</label>
                    <input type="text" id="linkTitle" class="form-control" placeholder="Contoh: Dokumentasi API" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <div class="input-group">
                        <span class="input-prefix"><i class="fas fa-link"></i></span>
                        <input type="url" id="linkUrl" class="form-control" placeholder="https://example.com" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select id="linkCategory" class="form-control">
                        <option value="">Pilih Kategori...</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Deskripsi (Opsional)</label>
                    <textarea id="linkDescription" class="form-control" rows="3" placeholder="Deskripsi singkat tentang link ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('linkModal')">Batal</button>
                <button class="btn-primary" onclick="saveLink()"><i class="fas fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal-overlay" id="categoryModal">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h3><i class="fas fa-folder-plus" style="margin-right: 8px; color: #eab308;"></i>Kategori Baru</h3>
                <button class="btn-close" onclick="closeModal('categoryModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" id="categoryName" class="form-control" placeholder="Masukkan nama kategori..." autocomplete="off">
                </div>
                
                <!-- Icon Picker -->
                <div class="form-group">
                    <label>Icon</label>
                    <div class="icon-picker-trigger" onclick="openIconPicker()">
                        <div class="icon-picker-preview" id="categoryIconPreview">
                            <i class="fas fa-tag"></i>
                        </div>
                        <span class="icon-picker-text" id="categoryIconText">fa-tag</span>
                        <i class="fas fa-chevron-down icon-picker-arrow"></i>
                    </div>
                    <input type="hidden" id="categoryIcon" value="fa-tag">
                </div>
                
                <!-- Icon Picker Dropdown -->
                <div class="icon-picker-dropdown" id="iconPickerDropdown">
                    <div class="icon-picker-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="iconSearch" placeholder="Cari icon..." oninput="filterIcons(this.value)">
                    </div>
                    <div class="icon-picker-grid" id="iconPickerGrid">
                        <!-- Icons rendered by JS -->
                    </div>
                </div>

                <div class="form-group">
                    <label>Warna</label>
                    <div class="color-picker-row">
                        <?php 
                        $presetColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'];
                        foreach ($presetColors as $color): 
                        ?>
                        <button type="button" class="color-swatch <?= $color === '#6366f1' ? 'active' : '' ?>" 
                            style="background: <?= $color ?>" 
                            data-color="<?= $color ?>"
                            onclick="selectColor(this, '<?= $color ?>')"></button>
                        <?php endforeach; ?>
                        <input type="color" id="categoryColor" class="color-custom" value="#6366f1" onchange="updateCustomColor(this.value)">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('categoryModal')">Batal</button>
                <button class="btn-primary" onclick="saveCategory()"><i class="fas fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal modal-confirm">
            <div class="modal-body">
                <div class="modal-confirm-icon danger"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>Hapus Link?</h3>
                <p>Link yang dihapus tidak dapat dikembalikan.</p>
                <div class="modal-confirm-buttons">
                    <button class="btn-cancel" onclick="closeModal('confirmModal')">Batal</button>
                    <button class="btn-confirm danger" onclick="executeDelete()"><i class="fas fa-trash"></i> Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const BASE_URL = "<?= base_url() ?>";
        const PROCESS_URL = "<?= url('process/linkManager.php') ?>";
    </script>
    <script src="<?= asset('js/linkManager/main.js') ?>"></script>
</body>
</html>

<?php
function getFavicon($url) {
    $domain = parse_url($url, PHP_URL_HOST);
    return $domain ? "https://www.google.com/s2/favicons?domain=$domain&sz=64" : null;
}

function getCategoryName($id, $categories) {
    foreach ($categories as $c) if ($c['id'] == $id) return $c['name'];
    return 'Unknown';
}
?>