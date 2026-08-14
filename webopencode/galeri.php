<?php
require_once 'config.php';

// Ambil daftar kegiatan untuk filter
$kegiatanList = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC')->fetchAll();

// Filter kegiatan aktif
$filterKegiatan = isset($_GET['kegiatan']) ? (int)$_GET['kegiatan'] : 0;

// Ambil semua foto galeri beserta info kegiatannya
if ($filterKegiatan > 0) {
    $stmt = $pdo->prepare(
        'SELECT kf.*, k.judul AS kegiatan_judul, k.tanggal AS kegiatan_tanggal
         FROM kegiatan_foto kf
         JOIN kegiatan k ON k.id = kf.kegiatan_id
         WHERE kf.kegiatan_id = ?
         ORDER BY kf.created_at DESC'
    );
    $stmt->execute([$filterKegiatan]);
} else {
    $fotoList = $pdo->query(
        'SELECT kf.*, k.judul AS kegiatan_judul, k.tanggal AS kegiatan_tanggal
         FROM kegiatan_foto kf
         JOIN kegiatan k ON k.id = kf.kegiatan_id
         ORDER BY k.tanggal DESC, kf.created_at DESC'
    )->fetchAll();
    $stmt = null;
}

if ($stmt) {
    $fotoList = $stmt->fetchAll();
}

$totalFoto = count($fotoList);

// Kelompokkan foto per kegiatan untuk tampilan galeri
$galeriGroup = [];
foreach ($fotoList as $f) {
    $galeriGroup[$f['kegiatan_id']]['judul'] = $f['kegiatan_judul'];
    $galeriGroup[$f['kegiatan_id']]['tanggal'] = $f['kegiatan_tanggal'];
    $galeriGroup[$f['kegiatan_id']]['foto'][] = $f;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan - Data Siswa Angkatan</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar scrolled">
        <div class="nav-container">
            <a href="index.php" class="logo">Angkatan<span>2026</span></a>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="data-siswa.php">Data Siswa</a></li>
                <li><a href="galeri.php" class="nav-active">Kegiatan Kami</a></li>
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
            <p class="hero-greeting">Dokumentasi momen kebersamaan</p>
            <h1 class="hero-title-big">Galeri Foto <span class="text-glow">Kegiatan</span></h1>
            <p class="hero-desc">Kumpulan foto-foto kegiatan bersama teman-teman satu angkatan. Klik foto untuk melihat versi besar.</p>
        </div>
    </section>

    <!-- FILTER -->
    <section class="section pt-0">
        <div class="container">
            <form method="GET" action="galeri.php" class="galeri-filter">
                <a href="galeri.php" class="filter-btn <?php echo $filterKegiatan == 0 ? 'active' : ''; ?>">Semua Foto</a>
                <?php foreach ($kegiatanList as $k): ?>
                    <?php
                    // Tampilkan hanya kegiatan yang punya foto
                    $cek = $pdo->prepare('SELECT COUNT(*) FROM kegiatan_foto WHERE kegiatan_id = ?');
                    $cek->execute([$k['id']]);
                    if ($cek->fetchColumn() == 0) continue;
                    ?>
                    <a href="galeri.php?kegiatan=<?php echo $k['id']; ?>" class="filter-btn <?php echo $filterKegiatan == $k['id'] ? 'active' : ''; ?>"><?php echo clean($k['judul']); ?></a>
                <?php endforeach; ?>
            </form>

            <?php if ($totalFoto > 0): ?>
                <p class="galeri-count"><?php echo $totalFoto; ?> foto <?php echo $filterKegiatan > 0 ? 'dari kegiatan ini' : 'dari seluruh kegiatan'; ?></p>
            <?php endif; ?>

            <?php if ($totalFoto === 0): ?>
                <p class="no-data">Belum ada foto di galeri.</p>
            <?php endif; ?>

            <!-- GALERI -->
            <?php foreach ($galeriGroup as $groupId => $group): ?>
                <div class="galeri-group">
                    <div class="galeri-group-header">
                        <h3><?php echo clean($group['judul']); ?></h3>
                        <span class="galeri-date"><?php echo tanggalIndo($group['tanggal']); ?></span>
                    </div>
                    <div class="galeri-grid">
                        <?php foreach ($group['foto'] as $foto): ?>
                            <figure class="galeri-item" data-keterangan="<?php echo clean($foto['keterangan']); ?>">
                                <a href="uploads/<?php echo clean($foto['foto']); ?>" class="galeri-link" data-caption="<?php echo clean($foto['kegiatan_judul']); ?><?php echo $foto['keterangan'] ? ' - ' . clean($foto['keterangan']) : ''; ?>">
                                    <img src="uploads/<?php echo clean($foto['foto']); ?>" alt="<?php echo clean($foto['keterangan']); ?>" loading="lazy">
                                </a>
                                <?php if (!empty($foto['keterangan'])): ?>
                                    <figcaption><?php echo clean($foto['keterangan']); ?></figcaption>
                                <?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" id="lightboxClose">&times;</button>
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev">&#10094;</button>
        <figure class="lightbox-content">
            <img id="lightboxImg" src="" alt="">
            <figcaption id="lightboxCaption"></figcaption>
        </figure>
        <button class="lightbox-nav lightbox-next" id="lightboxNext">&#10095;</button>
    </div>

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

    <script>
        // ============ LIGHTBOX ============
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightboxImg');
        const lightboxCaption = document.getElementById('lightboxCaption');
        const lightboxClose = document.getElementById('lightboxClose');
        const lightboxPrev = document.getElementById('lightboxPrev');
        const lightboxNext = document.getElementById('lightboxNext');

        const galeriLinks = Array.from(document.querySelectorAll('.galeri-link'));
        let currentIndex = 0;

        function openLightbox(index) {
            currentIndex = index;
            const link = galeriLinks[currentIndex];
            lightboxImg.src = link.getAttribute('href');
            lightboxCaption.textContent = link.dataset.caption || '';
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigate(direction) {
            currentIndex = (currentIndex + direction + galeriLinks.length) % galeriLinks.length;
            openLightbox(currentIndex);
        }

        galeriLinks.forEach((link, index) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                openLightbox(index);
            });
        });

        lightboxClose.addEventListener('click', closeLightbox);
        lightboxPrev.addEventListener('click', () => navigate(-1));
        lightboxNext.addEventListener('click', () => navigate(1));

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', (e) => {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') navigate(-1);
            if (e.key === 'ArrowRight') navigate(1);
        });
    </script>

</body>
</html>
