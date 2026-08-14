<?php
require_once 'config.php';

// Ambil data siswa dengan pencarian & filter
$search = isset($_GET['cari']) ? clean($_GET['cari']) : '';
$kelas = isset($_GET['kelas']) ? clean($_GET['kelas']) : '';

$sql = 'SELECT * FROM siswa WHERE 1=1';
$params = [];
if ($search !== '') {
    $sql .= ' AND (nama LIKE ? OR nis LIKE ? OR jurusan LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($kelas !== '') {
    $sql .= ' AND kelas = ?';
    $params[] = $kelas;
}
$sql .= ' ORDER BY nis ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$siswaList = $stmt->fetchAll();

// Daftar kelas untuk filter dropdown
$kelasList = $pdo->query('SELECT DISTINCT kelas FROM siswa ORDER BY kelas')->fetchAll();

// Statistik
$totalSiswa = $pdo->query('SELECT COUNT(*) FROM siswa')->fetchColumn();
$totalKelas = $pdo->query('SELECT COUNT(DISTINCT kelas) FROM siswa')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Angkatan 2026</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar scrolled">
        <div class="nav-container">
            <a href="index.php" class="logo">Angkatan<span>2026</span></a>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="data-siswa.php" class="nav-active">Data Siswa</a></li>
                <li><a href="galeri.php">Kegiatan Kami</a></li>
                <li><a href="index.php#tentang">Tentang</a></li>
                <?php if (isLogin()): ?>
                    <li><a href="admin/index.php" class="nav-admin">Dashboard Admin</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero hero-small">
        <div class="container">
            <p class="hero-greeting">Kenalan dengan teman seangkatan</p>
            <h1 class="hero-title-big">Data Siswa <span class="text-glow">Angkatan 2026</span></h1>
            <p class="hero-desc">Cari data lengkap teman-teman satu angkatan berdasarkan nama, NIS, atau jurusan.</p>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalSiswa; ?></span>
                    <span class="stat-label">Siswa Terdaftar</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $totalKelas; ?></span>
                    <span class="stat-label">Kelas</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo count($siswaList); ?></span>
                    <span class="stat-label">Hasil Pencarian</span>
                </div>
            </div>
        </div>
    </section>

    <!-- DATA SISWA -->
    <section class="section" id="siswa">
        <div class="container">
            <h2 class="section-title">Daftar <span>Siswa</span></h2>

            <form method="GET" action="data-siswa.php" class="search-bar">
                <input type="text" name="cari" value="<?php echo $search; ?>" placeholder="Cari nama, NIS, atau jurusan...">
                <select name="kelas">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?php echo clean($k['kelas']); ?>" <?php echo $kelas === $k['kelas'] ? 'selected' : ''; ?>>
                            <?php echo clean($k['kelas']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Cari</button>
                <?php if ($search !== '' || $kelas !== ''): ?>
                    <a href="data-siswa.php" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </form>

            <?php if (count($siswaList) > 0): ?>
            <div class="siswa-grid">
                <?php foreach ($siswaList as $s): ?>
                <div class="siswa-card">
                    <div class="siswa-avatar">
                        <?php if (!empty($s['foto'])): ?>
                            <img src="uploads/<?php echo clean($s['foto']); ?>" alt="<?php echo clean($s['nama']); ?>">
                        <?php else: ?>
                            <span><?php echo strtoupper(substr($s['nama'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="siswa-info">
                        <h3><?php echo clean($s['nama']); ?></h3>
                        <p class="siswa-nis">NIS: <?php echo clean($s['nis']); ?></p>
                        <p class="siswa-kelas"><?php echo clean($s['kelas']); ?> &middot; <?php echo clean($s['jurusan']); ?></p>
                        <div class="siswa-contact">
                            <?php if (!empty($s['email'])): ?>
                                <span title="Email"><?php echo clean($s['email']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($s['no_hp'])): ?>
                                <span title="No HP"><?php echo clean($s['no_hp']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="no-data">Tidak ada data siswa yang ditemukan.</p>
            <?php endif; ?>
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
