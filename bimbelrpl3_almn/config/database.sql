-- 1. Membuat Database Baru dengan cara:
--    Klik "New" di sebelah kiri, klik import,
--    pilih file database.sql di folder config
--    lalu Klik import

-- Collation utf8mb4_general_ci digunakan agar bisa menyimpan karakter khusus/emoji [cite: 157]
CREATE DATABASE IF NOT EXISTS pengaduan_sekolah_12rpl3 
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE pengaduan_sekolah_12rpl3;

-- 2. Membuat Tabel Users
-- Tabel untuk menyimpan data admin dan siswa [cite: 164]
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    -- id_user = nomor unik user (otomatis naik 1, 2, 3, ...)
    
    nama VARCHAR(100) NOT NULL,
    -- nama = nama lengkap (max 100 huruf, WAJIB diisi)
    
    username VARCHAR(50) UNIQUE NOT NULL,
    -- username = username login (UNIK tidak boleh sama, WAJIB)
    
    password VARCHAR(255) NOT NULL,
    -- password = password (dienkrip jadi panjang)
    
    role ENUM('admin', 'siswa') NOT NULL,
    -- role = peran (cuma bisa 'admin' atau 'siswa')
    
    kelas VARCHAR(20) NULL,
    -- kelas = kelas siswa (boleh kosong untuk admin)
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- created_at = waktu daftar (otomatis terisi)
);

-- 3. Membuat Tabel Kategori
-- Tabel untuk menyimpan kategori pengaduan [cite: 202]
CREATE TABLE kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT NULL
);

-- 4. Membuat Tabel Aspirasi
-- Tabel utama untuk menyimpan pengaduan siswa [cite: 216]
CREATE TABLE aspirasi (
    id_aspirasi INT PRIMARY KEY AUTO_INCREMENT,
    
    id_user INT NOT NULL,
    -- ID siswa yang buat pengaduan (FK ke tabel users)
    
    id_kategori INT NOT NULL,
    -- ID kategori pengaduan (FK ke tabel kategori)
    
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    
    status ENUM('pending', 'proses', 'selesai', 'ditolak') DEFAULT 'pending',
    -- status pengaduan (default: pending)
    
    prioritas ENUM('rendah', 'sedang', 'tinggi') DEFAULT 'sedang',
    -- tingkat urgensi (default: sedang)
    
    tanggal_pengaduan DATE NOT NULL,
    foto VARCHAR(255) NULL,
    -- nama file foto (boleh kosong)
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- updated_at otomatis berubah saat data diupdate
    
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    -- Jika user dihapus, pengaduannya ikut terhapus
    
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE RESTRICT
    -- Kategori tidak bisa dihapus kalau masih ada pengaduan
);

-- 5. Membuat Tabel Umpan Balik
-- Tabel untuk menyimpan tanggapan admin terhadap pengaduan [cite: 244]
CREATE TABLE umpan_balik (
    id_umpan_balik INT PRIMARY KEY AUTO_INCREMENT,
    
    id_aspirasi INT NOT NULL,
    -- ID pengaduan yang ditanggapi (FK ke tabel aspirasi)
    
    id_admin INT NOT NULL,
    -- ID admin yang memberi tanggapan (FK ke tabel users)
    
    isi_umpan_balik TEXT NOT NULL,
    -- isi tanggapan dari admin
    
    progres_perbaikan TEXT NULL,
    -- update progres perbaikan (boleh kosong)
    
    estimasi_selesai DATE NULL,
    -- perkiraan kapan selesai (boleh kosong)
    
    tanggal_umpan_balik DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_aspirasi) REFERENCES aspirasi(id_aspirasi) ON DELETE CASCADE,
    FOREIGN KEY (id_admin) REFERENCES users(id_user) ON DELETE RESTRICT
);

-- ==========================================
-- MENGISI DATA AWAL (SEED DATA)
-- ==========================================

-- Insert Admin (ID akan jadi 1)
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', MD5('admin123'), 'admin');

-- Insert Siswa Baru (ID akan jadi 2)
INSERT INTO users (nama, username, password, role, kelas) VALUES
('Siswa', 'siswa', MD5('siswa'), 'siswa', 'XII RPL 3');

-- Tambahan User Dummy (ID akan jadi 3) untuk mencegah error pada data aspirasi di bawah
INSERT INTO users (nama, username, password, role, kelas) VALUES
('Agus', 'Agus', MD5('siswa123'), 'siswa', 'XII RPL 1');

-- Insert Kategori Baru
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Kebersihan', 'Masalah terkait kebersihan sekolah'),           -- ID 1
('Fasilitas Kelas', 'Kerusakan atau kekurangan fasilitas kelas'), -- ID 2
('Toilet', 'Masalah toilet dan sanitasi'),                        -- ID 3
('Lapangan', 'Kondisi lapangan olahraga'),                        -- ID 4
('Perpustakaan', 'Fasilitas perpustakaan'),                       -- ID 5
('Lab Komputer', 'Peralatan dan fasilitas lab komputer'),         -- ID 6
('Lainnya', 'Kategori lainnya');                                  -- ID 7

-- Insert Data Pengaduan (Aspirasi)
INSERT INTO aspirasi (id_user, id_kategori, judul, deskripsi, lokasi, prioritas, tanggal_pengaduan) VALUES
(2, 2, 'AC Ruang Kelas XII RPL 1 Rusak', 
 'AC sudah tidak dingin sejak 3 hari yang lalu. Suhu ruangan sangat panas sehingga mengganggu konsentrasi belajar.', 
 'Ruang Kelas XII RPL 1', 
 'tinggi', 
 '2025-02-01'),

(3, 3, 'Toilet Lt.2 Pintu Rusak', 
 'Pintu toilet lantai 2 tidak bisa dikunci dari dalam sehingga tidak nyaman untuk digunakan.', 
 'Toilet Lantai 2', 
 'sedang', 
 '2025-02-01');