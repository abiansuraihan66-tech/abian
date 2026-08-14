<?php
require_once 'config.php';

// Statistik
$totalSiswa = $pdo->query('SELECT COUNT(*) FROM siswa')->fetchColumn();
$totalKegiatan = $pdo->query('SELECT COUNT(*) FROM kegiatan')->fetchColumn();
$totalKelas = $pdo->query('SELECT COUNT(DISTINCT kelas) FROM siswa')->fetchColumn();
$totalFoto = $pdo->query('SELECT COUNT(*) FROM kegiatan_foto')->fetchColumn();

// Preview foto galeri: 1 foto terbaru per kegiatan
$fotoPreview = $pdo->query(
    'SELECT kf.*, k.judul AS kegiatan_judul, k.tanggal AS kegiatan_tanggal
     FROM kegiatan_foto kf
     JOIN kegiatan k ON k.id = kf.kegiatan_id
     WHERE kf.id IN (
         SELECT MAX(id) FROM kegiatan_foto GROUP BY kegiatan_id
     )
     ORDER BY k.tanggal DESC'
)->fetchAll();

// Kegiatan terbaru untuk preview
$kegiatanPreview = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 3')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Data Siswa Angkatan 2026</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Angkatan<span>2026</span></a>
            <ul class="nav-links">
                <li><a href="#home">Beranda</a></li>
                <li><a href="data-siswa.php">Data Siswa</a></li>
                <li><a href="galeri.php">Kegiatan Kami</a></li>
                <li><a href="#tentang">Tentang</a></li>
                <?php if (isLogin()): ?>
                    <li><a href="admin/index.php" class="nav-admin">Dashboard Admin</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero landing-hero" id="home">
        <div class="hero-content">
            <p class="hero-greeting">Selamat datang di</p>
            <h1 class="hero-title-big">Data Siswa <span class="text-glow">Angkatan 2026</span></h1>
            <p class="hero-desc">Website resmi angkatan kita. Lihat data teman-teman satu angkatan, dan jelajahi momen-momen seru kegiatan bersama dalam galeri foto.</p>
            <div class="hero-buttons">
                <a href="galeri.php" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="vertical-align:-2px; margin-right:6px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Jelajahi Galeri Foto
                </a>
                <a href="data-siswa.php" class="btn btn-outline">Lihat Data Siswa</a>
            </div>
        </div>
        <div class="hero-shape"></div>
    </section>

    <!-- STATISTIK -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid stats-grid-4">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalSiswa; ?></span>
                    <span class="stat-label">Siswa Terdaftar</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalKelas; ?></span>
                    <span class="stat-label">Kelas</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalKegiatan; ?></span>
                    <span class="stat-label">Kegiatan</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalFoto; ?></span>
                    <span class="stat-label">Foto Galeri</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section class="section pt-0">
        <div class="container">
            <h2 class="section-title">Fitur <span>Utama</span></h2>
            <div class="feature-grid">
                <a href="data-siswa.php" class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <h3>Data Siswa</h3>
                    <p>Cari dan lihat data lengkap teman-teman satu angkatan: NIS, kelas, jurusan, dan kontak.</p>
                    <span class="feature-link">Buka Data Siswa &rarr;</span>
                </a>
                <a href="galeri.php" class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <h3>Galeri Foto Kegiatan</h3>
                    <p>Jelajahi foto-foto kegiatan kami: kelas bersama, jalan-jalan, bakti sosial, dan lainnya.</p>
                    <span class="feature-link">Lihat Foto &rarr;</span>
                </a>
                <a href="#tentang" class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    </div>
                    <h3>Info Angkatan</h3>
                    <p>Informasi tentang website angkatan dan cara bergabung dalam kegiatan kami.</p>
                    <span class="feature-link">Baca Info &rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <!-- PREVIEW GALERI -->
    <section class="section section-alt" id="galeri">
        <div class="container">
            <h2 class="section-title">Momen <span>Terbaru</span></h2>

            <?php if (count($fotoPreview) > 0): ?>
            <div class="galeri-grid galeri-preview">
                <?php foreach ($fotoPreview as $f): ?>
                <figure class="galeri-item">
                    <a href="galeri.php?kegiatan=<?php echo (int)$f['kegiatan_id']; ?>" class="galeri-link">
                        <img src="uploads/<?php echo clean($f['foto']); ?>" alt="<?php echo clean($f['kegiatan_judul']); ?>" loading="lazy">
                    </a>
                    <figcaption><?php echo clean($f['kegiatan_judul']); ?></figcaption>
                </figure>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="no-data">Belum ada foto di galeri.</p>
            <?php endif; ?>

            <div class="center-cta">
                <a href="galeri.php" class="btn btn-primary">Lihat Semua Foto Kegiatan</a>
            </div>
        </div>
    </section>

    <!-- MOMEN TERBARU (1 kegiatan terbaru) -->
    <section class="section" id="kegiatan">
        <div class="container">
            <h2 class="section-title">Momen <span>Terbaru</span></h2>
            <?php if (count($kegiatanPreview) > 0): ?>
            <div class="kegiatan-grid kegiatan-preview">
                <?php foreach ($kegiatanPreview as $k): ?>
                <article class="kegiatan-card">
                    <span class="kegiatan-date"><?php echo tanggalIndo($k['tanggal']); ?></span>
                    <h3><?php echo clean($k['judul']); ?></h3>
                    <p class="kegiatan-desc"><?php echo nl2br(clean(mb_substr($k['deskripsi'], 0, 120))) . (mb_strlen($k['deskripsi']) > 120 ? '...' : ''); ?></p>
                    <p class="kegiatan-lokasi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo clean($k['lokasi']); ?>
                    </p>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="no-data">Belum ada kegiatan yang dipublikasikan.</p>
            <?php endif; ?>
            <div class="center-cta">
                <a href="galeri.php" class="btn btn-outline">Lihat Foto Kegiatan Kami</a>
            </div>
        </div>
    </section>

    <!-- TENTANG -->
    <section class="section section-alt" id="tentang">
        <div class="container">
            <h2 class="section-title">Tentang <span>Website</span></h2>
            <div class="tentang-content">
                <p>Website ini dibuat untuk mendokumentasikan data teman-teman satu angkatan serta mempublikasikan kegiatan-kegiatan yang kita lakukan bersama. Semua data dikelola oleh admin agar tetap rapi dan terpercaya.</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <a href="index.php" class="logo">Angkatan<span>2026</span></a>
                <p class="footer-note">Dibuat dengan <span class="heart">&hearts;</span> untuk teman-teman seangkatan.</p>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Data Siswa Angkatan. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
