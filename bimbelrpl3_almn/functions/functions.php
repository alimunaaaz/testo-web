<?php
/**
 * File: functions/functions.php
 * Fungsi: Berisi semua function yang dibutuhkan aplikasi
 */

// Mulai session (WAJIB untuk login system)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include file konfigurasi database
require_once __DIR__ . '/../config/database.php';
// __DIR__ = folder saat ini (functions/)
// /../ = naik 1 folder (ke root)
// /config/database.php = masuk ke folder config

// ===== FUNCTION SANITASI INPUT =====

function clean_input($data) {
    /**
     * Fungsi untuk membersihkan input dari user
     * Mencegah XSS Attack (Cross-Site Scripting)
     */
    
    // Step 1: Hapus spasi di awal dan akhir
    $data = trim($data);
    // "  halo  " → "halo"
    
    // Step 2: Hapus backslash (\)
    $data = stripslashes($data);
    // Mencegah SQL Injection
    
    // Step 3: Ubah karakter HTML jadi aman
    $data = htmlspecialchars($data);
    // <script> → &lt;script&gt; (tidak bisa jalan)
    
    // Step 4: Kembalikan data yang sudah bersih
    return $data;
}
// ===== FUNCTION CEK LOGIN =====

function check_login() {
    /**
     * Fungsi untuk cek apakah user sudah login
     * Kalau belum, redirect ke halaman login
     */
    
    if (!isset($_SESSION['user_id'])) {
        // Belum login → paksa ke halaman login
        header("Location: ../login.php");
        exit();
    }
}


// ===== FUNCTION CEK ADMIN =====

function check_admin() {
    /**
     * Fungsi untuk cek apakah user adalah admin
     * Kalau bukan admin, redirect ke halaman utama
     */
    
    // Cek login dulu
    check_login();
    
    // Cek role
    if ($_SESSION['role'] !== 'admin') {
        // Bukan admin → tidak boleh akses
        header("Location: ../index.php");
        exit();
    }
}


// ===== FUNCTION CEK SISWA =====

function check_siswa() {
    /**
     * Fungsi untuk cek apakah user adalah siswa
     * Kalau bukan siswa, redirect ke halaman utama
     */
    
    // Cek login dulu
    check_login();
    
    // Cek role
    if ($_SESSION['role'] !== 'siswa') {
        // Bukan siswa → tidak boleh akses
        header("Location: ../index.php");
        exit();
    }
}

// ===== FUNCTION GET ALL ASPIRASI =====

function get_all_aspirasi($filters = []) {
    /**
     * Fungsi untuk ambil semua aspirasi dari database
     * Parameter $filters = array berisi filter (opsional)
     * Return: array berisi data aspirasi
     */
    
    $conn = getConnection();
    
    // Query dasar
    $query = "SELECT a.*, u.nama as nama_siswa, u.kelas, k.nama_kategori 
              FROM aspirasi a
              JOIN users u ON a.id_user = u.id_user
              JOIN kategori k ON a.id_kategori = k.id_kategori
              WHERE 1=1";
    // WHERE 1=1 = trik supaya bisa tambah AND di bawahnya
    
    $params = [];
    $types = "";
    
    // Filter berdasarkan tanggal
    if (!empty($filters['tanggal'])) {
        $query .= " AND DATE(a.tanggal_pengaduan) = ?";
        $params[] = $filters['tanggal'];
        $types .= "s";
    }
    
    // Filter berdasarkan user/siswa
    if (!empty($filters['id_user'])) {
        $query .= " AND a.id_user = ?";
        $params[] = $filters['id_user'];
        $types .= "i";
    }
    
    // Filter berdasarkan kategori
    if (!empty($filters['id_kategori'])) {
        $query .= " AND a.id_kategori = ?";
        $params[] = $filters['id_kategori'];
        $types .= "i";
    }
    
    // Filter berdasarkan status
    if (!empty($filters['status'])) {
        $query .= " AND a.status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }
    
    // Urutkan berdasarkan terbaru
    $query .= " ORDER BY a.created_at DESC";
    
    // Execute query
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }
    
    // Ambil semua data
    $aspirasi = [];
    while ($row = $result->fetch_assoc()) {
        $aspirasi[] = $row;
    }
    
    closeConnection($conn);
    return $aspirasi;
}


// ===== FUNCTION GET STATISTIK =====

