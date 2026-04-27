<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '../../../helper/route.php';
require_once __DIR__ . '/../../functions/fileManager.php';
checkAuth();

$currentFolderId = $_GET['folder'] ?? 1;
$currentFolder = getFolderById($currentFolderId);
$breadcrumbs = getBreadcrumbs($currentFolderId);
$subfolders = getSubfolders($currentFolderId);
$files = getFilesByFolder($currentFolderId, $_GET['type'] ?? 'all');
$viewMode = $_COOKIE['fileManagerView'] ?? 'grid';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/fileManager/main.css') ?>">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari file..." id="searchInput">
                </div>

                <div class="header-actions">
                    <button class="icon-btn" id="notificationBtn" title="Notifikasi">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot" id="notificationDot" style="display: none;"></span>
                    </button>

                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notifikasi</h4>
                            <button class="mark-all-read" id="markAllRead">Tandai dibaca</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div style="padding: 40px; text-align: center; color: var(--gray-500);">
                                <i class="fas fa-bell-slash" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                                <p>Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">File Manager</h1>
                    <p class="page-subtitle">Kelola dan organisir file Anda dengan mudah</p>
                </div>

                <div class="fm-top-bar">
                    <nav class="fm-breadcrumb" id="mainBreadcrumb">
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <?php if ($i > 0): ?><i class="fas fa-chevron-right separator"></i><?php endif; ?>
                            <a href="?folder=<?= $crumb['id'] ?>" class="<?= $i === count($breadcrumbs)-1 ? 'active' : '' ?>">
                                <?= $i === 0 ? '<i class="fas fa-home"></i>' : htmlspecialchars($crumb['name']) ?>
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
                            <button class="btn-primary" onclick="toggleFileDropdown('newMenu')">
                                <i class="fas fa-plus"></i> Baru
                            </button>
                            <div class="dropdown-menu" id="newMenu">
                                <button type="button" onclick="createFolder()" class="dropdown-item">
                                    <i class="fas fa-folder" style="color: #eab308;"></i> 
                                    <span>Folder Baru</span>
                                </button>
                                <div class="dropdown-divider"></div>
                                <button type="button" onclick="triggerUpload()" class="dropdown-item">
                                    <i class="fas fa-cloud-upload-alt" style="color: var(--blue);"></i> 
                                    <span>Upload File</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fm-container" id="fileManagerContainer">
                    <div class="fm-header">
                        <div class="fm-header-info">
                            <div class="fm-header-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div class="fm-header-text">
                                <h3><?= htmlspecialchars($currentFolder['name'] ?? 'My Files') ?></h3>
                                <p><?= count($subfolders) ?> folder, <?= count($files) ?> file</p>
                            </div>
                        </div>
                    </div>

                    <div class="fm-body" id="dropZone">
                        <?php if (empty($subfolders) && empty($files)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h3>Folder kosong</h3>
                                <p>Mulai dengan mengupload file atau membuat folder baru</p>
                                <div style="display: flex; gap: 12px;">
                                    <button class="btn-primary" onclick="triggerUpload()">
                                        <i class="fas fa-cloud-upload-alt"></i> Upload File
                                    </button>
                                    <button class="btn-secondary" onclick="createFolder()">
                                        <i class="fas fa-folder"></i> Buat Folder
                                    </button>
                                </div>
                            </div>

                        <?php else: ?>

                            <?php if (!empty($subfolders)): ?>
                                <div class="fm-section">
                                    <div class="fm-section-header">
                                        <span class="fm-section-title">Folder</span>
                                        <span class="fm-section-count"><?= count($subfolders) ?></span>
                                    </div>
                                    <div class="items-<?= $viewMode ?>">
                                        <?php foreach ($subfolders as $folder): ?>
                                        <div class="item folder-item" 
                                            data-id="<?= $folder['id'] ?>" 
                                            data-type="folder"
                                            draggable="true"
                                            data-folder-id="<?= $folder['id'] ?>">

                                            <div class="item-icon folder-icon">
                                                <i class="fas fa-folder"></i>
                                            </div>

                                            <?php if ($viewMode === 'list'): ?>
                                            <div class="item-info">
                                                <span class="item-name"><?= htmlspecialchars($folder['name']) ?></span>
                                                <span class="item-meta"><?= ($folder['file_count'] + $folder['subfolder_count']) ?> items</span>
                                                <span class="item-date"><?= date('d M Y', strtotime($folder['updated_at'])) ?></span>
                                            </div>
                                            <?php else: ?>
                                            <div class="item-name" title="<?= htmlspecialchars($folder['name']) ?>">
                                                <?= htmlspecialchars($folder['name']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($files)): ?>
                                <div class="fm-section">
                                    <div class="fm-section-header">
                                        <span class="fm-section-title">File</span>
                                        <span class="fm-section-count"><?= count($files) ?></span>
                                    </div>
                                    <div class="items-<?= $viewMode ?>">
                                        <?php foreach ($files as $file): ?>
                                        <div class="item file-item" 
                                            data-id="<?= $file['id'] ?>" 
                                            data-type="file"
                                            data-filetype="<?= $file['type'] ?>"
                                            draggable="true"
                                            data-file-id="<?= $file['id'] ?>"
                                            data-filename="<?= htmlspecialchars($file['filename']) ?>"
                                            data-extension="<?= $file['extension'] ?>">

                                            <div class="item-icon file-icon <?= $file['type'] ?>">
                                                <i class="fas <?= getFileIcon($file['type']) ?>"></i>
                                            </div>

                                            <?php if ($viewMode === 'list'): ?>
                                            <div class="item-info">
                                                <span class="item-name"><?= htmlspecialchars($file['filename']) ?></span>
                                                <span class="item-meta"><?= strtoupper($file['extension']) ?> • <?= $file['size_formatted'] ?></span>
                                                <span class="item-date"><?= date('d M Y', strtotime($file['updated_at'])) ?></span>
                                            </div>
                                            <?php else: ?>
                                            <div class="item-name" title="<?= htmlspecialchars($file['filename']) ?>">
                                                <?= htmlspecialchars($file['filename']) ?>
                                            </div>
                                            <div class="item-meta"><?= $file['size_formatted'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Context Menu -->
    <div class="context-menu" id="contextMenu">
        <div class="context-item download" onclick="contextAction('download')"><i class="fas fa-download" style="color: var(--green);"></i> Download</div>
        <div class="context-item rename" onclick="contextAction('rename')"><i class="fas fa-edit" style="color: var(--amber);"></i> Rename</div>
        <div class="context-item move" onclick="contextAction('move')"><i class="fas fa-folder-open" style="color: var(--purple);"></i> Pindahkan</div>
        <div class="context-divider"></div>
        <div class="context-item delete" onclick="contextAction('delete')"><i class="fas fa-trash" style="color: var(--red);"></i> Hapus</div>
    </div>

    <div class="selection-bar" id="selectionBar">
        <div class="selection-info"><span id="selectionCount">0</span> item dipilih</div>
        <div class="selection-actions">
            <button class="btn-icon-only" onclick="downloadSelected()" title="Download"><i class="fas fa-download"></i></button>
            <button class="btn-icon-only" onclick="moveSelected()" title="Pindahkan"><i class="fas fa-folder-open"></i></button>
            <button class="btn-icon-only danger" onclick="deleteSelected()" title="Hapus"><i class="fas fa-trash"></i></button>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-overlay" id="folderModal">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h3><i class="fas fa-folder-plus" style="margin-right: 8px; color: var(--blue);"></i>Buat Folder Baru</h3>
                <button class="btn-close" onclick="closeModal('folderModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Folder</label>
                    <input type="text" id="folderName" class="form-control" placeholder="Masukkan nama folder..." autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('folderModal')">Batal</button>
                <button class="btn-primary" onclick="saveFolder()"><i class="fas fa-check"></i> Buat Folder</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="uploadModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-cloud-upload-alt" style="margin-right: 8px; color: var(--blue);"></i>Upload File</h3>
                <button class="btn-close" onclick="closeModal('uploadModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-zone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <h4>Drag & Drop File Disini</h4>
                    <p>Atau klik tombol di bawah untuk memilih file</p>
                    <button class="btn-secondary" onclick="document.getElementById('fileInput').click()"><i class="fas fa-folder-open"></i> Pilih File</button>
                    <input type="file" id="fileInput" multiple hidden>
                </div>
                <div class="upload-list" id="uploadList"></div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirmModal">
        <div class="modal modal-confirm">
            <div class="modal-body">
                <div class="modal-confirm-icon" id="confirmIcon"><i class="fas fa-exclamation-triangle"></i></div>
                <h3 id="confirmTitle">Konfirmasi</h3>
                <p id="confirmMessage">Apakah Anda yakin?</p>
                <div class="modal-confirm-buttons">
                    <button class="btn-cancel" onclick="closeModal('confirmModal')">Batal</button>
                    <button class="btn-confirm" id="confirmBtn" onclick="executeConfirm()">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="promptModal">
        <div class="modal modal-prompt" style="max-width: 420px;">
            <div class="modal-header">
                <h3 id="promptTitle">Rename</h3>
                <button class="btn-close" onclick="closeModal('promptModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label id="promptLabel">Nama baru</label>
                    <input type="text" id="promptInput" autocomplete="off">
                </div>
                <div class="modal-confirm-buttons" style="justify-content: flex-end; margin-top: 8px;">
                    <button class="btn-cancel" onclick="closeModal('promptModal')">Batal</button>
                    <button class="btn-confirm" onclick="executePrompt()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="moveModal">
        <div class="modal" style="max-width: 420px;">
            <div class="modal-header">
                <h3><i class="fas fa-folder-open" style="margin-right: 8px; color: var(--purple);"></i>Pindahkan ke Folder</h3>
                <button class="btn-close" onclick="closeModal('moveModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="color: var(--gray-400); font-size: 14px; margin-bottom: 16px;">
                    Pilih folder tujuan untuk <strong id="moveItemName" style="color: var(--white);">item</strong>:
                </p>
                <div class="folder-tree" id="folderTree"></div>
                <div class="modal-confirm-buttons" style="justify-content: flex-end; margin-top: 20px;">
                    <button class="btn-cancel" onclick="closeModal('moveModal')">Batal</button>
                    <button class="btn-confirm" onclick="executeMove()"><i class="fas fa-check"></i> Pindahkan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="downloadModal">
        <div class="modal" style="max-width: 400px;">
            <div class="modal-body modal-loading">
                <div class="spinner"></div>
                <p>Mempersiapkan download...</p>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <!-- Global Variables -->
    <script>
        const CURRENT_FOLDER = <?= $currentFolderId ?>;
        const BASE_URL = "<?= base_url() ?>";

    </script>

    <!-- Main JavaScript File -->
    <script src="<?= asset('js/fileManager/main.js') ?>"></script>
</body>
</html>

<?php
function renderFolderTree($parentId, $currentId, $level = 0) {
    $folders = getSubfolders($parentId);
    if (empty($folders)) return '';

    $html = '<ul class="tree-list" style="padding-left:'.($level * 12).'px">';
    foreach ($folders as $folder) {
        $active = $folder['id'] == $currentId;
        $hasChildren = $folder['subfolder_count'] > 0;

        $html .= '<li class="tree-item '.($active?'active':'').'">';
        $html .= '<a href="?folder='.$folder['id'].'" class="tree-link">';
        if ($hasChildren) {
            $html .= '<i class="fas fa-chevron-right tree-toggle" onclick="toggleTree(event, '.$folder['id'].')"></i>';
        } else {
            $html .= '<span class="tree-spacer"></span>';
        }
        $html .= '<i class="fas fa-folder'.($active?'-open':'').'"></i>';
        $html .= '<span>'.htmlspecialchars($folder['name']).'</span>';
        $html .= '</a>';

        if ($hasChildren) {
            $html .= '<div class="tree-children" id="tree-'.$folder['id'].'">';
            $html .= renderFolderTree($folder['id'], $currentId, $level + 1);
            $html .= '</div>';
        }
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}

function getFileIcon($type) {
    $icons = [
        'word' => 'fa-file-word',
        'excel' => 'fa-file-excel',
        'powerpoint' => 'fa-file-powerpoint',
        'pdf' => 'fa-file-pdf',
        'image' => 'fa-file-image',
        'video' => 'fa-file-video',
        'audio' => 'fa-file-audio',
        'zip' => 'fa-file-archive',
        'other' => 'fa-file'
    ];
    return $icons[$type] ?? 'fa-file';
}
?>