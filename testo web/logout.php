<?php
/**
 * File: logout.php
 * Fungsi: Menghapus session dan logout user
 * 
 * Alur:
 * 1. Mulai session (harus dilakukan dulu agar bisa akses session)
 * 2. Hapus semua data session
 * 3. Destroy session (hapus session dari server)
 * 4. Redirect ke halaman login
 */

// Step 1: Mulai session — diperlukan agar PHP tahu session mana yang mau dihapus
session_start();

// Step 2: Hapus SEMUA data di session
// Ini menghapus: user_id, username, nama, role, kelas — semuanya
session_unset();

// Step 3: Destroy session — hapus session dari server secara permanen
session_destroy();

// Step 4: Redirect ke halaman login
// Setelah logout, user dikirim ke login.php
header("Location: login.php");
exit();
// exit() penting supaya code di bawah tidak jalan setelah header
?>