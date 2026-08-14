<?php
require_once '../config.php';

if (!isLogin()) {
    redirect('../login.php');
}

$totalSiswa = $pdo->query('SELECT COUNT(*) FROM siswa')->fetchColumn();
$totalKegiatan = $pdo->query('SELECT COUNT(*) FROM kegiatan')->fetchColumn();
$totalKelas = $pdo->query('SELECT COUNT(DISTINCT kelas) FROM siswa')->fetchColumn();
$siswaTerbaru = $pdo->query('SELECT * FROM siswa ORDER BY created_at DESC LIMIT 5')->fetchAll();
$kegiatanTerbaru = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Data Siswa Angkatan</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <div class="admin-layout">
        <aside class="sidebar">
            <a href="index.php" class="logo sidebar-logo">Angkatan<span>2026</span></a>
            <nav class="sidebar-nav">
                <a href="index.php" class="active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                    Dashboard
                </a>
                <a href="siswa.php">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Data Siswa
                </a>
                <a href="kegiatan.php">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Kegiatan
                </a>
                <a href="galeri.php">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Galeri Foto
                </a>
                <a href="../index.php" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Lihat Website
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <span>Halo, <?php echo clean($_SESSION['admin_nama']); ?></span>
                    <a href="../logout.php" class="btn btn-outline btn-sm">Logout</a>
                </div>
            </header>

            <div class="admin-stats">
                <div class="admin-stat-card">
                    <span class="stat-number"><?php echo $totalSiswa; ?></span>
                    <span class="stat-label">Total Siswa</span>
                </div>
                <div class="admin-stat-card">
                    <span class="stat-number"><?php echo $totalKelas; ?></span>
                    <span class="stat-label">Total Kelas</span>
                </div>
                <div class="admin-stat-card">
                    <span class="stat-number"><?php echo $totalKegiatan; ?></span>
                    <span class="stat-label">Total Kegiatan</span>
                </div>
            </div>

            <div class="admin-cards">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Siswa Terbaru</h2>
                        <a href="siswa.php" class="link">Kelola &rarr;</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr><th>NIS</th><th>Nama</th><th>Kelas</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswaTerbaru as $s): ?>
                            <tr>
                                <td><?php echo clean($s['nis']); ?></td>
                                <td><?php echo clean($s['nama']); ?></td>
                                <td><?php echo clean($s['kelas']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Kegiatan Terbaru</h2>
                        <a href="kegiatan.php" class="link">Kelola &rarr;</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr><th>Tanggal</th><th>Judul</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kegiatanTerbaru as $k): ?>
                            <tr>
                                <td><?php echo tanggalIndo($k['tanggal']); ?></td>
                                <td><?php echo clean($k['judul']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
