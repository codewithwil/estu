<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/userManager.php';
require_once __DIR__ . '../../../helper/route.php';

// Hanya admin ke atas yang bisa akses
requireRole('admin');

$current = 'userManager';
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$users = getAllUsers($search, $roleFilter);
$stats = getRoleStats();
$viewMode = $_COOKIE['userManagerView'] ?? 'grid';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/userManager/main.css') ?>">
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/../dashboard/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari user..." id="searchInput" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="header-actions">
                    <button class="icon-btn" id="notificationBtn" title="Notifikasi">
                        <i class="fas fa-bell"></i>
                    </button>
                </div>
            </header>

            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Manajemen User</h1>
                    <p class="page-subtitle">Kelola pengguna dan hak akses sistem</p>
                </div>

                <!-- Role Stats Cards -->
                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div class="stat-card" style="background: var(--gray-900); border: 1px solid var(--gray-800); border-radius: 16px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; background: rgba(239,68,68,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 20px;">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--white);"><?= $stats['superadmin'] ?></div>
                                <div style="font-size: 13px; color: var(--gray-500);">Superadmin</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card" style="background: var(--gray-900); border: 1px solid var(--gray-800); border-radius: 16px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; background: rgba(59,130,246,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--blue); font-size: 20px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--white);"><?= $stats['admin'] ?></div>
                                <div style="font-size: 13px; color: var(--gray-500);">Admin</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card" style="background: var(--gray-900); border: 1px solid var(--gray-800); border-radius: 16px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; background: rgba(16,163,74,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--green); font-size: 20px;">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--white);"><?= $stats['editor'] ?></div>
                                <div style="font-size: 13px; color: var(--gray-500);">Editor</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fm-top-bar">
                    <nav class="fm-breadcrumb">
                        <a href="?role=" class="<?= $roleFilter == '' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i> Semua
                        </a>
                        <i class="fas fa-chevron-right separator"></i>
                        <a href="?role=superadmin" class="<?= $roleFilter == 'superadmin' ? 'active' : '' ?>">
                            <i class="fas fa-crown" style="color: #ef4444;"></i> Superadmin
                        </a>
                        <i class="fas fa-chevron-right separator"></i>
                        <a href="?role=admin" class="<?= $roleFilter == 'admin' ? 'active' : '' ?>">
                            <i class="fas fa-user-shield" style="color: var(--blue);"></i> Admin
                        </a>
                        <i class="fas fa-chevron-right separator"></i>
                        <a href="?role=editor" class="<?= $roleFilter == 'editor' ? 'active' : '' ?>">
                            <i class="fas fa-user-edit" style="color: var(--green);"></i> Editor
                        </a>
                    </nav>

                    <div class="fm-actions">
                        <div class="view-toggle">
                            <button class="<?= $viewMode==='list'?'active':'' ?>" onclick="setViewMode('list')" title="List View">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="<?= $viewMode==='grid'?'active':'' ?>" onclick="setViewMode('grid')" title="Grid View">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>

                        <?php if (isSuperadmin()): ?>
                        <button class="btn-primary" onclick="openAddUserModal()">
                            <i class="fas fa-plus"></i> User Baru
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="fm-container">
                    <div class="fm-header">
                        <div class="fm-header-info">
                            <div class="fm-header-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="fm-header-text">
                                <h3>Daftar Pengguna</h3>
                                <p><?= count($users) ?> user terdaftar</p>
                            </div>
                        </div>
                    </div>

                    <div class="fm-body">
                        <?php if (empty($users)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <h3>Tidak ada user</h3>
                                <p>Belum ada pengguna dengan filter ini</p>
                            </div>
                        <?php else: ?>

                        <div class="items-<?= $viewMode ?>">
                            <?php foreach ($users as $user): 
                                $roleColors = [
                                    'superadmin' => ['bg' => 'rgba(239,68,68,0.15)', 'color' => '#ef4444', 'icon' => 'fa-crown'],
                                    'admin' => ['bg' => 'rgba(59,130,246,0.15)', 'color' => '#3b82f6', 'icon' => 'fa-user-shield'],
                                    'editor' => ['bg' => 'rgba(16,163,74,0.15)', 'color' => '#16a34a', 'icon' => 'fa-user-edit']
                                ];
                                $rc = $roleColors[$user['role']] ?? $roleColors['editor'];
                                $isSelf = $user['id'] == ($_SESSION['user_id'] ?? 0);
                            ?>
                            <div class="item user-item" data-id="<?= $user['id'] ?>" data-role="<?= $user['role'] ?>">
                                <div class="item-icon" style="background: <?= $rc['bg'] ?>; color: <?= $rc['color'] ?>">
                                    <i class="fas <?= $rc['icon'] ?>"></i>
                                </div>

                                <?php if ($viewMode === 'list'): ?>
                                <div class="item-info">
                                    <span class="item-name"><?= htmlspecialchars($user['name']) ?>
                                        <?php if ($isSelf): ?><span class="badge-self">Saya</span><?php endif; ?>
                                    </span>
                                    <span class="item-meta">
                                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                                        <span class="role-badge" style="background: <?= $rc['bg'] ?>; color: <?= $rc['color'] ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </span>
                                    <span class="item-date">
                                        <?= date('d M Y', strtotime($user['created_at'])) ?>
                                        <span style="margin-left: 8px; color: var(--gray-600);">
                                            <i class="fas fa-link"></i> <?= $user['link_count'] ?? 0 ?> link
                                        </span>
                                    </span>
                                </div>
                                <div class="item-actions-list">
                                    <button class="btn-icon-only" onclick="editUser(<?= $user['id'] ?>)" title="Edit" <?= (!isSuperadmin() && $user['role'] !== 'editor') ? 'disabled' : '' ?>>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if (isSuperadmin() && !$isSelf): ?>
                                    <button class="btn-icon-only" onclick="resetPassword(<?= $user['id'] ?>)" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn-icon-only danger" onclick="confirmDeleteUser(<?= $user['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <div class="item-name" title="<?= htmlspecialchars($user['name']) ?>">
                                    <?= htmlspecialchars($user['name']) ?>
                                    <?php if ($isSelf): ?><span class="badge-self">Saya</span><?php endif; ?>
                                </div>
                                <div class="item-meta" style="font-size: 12px; color: var(--gray-500);">
                                    <?= htmlspecialchars($user['email']) ?>
                                </div>
                                <div class="role-badge" style="background: <?= $rc['bg'] ?>; color: <?= $rc['color'] ?>; margin-top: 8px;">
                                    <i class="fas <?= $rc['icon'] ?>"></i> <?= ucfirst($user['role']) ?>
                                </div>
                                <div class="item-footer" style="margin-top: 12px;">
                                    <span class="item-date"><?= date('d M Y', strtotime($user['created_at'])) ?></span>
                                    <div class="item-actions-grid">
                                        <button class="btn-icon-only" onclick="editUser(<?= $user['id'] ?>)" title="Edit" <?= (!isSuperadmin() && $user['role'] !== 'editor') ? 'disabled' : '' ?>>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if (isSuperadmin() && !$isSelf): ?>
                                        <button class="btn-icon-only danger" onclick="confirmDeleteUser(<?= $user['id'] ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal-overlay" id="userModal">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h3 id="userModalTitle"><i class="fas fa-user-plus" style="margin-right: 8px; color: var(--blue);"></i>Tambah User</h3>
                <button class="btn-close" onclick="closeModal('userModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="userName" class="form-control" placeholder="Nama user..." autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group">
                        <span class="input-prefix"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="userEmail" class="form-control" placeholder="email@example.com" autocomplete="off">
                    </div>
                </div>
                <div class="form-group" id="passwordGroup">
                    <label>Password</label>
                    <div class="input-group">
                        <span class="input-prefix"><i class="fas fa-lock"></i></span>
                        <input type="password" id="userPassword" class="form-control" placeholder="Min. 6 karakter">
                    </div>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select id="userRole" class="form-control">
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                        <?php if (isSuperadmin()): ?>
                        <option value="superadmin">Superadmin</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('userModal')">Batal</button>
                <button class="btn-primary" onclick="saveUser()"><i class="fas fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal-overlay" id="resetPasswordModal">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h3><i class="fas fa-key" style="margin-right: 8px; color: var(--amber);"></i>Reset Password</h3>
                <button class="btn-close" onclick="closeModal('resetPasswordModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="resetUserId">
                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <span class="input-prefix"><i class="fas fa-lock"></i></span>
                        <input type="password" id="newPassword" class="form-control" placeholder="Min. 6 karakter">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('resetPasswordModal')">Batal</button>
                <button class="btn-primary" onclick="executeResetPassword()"><i class="fas fa-check"></i> Update</button>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal modal-confirm">
            <div class="modal-body">
                <div class="modal-confirm-icon danger"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>Hapus User?</h3>
                <p>User akan dihapus permanen dari sistem.</p>
                <div class="modal-confirm-buttons">
                    <button class="btn-cancel" onclick="closeModal('confirmModal')">Batal</button>
                    <button class="btn-confirm danger" onclick="executeDeleteUser()"><i class="fas fa-trash"></i> Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const PROCESS_URL = "<?= url('process/userManager.php') ?>";
        const IS_SUPERADMIN = <?= isSuperadmin() ? 'true' : 'false' ?>;
    </script>
    <script src="<?= asset('js/userManager/main.js') ?>"></script>
</body>
</html>