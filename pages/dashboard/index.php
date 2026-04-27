<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '../../../helper/route.php';
checkAuth();

$currentUser    = getCurrentUser();
$isAdmin        = isAdmin();
$isSuperadmin   = isSuperadmin();
$isEditor       = isEditor();

// --- DATA UMUM (semua role) ---

$totalUsers     = 0;
$recentUsers    = [];
$stmt           = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM users WHERE status = 'active'");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res        = mysqli_stmt_get_result($stmt);
    $totalUsers = mysqli_fetch_assoc($res)['total'] ?? 0;
}

$stmt = mysqli_prepare($conn, "SELECT id, name, email, role, created_at, last_login FROM users WHERE status = 'active' ORDER BY last_login DESC, created_at DESC LIMIT 5");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $recentUsers[] = $row;
    }
}

$totalPortfolios = 0;
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM portfolios");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $totalPortfolios = mysqli_fetch_assoc($res)['total'] ?? 0;
}

$totalServices = 0;
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM services_items");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $totalServices = mysqli_fetch_assoc($res)['total'] ?? 0;
}

$totalLinks = 0;
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM links");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $totalLinks = mysqli_fetch_assoc($res)['total'] ?? 0;
}

$totalClients = 0;
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM clients");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $totalClients = mysqli_fetch_assoc($res)['total'] ?? 0;
}

// --- DATA KHUSUS ADMIN ---

$totalFiles = 0;
$totalFileSize = 0;
$recentFiles = [];
$failedLoginsToday = 0;
$blockedUsers = 0;
$totalFolders = 0;

if ($isAdmin) {
    // Files
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total, COALESCE(SUM(size), 0) as size FROM files");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        $totalFiles = $row['total'] ?? 0;
        $totalFileSize = $row['size'] ?? 0;
    }

    $stmt = mysqli_prepare($conn, "SELECT f.id, f.filename, f.size, f.mime_type, f.created_at, fo.name as folder_name FROM files f LEFT JOIN folders fo ON f.folder_id = fo.id ORDER BY f.created_at DESC LIMIT 5");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $recentFiles[] = $row;
        }
    }

    // Security
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM login_attempts WHERE last_attempt > (NOW() - INTERVAL 24 HOUR)");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $failedLoginsToday = mysqli_fetch_assoc($res)['total'] ?? 0;
    }
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM login_attempts WHERE locked_until > NOW()");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $blockedUsers = mysqli_fetch_assoc($res)['total'] ?? 0;
    }

    // Folders
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM folders");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $totalFolders = mysqli_fetch_assoc($res)['total'] ?? 0;
    }
}

// --- DATA KHUSUS EDITOR (Portfolio & Services) ---

$recentPortfolios = [];
$recentServices = [];

if (!$isAdmin) {
    // Recent portfolios
    $stmt = mysqli_prepare($conn, "SELECT id, title, category, created_at FROM portfolios ORDER BY created_at DESC LIMIT 5");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $recentPortfolios[] = $row;
        }
    }

    // Recent services
    $stmt = mysqli_prepare($conn, "SELECT id, title, icon, created_at FROM services_items ORDER BY created_at DESC LIMIT 5");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $recentServices[] = $row;
        }
    }
}

// --- FUNGSI HELPER ---

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function timeAgo($datetime) {
    if (empty($datetime)) return 'Belum pernah';
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
    if ($diff < 604800) return floor($diff / 86400) . ' hari yang lalu';
    return date('d M Y', $time);
}

function getRoleBadge($role) {
    $colors = [
        'superadmin' => ['bg' => 'rgba(239,68,68,0.15)', 'color' => '#f87171', 'label' => 'Super Admin'],
        'admin' => ['bg' => 'rgba(245,158,11,0.15)', 'color' => '#fbbf24', 'label' => 'Admin'],
        'editor' => ['bg' => 'rgba(59,130,246,0.15)', 'color' => '#60a5fa', 'label' => 'Editor'],
    ];
    $c = $colors[$role] ?? $colors['editor'];
    return '<span style="background:' . $c['bg'] . ';color:' . $c['color'] . ';padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">' . $c['label'] . '</span>';
}

