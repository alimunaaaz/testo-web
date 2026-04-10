<?php
/**
 * File: index.php
 * Fungsi: Halaman utama / Landing Page aplikasi
 * Jika user sudah login, langsung redirect ke dashboard
 */

session_start();

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: siswa/index.php");
    }
    exit();
}

$page_title = 'Layanan Aspirasi';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- NAVBAR (sederhana, tanpa menu yang butuh login) -->
    <nav class="navbar navbar-expand-lg navbar-dark landing-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-school me-2"></i>Pengaduan Sekolah
            </a>
            <div class="ms-auto">
                <a href="login.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="register.php" class="btn btn-warning">
                    <i class="fas fa-user-plus"></i> Daftar
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION (bagian atas yang menarik) -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-4 fw-bold text-white mb-3">
                        <i class="fas fa-bullhorn me-3"></i>
                        Sistem Pengaduan<br>Sarana Sekolah
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Platform digital untuk siswa melaporkan masalah sarana dan prasarana sekolah.
                        Admin dapat menindaklanjuti dan memberikan umpan balik secara real-time.
                    </p>
                    <div>
                        <a href="login.php" class="btn btn-warning btn-lg me-3">
                            <i class="fas fa-sign-in-alt"></i> Login Sekarang
                        </a>
                        <a href="register.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus"></i> Daftar Baru
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0">
                    <!-- Ikon besar sebagai visual -->
                    <div class="hero-icon" style="margin-top: -20px;"> 
                        <i class="fas fa-graduation-cap text-white opacity-50" style="font-size: 13rem; filter: drop-shadow(0 0 20px rgba(255,255,255,0.1));"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR SECTION (tampilkan fitur utama) -->
    <section class="features-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold"><i class="fas fa-star text-warning"></i> Fitur Utama</h2>
                <p class="text-muted">Apa saja yang bisa Anda lakukan dengan sistem ini?</p>
            </div>

            <div class="row g-4">
                <!-- Fitur 1 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:70px; height:70px;">
                                <i class="fas fa-plus-circle fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Buat Pengaduan</h5>
                            <p class="text-muted mb-0">
                                Siswa dapat membuat laporan pengaduan dengan mudah, lengkap dengan foto pendukung.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fitur 2 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:70px; height:70px;">
                                <i class="fas fa-comments fa-2x text-success"></i>
                            </div>
                            <h5 class="fw-bold">Umpan Balik Real-Time</h5>
                            <p class="text-muted mb-0">
                                Admin memberikan tanggapan dan update progres langsung kepada siswa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fitur 3 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:70px; height:70px;">
                                <i class="fas fa-chart-bar fa-2x text-info"></i>
                            </div>
                            <h5 class="fw-bold">Dashboard & Statistik</h5>
                            <p class="text-muted mb-0">
                                Pantau status pengaduan dan lihat statistik lengkap di dashboard.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fitur 4 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:70px; height:70px;">
                                <i class="fas fa-filter fa-2x text-warning"></i>
                            </div>
                            <h5 class="fw-bold">Filter & Pencarian</h5>
                            <p class="text-muted mb-0">
                                Cari dan filter pengaduan berdasarkan kategori, status, tanggal, dan lainnya.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fitur 5 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:70px; height:70px;">
                                <i class="fas fa-shield-alt fa-2x text-danger"></i>
                            </div>
                            <h5 class="fw-bold">Keamanan Data</h5>
                            <p class="text-muted mb-0">
                                Sistem login berbasis role (Admin & Siswa) dengan enkripsi dan sanitasi input.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fitur 6 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width:70px; height:70px;">
                                <i class="fas fa-mobile-alt fa-2x text-secondary"></i>
                            </div>
                            <h5 class="fw-bold">Responsive Design</h5>
                            <p class="text-muted mb-0">
                                Aplikasi bisa diakses dari smartphone, tablet, maupun komputer dengan tampilan yang nyaman.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CARA KERJA SECTION -->
    <section class="how-section bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold"><i class="fas fa-question-circle text-primary"></i> Cara Kerjanya?</h2>
                <p class="text-muted">Hanya 4 langkah mudah!</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <div class="how-step">
                                <div class="step-circle bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                     style="width:60px; height:60px; font-size:1.5rem; font-weight:bold;">1</div>
                                <h6 class="fw-bold">Daftar Akun</h6>
                                <p class="text-muted small">Siswa daftar akun dengan data yang valid</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="how-step">
                                <div class="step-circle bg-success text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                     style="width:60px; height:60px; font-size:1.5rem; font-weight:bold;">2</div>
                                <h6 class="fw-bold">Buat Laporan</h6>
                                <p class="text-muted small">Isi formulir pengaduan + upload foto bukti</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="how-step">
                                <div class="step-circle bg-warning text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                     style="width:60px; height:60px; font-size:1.5rem; font-weight:bold;">3</div>
                                <h6 class="fw-bold">Admin Proses</h6>
                                <p class="text-muted small">Admin review dan berikan tanggapan</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="how-step">
                                <div class="step-circle bg-info text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                     style="width:60px; height:60px; font-size:1.5rem; font-weight:bold;">4</div>
                                <h6 class="fw-bold">Selesai!</h6>
                                <p class="text-muted small">Masalah terselesaikan, siswa puas 😊</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="landing-footer bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-1"><i class="fas fa-school"></i> Sistem Pengaduan Sarana Sekolah</p>
            <p class="text-muted mb-0 small">SMK PGRI TELAGASARI | ELLOE PROJECT © 2025 </p>
        </div>
    </footer>

</body>
</html>