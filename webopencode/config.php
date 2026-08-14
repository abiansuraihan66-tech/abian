<?php
// ============ KONFIGURASI KONEKSI DATABASE ============
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'data_siswa_angkatan');
define('DB_USER', 'root');
define('DB_PASS', ''); // kosong untuk XAMPP default

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage() . '.<br>Pastikan database <strong>data_siswa_angkatan</strong> sudah dibuat dengan mengimport <em>database.sql</em> di phpMyAdmin.');
}

// Helper cek login admin
function isLogin() {
    return isset($_SESSION['admin_id']);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Fungsi sanitasi input
function clean($input) {
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}

// Helper format tanggal Indonesia
function tanggalIndo($tanggal) {
    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($tanggal);
    return date('d', $t) . ' ' . $bulan[(int)date('m', $t) - 1] . ' ' . date('Y', $t);
}
?>