function get_statistik() {
    /**
     * Fungsi untuk ambil statistik/ringkasan data
     * Return: array berisi angka statistik
     */
    
    $conn = getConnection();
    $stats = [];
    
    // Total semua aspirasi
    $result = $conn->query("SELECT COUNT(*) as total FROM aspirasi");
    $stats['total_aspirasi'] = $result->fetch_assoc()['total'];
    
    // Aspirasi dengan status PENDING
    $result = $conn->query("SELECT COUNT(*) as total FROM aspirasi WHERE status = 'pending'");
    $stats['pending'] = $result->fetch_assoc()['total'];
    
    // Aspirasi dengan status PROSES
    $result = $conn->query("SELECT COUNT(*) as total FROM aspirasi WHERE status = 'proses'");
    $stats['proses'] = $result->fetch_assoc()['total'];
    
    // Aspirasi dengan status SELESAI
    $result = $conn->query("SELECT COUNT(*) as total FROM aspirasi WHERE status = 'selesai'");
    $stats['selesai'] = $result->fetch_assoc()['total'];
    
    closeConnection($conn);
    return $stats;
}


// ===== FUNCTION GET ASPIRASI BY ID =====

function get_aspirasi_by_id($id) {
    /**
     * Fungsi untuk ambil 1 aspirasi berdasarkan ID
     * Return: array data aspirasi atau null
     */
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("
        SELECT a.*, u.nama as nama_siswa, u.kelas, k.nama_kategori 
        FROM aspirasi a
        JOIN users u ON a.id_user = u.id_user
        JOIN kategori k ON a.id_kategori = k.id_kategori
        WHERE a.id_aspirasi = ?
    ");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $aspirasi = null;
    if ($result->num_rows === 1) {
        $aspirasi = $result->fetch_assoc();
    }
    
    $stmt->close();
    closeConnection($conn);
    
    return $aspirasi;
}


// ===== FUNCTION UPDATE STATUS ASPIRASI =====

function update_status_aspirasi($id, $status) {
    /**
     * Fungsi untuk update status aspirasi
     * Return: true jika berhasil, false jika gagal
     */
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE aspirasi SET status = ?, updated_at = NOW() WHERE id_aspirasi = ?");
    $stmt->bind_param("si", $status, $id);
    
    $success = $stmt->execute();
    
    $stmt->close();
    closeConnection($conn);
    
    return $success;
}

// ===== FUNCTION VALIDASI LOGIN =====

function validate_login($username, $password) {
    /**
     * Fungsi untuk cek username dan password
     * Return: true jika benar, false jika salah
     */
    
    // Step 1: Ambil koneksi database
    $conn = getConnection();
    
    // Step 2: Bersihkan input username
    $username = clean_input($username);
    
    // Step 3: Enkripsi password pakai MD5
    $password = md5($password);
    // "admin123" → "0192023a7bbd73250516f069df18b500"
    
    // Step 4: Cari di database pakai PREPARED STATEMENT
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    // Tanda ? akan diganti dengan data real
    // Ini AMAN dari SQL Injection!
    
    $stmt->bind_param("ss", $username, $password);
    // "ss" = string, string (2 parameter bertipe string)
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Step 5: Cek apakah data ditemukan
    if ($result->num_rows === 1) {
        // KETEMU! Ambil datanya
        $user = $result->fetch_assoc();
        
        // Simpan ke SESSION (seperti dapat kartu identitas)
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['kelas'] = $user['kelas'];
        
        // Tutup statement
        $stmt->close();
        closeConnection($conn);
        
        return true; // Login BERHASIL
    }
    
    // Data tidak ketemu
    $stmt->close();
    closeConnection($conn);
    
    return false; // Login GAGAL
}
// ===== FUNCTION GET UMPAN BALIK =====

function get_umpan_balik($id_aspirasi) {
    /**
     * Fungsi untuk ambil semua umpan balik dari 1 aspirasi
     * Return: array berisi umpan balik
     */
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("
        SELECT ub.*, u.nama as nama_admin
        FROM umpan_balik ub
        JOIN users u ON ub.id_admin = u.id_user
        WHERE ub.id_aspirasi = ?
        ORDER BY ub.tanggal_umpan_balik DESC
    ");
    
    $stmt->bind_param("i", $id_aspirasi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $umpan_balik = [];
    while ($row = $result->fetch_assoc()) {
        $umpan_balik[] = $row;
    }
    
    $stmt->close();
    closeConnection($conn);
    
    return $umpan_balik;
}


// ===== FUNCTION INSERT UMPAN BALIK =====

function insert_umpan_balik($data) {
    /**
     * Fungsi untuk menambahkan umpan balik baru
     * Parameter $data = array berisi data umpan balik
     * Return: true jika berhasil, false jika gagal
     */
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("
        INSERT INTO umpan_balik 
        (id_aspirasi, id_admin, isi_umpan_balik, progres_perbaikan, estimasi_selesai) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        "iisss", 
        $data['id_aspirasi'],
        $data['id_admin'],
        $data['isi_umpan_balik'],
        $data['progres_perbaikan'],
        $data['estimasi_selesai']
    );
    
    $success = $stmt->execute();
    
    $stmt->close();
    closeConnection($conn);
    
    return $success;
}

// ===== FUNCTION GET STATISTIK SISWA =====

function get_statistik_siswa($id_user) {
    /**
     * Fungsi untuk ambil statistik aspirasi per siswa
     * Parameter: $id_user = ID siswa
     * Return: array berisi angka statistik
     */
    
    $conn = getConnection();
    $stats = [];
    
    // Total aspirasi siswa ini
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM aspirasi WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_aspirasi'] = $result->fetch_assoc()['total'];
    
    // Aspirasi pending
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM aspirasi WHERE id_user = ? AND status = 'pending'");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['pending'] = $result->fetch_assoc()['total'];
    
    // Aspirasi dalam proses
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM aspirasi WHERE id_user = ? AND status = 'proses'");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['proses'] = $result->fetch_assoc()['total'];
    
    // Aspirasi selesai
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM aspirasi WHERE id_user = ? AND status = 'selesai'");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['selesai'] = $result->fetch_assoc()['total'];
    
    $stmt->close();
    closeConnection($conn);
    
    return $stats;
}


// ===== FUNCTION GET ALL KATEGORI =====

function get_all_kategori() {
    /**
     * Fungsi untuk ambil semua kategori
     * Return: array berisi data kategori
     */
    
    $conn = getConnection();
    
    $result = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
    
    $kategori = [];
    while ($row = $result->fetch_assoc()) {
        $kategori[] = $row;
    }
    
    closeConnection($conn);
    return $kategori;
}


// ===== FUNCTION UPLOAD FILE =====

function upload_file($file) {
    /**
     * Fungsi untuk upload file foto
     * Parameter: $file = $_FILES['nama_field']
     * Return: nama file jika berhasil, false jika gagal
     */
    
    // Cek apakah ada file yang diupload
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Tidak ada file (opsional)
    }
    
    // Cek error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Validasi tipe file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return false; // Tipe file tidak diizinkan
    }
    
    // Validasi ukuran file (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB dalam bytes
    if ($file['size'] > $max_size) {
        return false; // File terlalu besar
    }
    
    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '_' . time() . '.' . $extension;
    
    // Path tujuan upload
    $upload_dir = __DIR__ . '/../assets/img/uploads/';
    $upload_path = $upload_dir . $new_filename;
    
    // Pastikan folder uploads ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $new_filename; // Berhasil
    }
    
    return false; // Gagal upload
}


