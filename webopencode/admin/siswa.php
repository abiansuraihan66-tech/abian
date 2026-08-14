<?php
require_once '../config.php';

if (!isLogin()) {
    redirect('../login.php');
}

// ============ PROSES HAPUS ============
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $pdo->prepare('DELETE FROM siswa WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['flash'] = 'Data siswa berhasil dihapus.';
    redirect('siswa.php');
}

// ============ PROSES SIMPAN (TAMBAH / EDIT) ============
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nis     = clean($_POST['nis']);
    $nama    = clean($_POST['nama']);
    $kelas   = clean($_POST['kelas']);
    $jurusan = clean($_POST['jurusan']);
    $angkatan = clean($_POST['angkatan']);
    $alamat  = clean($_POST['alamat']);
    $no_hp   = clean($_POST['no_hp']);
    $email   = clean($_POST['email']);

    // Upload foto
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
            $foto = time() . '_' . preg_replace('/[^A-Za-z0-9_.]/', '', $_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/' . $foto);
        }
    }

    if ($id > 0) {
        // UPDATE
        if ($foto) {
            $stmt = $pdo->prepare('UPDATE siswa SET nis=?, nama=?, kelas=?, jurusan=?, angkatan=?, alamat=?, no_hp=?, email=?, foto=? WHERE id=?');
            $stmt->execute([$nis, $nama, $kelas, $jurusan, $angkatan, $alamat, $no_hp, $email, $foto, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE siswa SET nis=?, nama=?, kelas=?, jurusan=?, angkatan=?, alamat=?, no_hp=?, email=? WHERE id=?');
            $stmt->execute([$nis, $nama, $kelas, $jurusan, $angkatan, $alamat, $no_hp, $email, $id]);
        }
        $message = 'Data siswa berhasil diperbarui.';
    } else {
        // INSERT
        $stmt = $pdo->prepare('INSERT INTO siswa (nis, nama, kelas, jurusan, angkatan, alamat, no_hp, email, foto) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$nis, $nama, $kelas, $jurusan, $angkatan, $alamat, $no_hp, $email, $foto]);
        $message = 'Data siswa baru berhasil ditambahkan.';
    }
    $messageType = 'success';
}

// ============ AMBIL DATA UNTUK EDIT ============
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM siswa WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

// ============ DATA LIST ============
$siswaList = $pdo->query('SELECT * FROM siswa ORDER BY nis ASC')->fetchAll();

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin</title>
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
                <a href="siswa.php" class="active">
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
                <h1>Kelola Data Siswa</h1>
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
                    <h2><?php echo $editData ? 'Edit Data Siswa' : 'Tambah Data Siswa'; ?></h2>
                </div>
                <form method="POST" action="siswa.php" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $editData['id'] ?? 0; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>NIS</label>
                            <input type="text" name="nis" value="<?php echo $editData['nis'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" value="<?php echo $editData['nama'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" name="kelas" value="<?php echo $editData['kelas'] ?? ''; ?>" placeholder="Contoh: XII RPL 1" required>
                        </div>
                        <div class="form-group">
                            <label>Jurusan</label>
                            <select name="jurusan" required>
                                <?php
                                $jurusanOptions = ['Rekayasa Perangkat Lunak', 'Teknik Komputer dan Jaringan', 'Multimedia', 'Desain Komunikasi Visual', 'Akuntansi'];
                                $currentJurusan = $editData['jurusan'] ?? '';
                                foreach ($jurusanOptions as $j) {
                                    $sel = $currentJurusan === $j ? 'selected' : '';
                                    echo "<option value=\"$j\" $sel>$j</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Angkatan</label>
                            <input type="text" name="angkatan" value="<?php echo $editData['angkatan'] ?? '2026'; ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input type="text" name="no_hp" value="<?php echo $editData['no_hp'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo $editData['email'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Foto (maks 2MB: jpg/png/webp)</label>
                            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif,.webp">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" rows="3"><?php echo $editData['alamat'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Simpan Perubahan' : 'Tambah Siswa'; ?></button>
                        <?php if ($editData): ?>
                            <a href="siswa.php" class="btn btn-outline">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Daftar Siswa (<?php echo count($siswaList); ?>)</h2>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>No. HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswaList as $s): ?>
                            <tr>
                                <td><?php echo clean($s['nis']); ?></td>
                                <td><?php echo clean($s['nama']); ?></td>
                                <td><?php echo clean($s['kelas']); ?></td>
                                <td><?php echo clean($s['jurusan']); ?></td>
                                <td><?php echo clean($s['no_hp']); ?></td>
                                <td class="table-actions">
                                    <a href="siswa.php?edit=<?php echo $s['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="siswa.php?hapus=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus <?php echo clean($s['nama']); ?>?');">Hapus</a>
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
