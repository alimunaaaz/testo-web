<?php
/**
 * File: register.php
 */
session_start();
require_once 'functions/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] == 'admin' ? 'admin/index.php' : 'siswa/index.php'));
    exit();
}

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $username = clean_input($_POST['username']);
    $password = $_POST['password']; 
    $password_confirm = $_POST['password_confirm'];
    $kelas = clean_input($_POST['kelas']);

    if (empty($nama)) $errors[] = "Nama lengkap wajib diisi";
    if (empty($username)) $errors[] = "Username wajib diisi";
    if (empty($password)) $errors[] = "Password wajib diisi";
    if (empty($kelas)) $errors[] = "Kelas wajib diisi";
    if (!empty($username) && (strlen($username) < 3 || strlen($username) > 20)) $errors[] = "Username harus 3-20 karakter";
    if (!empty($password) && strlen($password) < 6) $errors[] = "Password harus minimal 6 karakter";
    if ($password !== $password_confirm) $errors[] = "Konfirmasi Password tidak sesuai";

    if (empty($errors)) {
        $result = register_user($nama, $username, $password, $kelas);
        if ($result === 'username_exist') {
            $errors[] = "Username sudah digunakan!";
        } elseif ($result === true) {
            $success_message = "Pendaftaran berhasil! Silakan login.";
        } else {
            $errors[] = "Gagal mendaftar. Coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Pengaduan Sekolah</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Override untuk Background Putih */
        .auth-page {
            background: #FFFFFF !important;
            padding: 40px 0;
        }
        .auth-page::before, .auth-page::after {
            display: none !important;
        }
        /* 2. Perbaikan Posisi Ikon Agar Simetris dengan Input */
        .auth-form-group.with-icon i {
            top: auto !important;
            bottom: 16px !important; /* (Tinggi input 48px - tinggi ikon ~20px) / 2 */
            transform: none !important;
        }
        .auth-card {
            border: 1px solid rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-card-header">
                <i class="fas fa-user-plus fa-3x mb-3"></i>
                <h2>Daftar Akun</h2>
                <p>Buat akun siswa baru di sini</p>
            </div>

            <div class="auth-card-body">
                <?php if ($success_message): ?>
                    <div class="auth-alert alert-success text-center">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                        <p><?php echo $success_message; ?></p>
                        <a href="login.php" class="auth-btn mt-2" style="text-decoration: none;">
                            <span>Ke Halaman Login</span>
                        </a>
                    </div>
                <?php else: ?>

                    <?php if (!empty($errors)): ?>
                        <div class="auth-alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="auth-form-group with-icon">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama lengkap Anda" value="<?php echo $_POST['nama'] ?? ''; ?>" required>
                            <i class="fas fa-user"></i>
                        </div>

                        <div class="auth-form-group with-icon">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" id="username" placeholder="Username unik" value="<?php echo $_POST['username'] ?? ''; ?>" required>
                            <i class="fas fa-at"></i>
                        </div>

                        <div class="auth-form-group with-icon">
                            <label for="kelas">Kelas</label>
                            <input type="text" class="form-control" name="kelas" id="kelas" placeholder="Contoh: XII RPL 1" value="<?php echo $_POST['kelas'] ?? ''; ?>" required>
                            <i class="fas fa-users"></i>
                        </div>

                        <div class="auth-form-group with-icon">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Min. 6 karakter" required>
                            <i class="fas fa-lock"></i>
                        </div>

                        <div class="auth-form-group with-icon">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Ulangi password" required>
                            <i class="fas fa-shield-alt"></i>
                        </div>

                        <button type="submit" class="auth-btn mt-3">
                            <span>Daftar Sekarang</span>
                        </button>
                    </form>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <p class="mb-0 text-muted">Sudah punya akun? 
                        <a href="login.php" class="auth-link">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>