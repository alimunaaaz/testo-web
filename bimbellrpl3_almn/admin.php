<?php
session_start();
include 'koneksi.php';

// Proteksi: Jika bukan admin, tendang ke index
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:index.php");
    exit();
}
// ... sisa kode lainnya

// --- LOGIKA TAMBAH SISWA ---
if (isset($_POST['add_siswa'])) {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    // Cek duplikat
    $cek = mysqli_query($koneksi, "SELECT * FROM Siswa WHERE nis='$nis'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIS sudah terdaftar!');</script>";
    } else {
        // PERHATIKAN: Password default diset sama dengan NIS
        mysqli_query($koneksi, "INSERT INTO Siswa (nis, kelas, password) VALUES ('$nis', '$kelas', '$nis')");
        header("location:admin.php?page=siswa&msg=added");
    }
}

if (isset($_GET['reset'])) {
    $id = $_GET['reset'];
    // Mengosongkan password & username berarti mengembalikan ke mode "Bisa masuk cuma pakai NIS"
    mysqli_query($koneksi, "UPDATE Siswa SET password=NULL, username=NULL WHERE nis='$id'");
    header("location:admin.php?page=siswa");
}

// --- LOGIKA RESET SISWA (BARU) ---
if (isset($_GET['reset_siswa'])) {
    $nis_target = $_GET['reset_siswa'];
    // Kembalikan password jadi NIS dan hapus username custom
    mysqli_query($koneksi, "UPDATE Siswa SET password=nis, username=NULL WHERE nis='$nis_target'");
    echo "<script>alert('Akun siswa di-reset! Password kembali menjadi NIS.'); window.location='admin.php?page=siswa';</script>";
}

// --- LOGIKA QUERY KATEGORI ---
if (isset($_POST['add_kategori'])) {
    $id_k = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama_k = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    mysqli_query($koneksi, "INSERT INTO Kategori (id_kategori, ket_kategori) VALUES ('$id_k', '$nama_k')");
    header("location:admin.php?page=kategori");
}

// Logika Balas Laporan
if (isset($_POST['balas'])) {
    $id_p = $_POST['id_pelaporan'];
    $stt = $_POST['status'];
    $fdb = $_POST['feedback'];
    $id_k = $_POST['id_kategori'];
    $cek = mysqli_query($koneksi, "SELECT * FROM Aspirasi WHERE id_pelaporan='$id_p'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE Aspirasi SET status='$stt', feedback='$fdb' WHERE id_pelaporan='$id_p'");
    } else {
        mysqli_query($koneksi, "INSERT INTO Aspirasi (id_pelaporan, status, id_kategori, feedback) VALUES ('$id_p', '$stt', '$id_k', '$fdb')");
    }
    header("location:admin.php?page=laporan");
}

