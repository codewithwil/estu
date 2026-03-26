<?php
require_once __DIR__ . '/../../functions/auth.php';
checkAuth();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/estu/assets/css/dashboard/main.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari konten, pengguna, atau setelan...">
                </div>
                
                <div class="header-actions">
                    <button class="icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <button class="icon-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <div class="page-header">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Selamat datang kembali, berikut ringkasan aktivitas hari ini.</p>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon blue">
                                <i class="fas fa-eye"></i>
                            </div>
                            <span class="stat-change positive">+12.5%</span>
                        </div>
                        <div class="stat-value">48.2K</div>
                        <div class="stat-label">Total Views</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon green">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <span class="stat-change positive">+3</span>
                        </div>
                        <div class="stat-value">142</div>
                        <div class="stat-label">Konten Aktif</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon amber">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="stat-change positive">+8.2%</span>
                        </div>
                        <div class="stat-value">2,847</div>
                        <div class="stat-label">Pengguna Baru</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon red">
                                <i class="fas fa-heart"></i>
                            </div>
                            <span class="stat-change negative">-2.1%</span>
                        </div>
                        <div class="stat-value">12.5K</div>
                        <div class="stat-label">Engagement</div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- Recent Content -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Konten Terbaru</h3>
                            <a href="#" class="card-action">Lihat Semua →</a>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Konten</th>
                                            <th>Penulis</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="content-title">
                                                    <div class="content-thumb">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="content-info">
                                                        <h4>Panduan SEO 2024</h4>
                                                        <span>Artikel • 5 menit baca</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Budi Santoso</td>
                                            <td><span class="status published">Published</span></td>
                                            <td>12 Mar 2026</td>
                                            <td>
                                                <div class="actions">
                                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                                    <button class="action-btn"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="content-title">
                                                    <div class="content-thumb">
                                                        <i class="fas fa-video"></i>
                                                    </div>
                                                    <div class="content-info">
                                                        <h4>Tutorial React Hooks</h4>
                                                        <span>Video • 15:30</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Ani Wijaya</td>
                                            <td><span class="status draft">Draft</span></td>
                                            <td>11 Mar 2026</td>
                                            <td>
                                                <div class="actions">
                                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                                    <button class="action-btn"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="content-title">
                                                    <div class="content-thumb">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div class="content-info">
                                                        <h4>E-book Design System</h4>
                                                        <span>Dokumen • 45 halaman</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Citra Lestari</td>
                                            <td><span class="status scheduled">Scheduled</span></td>
                                            <td>15 Mar 2026</td>
                                            <td>
                                                <div class="actions">
                                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                                    <button class="action-btn"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="content-title">
                                                    <div class="content-thumb">
                                                        <i class="fas fa-podcast"></i>
                                                    </div>
                                                    <div class="content-info">
                                                        <h4>Podcast Ep. 42</h4>
                                                        <span>Audio • 45:20</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Dedi Kurniawan</td>
                                            <td><span class="status published">Published</span></td>
                                            <td>10 Mar 2026</td>
                                            <td>
                                                <div class="actions">
                                                    <button class="action-btn"><i class="fas fa-edit"></i></button>
                                                    <button class="action-btn"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <!-- Recent Activity -->
                        <div class="card" style="margin-bottom: 24px;">
                            <div class="card-header">
                                <h3 class="card-title">Aktivitas Terbaru</h3>
                            </div>
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-avatar" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">BS</div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>Budi Santoso</strong> mempublikasikan artikel baru</p>
                                        <span class="activity-time">5 menit yang lalu</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;">AW</div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>Ani Wijaya</strong> mengedit konten video</p>
                                        <span class="activity-time">1 jam yang lalu</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">CL</div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>Citra Lestari</strong> menambahkan media baru</p>
                                        <span class="activity-time">3 jam yang lalu</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">DK</div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>Dedi Kurniawan</strong> menghapus komentar spam</p>
                                        <span class="activity-time">5 jam yang lalu</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Aksi Cepat</h3>
                            </div>
                            <div class="quick-actions">
                                <a href="#" class="quick-btn">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Konten Baru</span>
                                </a>
                                <a href="#" class="quick-btn">
                                    <i class="fas fa-upload"></i>
                                    <span>Upload Media</span>
                                </a>
                                <a href="#" class="quick-btn">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Tambah User</span>
                                </a>
                                <a href="#" class="quick-btn">
                                    <i class="fas fa-cog"></i>
                                    <span>Setelan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="/estu/assets/js/dashboard/main.js"></script>
</body>
</html>