function getFileIcon($mimeType) {
    if (empty($mimeType)) return 'fa-file';
    $mimeType = (string)$mimeType;
    if (strpos($mimeType, 'image/') === 0) return 'fa-image';
    if (strpos($mimeType, 'video/') === 0) return 'fa-video';
    if (strpos($mimeType, 'audio/') === 0) return 'fa-music';
    if (strpos($mimeType, 'application/pdf') === 0) return 'fa-file-pdf';
    if (strpos($mimeType, 'application/') === 0) return 'fa-file-alt';
    if (strpos($mimeType, 'text/') === 0) return 'fa-file-code';
    return 'fa-file';
}

// --- SERVER INFO (khusus admin) ---
$serverInfo = null;
if ($isAdmin) {
    $serverInfo = [
        'php_version' => phpversion(),
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true),
        'memory_limit' => ini_get('memory_limit'),
        'disk_free' => @disk_free_space(__DIR__) ?: 0,
        'disk_total' => @disk_total_space(__DIR__) ?: 0,
        'upload_max' => ini_get('upload_max_filesize'),
        'max_execution' => ini_get('max_execution_time') . 's',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'db_size' => 0,
    ];

    $memLimit = $serverInfo['memory_limit'];
    $memLimitBytes = return_bytes($memLimit);
    $serverInfo['memory_limit_bytes'] = $memLimitBytes;
    $serverInfo['memory_usage_pct'] = $memLimitBytes > 0 ? round(($serverInfo['memory_usage'] / $memLimitBytes) * 100, 1) : 0;

    $diskTotal = $serverInfo['disk_total'];
    $diskFree = $serverInfo['disk_free'];
    $serverInfo['disk_used'] = $diskTotal - $diskFree;
    $serverInfo['disk_used_pct'] = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : 0;

    $dbResult = mysqli_query($conn, "SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = DATABASE()");
    if ($dbResult) {
        $dbRow = mysqli_fetch_assoc($dbResult);
        $serverInfo['db_size'] = $dbRow['size'] ?? 0;
    }
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/dashboard/main.css') ?>">
    <style>
        :root {
            --card-bg: #1a1a1a;
            --border-color: #2a2a2a;
            --border-hover: #3a3a3a;
            --text-primary: #ffffff;
            --text-muted: #737373;
        }

        .server-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .server-stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
        }

        .server-stat-card:hover {
            border-color: var(--border-hover);
        }

        .server-stat-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .server-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .server-stat-icon.purple { background: rgba(139,92,246,0.15); color: #a78bfa; }
        .server-stat-icon.cyan { background: rgba(6,182,212,0.15); color: #67e8f9; }
        .server-stat-icon.pink { background: rgba(236,72,153,0.15); color: #f472b6; }
        .server-stat-icon.orange { background: rgba(249,115,22,0.15); color: #fb923c; }
        .server-stat-icon.teal { background: rgba(20,184,166,0.15); color: #5eead4; }
        .server-stat-icon.indigo { background: rgba(99,102,241,0.15); color: #818cf8; }

        .server-stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .server-stat-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .server-stat-value.small {
            font-size: 16px;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255,255,255,0.05);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .progress-bar-fill.green { background: linear-gradient(90deg, #22c55e, #4ade80); }
        .progress-bar-fill.yellow { background: linear-gradient(90deg, #eab308, #facc15); }
        .progress-bar-fill.red { background: linear-gradient(90deg, #ef4444, #f87171); }

        .progress-text {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
        }

        .user-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .user-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .user-item:hover {
            background: rgba(255,255,255,0.03);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        .user-time {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: var(--text-muted);
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 32px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 13px;
        }

        .admin-badge {
            background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(245,158,11,0.05));
            border: 1px solid rgba(245,158,11,0.3);
            color: #fbbf24;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .editor-badge {
            background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.05));
            border: 1px solid rgba(59,130,246,0.3);
            color: #60a5fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .file-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .file-item:hover {
            background: rgba(255,255,255,0.03);
        }

        .file-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            background: rgba(59,130,246,0.1);
            color: #60a5fa;
            flex-shrink: 0;
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-meta {
            font-size: 11px;
            color: var(--text-muted);
        }

        .file-size {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            white-space: nowrap;
        }

        .security-alert {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .security-alert-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(239,68,68,0.15);
            color: #f87171;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .security-alert-text {
            flex: 1;
        }

        .security-alert-text strong {
            color: #f87171;
            font-size: 13px;
        }

        .security-alert-text span {
            color: var(--text-muted);
            font-size: 12px;
        }

        .security-alert-count {
            font-size: 20px;
            font-weight: 700;
            color: #f87171;
        }

        /* Content cards for editor */
        .content-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .content-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .content-item:hover {
            background: rgba(255,255,255,0.03);
        }

        .content-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .content-icon.portfolio { background: rgba(34,197,94,0.1); color: #4ade80; }
        .content-icon.service { background: rgba(59,130,246,0.1); color: #60a5fa; }
        .content-icon.client { background: rgba(245,158,11,0.1); color: #fbbf24; }

        .content-info {
            flex: 1;
            min-width: 0;
        }

        .content-title-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .content-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .content-time {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .stats-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-mini {
            flex: 1;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }

        .stat-mini-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-mini-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari konten, pengguna, atau setelan...">
                </div>

                <div class="header-actions">
                    <?php if ($isAdmin): ?>
                        <span class="admin-badge"><i class="fas fa-shield-alt" style="margin-right:4px;"></i>Admin Mode</span>
                    <?php else: ?>
                        <span class="editor-badge"><i class="fas fa-pen" style="margin-right:4px;"></i>Editor Mode</span>
                    <?php endif; ?>
                    <button class="icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <div class="user-dropdown" style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:600;">
                            <?= strtoupper(substr($currentUser['name'], 0, 2)) ?>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Selamat datang kembali, <strong><?= htmlspecialchars($currentUser['name']) ?></strong>. Berikut ringkasan sistem hari ini.</p>
                </div>

                <!-- ===== STATS OVERVIEW ===== -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon blue">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($totalUsers) ?></div>
                        <div class="stat-label">Total Pengguna</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon green">
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($totalPortfolios) ?></div>
                        <div class="stat-label">Portfolio</div>
                    </div>

                    <?php if ($isAdmin): ?>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon amber">
                                <i class="fas fa-folder-open"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($totalFiles) ?></div>
                        <div class="stat-label">File Tersimpan</div>
                    </div>
                    <?php else: ?>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon amber">
                                <i class="fas fa-cogs"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($totalServices) ?></div>
                        <div class="stat-label">Services</div>
                    </div>
                    <?php endif; ?>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon red">
                                <i class="fas fa-link"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($totalLinks) ?></div>
                        <div class="stat-label">Link Tersimpan</div>
                    </div>
                </div>

                <?php if ($isAdmin && $serverInfo): ?>
                <!-- ===== SECURITY ALERTS (ADMIN ONLY) ===== -->
                <?php if ($failedLoginsToday > 0 || $blockedUsers > 0): ?>
                <div class="section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Peringatan Keamanan
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <?php if ($failedLoginsToday > 0): ?>
                    <div class="security-alert">
                        <div class="security-alert-icon"><i class="fas fa-times-circle"></i></div>
                        <div class="security-alert-text">
                            <strong>Login Gagal Hari Ini</strong><br>
                            <span><?= number_format($failedLoginsToday) ?> percobaan login gagal dalam 24 jam terakhir</span>
                        </div>
                        <div class="security-alert-count"><?= number_format($failedLoginsToday) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($blockedUsers > 0): ?>
                    <div class="security-alert">
                        <div class="security-alert-icon"><i class="fas fa-lock"></i></div>
                        <div class="security-alert-text">
                            <strong>Pengguna Diblokir</strong><br>
                            <span><?= number_format($blockedUsers) ?> akun sedang dalam status terkunci</span>
                        </div>
                        <div class="security-alert-count"><?= number_format($blockedUsers) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- ===== SERVER STATS (ADMIN ONLY) ===== -->
                <div class="section-title">
                    <i class="fas fa-server"></i>
                    Informasi Server
                </div>
                <div class="server-stats-grid">
                    <div class="server-stat-card">
                        <div class="server-stat-header">
                            <div class="server-stat-icon purple">
                                <i class="fas fa-memory"></i>
                            </div>
                            <span class="server-stat-label">Memory Usage</span>
                        </div>
                        <div class="server-stat-value"><?= formatBytes($serverInfo['memory_usage']) ?></div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill <?= $serverInfo['memory_usage_pct'] > 80 ? 'red' : ($serverInfo['memory_usage_pct'] > 60 ? 'yellow' : 'green') ?>" style="width: <?= min($serverInfo['memory_usage_pct'], 100) ?>%"></div>
                        </div>
                        <div class="progress-text">
                            <span><?= $serverInfo['memory_usage_pct'] ?>% digunakan</span>
                            <span>Limit: <?= $serverInfo['memory_limit'] ?></span>
                        </div>
                    </div>

                    <div class="server-stat-card">
                        <div class="server-stat-header">
                            <div class="server-stat-icon cyan">
                                <i class="fas fa-hdd"></i>
                            </div>
                            <span class="server-stat-label">Disk Usage</span>
                        </div>
                        <div class="server-stat-value"><?= formatBytes($serverInfo['disk_used']) ?></div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill <?= $serverInfo['disk_used_pct'] > 85 ? 'red' : ($serverInfo['disk_used_pct'] > 70 ? 'yellow' : 'green') ?>" style="width: <?= min($serverInfo['disk_used_pct'], 100) ?>%"></div>
                        </div>
                        <div class="progress-text">
                            <span><?= $serverInfo['disk_used_pct'] ?>% digunakan</span>
                            <span>Total: <?= formatBytes($serverInfo['disk_total']) ?></span>
                        </div>
                    </div>

                    <div class="server-stat-card">
                        <div class="server-stat-header">
                            <div class="server-stat-icon pink">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <span class="server-stat-label">PHP Version</span>
                        </div>
                        <div class="server-stat-value"><?= $serverInfo['php_version'] ?></div>
                        <div class="progress-text">
                            <span>Upload Max: <?= $serverInfo['upload_max'] ?></span>
                            <span>Max Exec: <?= $serverInfo['max_execution'] ?></span>
                        </div>
                    </div>

                    <div class="server-stat-card">
                        <div class="server-stat-header">
                            <div class="server-stat-icon orange">
                                <i class="fas fa-database"></i>
                            </div>
                            <span class="server-stat-label">Database Size</span>
                        </div>
                        <div class="server-stat-value"><?= formatBytes($serverInfo['db_size']) ?></div>
                        <div class="progress-text">
                            <span>Total ukuran database</span>
                        </div>
                    </div>

                    <div class="server-stat-card">
                        <div class="server-stat-header">
                            <div class="server-stat-icon teal">
                                <i class="fas fa-server"></i>
                            </div>
                            <span class="server-stat-label">Server</span>
                        </div>
                        <div class="server-stat-value small"><?= htmlspecialchars($serverInfo['server_software']) ?></div>
                        <div class="progress-text">
                            <span>Memory Peak: <?= formatBytes($serverInfo['memory_peak']) ?></span>
                        </div>
                    </div>

                    <div class="server-stat-card">
                        <div class="server-stat-header">
                            <div class="server-stat-icon indigo">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="server-stat-label">Waktu Server</span>
                        </div>
                        <div class="server-stat-value" style="font-size:18px;"><?= date('H:i:s') ?></div>
                        <div class="progress-text">
                            <span><?= date('l, d F Y') ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ===== CONTENT GRID ===== -->
                <div class="content-grid">
                    <?php if ($isAdmin): ?>
                    <!-- ADMIN: File Terbaru -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">File Terbaru</h3>
                            <a href="<?= base_url() ?>filemanager" class="card-action">Lihat Semua →</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentFiles)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <p>Belum ada file yang diupload</p>
                                </div>
                            <?php else: ?>
                            <div class="file-list">
                                <?php foreach ($recentFiles as $file):
                                    $icon = getFileIcon($file['mime_type']);
                                    $folderName = $file['folder_name'] ?? 'Root';
                                ?>
                                <div class="file-item">
                                    <div class="file-icon">
                                        <i class="fas <?= $icon ?>"></i>
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name"><?= htmlspecialchars($file['filename']) ?></div>
                                        <div class="file-meta"><?= $folderName ?> • <?= timeAgo($file['created_at']) ?></div>
                                    </div>
                                    <div class="file-size"><?= formatBytes($file['size']) ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- EDITOR: Portfolio & Services Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Portfolio Terbaru</h3>
                            <a href="<?= base_url() ?>portfolio" class="card-action">Lihat Semua →</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentPortfolios)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-briefcase"></i>
                                    <p>Belum ada portfolio yang dibuat</p>
                                </div>
                            <?php else: ?>
                            <div class="content-list">
                                <?php foreach ($recentPortfolios as $portfolio): ?>
                                <a href="<?= base_url() ?>portfolio/edit/<?= $portfolio['id'] ?>" class="content-item">
                                    <div class="content-icon portfolio">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="content-info">
                                        <div class="content-title-text"><?= htmlspecialchars($portfolio['title']) ?></div>
                                        <div class="content-subtitle"><?= htmlspecialchars($portfolio['category'] ?? 'Uncategorized') ?></div>
                                    </div>
                                    <div class="content-time"><?= timeAgo($portfolio['created_at']) ?></div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Right Column -->
                    <div>
                        <?php if (!$isAdmin): ?>
                        <!-- EDITOR: Services Terbaru -->
                        <div class="card" style="margin-bottom: 24px;">
                            <div class="card-header">
                                <h3 class="card-title">Services Terbaru</h3>
                                <a href="<?= base_url() ?>services" class="card-action">Lihat Semua →</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentServices)): ?>
                                    <div class="empty-state">
                                        <i class="fas fa-cogs"></i>
                                        <p>Belum ada service yang dibuat</p>
                                    </div>
                                <?php else: ?>
                                <div class="content-list">
                                    <?php foreach ($recentServices as $service): ?>
                                    <a href="<?= base_url() ?>services/edit/<?= $service['id'] ?>" class="content-item">
                                        <div class="content-icon service">
                                            <i class="fas <?= htmlspecialchars($service['icon'] ?? 'fa-cog') ?>"></i>
                                        </div>
                                        <div class="content-info">
                                            <div class="content-title-text"><?= htmlspecialchars($service['title']) ?></div>
                                            <div class="content-subtitle">Service Item</div>
                                        </div>
                                        <div class="content-time"><?= timeAgo($service['created_at']) ?></div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Recent Users -->
                        <div class="card" style="margin-bottom: 24px;">
                            <div class="card-header">
                                <h3 class="card-title">Pengguna Terbaru</h3>
                                <?php if ($isAdmin): ?>
                                <a href="<?= base_url() ?>users" class="card-action">Kelola →</a>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentUsers)): ?>
                                    <div class="empty-state">
                                        <i class="fas fa-users-slash"></i>
                                        <p>Belum ada pengguna terdaftar</p>
                                    </div>
                                <?php else: ?>
                                <div class="user-list">
                                    <?php foreach ($recentUsers as $user):
                                        $initials = strtoupper(substr($user['name'], 0, 2));
                                        $colors = ['#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4'];
                                        $color = $colors[$user['id'] % count($colors)];
                                    ?>
                                    <div class="user-item">
                                        <div class="user-avatar" style="background:<?= $color ?>20;color:<?= $color ?>;">
                                            <?= $initials ?>
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                                            <div class="user-meta"><?= htmlspecialchars($user['email']) ?> • <?= getRoleBadge($user['role']) ?></div>
                                        </div>
                                        <div class="user-time"><?= timeAgo($user['last_login'] ?? $user['created_at']) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Aksi Cepat</h3>
                            </div>
                            <div class="quick-actions">
                                <a href="<?= base_url() ?>portofolio" class="quick-btn">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Portfolio Baru</span>
                                </a>
                                <?php if ($isAdmin): ?>
                                <a href="<?= base_url() ?>/fileManager" class="quick-btn">
                                    <i class="fas fa-upload"></i>
                                    <span>Upload File</span>
                                </a>
                                <?php else: ?>
                                <a href="<?= base_url() ?>service" class="quick-btn">
                                    <i class="fas fa-cog"></i>
                                    <span>Service Baru</span>
                                </a>
                                <?php endif; ?>
                                <a href="<?= base_url() ?>linkManager" class="quick-btn">
                                    <i class="fas fa-link"></i>
                                    <span>Tambah Link</span>
                                </a>
                                <?php if ($isAdmin): ?>
                                <a href="<?= base_url() ?>userManager" class="quick-btn">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Tambah User</span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= asset('js/dashboard/main.js') ?>"></script>
</body>
</html>