<?php
require_once '../config.php';

if (!isLogin()) {
    redirect('../login.php');
}

// ============ PROSES HAPUS FOTO ============
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $pdo->prepare('SELECT foto FROM kegiatan_foto WHERE id = ?');
    $stmt->execute([$id]);
    $f = $stmt->fetch();
    if ($f) {
        $path = __DIR__ . '/../uploads/' . $f['foto'];
        if (file_exists($path)) {
            @unlink($path);
        }
        $del = $pdo->prepare('DELETE FROM kegiatan_foto WHERE id = ?');
        $del->execute([$id]);
        $_SESSION['flash'] = 'Foto berhasil dihapus.';
    }
    redirect('galeri.php');
}

// ============ PROSES UPLOAD BANYAK FOTO ============
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kegiatanId = (int)$_POST['kegiatan_id'];
    $keterangan = clean($_POST['keterangan']);

    if ($kegiatanId <= 0) {
        $message = 'Pilih kegiatan terlebih dahulu!';
        $messageType = 'error';
    } elseif (empty($_FILES['foto']['name'][0])) {
        $message = 'Pilih minimal 1 foto!';
        $messageType = 'error';
    } else {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $count = 0;
        foreach ($_FILES['foto']['name'] as $i => $nama) {
            if ($_FILES['foto']['error'][$i] !== UPLOAD_ERR_OK) continue;
            if ($_FILES['foto']['size'][$i] > 5 * 1024 * 1024) continue;
            $ext = strtolower(pathinfo($nama, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;
            $fileName = 'gal_' . time() . '_' . $i . '_' . preg_replace('/[^A-Za-z0-9_.]/', '', $nama);
            if (move_uploaded_file($_FILES['foto']['tmp_name'][$i], __DIR__ . '/../uploads/' . $fileName)) {
                $stmt = $pdo->prepare('INSERT INTO kegiatan_foto (kegiatan_id, foto, keterangan) VALUES (?,?,?)');
                $stmt->execute([$kegiatanId, $fileName, $keterangan]);
                $count++;
            }
        }
        if ($count > 0) {
            $message = $count . ' foto berhasil diupload ke galeri.';
            $messageType = 'success';
        } else {
            $message = 'Tidak ada foto yang berhasil diupload. Periksa format (jpg/png/webp, maks 5MB).';
            $messageType = 'error';
        }
    }
}

// ============ DATA ============
$kegiatanList = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC')->fetchAll();
$fotoList = $pdo->query(
    'SELECT kf.*, k.judul AS kegiatan_judul
     FROM kegiatan_foto kf
     JOIN kegiatan k ON k.id = kf.kegiatan_id
     ORDER BY kf.created_at DESC'
)->fetchAll();

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <div class="admin-layout">
        <aside class="sidebar">
            <a href="index.php" class="logo sidebar-logo">Angkatan<span>2026</span></a>
            <nav class="sidebar-nav">
                <a href="index.php">
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
                <a href="galeri.php" class="active">
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
                <h1>Kelola Galeri Foto</h1>
                <div class="admin-user">
                    <span>Halo, <?php echo clean($_SESSION['admin_nama']); ?></span>
                    <a href="../logout.php" class="btn btn-outline btn-sm">Logout</a>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($flash): ?>
                <div class="alert alert-success"><?php echo $flash; ?></div>
            <?php endif; ?>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Upload Foto Kegiatan</h2>
                </div>
                <form method="POST" action="galeri.php" class="admin-form" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Pilih Kegiatan</label>
                            <select name="kegiatan_id" required>
                                <option value="">-- Pilih Kegiatan --</option>
                                <?php foreach ($kegiatanList as $k): ?>
                                    <option value="<?php echo $k['id']; ?>"><?php echo clean($k['judul']); ?> (<?php echo tanggalIndo($k['tanggal']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan (opsional, berlaku untuk semua foto)</label>
                            <input type="text" name="keterangan" placeholder="Contoh: Sesi foto bersama">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pilih Foto (bisa banyak sekaligus, maks 5MB/foto)</label>
                        <input type="file" name="foto[]" accept=".jpg,.jpeg,.png,.gif,.webp" multiple required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Upload Foto</button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Semua Foto (<?php echo count($fotoList); ?>)</h2>
                </div>
                <?php if (count($fotoList) > 0): ?>
                <div class="admin-galeri">
                    <?php foreach ($fotoList as $f): ?>
                    <div class="admin-galeri-item">
                        <img src="../uploads/<?php echo clean($f['foto']); ?>" alt="<?php echo clean($f['keterangan']); ?>">
                        <div class="admin-galeri-info">
                            <strong><?php echo clean($f['kegiatan_judul']); ?></strong>
                            <span><?php echo clean($f['keterangan']); ?></span>
                        </div>
                        <a href="galeri.php?hapus=<?php echo $f['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus foto ini?');">Hapus</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="no-data">Belum ada foto. Upload foto pertama Anda.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

</body>
</html>
