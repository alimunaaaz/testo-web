<?php
/**
 * File: login.php
 */
session_start();
require_once 'functions/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] == 'admin' ? 'admin/index.php' : 'siswa/index.php'));
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (validate_login($username, $password)) {
        header("Location: " . ($_SESSION['role'] == 'admin' ? 'admin/index.php' : 'siswa/index.php'));
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pengaduan Sekolah</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* 1. Background Putih Bersih */
        .auth-page {
            background: #FFFFFF !important; 
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

        /* 3. Penyesuaian Card pada Background Putih */
        .auth-card {
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: var(--shadow-3); /* Shadow lebih halus untuk bg putih */
        }
    </style>
</head>
<body>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-card-header">
                <i class="fas fa-school fa-3x mb-3"></i>
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda</p>
            </div>

            <div class="auth-card-body">
                <?php if ($error): ?>
                    <div class="auth-alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> 
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="auth-form-group with-icon">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Masukkan username" required autofocus>
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="auth-form-group with-icon">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password" required>
                        <i class="fas fa-lock"></i>
                    </div>

                    <button type="submit" class="auth-btn mt-2">
                        <span>Masuk Sekarang</span>
                    </button>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted">Belum punya akun? 
                            <a href="register.php" class="auth-link">Daftar Akun</a>
                        </p>
                    </div>
                </form>
            </div>

            <div class="auth-card-footer">
                <p class="mb-0" style="font-size: 0.8rem;">Layanan Aspirasi &copy; 2026</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>