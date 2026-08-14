<?php
require_once '../config.php';

if (!isLogin()) {
    redirect('../login.php');
}

// ============ PROSES HAPUS ============
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $pdo->prepare('DELETE FROM kegiatan WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['flash'] = 'Kegiatan berhasil dihapus.';
    redirect('kegiatan.php');
}

// ============ PROSES SIMPAN (TAMBAH / EDIT) ============
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $judul     = clean($_POST['judul']);
    $deskripsi = clean($_POST['deskripsi']);
    $tanggal   = clean($_POST['tanggal']);
    $lokasi    = clean($_POST['lokasi']);

    // Upload foto
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
            $foto = 'keg_' . time() . '_' . preg_replace('/[^A-Za-z0-9_.]/', '', $_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/' . $foto);
        }
    }

    if ($id > 0) {
        if ($foto) {
            $stmt = $pdo->prepare('UPDATE kegiatan SET judul=?, deskripsi=?, tanggal=?, lokasi=?, foto=? WHERE id=?');
            $stmt->execute([$judul, $deskripsi, $tanggal, $lokasi, $foto, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE kegiatan SET judul=?, deskripsi=?, tanggal=?, lokasi=? WHERE id=?');
            $stmt->execute([$judul, $deskripsi, $tanggal, $lokasi, $id]);
        }
        $message = 'Kegiatan berhasil diperbarui.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO kegiatan (judul, deskripsi, tanggal, lokasi, foto) VALUES (?,?,?,?,?)');
        $stmt->execute([$judul, $deskripsi, $tanggal, $lokasi, $foto]);
        $message = 'Kegiatan baru berhasil dipublikasikan.';
    }
}

// ============ AMBIL DATA UNTUK EDIT ============
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM kegiatan WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

// ============ DATA LIST ============
$kegiatanList = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC')->fetchAll();

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan - Admin</title>
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
                <a href="kegiatan.php" class="active">
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
                <h1>Kelola Kegiatan</h1>
                <div class="admin-user">
                    <span>Halo, <?php echo clean($_SESSION['admin_nama']); ?></span>
                    <a href="../logout.php" class="btn btn-outline btn-sm">Logout</a>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($flash): ?>
                <div class="alert alert-success"><?php echo $flash; ?></div>
            <?php endif; ?>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2><?php echo $editData ? 'Edit Kegiatan' : 'Publikasikan Kegiatan Baru'; ?></h2>
                </div>
                <form method="POST" action="kegiatan.php" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $editData['id'] ?? 0; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Judul Kegiatan</label>
                            <input type="text" name="judul" value="<?php echo $editData['judul'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" value="<?php echo $editData['tanggal'] ?? date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" value="<?php echo $editData['lokasi'] ?? ''; ?>" placeholder="Contoh: Aula Sekolah">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="5" required><?php echo $editData['deskripsi'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Foto Kegiatan (opsional, maks 2MB)</label>
                        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif,.webp">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Simpan Perubahan' : 'Publikasikan'; ?></button>
                        <?php if ($editData): ?>
                            <a href="kegiatan.php" class="btn btn-outline">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Daftar Kegiatan (<?php echo count($kegiatanList); ?>)</h2>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kegiatanList as $k): ?>
                            <tr>
                                <td><?php echo tanggalIndo($k['tanggal']); ?></td>
                                <td><?php echo clean($k['judul']); ?></td>
                                <td><?php echo clean($k['lokasi']); ?></td>
                                <td class="table-actions">
                                    <a href="kegiatan.php?edit=<?php echo $k['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="kegiatan.php?hapus=<?php echo $k['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kegiatan ini?');">Hapus</a>
                                </td>
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