// --- LOGIKA UPDATE PROFIL ADMIN (PASTE DI SINI) ---
if (isset($_POST['update_siswa'])) {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_siswa']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    $update = mysqli_query($koneksi, "UPDATE Siswa SET nama_siswa='$nama', kelas='$kelas' WHERE nis='$nis'");

    if ($update) {
        echo "<script>alert('Data siswa berhasil diperbarui!'); window.location='admin.php?page=siswa';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

// Statistik Real-time
$total_laporan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM Input_Aspirasi"));
$total_siswa = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM Siswa"));
$total_selesai = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM Aspirasi WHERE status='Selesai'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | E-Aspirasi Next-Gen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.8);
            --primary-grad: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            background: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Glassmorphism */
        #sidebar {
            width: 280px;
            min-height: 100vh;
            position: fixed;
            background: #1e1e2d;
            color: #a2a3b7;
            transition: 0.4s;
            z-index: 1000;
        }

        .nav-link {
            color: #a2a3b7;
            padding: 15px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: var(--primary-grad);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 280px;
            padding: 40px;
            transition: 0.4s;
        }

        /* Premium Cards */
        .card-stats {
            border: none;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
        }

        .card-stats::after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            background: var(--primary-grad);
            opacity: 0.05;
            border-radius: 50%;
            top: -20px;
            right: -20px;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Status Badges */
        .badge-pill {
            border-radius: 30px;
            padding: 6px 16px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div id="sidebar">
        <div class="p-4 mb-4">
            <h4 class="text-white fw-bold"><i class="fas fa-rocket me-2"></i>E-ASPIRASI</h4>
            <small class="text-muted">Admin Control Center</small>
        </div>
        <nav class="nav flex-column">
            <a href="admin.php?page=dashboard" class="nav-link <?php echo !isset($_GET['page']) || $_GET['page'] == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-th-large me-3"></i> Dashboard
            </a>
            <a href="admin.php?page=laporan" class="nav-link <?php echo @$_GET['page'] == 'laporan' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list me-3"></i> Kelola Laporan
            </a>
            <a href="admin.php?page=siswa" class="nav-link <?php echo @$_GET['page'] == 'siswa' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate me-3"></i> Manajemen Siswa
            </a>
            <a href="admin.php?page=kategori" class="nav-link <?php echo @$_GET['page'] == 'kategori' ? 'active' : ''; ?>">
                <i class="fas fa-tags me-3"></i> Data Kategori
            </a>
            <div class="mt-5 p-3">
                <a href="logout.php" class="btn btn-danger w-100 rounded-pill shadow">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <?php
        $page = $_GET['page'] ?? 'dashboard';

        // HALAMAN: DASHBOARD
        if ($page == 'dashboard') { ?>
            <div data-aos="fade-down">
                <h2 class="fw-bold mb-1">Analytics Overview</h2>
                <p class="text-muted mb-4">Pantau statistik aspirasi sekolah secara real-time.</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="card-stats p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary text-white shadow-sm"><i class="fas fa-envelope"></i></div>
                            <div class="ms-4">
                                <p class="text-muted mb-0">Total Laporan</p>
                                <h2 class="fw-bold mb-0"><?php echo $total_laporan; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card-stats p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success text-white shadow-sm"><i class="fas fa-users"></i></div>
                            <div class="ms-4">
                                <p class="text-muted mb-0">Siswa Aktif</p>
                                <h2 class="fw-bold mb-0"><?php echo $total_siswa; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="card-stats p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info text-white shadow-sm"><i class="fas fa-check-circle"></i></div>
                            <div class="ms-4">
                                <p class="text-muted mb-0">Kasus Selesai</p>
                                <h2 class="fw-bold mb-0"><?php echo $total_selesai; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert shadow-sm border-0 bg-white p-4" data-aos="fade-up">
                <div class="d-flex align-items-center">
                    <div class="h1 mb-0 me-3 text-primary"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1">Status Sistem Aman</h5>
                        <p class="text-muted mb-0">Anda masuk sebagai Super Admin dengan akses database penuh.</p>
                    </div>
                </div>
            </div>
        <?php }

        // HALAMAN: MANAJEMEN SISWA
        elseif ($page == 'siswa') { ?>
            <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-right">
                <div>
                    <h2 class="fw-bold mb-1">Master Data Siswa</h2>
                    <p class="text-muted">Kelola akun akses siswa (NIS).</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow" data-bs-toggle="modal" data-bs-target="#addSiswa">
                    <i class="fas fa-plus me-2"></i> Tambah Siswa
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" data-aos="fade-up">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">NIS</th>
                            <th>Kelas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $s = mysqli_query($koneksi, "SELECT * FROM Siswa");
                        while ($rs = mysqli_fetch_array($s)) { ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?php echo $rs['nis']; ?></td>
                                <td><span class="badge bg-light text-dark border p-2 px-3"><?php echo $rs['kelas']; ?></span></td>
                                <td class="text-center">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm me-2"><i class="fas fa-edit text-warning"></i></button>
                                    <a href="admin.php?hapus_siswa=<?php echo $rs['nis']; ?>" class="btn btn-light btn-sm rounded-circle shadow-sm"><i class="fas fa-trash text-danger"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="addSiswa" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" class="modal-content border-0 rounded-4">
                        <div class="modal-header border-0 p-4 pb-0">
                            <h5>Input Siswa Baru</h5>
                        </div>
                        <div class="modal-body p-4">
                            <input type="number" name="nis" class="form-control rounded-pill mb-3" placeholder="Masukkan NIS" required>
                            <input type="text" name="kelas" class="form-control rounded-pill" placeholder="Masukkan Kelas (Contoh: XII RPL 1)" required>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="submit" name="add_siswa" class="btn btn-primary w-100 rounded-pill py-2">Simpan Data</button>
                        </div>
                    </form>
                </div>

            <?php } elseif ($page == 'settings') { ?>
                <div data-aos="fade-right">
                    <h2 class="fw-bold mb-1">Pengaturan Profil</h2>
                    <p class="text-muted mb-4">Perbarui informasi nama dan keamanan akun admin Anda.</p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4" data-aos="fade-up" style="max-width: 600px;">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap Admin</label>
                            <input type="text" name="nama_admin" class="form-control rounded-pill" placeholder="Masukkan Nama Baru" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" name="new_password" class="form-control rounded-pill" placeholder="Kosongkan jika tidak ingin ganti">
                        </div>
                        <button type="submit" name="update_admin" class="btn btn-primary w-100 rounded-pill py-2 shadow">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        <?php }


        // HALAMAN: KELOLA LAPORAN
        elseif ($page == 'laporan') { ?>
            <h2 class="fw-bold mb-4" data-aos="fade-right">Monitoring Aspirasi</h2>
            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Log Waktu</th>
                                <th>Info Laporan</th>
                                <th>Status</th>
                                <th>Respon Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query JOIN untuk mengambil Nama dari tabel Siswa
                            $q = mysqli_query($koneksi, "SELECT i.*, s.nama_siswa, a.status FROM Input_Aspirasi i 
    LEFT JOIN Siswa s ON i.nis = s.nis 
    LEFT JOIN Aspirasi a ON i.id_pelaporan = a.id_pelaporan ORDER BY i.id_pelaporan DESC");

                            while ($r = mysqli_fetch_array($q)) {
                                $st = $r['status'] ?? 'Menunggu';
                                $badge = ($st == 'Selesai') ? 'bg-success' : (($st == 'Proses') ? 'bg-warning' : 'bg-danger');
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo $r['nama_siswa'] ?: $r['nis']; ?></div>
                                        <small class="text-muted"><?php echo $r['nis']; ?></small>
                                    </td>
                                    <td><?php echo $r['lokasi']; ?></td>
                                    <td class="small"><?php echo $r['ket']; ?></td>
                                    <td><span class="badge <?php echo $badge; ?> rounded-pill px-3"><?php echo $st; ?></span></td>
                                    <td class="text-center">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#balas<?php echo $r['id_pelaporan']; ?>">
                                            <i class="fas fa-reply text-primary"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php }
        // --- LOGIKA QUERY KATEGORI ---
        if (isset($_POST['add_kategori'])) {
            $id_k = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
            $nama_k = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
            mysqli_query($koneksi, "INSERT INTO Kategori (id_kategori, ket_kategori) VALUES ('$id_k', '$nama_k')");
            header("location:admin.php?page=kategori");
        }

        // --- TAMPILAN HALAMAN KATEGORI ---
        elseif ($page == 'kategori') { ?>
            <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-right">
                <div>
                    <h2 class="fw-bold mb-1">Manajemen Kategori</h2>
                    <p class="text-muted">Atur jenis aspirasi yang tersedia.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow" data-bs-toggle="modal" data-bs-target="#addKat">
                    <i class="fas fa-plus me-2"></i> Tambah Kategori
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" data-aos="fade-up">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $k = mysqli_query($koneksi, "SELECT * FROM Kategori");
                        while ($rk = mysqli_fetch_array($k)) { ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $rk['id_kategori']; ?></td>
                                <td><span class="badge bg-soft-primary text-primary"><?php echo $rk['ket_kategori']; ?></span></td>
                                <td class="text-center">
                                    <a href="admin.php?hapus_kat=<?php echo $rk['id_kategori']; ?>" class="btn btn-light btn-sm rounded-circle"><i class="fas fa-trash text-danger"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="addKat" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" class="modal-content border-0 rounded-4">
                        <div class="modal-header border-0 p-4 pb-0">
                            <h5>Input Kategori Baru</h5>
                        </div>
                        <div class="modal-body p-4">
                            <input type="number" name="id_kategori" class="form-control rounded-pill mb-3" placeholder="ID Kategori (Contoh: 101)" required>
                            <input type="text" name="nama_kategori" class="form-control rounded-pill" placeholder="Nama Kategori (Contoh: Fasilitas)" required>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="submit" name="add_kategori" class="btn btn-primary w-100 rounded-pill py-2">Tambah Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>

    <a href="admin.php?page=settings" class="nav-link <?php echo @$_GET['page'] == 'settings' ? 'active' : ''; ?>">
        <i class="fas fa-cog me-3"></i> Pengaturan Akun
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>

</html>