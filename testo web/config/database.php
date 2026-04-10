<?php
/**
 * File: config/database.php
 * Fungsi: Konfigurasi dan koneksi database
 */

// ===== KONFIGURASI DATABASE =====
// Konstanta = nilai yang tidak berubah

define('DB_HOST', 'localhost');
// DB_HOST = alamat server database (localhost = komputer kita)

define('DB_USER', 'root');
// DB_USER = username MySQL (default Laragon = root)

define('DB_PASS', '');
// DB_PASS = password MySQL (default Laragon = kosong)

define('DB_NAME', 'pengaduan_sekolah_12rpl3');
// DB_NAME = nama database yang sudah kita buat


// ===== FUNCTION KONEKSI DATABASE =====

function getConnection() {
    // Fungsi ini mengembalikan koneksi ke database
    
    try {
        // TRY = coba jalankan code ini
        
        // Buat koneksi baru ke database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Cek apakah koneksi berhasil
        if ($conn->connect_error) {
            // Kalau gagal, lempar error
            throw new Exception("Koneksi gagal: " . $conn->connect_error);
        }
        
        // Set charset UTF-8 (supaya support Indonesia & emoji)
        $conn->set_charset("utf8mb4");
        
        // Kembalikan koneksi
        return $conn;
        
    } catch (Exception $e) {
        // CATCH = tangkap error yang terjadi
        
        // Tampilkan pesan error dan hentikan program
        die("Error Database: " . $e->getMessage());
    }
}


// ===== FUNCTION TUTUP KONEKSI =====

function closeConnection($conn) {
    // Fungsi untuk menutup koneksi database
    
    if ($conn) {
        $conn->close();
    }
}


// ===== TEST KONEKSI (opsional - bisa dihapus nanti) =====

// Uncomment 2 baris di bawah untuk test koneksi
// $conn = getConnection();
// echo "Koneksi berhasil!";

?>