// ===== FUNCTION INSERT ASPIRASI =====

function insert_aspirasi($data) {
    /**
     * Fungsi untuk menambahkan aspirasi baru
     * Parameter $data = array berisi data aspirasi
     * Return: true jika berhasil, false jika gagal
     */
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("
        INSERT INTO aspirasi 
        (id_user, id_kategori, judul, deskripsi, lokasi, prioritas, tanggal_pengaduan, foto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        "iissssss",
        $data['id_user'],
        $data['id_kategori'],
        $data['judul'],
        $data['deskripsi'],
        $data['lokasi'],
        $data['prioritas'],
        $data['tanggal_pengaduan'],
        $data['foto']
    );
    
    $success = $stmt->execute();
    
    $stmt->close();
    closeConnection($conn);
    
    return $success;
}

// ===== FUNCTION REGISTER USER BARU =====

function register_user($nama, $username, $password, $kelas) {
    /**
     * Fungsi untuk mendaftar user baru (role = siswa)
     * Parameter: nama, username, password (belum di-hash), kelas
     * Return: true jika berhasil, false jika gagal
     */

    $conn = getConnection();

    // Step 1: Cek apakah username sudah ada di database
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username sudah dipakai orang lain
        $stmt->close();
        closeConnection($conn);
        return 'username_exist'; // Kembalikan pesan khusus
    }

    // Step 2: Enkripsi password pakai MD5 (sama seperti di login)
    $hashed_password = md5($password);

    // Step 3: Insert ke database
    // Role selalu 'siswa' — admin tidak bisa daftar melalui register
    $stmt = $conn->prepare("
        INSERT INTO users (nama, username, password, role, kelas) 
        VALUES (?, ?, ?, 'siswa', ?)
    ");

    $stmt->bind_param("ssss", $nama, $username, $hashed_password, $kelas);

    $success = $stmt->execute();

    $stmt->close();
    closeConnection($conn);

    return $success ? true : false;
}
?>