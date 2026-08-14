-- Database: data_siswa_angkatan
-- Buat database ini di phpMyAdmin (http://localhost/phpmyadmin)
-- Cara pakai: import file ini melalui tab "Import" di phpMyAdmin

CREATE DATABASE IF NOT EXISTS data_siswa_angkatan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE data_siswa_angkatan;

-- ============ TABEL USER (untuk login admin) ============
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Password default: admin123 (sudah di-hash dengan password_hash)
INSERT INTO users (username, password, nama, role) VALUES
('admin', '$2y$10$5Q2MpWJkNN5jtSAqGmACa.KKpOBrNVLzccVWzDz7slS/uIfCSq4JS', 'Administrator', 'admin');

-- ============ TABEL SISWA ============
CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    jurusan VARCHAR(50) NOT NULL,
    angkatan VARCHAR(10) DEFAULT '2026',
    alamat TEXT,
    no_hp VARCHAR(20),
    email VARCHAR(100),
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO siswa (nis, nama, kelas, jurusan, angkatan, alamat, no_hp, email) VALUES
('24001', 'Andi Pratama', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '2026', 'Jl. Merdeka No. 12, Jakarta', '081234567890', 'andi.pratama@email.com'),
('24002', 'Budi Santoso', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '2026', 'Jl. Sudirman No. 34, Jakarta', '081298765432', 'budi.santoso@email.com'),
('24003', 'Citra Dewi', 'XII TKJ 2', 'Teknik Komputer dan Jaringan', '2026', 'Jl. Gatot Subroto No. 56, Jakarta', '0856111222333', 'citra.dewi@email.com'),
('24004', 'Dedi Kurniawan', 'XII TKJ 2', 'Teknik Komputer dan Jaringan', '2026', 'Jl. Jend. Ahmad Yani No. 78, Jakarta', '082134567890', 'dedi.kurniawan@email.com'),
('24005', 'Eka Sari', 'XII MM 1', 'Multimedia', '2026', 'Jl. Pramuka No. 90, Jakarta', '087812345678', 'eka.sari@email.com'),
('24006', 'Fajar Ramadhan', 'XII MM 1', 'Multimedia', '2026', 'Jl. Raya Bogor No. 11, Jakarta', '083812345678', 'fajar.ramadhan@email.com'),
('24007', 'Gita Permata', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '2026', 'Jl. Kemang No. 22, Jakarta', '081345678901', 'gita.permata@email.com'),
('24008', 'Hendra Gunawan', 'XII TKJ 2', 'Teknik Komputer dan Jaringan', '2026', 'Jl. Fatmawati No. 33, Jakarta', '085789012345', 'hendra.gunawan@email.com'),
('24009', 'Intan Puspita', 'XII MM 1', 'Multimedia', '2026', 'Jl. Senopati No. 44, Jakarta', '082278901234', 'intan.puspita@email.com'),
('24010', 'Joko Susilo', 'XII RPL 2', 'Rekayasa Perangkat Lunak', '2026', 'Jl. Tebet No. 55, Jakarta', '081256789012', 'joko.susilo@email.com');

-- ============ TABEL KEGIATAN ============
CREATE TABLE kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    lokasi VARCHAR(150) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kegiatan (judul, deskripsi, tanggal, lokasi) VALUES
('Kelas Bersama Proyek Kelompok', 'Kumpul bersama teman sekelas untuk menyelesaikan proyek kelompok mata pelajaran. Seru banget walaupun banyak bercandanya!', '2026-06-15', 'Ruang Kelas XII RPL 1'),
('Rapat OSIS Angkatan', 'Rapat koordinasi OSIS untuk mempersiapkan kegiatan-kegiatan sekolah di semester depan.', '2026-07-02', 'Aula Sekolah'),
('Jalan-jalan Bareng ke TMII', 'Liburan santai bersama teman angkatan ke Taman Mini Indonesia Indah. Banyak foto-foto keren!', '2026-07-20', 'Taman Mini Indonesia Indah'),
('Bakti Sosial Angkatan', 'Kegiatan bakti sosial membagikan sembako ke panti asuhan, dihadiri seluruh teman angkatan.', '2026-08-10', 'Panti Asuhan Harapan Kita'),
('Persiapan Pentas Seni', 'Latihan bersama untuk pentas seni akhir tahun. Semua angkatan ikut serta menampilkan bakat masing-masing.', '2026-08-25', 'Lapangan Sekolah');

-- ============ TABEL GALERI FOTO KEGIATAN (banyak foto per kegiatan) ============
CREATE TABLE kegiatan_foto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kegiatan_id INT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kegiatan_id) REFERENCES kegiatan(id) ON DELETE CASCADE
) ENGINE=InnoDB;
